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

    public function exportLocation()
    {
        // Menggunakan model GpsData
        $dataGPS = \App\Models\GpsData::orderBy('created_at', 'desc')->get();
        
        // Menambahkan format Jam-Menit agar nama file lebih unik
        $fileName = 'Log_Rute_SyncRo_' . date('Y-m-d_H-i') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($dataGPS) {
            $file = fopen('php://output', 'w');
            
            // Kolom Tanggal dan Waktu dipisah agar lebih jelas dan mudah difilter di Excel
            fputcsv($file, ['Tanggal', 'Waktu', 'Latitude', 'Longitude', 'Tautan Google Maps']);

            foreach ($dataGPS as $row) {
                // Link gmaps ini perlu format yang pas tanpa spasi
                $gmapsLink = "https://www.google.com/maps/search/?api=1&query={$row->latitude},{$row->longitude}";
                
                fputcsv($file, [
                    $row->created_at->format('Y-m-d'), // Hanya Tanggal
                    $row->created_at->format('H:i:s'), // Hanya Jam
                    $row->latitude,
                    $row->longitude,
                    $gmapsLink
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}