<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\UnavailableSlotController;

Route::get('/bookings', [BookingController::class, 'index']); // Fetch all bookings
Route::post('/booking', [BookingController::class, 'store']); // Store booking
Route::get('/bookings/{date}', [BookingController::class, 'getBookingsByDate']);
Route::delete('/bookings/{id}', [BookingController::class, 'destroy']); // Delete booking
Route::put('/bookings/{id}', [BookingController::class, 'update']); // Update booking

Route::get('/unavailable-slots', [UnavailableSlotController::class, 'index']); // Fetch all unavailable slots
Route::post('/unavailable-slots', [UnavailableSlotController::class, 'store']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
