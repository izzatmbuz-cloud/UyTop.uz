<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\PlatformSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class PlatformController extends Controller
{
    public function updateSettings(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate([
            'rent_commission_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'sale_commission_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'listing_confirmation_days' => ['required', 'integer', 'min:1', 'max:30'],
        ]);

        foreach ($data as $key => $value) {
            PlatformSetting::putNumber($key, (float) $value);
        }
        Cache::forget('home.featured-listings');

        return back()->with('success', 'Platforma sozlamalari saqlandi.');
    }

    public function updateCommission(Request $request, Commission $commission): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate([
            'status' => ['required', Rule::in(['pending', 'paid', 'cancelled'])],
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);
        $commission->update([...$data, 'paid_at' => $data['status'] === 'paid' ? now() : null]);

        return back()->with('success', 'Komissiya holati yangilandi.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === UserRole::ADMIN, 403);
    }
}
