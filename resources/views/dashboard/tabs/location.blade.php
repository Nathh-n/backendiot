<section id="page-location" class="tab-content">
    
    <style>
        /* ================= STYLE KHUSUS HALAMAN LOKASI ================= */
        .loc-grid {
            display: grid;
            grid-template-columns: 350px 1fr; 
            gap: 24px;
            align-items: start;
        }

        .map-card-large {
            background: var(--panel-bg);
            border: 1px solid var(--border-light);
            border-radius: var(--radius);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 600px; 
        }
        .map-header-large {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .large-map-container {
            flex-grow: 1;
            background: #e2e8f0; 
            z-index: 1;
        }

        .history-list-card {
            background: var(--panel-bg);
            border: 1px solid var(--border-light);
            border-radius: var(--radius);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            padding: 24px;
            height: 600px;
            overflow-y: auto; 
        }
        
        .loc-item { display: flex; gap: 15px; margin-bottom: 25px; position: relative; }
        .loc-item:not(:last-child)::after {
            content: ''; position: absolute;
            left: 21.5px; top: 35px; bottom: -25px;
            width: 2px; background: var(--border-light); z-index: 0;
        }

        .loc-time { font-size: 0.85rem; color: var(--text-muted); font-weight: 600; width: 45px; flex-shrink: 0; margin-top: 5px; }
        
        .loc-icon-wrap {
            width: 45px; height: 45px; background: var(--primary-light);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            color: var(--primary); flex-shrink: 0; z-index: 1;
            border: 4px solid var(--panel-bg); 
        }

        .loc-info { margin-top: 5px; }
        .loc-title { font-weight: 700; color: var(--text-dark); font-size: 0.95rem; margin-bottom: 4px; }
        .loc-coord { font-size: 0.75rem; color: var(--text-muted); font-family: monospace; background: var(--bg-main); padding: 2px 6px; border-radius: 6px; }

        @media (max-width: 900px) {
            .loc-grid { grid-template-columns: 1fr; } 
            .history-list-card { height: 400px; }
            .map-card-large { height: 400px; }
        }
    </style>

    <div class="calendar-widget">
        <div class="cal-header">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" onclick="changeMonth(-1)"><polyline points="15 18 9 12 15 6"></polyline></svg>
            <span id="current-month-loc">Juni 2026</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" onclick="changeMonth(1)"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </div>
        
        <div class="cal-days" id="calendar-days-loc">
        </div>

        <a href="{{ url('/export/location') }}" class="btn-action" style="display: block; text-align: center; text-decoration: none; box-sizing: border-box;">Unduh Log Rute</a>
    </div>

    <h3 class="timeline-header">Rute Perjalanan Terakhir</h3>

    <div class="loc-grid">
        <div class="history-list-card" id="loc-timeline-list">
            <p style="text-align:center; color:var(--text-muted); padding:20px;">Memilih tanggal...</p>
        </div>

        <div class="map-card-large">
            <div class="map-header-large">
                <div style="display:flex; align-items:center; gap:8px; font-weight: 700; color:var(--text-dark);">
                    <svg class="icon" viewBox="0 0 24 24" style="width:20px;height:20px; color:var(--primary);"><path d="M9 20l-5.447-2.724A1 1 0 0 1 3 16.382V5.618a1 1 0 0 1 1.447-.894L9 7l6-3 5.447 2.724A1 1 0 0 1 21 7.618v10.764a1 1 0 0 1-1.447.894L15 17l-6 3z"/><path d="M9 20V7M15 17V4"/></svg>
                    Peta Visualisasi Rute
                </div>
                <span class="badge" style="background:var(--primary-light); color:var(--primary);" id="distance-badge">Estimasi: - km</span>
            </div>
            
            <div id="history-map" class="large-map-container"></div>
        </div>
    </div>
</section>