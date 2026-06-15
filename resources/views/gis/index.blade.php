@extends('layouts.app')
@section('title', 'GIS Map')

@section('breadcrumb')
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-current">GIS Map</span>
@endsection

@section('topbar-actions')
    <div style="display:flex;align-items:center;gap:8px">
        <div style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--text2)">
            <div style="width:10px;height:10px;border-radius:50%;background:#16a34a"></div> BSU
        </div>
        <div style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--text2)">
            <div style="width:10px;height:10px;border-radius:50%;background:#d97706"></div> Agregator
        </div>
        <div style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--text2)">
            <div style="width:10px;height:10px;border-radius:50%;background:#7c3aed"></div> Recycler
        </div>
    </div>
@endsection

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
    .page-content { padding: 0 !important; overflow: hidden; position: relative; }
    #map { height: calc(100vh - 56px); width: 100%; }

    /* Sidebar panel */
    .map-sidebar { position: absolute; top: 10px; left: 10px; z-index: 1000; background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,.1); width: 220px; overflow: hidden; }
    .map-sidebar-head { padding: 12px 16px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-size: 12px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: .08em; }
    .map-stat-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 16px; border-bottom: 1px solid #f1f5f9; }
    .map-stat-row:last-child { border-bottom: none; }
    .map-stat-label { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #475569; font-weight: 600; }
    .map-stat-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .map-stat-val { font-size: 15px; font-weight: 800; color: #0f172a; }

    /* Right Panel Layout */
    .right-panel {
        position: absolute; top: 0; right: 0; bottom: 0; width: 450px; background: #fff; z-index: 2000;
        box-shadow: -10px 0 40px rgba(0,0,0,0.15); transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1); transform: translateX(110%);
        display: flex; flex-direction: column;
    }
    .right-panel.open { transform: translateX(0); }
    .rp-header { padding: 24px 30px; color: #fff; position: relative; flex-shrink: 0; }
    .rp-close { position: absolute; top: 20px; right: 20px; cursor: pointer; background: rgba(255,255,255,0.3); width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 18px; transition: background 0.2s; }
    .rp-close:hover { background: rgba(255,255,255,0.5); }
    .rp-type { display: flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 6px; opacity: 0.9; }
    .rp-name { font-size: 26px; font-weight: 800; line-height: 1.2; margin-bottom: 4px; }
    .rp-addr { font-size: 13px; opacity: 0.9; display: flex; align-items: center; gap: 4px; }
    .rp-body { padding: 24px 30px; overflow-y: auto; flex: 1; }
    
    .rp-section { font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 12px; margin-top: 24px; display: flex; align-items: center; gap: 8px; }
    .rp-section:first-child { margin-top: 0; }
    
    /* Filters */
    .filter-row { display: flex; align-items: center; gap: 6px; overflow-x: auto; padding-bottom: 8px; margin-bottom: 8px; }
    .filter-row::-webkit-scrollbar { display: none; }
    .filter-btn { padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #f1f5f9; color: #64748b; cursor: pointer; white-space: nowrap; border: 1px solid transparent; }
    .filter-btn:hover { background: #e2e8f0; }
    .filter-btn.active { background: #ea580c; color: #fff; }
    .filter-btn.active-green { background: #16a34a; color: #fff; }
    .filter-btn-nav { background: #ea580c; color: #fff; border-radius: 8px; padding: 6px 10px; display: flex; align-items: center; justify-content: center; cursor: pointer; flex-shrink: 0; }
    .filter-btn-nav.green { background: #16a34a; }

    /* Material Cards */
    .rp-mats { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 24px; }
    .rp-mat-card { flex: 1; min-width: 100px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 12px; text-align: center; }
    .rp-mat-name { font-size: 12px; font-weight: 800; color: #475569; text-transform: uppercase; }
    .rp-mat-val { font-size: 20px; font-weight: 800; color: #ea580c; margin-top: 6px; }
    .rp-mat-val span { font-size: 12px; font-weight: 600; color: #94a3b8; }
    .rp-mat-card.green .rp-mat-val { color: #16a34a; }

    /* Traceability History */
    .hist-card { border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
    .hist-head { display: flex; justify-content: space-between; align-items: flex-start; padding: 16px 20px; background: #fff; border-bottom: 1px solid #e2e8f0; }
    .hist-date { font-size: 15px; font-weight: 800; color: #0f172a; }
    .hist-type { font-size: 13px; color: #64748b; margin-top: 4px; }
    .hist-total-val { font-size: 16px; font-weight: 800; color: #ea580c; }
    
    table.hist-table { width: 100%; border-collapse: collapse; background: #fff; }
    table.hist-table th { padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 700; color: #16a34a; background: #f0fdf4; border-bottom: 1px solid #e2e8f0; }
    table.hist-table td { padding: 12px 20px; font-size: 13px; font-weight: 600; color: #475569; border-bottom: 1px solid #f1f5f9; }
    table.hist-table tr:last-child td { border-bottom: none; }
    
    .hist-foot { display: flex; padding: 16px 20px; background: #fff; gap: 12px; border-top: 1px solid #e2e8f0; }
    .hist-foot-box { flex: 1; background: #f1f5f9; border-radius: 8px; padding: 12px; text-align: center; }
    .hist-foot-box.green { background: #dcfce7; }
    .hist-foot-label { font-size: 11px; font-weight: 700; color: #64748b; }
    .hist-foot-val { font-size: 15px; font-weight: 800; color: #0f172a; margin-top: 4px; }
    .hist-foot-box.green .hist-foot-label { color: #16a34a; }
    .hist-foot-box.green .hist-foot-val { color: #15803d; }
</style>
@endpush

@section('content')
<div id="map"></div>
<div id="right-panel" class="right-panel closed"></div>
@endsection

@push('scripts')
<script>
const map = L.map('map', { center: [-7.5, 112.7], zoom: 9, zoomControl: false });

L.control.zoom({ position: 'topright' }).addTo(map);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 18
}).addTo(map);

function makeIcon(color, size = 14) {
    return L.divIcon({
        className: '',
        html: `<div style="width:${size}px;height:${size}px;border-radius:50%;background:${color};border:2.5px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.3)"></div>`,
        iconSize: [size, size],
        iconAnchor: [size/2, size/2],
        popupAnchor: [0, -size/2]
    });
}

fetch('{{ route("gis.data") }}')
    .then(r => r.json())
    .then(data => {
        const rightPanel = document.getElementById('right-panel');
        window.closeRightPanel = () => {
            rightPanel.classList.remove('open');
        };

        let currentPanelData = null;
        let currentPanelType = null;
        let currentPanelColor = null;
        let currentPanelTitle = null;
        let filterYear = new Date().getFullYear().toString();
        let filterMonth = "All";

        window.setFilter = (type, value) => {
            if (type === 'year') filterYear = value.toString();
            if (type === 'month') filterMonth = value;
            renderPanelBody();
        };

        const indoMonths = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const enMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        const renderFiltersHTML = (themeColor) => {
            const currentY = new Date().getFullYear();
            const years = [currentY - 4, currentY - 3, currentY - 2, currentY - 1, currentY];
            const activeClass = themeColor === '#16a34a' ? 'active-green' : 'active';
            
            let yearHtml = years.map(y => `<div class="filter-btn ${filterYear === y.toString() ? activeClass : ''}" onclick="setFilter('year', '${y}')">${y}</div>`).join('');
            
            let monthHtml = `<div class="filter-btn ${filterMonth === 'All' ? activeClass : ''}" onclick="setFilter('month', 'All')">Semua Bulan</div>`;
            monthHtml += enMonths.map((m, i) => `<div class="filter-btn ${filterMonth === m ? activeClass : ''}" onclick="setFilter('month', '${m}')">${indoMonths[i]}</div>`).join('');

            return `
                <div class="rp-section">🗓️ FILTER TAHUN</div>
                <div class="filter-row">
                    ${yearHtml}
                </div>
                <div class="rp-section">📅 RIWAYAT</div>
                <div class="filter-row">
                    ${monthHtml}
                </div>
            `;
        };

        const renderMats = (mats, isGreen) => {
            if (!mats || Object.keys(mats).length === 0) return `<div class="rp-mat-card"><div class="rp-mat-name">Belum ada stok</div></div>`;
            return Object.entries(mats).map(([k, v]) => `
                <div class="rp-mat-card ${isGreen ? 'green' : ''}">
                    <div class="rp-mat-name">${k}</div>
                    <div class="rp-mat-val">${Number(v).toLocaleString('id')}<span> Kg</span></div>
                </div>
            `).join('');
        };

        const renderHist = (history) => {
            if (!history || history.length === 0) return `<div style="font-size:13px;color:#94a3b8;font-style:italic">Belum ada riwayat transaksi pada filter ini.</div>`;
            return history.map(h => {
                const details = h.details.map(d => `
                    <tr>
                        <td>${d.material}</td>
                        <td>${d.weight} Kg</td>
                        <td>Rp ${d.price ? Number(d.price).toLocaleString('id') : '0'}</td>
                        <td>Rp ${d.total ? Number(d.total).toLocaleString('id') : '0'}</td>
                    </tr>
                `).join('');
                
                return `
                <div class="hist-card">
                    <div class="hist-head">
                        <div>
                            <div class="hist-date">${h.date}</div>
                            <div class="hist-type">${h.type} ${h.from_to ? `&bull; ${h.from_to}` : ''}</div>
                        </div>
                        ${h.total_value > 0 ? `<div class="hist-total-val">+ Rp ${Number(h.total_value).toLocaleString('id')}</div>` : ''}
                    </div>
                    <table class="hist-table">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th>Jumlah</th>
                                <th>Harga</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${details}
                        </tbody>
                    </table>
                    <div class="hist-foot">
                        <div class="hist-foot-box">
                            <div class="hist-foot-label">Total Sampah</div>
                            <div class="hist-foot-val">${Number(h.total_weight).toLocaleString('id')} Kg</div>
                        </div>
                        <div class="hist-foot-box green">
                            <div class="hist-foot-label">Total Pendapatan</div>
                            <div class="hist-foot-val">Rp ${Number(h.total_value).toLocaleString('id')}</div>
                        </div>
                    </div>
                </div>
                `;
            }).join('');
        };

        const renderPanelBody = () => {
            const data = currentPanelData;
            const type = currentPanelType;
            const color = currentPanelColor;
            const titleType = currentPanelTitle;

            const filteredHistory = data.history.filter(h => {
                return (h.year === filterYear) && (filterMonth === 'All' || h.month === filterMonth);
            });

            const stockMap = {};
            let totalStock = 0;
            let connected = '';
            
            if (type === 'aggregator') {
                data.stocks.forEach(s => { stockMap[s.material] = s.stock; totalStock += parseFloat(s.stock); });
                connected = `<div class="rp-section">🌐 KONEKSI</div>
                    <div class="conn-item" style="color:#16a34a;font-weight:700"><div class="conn-dot" style="background:#16a34a"></div> BSU Terkoneksi: ${data.bsu_count}</div>`;
            } else if (type === 'bsu') {
                connected = `<div class="rp-section">🌐 KONEKSI</div>
                    <div class="conn-item" style="color:#ea580c;font-weight:700"><div class="conn-dot" style="background:#ea580c"></div> Agregator Tujuan: ${data.aggregator || '-'}</div>`;
            } else if (type === 'recycler') {
                connected = ``;
            }

            const isGreen = type === 'bsu';

            rightPanel.innerHTML = `
                <div class="rp-header" style="background:${color}">
                    <div class="rp-close" onclick="closeRightPanel()">✕</div>
                    <div class="rp-type">♻️ ${titleType}</div>
                    <div class="rp-name">${data.name}</div>
                    <div class="rp-addr">📍 ${data.address}</div>
                </div>
                <div class="rp-body">
                    ${renderFiltersHTML(color)}
                    
                    ${type === 'aggregator' ? `<div class="rp-section">📦 INVENTORY (STOCK)</div>` : `<div class="rp-section">📦 MATERIAL</div>`}
                    <div class="rp-mats">
                        ${renderMats(type === 'aggregator' ? stockMap : data.materials, isGreen)}
                    </div>
                    
                    ${connected}

                    <div class="rp-section">🔄 WASTE TRACEABILITY (${filteredHistory.length} Transaksi)</div>
                    <div>
                        ${renderHist(filteredHistory)}
                    </div>
                </div>
            `;
        };

        const openRightPanel = (data, type, color, titleType) => {
            currentPanelData = data;
            currentPanelType = type;
            currentPanelColor = color;
            currentPanelTitle = titleType;
            filterYear = new Date().getFullYear().toString();
            filterMonth = "All";
            
            renderPanelBody();
            rightPanel.classList.add('open');
        };

        // BSU markers
        data.bsu.forEach(b => {
            const m = L.marker([b.lat, b.lng], { icon: makeIcon('#16a34a', 14) }).addTo(map);
            m.on('click', () => openRightPanel(b, 'bsu', '#16a34a', 'WASTE COLLECTION'));
        });

        // Aggregator markers
        data.aggregators.forEach(a => {
            const m = L.marker([a.lat, a.lng], { icon: makeIcon('#d97706', 17) }).addTo(map);
            m.on('click', () => openRightPanel(a, 'aggregator', '#ea580c', 'AGREGATOR'));
        });

        // Recycler markers
        data.recyclers.forEach(r => {
            const m = L.marker([r.lat, r.lng], { icon: makeIcon('#7c3aed', 17) }).addTo(map);
            m.on('click', () => openRightPanel(r, 'recycler', '#7c3aed', 'RECYCLER'));
        });

        // Stat sidebar
        const sidebarEl = L.control({ position: 'topleft' });
        sidebarEl.onAdd = () => {
            const div = L.DomUtil.create('div', 'map-sidebar');
            div.innerHTML = `
                <div class="map-sidebar-head">Ringkasan</div>
                <div class="map-stat-row">
                    <div class="map-stat-label"><div class="map-stat-dot" style="background:#16a34a"></div>BSU</div>
                    <div class="map-stat-val">${data.bsu.length}</div>
                </div>
                <div class="map-stat-row">
                    <div class="map-stat-label"><div class="map-stat-dot" style="background:#ea580c"></div>Agregator</div>
                    <div class="map-stat-val">${data.aggregators.length}</div>
                </div>
                <div class="map-stat-row">
                    <div class="map-stat-label"><div class="map-stat-dot" style="background:#7c3aed"></div>Recycler</div>
                    <div class="map-stat-val">${data.recyclers.length}</div>
                </div>
            `;
            L.DomEvent.disableClickPropagation(div);
            return div;
        };
        sidebarEl.addTo(map);
        
        // Hide panel when clicking map
        map.on('click', () => { closeRightPanel(); });
    });
</script>
@endpush
