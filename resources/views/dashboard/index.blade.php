@extends('layouts.app')

@section('content')
    <header class="flex justify-between items-center mb-4">
        <div>
            <h1 id="page-title">Beranda Ringkasan</h1>
            <p class="text-muted text-sm">Terakhir diperbarui: <span id="last-update" class="font-bold">--:--:--</span></p>
        </div>
        <span class="badge" style="background:#e6fcf5; color:#02946d;">Sistem Aktif</span>
    </header>

    @include('dashboard.tabs.home')
    @include('dashboard.tabs.heart')
    @include('dashboard.tabs.location')
@endsection

@push('scripts')
<!-- IMPORT CHART.JS DARI CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // ================= 1. FUNGSI PINDAH HALAMAN (SPA) =================
    function switchPage(pageId, element) {
        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));
        document.querySelectorAll('.nav-item-mobile').forEach(nav => nav.classList.remove('active'));
        
        document.getElementById('page-' + pageId).classList.add('active');
        
        const titles = { 'home': 'Beranda Ringkasan', 'heart': 'Analisis Detak Jantung', 'location': 'Pelacakan Rute GPS' };
        document.getElementById('page-title').innerText = titles[pageId];

        if(element.classList.contains('nav-item')) {
            element.classList.add('active');
            document.querySelectorAll('.nav-item-mobile')[Array.from(element.parentNode.children).indexOf(element)].classList.add('active');
        } else {
            element.classList.add('active');
            document.querySelectorAll('.nav-item')[Array.from(element.parentNode.children).indexOf(element)].classList.add('active');
        }

        // Fix resize issue untuk Peta dan Grafik saat pindah tab
        setTimeout(() => { 
            if(pageId === 'home') liveMap.invalidateSize(); 
            if(pageId === 'location') histMap.invalidateSize(); 
            if(pageId === 'heart' && histBpmChart) histBpmChart.resize();
        }, 100);
    }

    // ================= 2. PENGATURAN PETA LEAFLET =================
    const defaultCoord = [-7.684519, 109.622424]; 
    // MENGUBAH URL TILE LAYER AGAR LEBIH KONTRAS DAN JELAS (Menggunakan OpenStreetMap standar)
    const tileUrl = 'https://tile.openstreetmap.org/{z}/{x}/{y}.png';

    const liveMap = L.map('live-map', {zoomControl: false}).setView(defaultCoord, 16);
    L.tileLayer(tileUrl, { maxZoom: 19, attribution: '© OpenStreetMap' }).addTo(liveMap); 
    let liveMarker = L.marker(defaultCoord).addTo(liveMap);

    const histMap = L.map('history-map', {zoomControl: true}).setView(defaultCoord, 16);
    L.tileLayer(tileUrl, { maxZoom: 19, attribution: '© OpenStreetMap' }).addTo(histMap);
    let routeLine = L.polyline([], {color: '#e11d48', weight: 5, dashArray: '10, 10'}).addTo(histMap); // Warna garis diganti merah agar kontras dgn map
    let routeMarkers = L.layerGroup().addTo(histMap);

    // Fungsi klik dari timeline untuk zoom peta
    window.focusMap = function(lat, lng) {
        histMap.flyTo([lat, lng], 18, { animate: true, duration: 1.5 });
    };

    let lastLat = null; let lastLng = null;
    let totalBpm = 0; let countBpm = 0;

    // ================= 3. INISIALISASI CHART.JS (GRAFIK) =================
    
    // Grafik A: Mini Live Chart di Beranda
    const ctxLive = document.getElementById('liveBpmChart');
    let liveBpmChart = null;
    if(ctxLive) {
        liveBpmChart = new Chart(ctxLive, {
            type: 'line',
            data: { labels: [], datasets: [{ label: 'BPM', data: [], borderColor: '#f43f5e', borderWidth: 2, tension: 0.4, pointRadius: 0 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: {display:false}, tooltip: {enabled: false} }, scales: { x: {display:false}, y: {display:false, min: 40, max: 140} }, animation: { duration: 0 } }
        });
    }

    // Grafik B: History Chart di Halaman Jantung
    const ctxHist = document.getElementById('historyBpmChart');
    let histBpmChart = null;
    if(ctxHist) {
        histBpmChart = new Chart(ctxHist, {
            type: 'line',
            data: { labels: [], datasets: [{ label: 'Detak Jantung (BPM)', data: [], borderColor: '#3b82f6', backgroundColor: 'rgba(59, 130, 246, 0.1)', borderWidth: 2, tension: 0.3, fill: true }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: {display:false} }, scales: { y: { suggestedMin: 50, suggestedMax: 120 } } }
        });
    }

    // ================= 4. LOGIKA KALENDER INTERAKTIF =================
    let currentDate = new Date(); 
    const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    const dayNames = ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"];

    function renderCalendar() {
        let monthText = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
        if(document.getElementById('current-month')) document.getElementById('current-month').innerText = monthText;
        if(document.getElementById('current-month-loc')) document.getElementById('current-month-loc').innerText = monthText;

        let html = '';
        for(let i = -2; i <= 2; i++) {
            let d = new Date(currentDate);
            d.setDate(currentDate.getDate() + i);

            let isActive = (i === 0) ? 'active' : ''; 
            let dateString = new Date(d.getTime() - (d.getTimezoneOffset() * 60000)).toISOString().split('T')[0]; 

            html += `
            <div class="cal-day ${isActive}" onclick="changeDate('${dateString}')">
                <span class="cal-day-name">${dayNames[d.getDay()]}</span>
                <span class="cal-day-num">${d.getDate()}</span>
            </div>`;
        }

        if(document.getElementById('calendar-days')) document.getElementById('calendar-days').innerHTML = html;
        if(document.getElementById('calendar-days-loc')) document.getElementById('calendar-days-loc').innerHTML = html;
    }

    window.changeDate = function(dateString) {
        currentDate = new Date(dateString);
        renderCalendar(); 
        
        let options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        let formattedStr = currentDate.toLocaleDateString('id-ID', options);
        document.querySelectorAll('.timeline-header').forEach(el => {
            if(el.innerText.includes('Riwayat')) el.innerText = "Riwayat: " + formattedStr;
        });

        fetchHistoryData(dateString);
    }

    // ================= 5. FUNGSI AMBIL DATA DARI DATABASE =================
    function fetchHistoryData(dateFilter = '') {
        let urlBpm = '/api/history-bpm' + (dateFilter ? '?date=' + dateFilter : '');
        let urlGps = '/api/history-gps' + (dateFilter ? '?date=' + dateFilter : '');

        // 1. Ambil Riwayat Jantung
        fetch(urlBpm).then(res => res.json()).then(data => {
            const container = document.getElementById('heart-timeline-list');
            if(!container) return;
            
            if(data.length === 0) {
                container.innerHTML = '<p style="text-align:center; color:var(--text-muted); padding:30px;">Tidak ada rekam medis pada tanggal ini.</p>';
                if(histBpmChart) { histBpmChart.data.labels = []; histBpmChart.data.datasets[0].data = []; histBpmChart.update(); }
                return;
            }

            // UPDATE GRAFIK GARIS HISTORY
            if(histBpmChart) {
                let chartData = [...data].reverse(); // Balik data untuk urutan waktu dari kiri ke kanan di grafik
                histBpmChart.data.labels = chartData.map(item => new Date(item.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'}));
                histBpmChart.data.datasets[0].data = chartData.map(item => item.bpm);
                histBpmChart.update();
            }

            container.innerHTML = ''; 
            data.forEach(item => {
                let time = new Date(item.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
                let bpm = item.bpm;
                let sClass, nClass, sText, desc;

                // Warna kontras diaktifkan di sini
                if (bpm > 100) { sClass = 'card-danger'; nClass = 'node-danger'; sText = 'Tinggi'; desc = 'Terdeteksi detak jantung di atas normal.'; } 
                else if (bpm < 60) { sClass = 'card-warning'; nClass = 'node-warning'; sText = 'Rendah'; desc = 'Detak jantung lambat / di bawah normal.'; } 
                else { sClass = 'card-success'; nClass = 'node-success'; sText = 'Normal'; desc = 'Kondisi pasien stabil.'; }

                container.innerHTML += `
                <div class="timeline-item">
                    <div class="time-label">${time}</div>
                    <div class="timeline-node ${nClass}"></div>
                    <div class="timeline-card ${sClass}">
                        <div class="bpm-title">${bpm} BPM - ${sText}</div>
                        <div class="bpm-desc">
                            <svg class="icon" viewBox="0 0 24 24" style="width:14px;height:14px; stroke:currentColor; fill:none;"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                            ${desc}
                        </div>
                    </div>
                </div>`;
            });
        }).catch(err => console.log("Gagal memuat histori BPM"));

        // 2. Ambil Riwayat Lokasi & Gambar Rute
        fetch(urlGps).then(res => res.json()).then(data => {
            const container = document.getElementById('loc-timeline-list');
            if(!container) return;

            if(data.length === 0) {
                container.innerHTML = '<p style="text-align:center; color:var(--text-muted); padding:30px;">Tidak ada perjalanan pada tanggal ini.</p>';
                routeLine.setLatLngs([]); routeMarkers.clearLayers();
                return;
            }

            let sortedData = [...data].reverse(); 
            let latlngs = sortedData.map(item => [item.latitude, item.longitude]);
            
            routeLine.setLatLngs(latlngs);
            routeMarkers.clearLayers();
            L.circleMarker(latlngs[0], {color: '#05CD99', radius: 6, fillOpacity: 1}).addTo(routeMarkers).bindPopup("Titik Awal");
            L.circleMarker(latlngs[latlngs.length - 1], {color: '#3b82f6', radius: 8, fillOpacity: 1}).addTo(routeMarkers).bindPopup("Posisi Terakhir");
            histMap.fitBounds(routeLine.getBounds(), {padding: [30, 30]});
            
            container.innerHTML = '';
            data.forEach((item, index) => {
                let time = new Date(item.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
                let isLatest = index === 0;
                let title = isLatest ? "Posisi Terakhir Berhenti" : "Pergerakan Terlacak";
                let bg = isLatest ? "var(--danger-light)" : "var(--primary-light)";
                let color = isLatest ? "var(--danger)" : "var(--primary)";

                // Menambahkan onclick untuk menggeser peta
                container.innerHTML += `
                <div class="loc-item" onclick="focusMap(${item.latitude}, ${item.longitude})">
                    <div class="loc-icon-wrap" style="background: ${bg}; color: ${color};">
                        <svg class="icon" viewBox="0 0 24 24" style="width:20px;height:20px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <div class="loc-info">
                        <div class="loc-title">${title}</div>
                        <div class="loc-coord">${parseFloat(item.latitude).toFixed(5)}, ${parseFloat(item.longitude).toFixed(5)}</div>
                        <div style="font-size:0.8rem; color:var(--text-muted); margin-top:4px;">${time} WIB</div>
                    </div>
                </div>`;
            });
        }).catch(err => console.log("Gagal memuat histori GPS"));
    }

    renderCalendar();
    fetchHistoryData(currentDate.toISOString().split('T')[0]);

    // ================= 6. INTERVAL UPDATE (REALTIME) =================
    setInterval(function() {
        const nowString = new Date().toLocaleTimeString('id-ID', { hour12: false });
        document.getElementById('last-update').innerText = nowString;

        fetch('/api/latest-bpm').then(res => res.json()).then(data => {
            const bpmDisplay = document.getElementById('bpm-display');
            const statusText = document.getElementById('bpm-status');
            const avgDisplay = document.getElementById('bpm-avg');
            
            if(data && data.bpm) {
                let selisihWaktu = new Date().getTime() - new Date(data.created_at).getTime();
                
                if(selisihWaktu < 120000) {
                    if(bpmDisplay) { bpmDisplay.innerHTML = `${data.bpm}`; bpmDisplay.style.color = 'var(--danger)'; }
                    if(statusText) { statusText.innerText = "Aktif Merekam"; statusText.style.color = 'var(--success)'; }
                    
                    totalBpm += parseInt(data.bpm); countBpm++;
                    if(avgDisplay) avgDisplay.innerHTML = Math.round(totalBpm/countBpm);

                    // UPDATE MINI CHART DI BERANDA
                    if(liveBpmChart) {
                        liveBpmChart.data.labels.push('');
                        liveBpmChart.data.datasets[0].data.push(data.bpm);
                        if(liveBpmChart.data.labels.length > 20) { // Batasi hanya 20 titik di grafik kecil
                            liveBpmChart.data.labels.shift();
                            liveBpmChart.data.datasets[0].data.shift();
                        }
                        liveBpmChart.update();
                    }
                } else {
                    if(bpmDisplay) { bpmDisplay.innerHTML = `--`; bpmDisplay.style.color = 'var(--text-muted)'; }
                    if(statusText) { statusText.innerText = "OFF / Terputus"; statusText.style.color = 'var(--danger)'; }
                }
            }
        });
        
        fetch('/api/latest-gps').then(res => res.json()).then(data => {
            const gpsStatus = document.getElementById('gps-status');
            if(data && data.latitude && data.longitude) {
                if((new Date().getTime() - new Date(data.created_at).getTime()) < 15000) {
                    if(gpsStatus) { gpsStatus.innerText = 'Online'; gpsStatus.style.color = 'var(--success)'; }
                    if (lastLat !== data.latitude || lastLng !== data.longitude) {
                        lastLat = data.latitude; lastLng = data.longitude;
                        liveMarker.setLatLng([data.latitude, data.longitude]);
                        liveMap.setView([data.latitude, data.longitude], liveMap.getZoom());
                    }
                } else {
                    if(gpsStatus) { gpsStatus.innerText = 'Offline'; gpsStatus.style.color = 'var(--danger)'; }
                }
            }
        });
    }, 2000); 

    setInterval(function() {
        let todayStr = new Date().toISOString().split('T')[0];
        let selectedStr = currentDate.toISOString().split('T')[0];
        if(todayStr === selectedStr) {
            fetchHistoryData(selectedStr);
        }
    }, 5000);

</script>
@endpush