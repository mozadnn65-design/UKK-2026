<?php
// Jalankan SEKALI saja, lalu HAPUS file ini.
include "koneksi.php";

$hash = password_hash("password", PASSWORD_DEFAULT);

$stmt = mysqli_prepare($koneksi, "UPDATE t_users SET password = ?");
mysqli_stmt_bind_param($stmt, "s", $hash);
mysqli_stmt_execute($stmt);

echo "Selesai. " . mysqli_stmt_affected_rows($stmt) . " akun sekarang berpassword: password";
