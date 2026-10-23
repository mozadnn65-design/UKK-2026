<?php
// proses_edit_kelas.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = $_POST['id'];
$nama = $_POST['nama'];
$tingkat = $_POST['tingkat'];
$jurusan = $_POST['jurusan'];
$status_aktif = $_POST['status_aktif'];

if ($nama == '' || $tingkat == '' || $jurusan == '') {
    $_SESSION['pesan_error'] = "Semua data harus diisi!";
    header("Location: edit_kelas.php?id=$id");
    exit;
}

$sql = "UPDATE t_kelas SET
        nama = '$nama',
        tingkat = '$tingkat',
        jurusan = '$jurusan',
        status_aktif = '$status_aktif',
        updated_at = NOW()
        WHERE id = '$id'";

if (mysqli_query($koneksi, $sql)) {
    $_SESSION['pesan_sukses'] = "Data kelas berhasil diubah!";
    header("Location: kelola_kelas.php");
    exit;
} else {
    $_SESSION['pesan_error'] = "Data kelas gagal diubah!";
    header("Location: edit_kelas.php?id=$id");
    exit;
}
?>