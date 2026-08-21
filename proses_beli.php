<?php
session_start();
require_once 'koneksi.php';
require_once 'functions.php';
cek_login();

$id_user = $_SESSION['id_user'];

// Jika dari tombol "Beli Langsung" di index
if (isset($_GET['id'])) {
    $id_produk = intval($_GET['id']);
    $qty = 1;
    // Langsung buat pesanan
    $sql_produk = "SELECT * FROM produk WHERE id_produk = $id_produk";
    $res = $conn->query($sql_produk);
    $produk = $res->fetch_assoc();
    $total = $produk['harga'] * $qty;

    // Simpan ke pesanan
    $insert = "INSERT INTO pesanan (id_user, id_produk, jumlah, total_harga, status) 
               VALUES ($id_user, $id_produk, $qty, $total, 'dikemas')";
    if ($conn->query($insert)) {
        // Kurangi stok
        $conn->query("UPDATE produk SET stok = stok - $qty WHERE id_produk = $id_produk");
        echo "<script>
                alert('Pembelian berhasil!');
                window.location.href='pesanan_saya.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal memproses pesanan.');
                window.location.href='index.php';
              </script>";
    }
    exit;
}

// Jika dari keranjang (checkout)
if (isset($_GET['checkout']) && !empty($_SESSION['keranjang'])) {
    $keranjang = $_SESSION['keranjang'];
    $berhasil = true;
    foreach ($keranjang as $id_produk => $qty) {
        $sql_produk = "SELECT * FROM produk WHERE id_produk = $id_produk";
        $res = $conn->query($sql_produk);
        $produk = $res->fetch_assoc();
        $total = $produk['harga'] * $qty;
        $insert = "INSERT INTO pesanan (id_user, id_produk, jumlah, total_harga, status) 
                   VALUES ($id_user, $id_produk, $qty, $total, 'dikemas')";
        if ($conn->query($insert)) {
            $conn->query("UPDATE produk SET stok = stok - $qty WHERE id_produk = $id_produk");
        } else {
            $berhasil = false;
        }
    }
    if ($berhasil) {
        $_SESSION['keranjang'] = []; // kosongkan keranjang
        echo "<script>
                alert('Checkout berhasil! Pesanan sedang diproses.');
                window.location.href='pesanan_saya.php';
              </script>";
    } else {
        echo "<script>
                alert('Terjadi kesalahan saat checkout.');
                window.location.href='keranjang.php';
              </script>";
    }
    exit;
}

// Jika tidak ada parameter, redirect
header("Location: index.php");
exit;
?>