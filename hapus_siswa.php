<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

try {
    $hapus = mysqli_prepare($koneksi, "DELETE FROM t_siswa WHERE id = ?");
    mysqli_stmt_bind_param($hapus, "i", $id);
    mysqli_stmt_execute($hapus);

    if (mysqli_stmt_affected_rows($hapus) > 0) {
        $_SESSION['pesan_error'] = 'Data siswa berhasil dihapus!';
    } else {
        $_SESSION['pesan_error'] = 'Data siswa tidak ditemukan!';
    }

} catch (Exception $e) {
    $_SESSION['pesan_error'] = 'Data siswa tidak bisa dihapus karena masih dipakai data lain!';
}

header('Location: kelola_siswa.php');
exit;