<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequestController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
})->name('welcome_page');

Route::get('/home', function () {
    return Inertia::render('Home');
})->name('home');

Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/listings/{listing}', [CatalogController::class, 'show'])->name('listings.show');
Route::get('/compare', function () {
    $ids = array_filter(array_map('intval', explode(',', request('ids', ''))));

    $listings = collect();
    if (!empty($ids)) {
        $listings = \App\Models\Listing::with(['media', 'district', 'owner'])
            ->whereIn('id', $ids)
            ->where('moderation_status', 'approved')
            ->get();
    }

    return Inertia::render('Compare', [
        'items' => $listings,
        'selectedIds' => $ids,
    ]);
})->name('compare');

Route::get('/projects', function () {
    return Inertia::render('Projects', [
        'projects' => \App\Models\Project::with('district')->where('moderation_status', 'approved')->get(),
    ]);
})->name('projects');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/listings/{listing}/request', [RequestController::class, 'create'])->name('requests.create');
    Route::post('/listings/{listing}/requests', [RequestController::class, 'store'])->name('requests.store');
    Route::get('/account/requests', function () {
        return Inertia::render('Account/Requests', [
            'requests' => \App\Models\Request::with(['listing.district', 'recipient'])
                ->where('requester_id', auth()->id())
                ->orderByDesc('created_at')
                ->get(),
        ]);
    })->name('account.requests');
});

Route::get('/mahdiya', function(){
    return Inertia::render('Mahdiya');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
