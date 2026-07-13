<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Livewire\Dashboard;
use App\Livewire\Dispositions;
use App\Livewire\IncomingDocuments;
use App\Livewire\OutgoingDocuments;
use App\Livewire\Reports;
use App\Livewire\Users;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', Dashboard::class)->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Document Routes (Surat Masuk & Keluar)
    Route::get('/incoming', IncomingDocuments::class)->name('incoming.index');
    Route::get('/incoming/create', IncomingDocuments::class)->name('incoming.create');
    Route::get('/incoming/{incoming}', IncomingDocuments::class)->name('incoming.show');
    Route::get('/incoming/{incoming}/edit', IncomingDocuments::class)->name('incoming.edit');

    Route::get('/outgoing', OutgoingDocuments::class)->name('outgoing.index');
    Route::get('/outgoing/create', OutgoingDocuments::class)->name('outgoing.create');
    Route::get('/outgoing/{outgoing}', OutgoingDocuments::class)->name('outgoing.show');
    Route::get('/outgoing/{outgoing}/edit', OutgoingDocuments::class)->name('outgoing.edit');

    // Reports Route
    Route::get('/reports', Reports::class)->name('reports.index');

    // Dispositions Route
    Route::get('/dispositions', Dispositions::class)->name('dispositions.index');

    // Admin Only Routes
    Route::middleware('admin')->group(function () {
        Route::get('/users', Users::class)->name('users.index');
        Route::get('/users/create', Users::class)->name('users.create');
        Route::get('/users/{user}', Users::class)->name('users.show');
        Route::get('/users/{user}/edit', Users::class)->name('users.edit');
    });
});

require __DIR__.'/auth.php';
