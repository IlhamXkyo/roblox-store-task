<?php
/**
 * Test suite otomatis untuk logika helper, validasi bisnis, dan integritas kueri Roblox Store.
 * Jalankan melalui CLI: & "C:\xampp\php\php.exe" tests/test_store_logic.php
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

define('APP_ROOT', dirname(__DIR__));
require_once APP_ROOT . '/functions.php';

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

$total_assertions = 0;
$passed_assertions = 0;
$failed_assertions = [];

function assert_true($condition, $description) {
    global $total_assertions, $passed_assertions, $failed_assertions;
    $total_assertions++;
    if ($condition) {
        $passed_assertions++;
        echo "  [PASS] {$description}\n";
    } else {
        $failed_assertions[] = $description;
        echo "  [FAIL] {$description}\n";
    }
}

function assert_equals($expected, $actual, $description) {
    $matched = ($expected === $actual);
    $desc = $description . " (Diharapkan: " . var_export($expected, true) . ", Diterima: " . var_export($actual, true) . ")";
    assert_true($matched, $desc);
}

echo "========================================================\n";
echo "  ROBLOX STORE: AUTOMATED TEST SUITE\n";
echo "========================================================\n\n";

// 1. Pengujian Sanitasi Output XSS (escape & e)
echo "[1] Testing Sanitasi Output XSS (escape & e)...\n";

$payload_script = "<script>alert('xss')</script>";
$escaped_script = escape($payload_script);
assert_true(strpos($escaped_script, "<script>") === false, "Tag script harus di-encode dan tidak mentah");
assert_equals("&lt;script&gt;alert(&#039;xss&#039;)&lt;/script&gt;", $escaped_script, "Payload script harus ter-encode dengan ENT_QUOTES");

$payload_attr = '" onclick="alert(1)"';
assert_equals('&quot; onclick=&quot;alert(1)&quot;', escape($payload_attr), "Karakter double quotes harus diubah menjadi entity &quot;");

assert_equals('&amp;&lt;&gt;&quot;&#039;', escape('&<>"\''), "Karakter khusus HTML utama harus ter-escape sempurna");
assert_equals("123", escape(123), "Input integer harus dikonversi aman ke string");
assert_equals("", escape(""), "String kosong harus mengembalikan string kosong");
assert_equals(escape("<test>"), e("<test>"), "Alias fungsi e() harus memberikan output identik dengan escape()");

echo "\n";

// 2. Pengujian Sensor Nama (sensor_nama)
echo "[2] Testing Sensor Nama (sensor_nama)...\n";

assert_equals("", sensor_nama(""), "String kosong tidak boleh menyebabkan peringatan PHP dan mengembalikan string kosong");
assert_equals("A", sensor_nama("A"), "Nama 1 huruf tetap tampil tanpa penambahan bintang");
assert_equals("A*", sensor_nama("Ad"), "Nama 2 huruf menyensor huruf kedua");
assert_equals("A**", sensor_nama("Adi"), "Nama 3 huruf menyensor 2 huruf belakang");
assert_equals("B***", sensor_nama("Budi"), "Nama 4 huruf menyensor 3 huruf belakang");
assert_equals("Ale****er", sensor_nama("Alexander"), "Nama 9 huruf menampilkan 3 karakter depan dan 2 belakang dengan 4 bintang");
assert_equals("Adm********or", sensor_nama("Administrator"), "Nama 13 huruf disensor dengan panjang bintang proporsional");

echo "\n";

// 3. Pengujian CSRF Token Helper
echo "[3] Testing Validasi CSRF Token...\n";

if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}
$_SESSION = [];

$token1 = generate_csrf_token();
assert_true(!empty($token1), "CSRF token baru harus dihasilkan");
assert_true(strlen($token1) === 64, "CSRF token harus string hex 64 karakter (32 bytes)");
assert_equals($token1, csrf_token(), "Panggilan ulang csrf_token() harus menghasilkan token session yang sama");

assert_true(verify_csrf_token($token1), "Verifikasi token asli harus menghasilkan true");
assert_true(validasi_csrf($token1), "Alias validasi_csrf() harus menghasilkan true untuk token valid");

assert_true(!verify_csrf_token("invalid_token_1234567890"), "Verifikasi token palsu harus ditolak (false)");
assert_true(!verify_csrf_token(""), "Verifikasi token kosong harus ditolak (false)");
assert_true(!verify_csrf_token(null), "Verifikasi token bernilai null harus ditolak (false)");

echo "\n";

// 4. Pengujian Validasi Stok Beli (validasi_stok)
echo "[4] Testing Validasi Stok Beli (validasi_stok)...\n";

assert_true(validasi_stok(10, 1), "Stok 10 mencukupi untuk pembelian 1 item");
assert_true(validasi_stok(5, 5), "Stok 5 mencukupi untuk pembelian 5 item (pembelian batas stok)");
assert_true(!validasi_stok(3, 4), "Stok 3 tidak mencukupi untuk pembelian 4 item");
assert_true(!validasi_stok(0, 1), "Stok 0 harus menolak pembelian 1 item");
assert_true(!validasi_stok(10, 0), "Kuantitas beli 0 tidak valid");
assert_true(!validasi_stok(10, -5), "Kuantitas beli negatif tidak valid");
assert_true(validasi_stok("15", "2"), "String numerik harus di-cast dengan benar dan bernilai true");

echo "\n";

// 5. Pengujian Struktur Integritas Skema SQL & Kueri
echo "[5] Testing Struktur Integritas Skema SQL & Kueri...\n";

$sql_file = APP_ROOT . '/roblox_store.sql';
assert_true(file_exists($sql_file), "Berkas skema database roblox_store.sql harus tersedia");

$sql_content = file_get_contents($sql_file);
$required_tables = ['users', 'kategori', 'produk', 'pesanan', 'penilaian'];
foreach ($required_tables as $table) {
    $pattern = "/CREATE TABLE `{$table}`/i";
    assert_true((bool)preg_match($pattern, $sql_content), "Definisi tabel '{$table}' wajib ada di roblox_store.sql");
}

$required_columns = [
    'users' => ['id_user', 'username', 'password', 'nama_lengkap', 'role'],
    'kategori' => ['id_kategori', 'nama_kategori'],
    'produk' => ['id_produk', 'id_kategori', 'nama_produk', 'harga', 'stok', 'gambar', 'deskripsi'],
    'pesanan' => ['id_pesanan', 'id_user', 'id_produk', 'jumlah', 'total_harga', 'status', 'tanggal_pesan'],
    'penilaian' => ['id_penilaian', 'id_pesanan', 'id_user', 'id_produk', 'bintang', 'komentar', 'foto_ulasan', 'tanggal_ulasan']
];

foreach ($required_columns as $table => $columns) {
    foreach ($columns as $column) {
        $has_col = (bool)preg_match("/`{$column}`/i", $sql_content);
        assert_true($has_col, "Kolom '{$column}' pada tabel '{$table}' harus didefinisikan");
    }
}

// Integritas kueri SQLite in-memory: uji eksekusi sintaks DDL & DML standar
try {
    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("
        CREATE TABLE users (
            id_user INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            nama_lengkap TEXT NOT NULL,
            role TEXT DEFAULT 'buyer'
        );
        CREATE TABLE kategori (
            id_kategori INTEGER PRIMARY KEY AUTOINCREMENT,
            nama_kategori TEXT NOT NULL
        );
        CREATE TABLE produk (
            id_produk INTEGER PRIMARY KEY AUTOINCREMENT,
            id_kategori INTEGER NOT NULL,
            nama_produk TEXT NOT NULL,
            harga INTEGER NOT NULL,
            stok INTEGER NOT NULL,
            gambar TEXT DEFAULT 'roblox_logo.png',
            deskripsi TEXT
        );
        CREATE TABLE pesanan (
            id_pesanan INTEGER PRIMARY KEY AUTOINCREMENT,
            id_user INTEGER NOT NULL,
            id_produk INTEGER NOT NULL,
            jumlah INTEGER NOT NULL,
            total_harga INTEGER NOT NULL,
            status TEXT DEFAULT 'dikemas',
            tanggal_pesan DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE penilaian (
            id_penilaian INTEGER PRIMARY KEY AUTOINCREMENT,
            id_pesanan INTEGER NOT NULL,
            id_user INTEGER NOT NULL,
            id_produk INTEGER NOT NULL,
            bintang INTEGER NOT NULL,
            komentar TEXT,
            foto_ulasan TEXT,
            tanggal_ulasan DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");

    assert_true(true, "Inisialisasi skema tabel in-memory SQLite berhasil");

    $stmt_kat = $pdo->prepare("INSERT INTO kategori (nama_kategori) VALUES (?)");
    $stmt_kat->execute(['Avatar Accessories']);
    $id_kat = (int)$pdo->lastInsertId();

    $stmt_prod = $pdo->prepare("INSERT INTO produk (id_kategori, nama_produk, harga, stok, gambar, deskripsi) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt_prod->execute([$id_kat, 'Valkyrie Helm', 250000, 5, 'valk.png', 'Item legendaris']);
    $id_prod = (int)$pdo->lastInsertId();

    $stmt_user = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)");
    $stmt_user->execute(['testbuyer', 'hash', 'Test Buyer', 'buyer']);
    $id_usr = (int)$pdo->lastInsertId();

    $stmt_catalog = $pdo->prepare("
        SELECT p.*, k.nama_kategori 
        FROM produk p 
        LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
        ORDER BY p.id_produk DESC
    ");
    $stmt_catalog->execute();
    $catalog_data = $stmt_catalog->fetchAll(PDO::FETCH_ASSOC);
    assert_equals(1, count($catalog_data), "Kueri katalog join harus mengembalikan record produk");
    assert_equals('Avatar Accessories', $catalog_data[0]['nama_kategori'], "Join relasi kategori harus menghasilkan nama_kategori yang valid");

    $beli_qty = 2;
    assert_true(validasi_stok($catalog_data[0]['stok'], $beli_qty), "Validasi stok lolos sebelum transaksi");

    $pdo->beginTransaction();
    $stmt_order = $pdo->prepare("INSERT INTO pesanan (id_user, id_produk, jumlah, total_harga, status) VALUES (?, ?, ?, ?, 'dikemas')");
    $total_harga = $catalog_data[0]['harga'] * $beli_qty;
    $stmt_order->execute([$id_usr, $id_prod, $beli_qty, $total_harga]);

    $stmt_update_stok = $pdo->prepare("UPDATE produk SET stok = stok - ? WHERE id_produk = ? AND stok >= ?");
    $stmt_update_stok->execute([$beli_qty, $id_prod, $beli_qty]);
    $pdo->commit();

    $stmt_cek_sisa = $pdo->prepare("SELECT stok FROM produk WHERE id_produk = ?");
    $stmt_cek_sisa->execute([$id_prod]);
    $sisa_stok = (int)$stmt_cek_sisa->fetchColumn();
    assert_equals(3, $sisa_stok, "Sisa stok setelah transaksi pembelian 2 item dari stok 5 harus tepat 3");

} catch (Exception $ex) {
    assert_true(false, "Pengecekan integritas kueri mengalami kegagalan: " . $ex->getMessage());
}

echo "\n";

// 6. Pengujian Keamanan Autentikasi & Konfigurasi Lingkungan
echo "[6] Testing Keamanan Autentikasi & Konfigurasi Lingkungan...\n";

$raw_password = "SuperSecretPassword123!";
$hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);
assert_true(password_verify($raw_password, $hashed_password), "Verifikasi password asli harus cocok dengan hash PASSWORD_DEFAULT");
assert_true(!password_verify("WrongPassword123!", $hashed_password), "Verifikasi password salah harus ditolak");

// Pengujian deteksi keberadaan token CSRF pada form autentikasi
$login_content = file_get_contents(APP_ROOT . '/login.php');
assert_true(strpos($login_content, 'generate_csrf_token()') !== false, "Form login.php wajib menyertakan token CSRF");

$proses_login_content = file_get_contents(APP_ROOT . '/proses_login.php');
assert_true(strpos($proses_login_content, 'verify_csrf_token') !== false, "Handler proses_login.php wajib memverifikasi token CSRF");

$register_content = file_get_contents(APP_ROOT . '/register.php');
assert_true(strpos($register_content, 'generate_csrf_token()') !== false, "Form register.php wajib menyertakan token CSRF");

$proses_register_content = file_get_contents(APP_ROOT . '/proses_register.php');
assert_true(strpos($proses_register_content, 'verify_csrf_token') !== false, "Handler proses_register.php wajib memverifikasi token CSRF");

// Pengujian dukungan environment variable pada koneksi.php
$koneksi_content = file_get_contents(APP_ROOT . '/koneksi.php');
assert_true(strpos($koneksi_content, "getenv('DB_HOST')") !== false, "koneksi.php harus mendukung konfigurasi DB_HOST via environment variable");
assert_true(strpos($koneksi_content, "getenv('DB_USER')") !== false, "koneksi.php harus mendukung konfigurasi DB_USER via environment variable");
assert_true(strpos($koneksi_content, "getenv('DB_PASS')") !== false, "koneksi.php harus mendukung konfigurasi DB_PASS via environment variable");
assert_true(strpos($koneksi_content, "getenv('DB_NAME')") !== false, "koneksi.php harus mendukung konfigurasi DB_NAME via environment variable");

echo "\n========================================================\n";
echo "  HASIL PENGUJIAN: {$passed_assertions} / {$total_assertions} assertions PASS\n";
if (!empty($failed_assertions)) {
    echo "  GAGAL: " . count($failed_assertions) . " assertion(s)\n";
    foreach ($failed_assertions as $f) {
        echo "  - {$f}\n";
    }
    echo "========================================================\n";
    exit(1);
} else {
    echo "  STATUS: SEMUA PENGUJIAN LOLOS DENGAN SUKSES.\n";
    echo "========================================================\n";
    exit(0);
}
