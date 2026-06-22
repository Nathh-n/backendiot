<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HeartRate;

class HeartRateController extends Controller
{
    public function store(Request $request)
    {
        // 1. Pastikan ESP32 benar-benar mengirim data 'bpm' berupa angka
        $request->validate([
            'bpm' => 'required|numeric'
        ]);

        $incomingBpm = $request->bpm;

        // 2. Ambil 1 data paling terakhir yang ada di database
        $latestRecord = HeartRate::latest()->first();

        // 3. Logika Penyaringan: Cek apakah angkanya sama dengan yang terakhir
        if ($latestRecord && $latestRecord->bpm == $incomingBpm) {
            // Beri balasan OK ke ESP32 agar tidak mengira error, tapi JANGAN simpan ke DB
            return response()->json([
                'status' => 'ignored',
                'message' => 'Angka sama dengan data terakhir, tidak dimasukkan ke DB.'
            ], 200);
        }

        // 4. Jika angkanya BERBEDA (atau database kosong), baru simpan ke database!
        HeartRate::create([
            'bpm' => $incomingBpm
        ]);

        // Beri balasan ke ESP32 bahwa data sukses diterima dan disimpan
        return response()->json([
            'status' => 'saved',
            'message' => 'Data BPM berhasil disimpan!'
        ], 201);
    }
}

// ini tes saja coba lagi