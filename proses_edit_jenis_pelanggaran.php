<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = (int) ($_POST['id'] ?? 0);
$kode = trim($_POST['kode'] ?? '');
$nama = trim($_POST['nama'] ?? '');
$poin = (int) ($_POST['poin'] ?? 0);
$deskripsi = trim($_POST['deskripsi'] ?? '');
$status_aktif = (int) ($_POST['status_aktif'] ?? 1);

if ($id <= 0 || $kode == '' || $nama == '' || $poin < 0) {
    $_SESSION['pesan_error'] = "Data tidak valid!";
    header("Location: kelola_jenis_pelanggaran.php");
    exit;
}

$sql = "UPDATE t_pelanggaran
        SET kode = ?, nama = ?, poin = ?, deskripsi = ?,
            status_aktif = ?, updated_at = NOW()
        WHERE id = ?";

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    die("Query gagal: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param(
    $stmt,
    "ssisii",
    $kode,
    $nama,
    $poin,
    $deskripsi,
    $status_aktif,
    $id
);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['pesan_sukses'] = "Jenis pelanggaran berhasil diperbarui!";
    header("Location: kelola_jenis_pelanggaran.php");
    exit;
} else {
    $_SESSION['pesan_error'] = "Gagal mengedit data: " . mysqli_stmt_error($stmt);
    header("Location: edit_jenis_pelanggaran.php?id=" . $id);
    exit;
}
?>
