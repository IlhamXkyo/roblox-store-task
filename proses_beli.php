<?php
session_start();
require_once 'koneksi.php';
require_once 'functions.php';
cek_login();

$id_user = (int)$_SESSION['id_user'];

$token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
if (!verify_csrf_token($token)) {
    echo "<script>alert('Akses tidak valid: token CSRF tidak cocok.'); window.location.href='index.php';</script>";
    exit;
}

// Pembelian langsung 1 produk
$id_beli = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
if ($id_beli > 0) {
    $id_produk = $id_beli;
    $qty = 1;

    $conn->begin_transaction();
    try {
        $stmt_produk = $conn->prepare("SELECT nama_produk, harga, stok FROM produk WHERE id_produk = ? FOR UPDATE");
        $stmt_produk->bind_param("i", $id_produk);
        $stmt_produk->execute();
        $res = $stmt_produk->get_result();
        $produk = $res->fetch_assoc();

        if (!$produk) {
            throw new Exception("Produk tidak ditemukan.");
        }

        if (!validasi_stok($produk['stok'], $qty)) {
            throw new Exception("Stok produk tidak mencukupi untuk pembelian ini.");
        }

        $total = $produk['harga'] * $qty;

        $stmt_insert = $conn->prepare("INSERT INTO pesanan (id_user, id_produk, jumlah, total_harga, status) VALUES (?, ?, ?, ?, 'dikemas')");
        $stmt_insert->bind_param("iiii", $id_user, $id_produk, $qty, $total);
        if (!$stmt_insert->execute()) {
            throw new Exception("Gagal menyimpan data pesanan.");
        }

        $stmt_stok = $conn->prepare("UPDATE produk SET stok = stok - ? WHERE id_produk = ? AND stok >= ?");
        $stmt_stok->bind_param("iii", $qty, $id_produk, $qty);
        $stmt_stok->execute();

        if ($stmt_stok->affected_rows === 0) {
            throw new Exception("Stok tidak mencukupi atau gagal diperbarui.");
        }

        $conn->commit();
        echo "<script>alert('Pembelian berhasil!'); window.location.href='pesanan_saya.php';</script>";
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        $pesan = addslashes($e->getMessage());
        echo "<script>alert('$pesan'); window.location.href='index.php';</script>";
        exit;
    }
}

// Checkout dari keranjang
$is_checkout = isset($_POST['checkout']) || isset($_GET['checkout']);
if ($is_checkout && !empty($_SESSION['keranjang'])) {
    $keranjang = $_SESSION['keranjang'];

    $conn->begin_transaction();
    try {
        $stmt_cek = $conn->prepare("SELECT nama_produk, harga, stok FROM produk WHERE id_produk = ? FOR UPDATE");
        $stmt_insert = $conn->prepare("INSERT INTO pesanan (id_user, id_produk, jumlah, total_harga, status) VALUES (?, ?, ?, ?, 'dikemas')");
        $stmt_update = $conn->prepare("UPDATE produk SET stok = stok - ? WHERE id_produk = ? AND stok >= ?");

        foreach ($keranjang as $item_id => $item_qty) {
            $id_produk = (int)$item_id;
            $qty = (int)$item_qty;

            if ($qty <= 0) {
                continue;
            }

            $stmt_cek->bind_param("i", $id_produk);
            $stmt_cek->execute();
            $res = $stmt_cek->get_result();
            $produk = $res->fetch_assoc();

            if (!$produk) {
                throw new Exception("Salah satu produk di keranjang tidak ditemukan.");
            }

            if (!validasi_stok($produk['stok'], $qty)) {
                throw new Exception("Stok produk '" . $produk['nama_produk'] . "' tidak mencukupi.");
            }

            $total = $produk['harga'] * $qty;

            $stmt_insert->bind_param("iiii", $id_user, $id_produk, $qty, $total);
            if (!$stmt_insert->execute()) {
                throw new Exception("Gagal memproses salah satu pesanan di keranjang.");
            }

            $stmt_update->bind_param("iii", $qty, $id_produk, $qty);
            $stmt_update->execute();

            if ($stmt_update->affected_rows === 0) {
                throw new Exception("Gagal mengurangi stok produk '" . $produk['nama_produk'] . "'.");
            }
        }

        $conn->commit();
        $_SESSION['keranjang'] = [];
        echo "<script>alert('Checkout berhasil! Pesanan sedang diproses.'); window.location.href='pesanan_saya.php';</script>";
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        $pesan = addslashes($e->getMessage());
        echo "<script>alert('$pesan'); window.location.href='keranjang.php';</script>";
        exit;
    }
}

header("Location: index.php");
exit;