<?php
session_start();
require_once 'koneksi.php';
require_once 'functions.php';
cek_admin();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM kategori WHERE id_kategori = $id");
}
header("Location: admin_kategori.php");
exit;
?>