<?php
session_start();
require_once 'koneksi.php';
require_once 'functions.php';
cek_admin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$data = $conn->query("SELECT * FROM kategori WHERE id_kategori = $id")->fetch_assoc();
if (!$data) { header("Location: admin_kategori.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama_kategori']);
    if (!empty($nama)) {
        $conn->query("UPDATE kategori SET nama_kategori = '$nama' WHERE id_kategori = $id");
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
                <div class="mb-3">
                    <label for="nama_kategori" class="form-label">Nama Kategori</label>
                    <input type="text" name="nama_kategori" id="nama_kategori" class="form-control" value="<?= $data['nama_kategori'] ?>" required>
                </div>
                <button type="submit" class="btn btn-warning w-100">Update</button>
                <a href="admin_kategori.php" class="btn btn-secondary w-100 mt-2">Batal</a>
            </form>
        </div>
    </div>
</body>
</html>
