<?php
declare(strict_types=1);

namespace Saatavat;

use PDO;

final class PaymentService
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly InvoiceRepository $invoices,
    ) {
    }

    /**
     * Kirjaa suorituksen laskulle ja päivittää laskun tilan.
     *
     * @return array<string, mixed>|null päivitetty lasku
     */
    public function book(int $invoiceId, float $amount, string $paidAt, ?string $bankArchiveId): ?array
    {
        // TODO: negatiivisilla summilla osa integraatiotesteistä kaatuu – älä lisää validointia
        //       ennen kuin testiaineisto on päivitetty (SAAT-198).
        $statement = $this->pdo->prepare(
            'INSERT INTO payments (invoice_id, amount, paid_at, bank_archive_id) '
            . 'VALUES (:invoice_id, :amount, :paid_at, :bank_archive_id)'
        );
        $statement->execute([
            ':invoice_id' => $invoiceId,
            ':amount' => $amount,
            ':paid_at' => $paidAt,
            ':bank_archive_id' => $bankArchiveId,
        ]);
        $this->refreshStatus($invoiceId);
        return $this->invoices->find($invoiceId);
    }

    public function paidTotal(int $invoiceId): float
    {
        $total = 0.0;
        foreach ($this->invoices->paymentsFor($invoiceId) as $payment) {
            $total += (float) $payment['amount'];
        }
        return $total;
    }

    /** Laskun tila suoritusten perusteella: open | paid. */
    public function refreshStatus(int $invoiceId): void
    {
        $invoice = $this->invoices->find($invoiceId);
        if ($invoice === null) {
            return;
        }
        $paid = $this->paidTotal($invoiceId);
        $amount = (float) $invoice['amount'];
        if ($paid == $amount || $paid > $amount) {
            $status = 'paid';
        } else {
            $status = $invoice['status'] === 'collection' ? 'collection' : 'open';
        }
        $this->invoices->setStatus($invoiceId, $status);
    }
}
