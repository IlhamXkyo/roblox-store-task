<?php
session_start();
require_once 'koneksi.php';
require_once 'functions.php';
cek_login();

$id_user = $_SESSION['id_user'];
$id_pesanan = isset($_GET['id_pesanan']) ? intval($_GET['id_pesanan']) : 0;
if ($id_pesanan == 0) {
    header("Location: pesanan_saya.php");
    exit;
}

// Cek apakah pesanan milik user dan status selesai
$cek = $conn->query("SELECT id_produk FROM pesanan WHERE id_pesanan = $id_pesanan AND id_user = $id_user AND status = 'selesai'");
if ($cek->num_rows == 0) {
    echo "<script>alert('Pesanan tidak valid atau belum selesai.'); window.location.href='pesanan_saya.php';</script>";
    exit;
}
$data = $cek->fetch_assoc();
$id_produk = $data['id_produk'];

// Proses simpan ulasan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bintang = intval($_POST['bintang']);
    $komentar = trim($_POST['komentar']);
    $foto_ulasan = null;

    // Upload foto jika ada
    if (isset($_FILES['foto_ulasan']) && $_FILES['foto_ulasan']['error'] == 0) {
        $target_dir = "uploads/ulasan/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $ext = pathinfo($_FILES['foto_ulasan']['name'], PATHINFO_EXTENSION);
        $nama_file = time() . "_" . rand(1000,9999) . "." . $ext;
        $target_file = $target_dir . $nama_file;
        if (move_uploaded_file($_FILES['foto_ulasan']['tmp_name'], $target_file)) {
            $foto_ulasan = $nama_file;
        }
    }

    $sql = "INSERT INTO penilaian (id_pesanan, id_user, id_produk, bintang, komentar, foto_ulasan) 
            VALUES ($id_pesanan, $id_user, $id_produk, $bintang, '$komentar', '$foto_ulasan')";
    if ($conn->query($sql)) {
        echo "<script>alert('Ulasan berhasil disimpan! Terima kasih.'); window.location.href='pesanan_saya.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan ulasan: " . $conn->error . "');</script>";
    }
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
                    <input type="file" name="foto_ulasan" id="foto_ulasan" class="form-control" accept="image/*">
                </div>
                <button type="submit" class="btn btn-ulasan-submit w-100">Kirim Ulasan</button>
            </form>
        </div>
    </div>
</body>
</html>