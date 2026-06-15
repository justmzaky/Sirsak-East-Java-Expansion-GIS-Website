<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — PT Sirkular Saka Indonesia</title>
    <link rel="icon" href="{{ asset('logo_sirsak.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/dist/tabler-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',system-ui,sans-serif;background:#f1f5f9;min-height:100vh;display:flex;align-items:center;justify-content:center}

        .login-wrapper{display:flex;width:900px;min-height:540px;border-radius:20px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.1)}

        .login-left{flex:1;background:linear-gradient(145deg,#15803d 0%,#16a34a 45%,#22c55e 100%);padding:48px 44px;display:flex;flex-direction:column;justify-content:space-between;position:relative;overflow:hidden}
        .login-left::before{content:'';position:absolute;top:-80px;right:-80px;width:280px;height:280px;border-radius:50%;background:rgba(255,255,255,.06)}
        .login-left::after{content:'';position:absolute;bottom:-60px;left:-40px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.06)}
        .left-logo{display:flex;align-items:center;gap:10px;z-index:1}
        .left-logo-icon{width:38px;height:38px;background:rgba(255,255,255,.2);border-radius:10px;display:flex;align-items:center;justify-content:center}
        .left-logo-icon svg{width:20px;height:20px;stroke:#fff;stroke-width:2;fill:none;stroke-linecap:round;stroke-linejoin:round}
        .left-logo-name{font-size:18px;font-weight:700;color:#fff}
        .left-body{z-index:1}
        .left-title{font-size:26px;font-weight:700;color:#fff;line-height:1.3;margin-bottom:12px}
        .left-desc{font-size:14px;color:rgba(255,255,255,.8);line-height:1.7}
        .left-chips{display:flex;flex-wrap:wrap;gap:8px;margin-top:20px}
        .left-chip{background:rgba(255,255,255,.15);color:#fff;font-size:11.5px;font-weight:500;padding:5px 12px;border-radius:20px;border:1px solid rgba(255,255,255,.2)}
        .left-foot{font-size:11.5px;color:rgba(255,255,255,.6);z-index:1}

        .login-right{width:380px;background:#fff;padding:48px 40px;display:flex;flex-direction:column;justify-content:center}
        .form-head{margin-bottom:30px}
        .form-head h2{font-size:22px;font-weight:700;color:#0f172a;margin-bottom:6px}
        .form-head p{font-size:13px;color:#64748b}

        .form-group{margin-bottom:18px}
        .form-label{display:block;font-size:12.5px;font-weight:600;color:#374151;margin-bottom:6px}
        .input-wrap{position:relative}
        .input-icon{position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:17px;color:#94a3b8;pointer-events:none}
        .form-input{width:100%;height:42px;padding:0 12px 0 40px;border:1px solid #e2e8f0;border-radius:9px;font-size:13.5px;color:#0f172a;font-family:inherit;transition:all .15s}
        .form-input:focus{outline:none;border-color:#16a34a;box-shadow:0 0 0 3px rgba(22,163,74,.12)}
        .form-input.error{border-color:#dc2626;background:#fef2f2}

        .form-error{font-size:12px;color:#dc2626;margin-top:5px;display:flex;align-items:center;gap:4px}
        .remember-row{display:flex;align-items:center;gap:8px;margin-bottom:22px}
        .remember-row input[type=checkbox]{width:16px;height:16px;accent-color:#16a34a}
        .remember-row label{font-size:13px;color:#475569}

        .btn-login{width:100%;height:44px;background:#16a34a;color:#fff;border:none;border-radius:9px;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:7px;transition:background .15s}
        .btn-login:hover{background:#15803d}
        .btn-login:disabled{opacity:.6;cursor:not-allowed}

        .form-foot{margin-top:24px;padding-top:20px;border-top:1px solid #f1f5f9;text-align:center;font-size:12px;color:#94a3b8}
        .form-foot span{font-weight:600;color:#16a34a}
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-left">
            <div class="left-logo">
                <div class="left-logo-icon" style="background:transparent;width:50px;height:50px;">
                    <img src="{{ asset('logo_sirsak.png') }}" alt="Logo" style="width:100%;height:100%;object-fit:contain;">
                </div>
                <span class="left-logo-name" style="font-size:16px;">PT Sirkular Saka Indonesia</span>
            </div>
            <div class="left-body">
                <div class="left-title">East Java Waste<br>Traceability System</div>
                <div class="left-desc">Platform terintegrasi untuk memonitor dan menelusuri alur sampah dari Bank Sampah Unit hingga fasilitas daur ulang di seluruh Jawa Timur.</div>
                <div class="left-chips">
                    <span class="left-chip"><i class="ti ti-map-pin"></i> GIS Mapping</span>
                    <span class="left-chip"><i class="ti ti-recycle"></i> Material Tracking</span>
                    <span class="left-chip"><i class="ti ti-chart-bar"></i> Real-time Analytics</span>
                </div>
            </div>
            <div class="left-foot">PT Sirkular Saka Indonesia &copy; {{ date('Y') }}</div>
        </div>

        <div class="login-right" x-data="{loading:false}">
            <div class="form-head">
                <h2>Selamat Datang</h2>
                <p>Masuk ke panel administrasi</p>
            </div>

            <form action="{{ route('login.post') }}" method="POST" @submit="loading=true">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <div class="input-wrap">
                        <i class="ti ti-mail input-icon"></i>
                        <input type="email" name="email" class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                            placeholder="email@sirsak.id" value="{{ old('email') }}" autocomplete="email" autofocus>
                    </div>
                    @error('email')<div class="form-error"><i class="ti ti-alert-circle" style="font-size:13px"></i>{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-wrap">
                        <i class="ti ti-lock input-icon"></i>
                        <input type="password" name="password" class="form-input" placeholder="Masukkan password" autocomplete="current-password">
                    </div>
                </div>

                <div class="remember-row">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Ingat saya selama 30 hari</label>
                </div>

                <button type="submit" class="btn-login" :disabled="loading">
                    <i class="ti ti-login" x-show="!loading"></i>
                    <i class="ti ti-loader-2" x-show="loading" style="animation:spin 1s linear infinite"></i>
                    <span x-text="loading ? 'Masuk...' : 'Masuk ke Dashboard'"></span>
                </button>
            </form>

            <div class="form-foot">
                Sistem ini hanya untuk pengguna terdaftar.<br>
                Dikelola oleh <span>PT Sirkular Saka Indonesia</span>
            </div>
        </div>
    </div>
    <style>@keyframes spin{to{transform:rotate(360deg)}}</style>
</body>
</html>
