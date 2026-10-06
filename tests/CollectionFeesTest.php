<?php
declare(strict_types=1);

use Saatavat\CollectionFees;

function test_demand_fee_tiers_for_consumer(): void
{
    assert_same(14.00, CollectionFees::demandFee('consumer', 99.99));
    assert_same(24.00, CollectionFees::demandFee('consumer', 100.01));
    assert_same(24.00, CollectionFees::demandFee('consumer', 999.99));
    assert_same(50.00, CollectionFees::demandFee('consumer', 1000.01));
}

function test_demand_fee_for_business_is_fixed(): void
{
    assert_same(40.00, CollectionFees::demandFee('business', 10.00));
    assert_same(40.00, CollectionFees::demandFee('business', 15400.00));
}

function test_total_cap_limits_next_fee(): void
{
    $existing = [['fee' => '5.00'], ['fee' => '5.00'], ['fee' => '24.00'], ['fee' => '24.00'], ['fee' => '24.00']];
    // 82 € käytetty, katto 120 € -> seuraava vaatimus 24 € mahtuu kokonaan
    assert_same(24.00, CollectionFees::nextFee('consumer', 250.00, $existing, 'demand'));
    $nearCap = array_merge($existing, [['fee' => '24.00'], ['fee' => '10.00']]);
    // 116 € käytetty -> vain 4 € mahtuu
    assert_same(4.00, CollectionFees::nextFee('consumer', 250.00, $nearCap, 'demand'));
}

function test_reminder_fee_is_five_euros(): void
{
    assert_same(5.00, CollectionFees::nextFee('consumer', 89.90, [], 'reminder'));
    assert_same(5.00, CollectionFees::nextFee('business', 4300.00, [], 'reminder'));
}
