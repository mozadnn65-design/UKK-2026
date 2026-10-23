
<?php
session_start();
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Ambil ID jenis pelanggaran
$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['pesan_error'] = 'ID jenis pelanggaran tidak valid.';
    header("Location: kelola_jenis_pelanggaran.php");
    exit;
}

// Hapus data
$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_pelanggaran WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        $_SESSION['pesan_sukses'] =
            'Jenis pelanggaran berhasil dihapus.';
    } else {
        $_SESSION['pesan_error'] =
            'Data jenis pelanggaran tidak ditemukan.';
    }
} else {
    $_SESSION['pesan_error'] =
        'Gagal menghapus data. Data mungkin masih digunakan.';
}

mysqli_stmt_close($stmt);

header("Location: kelola_jenis_pelanggaran.php");
exit;
?>
