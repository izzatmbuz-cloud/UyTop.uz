<?php

namespace App\Services;

use App\Models\Listing;

class CostCalculationService
{
    /**
     * Calculate monthly and move-in costs
     *
     * R = rental price
     * U = utilities
     * D = deposit
     * C = commission
     */
    public function calculate(Listing $listing): array
    {
        $R = $this->amount($listing->price);
        $U_amount = $this->amount($listing->utilities_amount);
        $U_mode = $listing->utilities_mode;
        $D_amount = $this->amount($listing->deposit_amount);
        $D_mode = $listing->deposit_mode;
        $C_amount = $this->amount($listing->commission_amount);
        $C_mode = $listing->commission_mode;

        // Monthly payment M = R + U (if U is known)
        $monthly_payment = null;
        $monthly_complete = true;
        $unknown_monthly_items = [];

        if ($R === null) {
            $monthly_complete = false;
            $unknown_monthly_items[] = 'Ijara haqi';
        } else {
            $monthly_payment = $R;
        }

        // Add utilities if fixed
        if ($U_mode === 'included') {
            // Already included in price
            // Included utilities do not change the advertised rent.
        } elseif ($U_mode === 'fixed' && $U_amount !== null) {
            $monthly_payment = ($monthly_payment ?? 0) + $U_amount;
        } elseif ($U_mode === 'unknown' || $U_mode === null) {
            $monthly_complete = false;
            $unknown_monthly_items[] = 'Kommunal to\'lovlar';
        }

        // Move-in cost E = R + D + C + U₀
        $movein_cost = null;
        $movein_complete = true;
        $unknown_movein_items = [];

        if ($R === null) {
            $movein_complete = false;
            $unknown_movein_items[] = 'Ijara haqi';
        } else {
            $movein_cost = $R;
        }

        // Add deposit
        if ($D_mode === 'fixed' && $D_amount !== null) {
            $movein_cost = ($movein_cost ?? 0) + $D_amount;
        } elseif ($D_mode === 'unknown' || $D_mode === null) {
            $movein_complete = false;
            $unknown_movein_items[] = 'Depozit';
        }
        // D_mode === 'none' means no deposit

        // Add commission
        if ($C_mode === 'fixed' && $C_amount !== null) {
            $movein_cost = ($movein_cost ?? 0) + $C_amount;
        } elseif ($C_mode === 'unknown' || $C_mode === null) {
            $movein_complete = false;
            $unknown_movein_items[] = 'Vositachilik haqi';
        }
        // C_mode === 'none' means no commission

        // Add utilities at move-in if applicable
        if ($U_mode === 'fixed' && $U_amount !== null && $listing->utilities_payment_timing === 'move_in') {
            $movein_cost = ($movein_cost ?? 0) + $U_amount;
        }

        return [
            'monthly_payment' => $monthly_payment,
            'monthly_complete' => $monthly_complete && $monthly_payment !== null,
            'monthly_known_components' => $this->formatCurrency($listing->currency, $monthly_payment),
            'unknown_monthly_items' => $unknown_monthly_items,

            'movein_cost' => $movein_cost,
            'movein_complete' => $movein_complete && $movein_cost !== null,
            'movein_known_components' => $this->formatCurrency($listing->currency, $movein_cost),
            'unknown_movein_items' => $unknown_movein_items,

            'currency' => $listing->currency,
            'has_unknown_costs' => ! $monthly_complete || ! $movein_complete,
        ];
    }

    private function amount(mixed $value): ?float
    {
        return $value === null ? null : (float) $value;
    }

    private function formatCurrency(string $currency, ?float $amount): string
    {
        if ($amount === null) {
            return 'Ko\'rsatilmagan';
        }

        return number_format($amount, 0, ',', ' ').' '.$currency;
    }
}
