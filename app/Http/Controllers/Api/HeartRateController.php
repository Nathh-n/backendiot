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

    public function exportHeartRate()
    {
        // Menggunakan model HeartRate
        $dataJantung = \App\Models\HeartRate::orderBy('created_at', 'desc')->get();

        $fileName = 'Laporan_Jantung_SyncRo_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($dataJantung) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Tanggal & Waktu', 'BPM (Detak Jantung)', 'Status']);

            foreach ($dataJantung as $row) {
                $status = 'Normal';
                if ($row->bpm < 60 || $row->bpm > 100) {
                    $status = 'Tidak Normal (Peringatan)';
                }

                fputcsv($file, [
                    $row->created_at->format('Y-m-d H:i:s'),
                    $row->bpm,
                    $status
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

// ini tes saja coba lagi