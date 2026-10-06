<?php
declare(strict_types=1);

namespace Saatavat;

final class CollectionFees
{
    public const REMINDER_FEE = 5.00;
    public const BUSINESS_DEMAND_FEE = 40.00;

    /**
     * Maksuvaatimuksen kulu pääoman mukaan. Demon sääntö (README): kuluttajasaatavalla
     * enintään 100,00 € pääomasta 14 €, enintään 1 000,00 € pääomasta 24 €, sitä suuremmasta 50 €.
     * Yrityssaatavalla kiinteä 40 €.
     */
    public static function demandFee(string $debtorType, float $principal): float
    {
        if ($debtorType === 'business') {
            return self::BUSINESS_DEMAND_FEE;
        }
        if ($principal < 100.00) {
            return 14.00;
        }
        if ($principal < 1000.00) {
            return 24.00;
        }
        return 50.00;
    }

    /** Kuluttajasaatavan perintäkulujen kokonaiskatto; yrityssaatavalla ei kattoa. */
    public static function totalCap(string $debtorType, float $principal): ?float
    {
        if ($debtorType === 'business') {
            return null;
        }
        if ($principal <= 100.00) {
            return 60.00;
        }
        if ($principal <= 1000.00) {
            return 120.00;
        }
        return 210.00;
    }

    /** @param list<array<string, mixed>> $reminders */
    public static function totalFees(array $reminders): float
    {
        $total = 0.0;
        foreach ($reminders as $reminder) {
            $total += (float) $reminder['fee'];
        }
        return $total;
    }

    /**
     * Seuraavan muistutuksen tai maksuvaatimuksen kulu ottaen huomioon kokonaiskaton.
     *
     * @param list<array<string, mixed>> $existing jo lähetetyt muistutukset ja vaatimukset
     */
    public static function nextFee(string $debtorType, float $principal, array $existing, string $kind): float
    {
        $fee = $kind === 'reminder' ? self::REMINDER_FEE : self::demandFee($debtorType, $principal);
        $cap = self::totalCap($debtorType, $principal);
        if ($cap === null) {
            return $fee;
        }
        $used = self::totalFees($existing);
        return max(0.0, min($fee, $cap - $used));
    }
}
