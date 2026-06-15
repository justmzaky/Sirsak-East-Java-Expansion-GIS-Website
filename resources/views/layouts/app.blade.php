<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — PT Sirkular Saka Indonesia</title>
    <link rel="icon" href="{{ asset('logo_sirsak.png') }}" type="image/png">

    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/dist/tabler-icons.min.css">
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{
            --green:#16a34a;--green-dark:#15803d;--green-light:#dcfce7;--green-mid:#86efac;
            --green-50:#f0fdf4;--green-100:#dcfce7;--green-600:#16a34a;--green-700:#15803d;
            --page:#f1f5f9;--white:#ffffff;
            --border:#e2e8f0;--border2:#cbd5e1;
            --text:#0f172a;--text2:#475569;--text3:#94a3b8;
            --sidebar-w:228px;
            --topbar-h:56px;
        }
        html,body{height:100%;font-family:'Inter',system-ui,sans-serif;font-size:13.5px;color:var(--text);background:var(--page)}
        a{text-decoration:none;color:inherit}
        input,select,textarea,button{font-family:inherit;font-size:inherit}

        /* ── Layout ── */
        .layout{display:flex;height:100vh;overflow:hidden}
        .sidebar{width:var(--sidebar-w);flex-shrink:0;background:var(--white);border-right:1px solid var(--border);display:flex;flex-direction:column;overflow-y:auto}
        .main-area{flex:1;display:flex;flex-direction:column;overflow:hidden}
        .topbar{height:var(--topbar-h);background:var(--white);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 24px;gap:10px;flex-shrink:0}
        .page-content{flex:1;overflow-y:auto;padding:24px}

        /* ── Sidebar ── */
        .sidebar-logo{padding:18px 16px 16px;border-bottom:1px solid var(--border)}
        .logo-row{display:flex;align-items:center;gap:10px}
        .logo-icon{width:34px;height:34px;background:var(--green);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
        .logo-icon svg{width:18px;height:18px;stroke:#fff;stroke-width:2;fill:none;stroke-linecap:round;stroke-linejoin:round}
        .brand-name{font-size:14px;font-weight:700;color:var(--text);letter-spacing:-.3px}
        .brand-sub{font-size:10px;color:var(--text3);margin-top:1px;font-weight:400}

        .sidebar-nav{flex:1;padding:10px 8px}
        .nav-group-label{font-size:10px;font-weight:600;color:var(--text3);letter-spacing:.08em;text-transform:uppercase;padding:12px 8px 5px;display:block}
        .nav-item{display:flex;align-items:center;gap:9px;padding:8px 10px;border-radius:8px;color:var(--text2);transition:background .12s,color .12s;cursor:pointer;margin-bottom:1px}
        .nav-item:hover{background:#f8fafc;color:var(--text)}
        .nav-item.active{background:var(--green-light);color:var(--green-dark);font-weight:600}
        .nav-item i{font-size:17px;flex-shrink:0;width:20px;text-align:center}
        .nav-item span{font-size:13px;flex:1}
        .nav-badge{font-size:10px;font-weight:600;padding:1px 7px;border-radius:20px;background:#dcfce7;color:var(--green-dark)}
        .nav-badge.sa{background:#fef9c3;color:#a16207}

        .sidebar-foot{padding:12px;border-top:1px solid var(--border)}
        .user-card{display:flex;align-items:center;gap:9px}
        .user-avatar{width:34px;height:34px;border-radius:50%;background:var(--green-light);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--green-dark);flex-shrink:0}
        .user-name{font-size:12.5px;font-weight:600;color:var(--text);line-height:1.3}
        .user-role{font-size:10.5px;color:var(--green);font-weight:500;margin-top:1px}

        /* ── Topbar ── */
        .breadcrumb{display:flex;align-items:center;gap:5px;font-size:13px}
        .breadcrumb a{color:var(--text3)}
        .breadcrumb a:hover{color:var(--text)}
        .breadcrumb-sep{color:var(--text3);font-size:11px}
        .breadcrumb-current{font-weight:600;color:var(--text)}
        .topbar-spacer{flex:1}
        .topbar-actions{display:flex;align-items:center;gap:8px}

        /* ── Buttons ── */
        .btn{display:inline-flex;align-items:center;gap:6px;height:34px;padding:0 14px;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;border:1px solid var(--border2);background:var(--white);color:var(--text2);transition:all .12s;white-space:nowrap}
        .btn:hover{background:#f8fafc;color:var(--text)}
        .btn i{font-size:15px}
        .btn-green{background:var(--green);color:#fff;border-color:var(--green);font-weight:600}
        .btn-green:hover{background:var(--green-dark)}
        .btn-red{background:#dc2626;color:#fff;border-color:#dc2626}
        .btn-red:hover{background:#b91c1c}
        .btn-sm{height:28px;padding:0 10px;font-size:12px}
        .btn-sm i{font-size:13px}
        .btn-icon{width:34px;padding:0;justify-content:center}

        /* ── Cards ── */
        .card{background:var(--white);border:1px solid var(--border);border-radius:12px;overflow:hidden}
        .card-header{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
        .card-title{font-size:14px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:8px}
        .card-title i{font-size:16px;color:var(--green)}
        .card-subtitle{font-size:12px;color:var(--text3);margin-top:2px}
        .card-body{padding:20px}

        /* ── KPI Cards ── */
        .kpi-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:20px}
        .kpi-card{background:var(--white);border:1px solid var(--border);border-radius:12px;padding:16px;border-top:3px solid}
        .kpi-label{font-size:11px;font-weight:600;color:var(--text3);text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px}
        .kpi-val{font-size:22px;font-weight:700;color:var(--text);letter-spacing:-.5px;line-height:1}
        .kpi-unit{font-size:12px;font-weight:400;color:var(--text3);margin-left:3px}
        .kpi-trend{font-size:11.5px;margin-top:6px;display:flex;align-items:center;gap:3px;font-weight:500}
        .kpi-trend.up{color:#16a34a} .kpi-trend.dn{color:#dc2626}

        /* ── Tables ── */
        .table-wrap{overflow-x:auto}
        table{width:100%;border-collapse:collapse}
        thead th{background:#f8fafc;padding:10px 14px;text-align:left;font-size:11.5px;font-weight:600;color:var(--text3);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--border);white-space:nowrap}
        tbody td{padding:12px 14px;font-size:13px;color:var(--text);border-bottom:1px solid var(--border)}
        tbody tr:last-child td{border-bottom:none}
        tbody tr:hover{background:#fafbfc}

        /* ── Badges ── */
        .badge{display:inline-flex;align-items:center;gap:3px;font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;white-space:nowrap}
        .badge-green{background:var(--green-light);color:var(--green-dark)}
        .badge-amber{background:#fef3c7;color:#92400e}
        .badge-blue{background:#eff6ff;color:#1d4ed8}
        .badge-red{background:#fef2f2;color:#991b1b}
        .badge-gray{background:#f1f5f9;color:#475569}
        .badge-purple{background:#f3e8ff;color:#6b21a8}

        /* ── Forms ── */
        .form-group{display:flex;flex-direction:column;gap:5px;margin-bottom:16px}
        .form-label{font-size:12.5px;font-weight:600;color:var(--text2)}
        .form-label .req{color:#dc2626}
        .form-control{height:38px;padding:0 12px;border:1px solid var(--border2);border-radius:8px;background:var(--white);color:var(--text);font-size:13px;width:100%;transition:border-color .15s,box-shadow .15s}
        .form-control:focus{outline:none;border-color:var(--green);box-shadow:0 0 0 3px rgba(22,163,74,.12)}
        textarea.form-control{height:auto;padding:10px 12px;resize:vertical}
        .form-hint{font-size:11.5px;color:var(--text3);margin-top:3px}
        .form-error{font-size:11.5px;color:#dc2626;margin-top:3px}
        .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}

        /* ── Alerts ── */
        .alert{padding:12px 16px;border-radius:8px;font-size:13px;display:flex;align-items:flex-start;gap:8px;margin-bottom:16px}
        .alert i{font-size:16px;flex-shrink:0;margin-top:1px}
        .alert-success{background:#f0fdf4;border:1px solid #bbf7d0;color:var(--green-dark)}
        .alert-error{background:#fef2f2;border:1px solid #fecaca;color:#991b1b}
        .alert-info{background:#eff6ff;border:1px solid #bfdbfe;color:#1e40af}
        .alert-warning{background:#fffbeb;border:1px solid #fde68a;color:#92400e}

        /* ── Page header ── */
        .page-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px}
        .page-header-left h1{font-size:20px;font-weight:700;color:var(--text);letter-spacing:-.4px}
        .page-header-left p{font-size:12.5px;color:var(--text3);margin-top:3px}

        /* ── Filter bar ── */
        .filter-bar{background:var(--white);border:1px solid var(--border);border-radius:10px;padding:14px 18px;display:flex;align-items:center;gap:10px;margin-bottom:20px;flex-wrap:wrap}
        .filter-label{font-size:12px;font-weight:600;color:var(--text2);white-space:nowrap}
        .filter-select{height:34px;padding:0 10px;border:1px solid var(--border2);border-radius:7px;font-size:13px;color:var(--text);background:var(--white)}
        .filter-select:focus{outline:none;border-color:var(--green)}

        /* ── Stats in table header ── */
        .stat-strip{display:flex;gap:12px;flex-wrap:wrap}
        .stat-chip{background:#f8fafc;border:1px solid var(--border);border-radius:8px;padding:8px 14px}
        .stat-chip-label{font-size:10.5px;color:var(--text3);font-weight:500;text-transform:uppercase;letter-spacing:.05em}
        .stat-chip-val{font-size:16px;font-weight:700;color:var(--text);margin-top:2px}

        /* ── Pagination ── */
        .pagination{display:flex;align-items:center;gap:4px;justify-content:flex-end;padding:14px 20px;border-top:1px solid var(--border)}
        .pagination a,.pagination span{display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 8px;border-radius:7px;font-size:12.5px;font-weight:500;border:1px solid var(--border);color:var(--text2);transition:all .12s}
        .pagination a:hover{background:#f8fafc;color:var(--text)}
        .pagination .active span{background:var(--green);color:#fff;border-color:var(--green)}
        .pagination .disabled span{color:var(--text3);cursor:not-allowed}

        /* ── Map ── */
        #map{height:calc(100vh - var(--topbar-h) - 48px);border-radius:0}

        /* Misc */
        .text-green{color:var(--green)} .text-gray{color:var(--text3)} .text-red{color:#dc2626}
        .font-mono{font-family:ui-monospace,monospace}
        .truncate{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .empty-state{padding:48px 24px;text-align:center}
        .empty-state i{font-size:36px;color:var(--text3);display:block;margin-bottom:10px}
        .empty-state p{font-size:14px;color:var(--text3)}

        /* Responsive table on mobile */
        @media(max-width:768px){
            .sidebar{display:none}
            .kpi-grid{grid-template-columns:repeat(2,1fr)}
            .form-grid{grid-template-columns:1fr}
        }
    </style>

    @stack('head')
</head>
<body>
<div class="layout">

    {{-- ── SIDEBAR ─────────────────────────────────────────────── --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="logo-row">
                <div class="logo-icon" style="background:transparent;width:42px;height:42px;">
                    <img src="{{ asset('logo_sirsak.png') }}" alt="Logo" style="width:100%;height:100%;object-fit:contain;">
                </div>
                <div style="flex:1">
                    <div class="brand-name" style="font-size:11.5px;line-height:1.3;white-space:normal;">PT Sirkular Saka Indonesia</div>
                    <div class="brand-sub" style="margin-top:2px">East Java Waste Traceability</div>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <span class="nav-group-label">Overview</span>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="ti ti-layout-dashboard"></i><span>Dashboard</span>
            </a>
            <a href="{{ route('gis.index') }}" class="nav-item {{ request()->routeIs('gis.*') ? 'active' : '' }}">
                <i class="ti ti-map-2"></i><span>GIS Map</span>
            </a>

            <span class="nav-group-label">Monitoring</span>
            <a href="{{ route('waste-collection.index') }}" class="nav-item {{ request()->routeIs('waste-collection.*') ? 'active' : '' }}">
                <i class="ti ti-recycle"></i><span>Waste Collection</span>
            </a>
            <a href="{{ route('agregator.index') }}" class="nav-item {{ request()->routeIs('agregator.*') ? 'active' : '' }}">
                <i class="ti ti-truck-delivery"></i><span>Agregator</span>
            </a>
            <a href="{{ route('recycler.index') }}" class="nav-item {{ request()->routeIs('recycler.*') ? 'active' : '' }}">
                <i class="ti ti-building-factory-2"></i><span>Recycler</span>
            </a>

            @role('superadmin')
            <span class="nav-group-label">Manajemen</span>
            <a href="{{ route('superadmin.entitas.bsu.index') }}" class="nav-item {{ request()->routeIs('superadmin.entitas.*') ? 'active' : '' }}">
                <i class="ti ti-buildings"></i><span>Kelola Entitas</span><span class="nav-badge sa">SA</span>
            </a>
            <a href="{{ route('superadmin.penimbangan.index') }}" class="nav-item {{ request()->routeIs('superadmin.penimbangan.*') ? 'active' : '' }}">
                <i class="ti ti-scale"></i><span>Penimbangan</span><span class="nav-badge sa">SA</span>
            </a>
            <a href="{{ route('superadmin.pengiriman.index') }}" class="nav-item {{ request()->routeIs('superadmin.pengiriman.*') ? 'active' : '' }}">
                <i class="ti ti-arrows-right-left"></i><span>Pengiriman</span><span class="nav-badge sa">SA</span>
            </a>
            <a href="{{ route('superadmin.laporan.index') }}" class="nav-item {{ request()->routeIs('superadmin.laporan.*') ? 'active' : '' }}">
                <i class="ti ti-report-analytics"></i><span>Laporan</span><span class="nav-badge sa">SA</span>
            </a>
            @endrole
        </nav>

        <div class="sidebar-foot">
            <div class="user-card">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                <div style="flex:1;min-width:0">
                    <div class="user-name truncate">{{ auth()->user()->name }}</div>
                    <div class="user-role">{{ auth()->user()->isSuperAdmin() ? 'Super Admin' : 'Admin' }}</div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-icon btn-sm" title="Logout">
                        <i class="ti ti-logout" style="font-size:16px;color:#dc2626"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ── MAIN AREA ─────────────────────────────────────────────── --}}
    <div class="main-area">
        <header class="topbar">
            <div class="breadcrumb">
                <a href="{{ route('dashboard') }}"><i class="ti ti-home" style="font-size:14px"></i></a>
                @yield('breadcrumb')
            </div>
            <div class="topbar-spacer"></div>
            <div class="topbar-actions">
                @yield('topbar-actions')
            </div>
        </header>

        <main class="page-content">
            {{-- Flash messages --}}
            @if(session('success'))
            <div class="alert alert-success" x-data x-init="setTimeout(()=>$el.remove(),5000)">
                <i class="ti ti-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-error" x-data x-init="setTimeout(()=>$el.remove(),5000)">
                <i class="ti ti-circle-x"></i>
                <span>{{ session('error') }}</span>
            </div>
            @endif
            @if($errors->any())
            <div class="alert alert-error">
                <i class="ti ti-alert-circle"></i>
                <div>
                    <strong>Terdapat kesalahan:</strong>
                    <ul style="margin-top:4px;padding-left:16px">
                        @foreach($errors->all() as $e)<li style="margin-top:2px">{{ $e }}</li>@endforeach
                    </ul>
                </div>
            </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
