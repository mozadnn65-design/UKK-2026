<?php
// proses_edit_wali_kelas.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = $_POST['id'];
$guru_id = $_POST['guru_id'];
$kelas_id = $_POST['kelas_id'];
$tahun_ajaran_id = $_POST['tahun_ajaran_id'];

if ($guru_id == '' || $kelas_id == '' || $tahun_ajaran_id == '') {
    $_SESSION['pesan_error'] = "Semua data harus diisi!";
    header("Location: edit_wali_kelas.php?id=$id");
    exit;
}

$sql = "UPDATE t_wali_kelas SET
        guru_id = '$guru_id',
        kelas_id = '$kelas_id',
        tahun_ajaran_id = '$tahun_ajaran_id',
        updated_at = NOW()
        WHERE id = '$id'";

if (mysqli_query($koneksi, $sql)) {
    $_SESSION['pesan_sukses'] = "Data wali kelas berhasil diubah!";
    header("Location: kelola_wali_kelas.php");
    exit;
} else {
    $_SESSION['pesan_error'] = "Data wali kelas gagal diubah!";
    header("Location: edit_wali_kelas.php?id=$id");
    exit;
}
?>