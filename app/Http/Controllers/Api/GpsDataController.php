<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GpsData;

class GpsDataController extends Controller
{
    // Fungsi untuk menerima data dari ESP32
    public function store(Request $request)
    {
        // Memastikan data yang dikirim tidak kosong
        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required'
        ]);

        // Menyimpan ke database
        GpsData::create([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Koordinat berhasil disimpan!'
        ], 201);
    }

    // Fungsi untuk mengirim data terbaru ke Dashboard
    public function latest()
    {
        $data = GpsData::latest()->first();
        return response()->json($data);
    }
}