<section id="page-home" class="tab-content active">
    <div class="grid-home">
        
        <div class="card" style="align-items: center; text-align: center; justify-content: center; padding: 30px 20px;">
            <div style="background: var(--danger-light); padding: 12px; border-radius: 50%; margin-bottom: 15px;">
                <svg class="icon anim-pulse" viewBox="0 0 24 24" style="width:24px; height:24px;"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </div>
            <div style="font-weight:600; color:var(--text-muted); font-size: 0.9rem;">Detak Jantung</div>
            <div class="bpm-value" id="bpm-display">--</div>
            <div style="font-size:0.8rem; font-weight: 500; color:var(--text-muted);" id="bpm-status">Menunggu data...</div>
        </div>

        <div class="card" style="align-items: center; text-align: center; justify-content: center; padding: 30px 20px;">
            <div style="background: var(--success-light); padding: 12px; border-radius: 50%; margin-bottom: 15px;">
                <svg class="icon" viewBox="0 0 24 24" style="width:24px; height:24px; color:var(--success)"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
            </div>
            <div style="font-weight:600; color:var(--text-muted); font-size: 0.9rem;">Rata-Rata Harian</div>
            <div class="bpm-value" id="bpm-avg">--</div>
            <div style="font-size:0.8rem; font-weight: 500; color:var(--text-muted);">Kalkulasi otomatis</div>
        </div>

        <div class="card" style="padding: 20px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 15px;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="background: var(--primary-light); padding: 8px; border-radius: 8px;">
                        <svg class="icon" viewBox="0 0 24 24" style="width:18px;height:18px; color:var(--primary);"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <span style="font-weight:600; color:var(--text-dark);">Lokasi Terkini</span>
                </div>
                <span id="gps-status" style="font-size:0.75rem; font-weight:600; color:var(--text-muted);">Mengecek...</span>
            </div>
            <div id="live-map" class="map-container"></div>
        </div>

    </div>
</section>