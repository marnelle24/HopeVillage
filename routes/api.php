<?php

use App\Http\Controllers\ActivityTypeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventRegistrationController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MemberActivityController;
use App\Http\Controllers\SettingsController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return new UserResource($request->user());
// })->middleware('auth:sanctum');

// QR Code Scan - Member Activity Tracking (Public endpoint for scanner devices)
Route::post('/member-activity/scan', [MemberActivityController::class, 'scan'])
    ->name('api.member-activity.scan');


// Create the API to GET all the activity_type
Route::get('/activity-types', [ActivityTypeController::class, 'index'])
    ->name('api.activity-types.index');

// create am api to get all the Locations
Route::get('/locations', [LocationController::class, 'index'])
    ->name('api.locations.index');

// create a api to get all the active events
Route::get('/events', [EventController::class, 'getEvents'])
    ->name('api.events.index');

// API to get settings records
// Optional query parameter "key" - if empty, fetch all; if specified, return matching setting
Route::get('/settings', [SettingsController::class, 'index'])
    ->name('api.settings.index');

// create an API to get all member activities
Route::get('/member-activities', [MemberActivityController::class, 'index'])
    ->name('api.member-activities.index');

// create an API that will that will add member in the event_registration when scanning the event QR code
// The status would be `attended`, attended_at would be the current timestamp and the type would be `external_scanner`
Route::post('/event-registration/scan', [EventRegistrationController::class, 'scan'])
    ->name('api.event-registration.scan');