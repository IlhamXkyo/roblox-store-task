<?php
session_start();
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Cek apakah username sudah ada
    $check = $conn->query("SELECT id_user FROM users WHERE username = '$username'");
    if ($check->num_rows > 0) {
        $_SESSION['error'] = "Username sudah terdaftar, gunakan yang lain.";
        header("Location: register.php");
        exit;
    }

    // Simpan user baru dengan role default 'buyer'
    $sql = "INSERT INTO users (username, password, nama_lengkap, role) VALUES ('$username', '$password', '$nama_lengkap', 'buyer')";
    if ($conn->query($sql)) {
        $_SESSION['success'] = "Pendaftaran berhasil! Silakan login.";
        header("Location: login.php");
    } else {
        $_SESSION['error'] = "Terjadi kesalahan: " . $conn->error;
        header("Location: register.php");
    }
} else {
    header("Location: register.php");
}
?>