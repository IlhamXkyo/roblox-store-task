<?php
session_start();
require_once 'koneksi.php';
require_once 'functions.php';
cek_login();

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

if (isset($_GET['tambah'])) {
    $id = (int)$_GET['tambah'];
    if ($id > 0) {
        if (isset($_SESSION['keranjang'][$id])) {
            $_SESSION['keranjang'][$id]++;
        } else {
            $_SESSION['keranjang'][$id] = 1;
        }
    }
    header("Location: keranjang.php");
    exit;
}

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    unset($_SESSION['keranjang'][$id]);
    header("Location: keranjang.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'], $_POST['qty']) && is_array($_POST['qty'])) {
    foreach ($_POST['qty'] as $id => $qty) {
        $id = (int)$id;
        $qty = (int)$qty;
        if ($qty <= 0) {
            unset($_SESSION['keranjang'][$id]);
        } else {
            $_SESSION['keranjang'][$id] = $qty;
        }
    }
    header("Location: keranjang.php");
    exit;
}

$items = [];
$grand_total = 0;
if (!empty($_SESSION['keranjang'])) {
    $ids = array_map('intval', array_keys($_SESSION['keranjang']));
    $ids = array_filter($ids, fn($val) => $val > 0);
    if (!empty($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $stmt = $conn->prepare("SELECT * FROM produk WHERE id_produk IN ($placeholders)");
        if ($stmt) {
            $stmt->bind_param($types, ...$ids);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $id = (int)$row['id_produk'];
                $qty = (int)($_SESSION['keranjang'][$id] ?? 0);
                $subtotal = $row['harga'] * $qty;
                $grand_total += $subtotal;
                $items[] = [
                    'produk' => $row,
                    'qty' => $qty,
                    'subtotal' => $subtotal
                ];
            }
        }
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
                    <?php foreach ($items as $item): 
                        $raw_img = basename($item['produk']['gambar'] ?? '');
                        $img_path = (!empty($raw_img) && file_exists('uploads/produk/' . $raw_img)) ? 'uploads/produk/' . $raw_img : 'assets/img/roblox_logo.png';
                    ?>
                        <div class="row align-items-center border-bottom py-3">
                            <div class="col-md-2">
                                <img src="<?= htmlspecialchars($img_path, ENT_QUOTES, 'UTF-8') ?>" 
                                     class="img-fluid rounded" style="max-height:80px;" alt="Produk">
                            </div>
                            <div class="col-md-4">
                                <h6 class="fw-bold"><?= htmlspecialchars($item['produk']['nama_produk'], ENT_QUOTES, 'UTF-8') ?></h6>
                                <span>Rp <?= number_format($item['produk']['harga'],0,',','.') ?></span>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="qty[<?= (int)$item['produk']['id_produk'] ?>]" value="<?= (int)$item['qty'] ?>" min="1" class="form-control" style="width:80px;">
                            </div>
                            <div class="col-md-2">
                                <strong>Rp <?= number_format($item['subtotal'],0,',','.') ?></strong>
                            </div>
                            <div class="col-md-2">
                                <a href="keranjang.php?hapus=<?= (int)$item['produk']['id_produk'] ?>" class="btn btn-hapus btn-sm">Hapus</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <button type="submit" name="update" value="1" class="btn btn-warning">Update Keranjang</button>
                        </div>
                        <div class="col-md-6 text-end">
                            <h5 class="mb-3">Total: <span class="text-danger">Rp <?= number_format($grand_total,0,',','.') ?></span></h5>
                        </div>
                    </div>
                </div>
            </form>
            <div class="text-end mt-3">
                <form action="proses_beli.php" method="POST" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <input type="hidden" name="checkout" value="1">
                    <button type="submit" class="btn btn-checkout">Checkout Sekarang</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>