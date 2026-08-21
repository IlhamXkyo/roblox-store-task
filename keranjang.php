<?php
session_start();
require_once 'koneksi.php';
require_once 'functions.php';
cek_login();

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// Tambah item ke keranjang
if (isset($_GET['tambah'])) {
    $id = intval($_GET['tambah']);
    if (isset($_SESSION['keranjang'][$id])) {
        $_SESSION['keranjang'][$id]++;
    } else {
        $_SESSION['keranjang'][$id] = 1;
    }
    header("Location: keranjang.php");
    exit;
}

// Hapus item dari keranjang
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    unset($_SESSION['keranjang'][$id]);
    header("Location: keranjang.php");
    exit;
}

// Update jumlah
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    foreach ($_POST['qty'] as $id => $qty) {
        if ($qty <= 0) {
            unset($_SESSION['keranjang'][$id]);
        } else {
            $_SESSION['keranjang'][$id] = $qty;
        }
    }
    header("Location: keranjang.php");
    exit;
}

// Ambil data produk dari keranjang
$items = [];
$grand_total = 0;
if (!empty($_SESSION['keranjang'])) {
    $ids = array_keys($_SESSION['keranjang']);
    $ids_str = implode(',', $ids);
    $sql = "SELECT * FROM produk WHERE id_produk IN ($ids_str)";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $id = $row['id_produk'];
        $qty = $_SESSION['keranjang'][$id];
        $subtotal = $row['harga'] * $qty;
        $grand_total += $subtotal;
        $items[] = [
            'produk' => $row,
            'qty' => $qty,
            'subtotal' => $subtotal
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang - Roblox Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Fredoka', sans-serif; background: #f0f8ff; }
        .navbar-roblox { background: #1a1a2e; padding: 15px 0; }
        .navbar-roblox .navbar-brand { font-weight: 700; color: #ffd700; font-size: 1.8rem; }
        .navbar-roblox .nav-link { color: #fff !important; font-weight: 500; }
        .card-keranjang { border-radius: 25px; border: 2px solid #ffd700; }
        .btn-checkout { background: #ff5e00; border: none; color: #fff; font-weight: 600; border-radius: 50px; padding: 12px 30px; }
        .btn-checkout:hover { background: #e05500; color: #fff; }
        .btn-hapus { background: #dc3545; border: none; border-radius: 50px; color: #fff; padding: 5px 15px; }
        .btn-hapus:hover { background: #b02a37; color: #fff; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-roblox">
        <div class="container">
            <a class="navbar-brand" href="index.php">🎮 Roblox Store</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-house"></i> Katalog</a></li>
                    <li class="nav-item"><a class="nav-link" href="pesanan_saya.php"><i class="bi bi-box-seam"></i> Pesanan Saya</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="fw-bold">🛒 Keranjang Belanja</h2>
        <?php if (empty($items)): ?>
            <div class="alert alert-info mt-4">Keranjang kosong. <a href="index.php">Belanja sekarang</a></div>
        <?php else: ?>
            <form method="POST">
                <div class="card card-keranjang p-3">
                    <?php foreach ($items as $item): ?>
                        <div class="row align-items-center border-bottom py-3">
                            <div class="col-md-2">
                                <img src="<?= (file_exists('uploads/produk/'.$item['produk']['gambar'])) ? 'uploads/produk/'.$item['produk']['gambar'] : 'assets/img/roblox_logo.png' ?>" 
                                     class="img-fluid rounded" style="max-height:80px;">
                            </div>
                            <div class="col-md-4">
                                <h6 class="fw-bold"><?= $item['produk']['nama_produk'] ?></h6>
                                <span>Rp <?= number_format($item['produk']['harga'],0,',','.') ?></span>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="qty[<?= $item['produk']['id_produk'] ?>]" value="<?= $item['qty'] ?>" min="1" class="form-control" style="width:80px;">
                            </div>
                            <div class="col-md-2">
                                <strong>Rp <?= number_format($item['subtotal'],0,',','.') ?></strong>
                            </div>
                            <div class="col-md-2">
                                <a href="keranjang.php?hapus=<?= $item['produk']['id_produk'] ?>" class="btn btn-hapus btn-sm">Hapus</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <button type="submit" name="update" class="btn btn-warning">Update Keranjang</button>
                        </div>
                        <div class="col-md-6 text-end">
                            <h5>Total: <span class="text-danger">Rp <?= number_format($grand_total,0,',','.') ?></span></h5>
                            <a href="proses_beli.php?checkout=1" class="btn btn-checkout">Checkout Sekarang</a>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>