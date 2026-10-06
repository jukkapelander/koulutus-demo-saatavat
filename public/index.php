<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use Saatavat\Auth;
use Saatavat\CollectionFees;
use Saatavat\Config;
use Saatavat\Db;
use Saatavat\Http;
use Saatavat\Interest;
use Saatavat\InvoiceRepository;
use Saatavat\Logger;
use Saatavat\PaymentService;

set_exception_handler(static function (Throwable $e): void {
    Http::json([
        'error' => 'internal error',
        'message' => $e->getMessage(),
        'file' => $e->getFile() . ':' . $e->getLine(),
    ], 500);
});

$method = $_SERVER['REQUEST_METHOD'];
$path = (string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

Logger::info('request', [
    'method' => $method,
    'uri' => $_SERVER['REQUEST_URI'],
    'headers' => Http::headers(),
]);

if ($path === '/api/health') {
    Http::json(['ok' => true, 'time' => date('c')]);
}

if ($path === '/api/debug/config' && Config::debug()) {
    Http::json(['env' => getenv(), 'dsn' => Config::dsn()]);
}

$company = Auth::authenticate();
if ($company === null) {
    Http::json(['error' => 'unauthorized'], 401);
}

$pdo = Db::pdo();
$invoices = new InvoiceRepository($pdo);
$payments = new PaymentService($pdo, $invoices);
$companyId = (int) $company['id'];

if ($method === 'GET' && $path === '/api/invoices') {
    $query = (string) ($_GET['q'] ?? '');
    $rows = $query !== ''
        ? $invoices->search($companyId, $query)
        : $invoices->listForCompany($companyId);
    Http::json(['count' => count($rows), 'items' => $rows]);
}

if (preg_match('#^/api/invoices/(\d+)$#', $path, $m) === 1) {
    $invoiceId = (int) $m[1];
    $invoice = $invoices->find($invoiceId);
    if ($invoice === null) {
        Http::json(['error' => 'not found'], 404);
    }
    if ($method === 'GET') {
        Logger::info('invoice.fetch', ['company' => $company['name'], 'invoice' => $invoice]);
        Http::json($invoice + [
            'paid_total' => $payments->paidTotal($invoiceId),
            'payments' => $invoices->paymentsFor($invoiceId),
            'reminders' => $invoices->remindersFor($invoiceId),
        ]);
    }
    if ($method === 'PATCH') {
        Http::json($invoices->update($invoiceId, Http::body()));
    }
    Http::json(['error' => 'method not allowed'], 405);
}

if ($method === 'POST' && preg_match('#^/api/invoices/(\d+)/payments$#', $path, $m) === 1) {
    $invoiceId = (int) $m[1];
    if ($invoices->find($invoiceId) === null) {
        Http::json(['error' => 'not found'], 404);
    }
    $body = Http::body();
    $invoice = $payments->book(
        $invoiceId,
        (float) ($body['amount'] ?? 0),
        (string) ($body['paid_at'] ?? date('Y-m-d')),
        isset($body['bank_archive_id']) ? (string) $body['bank_archive_id'] : null,
    );
    Http::json($invoice, 201);
}

if ($method === 'GET' && preg_match('#^/api/invoices/(\d+)/interest$#', $path, $m) === 1) {
    $invoice = $invoices->find((int) $m[1]);
    if ($invoice === null) {
        Http::json(['error' => 'not found'], 404);
    }
    $asOf = (string) ($_GET['as_of'] ?? date('Y-m-d'));
    $rate = (float) $company['interest_rate'];
    $interest = Interest::calculate(
        (float) $invoice['amount'],
        $rate,
        new DateTimeImmutable((string) $invoice['due_date']),
        new DateTimeImmutable($asOf),
    );
    Http::json([
        'invoice_id' => (int) $invoice['id'],
        'due_date' => $invoice['due_date'],
        'as_of' => $asOf,
        'rate_percent' => $rate,
        'interest' => $interest,
    ]);
}

if ($method === 'POST' && preg_match('#^/api/invoices/(\d+)/reminders$#', $path, $m) === 1) {
    $invoiceId = (int) $m[1];
    $invoice = $invoices->find($invoiceId);
    if ($invoice === null) {
        Http::json(['error' => 'not found'], 404);
    }
    $body = Http::body();
    $kind = (string) ($body['kind'] ?? 'reminder');
    $existing = $invoices->remindersFor($invoiceId);
    $fee = CollectionFees::nextFee((string) $invoice['debtor_type'], (float) $invoice['amount'], $existing, $kind);
    $statement = $pdo->prepare(
        'INSERT INTO reminders (invoice_id, kind, fee, sent_at) VALUES (:invoice_id, :kind, :fee, :sent_at) RETURNING *'
    );
    $statement->execute([
        ':invoice_id' => $invoiceId,
        ':kind' => $kind,
        ':fee' => $fee,
        ':sent_at' => (string) ($body['sent_at'] ?? date('Y-m-d')),
    ]);
    Http::json($statement->fetch(), 201);
}

Http::json(['error' => 'not found'], 404);
