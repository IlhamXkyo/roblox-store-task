<?php
session_start();
require_once 'koneksi.php';
require_once 'functions.php';
cek_admin();

$token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
if (!verify_csrf_token($token)) {
    die("Validasi token CSRF gagal.");
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
if ($id > 0) {
    $stmt_find = $conn->prepare("SELECT gambar FROM produk WHERE id_produk = ?");
    if ($stmt_find) {
        $stmt_find->bind_param("i", $id);
        $stmt_find->execute();
        $res = $stmt_find->get_result();
        $data = $res->fetch_assoc();
        if ($data && !empty($data['gambar']) && $data['gambar'] !== 'roblox_logo.png') {
            $file_path = "uploads/produk/" . basename($data['gambar']);
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    $stmt_del = $conn->prepare("DELETE FROM produk WHERE id_produk = ?");
    if ($stmt_del) {
        $stmt_del->bind_param("i", $id);
        $stmt_del->execute();
    }
}

header("Location: admin_produk.php");
exit;