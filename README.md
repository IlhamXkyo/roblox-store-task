# 🎮 Roblox Store  
### Sistem Informasi E-Commerce Item Roblox Berbasis Web

![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

> **📌 Project Ujian Akhir Semester (UAS) – Pemrograman Web**  
> Tema: *Rancang Bangun Sistem Informasi Roblox Item Store (B2C) Berbasis Web*

---

## 📖 Tentang Project

**Roblox Store** adalah aplikasi web *e-commerce* yang dirancang khusus untuk menjual item-item dalam game **Roblox**. Dibangun dengan **PHP Native** dan **MySQL** tanpa framework, project ini mengusung tema *cartoonish/gaming style* dengan warna cerah khas Roblox.

Sistem mendukung **2 role pengguna**:
- 🛡️ **Admin** – Mengelola kategori, produk, pelanggan, dan status pesanan.
- 🎮 **Buyer** – Membeli item, mengelola keranjang, melacak pesanan, dan memberi ulasan.

---

## ✨ Fitur Unggulan

### 👤 Untuk User (Buyer)
- ✅ Registrasi & Login akun
- ✅ Katalog produk dengan tampilan gaming
- ✅ Statistik produk: **Total Terjual** & **Rata-rata Bintang** ⭐
- ✅ Keranjang belanja berbasis session
- ✅ Proses checkout dengan konfirmasi
- ✅ Tracking status pesanan:  
  `📦 Dikemas` → `⚙️ Diproses` → `🚚 Dikirim` → `✅ Selesai`
- ✅ Tombol **"Barang Diterima"** untuk menyelesaikan pesanan
- ✅ Form ulasan produk (bintang 1-5, komentar, upload foto)
- ✅ Sensor otomatis nama pengguna pada ulasan (contoh: `BudiSantoso` → `Bud******so`)

### 🛡️ Untuk Admin
- ✅ Dashboard statistik real-time
- ✅ **CRUD Kategori** (Tambah, Edit, Hapus)
- ✅ **CRUD Produk** (Tambah, Edit, Hapus) + Upload gambar
- ✅ Manajemen pelanggan (Lihat & Hapus akun buyer)
- ✅ Kelola status pesanan (`Dikemas` → `Diproses` → `Dikirim`)

---

## 🛠️ Teknologi yang Digunakan

| Komponen | Teknologi |
| :--- | :--- |
| **Frontend** | HTML5, CSS3, Bootstrap 5 (CDN), JavaScript Native |
| **Backend** | PHP Native (tanpa framework) |
| **Database** | MySQL (5 tabel terintegrasi) |
| **Web Server** | XAMPP / Apache / Laragon |
| **Library Tambahan** | SweetAlert2 (opsional), Google Fonts (Fredoka) |

---

## 🗄️ Struktur Database

| Tabel | Keterangan |
| :--- | :--- |
| `users` | Data akun pengguna (admin & buyer) |
| `kategori` | Kategori produk Roblox |
| `produk` | Data item (nama, harga, stok, gambar, deskripsi) |
| `pesanan` | Riwayat transaksi pembelian |
| `penilaian` | Ulasan dan rating dari pembeli |

---

## 🚀 Cara Instalasi & Menjalankan

1. **Clone / Download** repository ini ke folder `htdocs` (XAMPP) atau `www` (Laragon).  
   ```bash
   git clone https://github.com/username/roblox-store.git
