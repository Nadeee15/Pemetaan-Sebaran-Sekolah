<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SIG Sekolah Lampung - Pemetaan Sebaran Sekolah</title>
<meta name="description" content="Sistem Informasi Geografis pemetaan sebaran sekolah dan analisis aksesibilitas pendidikan di Provinsi Lampung">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{
  --primary:#1a56db;--primary-dark:#1e40af;--accent:#06b6d4;
  --bg:#0f172a;--surface:#1e293b;--surface2:#334155;
  --text:#f1f5f9;--text-muted:#94a3b8;--border:#334155;
  --sd:#f59e0b;--smp:#10b981;--sma:#3b82f6;--smk:#8b5cf6;--slb:#ef4444;
}
body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text);height:100vh;overflow:hidden;display:flex;flex-direction:column}

/* HEADER */
.header{background:linear-gradient(135deg,#1e293b 0%,#0f172a 100%);border-bottom:1px solid var(--border);padding:10px 20px;display:flex;align-items:center;gap:16px;z-index:1000;flex-shrink:0}
.header-logo{width:40px;height:40px;background:linear-gradient(135deg,var(--primary),var(--accent));border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.header-title h1{font-size:16px;font-weight:700;color:var(--text)}
.header-title p{font-size:11px;color:var(--text-muted)}
.header-stats{margin-left:auto;display:flex;gap:12px}
.hstat{text-align:center;background:var(--surface);padding:6px 14px;border-radius:8px;border:1px solid var(--border)}
.hstat-num{font-size:16px;font-weight:700;color:var(--accent)}
.hstat-lbl{font-size:10px;color:var(--text-muted)}

/* MAIN LAYOUT */
.main{display:flex;flex:1;overflow:hidden}

/* SIDEBAR */
.sidebar{width:320px;background:var(--surface);border-right:1px solid var(--border);display:flex;flex-direction:column;overflow:hidden;flex-shrink:0}
.sidebar-header{padding:14px 16px;border-bottom:1px solid var(--border);font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em}

/* FILTER PANEL */
.filter-group{padding:14px 16px;border-bottom:1px solid var(--border)}
.filter-label{font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;margin-bottom:8px}
.search-box{position:relative;margin-bottom:10px}
.search-box input{width:100%;background:var(--surface2);border:1px solid var(--border);color:var(--text);padding:8px 10px 8px 34px;border-radius:8px;font-size:13px;font-family:'Inter',sans-serif;outline:none;transition:.2s}
.search-box input:focus{border-color:var(--primary);box-shadow:0 0 0 2px rgba(26,86,219,.2)}
.search-box i{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:12px}
.select-custom{width:100%;background:var(--surface2);border:1px solid var(--border);color:var(--text);padding:8px 10px;border-radius:8px;font-size:13px;font-family:'Inter',sans-serif;outline:none;cursor:pointer;margin-bottom:8px}
.btn-filter{width:100%;padding:9px;background:linear-gradient(135deg,var(--primary),var(--accent));border:none;border-radius:8px;color:#fff;font-size:13px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;transition:.2s}
.btn-filter:hover{opacity:.9;transform:translateY(-1px)}
.btn-reset{width:100%;padding:8px;background:transparent;border:1px solid var(--border);border-radius:8px;color:var(--text-muted);font-size:12px;cursor:pointer;font-family:'Inter',sans-serif;transition:.2s;margin-top:6px}
.btn-reset:hover{border-color:var(--text-muted);color:var(--text)}

/* RADIUS TOOL */
.radius-group{padding:14px 16px;border-bottom:1px solid var(--border)}
.radius-btns{display:flex;gap:6px;flex-wrap:wrap}
.btn-radius{flex:1;min-width:60px;padding:7px 4px;background:var(--surface2);border:1px solid var(--border);border-radius:7px;color:var(--text-muted);font-size:12px;cursor:pointer;font-family:'Inter',sans-serif;text-align:center;transition:.2s}
.btn-radius:hover,.btn-radius.active{background:var(--primary);border-color:var(--primary);color:#fff}
.btn-radius-clear{width:100%;padding:7px;background:transparent;border:1px solid var(--border);border-radius:7px;color:var(--text-muted);font-size:12px;cursor:pointer;font-family:'Inter',sans-serif;margin-top:6px;transition:.2s}
.btn-radius-clear:hover{border-color:#ef4444;color:#ef4444}
.radius-hint{font-size:11px;color:var(--text-muted);margin-top:8px;padding:7px;background:rgba(6,182,212,.08);border-radius:6px;border-left:2px solid var(--accent)}

/* LEGENDA */
.legenda-group{padding:14px 16px;border-bottom:1px solid var(--border)}
.legenda-item{display:flex;align-items:center;gap:8px;margin-bottom:6px;font-size:12px}
.legenda-dot{width:12px;height:12px;border-radius:50%;border:2px solid rgba(255,255,255,.3);flex-shrink:0}

/* STATISTIK */
.stat-group{padding:14px 16px;flex:1;overflow-y:auto}
.stat-item{display:flex;justify-content:space-between;align-items:center;padding:7px 10px;background:var(--surface2);border-radius:7px;margin-bottom:6px;font-size:12px}
.stat-bar{height:4px;background:var(--border);border-radius:2px;margin-top:4px;overflow:hidden}
.stat-bar-fill{height:100%;border-radius:2px;transition:width .6s ease}
.result-count{padding:10px 16px;background:rgba(6,182,212,.08);border-top:1px solid var(--border);font-size:12px;color:var(--accent);font-weight:500}

/* MAP */
#map{flex:1;position:relative}

/* POPUP */
.leaflet-popup-content-wrapper{background:var(--surface);border:1px solid var(--border);border-radius:12px;box-shadow:0 20px 40px rgba(0,0,0,.5)}
.leaflet-popup-tip{background:var(--surface)}
.leaflet-popup-content{margin:0!important}
.popup-card{padding:14px}
.popup-badge{display:inline-block;padding:3px 8px;border-radius:20px;font-size:10px;font-weight:700;text-transform:uppercase;margin-bottom:8px}
.popup-name{font-size:14px;font-weight:700;color:var(--text);margin-bottom:8px;line-height:1.4}
.popup-info{font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:6px;margin-bottom:4px}
.popup-info i{width:14px;color:var(--accent)}
.popup-actions{display:flex;gap:6px;margin-top:10px}
.popup-btn{flex:1;padding:6px;border:none;border-radius:6px;font-size:11px;cursor:pointer;font-family:'Inter',sans-serif;font-weight:600}
.popup-btn-radius{background:linear-gradient(135deg,var(--primary),var(--accent));color:#fff}
.popup-btn-close{background:var(--surface2);color:var(--text-muted);border:1px solid var(--border);}

/* RADIUS INFO */
.radius-info{position:absolute;bottom:30px;right:10px;background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:12px;z-index:1000;max-width:220px;font-size:12px}
.radius-info h4{color:var(--accent);font-size:13px;margin-bottom:6px}
.radius-info p{color:var(--text-muted);margin-bottom:4px}
.radius-close{cursor:pointer;color:var(--text-muted);float:right;font-size:14px}

/* TOAST */
.toast{position:fixed;bottom:20px;left:50%;transform:translateX(-50%);background:var(--surface);border:1px solid var(--accent);border-radius:8px;padding:10px 16px;font-size:13px;color:var(--text);z-index:9999;opacity:0;transition:.3s;pointer-events:none}
.toast.show{opacity:1}

/* SCROLLBAR */
::-webkit-scrollbar{width:5px}
::-webkit-scrollbar-track{background:var(--surface)}
::-webkit-scrollbar-thumb{background:var(--surface2);border-radius:3px}
</style>
</head>
<body>

<!-- HEADER -->
<header class="header">
  <div class="header-logo"><i class="fas fa-map-marked-alt" style="color:#fff"></i></div>
  <div class="header-title">
    <h1>SIG Sekolah Lampung</h1>
    <p>Sistem Informasi Geografis · Pemetaan Sebaran Sekolah</p>
  </div>
  <div class="header-stats">
    <div class="hstat"><div class="hstat-num" id="stat-total">-</div><div class="hstat-lbl">Total Sekolah</div></div>
    <div class="hstat"><div class="hstat-num" id="stat-tampil">-</div><div class="hstat-lbl">Ditampilkan</div></div>
    <div class="hstat" style="display:none" id="radius-stat"><div class="hstat-num" id="stat-radius">-</div><div class="hstat-lbl">Dalam Radius</div></div>
  </div>
</header>

<!-- MAIN -->
<div class="main">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="sidebar-header"><i class="fas fa-sliders-h"></i> &nbsp;Filter & Alat</div>

    <!-- FILTER -->
    <div class="filter-group">
      <div class="filter-label"><i class="fas fa-search"></i> Cari Sekolah</div>
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="input-search" placeholder="Nama sekolah..." autocomplete="off">
      </div>
      <div class="filter-label" style="margin-top:10px"><i class="fas fa-filter"></i> Filter</div>
      <select class="select-custom" id="filter-stage">
        <option value="all">Semua Jenjang</option>
        <option value="SD">SD</option>
        <option value="SMP">SMP</option>
        <option value="SMA">SMA</option>
        <option value="SMK">SMK</option>
        <option value="SLB">SLB</option>
      </select>
      <select class="select-custom" id="filter-status">
        <option value="all">Semua Status</option>
        <option value="Negeri">Negeri</option>
        <option value="Swasta">Swasta</option>
      </select>
      <button class="btn-filter" id="btn-apply-filter" onclick="applyFilter()">
        <i class="fas fa-search"></i> Terapkan Filter
      </button>
      <button class="btn-reset" onclick="resetFilter()"><i class="fas fa-undo"></i> Reset</button>
    </div>

    <!-- RADIUS -->
    <div class="radius-group">
      <div class="filter-label"><i class="fas fa-circle-notch"></i> Radius Layanan</div>
      <div class="radius-btns">
        <button class="btn-radius" onclick="setRadius(500)">500m</button>
        <button class="btn-radius" onclick="setRadius(1000)">1 km</button>
        <button class="btn-radius" onclick="setRadius(3000)">3 km</button>
        <button class="btn-radius" onclick="setRadius(5000)">5 km</button>
      </div>
      <button class="btn-radius-clear" onclick="clearRadius()"><i class="fas fa-times"></i> Hapus Radius</button>
      <div class="radius-hint"><i class="fas fa-info-circle"></i> Klik marker sekolah → pilih tampilkan radius, atau klik langsung di peta setelah memilih jarak.</div>
    </div>

    <!-- LEGENDA -->
    <div class="legenda-group">
      <div class="filter-label"><i class="fas fa-map-pin"></i> Legenda Jenjang</div>
      <div class="legenda-item"><div class="legenda-dot" style="background:#f59e0b"></div><span>SD (Sekolah Dasar)</span></div>
      <div class="legenda-item"><div class="legenda-dot" style="background:#10b981"></div><span>SMP</span></div>
      <div class="legenda-item"><div class="legenda-dot" style="background:#3b82f6"></div><span>SMA</span></div>
      <div class="legenda-item"><div class="legenda-dot" style="background:#8b5cf6"></div><span>SMK</span></div>
      <div class="legenda-item"><div class="legenda-dot" style="background:#ef4444"></div><span>SLB</span></div>
    </div>

    <!-- STATISTIK -->
    <div class="stat-group">
      <div class="filter-label"><i class="fas fa-chart-bar"></i> Statistik</div>
      <div id="stat-jenjang"></div>
      <div style="margin-top:12px" class="filter-label"><i class="fas fa-building"></i> Per Kota/Kabupaten (Top 5)</div>
      <div id="stat-kota"></div>
    </div>

    <div class="result-count" id="result-count"><i class="fas fa-map-marker-alt"></i> Memuat data...</div>
  </aside>

  <!-- MAP -->
  <div id="map"></div>
</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ===== WARNA MARKER =====
const stageColors = {
  'SD':  {color:'#f59e0b',icon:'🏫'},
  'SMP': {color:'#10b981',icon:'🏫'},
  'SMA': {color:'#3b82f6',icon:'🏫'},
  'SMK': {color:'#8b5cf6',icon:'🎓'},
  'SLB': {color:'#ef4444',icon:'♿'},
};

function getColor(stage){ return (stageColors[stage]||{color:'#94a3b8'}).color; }

function createMarkerIcon(stage, isHighlight=false) {
  const c = getColor(stage);
  const s = isHighlight ? 14 : 10;
  return L.divIcon({
    className:'',
    html:`<div style="
      width:${s}px;height:${s}px;border-radius:50%;
      background:${c};border:2px solid rgba(255,255,255,0.8);
      box-shadow:0 2px 6px rgba(0,0,0,0.4);
      ${isHighlight?'transform:scale(1.4);':''}
    "></div>`,
    iconSize:[s,s],iconAnchor:[s/2,s/2],popupAnchor:[0,-s/2]
  });
}

// ===== INISIALISASI PETA =====
const map = L.map('map',{
  center:[-5.4,105.25],
  zoom:8,
  zoomControl:true
});

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{
  attribution:'© OpenStreetMap contributors',
  maxZoom:19
}).addTo(map);

// ===== STATE =====
let allMarkers = [];
let markerLayer = L.layerGroup().addTo(map);
let radiusCircle = null;
let radiusMarker = null;
let selectedRadius = null;
let radiusClickMode = false;
let totalAll = 0;

// ===== LOAD DATA =====
async function loadData(params={}) {
  try {
    const url = new URL('/api/sekolah/geojson', window.location.origin);
    Object.entries(params).forEach(([k,v])=>{ if(v&&v!=='all') url.searchParams.set(k,v); });

    const res = await fetch(url);
    const data = await res.json();

    renderMarkers(data.features);
    document.getElementById('stat-tampil').textContent = data.total.toLocaleString();
    document.getElementById('result-count').innerHTML =
      `<i class="fas fa-map-marker-alt"></i> Menampilkan <strong>${data.total.toLocaleString()}</strong> sekolah`;
  } catch(e) {
    showToast('Gagal memuat data sekolah. Pastikan database terhubung.', 'error');
  }
}

function renderMarkers(features) {
  markerLayer.clearLayers();
  allMarkers = [];

  features.forEach(f => {
    const p = f.properties;
    const [lng,lat] = f.geometry.coordinates;
    const marker = L.marker([lat,lng], { icon: createMarkerIcon(p.stage) });

    marker.bindPopup(buildPopup(p), {maxWidth:260});
    marker.on('click', function(){ map.setView([lat,lng],14); });
    markerLayer.addLayer(marker);
    allMarkers.push({marker, props:p});
  });
}

function buildPopup(p) {
  const c = getColor(p.stage);
  return `<div class="popup-card">
    <span class="popup-badge" style="background:${c}22;color:${c};border:1px solid ${c}55">${p.stage}</span>
    <div class="popup-name">${p.name}</div>
    <div class="popup-info"><i class="fas fa-tag"></i>${p.status||'-'}</div>
    <div class="popup-info"><i class="fas fa-map-marker-alt"></i>${p.district||'-'}</div>
    <div class="popup-info"><i class="fas fa-city"></i>${p.city||'-'}</div>
    <div class="popup-info"><i class="fas fa-location-arrow"></i>${p.lat?.toFixed(5)}, ${p.lng?.toFixed(5)}</div>
    <div class="popup-actions">
      <button class="popup-btn popup-btn-radius" onclick="showRadiusFromPopup(${p.lat},${p.lng})">
        <i class="fas fa-circle-notch"></i> Radius
      </button>
      <button class="popup-btn popup-btn-close" onclick="map.closePopup()">Tutup</button>
    </div>
  </div>`;
}

// ===== FILTER =====
function applyFilter() {
  const stage = document.getElementById('filter-stage').value;
  const status = document.getElementById('filter-status').value;
  const search = document.getElementById('input-search').value.trim();
  loadData({stage,status,search});
}

function resetFilter() {
  document.getElementById('filter-stage').value = 'all';
  document.getElementById('filter-status').value = 'all';
  document.getElementById('input-search').value = '';
  loadData();
}

// Enter key search
document.getElementById('input-search').addEventListener('keydown', e => {
  if (e.key === 'Enter') applyFilter();
});

// ===== RADIUS =====
function setRadius(r) {
  selectedRadius = r;
  document.querySelectorAll('.btn-radius').forEach(b => b.classList.remove('active'));
  event.target.classList.add('active');
  showToast(`Mode radius ${r>=1000?r/1000+' km':r+' m'} aktif. Klik marker sekolah untuk tampilkan radius.`);
}

function showRadiusFromPopup(lat, lng) {
  if (!selectedRadius) {
    showToast('Pilih radius terlebih dahulu di panel kiri!');
    return;
  }
  map.closePopup();
  drawRadius(lat, lng, selectedRadius);
}

function drawRadius(lat, lng, r) {
  clearRadius();
  radiusCircle = L.circle([lat, lng], {
    radius: r,
    color: '#06b6d4',
    fillColor: '#06b6d4',
    fillOpacity: 0.1,
    weight: 2,
    dashArray: '6,4'
  }).addTo(map);

  map.fitBounds(radiusCircle.getBounds());
  fetchRadiusData(lat, lng, r);
}

async function fetchRadiusData(lat, lng, r) {
  try {
    const res = await fetch(`/api/sekolah/radius?lat=${lat}&lng=${lng}&radius=${r}`);
    const data = await res.json();
    document.getElementById('radius-stat').style.display = 'block';
    document.getElementById('stat-radius').textContent = data.count;
    showToast(`${data.count} sekolah dalam radius ${r>=1000?r/1000+' km':r+' m'}`);
  } catch(e) {}
}

function clearRadius() {
  if (radiusCircle) { map.removeLayer(radiusCircle); radiusCircle = null; }
  document.getElementById('radius-stat').style.display = 'none';
  document.querySelectorAll('.btn-radius').forEach(b => b.classList.remove('active'));
  selectedRadius = null;
}

// ===== STATISTIK =====
async function loadStatistik() {
  try {
    const res = await fetch('/api/sekolah/statistik');
    const data = await res.json();
    totalAll = data.total;
    document.getElementById('stat-total').textContent = data.total.toLocaleString();

    // Per jenjang
    const colors = {SD:'#f59e0b',SMP:'#10b981',SMA:'#3b82f6',SMK:'#8b5cf6',SLB:'#ef4444'};
    const jenjangHtml = Object.entries(data.per_jenjang).map(([j,n]) => `
      <div class="stat-item">
        <span style="color:${colors[j]||'#94a3b8'};font-weight:600">${j}</span>
        <span style="font-weight:700">${n.toLocaleString()}</span>
      </div>
      <div class="stat-bar"><div class="stat-bar-fill" style="width:${(n/data.total*100).toFixed(1)}%;background:${colors[j]||'#94a3b8'}"></div></div>
    `).join('');
    document.getElementById('stat-jenjang').innerHTML = jenjangHtml;

    // Per kota top 5
    const kotaEntries = Object.entries(data.per_kota).slice(0,5);
    const maxKota = kotaEntries[0]?.[1] || 1;
    const kotaHtml = kotaEntries.map(([k,n]) => `
      <div class="stat-item" style="flex-direction:column;align-items:stretch">
        <div style="display:flex;justify-content:space-between">
          <span style="font-size:11px">${k}</span>
          <span style="font-weight:700;color:var(--accent)">${n}</span>
        </div>
        <div class="stat-bar" style="margin-top:6px"><div class="stat-bar-fill" style="width:${(n/maxKota*100).toFixed(1)}%;background:var(--accent)"></div></div>
      </div>
    `).join('');
    document.getElementById('stat-kota').innerHTML = kotaHtml;
  } catch(e) {}
}

// ===== TOAST =====
function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(()=>t.classList.remove('show'), 3000);
}

// ===== INIT =====
loadData();
loadStatistik();
</script>
</body>
</html>
