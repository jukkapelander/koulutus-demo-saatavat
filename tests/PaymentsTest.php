<?php
declare(strict_types=1);

use Saatavat\Db;
use Saatavat\InvoiceRepository;
use Saatavat\PaymentService;

/**
 * Integraatiotestit vaativat käynnissä olevan tietokannan (ks. README). Testit luovat omat rivinsä
 * ja siivoavat ne lopuksi.
 */
function payments_test_setup(): array
{
    $pdo = Db::pdo();
    $pdo->exec("INSERT INTO companies (name, business_id, api_key, interest_rate) VALUES ('Testi Oy', '0000000-0', 'test-key-' || md5(random()::text), 11.50)");
    $companyId = (int) $pdo->lastInsertId('companies_id_seq');
    $statement = $pdo->prepare(
        "INSERT INTO invoices (company_id, invoice_number, customer_name, debtor_type, amount, issue_date, due_date, status, reference)
         VALUES (:c, 'T-1', 'Testiasiakas', 'business', 100.00, '2026-09-01', '2026-09-15', 'open', 'T0001') RETURNING id"
    );
    $statement->execute([':c' => $companyId]);
    $invoiceId = (int) $statement->fetchColumn();
    return [$pdo, $companyId, $invoiceId];
}

function payments_test_teardown(PDO $pdo, int $companyId, int $invoiceId): void
{
    $pdo->prepare('DELETE FROM payments WHERE invoice_id = :i')->execute([':i' => $invoiceId]);
    $pdo->prepare('DELETE FROM invoices WHERE id = :i')->execute([':i' => $invoiceId]);
    $pdo->prepare('DELETE FROM companies WHERE id = :c')->execute([':c' => $companyId]);
}

function test_full_payment_marks_invoice_paid(): void
{
    [$pdo, $companyId, $invoiceId] = payments_test_setup();
    try {
        $service = new PaymentService($pdo, new InvoiceRepository($pdo));
        $invoice = $service->book($invoiceId, 100.00, '2026-09-10', 'T-ARK-1');
        assert_same('paid', $invoice['status']);
    } finally {
        payments_test_teardown($pdo, $companyId, $invoiceId);
    }
}

function test_partial_payment_keeps_invoice_open(): void
{
    [$pdo, $companyId, $invoiceId] = payments_test_setup();
    try {
        $service = new PaymentService($pdo, new InvoiceRepository($pdo));
        $invoice = $service->book($invoiceId, 40.00, '2026-09-10', 'T-ARK-2');
        assert_same('open', $invoice['status']);
        assert_same(40.0, $service->paidTotal($invoiceId));
    } finally {
        payments_test_teardown($pdo, $companyId, $invoiceId);
    }
}

function test_two_payments_sum_to_total(): void
{
    [$pdo, $companyId, $invoiceId] = payments_test_setup();
    try {
        $service = new PaymentService($pdo, new InvoiceRepository($pdo));
        $service->book($invoiceId, 60.00, '2026-09-10', 'T-ARK-3');
        $invoice = $service->book($invoiceId, 40.00, '2026-09-12', 'T-ARK-4');
        assert_same('paid', $invoice['status']);
    } finally {
        payments_test_teardown($pdo, $companyId, $invoiceId);
    }
}
