<?php
session_start();
require_once 'koneksi.php';
require_once 'functions.php';
cek_admin();

// Proses update status
if (isset($_GET['ubah_status']) && isset($_GET['id'])) {
    $id_pesanan = intval($_GET['id']);
    $status_baru = $_GET['ubah_status'];
    $allowed = ['dikemas','diproses','dikirim'];
    if (in_array($status_baru, $allowed)) {
        $conn->query("UPDATE pesanan SET status = '$status_baru' WHERE id_pesanan = $id_pesanan");
    }
    header("Location: admin_pesanan.php");
    exit;
}

$sql = "SELECT p.*, u.nama_lengkap, pr.nama_produk 
        FROM pesanan p 
        JOIN users u ON p.id_user = u.id_user 
        JOIN produk pr ON p.id_produk = pr.id_produk 
        ORDER BY p.tanggal_pesan DESC";
$pesanan = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - Admin</title>
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
        .btn-status { border-radius: 50px; padding: 3px 15px; font-size: 0.8rem; margin: 2px; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar">
            <h4 class="text-white text-center mb-4">🎮 Admin</h4>
            <a href="admin_dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="admin_kategori.php"><i class="bi bi-tags"></i> Kategori</a>
            <a href="admin_produk.php"><i class="bi bi-box"></i> Produk</a>
            <a href="admin_pelanggan.php"><i class="bi bi-people"></i> Pelanggan</a>
            <a href="admin_pesanan.php" class="active"><i class="bi bi-truck"></i> Pesanan</a>
            <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
        <div class="col-md-10 p-4">
            <h2 class="fw-bold">🚚 Kelola Pesanan</h2>
            <div class="card-table">
                <table class="table table-hover">
                    <thead>
                        <tr><th>ID</th><th>Pembeli</th><th>Produk</th><th>Jumlah</th><th>Total</th><th>Status</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $pesanan->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id_pesanan'] ?></td>
                            <td><?= $row['nama_lengkap'] ?></td>
                            <td><?= $row['nama_produk'] ?></td>
                            <td><?= $row['jumlah'] ?></td>
                            <td>Rp <?= number_format($row['total_harga'],0,',','.') ?></td>
                            <td><span class="badge bg-<?= match($row['status']) {'dikemas'=>'secondary','diproses'=>'primary','dikirim'=>'warning text-dark','selesai'=>'success'} ?>"><?= strtoupper($row['status']) ?></span></td>
                            <td>
                                <?php if ($row['status'] == 'dikemas'): ?>
                                    <a href="admin_pesanan.php?ubah_status=diproses&id=<?= $row['id_pesanan'] ?>" class="btn btn-primary btn-status">Proses</a>
                                <?php elseif ($row['status'] == 'diproses'): ?>
                                    <a href="admin_pesanan.php?ubah_status=dikirim&id=<?= $row['id_pesanan'] ?>" class="btn btn-warning btn-status">Kirim</a>
                                <?php elseif ($row['status'] == 'dikirim'): ?>
                                    <span class="text-muted">Menunggu konfirmasi user</span>
                                <?php else: ?>
                                    <span class="text-success">Selesai</span>
                                <?php endif; ?>
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