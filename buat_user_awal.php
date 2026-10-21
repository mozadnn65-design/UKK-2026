<?php
// Menghubungkan database
include "config/koneksi.php";


// DATA AKUN ADMIN
$nama_admin = "Ahmad Ramadhan";
$email_admin = "user001@sekolah.test";
$password_admin = password_hash("admin123", PASSWORD_DEFAULT);
$role_admin = "admin";

$cek_admin = mysqli_query($koneksi, "SELECT id FROM t_users WHERE email='$email_admin'");

if (mysqli_num_rows($cek_admin) > 0) {
    $sql_admin = "UPDATE t_users
                  SET password='$password_admin', role='$role_admin'
                  WHERE email='$email_admin'";
} else {
    $sql_admin = "INSERT INTO t_users (name, email, password, role)
                  VALUES ('$nama_admin', '$email_admin', '$password_admin', '$role_admin')";
}

if (mysqli_query($koneksi, $sql_admin)) {
    echo "Akun admin berhasil dibuat atau diperbarui.<br>";
} else {
    echo "Akun admin gagal diproses.<br>";
}


// DATA AKUN GURU
$nama_guru = "Fajar Aditya";
$email_guru = "user006@sekolah.test";
$password_guru = password_hash("guru123", PASSWORD_DEFAULT);
$role_guru = "guru";

$cek_guru = mysqli_query($koneksi, "SELECT id FROM t_users WHERE email='$email_guru'");

if (mysqli_num_rows($cek_guru) > 0) {
    $sql_guru = "UPDATE t_users
                 SET password='$password_guru', role='$role_guru'
                 WHERE email='$email_guru'";
} else {
    $sql_guru = "INSERT INTO t_users (name, email, password, role)
                 VALUES ('$nama_guru', '$email_guru', '$password_guru', '$role_guru')";
}

if (mysqli_query($koneksi, $sql_guru)) {
    echo "Akun guru berhasil dibuat atau diperbarui.";
} else {
    echo "Akun guru gagal diproses.";
}
?>
