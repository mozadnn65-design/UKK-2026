<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['role'] != 'admin') {
    echo 'Akses ditolak!';
    exit;
}

$id = (int) ($_GET['id'] ?? 0);

$hapus = mysqli_prepare($koneksi, "DELETE FROM t_tahun_ajaran WHERE id = ?");
mysqli_stmt_bind_param($hapus, "i", $id);

try {
    $berhasil = mysqli_stmt_execute($hapus);
} catch (Exception $e) {
    $berhasil = false;
}

if ($berhasil) {
    $_SESSION['pesan_error'] = 'Data tahun ajaran berhasil dihapus!';
} else {
    $_SESSION['pesan_error'] = 'Data tahun ajaran tidak bisa dihapus karena masih dipakai data lain!';
}

header('Location: kelola_tahun_ajaran.php');
exit;