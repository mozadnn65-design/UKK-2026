<?php
// buat_user_awal.php
// Jalankan file  ini SATU KALI saja lewat browser untuk membuat user awal
include 'config/koneksi.php';

$name     = 'Administrator';
$email = 'admin@gmail.com';
$password = password_hash('admin123', PASSWORD_DEFAULT);
$role     ='admin';

$sql = "INSERT INTO t_users ( name, email, password, role)";
$sql .= " VALUES ( '$name', '$email', '$password' , '$role')";

if (mysqli_query($koneksi, $sql)) {
    echo 'User admin berhasil dibuat. Silakan hapus file ini.';
} else {
    echo 'Gagal membuat user: ' .mysqli_error($koneksi);
}
?>