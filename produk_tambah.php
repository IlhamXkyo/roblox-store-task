<?php
session_start();
require_once 'koneksi.php';
require_once 'functions.php';
cek_admin();

// Ambil daftar kategori untuk dropdown
$kategori = $conn->query("SELECT * FROM kategori");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_kategori = intval($_POST['id_kategori']);
    $nama_produk = trim($_POST['nama_produk']);
    $harga = intval($_POST['harga']);
    $stok = intval($_POST['stok']);
    $deskripsi = trim($_POST['deskripsi']);
    $gambar = 'roblox_logo.png'; // default

    // Upload gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "uploads/produk/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $nama_file = time() . "_" . rand(1000,9999) . "." . $ext;
        $target_file = $target_dir . $nama_file;
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
            $gambar = $nama_file;
        }
    }

    $sql = "INSERT INTO produk (id_kategori, nama_produk, harga, stok, gambar, deskripsi) 
            VALUES ($id_kategori, '$nama_produk', $harga, $stok, '$gambar', '$deskripsi')";
    if ($conn->query($sql)) {
        header("Location: admin_produk.php");
        exit;
    } else {
        echo "Gagal: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Fredoka', sans-serif; background: #f0f8ff; }
        .card-form { max-width: 600px; margin: 50px auto; border-radius: 30px; border: 3px solid #ffd700; background: #fff; padding: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card-form">
            <h3 class="fw-bold text-center">Tambah Produk</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="nama_produk" class="form-label">Nama Produk</label>
                    <input type="text" name="nama_produk" id="nama_produk" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="id_kategori" class="form-label">Kategori</label>
                    <select name="id_kategori" id="id_kategori" class="form-select" required>
                        <option value="">Pilih Kategori</option>
                        <?php while ($k = $kategori->fetch_assoc()): ?>
                            <option value="<?= $k['id_kategori'] ?>"><?= $k['nama_kategori'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="harga" class="form-label">Harga</label>
                    <input type="number" name="harga" id="harga" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="stok" class="form-label">Stok</label>
                    <input type="number" name="stok" id="stok" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="3" class="form-control"></textarea>
                </div>
                <div class="mb-3">
                    <label for="gambar" class="form-label">Gambar (opsional)</label>
                    <input type="file" name="gambar" id="gambar" class="form-control" accept="image/*">
                </div>
                <button type="submit" class="btn btn-success w-100">Simpan</button>
                <a href="admin_produk.php" class="btn btn-secondary w-100 mt-2">Batal</a>
            </form>
        </div>
    </div>
</body>
</html>