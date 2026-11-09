<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = (int) ($_GET['id'] ?? 0);

$hapus = mysqli_prepare($koneksi, "DELETE FROM t_pelanggaran_siswa WHERE id = ?");
mysqli_stmt_bind_param($hapus, "i", $id);

try {
    $berhasil = mysqli_stmt_execute($hapus);
} catch (Exception $e) {
    $berhasil = false;
}

if ($berhasil) {
    $_SESSION['pesan_error'] = 'Data pelanggaran berhasil dihapus!';
} else {
    $_SESSION['pesan_error'] = 'Data pelanggaran tidak bisa dihapus!';
}

header('Location: catat_pelanggaran.php');
exit;