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
$_SESSION['nama'] = $name;
$_SESSION['email'] = $email_db;
$_SESSION['role'] = $role;

// Login berhasil
header("Location: dashboard.php");
exit;

?>