<?php
session_start();
require_once 'koneksi.php';
require_once 'functions.php';
cek_admin();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // Hapus user beserta pesanan dan penilaian (karena foreign key cascade)
    $conn->query("DELETE FROM users WHERE id_user = $id AND role = 'buyer'");
}
header("Location: admin_pelanggan.php");
exit;
?>