<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Services\CostCalculationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ComparisonController extends Controller
{
    public function __invoke(Request $request, CostCalculationService $costs)
    {
        $validated = $request->validate([
            'ids' => ['nullable', 'array', 'max:3'],
            'ids.*' => ['integer', 'distinct', Rule::exists('listings', 'id')],
        ]);

        $ids = array_values($validated['ids'] ?? []);
        $byId = Listing::query()
            ->with(['media', 'district'])
            ->whereIn('id', $ids)
            ->where('moderation_status', 'approved')
            ->where('availability_status', 'available')
            ->whereNull('archived_at')
            ->where(fn ($query) => $query->whereNull('confirmed_at')->orWhere('confirmed_at', '>=', now()->subDays(14)))
            ->get()
            ->keyBy('id');

        $items = collect($ids)
            ->map(fn (int $id) => $byId->get($id))
            ->filter()
            ->values();

        $compatibility = $this->compatibility($items);

        return Inertia::render('Compare', [
            'items' => $items->map(fn (Listing $listing) => [
                ...$listing->toArray(),
                'costs' => $listing->deal_type->value === 'rent' ? $costs->calculate($listing) : null,
            ]),
            'selectedIds' => $items->pluck('id'),
            'compatibility' => $compatibility,
        ]);
    }

    private function compatibility($items): array
    {
        if ($items->count() < 2) {
            return ['compatible' => true, 'message' => null];
        }

        $currencies = $items->pluck('currency')->unique();
        $bases = $items->pluck('price_basis')->unique();
        $units = $items->pluck('rental_unit')->map(fn ($unit) => $unit?->value)->unique();

        if ($currencies->count() > 1) {
            return ['compatible' => false, 'message' => 'Turli valyutadagi takliflar avtomatik taqqoslanmaydi.'];
        }

        if ($bases->count() > 1 || $units->count() > 1) {
            return ['compatible' => false, 'message' => 'Narx birligi yoki ijara turi turlicha. Narx avtomatik bo‘linmaydi.'];
        }

        return ['compatible' => true, 'message' => null];
    }
}
