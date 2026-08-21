<?php
session_start();
require_once 'koneksi.php';
require_once 'functions.php';
cek_admin();

$sql = "SELECT * FROM kategori ORDER BY id_kategori DESC";
$kategori = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori - Admin</title>
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
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar">
            <h4 class="text-white text-center mb-4">🎮 Admin</h4>
            <a href="admin_dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="admin_kategori.php" class="active"><i class="bi bi-tags"></i> Kategori</a>
            <a href="admin_produk.php"><i class="bi bi-box"></i> Produk</a>
            <a href="admin_pelanggan.php"><i class="bi bi-people"></i> Pelanggan</a>
            <a href="admin_pesanan.php"><i class="bi bi-truck"></i> Pesanan</a>
            <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
        <div class="col-md-10 p-4">
            <h2 class="fw-bold">🏷️ Kelola Kategori</h2>
            <a href="kategori_tambah.php" class="btn btn-success mb-3"><i class="bi bi-plus-circle"></i> Tambah Kategori</a>
            <div class="card-table">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Kategori</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $kategori->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id_kategori'] ?></td>
                            <td><?= $row['nama_kategori'] ?></td>
                            <td>
                                <a href="kategori_edit.php?id=<?= $row['id_kategori'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="kategori_hapus.php?id=<?= $row['id_kategori'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</a>
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