<?php
// koneksi.php - Koneksi ke database MySQL
$host = 'localhost';
$user = 'root';        // default XAMPP
$pass = '';            // default kosong
$db   = 'roblox_store';

$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set charset agar mendukung UTF-8
$conn->set_charset("utf8mb4");
?>