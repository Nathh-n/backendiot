<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>SyncRo | Live Monitoring System</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        :root {
            --bg-main: #f4f7fe; 
            --panel-bg: #ffffff;
            --primary: #4318FF; 
            --primary-light: #f4f7fe;
            --success: #05CD99;
            --warning: #FFCE20;
            --danger: #EE5D50;
            --text-dark: #2B3674; 
            --text-muted: #A3AED0;
            --border-light: #e2e8f0;
            --radius: 20px; 
            --sidebar-width: 260px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            background-color: var(--bg-main); 
            color: var(--text-dark); 
            font-family: 'Poppins', sans-serif; 
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* ================= SIDEBAR (DESKTOP) ================= */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--panel-bg);
            height: 100vh;
            padding: 30px 20px;
            box-shadow: 5px 0 20px rgba(0,0,0,0.02);
            z-index: 100;
            display: flex;
            flex-direction: column;
        }
        .brand {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--primary);
            text-align: center;
            margin-bottom: 40px;
            letter-spacing: -1px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .nav-menu { list-style: none; display: flex; flex-direction: column; gap: 10px; }
        .nav-item {
            padding: 14px 20px;
            border-radius: 14px;
            color: var(--text-muted);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .nav-item:hover { background: var(--bg-main); color: var(--primary); }
        .nav-item.active { background: var(--primary); color: #fff; box-shadow: 0 10px 20px rgba(67, 24, 255, 0.2); }
        .nav-item .icon { width: 22px; height: 22px; stroke-width: 2.5; }

        /* ================= MAIN CONTENT ================= */
        .main-wrapper {
            flex-grow: 1;
            height: 100vh;
            overflow-y: auto;
            padding: 30px;
            position: relative;
        }
        .tab-content { display: none; animation: fadeIn 0.4s ease; }
        .tab-content.active { display: block; }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* Komponen Header & Card (Bawaan sebelumnya) */
        h1 { font-weight: 700; font-size: 1.8rem; letter-spacing: -0.5px;}
        .card { 
            background: var(--panel-bg); 
            border-radius: var(--radius); 
            padding: 24px; 
            box-shadow: 0px 10px 20px rgba(112, 144, 176, 0.08); 
            position: relative;
            display: flex; flex-direction: column;
        }
        .card-accent::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 5px;
            border-top-left-radius: var(--radius); border-top-right-radius: var(--radius);
        }
        .card-danger::before { background-color: var(--danger); }
        .card-primary::before { background-color: var(--primary); }
        .card-success::before { background-color: var(--success); }

        .bpm-value { font-size: 4.5rem; font-weight: 800; line-height: 1; margin: 15px 0; color: var(--text-dark); }
        .bpm-unit { font-size: 1.5rem; color: var(--text-muted); font-weight: 600; }
        .map-container { flex-grow: 1; min-height: 350px; border-radius: 14px; z-index: 1; }
        .badge { padding: 6px 14px; border-radius: 30px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;}

        .grid-home { display: grid; grid-template-columns: 1fr 1fr 2fr; gap: 24px; }
        
        /* Animasi Jantung */
        @keyframes pulse-heart { 0%, 50%, 100% { transform: scale(1); } 25%, 75% { transform: scale(1.15); } }
        .anim-pulse { animation: pulse-heart 1.5s infinite; color: var(--danger); }
        .icon { stroke: currentColor; fill: none; stroke-linecap: round; stroke-linejoin: round; }

        /* ================= BOTTOM NAV (MOBILE) ================= */
        .bottom-nav { display: none; }

        @media (max-width: 900px) {
            .sidebar { display: none; } /* Sembunyikan sidebar di HP */
            .main-wrapper { padding: 20px 15px 90px 15px; } /* Ruang untuk bottom nav */
            .grid-home { grid-template-columns: 1fr 1fr; }
            .grid-home .card:nth-child(3) { grid-column: 1 / span 2; } /* Peta memanjang di HP */
            .bpm-value { font-size: 3rem; }

            /* Munculkan Bottom Nav */
            .bottom-nav {
                display: flex;
                justify-content: space-around;
                align-items: center;
                position: fixed;
                bottom: 0; left: 0; right: 0;
                background: var(--panel-bg);
                padding: 15px 20px;
                box-shadow: 0 -5px 20px rgba(0,0,0,0.05);
                border-top-left-radius: 25px;
                border-top-right-radius: 25px;
                z-index: 1000;
            }
            .nav-item-mobile {
                display: flex; flex-direction: column; align-items: center; gap: 4px;
                color: var(--text-muted); font-size: 0.7rem; font-weight: 600; cursor: pointer;
            }
            .nav-item-mobile .icon { width: 24px; height: 24px; stroke-width: 2; transition: 0.3s; }
            .nav-item-mobile.active { color: var(--primary); }
            .nav-item-mobile.active .icon { stroke-width: 3; transform: translateY(-3px); }
        }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="brand">
            <svg class="icon" viewBox="0 0 24 24" style="width:28px; height:28px; stroke:var(--primary); stroke-width:3"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
            SyncRo
        </div>
        <ul class="nav-menu">
            <li class="nav-item active" onclick="switchPage('home', this)">
                <svg class="icon" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Beranda
            </li>
            <li class="nav-item" onclick="switchPage('heart', this)">
                <svg class="icon" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                Heart Rate
            </li>
            <li class="nav-item" onclick="switchPage('location', this)">
                <svg class="icon" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Lokasi GPS
            </li>
        </ul>
    </aside>

    <main class="main-wrapper">
        
        <header class="flex justify-between items-center mb-4">
            <div>
                <h1 id="page-title">Beranda Ringkasan</h1>
                <p class="text-muted text-sm">Terakhir diperbarui: <span id="last-update" class="font-bold">--:--:--</span></p>
            </div>
            <span class="badge" style="background:#e6fcf5; color:#02946d;">Sistem Aktif</span>
        </header>

        <section id="page-home" class="tab-content active">
            <div class="grid-home">
                
                <div class="card card-accent card-danger" style="text-align: center;">
                    <div style="font-weight:700; color:var(--text-dark); display:flex; align-items:center; justify-content:center; gap:8px;">
                        <svg class="icon anim-pulse" viewBox="0 0 24 24" style="width:20px; height:20px;"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                        BPM Saat Ini
                    </div>
                    <div class="bpm-value" id="bpm-display">--</div>
                    <div style="font-size:0.85rem; color:var(--text-muted);" id="bpm-status">Menunggu data...</div>
                </div>

                <div class="card card-accent card-success" style="text-align: center;">
                    <div style="font-weight:700; color:var(--text-dark); display:flex; align-items:center; justify-content:center; gap:8px;">
                        <svg class="icon" viewBox="0 0 24 24" style="width:20px; height:20px; color:var(--success)"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                        Rata-Rata Harian
                    </div>
                    <div class="bpm-value" id="bpm-avg">--</div>
                    <div style="font-size:0.85rem; color:var(--text-muted);">Kalkulasi otomatis</div>
                </div>

                <div class="card card-accent card-primary" style="padding:15px; padding-bottom: 15px;">
                    <div style="font-weight:700; color:var(--text-dark); margin-bottom:10px; display:flex; justify-content:space-between;">
                        <span style="display:flex; align-items:center; gap:8px;">
                            <svg class="icon" viewBox="0 0 24 24" style="width:18px;height:18px; color:var(--primary);"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            Lokasi Terkini
                        </span>
                        <span id="gps-status" style="font-size:0.75rem; color:var(--text-muted);">Mengecek...</span>
                    </div>
                    <div id="live-map" class="map-container" style="min-height: 200px;"></div>
                </div>

            </div>
        </section>

        <section id="page-heart" class="tab-content">
            <div class="card card-accent card-danger" style="min-height: 400px; display:flex; align-items:center; justify-content:center; text-align:center;">
                <div>
                    <svg class="icon" viewBox="0 0 24 24" style="width:60px; height:60px; color:var(--text-muted); opacity:0.3; margin-bottom:15px;"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    <h2 style="color:var(--text-muted);">Halaman Detail Heart Rate</h2>
                    <p style="color:var(--text-muted); font-size:0.9rem;">(Ruang untuk Grafik dan Tabel Riwayat Jantung)</p>
                </div>
            </div>
        </section>

        <section id="page-location" class="tab-content">
            <div class="card card-accent card-primary" style="min-height: 400px; display:flex; align-items:center; justify-content:center; text-align:center;">
                <div>
                    <svg class="icon" viewBox="0 0 24 24" style="width:60px; height:60px; color:var(--text-muted); opacity:0.3; margin-bottom:15px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <h2 style="color:var(--text-muted);">Halaman Detail Lokasi</h2>
                    <p style="color:var(--text-muted); font-size:0.9rem;">(Ruang untuk Peta Linimasa dan Histori Perjalanan)</p>
                </div>
            </div>
        </section>

    </main>

    <nav class="bottom-nav">
        <div class="nav-item-mobile active" onclick="switchPage('home', this)">
            <svg class="icon" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Beranda
        </div>
        <div class="nav-item-mobile" onclick="switchPage('heart', this)">
            <svg class="icon" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            Jantung
        </div>
        <div class="nav-item-mobile" onclick="switchPage('location', this)">
            <svg class="icon" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            Lokasi
        </div>
    </nav>

<script>
    // ================= FUNGSI PINDAH HALAMAN (SPA) =================
    function switchPage(pageId, element) {
        // 1. Sembunyikan semua konten tab
        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
        // 2. Hapus class 'active' dari semua menu (Desktop & Mobile)
        document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));
        document.querySelectorAll('.nav-item-mobile').forEach(nav => nav.classList.remove('active'));
        
        // 3. Tampilkan tab yang dipilih
        document.getElementById('page-' + pageId).classList.add('active');
        
        // 4. Ubah Judul Halaman
        const titles = { 'home': 'Beranda Ringkasan', 'heart': 'Analisis Detak Jantung', 'location': 'Pelacakan Rute GPS' };
        document.getElementById('page-title').innerText = titles[pageId];

        // 5. Aktifkan tombol yang diklik (Sinkronisasi Desktop & Mobile)
        if(element.classList.contains('nav-item')) {
            element.classList.add('active');
            // Cari elemen mobile yang sesuai dan aktifkan juga
            document.querySelectorAll('.nav-item-mobile')[Array.from(element.parentNode.children).indexOf(element)].classList.add('active');
        } else {
            element.classList.add('active');
            // Cari elemen desktop yang sesuai dan aktifkan juga
            document.querySelectorAll('.nav-item')[Array.from(element.parentNode.children).indexOf(element)].classList.add('active');
        }

        // Fix Bug Map Leaflet (Peta sering abu-abu jika diload saat tab tersembunyi)
        if(pageId === 'home') {
            setTimeout(() => { liveMap.invalidateSize(); }, 100);
        }
    }

    // ================= PENGATURAN PETA LEAFLET (MINI) =================
    const defaultCoord = [-7.684519, 109.622424]; 
    const liveMap = L.map('live-map', {zoomControl: false}).setView(defaultCoord, 16);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { maxZoom: 19 }).addTo(liveMap); 
    let liveMarker = L.marker(defaultCoord).addTo(liveMap);

    let lastLat = null;
    let lastLng = null;
    
    // Variabel kalkulasi rata-rata (Dummy sementara, idealnya hitung dari database)
    let totalBpm = 0;
    let countBpm = 0;

    // ================= PENGAMBILAN DATA (2 DETIK) =================
    setInterval(function() {
        const nowString = new Date().toLocaleTimeString('id-ID', { hour12: false });
        document.getElementById('last-update').innerText = nowString;

        // FETCH BPM
        fetch('/api/latest-bpm')
            .then(res => res.json())
            .then(data => {
                const bpmDisplay = document.getElementById('bpm-display');
                const statusText = document.getElementById('bpm-status');
                const avgDisplay = document.getElementById('bpm-avg');
                
                if(data && data.bpm) {
                    const dataTime = new Date(data.created_at).getTime();
                    const nowTime = new Date().getTime();
                    
                    if((nowTime - dataTime) < 15000) {
                        let bpm = data.bpm;
                        bpmDisplay.innerHTML = `${bpm}`;
                        bpmDisplay.style.color = 'var(--text-dark)';
                        statusText.innerText = "Realtime Aktif";
                        statusText.style.color = 'var(--success)';

                        // Kalkulasi Rata-rata sederhana
                        totalBpm += parseInt(bpm);
                        countBpm++;
                        avgDisplay.innerHTML = Math.round(totalBpm/countBpm);
                    } else {
                        bpmDisplay.innerHTML = `--`;
                        bpmDisplay.style.color = 'var(--text-muted)';
                        statusText.innerText = "OFF / Terputus";
                        statusText.style.color = 'var(--danger)';
                    }
                }
            }).catch(err => console.log("Gagal mengambil BPM"));

        // FETCH GPS LIVE
        fetch('/api/latest-gps')
            .then(res => res.json())
            .then(data => {
                const gpsStatus = document.getElementById('gps-status');
                
                if(data && data.latitude && data.longitude) {
                    const dataTime = new Date(data.created_at).getTime();
                    const nowTime = new Date().getTime();
                    
                    if((nowTime - dataTime) < 15000) {
                        gpsStatus.innerText = 'Online';
                        gpsStatus.style.color = 'var(--success)';

                        if (lastLat !== data.latitude || lastLng !== data.longitude) {
                            lastLat = data.latitude;
                            lastLng = data.longitude;
                            
                            let newCoord = [data.latitude, data.longitude];
                            liveMarker.setLatLng(newCoord);
                            liveMap.setView(newCoord, liveMap.getZoom());
                        }
                    } else {
                        gpsStatus.innerText = 'Offline';
                        gpsStatus.style.color = 'var(--danger)';
                    }
                }
            }).catch(err => console.log("Gagal mengambil GPS"));
    }, 2000); 

</script>

</body>
</html>