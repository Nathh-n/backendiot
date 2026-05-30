<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HeartRate;

class HeartRateController extends Controller
{
    public function store(Request $request)
    {
        // Pastikan ESP32 benar-benar mengirim data 'bpm' berupa angka
        $request->validate([
            'bpm' => 'required|numeric'
        ]);

        // Simpan ke database
        HeartRate::create([
            'bpm' => $request->bpm
        ]);

        // Beri balasan ke ESP32 bahwa data sukses diterima
        return response()->json(['message' => 'Data BPM berhasil disimpan!'], 201);
    }
}

// ini tes saja tes lagiSSS