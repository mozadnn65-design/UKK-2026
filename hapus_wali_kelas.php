<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Hanya admin yang boleh menghapus
if (($_SESSION['role'] ?? '') !== 'admin') {
    echo 'Akses ditolak!';
    exit;
}

$id = (int) ($_GET['id'] ?? 0);

$hapus = mysqli_prepare($koneksi, "DELETE FROM t_wali_kelas WHERE id = ?");
mysqli_stmt_bind_param($hapus, "i", $id);

try {
    $berhasil = mysqli_stmt_execute($hapus);
} catch (Exception $e) {
    $berhasil = false;
}

if ($berhasil && mysqli_stmt_affected_rows($hapus) > 0) {
    $_SESSION['pesan_error'] = 'Data wali kelas berhasil dihapus!';
} elseif ($berhasil) {
    $_SESSION['pesan_error'] = 'Data wali kelas tidak ditemukan!';
} else {
    $_SESSION['pesan_error'] = 'Data wali kelas tidak bisa dihapus karena masih dipakai data lain!';
}

header('Location: kelola_wali_kelas.php');
exit;