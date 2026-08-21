<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}
require_once 'koneksi.php';
require_once 'functions.php';

// Ambil semua produk beserta kategori
$sql = "SELECT p.*, k.nama_kategori FROM produk p 
        LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
        ORDER BY p.id_produk DESC";
$produk = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roblox Store - Katalog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Fredoka', sans-serif; background: #f0f8ff; }
        .navbar-roblox { background: #1a1a2e; padding: 15px 0; }
        .navbar-roblox .navbar-brand { font-weight: 700; color: #ffd700; font-size: 1.8rem; }
        .navbar-roblox .nav-link { color: #fff !important; font-weight: 500; }
        .card-produk {
            border-radius: 25px;
            border: 3px solid #ffd700;
            transition: 0.3s;
            background: #fff;
            height: 100%;
        }
        .card-produk:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 28px rgba(0,0,0,0.15);
        }
        .card-produk img {
            border-top-left-radius: 23px;
            border-top-right-radius: 23px;
            height: 200px;
            object-fit: cover;
            background: #eee;
        }
        .btn-beli {
            background: #ff5e00;
            color: #fff;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            padding: 8px 18px;
        }
        .btn-beli:hover { background: #e05500; color: #fff; }
        .btn-keranjang {
            background: #4a90d9;
            color: #fff;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            padding: 8px 18px;
        }
        .btn-keranjang:hover { background: #3a7bc8; color: #fff; }
        .badge-stok { background: #ffd700; color: #1a1a2e; font-weight: 600; }
        .harga { color: #ff5e00; font-weight: 700; font-size: 1.2rem; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-roblox">
        <div class="container">
            <a class="navbar-brand" href="index.php">🎮 Roblox Store</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="keranjang.php"><i class="bi bi-cart"></i> Keranjang</a></li>
                    <li class="nav-item"><a class="nav-link" href="pesanan_saya.php"><i class="bi bi-box-seam"></i> Pesanan Saya</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="text-center mb-4">
            <h1 class="display-5 fw-bold" style="color: #1a1a2e;">🔥 Item Roblox Terbaru</h1>
            <p class="text-muted">Temukan item keren untuk karakter Roblox-mu!</p>
        </div>

        <div class="row g-4">
            <?php while ($row = $produk->fetch_assoc()): 
                $rata = rata_bintang($conn, $row['id_produk']);
                $terjual = total_terjual($conn, $row['id_produk']);
                $gambar = (!empty($row['gambar']) && file_exists("uploads/produk/".$row['gambar'])) 
                          ? "uploads/produk/".$row['gambar'] 
                          : "assets/img/roblox_logo.png";
            ?>
                <div class="col-md-4 col-lg-3">
                    <div class="card card-produk">
                        <img src="<?= $gambar ?>" class="card-img-top" alt="<?= $row['nama_produk'] ?>">
                        <div class="card-body">
                            <h5 class="card-title fw-bold"><?= $row['nama_produk'] ?></h5>
                            <p class="card-text text-muted small"><?= $row['nama_kategori'] ?></p>
                            <p class="harga">Rp <?= number_format($row['harga'], 0, ',', '.') ?></p>
                            <p class="mb-2">
                                <span class="badge badge-stok">Stok <?= $row['stok'] ?></span>
                                <span class="badge bg-info text-dark">⭐ <?= $rata ?> (<?= $terjual ?> terjual)</span>
                            </p>
                            <div class="d-flex gap-2">
                                <a href="proses_beli.php?id=<?= $row['id_produk'] ?>" class="btn btn-beli btn-sm flex-fill">Beli Langsung</a>
                                <a href="keranjang.php?tambah=<?= $row['id_produk'] ?>" class="btn btn-keranjang btn-sm flex-fill">+ Keranjang</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>