<?php
/**
 * Sanitasi output HTML untuk mencegah serangan Cross-Site Scripting (XSS)
 */
function escape($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

/**
 * Alias singkat fungsi escape
 */
function e($str) {
    return escape($str);
}

/**
 * Validasi kecukupan stok produk sebelum transaksi
 */
function validasi_stok($stok_tersedia, $jumlah_beli) {
    $stok = (int)$stok_tersedia;
    $jumlah = (int)$jumlah_beli;
    if ($jumlah <= 0) {
        return false;
    }
    return $stok >= $jumlah;
}

function sensor_nama($nama) {
    $nama = (string)$nama;
    $panjang = strlen($nama);
    if ($panjang === 0) {
        return '';
    }
    if ($panjang <= 4) {
        return $nama[0] . str_repeat('*', $panjang - 1);
    }
    $awal = substr($nama, 0, 3);
    $akhir = substr($nama, -2);
    $bintang = str_repeat('*', max(0, $panjang - 5));
    return $awal . $bintang . $akhir;
}

function rata_bintang($conn, $id_produk) {
    $stmt = $conn->prepare("SELECT AVG(bintang) as rata FROM penilaian WHERE id_produk = ?");
    if (!$stmt) {
        return 0;
    }
    $id = (int)$id_produk;
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $hasil = $stmt->get_result();
    $data = $hasil->fetch_assoc();
    return ($data && $data['rata'] !== null) ? round((float)$data['rata'], 1) : 0;
}

function total_terjual($conn, $id_produk) {
    $stmt = $conn->prepare("SELECT SUM(jumlah) as total FROM pesanan WHERE id_produk = ? AND status = 'selesai'");
    if (!$stmt) {
        return 0;
    }
    $id = (int)$id_produk;
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $hasil = $stmt->get_result();
    $data = $hasil->fetch_assoc();
    return ($data && $data['total'] !== null) ? (int)$data['total'] : 0;
}

function generate_csrf_token() {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_token() {
    return generate_csrf_token();
}

function verify_csrf_token($token) {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        session_start();
    }
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], (string)$token);
}

function validasi_csrf($token) {
    return verify_csrf_token($token);
}

function cek_login() {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        session_start();
    }
    if (!isset($_SESSION['id_user'])) {
        header("Location: login.php");
        exit;
    }
}

function cek_admin() {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        session_start();
    }
    if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
        header("Location: login.php");
        exit;
    }
}
