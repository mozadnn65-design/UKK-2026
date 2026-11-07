<?php

session_start();

include "config/koneksi.php";

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';


// Cek input
if ($email === '' || $password === '') {
    $_SESSION['pesan_error'] = "Email dan password wajib diisi.";
    header("Location: login.php");
    exit;
}

// Cari user
$sql = "SELECT id, name, email, password, role
        FROM t_users
        WHERE email = ?
        LIMIT 1";

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    $_SESSION['pesan_error'] = "Terjadi kesalahan sistem.";
    header("Location: login.php");
    exit;
}

mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);

mysqli_stmt_bind_result(
    $stmt,
    $id,
    $name,
    $email_db,
    $password_db,
    $role
);

// Email tidak ditemukan
if (!mysqli_stmt_fetch($stmt)) {

    mysqli_stmt_close($stmt);

    $_SESSION['pesan_error'] = "Email tidak ditemukan.";
    header("Location: login.php");
    exit;
}

mysqli_stmt_close($stmt);

// Cek password
if (!password_verify($password, $password_db)) {

    $_SESSION['pesan_error'] = "Password salah.";
    header("Location: login.php");
    exit;
}

// Cek role
if ($role !== 'admin' && $role !== 'guru') {

    $_SESSION['pesan_error'] = "Role akun tidak diizinkan.";
    header("Location: login.php");
    exit;
}

// Buat session
session_regenerate_id(true);

$_SESSION['user_id'] = $id;
$_SESSION['id_user'] = $id; // dipakai oleh laporan.php
$_SESSION['nama'] = $name;
$_SESSION['email'] = $email_db;
$_SESSION['role'] = $role;

// Cek apakah guru ini wali kelas aktif (data dari menu Data Wali Kelas)
$_SESSION['is_wali_kelas'] = false;
$_SESSION['guru_id'] = 0;

if ($role === 'guru') {

    $sql_wali = "SELECT g.id
                 FROM t_guru g
                 INNER JOIN t_wali_kelas wk ON wk.guru_id = g.id
                 WHERE g.user_id = ?
                   AND wk.status_aktif = 1
                 LIMIT 1";

    $stmt_wali = mysqli_prepare($koneksi, $sql_wali);

    if ($stmt_wali) {
        mysqli_stmt_bind_param($stmt_wali, "i", $id);
        mysqli_stmt_execute($stmt_wali);
        mysqli_stmt_bind_result($stmt_wali, $guru_id);

        if (mysqli_stmt_fetch($stmt_wali)) {
            $_SESSION['is_wali_kelas'] = true;
            $_SESSION['guru_id'] = (int) $guru_id;
        }

        mysqli_stmt_close($stmt_wali);
    }
}

// Login berhasil
header("Location: dashboard.php");
exit;

?>
