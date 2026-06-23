<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>SyncRo | Live Monitoring System</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        :root {
            --bg-main: #f4f7fe; 
            --panel-bg: #ffffff;
            --primary: #4318FF; 
            --primary-light: #f4f7fe;
            --success: #05CD99;
            --success-light: #e6fcf5;
            --warning: #FFCE20;
            --warning-light: #fffbea;
            --danger: #EE5D50;
            --danger-light: #fef2f2;
            --text-dark: #2B3674; 
            --text-muted: #A3AED0;
            --border-light: #e2e8f0;
            --radius: 20px; 
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            background-color: var(--bg-main); 
            color: var(--text-dark); 
            font-family: 'Poppins', sans-serif; 
            line-height: 1.5; 
            padding: 20px; 
            -webkit-font-smoothing: antialiased;
        }
        
        .container { max-width: 1200px; margin: 0 auto; }
        
        h1 { font-weight: 700; color: var(--text-dark); font-size: 1.8rem; letter-spacing: -0.5px;}
        .text-sm { font-size: 0.875rem; }
        .text-muted { color: var(--text-muted); }
        .font-bold { font-weight: 700; }
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .gap-2 { gap: 0.5rem; }
        .mb-4 { margin-bottom: 1.5rem; }
        
        /* Layout Grids */
        .grid { display: grid; gap: 24px; margin-bottom: 24px; }
        .grid-top { grid-template-columns: 1fr 2fr; }
        .grid-chart { grid-template-columns: 1fr; }
        .grid-history { grid-template-columns: 1fr 1fr; }

        /* Cards/Panels - FIX BORDER BOCOR */
        .card { 
            background: var(--panel-bg); 
            border-radius: var(--radius); 
            padding: 24px; 
            box-shadow: 0px 10px 20px rgba(112, 144, 176, 0.08); 
            position: relative;
            display: flex;
            flex-direction: column;
        }
        
        /* Menggunakan ::before agar border membaur sempurna dengan border-radius */
        .card-accent::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 5px;
            border-top-left-radius: var(--radius);
            border-top-right-radius: var(--radius);
        }
        .card-danger::before { background-color: var(--danger); }
        .card-primary::before { background-color: var(--primary); }

        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .card-title { font-size: 1.1rem; display: flex; align-items: center; gap: 10px; color: var(--text-dark); font-weight: 700;}
        
        /* Badges */
        .badge { padding: 6px 14px; border-radius: 30px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;}
        .bg-success { background: var(--success-light); color: #02946d; }
        .bg-warning { background: var(--warning-light); color: #b38f00; }
        .bg-danger { background: var(--danger-light); color: #bd3126; }
        .bg-gray { background: #f4f7fe; color: var(--text-muted); }

        /* Specific Components */
        .bpm-value { font-size: 4.5rem; font-weight: 800; line-height: 1; margin: 20px 0; color: var(--text-dark); text-shadow: 2px 2px 4px rgba(0,0,0,0.02);}
        .bpm-unit { font-size: 1.5rem; color: var(--text-muted); font-weight: 600; }
        .icon { width: 22px; height: 22px; stroke: currentColor; stroke-width: 2.5; fill: none; stroke-linecap: round; stroke-linejoin: round; }
        
        /* Animasi Jantung Berdetak */
        @keyframes pulse-heart {
            0% { transform: scale(1); }
            25% { transform: scale(1.15); }
            50% { transform: scale(1); }
            75% { transform: scale(1.15); }
            100% { transform: scale(1); }
        }
        .anim-pulse { animation: pulse-heart 1.5s infinite; color: var(--danger); }

        /* Tables & Maps */
        table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
        th, td { padding: 14px 10px; text-align: left; border-bottom: 1px solid var(--border-light); }
        th { color: var(--text-muted); font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;}
        .table-container { overflow-x: auto; max-height: 320px; overflow-y: auto; }
        
        .map-container { flex-grow: 1; min-height: 350px; border-radius: 14px; z-index: 1; }
        .history-map-container { flex-grow: 1; min-height: 300px; border-radius: 14px; z-index: 1; }

        /* Mobile Responsif */
        @media (max-width: 900px) {
            body { padding: 15px; }
            .grid-top, .grid-history { grid-template-columns: 1fr; }
            h1 { font-size: 1.5rem; }
            .card { padding: 18px; }
            .bpm-value { font-size: 3.5rem; }
            .map-container { min-height: 250px; }
            .history-map-container { min-height: 250px; }
        }
    </style>
</head>
<body>

<div class="container">
    <header class="flex justify-between items-center mb-4" style="flex-wrap: wrap; gap: 15px;">
        <div>
            <h1>SyncRo Dashboard</h1>
            <p class="text-muted text-sm flex items-center gap-2">
                <svg class="icon" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Terakhir diperbarui: <span id="last-update" class="font-bold">--:--:--</span>
            </p>
        </div>
        <div>
            <span class="badge bg-success" style="font-size: 0.85rem;"><svg class="icon" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path d="M5 12.55a11 11 0 0 1 14.08 0M1.42 9a16 16 0 0 1 21.16 0M8.53 16.11a6 6 0 0 1 6.95 0M12 20h.01"/></svg> Sistem Aktif</span>
        </div>
    </header>

    <div class="grid grid-top">
        
        <div class="card card-accent card-danger" style="justify-content: center; text-align: center;">
            <div class="card-title" style="justify-content: center; margin-bottom: 5px;">
                <svg class="icon anim-pulse" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                Sensor Detak Jantung
            </div>
            
            <div class="bpm-value" id="bpm-display">--<span class="bpm-unit">BPM</span></div>
            
            <div><span class="badge bg-gray" id="bpm-status">Mengecek koneksi...</span></div>
            <div class="text-muted text-sm mt-3" id="bpm-time">Terakhir Aktif: -</div>
        </div>

        <div class="card card-accent card-primary" style="padding-bottom: 20px;">
            <div class="card-header">
                <div class="card-title">
                    <svg class="icon" viewBox="0 0 24 24" style="color: var(--primary);"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    Posisi Live (ESP3)
                </div>
                <span class="badge bg-gray" id="gps-status">Mengecek koneksi...</span>
            </div>
            <div id="live-map" class="map-container"></div>
        </div>
    </div>

    <div class="grid grid-chart">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg class="icon" viewBox="0 0 24 24" style="color: var(--primary);"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                    Grafik Fluktuasi Jantung (Realtime)
                </div>
            </div>
            <div style="height: 250px; width: 100%;">
                <canvas id="realtimeChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-history">
        
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg class="icon" viewBox="0 0 24 24" style="color: var(--text-dark);"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>
                    Riwayat Detak Jantung
                </div>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Waktu (WIB)</th>
                            <th>Nilai BPM</th>
                            <th>Status Medis</th>
                        </tr>
                    </thead>
                    <tbody id="history-bpm-body">
                        <tr><td colspan="3" style="text-align: center; color: #A3AED0; padding: 30px;">Memuat data sensor...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg class="icon" viewBox="0 0 24 24" style="color: var(--text-dark);"><path d="M9 20l-5.447-2.724A1 1 0 0 1 3 16.382V5.618a1 1 0 0 1 1.447-.894L9 7l6-3 5.447 2.724A1 1 0 0 1 21 7.618v10.764a1 1 0 0 1-1.447.894L15 17l-6 3z"/><path d="M9 20V7M15 17V4"/></svg>
                    Rute Perjalanan Kursi Roda
                </div>
            </div>
            <div id="history-map" class="history-map-container"></div>
        </div>
        
    </div>
</div>

<script>
    const ctx = document.getElementById('realtimeChart').getContext('2d');
    
    let gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(238, 93, 80, 0.4)');
    gradient.addColorStop(1, 'rgba(238, 93, 80, 0.0)');

    const realtimeChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [], 
            datasets: [{
                label: 'BPM', data: [], borderColor: '#EE5D50', 
                backgroundColor: gradient, borderWidth: 3,
                pointBackgroundColor: '#ffffff', pointBorderColor: '#EE5D50',
                pointRadius: 4, pointHoverRadius: 6, fill: true, tension: 0.4 
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            scales: { 
                y: { min: 40, max: 160, grid: { borderDash: [5, 5], color: '#e2e8f0' } }, 
                x: { grid: { display: false } } 
            },
            plugins: { legend: { display: false } }, 
            animation: { duration: 400, easing: 'easeOutQuart' } 
        }
    });

    const defaultCoord = [-7.684519, 109.622424]; 

    const liveMap = L.map('live-map', {zoomControl: false}).setView(defaultCoord, 16);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { maxZoom: 19 }).addTo(liveMap); 
    let liveMarker = L.marker(defaultCoord).addTo(liveMap);

    const histMap = L.map('history-map', {zoomControl: false}).setView(defaultCoord, 16);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { maxZoom: 19 }).addTo(histMap);
    let routeLine = L.polyline([], {color: '#4318FF', weight: 4, dashArray: '10, 10'}).addTo(histMap); 
    let routeMarkers = L.layerGroup().addTo(histMap);

    let lastLat = null;
    let lastLng = null;

    setInterval(function() {
        const nowString = new Date().toLocaleTimeString('id-ID', { hour12: false });
        document.getElementById('last-update').innerText = nowString;

        // FETCH BPM
        fetch('/api/latest-bpm')
            .then(res => res.json())
            .then(data => {
                const bpmDisplay = document.getElementById('bpm-display');
                const statusBadge = document.getElementById('bpm-status');
                const timeLabel = document.getElementById('bpm-time');
                
                if(data && data.bpm) {
                    const dataTime = new Date(data.created_at).getTime();
                    const nowTime = new Date().getTime();
                    
                    if((nowTime - dataTime) < 15000) {
                        let bpm = data.bpm;
                        bpmDisplay.innerHTML = `${bpm}<span class="bpm-unit">BPM</span>`;
                        bpmDisplay.style.color = 'var(--text-dark)';
                        
                        let statusText = "Normal", badgeClass = "bg-success";
                        if (bpm < 60) { statusText = "Rendah"; badgeClass = "bg-warning"; } 
                        else if (bpm > 100) { statusText = "Tinggi"; badgeClass = "bg-danger"; }

                        statusBadge.className = `badge ${badgeClass}`;
                        statusBadge.innerHTML = `<svg class="icon" viewBox="0 0 24 24" style="width:14px;height:14px;"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg> ${statusText}`;
                        timeLabel.innerText = "Koneksi Stabil";

                        realtimeChart.data.labels.push(nowString);
                        realtimeChart.data.datasets[0].data.push(bpm);
                        if (realtimeChart.data.labels.length > 20) {
                            realtimeChart.data.labels.shift(); 
                            realtimeChart.data.datasets[0].data.shift();
                        }
                        realtimeChart.update();
                    } else {
                        bpmDisplay.innerHTML = `--<span class="bpm-unit">BPM</span>`;
                        bpmDisplay.style.color = 'var(--text-muted)';
                        statusBadge.className = `badge bg-gray`;
                        statusBadge.innerText = "OFF / Terputus";
                        timeLabel.innerText = "Terakhir: " + new Date(data.created_at).toLocaleTimeString('id-ID');
                    }
                }
            }).catch(err => console.log("Gagal mengambil BPM"));

        // FETCH GPS LIVE
        fetch('/api/latest-gps')
            .then(res => res.json())
            .then(data => {
                const gpsBadge = document.getElementById('gps-status');
                
                if(data && data.latitude && data.longitude) {
                    const dataTime = new Date(data.created_at).getTime();
                    const nowTime = new Date().getTime();
                    
                    if((nowTime - dataTime) < 15000) {
                        gpsBadge.className = 'badge bg-success';
                        gpsBadge.innerText = 'Online / Terlacak';

                        if (lastLat !== data.latitude || lastLng !== data.longitude) {
                            lastLat = data.latitude;
                            lastLng = data.longitude;
                            
                            let newCoord = [data.latitude, data.longitude];
                            liveMarker.setLatLng(newCoord);
                            liveMap.setView(newCoord, liveMap.getZoom());
                        }
                    } else {
                        gpsBadge.className = 'badge bg-gray';
                        gpsBadge.innerText = 'OFF / Hilang Sinyal';
                    }
                }
            }).catch(err => console.log("Gagal mengambil GPS"));
    }, 2000); 

    setInterval(function() {
        // Fetch Tabel BPM History
        fetch('/api/history-bpm')
            .then(res => res.json())
            .then(data => {
                let tbody = document.getElementById('history-bpm-body');
                tbody.innerHTML = '';
                if(data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; color: var(--text-muted); padding:20px;">Belum ada riwayat terekam</td></tr>';
                } else {
                    data.forEach(item => {
                        let time = new Date(item.created_at).toLocaleTimeString('id-ID');
                        let statusHTML = item.bpm > 100 ? '<span class="badge bg-danger">Tinggi</span>' : (item.bpm < 60 ? '<span class="badge bg-warning">Rendah</span>' : '<span class="badge bg-success">Normal</span>');
                        tbody.innerHTML += `<tr><td style="color:var(--text-muted);">${time}</td><td class="font-bold">${item.bpm} BPM</td><td>${statusHTML}</td></tr>`;
                    });
                }
            }).catch(err => console.log("Gagal memuat histori BPM"));

        // Fetch Linimasa Rute GPS
        fetch('/api/history-gps')
            .then(res => res.json())
            .then(data => {
                if(data.length > 0) {
                    let sortedData = data.reverse(); 
                    let latlngs = sortedData.map(item => [item.latitude, item.longitude]);
                    
                    routeLine.setLatLngs(latlngs);
                    routeMarkers.clearLayers();
                    
                    L.circleMarker(latlngs[0], {color: '#05CD99', radius: 6, fillOpacity: 1}).addTo(routeMarkers).bindPopup("Titik Awal");
                    L.circleMarker(latlngs[latlngs.length - 1], {color: '#4318FF', radius: 8, fillOpacity: 1}).addTo(routeMarkers).bindPopup("Posisi Terakhir");
                    
                    histMap.fitBounds(routeLine.getBounds(), {padding: [30, 30]});
                }
            }).catch(err => console.log("Gagal memuat histori GPS"));

    }, 5000); 
</script>

</body>
</html>