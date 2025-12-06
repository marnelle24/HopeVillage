<?php

use App\Http\Controllers\QrCodeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Admin Dashboard - Only accessible by admin users
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'admin',
])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    // Location CRUD Routes
    Route::get('/admin/locations', \App\Livewire\Locations\Index::class)->name('admin.locations.index');
    Route::get('/admin/locations/create', \App\Livewire\Locations\Form::class)->name('admin.locations.create');
    Route::get('/admin/locations/{id}/edit', \App\Livewire\Locations\Form::class)->name('admin.locations.edit');
});

// Member Dashboard - Only accessible by member users
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'member',
])->group(function () {
    Route::get('/member/dashboard', function () {
        return view('member.dashboard');
    })->name('member.dashboard');
    
    // QR Code routes
    Route::get('/member/qr-code', [QrCodeController::class, 'show'])->name('member.qr-code');
    Route::get('/member/qr-code/full', [QrCodeController::class, 'fullSize'])->name('member.qr-code.full');
});

// Legacy dashboard route - redirects based on user type
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isMember()) {
            return redirect()->route('member.dashboard');
        }
        abort(403, 'Invalid user type');
    })->name('dashboard');
});
