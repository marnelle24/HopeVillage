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
    Route::get('/admin/locations/{location_code}/edit', \App\Livewire\Locations\Form::class)->name('admin.locations.edit');
    Route::get('/admin/locations/{location_code}', \App\Livewire\Locations\Profile::class)->name('admin.locations.profile');
    
    // Event CRUD Routes
    Route::get('/admin/events', \App\Livewire\Events\AllEvents::class)->name('admin.events.index');
    Route::get('/admin/events/{event_code}', \App\Livewire\Events\Profile::class)->name('admin.events.profile');
    Route::get('/admin/locations/{location_code}/events', \App\Livewire\Events\Index::class)->name('admin.locations.events.index');
    Route::get('/admin/locations/{location_code}/events/create', \App\Livewire\Events\Form::class)->name('admin.locations.events.create');
    Route::get('/admin/locations/{location_code}/events/{id}/edit', \App\Livewire\Events\Form::class)->name('admin.locations.events.edit');
    
    // Amenity CRUD Routes
    Route::get('/admin/amenities', \App\Livewire\Amenities\Index::class)->name('admin.amenities.index');
    Route::get('/admin/amenities/create', \App\Livewire\Amenities\Form::class)->name('admin.amenities.create');
    Route::get('/admin/amenities/{id}/edit', \App\Livewire\Amenities\Form::class)->name('admin.amenities.edit');
    
    // Merchant CRUD Routes
    Route::get('/admin/merchants', \App\Livewire\Merchants\Index::class)->name('admin.merchants.index');
    Route::get('/admin/merchants/create', \App\Livewire\Merchants\Form::class)->name('admin.merchants.create');
    Route::get('/admin/merchants/{merchant_code}/edit', \App\Livewire\Merchants\Form::class)->name('admin.merchants.edit');
    Route::get('/admin/merchants/{merchant_code}', \App\Livewire\Merchants\Profile::class)->name('admin.merchants.profile');
    
    // Voucher CRUD Routes
    Route::get('/admin/vouchers', \App\Livewire\Vouchers\Index::class)->name('admin.vouchers.index');
    Route::get('/admin/vouchers/create', \App\Livewire\Vouchers\Form::class)->name('admin.vouchers.create');
    Route::get('/admin/vouchers/{voucher_code}/edit', \App\Livewire\Vouchers\Form::class)->name('admin.vouchers.edit');
    Route::get('/admin/vouchers/{voucher_code}', \App\Livewire\Vouchers\Profile::class)->name('admin.vouchers.profile');
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

// Merchant Dashboard - Only accessible by merchant users
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'merchant_user',
])->group(function () {
    Route::get('/merchant/dashboard', function () {
        return view('merchant.dashboard');
    })->name('merchant.dashboard');
    
    // Merchant Voucher CRUD Routes
    Route::get('/merchant/vouchers', \App\Livewire\Merchant\Vouchers\Index::class)->name('merchant.vouchers.index');
    Route::get('/merchant/vouchers/create', \App\Livewire\Merchant\Vouchers\Form::class)->name('merchant.vouchers.create');
    Route::get('/merchant/vouchers/{voucher_code}/edit', \App\Livewire\Merchant\Vouchers\Form::class)->name('merchant.vouchers.edit');
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
        } elseif ($user->isMerchantUser()) {
            return redirect()->route('merchant.dashboard');
        }
        abort(403, 'Invalid user type');
    })->name('dashboard');
});
