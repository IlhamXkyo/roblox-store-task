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
    $stmt = $conn->prepare("DELETE FROM kategori WHERE id_kategori = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}

header("Location: admin_kategori.php");
exit;
