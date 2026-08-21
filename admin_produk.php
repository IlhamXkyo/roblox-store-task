<?php
session_start();
require_once 'koneksi.php';
require_once 'functions.php';
cek_admin();

$sql = "SELECT p.*, k.nama_kategori FROM produk p LEFT JOIN kategori k ON p.id_kategori = k.id_kategori ORDER BY p.id_produk DESC";
$produk = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Fredoka', sans-serif; background: #f0f8ff; }
        .sidebar { background: #1a1a2e; min-height: 100vh; padding: 20px; }
        .sidebar a { color: #fff; text-decoration: none; display: block; padding: 12px 15px; border-radius: 50px; margin-bottom: 8px; }
        .sidebar a:hover { background: #ffd700; color: #1a1a2e; }
        .sidebar a.active { background: #ffd700; color: #1a1a2e; }
        .card-table { border-radius: 25px; border: 2px solid #ffd700; background: #fff; padding: 20px; }
        .img-thumb-produk { max-width: 60px; max-height: 60px; border-radius: 15px; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar">
            <h4 class="text-white text-center mb-4">🎮 Admin</h4>
            <a href="admin_dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="admin_kategori.php"><i class="bi bi-tags"></i> Kategori</a>
            <a href="admin_produk.php" class="active"><i class="bi bi-box"></i> Produk</a>
            <a href="admin_pelanggan.php"><i class="bi bi-people"></i> Pelanggan</a>
            <a href="admin_pesanan.php"><i class="bi bi-truck"></i> Pesanan</a>
            <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
        <div class="col-md-10 p-4">
            <h2 class="fw-bold">📦 Kelola Produk</h2>
            <a href="produk_tambah.php" class="btn btn-success mb-3"><i class="bi bi-plus-circle"></i> Tambah Produk</a>
            <div class="card-table">
                <table class="table table-hover">
                    <thead>
                        <tr><th>ID</th><th>Gambar</th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $produk->fetch_assoc()): 
                            $gambar = (file_exists('uploads/produk/'.$row['gambar'])) ? 'uploads/produk/'.$row['gambar'] : 'assets/img/roblox_logo.png';
                        ?>
                        <tr>
                            <td><?= $row['id_produk'] ?></td>
                            <td><img src="<?= $gambar ?>" class="img-thumb-produk"></td>
                            <td><?= $row['nama_produk'] ?></td>
                            <td><?= $row['nama_kategori'] ?></td>
                            <td>Rp <?= number_format($row['harga'],0,',','.') ?></td>
                            <td><?= $row['stok'] ?></td>
                            <td>
                                <a href="produk_edit.php?id=<?= $row['id_produk'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="produk_hapus.php?id=<?= $row['id_produk'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>