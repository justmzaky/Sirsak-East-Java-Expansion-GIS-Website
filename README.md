# SIRSAK — East Java Waste Traceability System
**PT Sirkular Saka Indonesia**

Sistem traceability sampah Jawa Timur berbasis web menggunakan Laravel 12 + MySQL 8.

---

## 🔧 Tech Stack
- **Backend:** Laravel 12, PHP 8.2+
- **Database:** MySQL 8.x
- **Frontend:** Blade + Alpine.js (CDN) + Tailwind-style custom CSS
- **Maps:** Leaflet.js (OpenStreetMap)
- **Charts:** Chart.js
- **Icons:** Tabler Icons
- **Auth & Roles:** Laravel Sanctum + Spatie Laravel Permission
- **Export:** Maatwebsite Laravel Excel

---

## 🚀 Instalasi

### 1. Clone & Setup
```bash
git clone <repo-url> sirsak
cd sirsak
composer install
cp .env.example .env
php artisan key:generate
```

### 2. Konfigurasi Database
Edit `.env`:
```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sirsak_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. Migrasi & Seed
```bash
php artisan migrate
php artisan db:seed
```

### 4. Jalankan Server
```bash
php artisan serve
```

Buka: **http://localhost:8000**

---

## 👤 Akun Default

| Role       | Email                    | Password      |
|------------|--------------------------|---------------|
| SuperAdmin | superadmin@sirsak.id     | superadmin123 |
| Admin      | admin@sirsak.id          | admin123      |

> ⚠️ Ganti password default setelah login pertama!

---

## 📁 Struktur Halaman

### Dapat Diakses (Admin & SuperAdmin)
| Halaman           | URL                    | Deskripsi                              |
|-------------------|------------------------|----------------------------------------|
| Login             | `/login`               | Halaman login                          |
| Dashboard         | `/dashboard`           | KPI, grafik material, aktivitas terbaru|
| GIS Map           | `/gis`                 | Peta interaktif BSU, Agregator, Recycler|
| Waste Collection  | `/waste-collection`    | Riwayat penimbangan seluruh BSU        |
| Agregator         | `/agregator`           | Inventori & monitoring agregator       |
| Recycler          | `/recycler`            | Monitoring penerimaan recycler         |

### Hanya SuperAdmin
| Halaman           | URL                             | Deskripsi                       |
|-------------------|---------------------------------|---------------------------------|
| Kelola Agregator  | `/superadmin/entitas/aggregator`| CRUD data agregator             |
| Kelola BSU        | `/superadmin/entitas/bsu`       | CRUD data Bank Sampah Unit      |
| Kelola Recycler   | `/superadmin/entitas/recycler`  | CRUD data recycler              |
| Input Penimbangan | `/superadmin/penimbangan`       | Input + riwayat penimbangan BSU |
| Pengiriman        | `/superadmin/pengiriman`        | Kirim dari agregator ke recycler|

---

## ⚙️ Alur Sistem

```
BSU (Waste Collection)
    ↓ Input Penimbangan (SuperAdmin)
    ↓ [net_weight_kg menambah aggregator_stocks]
Agregator (Pengepul)
    ↓ Input Pengiriman (SuperAdmin)
    ↓ [shipped_weight_kg mengurangi aggregator_stocks]
Recycler (Daur Ulang)
    → View History Penerimaan
```

---

## 🗄️ Database Schema

### Tabel Utama
| Tabel               | Keterangan                                  |
|---------------------|---------------------------------------------|
| `users`             | Akun admin, Primary Key: ULID               |
| `roles`             | Spatie role (superadmin, admin)             |
| `aggregators`       | Data pengepul                               |
| `waste_units`       | Data BSU, FK → aggregators                  |
| `recyclers`         | Data fasilitas daur ulang                   |
| `collections`       | Transaksi penimbangan BSU → Agregator       |
| `aggregator_stocks` | Inventori stok per material per agregator   |
| `shipments`         | Pengiriman Agregator → Recycler             |
| `activity_logs`     | Audit trail semua aksi                      |

### Jenis Material
`PET`, `MLP`, `Kardus`, `Metal`, `HDPE`, `Campuran`

### Status Pengiriman
`dispatched` → `in_transit` → `received` / `cancelled`

---

## 🔑 Sistem Role & Permission

- **superadmin**: Akses penuh semua halaman
- **admin**: Dashboard, GIS, Waste Collection, Agregator, Recycler (view only)

---

## 📦 Package Dependencies
```json
{
  "laravel/framework": "^12.0",
  "laravel/sanctum": "^4.0",
  "spatie/laravel-permission": "^6.10",
  "maatwebsite/excel": "^3.1"
}
```

---

## 🌐 Deployment (Production)

```bash
# Set environment
APP_ENV=production
APP_DEBUG=false

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Web server: Nginx + PHP-FPM 8.3
# Document root: /var/www/sirsak/public
```

---

## 📞 Support
**PT Sirkular Saka Indonesia**
Email: admin@sirsak.id
