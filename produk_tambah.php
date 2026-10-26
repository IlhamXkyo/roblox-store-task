<?php
session_start();
require_once 'koneksi.php';
require_once 'functions.php';
cek_admin();

$kategori = $conn->query("SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori ASC");

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        die("Validasi token CSRF gagal.");
    }

    $id_kategori = (int)($_POST['id_kategori'] ?? 0);
    $nama_produk = trim($_POST['nama_produk'] ?? '');
    $harga = (int)($_POST['harga'] ?? 0);
    $stok = (int)($_POST['stok'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $gambar = 'roblox_logo.png';

    if ($id_kategori <= 0 || $nama_produk === '' || $harga < 0 || $stok < 0) {
        $error = "Data produk tidak lengkap atau tidak valid.";
    } else {
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
            $allowed_mime = [
                'jpg'  => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png'  => 'image/png',
                'webp' => 'image/webp'
            ];

            $file_tmp = $_FILES['gambar']['tmp_name'];
            $file_name = $_FILES['gambar']['name'];
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file_tmp);
            finfo_close($finfo);

            if (!in_array($ext, $allowed_ext, true) || !isset($allowed_mime[$ext]) || $mime !== $allowed_mime[$ext]) {
                $error = "Format berkas tidak diizinkan. Gunakan JPG, JPEG, PNG, atau WEBP yang valid.";
            } else {
                $target_dir = __DIR__ . '/uploads/produk/';
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                $nama_file = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
                $target_file = $target_dir . $nama_file;

                if (move_uploaded_file($file_tmp, $target_file)) {
                    $gambar = $nama_file;
                } else {
                    $error = "Gagal mengunggah berkas gambar.";
                }
            }
        }

        if (empty($error)) {
            $stmt = $conn->prepare("INSERT INTO produk (id_kategori, nama_produk, harga, stok, gambar, deskripsi) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("isiiss", $id_kategori, $nama_produk, $harga, $stok, $gambar, $deskripsi);
                if ($stmt->execute()) {
                    header("Location: admin_produk.php");
                    exit;
                }
            }
            $error = "Gagal menyimpan data produk.";
        }
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
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= escape($error) ?></div>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                <div class="mb-3">
                    <label for="nama_produk" class="form-label">Nama Produk</label>
                    <input type="text" name="nama_produk" id="nama_produk" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="id_kategori" class="form-label">Kategori</label>
                    <select name="id_kategori" id="id_kategori" class="form-select" required>
                        <option value="">Pilih Kategori</option>
                        <?php while ($k = $kategori->fetch_assoc()): ?>
                            <option value="<?= (int)$k['id_kategori'] ?>"><?= escape($k['nama_kategori']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="harga" class="form-label">Harga</label>
                    <input type="number" name="harga" id="harga" min="0" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="stok" class="form-label">Stok</label>
                    <input type="number" name="stok" id="stok" min="0" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="3" class="form-control"></textarea>
                </div>
                <div class="mb-3">
                    <label for="gambar" class="form-label">Gambar (opsional, format JPG/PNG/WEBP)</label>
                    <input type="file" name="gambar" id="gambar" class="form-control" accept="image/jpeg,image/png,image/webp">
                </div>
                <button type="submit" class="btn btn-success w-100">Simpan</button>
                <a href="admin_produk.php" class="btn btn-secondary w-100 mt-2">Batal</a>
            </form>
        </div>
    </div>
</body>
</html>