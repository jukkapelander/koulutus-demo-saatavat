<?php
declare(strict_types=1);

namespace Saatavat;

use DateTimeImmutable;

final class Interest
{
    /**
     * Viivästyskorko.
     *
     * Demon laskentasääntö (ks. README, "Laskentasäännöt"): korkoa kertyy eräpäivää seuraavasta
     * päivästä maksupäivään asti, molemmat päivät mukaan lukien. Vuodessa on 365 päivää.
     * Pyöristys sentteihin tehdään vasta lopuksi.
     */
    public static function calculate(
        float $principal,
        float $annualRatePercent,
        DateTimeImmutable $dueDate,
        DateTimeImmutable $paidDate,
    ): float {
        if ($paidDate <= $dueDate || $principal <= 0 || $annualRatePercent <= 0) {
            return 0.0;
        }
        $days = (int) $dueDate->diff($paidDate)->days + 1;
        return round($principal * ($annualRatePercent / 100) * $days / 365, 2);
    }
}
