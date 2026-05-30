<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HeartRateController;
use App\Models\HeartRate;

// Pintu Masuk (Untuk menerima data dari ESP1)
Route::post('/heart-rate', [HeartRateController::class, 'store']);

// Pintu Keluar (Untuk mengirim data ke UI / Halaman Web)
Route::get('/latest-bpm', function() {
    return HeartRate::latest()->first();
});