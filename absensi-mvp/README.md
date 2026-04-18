# 📋 Absensi MVP - Sistem Presensi Sederhana

Sistem absensi/check-in check-out karyawan atau siswa dengan fitur minimum yang siap dikembangkan.

## ✅ Fitur (5 Fitur Inti)

1. **Login** → Admin & User (karyawan/siswa)
2. **Check-in & Check-out** → Tombol sederhana, waktu dicatat dari server
3. **Riwayat Pribadi** → User bisa lihat tabel kehadiran sendiri
4. **Dashboard Admin** → Lihat semua data + filter tanggal
5. **Export CSV/Excel** → Download rekap untuk keperluan payroll/laporan

## 🛠️ Tech Stack

- **Frontend**: HTML + CSS (Bootstrap 5) + Vanilla JS
- **Backend**: PHP Native (PDO)
- **Database**: MySQL/MariaDB
- **Hosting**: Local (XAMPP/Laragon) → Shared Hosting (cPanel)

## 📦 Instalasi

### 1. Clone/Download Project
```bash
cd /path/to/your/webserver
# Jika menggunakan Git
git clone <repository-url> absensi-mvp

# Atau extract file ke folder ini
```

### 2. Buat Database

Buka phpMyAdmin (http://localhost/phpmyadmin) dan:

1. Buat database baru bernama `absensi_mvp`
2. Import file `database.sql`

Atau via command line:
```bash
mysql -u root -p absensi_mvp < database.sql
```

### 3. Konfigurasi Database

Edit file `includes/config.php`:

```php
$host = 'localhost';
$dbname = 'absensi_mvp';
$username = 'root';
$password = ''; // Sesuaikan dengan password MySQL Anda
```

### 4. Akses Aplikasi

Buka browser dan akses:
```
http://localhost/absensi-mvp/login.php
```

## 👤 Akun Demo

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@absensi.com | admin123 |
| User | user@absensi.com | user123 |

## 📁 Struktur Folder

```
absensi-mvp/
├── includes/
│   ├── config.php       # Konfigurasi database
│   └── functions.php    # Fungsi helper
├── css/                  # (Opsional) Custom CSS
├── js/                   # (Opsional) Custom JS
├── login.php            # Halaman login
├── logout.php           # Proses logout
├── dashboard.php        # Dashboard user (check-in/out)
├── admin.php            # Dashboard admin
├── export.php           # Export data ke CSV
└── database.sql         # Script database
```

## 🔒 Keamanan

- Password di-hash dengan `password_hash()` (bcrypt)
- Session-based authentication
- Prepared statements untuk mencegah SQL Injection
- XSS protection dengan `htmlspecialchars()`
- Waktu diambil dari server, bukan client

## ⚙️ Konfigurasi Tambahan

### Mengubah Jam Toleransi

Edit `includes/config.php`:

```php
define('WAKTU_MASUK_TOLERANSI', '08:00:00'); // Jam masuk toleransi
define('WAKTU_PULANG_MINIMAL', '16:00:00');  // Jam pulang minimal
```

## 🚀 Deploy ke Hosting

1. Upload semua file ke hosting via FTP/cPanel File Manager
2. Buat database di cPanel MySQL Databases
3. Import `database.sql` ke database yang dibuat
4. Edit `includes/config.php` dengan kredensial database hosting
5. Akses domain Anda

## 📝 Catatan Pengembangan

Fitur yang bisa ditambahkan nanti:
- [ ] GPS Location tracking
- [ ] Foto selfie saat presensi
- [ ] Manajemen cuti/izin
- [ ] Shift kerja
- [ ] Notifikasi email/WhatsApp
- [ ] Laporan bulanan
- [ ] Multi-level approval

## 📄 License

Free to use and modify.

---

**Dibuat dengan ❤️ untuk kemudahan manajemen absensi**
