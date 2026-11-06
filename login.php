<?php
session_start();

// Kalau sudah login, langsung lempar ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

// Ambil pesan error (kalau ada), lalu hapus dari session
$pesan_error = $_SESSION['pesan_error'] ?? null;
unset($_SESSION['pesan_error']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Pelanggaran Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-dark">

    <div class="container min-vh-100 d-flex justify-content-center align-items-center">
        <div class="card shadow-lg border-0 rounded-4 w-100" style="max-width: 420px;">

            <!-- Header -->
            <div class="card-header bg-primary text-white text-center py-4 rounded-top-4">
                <i class="bi bi-shield-lock-fill fs-1"></i>
                <h4 class="fw-bold mt-2 mb-0">Pelanggaran Siswa</h4>
                <small>Aplikasi Manajemen Pelanggaran Siswa</small>
            </div>

            <!-- Form Login -->
            <div class="card-body p-4">
                <h5 class="text-center fw-bold mb-3">Masuk ke Akun Anda</h5>

                <?php if ($pesan_error): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($pesan_error); ?>
                    </div>
                <?php endif; ?>

                <form action="proses_login.php" method="POST">

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email"
                               class="form-control"
                               placeholder="Masukkan email"
                               autocomplete="username"
                               readonly onfocus="this.removeAttribute('readonly');"
                               required>
                    </div>

                    <!-- Password + tombol lihat/sembunyikan -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password"
                                   class="form-control"
                                   placeholder="Masukkan password"
                                   autocomplete="current-password"
                                   readonly onfocus="this.removeAttribute('readonly');"
                                   required>
                            <button type="button" class="btn btn-outline-secondary" id="btnLihatPassword">
                                <i class="bi bi-eye-fill" id="iconLihatPassword"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Login
                    </button>

                </form>
            </div>

            <div class="card-footer text-center text-secondary">
                <small>Silahkan masuk menggunakan akun Anda</small>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tombol untuk melihat / menyembunyikan password
        const inputPassword = document.getElementById('password');
        const iconMata = document.getElementById('iconLihatPassword');

        document.getElementById('btnLihatPassword').onclick = function () {
            const sedangTerlihat = inputPassword.type === 'text';

            inputPassword.type = sedangTerlihat ? 'password' : 'text';
            iconMata.className = sedangTerlihat ? 'bi bi-eye-fill' : 'bi bi-eye-slash-fill';
        };
    </script>

</body>
</html>