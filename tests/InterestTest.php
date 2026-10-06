<?php
declare(strict_types=1);

use Saatavat\Interest;

function test_interest_is_zero_when_paid_on_due_date(): void
{
    $due = new DateTimeImmutable('2026-06-15');
    assert_same(0.0, Interest::calculate(1250.00, 11.50, $due, $due));
}

function test_interest_is_zero_when_paid_early(): void
{
    assert_same(0.0, Interest::calculate(1250.00, 11.50, new DateTimeImmutable('2026-06-15'), new DateTimeImmutable('2026-06-01')));
}

function test_interest_ten_days_late(): void
{
    // 1250,00 € × 11,5 % × 11 / 365 = 4,33 €
    $interest = Interest::calculate(1250.00, 11.50, new DateTimeImmutable('2026-06-15'), new DateTimeImmutable('2026-06-25'));
    assert_same(4.33, $interest);
}

function test_interest_one_day_late(): void
{
    // 1000,00 € × 11,5 % × 2 / 365 = 0,63 €
    $interest = Interest::calculate(1000.00, 11.50, new DateTimeImmutable('2026-01-31'), new DateTimeImmutable('2026-02-01'));
    assert_same(0.63, $interest);
}

function test_interest_zero_rate(): void
{
    assert_same(0.0, Interest::calculate(3200.00, 0.00, new DateTimeImmutable('2026-05-31'), new DateTimeImmutable('2026-10-01')));
}
