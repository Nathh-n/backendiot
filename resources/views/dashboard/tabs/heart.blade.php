<section id="page-heart" class="tab-content">
    <style>
        /* (Gunakan style bawaanmu sebelumnya yang sudah bagus, aku biarkan sama) */
        .calendar-widget { background: var(--panel-bg); border: 1px solid var(--border-light); border-radius: var(--radius); padding: 24px; margin-bottom: 30px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); }
        .cal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; font-weight: 700; font-size: 1.1rem; color: var(--text-dark); }
        .cal-header svg { cursor: pointer; width: 24px; height: 24px; color: var(--primary); transition: 0.2s;}
        .cal-header svg:hover { transform: scale(1.1); }
        .cal-days { display: flex; justify-content: space-between; margin-bottom: 25px; }
        .cal-day { display: flex; flex-direction: column; align-items: center; gap: 8px; cursor: pointer; }
        .cal-day-name { font-size: 0.8rem; color: var(--text-muted); font-weight: 600; }
        .cal-day-num { width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 1rem; font-weight: 600; color: var(--text-dark); transition: 0.3s; }
        .cal-day.active .cal-day-name { color: var(--primary); }
        .cal-day.active .cal-day-num { background: var(--primary); color: white; box-shadow: 0 4px 10px rgba(104, 169, 207, 0.35); }
        .btn-action { background: var(--primary-light); color: var(--primary); width: 100%; border: 2px solid var(--primary); padding: 12px; border-radius: 16px; font-weight: 700; font-size: 1rem; cursor: pointer; transition: all 0.2s; }
        .btn-action:hover { background: var(--primary); color: white; box-shadow: 0 4px 12px rgba(104, 169, 207, 0.3); }
        .timeline-header { font-size: 1.2rem; font-weight: 700; color: var(--text-dark); margin-bottom: 20px; }
        .timeline-container { display: flex; flex-direction: column; gap: 0; position: relative; }
        .timeline-container::before { content: ''; position: absolute; left: 62px; top: 10px; bottom: 0; width: 2px; background: var(--border-light); z-index: 0; }
        .timeline-item { display: flex; gap: 20px; margin-bottom: 25px; position: relative; z-index: 1; }
        .time-label { width: 45px; font-size: 0.85rem; color: var(--text-muted); font-weight: 600; text-align: right; margin-top: 15px; flex-shrink: 0;}
        .timeline-node { width: 14px; height: 14px; border-radius: 50%; background: white; border: 3px solid var(--primary); margin-top: 17px; flex-shrink: 0; box-shadow: 0 0 0 4px var(--bg-main); }
        .node-danger { border-color: var(--danger); }
        .node-success { border-color: var(--success); }
        .node-warning { border-color: var(--warning); }
        .timeline-card { flex-grow: 1; padding: 16px 20px; border-radius: 16px; border: 1px solid var(--border-light); }
        .card-danger { background: var(--danger-light); border-color: #fecdd3; }
        .card-success { background: var(--success-light); border-color: #a7f3d0; }
        .card-warning { background: var(--warning-light); border-color: #fde68a; }
        .bpm-title { font-weight: 700; color: var(--text-dark); font-size: 1rem; margin-bottom: 5px; }
        .bpm-desc { font-size: 0.85rem; color: var(--text-muted); display: flex; align-items: center; gap: 6px;}

        /* CSS BARU: Kotak Grafik */
        .chart-container { background: white; border: 1px solid var(--border-light); border-radius: 16px; padding: 20px; margin-bottom: 30px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); height: 280px;}
    </style>

    <div class="calendar-widget">
        <div class="cal-header">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
            <span id="current-month">Juni 2026</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </div>
        
        <div class="cal-days" id="calendar-days">
            <!-- Render via JS -->
        </div>

        <a href="{{ url('/export/heart-rate') }}" class="btn-action" style="display: block; text-align: center; text-decoration: none; box-sizing: border-box;">Unduh Laporan Medis</a>
    </div>

    <!-- ELEMEN BARU: Area Grafik Garis -->
    <h3 class="timeline-header">Tren Grafik Hari Ini</h3>
    <div class="chart-container">
        <canvas id="historyBpmChart"></canvas>
    </div>

    <h3 class="timeline-header">Riwayat Detail</h3>
    <div class="timeline-container" id="heart-timeline-list">
        <p style="text-align:center; color:var(--text-muted); padding:20px;">Memuat data dari database...</p>
    </div>
</section>