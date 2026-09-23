# Roblox Store

Aplikasi web toko e-commerce berbasis PHP Native dan MySQL untuk transaksi item virtual game Roblox.

Proyek ini dibangun tanpa framework eksternal untuk mendemonstrasikan implementasi autentikasi peran ganda, manajemen inventaris item, dan alur pemrosesan pesanan berbasis session.

## Fitur

### Pengguna (Pembeli)
- Registrasi akun, verifikasi login, dan manajemen profil.
- Eksplorasi katalog item dengan filter kategori dan pencarian nama.
- Detail produk dengan ulasan bintang dan riwayat jumlah item terjual.
- Keranjang belanja berbasis session PHP.
- Formulir checkout transaksi dan riwayat pelacakan status pesanan (Dikemas, Diproses, Dikirim, Selesai).
- Tombol konfirmasi penerimaan barang untuk menyelesaikan transaksi.

### Administrator
- Dashboard ringkasan metrik penjualan, total pendapatan, dan jumlah pesanan baru.
- Manajemen produk: tambah produk, edit detail harga, kelola stok, dan unggah gambar aset.
- Manajemen kategori item.
- Pembaruan status pesanan pelanggan dan monitoring daftar akun pengguna.

## Kebutuhan Sistem

- Web Server: Apache (misal melalui XAMPP atau Laragon)
- PHP versi 7.4 atau lebih baru
- Database: MySQL atau MariaDB 5.7+

## Panduan Instalasi

1. Salin atau clone repositori ini ke folder root web server lokal kamu:
   ```bash
   # Contoh pada direktori XAMPP di Windows:
   cd C:/xampp/htdocs
   git clone https://github.com/IlhamXkyo/roblox-store-task.git
   ```

2. Buat database baru di MySQL dengan nama `roblox_store`.

3. Impor skema database dari berkas SQL yang tersedia:
   ```bash
   mysql -u root -p roblox_store < database.sql
   ```

4. Konfigurasi kredensial database pada berkas koneksi (misal `config/database.php` atau `koneksi.php`):
   ```php
   $host = "localhost";
   $user = "root";
   $pass = "";
   $db   = "roblox_store";
   ```

5. Buka browser dan akses alamat:
   ```text
   http://localhost/roblox-store-task
   ```

## Akun Demo Bawaan

- **Admin**: Username `admin`, Password `admin123`
- **Buyer**: Username `buyer`, Password `buyer123`

## Lisensi

Didistribusikan di bawah lisensi MIT.
