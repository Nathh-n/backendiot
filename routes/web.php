<?php

use Illuminate\Support\Facades\Route;
// Panggil Controller yang benar (dari dalam folder Api)
use App\Http\Controllers\Api\HeartRateController;
use App\Http\Controllers\Api\GpsDataController;

Route::get('/', function () {
    return view('dashboard.index'); // Mengarahkan ke halaman dashboard
});

// Jalur untuk tombol unduh
Route::get('/export/heart-rate', [HeartRateController::class, 'exportHeartRate']);
Route::get('/export/location', [GpsDataController::class, 'exportLocation']);