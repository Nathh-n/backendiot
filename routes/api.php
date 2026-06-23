<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HeartRateController;
use App\Http\Controllers\Api\GpsDataController;
use App\Models\HeartRate;

// ==========================================
// 1. SISTEM SENSOR JANTUNG (ESP1)
// ==========================================
// Pintu Masuk (Untuk menerima data dari ESP1)
Route::post('/heart-rate', [HeartRateController::class, 'store']);

// Pintu Keluar (Untuk mengirim data ke UI / Halaman Web)
Route::get('/latest-bpm', function() {
    return HeartRate::latest()->first();
});


// ==========================================
// 2. SISTEM PELACAKAN LOKASI GPS (ESP3)
// ==========================================
// Pintu Masuk (Untuk menerima data koordinat dari ESP3)
Route::post('/gps', [GpsDataController::class, 'store']);

// Pintu Keluar (Untuk mengirim koordinat peta ke Dashboard)
Route::get('/latest-gps', [GpsDataController::class, 'latest']);

// Mengambil 10 riwayat terakhir untuk tabel
Route::get('/history-bpm', function() {
    return App\Models\HeartRate::latest()->take(10)->get();
});

Route::get('/history-gps', function() {
    return App\Models\GpsData::latest()->take(10)->get();
});