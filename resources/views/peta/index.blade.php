<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Informasi Geografis Pemetaan Sekolah Provinsi Lampung">
    <title>SIG Sekolah Lampung — Pemetaan & Aksesibilitas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    @vite(['resources/css/app.css'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; }

        /* SCROLLBAR */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d4d4d8; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #a1a1aa; }

        /* LEAFLET POPUP */
        .leaflet-popup-content-wrapper { background: #ffffff; color: #1c1917; border: 1px solid #e7e5e4; border-radius: 12px; box-shadow: 0 12px 32px rgb(0 0 0 / 0.1); }
        .leaflet-popup-tip { background: #ffffff; }
        .leaflet-popup-content { margin: 14px; }
        .leaflet-container a.leaflet-popup-close-button { color: #64748b; padding: 4px; }

        /* BODY */
        body { background: #f4f4f5; color: #18181b; font-family: 'Plus Jakarta Sans', sans-serif; height: 100vh; display: flex; flex-direction: column; overflow: hidden; margin: 0; }

        /* HEADER */
        .app-header { height: 52px; background: #fff; border-bottom: 1px solid #e4e4e7; display: flex; align-items: center; justify-content: space-between; padding: 0 18px; z-index: 50; flex-shrink: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
        .header-logo { background: #2d6a4f; color: #fff; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .header-title h1 { font-size: 14px; font-weight: 700; color: #18181b; margin: 0; }
        .header-title p { font-size: 11px; color: #71717a; margin: 0; font-weight: 400; }
        .header-stats { display: flex; align-items: center; gap: 16px; }
        .stat-pill { display: flex; flex-direction: column; align-items: flex-end; }
        .stat-pill span:first-child { font-size: 10px; color: #a1a1aa; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 500; }
        .stat-pill span:last-child { font-family: 'DM Mono', monospace; font-weight: 500; color: #18181b; font-size: 15px; line-height: 1.2; }
        .stat-divider { width: 1px; height: 24px; background: #e4e4e7; }

        /* MAIN LAYOUT */
        .app-main { display: flex; flex: 1; overflow: hidden; }

        /* SIDEBAR */
        .app-sidebar { width: 300px; background: #fff; border-right: 1px solid #e4e4e7; display: flex; flex-direction: column; flex-shrink: 0; z-index: 40; }

        /* TABS */
        .tab-bar { display: flex; padding: 8px 10px 0; border-bottom: 1px solid #e4e4e7; background: #fff; gap: 2px; }
        .tab-btn { flex: 1; padding: 7px 0; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 0.15s; border: none; border-bottom: 2px solid transparent; background: transparent; color: #a1a1aa; font-family: inherit; margin-bottom: -1px; }
        .tab-btn:hover { color: #3f3f46; }
        .tab-btn.active { color: #2d6a4f; border-bottom-color: #2d6a4f; }

        /* SIDEBAR CONTENT */
        .sidebar-scroll { flex: 1; overflow-y: auto; }
        .sidebar-section { padding: 14px 16px; border-bottom: 1px solid #f4f4f5; }
        .section-label { font-size: 10px; font-weight: 600; color: #a1a1aa; text-transform: uppercase; letter-spacing: 0.07em; display: flex; align-items: center; gap: 5px; margin-bottom: 10px; }

        /* INPUTS */
        .input-search { width: 100%; background: #fafafa; border: 1px solid #e4e4e7; border-radius: 6px; padding: 8px 10px 8px 32px; font-size: 12.5px; color: #18181b; font-family: inherit; outline: none; transition: border-color 0.15s, box-shadow 0.15s; }
        .input-search:focus { border-color: #2d6a4f; box-shadow: 0 0 0 3px rgba(45,106,79,0.1); background: #fff; }
        .input-search::placeholder { color: #a1a1aa; }
        .input-wrap { position: relative; margin-bottom: 8px; }
        .input-icon { position: absolute; left: 9px; top: 50%; transform: translateY(-50%); color: #a1a1aa; width: 15px; height: 15px; }
        select.form-select { width: 100%; background: #fafafa; border: 1px solid #e4e4e7; border-radius: 6px; padding: 8px 10px; font-size: 12.5px; color: #18181b; font-family: inherit; outline: none; transition: border-color 0.15s; cursor: pointer; margin-bottom: 8px; -webkit-appearance: none; }
        select.form-select:focus { border-color: #2d6a4f; box-shadow: 0 0 0 3px rgba(45,106,79,0.1); }

        /* BUTTONS */
        .btn-primary { width: 100%; background: #2d6a4f; color: #fff; border: none; border-radius: 6px; padding: 8px 12px; font-size: 12.5px; font-weight: 600; font-family: inherit; cursor: pointer; transition: all 0.15s; }
        .btn-primary:hover { background: #1b4332; box-shadow: 0 2px 8px rgba(45,106,79,0.3); }
        .btn-icon { padding: 7px; background: #fafafa; border: 1px solid #e4e4e7; border-radius: 6px; color: #71717a; cursor: pointer; transition: all 0.15s; display: flex; align-items: center; justify-content: center; }
        .btn-icon:hover { background: #f0f0f0; }
        .btn-row { display: flex; gap: 7px; }

        /* RADIUS BUTTONS */
        .radius-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 5px; margin-bottom: 8px; }
        .btn-radius { background: #fafafa; border: 1px solid #e4e4e7; border-radius: 6px; padding: 7px 4px; font-size: 11.5px; color: #52525b; font-family: inherit; cursor: pointer; text-align: center; transition: all 0.15s; font-weight: 500; }
        .btn-radius:hover { border-color: #2d6a4f; color: #2d6a4f; background: #f0fdf4; }
        .btn-radius.active { background: #2d6a4f; border-color: #2d6a4f; color: #fff; font-weight: 600; }
        .btn-danger-ghost { width: 100%; background: #fff5f5; border: 1px solid #fecaca; border-radius: 6px; padding: 7px 12px; font-size: 12px; color: #dc2626; font-family: inherit; cursor: pointer; transition: all 0.15s; }
        .btn-danger-ghost:hover { background: #fee2e2; }
        .btn-outline { width: 100%; background: #fafafa; border: 1px solid #e4e4e7; border-radius: 6px; padding: 8px 12px; font-size: 12.5px; color: #3f3f46; font-family: inherit; cursor: pointer; transition: all 0.15s; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn-outline:hover { border-color: #2d6a4f; color: #2d6a4f; }
        .btn-blue { background: #2d6a4f; color: #fff; border-color: transparent; }
        .btn-blue:hover { background: #1b4332; }

        /* LEGENDA */
        .legend-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 7px; }
        .legend-item { display: flex; align-items: center; gap: 7px; font-size: 12px; color: #3f3f46; font-weight: 500; }
        .legend-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }

        /* STATISTIK */
        .stat-bar-item { margin-bottom: 12px; }
        .stat-bar-label { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 5px; }
        .stat-bar-label span:first-child { font-weight: 500; color: #44403c; }
        .stat-bar-label span:last-child { color: #78716c; }
        .stat-bar-track { height: 6px; width: 100%; background: #f5f5f4; border-radius: 99px; overflow: hidden; }
        .stat-bar-fill { height: 100%; border-radius: 99px; transition: width 0.8s cubic-bezier(0.4,0,0.2,1); }

        /* INFO BOX */
        .info-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 11px 13px; font-size: 12px; color: #166534; line-height: 1.75; }

        /* PRIORITY CARDS */
        .priority-card { background: #fff; border: 1px solid #e4e4e7; border-radius: 8px; padding: 11px 13px; cursor: pointer; transition: all 0.15s; margin-bottom: 7px; }
        .priority-card:hover { border-color: #2d6a4f; box-shadow: 0 2px 8px rgba(45,106,79,0.1); transform: translateY(-1px); }
        .priority-name { font-weight: 600; font-size: 12.5px; color: #18181b; transition: color 0.15s; }
        .priority-card:hover .priority-name { color: #2d6a4f; }
        .priority-badge { font-size: 9px; background: #fef2f2; color: #dc2626; padding: 2px 6px; border-radius: 4px; border: 1px solid #fecaca; text-transform: uppercase; letter-spacing: 0.04em; font-weight: 600; }
        .priority-loc { font-size: 11px; color: #71717a; display: flex; align-items: center; gap: 4px; margin: 3px 0 6px; }
        .priority-detail { background: #fafafa; border-radius: 6px; padding: 7px 10px; font-size: 11.5px; color: #52525b; border: 1px solid #e4e4e7; }

        /* MAP STATUS OVERLAY */
        .map-status { position: absolute; bottom: 20px; left: 20px; background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); border: 1px solid #e4e4e7; padding: 8px 14px; border-radius: 8px; font-size: 12px; font-weight: 500; z-index: 1000; display: flex; align-items: center; gap: 8px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); transition: opacity 0.3s; color: #3f3f46; }

        /* RADIUS INFO */
        .radius-info-panel { position: absolute; top: 16px; right: 16px; background: #fff; border: 1px solid #e4e4e7; padding: 16px; border-radius: 10px; box-shadow: 0 4px 24px rgba(0,0,0,0.1); z-index: 1000; width: 260px; }
        .ri-title { font-size: 10px; font-weight: 600; color: #a1a1aa; text-transform: uppercase; letter-spacing: 0.07em; display: flex; align-items: center; gap: 7px; }
        .ri-count { font-size: 36px; font-family: 'DM Mono', monospace; font-weight: 500; color: #2d6a4f; margin: 4px 0 0; }
        .ri-desc { font-size: 11.5px; color: #71717a; margin-bottom: 12px; }
        .ri-divider { height: 1px; background: #f4f4f5; margin-bottom: 10px; }
        .ri-list-label { font-size: 9.5px; color: #a1a1aa; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 600; margin-bottom: 7px; }
        .ri-school-item { background: #fafafa; padding: 8px 10px; border-radius: 6px; border: 1px solid #f4f4f5; display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; transition: border-color 0.15s; }
        .ri-school-item:hover { border-color: #d4d4d8; }
        .ri-school-name { font-weight: 500; font-size: 12px; color: #18181b; }
        .ri-school-sub { font-size: 10px; color: #71717a; }
        .ri-school-dist { font-size: 11.5px; font-family: 'DM Mono', monospace; color: #2d6a4f; font-weight: 500; }

        /* PULSE ANIMATION */
        @keyframes pulse-ring { 0% { transform: scale(0.8); opacity: 0.5; } 100% { transform: scale(2.4); opacity: 0; } }
        .marker-pulse::before { content: ''; position: absolute; left: -50%; top: -50%; width: 200%; height: 200%; border-radius: 50%; animation: pulse-ring 2s cubic-bezier(0.215, 0.61, 0.355, 1) infinite; }

        /* POPUP INNER */
        .popup-title { font-weight: 700; font-size: 14px; color: #1c1917; margin-bottom: 8px; }
        .popup-badge { font-size: 10px; padding: 2px 8px; border-radius: 4px; background: #f5f5f4; border: 1px solid #e7e5e4; color: #57534e; }
        .popup-meta { font-size: 12px; color: #78716c; display: flex; align-items: center; gap: 6px; margin-bottom: 4px; }
        .popup-divider { height: 1px; background: #f5f5f4; margin: 10px 0; }
        .popup-btn-radius { width: 100%; background: #f0fdfa; border: 1px solid #99f6e4; border-radius: 8px; padding: 8px; font-size: 12px; font-weight: 600; color: #0d9488; font-family: inherit; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 6px; }
        .popup-btn-radius:hover { background: #ccfbf1; }
        .popup-detail-box { background: #fafaf9; border: 1px solid #e7e5e4; border-radius: 8px; padding: 8px 10px; font-size: 12px; margin-top: 8px; }
        .popup-detail-row { display: flex; justify-content: space-between; margin-bottom: 4px; }
        .popup-detail-row:last-child { margin-bottom: 0; }
        .popup-detail-label { color: #a8a29e; }
        .popup-detail-value { font-weight: 500; color: #1c1917; }
        .popup-note { font-size: 10px; color: #a8a29e; font-style: italic; margin-top: 6px; }
    </style>
</head>
<body>

<!-- HEADER -->
<header class="app-header">
    <div style="display:flex;align-items:center;gap:12px;">
        <div class="header-logo">
            <i data-lucide="map" style="width:20px;height:20px;"></i>
        </div>
        <div class="header-title">
            <h1>SIG Sekolah Lampung</h1>
            <p>Pemetaan &amp; Aksesibilitas Pendidikan</p>
        </div>
    </div>
    <div class="header-stats">
        <div class="stat-pill">
            <span>Total Sekolah</span>
            <span id="header-total">-</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-pill">
            <span>Ditampilkan</span>
            <span id="header-tampil">-</span>
        </div>
    </div>
</header>

<!-- MAIN -->
<div class="app-main">
    <!-- SIDEBAR -->
    <aside class="app-sidebar">
        <!-- TABS -->
        <div class="tab-bar">
            <button onclick="switchTab('sekolah')" id="tab-btn-sekolah" class="tab-btn active">Data Sekolah</button>
            <button onclick="switchTab('bantuan')" id="tab-btn-bantuan" class="tab-btn">Bantuan Pendidikan</button>
        </div>

        <div class="sidebar-scroll">
            <!-- TAB: SEKOLAH -->
            <div id="tab-sekolah">
                <!-- Search & Filter -->
                <div class="sidebar-section">
                    <div class="section-label">
                        <i data-lucide="search" style="width:14px;height:14px;"></i> Pencarian &amp; Filter
                    </div>
                    <div class="input-wrap">
                        <i data-lucide="search" class="input-icon"></i>
                        <input type="text" id="filter-search" placeholder="Cari nama sekolah..." class="input-search">
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:7px;margin-bottom:7px;">
                        <select id="filter-stage" class="form-select" style="margin-bottom:0;">
                            <option value="all">Semua Jenjang</option>
                            <option value="SD">SD</option>
                            <option value="SMP">SMP</option>
                            <option value="SMA">SMA</option>
                            <option value="SMK">SMK</option>
                            <option value="SLB">SLB</option>
                        </select>
                        <select id="filter-status" class="form-select" style="margin-bottom:0;">
                            <option value="all">Semua Status</option>
                            <option value="Negeri">Negeri</option>
                            <option value="Swasta">Swasta</option>
                        </select>
                    </div>
                    <select id="filter-kondisi" class="form-select">
                        <option value="all">Semua Kondisi Fasilitas</option>
                        <option value="Baik">🟢 Baik</option>
                        <option value="Sedang">🟡 Sedang</option>
                        <option value="Minim">🔴 Minim</option>
                    </select>
                    <div class="btn-row">
                        <button onclick="applyFilterSekolah()" class="btn-primary" style="flex:1;">Terapkan Filter</button>
                        <button onclick="resetFilterSekolah()" class="btn-icon" title="Reset Filter">
                            <i data-lucide="rotate-ccw" style="width:16px;height:16px;"></i>
                        </button>
                    </div>
                </div>

                <!-- Radius Layanan -->
                <div class="sidebar-section">
                    <div class="section-label">
                        <i data-lucide="target" style="width:14px;height:14px;"></i> Radius Layanan
                    </div>
                    <div class="radius-grid">
                        <button onclick="setRadius(500)" class="btn-radius" data-val="500">500m</button>
                        <button onclick="setRadius(1000)" class="btn-radius" data-val="1000">1km</button>
                        <button onclick="setRadius(3000)" class="btn-radius" data-val="3000">3km</button>
                        <button onclick="setRadius(5000)" class="btn-radius" data-val="5000">5km</button>
                    </div>
                    <button onclick="clearRadius()" class="btn-danger-ghost">Hapus Radius Aktif</button>
                </div>

                <!-- Legenda Jenjang -->
                <div class="sidebar-section">
                    <div class="section-label">
                        <i data-lucide="list" style="width:14px;height:14px;"></i> Legenda Jenjang
                    </div>
                    <div class="legend-grid">
                        <div class="legend-item"><div class="legend-dot" style="background:#f59e0b;"></div>SD</div>
                        <div class="legend-item"><div class="legend-dot" style="background:#10b981;"></div>SMP</div>
                        <div class="legend-item"><div class="legend-dot" style="background:#3b82f6;"></div>SMA</div>
                        <div class="legend-item"><div class="legend-dot" style="background:#a855f7;"></div>SMK</div>
                        <div class="legend-item"><div class="legend-dot" style="background:#f43f5e;"></div>SLB</div>
                    </div>
                </div>

                <!-- Legenda Kondisi Fasilitas -->
                <div class="sidebar-section">
                    <div class="section-label">
                        <i data-lucide="building-2" style="width:14px;height:14px;"></i> Kondisi Fasilitas
                    </div>
                    <div style="display:flex;flex-direction:column;gap:6px;">
                        <div class="legend-item">
                            <div style="width:10px;height:10px;border-radius:3px;background:#22c55e;flex-shrink:0;"></div>
                            <span>Baik</span>
                        </div>
                        <div class="legend-item">
                            <div style="width:10px;height:10px;border-radius:3px;background:#f59e0b;flex-shrink:0;"></div>
                            <span>Sedang</span>
                        </div>
                        <div class="legend-item">
                            <div style="width:10px;height:10px;border-radius:3px;background:#ef4444;flex-shrink:0;"></div>
                            <span>Minim</span>
                        </div>
                    </div>
                    <div style="margin-top:8px;font-size:10px;color:#a1a1aa;font-style:italic;">* Saat filter kondisi aktif, warna marker mengikuti kondisi fasilitas</div>
                </div>

                <!-- Statistik Jenjang -->
                <div class="sidebar-section">
                    <div class="section-label">
                        <i data-lucide="bar-chart-2" style="width:14px;height:14px;"></i> Statistik Jenjang
                    </div>
                    <div id="stat-jenjang"><!-- Filled via JS --></div>
                </div>

                <!-- Statistik Kondisi Fasilitas -->
                <div class="sidebar-section" style="border-bottom:none;">
                    <div class="section-label">
                        <i data-lucide="activity" style="width:14px;height:14px;"></i> Statistik Kondisi Fasilitas
                    </div>
                    <div id="stat-kondisi"><!-- Filled via JS --></div>
                </div>
            </div>

            <!-- TAB: BANTUAN -->
            <div id="tab-bantuan" style="display:none;">
                <div class="sidebar-section">
                    <div class="info-box">
                        Warna marker mengikuti status bantuan:<br>
                        <span style="color:#ef4444;font-weight:600;">• Merah:</span> Dibutuhkan<br>
                        <span style="color:#f59e0b;font-weight:600;">• Kuning:</span> Proses<br>
                        <span style="color:#22c55e;font-weight:600;">• Hijau:</span> Tersalurkan
                    </div>
                </div>

                <!-- Filter Bantuan -->
                <div class="sidebar-section">
                    <div class="section-label">
                        <i data-lucide="filter" style="width:14px;height:14px;"></i> Filter Bantuan
                    </div>
                    <select id="filter-jenis-bantuan" class="form-select">
                        <option value="all">Semua Jenis Bantuan</option>
                        <option value="Buku Pelajaran">Buku Pelajaran</option>
                        <option value="Buku Perpustakaan">Buku Perpustakaan</option>
                        <option value="Komputer">Komputer</option>
                        <option value="Internet">Internet</option>
                        <option value="Meja Kursi">Meja Kursi</option>
                        <option value="Renovasi Ruang Kelas">Renovasi Ruang Kelas</option>
                        <option value="Peralatan Laboratorium">Peralatan Laboratorium</option>
                        <option value="Peralatan Praktik">Peralatan Praktik</option>
                    </select>
                    <select id="filter-status-bantuan" class="form-select">
                        <option value="all">Semua Status</option>
                        <option value="Dibutuhkan">Dibutuhkan</option>
                        <option value="Proses">Proses</option>
                        <option value="Tersalurkan">Tersalurkan</option>
                    </select>
                    <select id="filter-prioritas-bantuan" class="form-select">
                        <option value="all">Semua Prioritas</option>
                        <option value="Tinggi">Tinggi</option>
                        <option value="Sedang">Sedang</option>
                        <option value="Rendah">Rendah</option>
                    </select>
                    <button onclick="applyFilterBantuan()" class="btn-primary">Terapkan Filter</button>
                </div>

                <!-- Jalur Distribusi -->
                <div class="sidebar-section">
                    <div class="section-label">
                        <i data-lucide="route" style="width:14px;height:14px;"></i> Jalur Distribusi
                    </div>
                    <button onclick="toggleJalurBantuan()" id="btn-jalur" class="btn-outline">
                        <i data-lucide="git-merge" style="width:16px;height:16px;"></i> Tampilkan Jalur Estimasi
                    </button>
                </div>

                <!-- Rekomendasi Prioritas -->
                <div class="sidebar-section" style="border-bottom:none;">
                    <div class="section-label">
                        <i data-lucide="star" style="width:14px;height:14px;"></i> Rekomendasi Prioritas Tinggi
                    </div>
                    <div id="list-prioritas">
                        <div style="font-size:12px;color:#94a3b8;text-align:center;padding:16px 0;">Memuat rekomendasi...</div>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- MAP AREA -->
    <main style="flex:1;position:relative;background:#e7e5e4;">
        <div id="map" style="width:100%;height:100%;"></div>

        <!-- Status Overlay -->
        <div id="map-status" class="map-status">
            <span style="position:relative;display:flex;width:10px;height:10px;">
                <span style="position:absolute;display:inline-flex;width:100%;height:100%;border-radius:50%;background:#5eead4;opacity:0.75;animation:ping 1s cubic-bezier(0,0,0.2,1) infinite;"></span>
                <span style="position:relative;display:inline-flex;width:10px;height:10px;border-radius:50%;background:#0d9488;"></span>
            </span>
            <span id="status-text">Memuat peta...</span>
        </div>

        <!-- Radius Info Overlay -->
        <div id="radius-info" class="radius-info-panel" style="display:none;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px;">
                <div class="ri-title"><i data-lucide="crosshair" style="width:16px;height:16px;color:#94a3b8;"></i> Hasil Radius</div>
                <button onclick="clearRadius()" style="background:none;border:none;cursor:pointer;color:#94a3b8;display:flex;align-items:center;" title="Tutup">
                    <i data-lucide="x" style="width:16px;height:16px;"></i>
                </button>
            </div>
            <div class="ri-count" id="ri-count">0</div>
            <div class="ri-desc">sekolah dalam radius <span id="ri-dist" style="color:#334155;font-weight:600;"></span>.</div>
            <div class="ri-divider"></div>
            <div class="ri-list-label">Daftar Sekolah</div>
            <div id="ri-list" style="max-height:160px;overflow-y:auto;">
                <!-- List -->
            </div>
        </div>
    </main>
</div>

<style>
@keyframes ping { 75%, 100% { transform: scale(2); opacity: 0; } }
</style>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    lucide.createIcons();

    // Configuration
    const stageColors = {
        'SD': '#f59e0b',
        'SMP': '#10b981',
        'SMA': '#3b82f6',
        'SMK': '#a855f7',
        'SLB': '#f43f5e'
    };
    
    const statusBantuanColors = {
        'Dibutuhkan': '#ef4444',
        'Proses': '#eab308',
        'Tersalurkan': '#22c55e'
    };

    const kondisiColors = {
        'Baik': '#22c55e',
        'Sedang': '#f59e0b',
        'Minim': '#ef4444'
    };

    let currentTab = 'sekolah';
    let map;
    let markerLayer = L.layerGroup();
    let jalurLayer = L.layerGroup();
    let radiusCircle = null;
    let selectedRadius = null;
    let isJalurVisible = false;

    // Initialize Map
    function initMap() {
        map = L.map('map', {
            center: [-5.1, 105.1],
            zoom: 8,
            zoomControl: false
        });
        
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        markerLayer.addTo(map);
        jalurLayer.addTo(map);
        
        loadStatistik();
        loadSekolah();
        loadPrioritasBantuan();
    }

    function setStatus(msg, isError = false) {
        const el = document.getElementById('map-status');
        const text = document.getElementById('status-text');
        text.textContent = msg;
        text.className = isError ? 'text-rose-500' : 'text-slate-700';
        
        const dot = el.querySelector('.bg-blue-500');
        if(dot) dot.className = `relative inline-flex rounded-full h-2.5 w-2.5 ${isError ? 'bg-rose-500' : 'bg-blue-500'}`;
        
        setTimeout(() => { if(text.textContent === msg) el.style.opacity = '0'; }, 3000);
        el.style.opacity = '1';
    }

    // Tabs
    function switchTab(tab) {
        currentTab = tab;
        document.getElementById('tab-sekolah').style.display = tab === 'sekolah' ? 'block' : 'none';
        document.getElementById('tab-bantuan').style.display = tab === 'bantuan' ? 'block' : 'none';
        
        document.getElementById('tab-btn-sekolah').className = 'tab-btn' + (tab === 'sekolah' ? ' active' : '');
        document.getElementById('tab-btn-bantuan').className = 'tab-btn' + (tab === 'bantuan' ? ' active' : '');

        if(tab === 'sekolah') applyFilterSekolah();
        else applyFilterBantuan();
        
        clearRadius();
    }

    // Load Data
    async function loadSekolah() {
        setStatus('Memuat data sekolah...');
        const stage = document.getElementById('filter-stage').value;
        const status = document.getElementById('filter-status').value;
        const search = document.getElementById('filter-search').value.trim();
        const kondisi = document.getElementById('filter-kondisi').value;
        
        let url = new URL('/api/sekolah', window.location.origin);
        if(stage !== 'all') url.searchParams.append('stage', stage);
        if(status !== 'all') url.searchParams.append('status', status);
        if(search) url.searchParams.append('search', search);
        if(kondisi !== 'all') url.searchParams.append('kondisi', kondisi);

        try {
            const res = await fetch(url);
            const data = await res.json();
            renderSekolahMarkers(data.features, kondisi !== 'all');
            document.getElementById('header-tampil').textContent = data.total.toLocaleString();
            setStatus('Selesai memuat data.');
        } catch(e) {
            setStatus('Gagal memuat data!', true);
        }
    }

    async function loadBantuan() {
        setStatus('Memuat data bantuan...');
        const jenis = document.getElementById('filter-jenis-bantuan').value;
        const status = document.getElementById('filter-status-bantuan').value;
        const prioritas = document.getElementById('filter-prioritas-bantuan').value;
        
        let url = new URL('/api/bantuan', window.location.origin);
        if(jenis !== 'all') url.searchParams.append('jenis_bantuan', jenis);
        if(status !== 'all') url.searchParams.append('status_bantuan', status);
        if(prioritas !== 'all') url.searchParams.append('tingkat_prioritas', prioritas);

        try {
            const res = await fetch(url);
            const data = await res.json();
            renderBantuanMarkers(data.features);
            document.getElementById('header-tampil').textContent = data.features.length.toLocaleString();
            setStatus('Selesai memuat data bantuan.');
        } catch(e) {
            setStatus('Gagal memuat data bantuan!', true);
        }
    }

    // Render Markers
    // useKondisiColor: true saat filter kondisi aktif
    function renderSekolahMarkers(features, useKondisiColor = false) {
        markerLayer.clearLayers();
        features.forEach(f => {
            const p = f.properties;
            const latlng = [f.geometry.coordinates[1], f.geometry.coordinates[0]];

            // Pilih warna: kondisi (saat filter aktif) atau jenjang (default)
            let color;
            if (useKondisiColor) {
                color = kondisiColors[p.kondisi_fasilitas] || '#a1a1aa';
            } else {
                color = stageColors[p.stage] || '#a1a1aa';
            }

            // Badge kondisi
            const kondisiLabel = p.kondisi_fasilitas || '-';
            const kondisiBg = { 'Baik': '#dcfce7', 'Sedang': '#fef9c3', 'Minim': '#fee2e2' }[kondisiLabel] || '#f4f4f5';
            const kondisiText = { 'Baik': '#166534', 'Sedang': '#713f12', 'Minim': '#991b1b' }[kondisiLabel] || '#52525b';
            const kondisiBorder = { 'Baik': '#86efac', 'Sedang': '#fde047', 'Minim': '#fca5a5' }[kondisiLabel] || '#e4e4e7';
            
            const icon = L.divIcon({
                className: '',
                html: `<div style="width:13px;height:13px;border-radius:50%;border:2px solid white;box-shadow:0 1px 4px rgba(0,0,0,0.3);background:${color}"></div>`,
                iconSize: [13, 13], iconAnchor: [6, 6]
            });

            const marker = L.marker(latlng, { icon }).addTo(markerLayer);
            marker.bindPopup(`
                <div style="min-width:190px;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:8px;">
                        <div style="font-weight:700;font-size:13px;color:#18181b;line-height:1.3;">${p.name}</div>
                        <span style="font-size:10px;padding:2px 7px;border-radius:4px;background:#f4f4f5;border:1px solid #e4e4e7;color:#52525b;white-space:nowrap;">${p.stage}</span>
                    </div>
                    <div style="font-size:11.5px;color:#71717a;margin-bottom:3px;display:flex;align-items:center;gap:5px;"><i data-lucide="tag" style="width:11px;height:11px;"></i> ${p.status}</div>
                    <div style="font-size:11.5px;color:#71717a;margin-bottom:8px;display:flex;align-items:center;gap:5px;"><i data-lucide="map-pin" style="width:11px;height:11px;"></i> ${p.district}, ${p.city}</div>
                    <div style="background:${kondisiBg};border:1px solid ${kondisiBorder};border-radius:6px;padding:6px 10px;display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                        <span style="font-size:11px;color:#52525b;">Kondisi Fasilitas</span>
                        <span style="font-size:11px;font-weight:700;color:${kondisiText};">${kondisiLabel}</span>
                    </div>
                    <div style="border-top:1px solid #f4f4f5;padding-top:8px;">
                        <button onclick="drawRadius(${latlng[0]}, ${latlng[1]}, '${p.name}')" style="width:100%;background:#f0fdf4;border:1px solid #86efac;color:#15803d;padding:7px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:5px;font-family:inherit;">
                            <i data-lucide="target" style="width:13px;height:13px;"></i> Analisis Radius
                        </button>
                    </div>
                </div>
            `);
            marker.on('popupopen', () => lucide.createIcons());
        });
    }

    function renderBantuanMarkers(features) {
        markerLayer.clearLayers();
        features.forEach(f => {
            const p = f.properties;
            const latlng = [f.geometry.coordinates[1], f.geometry.coordinates[0]];
            const color = statusBantuanColors[p.status_bantuan] || '#a1a1aa';
            
            const isHighPriority = p.tingkat_prioritas === 'Tinggi' && p.status_bantuan === 'Dibutuhkan';
            const pulseClass = isHighPriority ? 'marker-pulse' : '';
            
            const icon = L.divIcon({
                className: '',
                html: `<div class="relative w-4 h-4 rounded-full border-2 border-zinc-950 shadow-[0_0_10px_rgba(0,0,0,0.8)] ${pulseClass}" style="background:${color}"></div>`,
                iconSize: [16, 16], iconAnchor: [8, 8]
            });

            const marker = L.marker(latlng, { icon }).addTo(markerLayer);
            marker.bindPopup(`
                <div style="min-width:200px;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:8px;">
                        <div style="font-weight:700;font-size:13px;color:#0d9488;line-height:1.3;">${p.school_name}</div>
                        <span style="font-size:10px;padding:2px 7px;border-radius:4px;background:#f5f5f4;border:1px solid #e7e5e4;color:#57534e;white-space:nowrap;">${p.stage}</span>
                    </div>
                    <div style="background:#fafaf9;border:1px solid #e7e5e4;border-radius:8px;padding:8px 10px;font-size:12px;margin-top:6px;">
                        <div style="display:flex;justify-content:space-between;margin-bottom:4px;"><span style="color:#a8a29e;">Bantuan:</span> <span style="font-weight:500;color:#1c1917;">${p.jenis_bantuan}</span></div>
                        <div style="display:flex;justify-content:space-between;margin-bottom:4px;"><span style="color:#a8a29e;">Jumlah:</span> <span style="font-weight:500;color:#1c1917;">${p.jumlah}</span></div>
                        <div style="display:flex;justify-content:space-between;margin-bottom:4px;"><span style="color:#a8a29e;">Status:</span> <span style="font-weight:600;" style="color:${color}">${p.status_bantuan}</span></div>
                        <div style="display:flex;justify-content:space-between;"><span style="color:#a8a29e;">Prioritas:</span> <span style="font-weight:500;color:${p.tingkat_prioritas === 'Tinggi' ? '#dc2626' : '#1c1917'};">${p.tingkat_prioritas}</span></div>
                    </div>
                    <p style="font-size:10px;color:#a8a29e;font-style:italic;margin-top:6px;">${p.keterangan || '-'}</p>
                </div>
            `);
        });
    }

    // Radius Features
    function setRadius(r) {
        selectedRadius = r;
        document.querySelectorAll('.btn-radius').forEach(b => {
            if(parseInt(b.dataset.val) === r) {
                b.classList.add('active');
            } else {
                b.classList.remove('active');
            }
        });
        setStatus(`Mode Radius ${r}m aktif. Pilih sekolah di peta.`);
    }

    function clearRadius() {
        if(radiusCircle) map.removeLayer(radiusCircle);
        radiusCircle = null;
        document.getElementById('radius-info').style.display = 'none';
        selectedRadius = null;
        document.querySelectorAll('.btn-radius').forEach(b => b.classList.remove('active'));
    }

    async function drawRadius(lat, lng, schoolName) {
        if(!selectedRadius) {
            alert('Pilih jarak radius terlebih dahulu di panel kiri.');
            return;
        }
        
        map.closePopup();
        if(radiusCircle) map.removeLayer(radiusCircle);
        
        radiusCircle = L.circle([lat, lng], {
            radius: selectedRadius,
            color: '#0d9488',
            fillColor: '#0d9488',
            fillOpacity: 0.12,
            weight: 2,
            dashArray: '6,6'
        }).addTo(map);
        
        map.flyToBounds(radiusCircle.getBounds(), { padding: [50, 50], duration: 1 });
        
        setStatus('Menghitung radius...');
        
        try {
            const res = await fetch(`/api/sekolah/radius?lat=${lat}&lng=${lng}&radius=${selectedRadius}`);
            const data = await res.json();
            
            document.getElementById('ri-count').textContent = data.count;
            document.getElementById('ri-dist').textContent = selectedRadius >= 1000 ? (selectedRadius/1000)+'km' : selectedRadius+'m';
            
            const listHtml = data.sekolah.map(s => `
                <div style="background:#fafaf9;padding:10px;border-radius:8px;border:1px solid #e7e5e4;display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                    <div>
                        <div style="font-weight:500;color:#1c1917;font-size:12px;">${s.school_name}</div>
                        <div style="font-size:10px;color:#78716c;">${s.stage} • ${s.district_name}</div>
                    </div>
                    <div style="color:#0d9488;font-weight:700;font-size:12px;">${Math.round(s.jarak_meter)}m</div>
                </div>
            `).join('');
            
            document.getElementById('ri-list').innerHTML = listHtml || '<div style="color:#a8a29e;font-style:italic;padding:8px 0;">Tidak ada sekolah lain dalam radius ini.</div>';
            document.getElementById('radius-info').style.display = 'block';
            setStatus('Selesai menghitung radius.');
        } catch(e) {
            setStatus('Gagal mengambil data radius.', true);
        }
    }

    // Bantuan Prioritas & Jalur
    async function loadPrioritasBantuan() {
        try {
            const res = await fetch('/api/prioritas-bantuan');
            const data = await res.json();
            
            if(data.length === 0) {
                document.getElementById('list-prioritas').innerHTML = '<div style="font-size:12px;color:#a8a29e;text-align:center;padding:16px;border:1px dashed #d6d3d1;border-radius:10px;">Tidak ada data prioritas saat ini.</div>';
                return;
            }
            
            const html = data.map(p => `
                <div onclick="focusToMarker(${p.lat}, ${p.long})" style="background:#fff;border:1px solid #e7e5e4;border-radius:10px;padding:12px 14px;cursor:pointer;transition:all 0.2s;margin-bottom:8px;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px;">
                        <div style="font-weight:600;font-size:13px;color:#1c1917;">${p.school_name}</div>
                        <span style="font-size:9px;background:#fef2f2;color:#dc2626;padding:2px 7px;border-radius:4px;border:1px solid #fecaca;text-transform:uppercase;font-weight:600;">Prioritas</span>
                    </div>
                    <div style="font-size:10px;color:#78716c;display:flex;align-items:center;gap:4px;margin-bottom:8px;"><i data-lucide="map-pin" style="width:12px;height:12px;"></i> ${p.district_name}, ${p.city_name}</div>
                    <div style="background:#fafaf9;border-radius:8px;padding:6px 10px;font-size:12px;color:#57534e;border:1px solid #f5f5f4;">
                        <span style="color:#a8a29e;">Butuh:</span> ${p.jumlah} ${p.jenis_bantuan}
                    </div>
                </div>
            `).join('');
            
            document.getElementById('list-prioritas').innerHTML = html;
            lucide.createIcons();
        } catch(e) {
            document.getElementById('list-prioritas').innerHTML = '<div style="font-size:12px;color:#dc2626;">Gagal memuat prioritas.</div>';
        }
    }

    function focusToMarker(lat, lng) {
        map.flyTo([lat, lng], 15, { duration: 1.5 });
    }

    async function toggleJalurBantuan() {
        const btn = document.getElementById('btn-jalur');
        
        if(isJalurVisible) {
            jalurLayer.clearLayers();
            isJalurVisible = false;
            btn.innerHTML = '<i data-lucide="git-merge" style="width:16px;height:16px;"></i> Tampilkan Jalur Estimasi';
            btn.className = 'btn-outline';
            lucide.createIcons();
            return;
        }

        await renderJalur();
    }

    async function renderJalur() {
        const btn = document.getElementById('btn-jalur');
        setStatus('Memuat jalur distribusi...');
        try {
            const res = await fetch('/api/jalur-bantuan');
            const allData = await res.json();

            // Filter client-side — hanya terapkan jika kolom ada di data view
            const jenis = document.getElementById('filter-jenis-bantuan').value;
            const status = document.getElementById('filter-status-bantuan').value;
            const prioritas = document.getElementById('filter-prioritas-bantuan').value;

            // Cek kolom apa saja yang tersedia di view (dari baris pertama data)
            const sample = allData[0] || {};
            const hasJenis = 'jenis_bantuan' in sample;
            const hasStatus = 'status_bantuan' in sample;
            const hasPrioritas = 'tingkat_prioritas' in sample;

            const data = allData.filter(item => {
                if (jenis !== 'all' && hasJenis && item.jenis_bantuan !== jenis) return false;
                if (status !== 'all' && hasStatus && item.status_bantuan !== status) return false;
                if (prioritas !== 'all' && hasPrioritas && item.tingkat_prioritas !== prioritas) return false;
                return true;
            });

            jalurLayer.clearLayers();
            
            data.forEach(item => {
                if(!item.geojson_jalur) return;
                
                const geojson = JSON.parse(item.geojson_jalur);
                L.geoJSON(geojson, {
                    style: {
                        color: '#2d6a4f',
                        weight: 3,
                        opacity: 0.7,
                        dashArray: '10, 10',
                        className: 'animate-dash'
                    }
                }).bindPopup(`
                    <div style="font-size:13px;">
                        <div style="font-weight:700;color:#2d6a4f;margin-bottom:6px;">Jalur Distribusi Bantuan</div>
                        <div style="font-size:12px;color:#71717a;margin-bottom:3px;">Dari: <span style="color:#18181b;font-weight:500;">${item.nama_pos}</span></div>
                        <div style="font-size:12px;color:#71717a;margin-bottom:8px;">Ke: <span style="color:#18181b;font-weight:500;">${item.school_name}</span></div>
                        <div style="font-size:10px;background:#fafafa;padding:6px 8px;border-radius:6px;border:1px solid #e4e4e7;color:#52525b;">${item.jenis_bantuan}</div>
                    </div>
                `).addTo(jalurLayer);
            });
            
            isJalurVisible = true;
            btn.innerHTML = '<i data-lucide="eye-off" style="width:16px;height:16px;"></i> Sembunyikan Jalur';
            btn.className = 'btn-outline btn-blue';
            lucide.createIcons();
            setStatus(`Menampilkan ${data.length} jalur distribusi.`);
            
            if(jalurLayer.getLayers().length > 0) {
                const group = new L.featureGroup(jalurLayer.getLayers());
                map.flyToBounds(group.getBounds(), { padding: [50, 50] });
            }
            
        } catch(e) {
            setStatus('Gagal memuat jalur!', true);
        }
    }

    function applyFilterBantuan() {
        // Jika jalur sedang ditampilkan, reload jalur sesuai filter baru
        if (isJalurVisible) {
            renderJalur();
        }
        loadBantuan();
    }
            

    // Actions
    function applyFilterSekolah() { loadSekolah(); }
    function resetFilterSekolah() {
        document.getElementById('filter-stage').value = 'all';
        document.getElementById('filter-status').value = 'all';
        document.getElementById('filter-search').value = '';
        document.getElementById('filter-kondisi').value = 'all';
        loadSekolah();
    }
    
    function applyFilterBantuan() {
        // Sembunyikan jalur distribusi saat filter berubah
        if (isJalurVisible) {
            jalurLayer.clearLayers();
            isJalurVisible = false;
            const btn = document.getElementById('btn-jalur');
            btn.innerHTML = '<i data-lucide="git-merge" style="width:16px;height:16px;"></i> Tampilkan Jalur Estimasi';
            btn.className = 'btn-outline';
            lucide.createIcons();
        }
        loadBantuan();
    }

    document.getElementById('filter-search').addEventListener('keydown', e => {
        if(e.key === 'Enter') applyFilterSekolah();
    });

    // Statistik
    async function loadStatistik() {
        try {
            const res = await fetch('/api/sekolah/statistik');
            const data = await res.json();
            
            document.getElementById('header-total').textContent = data.total.toLocaleString();
            
            // Statistik per jenjang
            const htmlJenjang = Object.entries(data.per_jenjang).map(([j,n]) => {
                const pct = ((n/data.total)*100).toFixed(1);
                const color = stageColors[j] || '#a1a1aa';
                return `
                    <div style="margin-bottom:10px;">
                        <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px;">
                            <span style="font-weight:500;color:#3f3f46;">${j}</span>
                            <span style="color:#71717a;">${n.toLocaleString()} <span style="font-size:10px;opacity:0.7;">(${pct}%)</span></span>
                        </div>
                        <div style="height:5px;width:100%;background:#f4f4f5;border-radius:99px;overflow:hidden;">
                            <div style="height:100%;border-radius:99px;width:${pct}%;background:${color};transition:width 0.8s ease;"></div>
                        </div>
                    </div>
                `;
            }).join('');
            document.getElementById('stat-jenjang').innerHTML = htmlJenjang;

            // Statistik kondisi fasilitas
            const kondisiOrder = ['Baik', 'Sedang', 'Minim'];
            const totalKondisi = Object.values(data.per_kondisi || {}).reduce((a,b) => a+b, 0);
            const htmlKondisi = kondisiOrder.map(k => {
                const n = (data.per_kondisi || {})[k] || 0;
                const pct = totalKondisi > 0 ? ((n/totalKondisi)*100).toFixed(1) : '0.0';
                const color = kondisiColors[k] || '#a1a1aa';
                return `
                    <div style="margin-bottom:10px;">
                        <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px;">
                            <span style="font-weight:500;color:#3f3f46;">${k}</span>
                            <span style="color:#71717a;">${n.toLocaleString()} <span style="font-size:10px;opacity:0.7;">(${pct}%)</span></span>
                        </div>
                        <div style="height:5px;width:100%;background:#f4f4f5;border-radius:99px;overflow:hidden;">
                            <div style="height:100%;border-radius:99px;width:${pct}%;background:${color};transition:width 0.8s ease;"></div>
                        </div>
                    </div>
                `;
            }).join('');
            document.getElementById('stat-kondisi').innerHTML = htmlKondisi || '<div style="font-size:12px;color:#a1a1aa;">Belum ada data kondisi.</div>';

        } catch(e) {
            console.error(e);
        }
    }

    // Custom CSS for dash animation
    const style = document.createElement('style');
    style.innerHTML = `
        .animate-dash { animation: dash 20s linear infinite; }
        @keyframes dash { to { stroke-dashoffset: -1000; } }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    `;
    document.head.appendChild(style);

    // Start
    window.onload = initMap;
</script>
</body>
</html>
