
<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['pesan_error'] = 'ID pelanggaran tidak valid.';
    header("Location: catat_pelanggaran.php");
    exit;
}

$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_pelanggaran_siswa WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        $_SESSION['pesan_sukses'] = 'Data pelanggaran berhasil dihapus.';
    } else {
        $_SESSION['pesan_error'] = 'Data pelanggaran tidak ditemukan.';
    }
} else {
    $_SESSION['pesan_error'] = 'Gagal menghapus data pelanggaran.';
}

mysqli_stmt_close($stmt);

header("Location: catat_pelanggaran.php");
exit;
?>
