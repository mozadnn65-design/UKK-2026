
<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Pelanggaran Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container min-vh-100 d-flex justify-content-center align-items-center">

    <div class="card shadow-sm border w-100" style="max-width: 400px;">

        <div class="card-header bg-primary text-white text-center py-3">
            <h4 class="mb-1">Pelanggaran Siswa</h4>
            <small>Aplikasi Manajemen Pelanggaran Siswa</small>
        </div>

        <div class="card-body p-4">

            <h5 class="text-center mb-4">Login</h5>

            <?php if (isset($_SESSION['pesan_error'])) { ?>
                <div class="alert alert-danger" role="alert">
                    <?php
                    echo htmlspecialchars($_SESSION['pesan_error']);
                    unset($_SESSION['pesan_error']);
                    ?>
                </div>
            <?php } ?>

            <form action="proses_login.php"
                  method="POST"
                  autocomplete="off">

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control"
                        placeholder="Masukkan email"
                        autocomplete="off"
                        required>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control"
                        placeholder="Masukkan password"
                        autocomplete="new-password"
                        required>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Login
                </button>

            </form>

        </div>

        <div class="card-footer text-center text-secondary py-3">
            <small>Silakan masuk menggunakan akun Anda</small>
        </div>

    </div>

</div>

</body>
</html>
