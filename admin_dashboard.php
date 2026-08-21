<?php
session_start();
require_once 'koneksi.php';
require_once 'functions.php';
cek_admin();

// Statistik
$total_user = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='buyer'")->fetch_assoc()['total'];
$total_produk = $conn->query("SELECT COUNT(*) as total FROM produk")->fetch_assoc()['total'];
$total_pesanan = $conn->query("SELECT COUNT(*) as total FROM pesanan")->fetch_assoc()['total'];
$total_penjualan = $conn->query("SELECT SUM(total_harga) as total FROM pesanan WHERE status='selesai'")->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Roblox Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Fredoka', sans-serif; background: #f0f8ff; }
        .sidebar { background: #1a1a2e; min-height: 100vh; padding: 20px; }
        .sidebar a { color: #fff; text-decoration: none; display: block; padding: 12px 15px; border-radius: 50px; margin-bottom: 8px; transition: 0.3s; }
        .sidebar a:hover { background: #ffd700; color: #1a1a2e; }
        .sidebar a.active { background: #ffd700; color: #1a1a2e; }
        .card-dashboard { border-radius: 25px; border: 3px solid #ffd700; background: #fff; padding: 20px; }
        .stat-number { font-size: 2.5rem; font-weight: 700; color: #4a90d9; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <h4 class="text-white text-center mb-4">🎮 Admin</h4>
                <a href="admin_dashboard.php" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
                <a href="admin_kategori.php"><i class="bi bi-tags"></i> Kategori</a>
                <a href="admin_produk.php"><i class="bi bi-box"></i> Produk</a>
                <a href="admin_pelanggan.php"><i class="bi bi-people"></i> Pelanggan</a>
                <a href="admin_pesanan.php"><i class="bi bi-truck"></i> Pesanan</a>
                <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>

            <!-- Content -->
            <div class="col-md-10 p-4">
                <h2 class="fw-bold">📊 Dashboard</h2>
                <div class="row mt-4">
                    <div class="col-md-3 mb-3">
                        <div class="card-dashboard text-center">
                            <h5>Total User</h5>
                            <div class="stat-number"><?= $total_user ?></div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card-dashboard text-center">
                            <h5>Total Produk</h5>
                            <div class="stat-number"><?= $total_produk ?></div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card-dashboard text-center">
                            <h5>Total Pesanan</h5>
                            <div class="stat-number"><?= $total_pesanan ?></div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card-dashboard text-center">
                            <h5>Penjualan (Rp)</h5>
                            <div class="stat-number"><?= number_format($total_penjualan,0,',','.') ?></div>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-muted">Selamat datang, Admin <?= $_SESSION['nama_lengkap'] ?>!</p>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>