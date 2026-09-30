<?php

namespace Tests\Unit;

use App\Models\Listing;
use App\Services\CostCalculationService;
use PHPUnit\Framework\TestCase;

class CostCalculationServiceTest extends TestCase
{
    public function test_it_calculates_known_monthly_and_move_in_costs(): void
    {
        $listing = new Listing([
            'currency' => 'UZS', 'price' => 800000,
            'utilities_mode' => 'fixed', 'utilities_amount' => 100000, 'utilities_payment_timing' => 'later',
            'deposit_mode' => 'fixed', 'deposit_amount' => 800000,
            'commission_mode' => 'none', 'commission_amount' => null,
        ]);

        $result = (new CostCalculationService)->calculate($listing);

        $this->assertSame(900000.0, $result['monthly_payment']);
        $this->assertSame(1600000.0, $result['movein_cost']);
        $this->assertTrue($result['monthly_complete']);
        $this->assertTrue($result['movein_complete']);
    }

    public function test_it_marks_unknown_costs_without_inventing_values(): void
    {
        $listing = new Listing([
            'currency' => 'UZS', 'price' => 800000,
            'utilities_mode' => 'unknown', 'deposit_mode' => 'unknown', 'commission_mode' => 'unknown',
        ]);

        $result = (new CostCalculationService)->calculate($listing);

        $this->assertFalse($result['monthly_complete']);
        $this->assertFalse($result['movein_complete']);
        $this->assertContains('Kommunal to\'lovlar', $result['unknown_monthly_items']);
        $this->assertSame(800000.0, $result['movein_cost']);
    }
}
