<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

$hapus = mysqli_prepare($koneksi, "DELETE FROM t_pelanggaran WHERE id = ?");
mysqli_stmt_bind_param($hapus, "i", $id);

try {
    $berhasil = mysqli_stmt_execute($hapus);
} catch (Exception $e) {
    $berhasil = false;
}

if ($berhasil) {
    $_SESSION['pesan_error'] = 'Jenis pelanggaran berhasil dihapus!';
} else {
    $_SESSION['pesan_error'] = 'Jenis pelanggaran tidak bisa dihapus karena masih dipakai data lain!';
}

header('Location: kelola_jenis_pelanggaran.php');
exit;