<?php

session_start();

include "config/koneksi.php";


// MENGAMBIL DATA FORM
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';



// CEK INPUT
if ($email == '' || $password == '') {

    $_SESSION['pesan_error'] = "Email dan password wajib diisi.";

    header("Location: login.php");
    exit;
}


// MENCARI USER
$query = "SELECT id, name, email, password, role
          FROM t_users
          WHERE email = ?
          LIMIT 1";

$stmt = mysqli_prepare($koneksi, $query);

if (!$stmt) {

    die("Query gagal: " . mysqli_error($koneksi));

}

mysqli_stmt_bind_param($stmt, "s", $email);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);



// CEK EMAIL
if (!$user) {

    $_SESSION['pesan_error'] = "Email tidak ditemukan.";

    header("Location: login.php");
    exit;
}



// CEK PASSWORD
$password_benar = false;


// Password menggunakan password_hash()
if (password_verify($password, $user['password'])) {

    $password_benar = true;

}


// Password biasa
if ($password === $user['password']) {

    $password_benar = true;

}



// PASSWORD SALAH
if (!$password_benar) {

    $_SESSION['pesan_error'] = "Password salah.";

    header("Location: login.php");
    exit;
}


// CEK ROLE
// HANYA ADMIN DAN GURU
if ($user['role'] !== 'admin' && $user['role'] !== 'guru') {

    $_SESSION['pesan_error'] = "Role akun tidak diizinkan.";

    header("Location: login.php");
    exit;
}



// MEMBUAT SESSION
$_SESSION['user_id'] = $user['id'];
$_SESSION['nama'] = $user['name'];
$_SESSION['email'] = $user['email'];
$_SESSION['role'] = $user['role'];


// LOGIN BERHASIL
header("Location: dashboard.php");
exit;

?>