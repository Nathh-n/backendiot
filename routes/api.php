<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HeartRateController;
use App\Http\Controllers\Api\GpsDataController;
use App\Models\HeartRate;
use App\Models\GpsData;

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


// ==========================================
// 3. SISTEM RIWAYAT (DENGAN FILTER TANGGAL KALENDER)
// ==========================================

// Riwayat untuk Halaman Heart Rate
Route::get('/history-bpm', function(Request $request) {
    $query = HeartRate::query();

    // Saring data HANYA untuk tanggal yang diklik di kalender
    if ($request->has('date') && $request->date != '') {
        $query->whereDate('created_at', $request->date);
    } else {
        // Jika website baru dibuka (belum klik kalender), ambil data hari ini saja
        $query->whereDate('created_at', date('Y-m-d'));
    }

    // Ambil semua riwayat pada tanggal tersebut, urutkan dari yang paling baru
    return $query->latest()->get();
});

// Riwayat untuk Halaman Lokasi GPS
Route::get('/history-gps', function(Request $request) {
    $query = GpsData::query();

    // Saring data HANYA untuk tanggal yang diklik di kalender
    if ($request->has('date') && $request->date != '') {
        $query->whereDate('created_at', $request->date);
    } else {
        // Jika website baru dibuka (belum klik kalender), ambil data hari ini saja
        $query->whereDate('created_at', date('Y-m-d'));
    }

    // Ambil semua riwayat perjalanan pada tanggal tersebut
    return $query->latest()->get();
});