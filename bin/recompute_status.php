<?php
declare(strict_types=1);

/**
 * Laskee kaikkien laskujen tilan uudelleen suoritusten perusteella. Ajetaan: php bin/recompute_status.php
 */
require dirname(__DIR__) . '/src/bootstrap.php';

use Saatavat\Db;
use Saatavat\InvoiceRepository;
use Saatavat\PaymentService;

$pdo = Db::pdo();
$invoices = new InvoiceRepository($pdo);
$payments = new PaymentService($pdo, $invoices);

$ids = $pdo->query('SELECT id FROM invoices ORDER BY id')->fetchAll(PDO::FETCH_COLUMN);
foreach ($ids as $id) {
    $payments->refreshStatus((int) $id);
    $invoice = $invoices->find((int) $id);
    printf("%-6d %-14s %10.2f maksettu %10.2f  -> %s\n",
        $id, $invoice['invoice_number'], (float) $invoice['amount'], $payments->paidTotal((int) $id), $invoice['status']);
}
