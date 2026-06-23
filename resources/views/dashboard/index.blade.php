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

        setTimeout(() => { 
            if(pageId === 'home') liveMap.invalidateSize(); 
            if(pageId === 'location') histMap.invalidateSize(); 
        }, 100);
    }

    // ================= 2. PENGATURAN PETA LEAFLET =================
    const defaultCoord = [-7.684519, 109.622424]; 
    const liveMap = L.map('live-map', {zoomControl: false}).setView(defaultCoord, 16);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { maxZoom: 19 }).addTo(liveMap); 
    let liveMarker = L.marker(defaultCoord).addTo(liveMap);

    const histMap = L.map('history-map', {zoomControl: true}).setView(defaultCoord, 16);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { maxZoom: 19 }).addTo(histMap);
    let routeLine = L.polyline([], {color: '#68A9CF', weight: 4, dashArray: '10, 10'}).addTo(histMap); 
    let routeMarkers = L.layerGroup().addTo(histMap);

    let lastLat = null; let lastLng = null;
    let totalBpm = 0; let countBpm = 0;

    // ================= 3. LOGIKA KALENDER INTERAKTIF =================
    let currentDate = new Date(); // Menyimpan tanggal yang sedang dipilih
    const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    const dayNames = ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"];

    // Fungsi menggambar ulang angka-angka di kalender
    function renderCalendar() {
        let monthText = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
        if(document.getElementById('current-month')) document.getElementById('current-month').innerText = monthText;
        if(document.getElementById('current-month-loc')) document.getElementById('current-month-loc').innerText = monthText;

        let html = '';
        // Generate 5 hari (2 hari sebelum, Hari-H, 2 hari sesudah)
        for(let i = -2; i <= 2; i++) {
            let d = new Date(currentDate);
            d.setDate(currentDate.getDate() + i);

            let isActive = (i === 0) ? 'active' : ''; // Hari yang diklik posisinya selalu di tengah
            
            // Format YYYY-MM-DD (menghindari zona waktu bergeser)
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

    // Fungsi yang dipanggil saat angka kalender diklik
    window.changeDate = function(dateString) {
        currentDate = new Date(dateString);
        renderCalendar(); 
        
        // Update Teks Judul Riwayat
        let options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        let formattedStr = currentDate.toLocaleDateString('id-ID', options);
        document.querySelectorAll('.timeline-header').forEach(el => el.innerText = "Riwayat: " + formattedStr);

        // Panggil data dari database untuk tanggal yang baru diklik
        fetchHistoryData(dateString);
    }

    // ================= 4. FUNGSI AMBIL DATA DARI DATABASE =================
    
    // Fungsi khusus riwayat (Bisa difilter berdasarkan tanggal kalender)
    function fetchHistoryData(dateFilter = '') {
        // Javascript menambahkan query "?date=YYYY-MM-DD" ke URL API Laravelmu
        let urlBpm = '/api/history-bpm' + (dateFilter ? '?date=' + dateFilter : '');
        let urlGps = '/api/history-gps' + (dateFilter ? '?date=' + dateFilter : '');

        // 1. Ambil Riwayat Jantung
        fetch(urlBpm).then(res => res.json()).then(data => {
            const container = document.getElementById('heart-timeline-list');
            if(!container) return;
            
            if(data.length === 0) {
                container.innerHTML = '<p style="text-align:center; color:var(--text-muted); padding:30px;">Tidak ada rekam medis pada tanggal ini.</p>';
                return;
            }

            container.innerHTML = ''; 
            data.forEach(item => {
                let time = new Date(item.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
                let bpm = item.bpm;
                let sClass, nClass, sText, desc;

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
            L.circleMarker(latlngs[latlngs.length - 1], {color: '#68A9CF', radius: 8, fillOpacity: 1}).addTo(routeMarkers).bindPopup("Posisi Terakhir");
            histMap.fitBounds(routeLine.getBounds(), {padding: [30, 30]});
            
            container.innerHTML = '';
            data.forEach((item, index) => {
                let time = new Date(item.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
                let isLatest = index === 0;
                let title = isLatest ? "Posisi Terakhir Berhenti" : "Pergerakan Terlacak";
                let bg = isLatest ? "var(--danger-light)" : "var(--primary-light)";
                let color = isLatest ? "var(--danger)" : "var(--primary)";

                container.innerHTML += `
                <div class="loc-item">
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

    // Eksekusi Render Pertama Kali Saat Web Dibuka
    renderCalendar();
    fetchHistoryData(currentDate.toISOString().split('T')[0]);

    // ================= 5. INTERVAL UPDATE (REALTIME) =================
    
    // Interval 1: Update Live Beranda (Setiap 2 detik)
    setInterval(function() {
        const nowString = new Date().toLocaleTimeString('id-ID', { hour12: false });
        document.getElementById('last-update').innerText = nowString;

        fetch('/api/latest-bpm').then(res => res.json()).then(data => {
            const bpmDisplay = document.getElementById('bpm-display');
            const statusText = document.getElementById('bpm-status');
            const avgDisplay = document.getElementById('bpm-avg');
            if(data && data.bpm) {
                if((new Date().getTime() - new Date(data.created_at).getTime()) < 15000) {
                    if(bpmDisplay) { bpmDisplay.innerHTML = `${data.bpm}`; bpmDisplay.style.color = 'var(--text-dark)'; }
                    if(statusText) { statusText.innerText = "Realtime Aktif"; statusText.style.color = 'var(--success)'; }
                    totalBpm += parseInt(data.bpm); countBpm++;
                    if(avgDisplay) avgDisplay.innerHTML = Math.round(totalBpm/countBpm);
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

    // Interval 2: Refresh Linimasa Riwayat (Tiap 5 detik)
    setInterval(function() {
        // HANYA refresh riwayat jika kalender sedang berada di hari ini
        let todayStr = new Date().toISOString().split('T')[0];
        let selectedStr = currentDate.toISOString().split('T')[0];
        if(todayStr === selectedStr) {
            fetchHistoryData(selectedStr);
        }
    }, 5000);

</script>
@endpush