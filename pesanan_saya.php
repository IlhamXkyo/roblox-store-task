<?php
session_start();
require_once 'koneksi.php';
require_once 'functions.php';
cek_login();
$id_user = (int)$_SESSION['id_user'];

if (isset($_GET['selesai'])) {
    $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        die("Validasi token CSRF gagal.");
    }

    $id_pesanan = (int)$_GET['selesai'];
    $stmt_cek = $conn->prepare("SELECT status FROM pesanan WHERE id_pesanan = ? AND id_user = ?");
    $stmt_cek->bind_param("ii", $id_pesanan, $id_user);
    $stmt_cek->execute();
    $res_cek = $stmt_cek->get_result();

    if ($res_cek->num_rows > 0) {
        $row = $res_cek->fetch_assoc();
        if ($row['status'] === 'dikirim') {
            $stmt_update = $conn->prepare("UPDATE pesanan SET status = 'selesai' WHERE id_pesanan = ?");
            $stmt_update->bind_param("i", $id_pesanan);
            $stmt_update->execute();
            echo "<script>alert('Pesanan telah selesai! Terima kasih.'); window.location.href='pesanan_saya.php';</script>";
        } else {
            echo "<script>alert('Pesanan belum dikirim, belum bisa diselesaikan.'); window.location.href='pesanan_saya.php';</script>";
        }
    }
    exit;
}

$stmt_pesanan = $conn->prepare("SELECT p.*, pr.nama_produk, pr.gambar 
        FROM pesanan p 
        JOIN produk pr ON p.id_produk = pr.id_produk 
        WHERE p.id_user = ? 
        ORDER BY p.tanggal_pesan DESC");
$stmt_pesanan->bind_param("i", $id_user);
$stmt_pesanan->execute();
$pesanan = $stmt_pesanan->get_result();

$stmt_ulasan = $conn->prepare("SELECT id_penilaian FROM penilaian WHERE id_pesanan = ?");
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
        <?php if ($pesanan->num_rows === 0): ?>
            <div class="alert alert-info mt-3">Belum ada pesanan. <a href="index.php">Mulai belanja</a></div>
        <?php else: ?>
            <div class="row">
                <?php while ($row = $pesanan->fetch_assoc()): 
                    $raw_gambar = basename($row['gambar'] ?? '');
                    $gambar = (!empty($raw_gambar) && file_exists('uploads/produk/' . $raw_gambar)) ? 'uploads/produk/' . $raw_gambar : 'assets/img/roblox_logo.png';
                    $status = $row['status'];
                    $badge_color = match($status) {
                        'dikemas' => 'bg-secondary',
                        'diproses' => 'bg-primary',
                        'dikirim' => 'bg-warning text-dark',
                        'selesai' => 'bg-success',
                        default => 'bg-secondary'
                    };
                ?>
                    <div class="col-md-6 mb-4">
                        <div class="card p-3" style="border-radius:25px;border:2px solid #ffd700;">
                            <div class="row">
                                <div class="col-4">
                                    <img src="<?= htmlspecialchars($gambar, ENT_QUOTES, 'UTF-8') ?>" class="img-fluid rounded" style="max-height:120px;" alt="Produk">
                                </div>
                                <div class="col-8">
                                    <h6 class="fw-bold"><?= htmlspecialchars($row['nama_produk'], ENT_QUOTES, 'UTF-8') ?></h6>
                                    <p>Jumlah: <?= (int)$row['jumlah'] ?> | Total: Rp <?= number_format($row['total_harga'],0,',','.') ?></p>
                                    <span class="badge badge-status <?= $badge_color ?>"><?= strtoupper($status) ?></span>
                                    <p class="text-muted small">Tanggal: <?= htmlspecialchars($row['tanggal_pesan'], ENT_QUOTES, 'UTF-8') ?></p>

                                    <?php if ($status === 'dikirim'): ?>
                                        <a href="pesanan_saya.php?selesai=<?= (int)$row['id_pesanan'] ?>&csrf_token=<?= generate_csrf_token() ?>" class="btn btn-selesai btn-sm">✅ Barang Diterima</a>
                                    <?php endif; ?>

                                    <?php if ($status === 'selesai'): ?>
                                        <?php 
                                        $id_pesanan_row = (int)$row['id_pesanan'];
                                        $stmt_ulasan->bind_param("i", $id_pesanan_row);
                                        $stmt_ulasan->execute();
                                        $cek_ulasan = $stmt_ulasan->get_result();
                                        if ($cek_ulasan->num_rows === 0): 
                                        ?>
                                            <a href="penilaian.php?id_pesanan=<?= $id_pesanan_row ?>" class="btn btn-ulasan btn-sm">⭐ Beri Ulasan</a>
                                        <?php else: ?>
                                            <span class="text-success small"><i class="bi bi-check-circle"></i> Sudah diulas</span>
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