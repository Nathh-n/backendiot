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
            /* PALET WARNA BARU YANG LEBIH LEMBUT */
            --bg-main: #f8fafc; 
            --panel-bg: #ffffff;
            --primary: #68A9CF; /* Warna Pilihanmu */
            --primary-light: #f0f7fb; /* Transparan biru untuk hover */
            --success: #34d399;
            --success-light: #ecfdf5;
            --danger: #fb7185;
            --danger-light: #fff1f2;
            --text-dark: #334155; 
            --text-muted: #94a3b8;
            --border-light: #e2e8f0;
            --radius: 16px; 
            --sidebar-width: 250px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            background-color: var(--bg-main); color: var(--text-dark); 
            font-family: 'Poppins', sans-serif; display: flex; height: 100vh; overflow: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* ================= SIDEBAR (DESKTOP) ================= */
        .sidebar {
            width: var(--sidebar-width); background: var(--panel-bg); height: 100vh;
            padding: 30px 20px; border-right: 1px solid var(--border-light); z-index: 100;
            display: flex; flex-direction: column;
        }
        .brand {
            font-size: 1.6rem; font-weight: 800; color: var(--text-dark); text-align: center;
            margin-bottom: 40px; letter-spacing: -0.5px; display: flex; align-items: center;
            justify-content: center; gap: 10px;
        }
        .nav-menu { list-style: none; display: flex; flex-direction: column; gap: 8px; }
        .nav-item {
            padding: 12px 18px; border-radius: 12px; color: var(--text-muted); font-weight: 500;
            display: flex; align-items: center; gap: 15px; cursor: pointer; transition: all 0.3s ease;
        }
        .nav-item:hover { background: var(--bg-main); color: var(--text-dark); }
        /* Gaya tombol aktif yang lebih simpel & elegan */
        .nav-item.active { background: var(--primary-light); color: var(--primary); font-weight: 600; }
        .nav-item .icon { width: 20px; height: 20px; stroke-width: 2; transition: 0.3s; }
        .nav-item.active .icon { stroke-width: 2.5; }

        /* ================= MAIN CONTENT ================= */
        .main-wrapper { flex-grow: 1; height: 100vh; overflow-y: auto; padding: 30px; position: relative; }
        .tab-content { display: none; animation: fadeIn 0.3s ease; }
        .tab-content.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

        h1 { font-weight: 700; font-size: 1.6rem; letter-spacing: -0.5px; color: var(--text-dark);}
        
        /* Gaya Kotak (Card) yang Lebih Bersih */
        .card { 
            background: var(--panel-bg); border-radius: var(--radius); padding: 24px; 
            border: 1px solid var(--border-light); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            position: relative; display: flex; flex-direction: column;
        }

        .bpm-value { font-size: 4rem; font-weight: 800; line-height: 1; margin: 10px 0; color: var(--text-dark); letter-spacing: -1px;}
        .bpm-unit { font-size: 1.2rem; color: var(--text-muted); font-weight: 500; }
        .map-container { flex-grow: 1; min-height: 300px; border-radius: 12px; z-index: 1; }
        .badge { padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;}

        .grid-home { display: grid; grid-template-columns: 1fr 1fr 2fr; gap: 20px; }
        
        @keyframes pulse-heart { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.1); } }
        .anim-pulse { animation: pulse-heart 1.5s infinite; color: var(--danger); }
        .icon { stroke: currentColor; fill: none; stroke-linecap: round; stroke-linejoin: round; }

        /* ================= BOTTOM NAV (MOBILE) ================= */
        .bottom-nav { display: none; }

        @media (max-width: 900px) {
            .sidebar { display: none; }
            .main-wrapper { padding: 20px 15px 90px 15px; }
            .grid-home { grid-template-columns: 1fr 1fr; gap: 15px;}
            .grid-home .card:nth-child(3) { grid-column: 1 / span 2; }
            .bpm-value { font-size: 2.8rem; }

            .bottom-nav {
                display: flex; justify-content: space-around; align-items: center; position: fixed;
                bottom: 0; left: 0; right: 0; background: var(--panel-bg); padding: 12px 20px;
                border-top: 1px solid var(--border-light); z-index: 1000;
            }
            .nav-item-mobile {
                display: flex; flex-direction: column; align-items: center; gap: 4px;
                color: var(--text-muted); font-size: 0.7rem; font-weight: 500; cursor: pointer;
            }
            .nav-item-mobile .icon { width: 22px; height: 22px; stroke-width: 2; transition: 0.3s; }
            .nav-item-mobile.active { color: var(--primary); font-weight: 600;}
            .nav-item-mobile.active .icon { stroke-width: 2.5; transform: translateY(-2px); }
        }
    </style>
</head>
<body>

    @include('components.sidebar')

    <main class="main-wrapper">
        @yield('content')
    </main>

    @include('components.bottom-nav')

    @stack('scripts')

</body>
</html>