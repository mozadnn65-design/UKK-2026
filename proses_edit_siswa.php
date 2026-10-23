<?php
// proses_edit_siswa.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = $_POST['id'];
$nis = $_POST['nis'];
$nisn = $_POST['nisn'];
$nama = $_POST['nama'];
$jenis_kelamin = $_POST['jenis_kelamin'];
$tanggal_lahir = $_POST['tanggal_lahir'];
$alamat = $_POST['alamat'];
$status_aktif = $_POST['status_aktif'];

// Cek data kosong
if ($nis == '' || $nisn == '' || $nama == '') {
    $_SESSION['pesan_error'] = "Data siswa harus diisi!";
    header("Location: edit_siswa.php?id=$id");
    exit;
}

// Update data siswa
$sql = "UPDATE t_siswa SET
        nis = '$nis',
        nisn = '$nisn',
        nama = '$nama',
        jenis_kelamin = '$jenis_kelamin',
        tanggal_lahir = '$tanggal_lahir',
        alamat = '$alamat',
        status_aktif = '$status_aktif'
        WHERE id = '$id'";

if (mysqli_query($koneksi, $sql)) {
    $_SESSION['pesan_sukses'] = "Data siswa berhasil diubah!";
    header("Location: kelola_siswa.php");
    exit;
} else {
    $_SESSION['pesan_error'] = "Data siswa gagal diubah!";
    header("Location: edit_siswa.php?id=$id");
    exit;
}
?>