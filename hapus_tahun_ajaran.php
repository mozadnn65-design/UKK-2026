<?php

session_start();

include "config/koneksi.php";

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Hanya admin
if ($_SESSION['role'] != "admin") {
    echo "Akses ditolak!";
    exit;
}

// Ambil ID
$id = $_GET['id'];

// Hapus data
$query = mysqli_query(
    $koneksi,
    "DELETE FROM t_tahun_ajaran WHERE id = '$id'"
);

// Kembali ke halaman kelola
header("Location: kelola_tahun_ajaran.php");
exit;

?>