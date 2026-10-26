<?php
session_start();
require_once 'functions.php';
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        $_SESSION['error'] = "Validasi token keamanan (CSRF) gagal. Silakan coba lagi.";
        header("Location: register.php");
        exit;
    }

    $username = trim($_POST['username'] ?? '');
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $password_raw = $_POST['password'] ?? '';

    if ($username === '' || $nama_lengkap === '' || $password_raw === '') {
        $_SESSION['error'] = "Semua bidang wajib diisi.";
        header("Location: register.php");
        exit;
    }

    $password = password_hash($password_raw, PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT id_user FROM users WHERE username = ?");
    if ($check) {
        $check->bind_param("s", $username);
        $check->execute();
        $check_res = $check->get_result();
        if ($check_res->num_rows > 0) {
            $_SESSION['error'] = "Username sudah terdaftar, gunakan yang lain.";
            header("Location: register.php");
            exit;
        }
    }

    $sql = $conn->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, 'buyer')");
    if ($sql) {
        $sql->bind_param("sss", $username, $password, $nama_lengkap);
        if ($sql->execute()) {
            $_SESSION['success'] = "Pendaftaran berhasil! Silakan login.";
            header("Location: login.php");
            exit;
        }
    }

    $_SESSION['error'] = "Terjadi kesalahan saat pendaftaran.";
    header("Location: register.php");
    exit;
}

header("Location: register.php");
exit;
