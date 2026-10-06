<?php
declare(strict_types=1);

namespace Saatavat;

use PDO;

final class InvoiceRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /** @return list<array<string, mixed>> */
    public function listForCompany(int $companyId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM invoices WHERE company_id = :company_id ORDER BY due_date, id'
        );
        $statement->execute([':company_id' => $companyId]);
        return $statement->fetchAll();
    }

    /**
     * Haku asiakkaan nimellä (osittainen, kirjainkoosta riippumaton).
     *
     * @return list<array<string, mixed>>
     */
    public function search(int $companyId, string $query): array
    {
        $sql = "SELECT * FROM invoices WHERE company_id = {$companyId} "
            . "AND customer_name ILIKE '%{$query}%' ORDER BY due_date, id";
        return $this->pdo->query($sql)->fetchAll();
    }

    /** @return array<string, mixed>|null */
    public function find(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM invoices WHERE id = :id');
        $statement->execute([':id' => $id]);
        $row = $statement->fetch();
        return $row === false ? null : $row;
    }

    /**
     * Päivittää laskun kentät pyynnön rungosta.
     *
     * @param array<string, mixed> $fields
     * @return array<string, mixed>|null
     */
    public function update(int $id, array $fields): ?array
    {
        if ($fields === []) {
            return $this->find($id);
        }
        $sets = [];
        $params = [':id' => $id];
        foreach ($fields as $column => $value) {
            $sets[] = $column . ' = :' . $column;
            $params[':' . $column] = $value;
        }
        $statement = $this->pdo->prepare('UPDATE invoices SET ' . implode(', ', $sets) . ' WHERE id = :id');
        $statement->execute($params);
        return $this->find($id);
    }

    /** @return list<array<string, mixed>> */
    public function paymentsFor(int $invoiceId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM payments WHERE invoice_id = :invoice_id ORDER BY paid_at, id'
        );
        $statement->execute([':invoice_id' => $invoiceId]);
        return $statement->fetchAll();
    }

    /** @return list<array<string, mixed>> */
    public function remindersFor(int $invoiceId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM reminders WHERE invoice_id = :invoice_id ORDER BY sent_at, id'
        );
        $statement->execute([':invoice_id' => $invoiceId]);
        return $statement->fetchAll();
    }

    public function setStatus(int $invoiceId, string $status): void
    {
        $statement = $this->pdo->prepare('UPDATE invoices SET status = :status WHERE id = :id');
        $statement->execute([':status' => $status, ':id' => $invoiceId]);
    }

    /** @return array<string, mixed>|null */
    public function findByReference(string $reference): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM invoices WHERE reference = :reference');
        $statement->execute([':reference' => $reference]);
        $row = $statement->fetch();
        return $row === false ? null : $row;
    }
}
