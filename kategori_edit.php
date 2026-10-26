<?php
session_start();
require_once 'koneksi.php';
require_once 'functions.php';
cek_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt_get = $conn->prepare("SELECT id_kategori, nama_kategori FROM kategori WHERE id_kategori = ?");
$stmt_get->bind_param("i", $id);
$stmt_get->execute();
$data = $stmt_get->get_result()->fetch_assoc();

if (!$data) {
    header("Location: admin_kategori.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        die("Validasi token CSRF gagal.");
    }

    $nama = trim($_POST['nama_kategori'] ?? '');
    if ($nama !== '') {
        $stmt_update = $conn->prepare("UPDATE kategori SET nama_kategori = ? WHERE id_kategori = ?");
        $stmt_update->bind_param("si", $nama, $id);
        $stmt_update->execute();
        header("Location: admin_kategori.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Fredoka', sans-serif; background: #f0f8ff; }
        .card-form { max-width: 500px; margin: 80px auto; border-radius: 30px; border: 3px solid #ffd700; background: #fff; padding: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card-form">
            <h3 class="fw-bold text-center">Edit Kategori</h3>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                <div class="mb-3">
                    <label for="nama_kategori" class="form-label">Nama Kategori</label>
                    <input type="text" name="nama_kategori" id="nama_kategori" class="form-control" value="<?= escape($data['nama_kategori']) ?>" required>
                </div>
                <button type="submit" class="btn btn-warning w-100">Update</button>
                <a href="admin_kategori.php" class="btn btn-secondary w-100 mt-2">Batal</a>
            </form>
        </div>
    </div>
</body>
</html>
