<?php
session_start();
require_once 'koneksi.php';
require_once 'functions.php';
cek_login();
$id_user = $_SESSION['id_user'];

// Proses jika user klik "Barang Diterima" (ubah status jadi selesai)
if (isset($_GET['selesai'])) {
    $id_pesanan = intval($_GET['selesai']);
    $cek = $conn->query("SELECT status FROM pesanan WHERE id_pesanan = $id_pesanan AND id_user = $id_user");
    if ($cek->num_rows > 0) {
        $row = $cek->fetch_assoc();
        if ($row['status'] == 'dikirim') {
            $conn->query("UPDATE pesanan SET status = 'selesai' WHERE id_pesanan = $id_pesanan");
            echo "<script>alert('Pesanan telah selesai! Terima kasih.'); window.location.href='pesanan_saya.php';</script>";
        } else {
            echo "<script>alert('Pesanan belum dikirim, belum bisa diselesaikan.'); window.location.href='pesanan_saya.php';</script>";
        }
    }
    exit;
}

// Ambil pesanan user
$sql = "SELECT p.*, pr.nama_produk, pr.gambar FROM pesanan p 
        JOIN produk pr ON p.id_produk = pr.id_produk 
        WHERE p.id_user = $id_user 
        ORDER BY p.tanggal_pesan DESC";
$pesanan = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Roblox Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Fredoka', sans-serif; background: #f0f8ff; }
        .navbar-roblox { background: #1a1a2e; padding: 15px 0; }
        .navbar-roblox .navbar-brand { font-weight: 700; color: #ffd700; font-size: 1.8rem; }
        .navbar-roblox .nav-link { color: #fff !important; font-weight: 500; }
        .badge-status { font-size: 0.9rem; padding: 6px 15px; border-radius: 50px; }
        .btn-selesai { background: #28a745; border: none; color: #fff; border-radius: 50px; padding: 5px 20px; }
        .btn-selesai:hover { background: #1e7e34; color: #fff; }
        .btn-ulasan { background: #ffc107; border: none; color: #1a1a2e; border-radius: 50px; padding: 5px 20px; }
        .btn-ulasan:hover { background: #e0a800; color: #1a1a2e; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-roblox">
        <div class="container">
            <a class="navbar-brand" href="index.php">🎮 Roblox Store</a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-house"></i> Katalog</a></li>
                <li class="nav-item"><a class="nav-link" href="keranjang.php"><i class="bi bi-cart"></i> Keranjang</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="fw-bold">📦 Pesanan Saya</h2>
        <?php if ($pesanan->num_rows == 0): ?>
            <div class="alert alert-info mt-3">Belum ada pesanan. <a href="index.php">Mulai belanja</a></div>
        <?php else: ?>
            <div class="row">
                <?php while ($row = $pesanan->fetch_assoc()): 
                    $gambar = (file_exists('uploads/produk/'.$row['gambar'])) ? 'uploads/produk/'.$row['gambar'] : 'assets/img/roblox_logo.png';
                    $status = $row['status'];
                    $badge_color = match($status) {
                        'dikemas' => 'bg-secondary',
                        'diproses' => 'bg-primary',
                        'dikirim' => 'bg-warning text-dark',
                        'selesai' => 'bg-success'
                    };
                ?>
                    <div class="col-md-6 mb-4">
                        <div class="card p-3" style="border-radius:25px;border:2px solid #ffd700;">
                            <div class="row">
                                <div class="col-4">
                                    <img src="<?= $gambar ?>" class="img-fluid rounded" style="max-height:120px;">
                                </div>
                                <div class="col-8">
                                    <h6 class="fw-bold"><?= $row['nama_produk'] ?></h6>
                                    <p>Jumlah: <?= $row['jumlah'] ?> | Total: Rp <?= number_format($row['total_harga'],0,',','.') ?></p>
                                    <span class="badge badge-status <?= $badge_color ?>"><?= strtoupper($status) ?></span>
                                    <p class="text-muted small">Tanggal: <?= $row['tanggal_pesan'] ?></p>

                                    <?php if ($status == 'dikirim'): ?>
                                        <a href="pesanan_saya.php?selesai=<?= $row['id_pesanan'] ?>" class="btn btn-selesai btn-sm">✅ Barang Diterima</a>
                                    <?php endif; ?>

                                    <?php if ($status == 'selesai'): ?>
                                        <?php 
                                        // Cek apakah sudah ada penilaian untuk pesanan ini
                                        $cek_ulasan = $conn->query("SELECT id_penilaian FROM penilaian WHERE id_pesanan = ".$row['id_pesanan']);
                                        if ($cek_ulasan->num_rows == 0): 
                                        ?>
                                            <a href="penilaian.php?id_pesanan=<?= $row['id_pesanan'] ?>" class="btn btn-ulasan btn-sm">⭐ Beri Ulasan</a>
                                        <?php else: ?>
                                            <span class="badge bg-info">Sudah diulas</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>