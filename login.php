<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Roblox Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Fredoka', sans-serif; background: #f0f8ff; }
        .login-box {
            max-width: 400px;
            margin: 100px auto;
            background: #fff;
            padding: 30px;
            border-radius: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border: 3px solid #4a90d9;
        }
        .login-box h2 { color: #4a90d9; font-weight: 700; }
        .btn-roblox {
            background: #4a90d9;
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 50px;
            padding: 10px 0;
        }
        .btn-roblox:hover { background: #3a7bc8; transform: scale(1.02); }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h2 class="text-center">🔑 Login</h2>
            <p class="text-center text-muted">Masuk ke akun Roblox Store</p>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <form action="proses_login.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-roblox w-100">Login</button>
            </form>
            <p class="text-center mt-3">Belum punya akun? <a href="register.php">Daftar</a></p>
        </div>
    </div>
</body>
</html>