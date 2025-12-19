<?php

use App\Http\Controllers\MemberActivityController;
use App\Http\Controllers\WhatsAppValidationController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return new UserResource($request->user());
})->middleware('auth:sanctum');

// QR Code Scan - Member Activity Tracking (Public endpoint for scanner devices)
Route::post('/member-activity/scan', [MemberActivityController::class, 'scan'])
    ->name('api.member-activity.scan');

// WhatsApp Validation Check (with CSRF protection)
Route::post('/check-whatsapp', [WhatsAppValidationController::class, 'check'])
    ->middleware('web')
    ->name('api.check-whatsapp');
