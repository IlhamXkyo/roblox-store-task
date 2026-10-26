<?php
session_start();
require_once 'koneksi.php';
require_once 'functions.php';
cek_login();

$id_user = (int)$_SESSION['id_user'];
$id_pesanan = isset($_GET['id_pesanan']) ? (int)$_GET['id_pesanan'] : 0;
if ($id_pesanan <= 0) {
    header("Location: pesanan_saya.php");
    exit;
}

$stmt_cek = $conn->prepare("SELECT id_produk FROM pesanan WHERE id_pesanan = ? AND id_user = ? AND status = 'selesai'");
$stmt_cek->bind_param("ii", $id_pesanan, $id_user);
$stmt_cek->execute();
$res_cek = $stmt_cek->get_result();

if ($res_cek->num_rows === 0) {
    echo "<script>alert('Pesanan tidak valid atau belum selesai.'); window.location.href='pesanan_saya.php';</script>";
    exit;
}

$data = $res_cek->fetch_assoc();
$id_produk = (int)$data['id_produk'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        die("Validasi token CSRF gagal.");
    }

    $bintang = (int)($_POST['bintang'] ?? 5);
    if ($bintang < 1 || $bintang > 5) {
        $bintang = 5;
    }
    $komentar = trim($_POST['komentar'] ?? '');
    $foto_ulasan = null;

    if (isset($_FILES['foto_ulasan']) && $_FILES['foto_ulasan']['error'] === UPLOAD_ERR_OK) {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        $allowed_mime = [
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'webp' => 'image/webp'
        ];

        $tmp_file = $_FILES['foto_ulasan']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['foto_ulasan']['name'], PATHINFO_EXTENSION));

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $tmp_file);
        finfo_close($finfo);

        if (in_array($ext, $allowed_ext, true) && isset($allowed_mime[$ext]) && $mime === $allowed_mime[$ext]) {
            $target_dir = __DIR__ . "/uploads/ulasan/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            $nama_file = time() . "_" . bin2hex(random_bytes(8)) . "." . $ext;
            if (move_uploaded_file($tmp_file, $target_dir . $nama_file)) {
                $foto_ulasan = $nama_file;
            }
        }
    }

    $stmt_insert = $conn->prepare("INSERT INTO penilaian (id_pesanan, id_user, id_produk, bintang, komentar, foto_ulasan) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt_insert) {
        $stmt_insert->bind_param("iiiiss", $id_pesanan, $id_user, $id_produk, $bintang, $komentar, $foto_ulasan);
        if ($stmt_insert->execute()) {
            echo "<script>alert('Ulasan berhasil disimpan! Terima kasih.'); window.location.href='pesanan_saya.php';</script>";
            exit;
        }
    }

    echo "<script>alert('Gagal menyimpan ulasan.');</script>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beri Ulasan - Roblox Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Fredoka', sans-serif; background: #f0f8ff; }
        .card-ulasan { max-width: 600px; margin: 50px auto; border-radius: 30px; border: 3px solid #ffd700; padding: 30px; background: #fff; }
        .btn-ulasan-submit { background: #ff5e00; border: none; color: #fff; border-radius: 50px; padding: 10px 30px; font-weight: 600; }
        .btn-ulasan-submit:hover { background: #e05500; color: #fff; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card-ulasan">
            <h3 class="text-center fw-bold">⭐ Beri Ulasan</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                <div class="mb-3">
                    <label for="bintang" class="form-label">Bintang (1-5)</label>
                    <select name="bintang" id="bintang" class="form-select" required>
                        <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                        <option value="4">⭐⭐⭐⭐ (4)</option>
                        <option value="3">⭐⭐⭐ (3)</option>
                        <option value="2">⭐⭐ (2)</option>
                        <option value="1">⭐ (1)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="komentar" class="form-label">Komentar</label>
                    <textarea name="komentar" id="komentar" rows="4" class="form-control" placeholder="Tulis pengalamanmu..." required></textarea>
                </div>
                <div class="mb-3">
                    <label for="foto_ulasan" class="form-label">Foto (opsional)</label>
                    <input type="file" name="foto_ulasan" id="foto_ulasan" class="form-control" accept="image/jpeg,image/png,image/webp">
                </div>
                <button type="submit" class="btn btn-ulasan-submit w-100">Kirim Ulasan</button>
            </form>
        </div>
    </div>
</body>
</html>