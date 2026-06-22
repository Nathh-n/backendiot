<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Kursi Roda | Clean System</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --bg-main: #f1f5f9;
            --panel-bg: #ffffff;
            --primary: #3b82f6;
            --success: #10b981;
            --success-light: #d1fae5;
            --warning: #f59e0b;
            --warning-light: #fef3c7;
            --danger: #ef4444;
            --danger-light: #fee2e2;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border-light: #e2e8f0;
            --radius: 12px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background-color: var(--bg-main); color: var(--text-dark); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.5; padding: 20px; }
        
        .container { max-width: 1200px; margin: 0 auto; }
        
        /* Typography & Utility */
        h1 { font-weight: 600; color: var(--text-dark); font-size: 1.5rem; }
        .text-sm { font-size: 0.875rem; }
        .text-muted { color: var(--text-muted); }
        .font-bold { font-weight: 700; }
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .gap-2 { gap: 0.5rem; }
        .mb-4 { margin-bottom: 1rem; }
        
        /* Layout Grids */
        .grid { display: grid; gap: 20px; margin-bottom: 20px; }
        .grid-top { grid-template-columns: 1fr 1.5fr; }
        .grid-chart { grid-template-columns: 1fr; }

        @media (max-width: 900px) {
            .grid-top { grid-template-columns: 1fr; }
        }

        /* Cards/Panels */
        .card { background: var(--panel-bg); border: 1px solid var(--border-light); border-radius: var(--radius); padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid var(--border-light); padding-bottom: 10px; }
        .card-title { font-size: 1rem; display: flex; align-items: center; gap: 8px; color: var(--text-muted); font-weight: 600;}
        
        /* Badges */
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;}
        .bg-success { background: var(--success-light); color: #065f46; }
        .bg-warning { background: var(--warning-light); color: #92400e; }
        .bg-danger { background: var(--danger-light); color: #991b1b; }
        .bg-gray { background: #e2e8f0; color: var(--text-muted); }

        /* Specific Components */
        .bpm-value { font-size: 4rem; font-weight: 800; line-height: 1; margin: 15px 0; color: var(--text-dark);}
        .bpm-unit { font-size: 1.2rem; color: var(--text-muted); font-weight: 500; }
        .icon { width: 20px; height: 20px; stroke: currentColor; stroke-width: 2; fill: none; stroke-linecap: round; stroke-linejoin: round; }
    </style>
</head>
<body>

<div class="container">
    <header class="flex justify-between items-center mb-4" style="flex-wrap: wrap; gap: 15px;">
        <div>
            <h1>Live Monitoring Kursi Roda</h1>
            <p class="text-muted text-sm flex items-center gap-2">
                <svg class="icon" style="width: 16px; height: 16px;"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                Terakhir diperbarui: <span id="last-update">--:--:--</span>
            </p>
        </div>
    </header>

    <div class="grid grid-top">
        
        <div class="card" style="display: flex; flex-direction: column; justify-content: center; text-align: center; border-top: 4px solid var(--danger);">
            <div class="card-title" style="justify-content: center; margin-bottom: 10px;">
                <svg class="icon" style="stroke: var(--danger);"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                Sensor Detak Jantung (ESP1)
            </div>
            
            <div class="bpm-value" id="bpm-display">--<span class="bpm-unit">BPM</span></div>
            
            <div>
                <span class="badge bg-gray" id="bpm-status">Mengecek koneksi...</span>
            </div>
            <div class="text-muted text-sm mt-2" style="margin-top: 10px;" id="bpm-time">Terakhir Aktif: -</div>
        </div>

        <div class="card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column; min-height: 350px; border-top: 4px solid var(--primary);">
            <div class="card-header" style="margin: 15px 15px 0 15px; border-bottom: none;">
                <div class="card-title">
                    <svg class="icon"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    Pelacakan Lokasi (ESP3)
                </div>
                <span class="badge bg-gray" id="gps-status">Mengecek koneksi...</span>
            </div>
            
            <div id="map-container" style="flex-grow: 1; background: #e2e8f0; display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 14px;">
                Memuat peta...
            </div>
        </div>

    </div>

    <div class="grid grid-chart">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg class="icon"><path d="M21.21 15.89A10 10 0 1 1 8 2.83M22 12A10 10 0 0 0 12 2v10z"/></svg>
                    Grafik Heart Rate (Realtime)
                </div>
            </div>
            <div style="height: 300px;">
                <canvas id="realtimeChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    // --- KONFIGURASI CHART ---
    const ctx = document.getElementById('realtimeChart').getContext('2d');
    const realtimeChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [], 
            datasets: [{
                label: 'BPM',
                data: [], 
                borderColor: '#ef4444', 
                backgroundColor: 'rgba(239, 68, 68, 0.1)', 
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#ef4444',
                pointRadius: 4,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { min: 40, max: 160, grid: { color: '#e2e8f0' } },
                x: { grid: { display: false } }
            },
            plugins: { legend: { display: false } },
            animation: { duration: 400 } 
        }
    });

    // --- VARIABEL TRACKING ---
    let lastLat = null;
    let lastLng = null;

    // --- FUNGSI UPDATE DATA SETIAP 2 DETIK ---
    setInterval(function() {
        const nowString = new Date().toLocaleTimeString('id-ID', { hour12: false });
        document.getElementById('last-update').innerText = nowString;

        // 1. FETCH DATA HEART RATE
        fetch('/api/latest-bpm')
            .then(response => response.json())
            .then(data => {
                const bpmDisplay = document.getElementById('bpm-display');
                const statusBadge = document.getElementById('bpm-status');
                const timeLabel = document.getElementById('bpm-time');
                
                if(data && data.bpm) {
                    // Cek apakah data ini usang (lebih dari 15 detik) berdasarkan created_at database
                    const dataTime = new Date(data.created_at).getTime();
                    const nowTime = new Date().getTime();
                    
                    // Jika selisih waktu database dan sekarang kurang dari 15 detik (Aktif)
                    if((nowTime - dataTime) < 15000) {
                        let bpm = data.bpm;
                        bpmDisplay.innerHTML = `${bpm}<span class="bpm-unit">BPM</span>`;
                        bpmDisplay.style.color = '#0f172a';
                        
                        // Logika Warna Medis
                        let statusText = "Normal";
                        let badgeClass = "bg-success";
                        if (bpm < 60) { statusText = "Rendah"; badgeClass = "bg-warning"; } 
                        else if (bpm > 100) { statusText = "Tinggi"; badgeClass = "bg-danger"; }

                        statusBadge.className = `badge ${badgeClass}`;
                        statusBadge.innerText = statusText;
                        timeLabel.innerText = "Sistem Online";

                        // Update Grafik
                        realtimeChart.data.labels.push(nowString);
                        realtimeChart.data.datasets[0].data.push(bpm);
                        if (realtimeChart.data.labels.length > 20) {
                            realtimeChart.data.labels.shift(); 
                            realtimeChart.data.datasets[0].data.shift();
                        }
                        realtimeChart.update();
                    } else {
                        // ESP1 Mati / OFF (Data Terakhir sudah usang)
                        bpmDisplay.innerHTML = `--<span class="bpm-unit">BPM</span>`;
                        bpmDisplay.style.color = '#cbd5e1';
                        statusBadge.className = `badge bg-gray`;
                        statusBadge.innerText = "OFF / Terputus";
                        timeLabel.innerText = "Terakhir: " + new Date(data.created_at).toLocaleTimeString('id-ID');
                    }
                }
            })
            .catch(err => console.log("Gagal mengambil BPM"));

        // 2. FETCH DATA GPS
        fetch('/api/latest-gps')
            .then(response => response.json())
            .then(data => {
                const gpsBadge = document.getElementById('gps-status');
                
                if(data && data.latitude && data.longitude) {
                    const dataTime = new Date(data.created_at).getTime();
                    const nowTime = new Date().getTime();
                    
                    // Jika selisih kurang dari 15 detik (GPS Aktif)
                    if((nowTime - dataTime) < 15000) {
                        gpsBadge.className = 'badge bg-success';
                        gpsBadge.innerText = 'Online / Akurat';

                        // Cegah layar peta refresh berulang jika titiknya tidak berubah
                        if (lastLat !== data.latitude || lastLng !== data.longitude) {
                            lastLat = data.latitude;
                            lastLng = data.longitude;
                            
                            const mapHtml = `<iframe 
                                width="100%" height="100%" frameborder="0" scrolling="no" 
                                src="https://maps.google.com/maps?q=${data.latitude},${data.longitude}&z=17&output=embed">
                            </iframe>`;
                            
                            document.getElementById('map-container').innerHTML = mapHtml;
                        }
                    } else {
                        // ESP3 / GPS Mati (Data Terakhir sudah usang)
                        gpsBadge.className = 'badge bg-gray';
                        gpsBadge.innerText = 'OFF / Hilang Sinyal';
                    }
                }
            })
            .catch(err => console.log("Gagal mengambil GPS"));

    }, 2000); // Eksekusi pengecekan setiap 2 detik
</script>

</body>
</html>