<?php
// proses_edit_tahun_ajaran.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = $_POST['id'];
$nama = $_POST['nama'];
$tanggal_mulai = $_POST['tanggal_mulai'];
$tanggal_selesai = $_POST['tanggal_selesai'];
$status_aktif = $_POST['status_aktif'];

if ($nama == '' || $tanggal_mulai == '' || $tanggal_selesai == '') {
    $_SESSION['pesan_error'] = "Semua data harus diisi!";
    header("Location: edit_tahun_ajaran.php?id=$id");
    exit;
}

if ($tanggal_selesai < $tanggal_mulai) {
    $_SESSION['pesan_error'] = "Tanggal selesai tidak boleh sebelum tanggal mulai!";
    header("Location: edit_tahun_ajaran.php?id=$id");
    exit;
}

$sql = "UPDATE t_tahun_ajaran SET
        nama = '$nama',
        tanggal_mulai = '$tanggal_mulai',
        tanggal_selesai = '$tanggal_selesai',
        status_aktif = '$status_aktif',
        updated_at = NOW()
        WHERE id = '$id'";

if (mysqli_query($koneksi, $sql)) {
    $_SESSION['pesan_sukses'] = "Tahun ajaran berhasil diubah!";
    header("Location: kelola_tahun_ajaran.php");
    exit;
} else {
    $_SESSION['pesan_error'] = "Tahun ajaran gagal diubah!";
    header("Location: edit_tahun_ajaran.php?id=$id");
    exit;
}
?>