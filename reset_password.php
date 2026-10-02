<?php

include "config/koneksi.php";

// Password baru untuk semua akun
$password = "password";

// Buat hash bcrypt
$hash = password_hash($password, PASSWORD_DEFAULT);

// Update semua user
$sql = "UPDATE t_users SET password = ?";

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    die("Prepare gagal: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($stmt, "s", $hash);

if (mysqli_stmt_execute($stmt)) {

    echo "<h2>PASSWORD BERHASIL DIPERBAIKI</h2>";
    echo "<p>Semua akun sekarang menggunakan password:</p>";
    echo "<h3>password</h3>";

} else {

    echo "Gagal memperbarui password: " . mysqli_stmt_error($stmt);
}

mysqli_stmt_close($stmt);
mysqli_close($koneksi);

?>