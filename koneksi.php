<?php
// koneksi.php - Koneksi ke database MySQL
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';        // default XAMPP
$pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';            // default kosong
$db   = getenv('DB_NAME') ?: 'roblox_store';

$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Set charset agar mendukung UTF-8
$conn->set_charset("utf8mb4");
?>
