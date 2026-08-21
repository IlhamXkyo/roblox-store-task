<?php
session_start();
require_once 'koneksi.php';
require_once 'functions.php';
cek_admin();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // Ambil gambar untuk dihapus
    $data = $conn->query("SELECT gambar FROM produk WHERE id_produk = $id")->fetch_assoc();
    if ($data && $data['gambar'] != 'roblox_logo.png' && file_exists("uploads/produk/".$data['gambar'])) {
        unlink("uploads/produk/".$data['gambar']);
    }
    $conn->query("DELETE FROM produk WHERE id_produk = $id");
}
header("Location: admin_produk.php");
exit;
?>