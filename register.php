<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Roblox Store</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Fredoka', sans-serif; background: #f0f8ff; }
        .register-box {
            max-width: 450px;
            margin: 80px auto;
            background: #fff;
            padding: 30px;
            border-radius: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border: 3px solid #ffd700;
        }
        .register-box h2 {
            color: #4a90d9;
            font-weight: 700;
        }
        .btn-roblox {
            background: #ff5e00;
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 50px;
            padding: 10px 0;
            transition: 0.3s;
        }
        .btn-roblox:hover {
            background: #e05500;
            transform: scale(1.02);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-box">
            <h2 class="text-center">🎮 Daftar Akun</h2>
            <p class="text-center text-muted">Bergabunglah sebagai pembeli</p>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <form action="proses_register.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-roblox w-100">Daftar Sekarang</button>
            </form>
            <p class="text-center mt-3">Sudah punya akun? <a href="login.php">Login</a></p>
        </div>
    </div>
</body>
</html>