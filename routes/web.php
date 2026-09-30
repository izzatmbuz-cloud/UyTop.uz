<?php

use App\Http\Controllers\Admin\ModerationController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ComparisonController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequestController;
use App\Models\Project;
use App\Models\Request as RequestModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', HomeController::class)->name('welcome_page');
Route::get('/home', HomeController::class)->name('home');

Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/listings/{listing}', [CatalogController::class, 'show'])->name('listings.show');
Route::get('/compare', ComparisonController::class)->name('compare');

Route::get('/projects', function () {
    return Inertia::render('Projects', [
        'projects' => Project::with('district')->where('moderation_status', 'approved')->get(),
    ]);
})->name('projects');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/account/listings', [ListingController::class, 'index'])->name('account.listings');
    Route::get('/account/listings/create', [ListingController::class, 'create'])->name('account.listings.create');
    Route::post('/account/listings', [ListingController::class, 'store'])->name('account.listings.store');
    Route::get('/account/listings/{listing}/edit', [ListingController::class, 'edit'])->name('account.listings.edit');
    Route::put('/account/listings/{listing}', [ListingController::class, 'update'])->name('account.listings.update');
    Route::patch('/account/listings/{listing}/archive', [ListingController::class, 'archive'])->name('account.listings.archive');
    Route::post('/account/listings/{listing}/confirm', [ListingController::class, 'confirm'])->name('account.listings.confirm');
    Route::patch('/account/listings/{listing}/availability', [ListingController::class, 'updateAvailability'])->name('account.listings.availability');
    Route::get('/listings/{listing}/request', [RequestController::class, 'create'])->name('requests.create');
    Route::post('/listings/{listing}/requests', [RequestController::class, 'store'])->name('requests.store');
    Route::patch('/requests/{request}/status', [RequestController::class, 'updateStatus'])->name('requests.status');
    Route::get('/account/requests', function () {
        return Inertia::render('Account/Requests', [
            'requests' => RequestModel::with(['listing.district', 'recipient'])
                ->where('requester_id', Auth::id())
                ->orderByDesc('created_at')
                ->get(),
        ]);
    })->name('account.requests');
    Route::get('/account/inbox', function () {
        return Inertia::render('Account/Inbox', [
            'requests' => RequestModel::with(['listing.district', 'requester', 'events.actor'])
                ->where('recipient_id', Auth::id())
                ->latest()
                ->get(),
        ]);
    })->name('account.inbox');

    Route::get('/admin/moderation', [ModerationController::class, 'index'])->name('admin.moderation');
    Route::patch('/admin/moderation/{listing}', [ModerationController::class, 'update'])->name('admin.moderation.update');
});

Route::get('/dashboard', function () {
    return redirect()->route('account.listings');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
