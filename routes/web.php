<?php

use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\PointsActionsController;
use App\Http\Controllers\VerificationCodeController;
use App\Http\Controllers\WhatsAppValidationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// WhatsApp Validation Check (needs CSRF protection, so in web routes)
Route::post('/api/check-whatsapp', [WhatsAppValidationController::class, 'check'])
    ->name('api.check-whatsapp');

// Public Merchant Application Route
Route::get('/merchant/apply', \App\Livewire\Merchant\Apply::class)->name('merchant.apply');

// Member verification (OTP via email / WhatsApp)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
])->group(function () {
    Route::get('/verify-account', [VerificationCodeController::class, 'show'])->name('verification.code.show');
    Route::post('/verify-account', [VerificationCodeController::class, 'verify'])->name('verification.code.verify');
    Route::post('/verify-account/resend', [VerificationCodeController::class, 'resend'])->name('verification.code.resend');
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
    
    // Members
    Route::get('/admin/members', \App\Livewire\Members\Index::class)->name('admin.members.index');
    Route::get('/admin/members/{fin}', \App\Livewire\Members\Profile::class)->name('admin.members.profile');

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

    // Point System CRUD Routes
    Route::get('/admin/point-system', \App\Livewire\PointSystem\Index::class)->name('admin.point-system.index');
    Route::get('/admin/point-system/create', \App\Livewire\PointSystem\Form::class)->name('admin.point-system.create');
    Route::get('/admin/point-system/{id}/edit', \App\Livewire\PointSystem\Form::class)->name('admin.point-system.edit');

    // Raffle / Roulette
    Route::get('/admin/raffle', \App\Livewire\Raffle\Roulette::class)->name('admin.raffle');

    // Points actions (admin-operated)
    Route::post('/admin/points/location-entry', [PointsActionsController::class, 'locationEntry'])->name('admin.points.location-entry');
    Route::post('/admin/points/event-attend', [PointsActionsController::class, 'attendEvent'])->name('admin.points.event-attend');
});

// Member Dashboard - Only accessible by member users
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'member',
    'hv_verified',
])->group(function () {
    Route::get('/member/dashboard', function () {
        return view('member.dashboard');
    })->name('member.dashboard');

    Route::get('/member/events', function () {
        return view('member.events');
    })->name('member.events');

    Route::get('/member/vouchers', function () {
        return view('member.vouchers');
    })->name('member.vouchers');

    // Backward/alternate URL: /member/voucher?status=...
    Route::get('/member/voucher', function () {
        return redirect()->route('member.vouchers', request()->query());
    })->name('member.voucher');

    Route::get('/member/event/{event_code}', \App\Livewire\Member\Events\Profile::class)
        ->name('member.events.profile');
    
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

    // Points actions (merchant-operated)
    Route::post('/merchant/points/voucher-redeem', [PointsActionsController::class, 'redeemVoucher'])->name('merchant.points.voucher-redeem');
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
