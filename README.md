# Roblox Store

[![CI Automated Test Suite](https://github.com/IlhamXkyo/roblox-store-task/actions/workflows/ci.yml/badge.svg)](https://github.com/IlhamXkyo/roblox-store-task/actions/workflows/ci.yml)
[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![Database](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![Tests](https://img.shields.io/badge/Tests-79%20Passed-brightgreen)](tests/test_store_logic.php)

Aplikasi web toko e-commerce berbasis PHP Native dan MySQL untuk transaksi item virtual game Roblox. Proyek ini dibangun tanpa framework eksternal untuk mendemonstrasikan implementasi autentikasi peran ganda (Admin & Buyer), manajemen inventaris item, dan alur transaksi pesanan berbasis session yang aman.

---

## Fitur Utama

### Pengguna (Pembeli)
- **Autentikasi Aman**: Registrasi akun pembeli, validasi hash password (`PASSWORD_DEFAULT`), regenerasi ID session saat login, dan token CSRF otomatis.
- **Eksplorasi Katalog**: Pencarian dan filter produk berdasarkan kategori, kalkulasi rating bintang dinamis, serta sensor nama pembeli pada ulasan publik.
- **Keranjang Belanja**: Keranjang session interaktif dengan update kuantitas item dan proteksi token CSRF.
- **Checkout Transaksional**: Transaksi pesanan dengan verifikasi stok atomik (ACID) untuk mencegah race condition.
- **Pelacakan Status Pesanan**: Monitoring alur pesanan dari `dikemas`, `diproses`, `dikirim`, hingga `selesai`.
- **Ulasan & Penilaian**: Konfirmasi barang diterima dengan unggah foto ulasan dan rating bintang 1-5.

### Administrator
- **Dashboard Metrik**: Ringkasan total pendapatan, jumlah pengguna terdaftar, sisa stok produk, dan status pesanan berjalan.
- **Manajemen Produk (CRUD)**: Penambahan produk, edit harga, kontrol stok langsung, serta unggah gambar aset dengan validasi MIME type.
- **Manajemen Kategori**: Pengorganisasian kategori item (Accessories, Clothes, Gamepasses, Limiteds).
- **Manajemen Pesanan & Pelanggan**: Pembaruan status logistik pesanan pelanggan dan monitoring akun.

---

## Standar Keamanan & Hardening

1. **Prepared Statements (SQL Injection Immunity)**: Seluruh kueri dinamis pada autentikasi, transaksi, ulasan, dan panel admin menggunakan prepared statements dengan binding parameter terpisah via MySQLi.
2. **CSRF Protection (Cross-Site Request Forgery)**: Setiap form mutasi data (login, register, checkout, update status pesanan, ulasan, dan penghapusan data) dilindungi token session kriptografis 64-karakter yang divalidasi via `hash_equals()`.
3. **Output Sanitization (XSS Defense)**: Seluruh data masukan yang dirender ke antarmuka disanitasi menggunakan fungsi pembantu `escape($str)` (`htmlspecialchars` dengan bendera `ENT_QUOTES`).
4. **Proteksi Direktori Uploads**: Dilengkapi berkas `uploads/.htaccess` untuk menonaktifkan eksekusi skrip PHP di folder media.
5. **Konfigurasi Fleksibel (Environment Variables)**: Mendukung variabel lingkungan sistem (`DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`) dengan fallback otomatis ke konfigurasi lokal XAMPP.

---

## Kebutuhan Sistem

- Web Server: Apache (misal melalui XAMPP atau Laragon)
- PHP: Versi 8.0 atau lebih baru (kompatibel PHP 8.1, 8.2, 8.3)
- Database: MySQL atau MariaDB 5.7+
- Ekstensi PHP: `mysqli`, `pdo_sqlite` (untuk pengujian lokal mandiri)

---

## Panduan Instalasi

1. Salin atau clone repositori ini ke folder root web server lokal kamu:
   ```bash
   # Contoh pada direktori XAMPP di Windows:
   cd C:/xampp/htdocs
   git clone https://github.com/IlhamXkyo/roblox-store-task.git
   ```

2. Buat database baru di MySQL dengan nama `roblox_store`.

3. Impor skema database dari berkas SQL yang tersedia di repositori:
   ```bash
   mysql -u root -p roblox_store < roblox_store.sql
   ```

4. Konfigurasi kredensial database pada berkas `koneksi.php` atau atur variabel lingkungan:
   ```php
   $host = getenv('DB_HOST') ?: 'localhost';
   $user = getenv('DB_USER') ?: 'root';
   $pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
   $db   = getenv('DB_NAME') ?: 'roblox_store';
   ```

5. Buka browser dan akses alamat:
   ```text
   http://localhost/roblox-store-task
   ```

### Akun Demo Bawaan
- **Admin**: Username `admin`, Password `admin123`
- **Buyer**: Username `buyer`, Password `buyer123`

---

## Pengujian Otomatis (CI / Local)

Repositori dilengkapi rangkaian test suite otomatis mandiri di direktori `tests/test_store_logic.php` dengan 79 assertions yang mencakup:
- Sanitasi output XSS (`escape()` dan `e()`)
- Sensor nama pelanggan publik (`sensor_nama()`)
- Generator dan validator token CSRF (`verify_csrf_token()`)
- Validasi logika stok transaksional (`validasi_stok()`)
- Integritas skema database MySQL (`roblox_store.sql`) dan simulasi in-memory SQLite
- Keamanan hash autentikasi (`PASSWORD_DEFAULT`)
- Keberadaan token CSRF pada form autentikasi dan handler login/register
- Dukungan konfigurasi environment variable pada `koneksi.php`

Jalankan pengujian menggunakan CLI PHP:
```powershell
& "C:\xampp\php\php.exe" tests/test_store_logic.php
```

Atau di Linux/macOS/CI:
```bash
php tests/test_store_logic.php
```

---

## Lisensi

Didistribusikan di bawah lisensi [MIT](LICENSE).
