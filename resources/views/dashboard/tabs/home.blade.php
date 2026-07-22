<section id="page-home" class="tab-content active">
    <style>
        /* ================= STYLE RESPONSIVE BERANDA ================= */
        .grid-home {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .map-card {
            grid-column: span 2; 
        }

        .live-map-wrapper { height: 400px; }

        /* KELAS KHUSUS CARD STATISTIK (Fokus untuk Desktop) */
        .stat-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px 20px; /* Padding diperbesar agar proporsional di ruang kosong desktop */
            height: 100%;
        }

        .stat-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        .stat-title {
            font-weight: 600;
            color: var(--text-muted);
            font-size: 1rem;
            margin-bottom: 10px;
        }

        /* Angka Utama di Desktop dibikin Jauh Lebih Besar dan Jelas */
        .stat-value {
            font-size: 4.5rem; 
            font-weight: 800;
            color: var(--text-dark);
            line-height: 1;
            margin-bottom: 10px;
        }

        .stat-status {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        /* PENGATURAN KHUSUS LAYAR MOBILE (HP) */
        @media (max-width: 768px) {
            .grid-home {
                gap: 12px; 
                margin-bottom: 10px;
            }
            
            .hide-on-mobile { display: none !important; }
            
            /* Meng-override gaya desktop agar ringkas di HP */
            .stat-card {
                padding: 15px 10px !important;
            }

            .stat-title {
                font-size: 0.75rem !important;
                margin-bottom: 4px !important;
            }

            .stat-value {
                font-size: 2.2rem !important;
                margin-bottom: 0 !important;
            }

            .live-map-wrapper { height: 350px; }
        }
    </style>

    <div class="grid-home">
        
        <!-- Card 1: Detak Jantung -->
        <div class="card stat-card">
            <!-- Icon Wrapper -->
            <div class="stat-icon hide-on-mobile" style="background: var(--danger-light); color: var(--danger);">
                <svg class="icon anim-pulse" viewBox="0 0 24 24" style="width:28px; height:28px; fill:currentColor;"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
            </div>
            
            <div class="stat-title">Detak Jantung</div>
            <div class="bpm-value stat-value" id="bpm-display">--</div>
            
            <div class="stat-status hide-on-mobile" id="bpm-status">Menunggu data...</div>
            
            <!-- Tempat untuk grafik mini live -->
            <div class="hide-on-mobile" style="width: 100%; height: 60px; margin-top: 20px;">
                <canvas id="liveBpmChart"></canvas>
            </div>
        </div>

        <!-- Card 2: Rata-Rata Harian -->
        <div class="card stat-card">
            <!-- Icon Wrapper -->
            <div class="stat-icon hide-on-mobile" style="background: var(--success-light); color: var(--success);">
                <svg class="icon" viewBox="0 0 24 24" style="width:28px; height:28px; stroke:currentColor; fill:none; stroke-width:2;"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
            </div>
            
            <div class="stat-title">Rata-Rata</div>
            <div class="bpm-value stat-value" id="bpm-avg">--</div>
            
            <div class="stat-status hide-on-mobile">Kalkulasi otomatis</div>
        </div>

        <!-- Card 3: Peta Lokasi -->
        <div class="card map-card" style="padding: 20px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 15px;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="background: var(--primary-light); padding: 8px; border-radius: 8px;">
                        <svg class="icon" viewBox="0 0 24 24" style="width:18px;height:18px; color:var(--primary);"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <span style="font-weight:600; color:var(--text-dark);">Lokasi Terkini</span>
                </div>
                <span id="gps-status" style="font-size:0.75rem; font-weight:600; color:var(--text-muted);">Mengecek...</span>
            </div>
            
            <div id="live-map" class="map-container live-map-wrapper" style="border-radius: 12px; overflow: hidden; border: 1px solid var(--border-light);"></div>
        </div>

    </div>
</section>