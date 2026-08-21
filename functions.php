<?php
// functions.php - Kumpulan fungsi helper

/**
 * Sensor nama: tampilkan huruf awal dan akhir, sisanya bintang
 */
function sensor_nama($nama) {
    $panjang = strlen($nama);
    if ($panjang <= 4) {
        return $nama[0] . str_repeat('*', $panjang - 1);
    }
    $awal = substr($nama, 0, 3);
    $akhir = substr($nama, -2);
    $bintang = str_repeat('*', $panjang - 5);
    return $awal . $bintang . $akhir;
}

/**
 * Rata-rata bintang suatu produk
 */
function rata_bintang($conn, $id_produk) {
    $sql = "SELECT AVG(bintang) as rata FROM penilaian WHERE id_produk = $id_produk";
    $hasil = $conn->query($sql);
    $data = $hasil->fetch_assoc();
    return $data['rata'] ? round($data['rata'], 1) : 0;
}

/**
 * Totalterjual suatu produk (status selesai)
 */
function total_terjual($conn, $id_produk) {
    $sql = "SELECT SUM(jumlah) as total FROM pesanan WHERE id_produk = $id_produk AND status = 'selesai'";
    $hasil = $conn->query($sql);
    $data = $hasil->fetch_assoc();
    return $data['total'] ? $data['total'] : 0;
}

/**
 * Cek login - mulai session hanya jika belum aktif
 */
function cek_login() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['id_user'])) {
        header("Location: login.php");
        exit;
    }
}

/**
 * Cek admin - mulai session hanya jika belum aktif
 */
function cek_admin() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
        header("Location: login.php");
        exit;
    }
}
?>
