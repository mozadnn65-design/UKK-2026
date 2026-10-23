
<?php
// proses_tambah_siswa.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// Pastikan data dikirim melalui form
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: tambah_siswa.php");
    exit;
}

// Mengambil data form
$nis = trim($_POST['nis'] ?? '');
$nisn = trim($_POST['nisn'] ?? '');
$nama = trim($_POST['nama'] ?? '');
$jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
$tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
$alamat = trim($_POST['alamat'] ?? '');
$status_aktif = $_POST['status_aktif'] ?? '1';

// Fungsi untuk menampilkan pesan error
function gagalTambahSiswa($pesan)
{
    $_SESSION['pesan_error'] = $pesan;
    header("Location: tambah_siswa.php");
    exit;
}

// Validasi data wajib
if (
    $nis === '' || $nisn === '' || $nama === '' ||
    $jenis_kelamin === '' || $tanggal_lahir === '' ||
    $alamat === ''
) {
    gagalTambahSiswa("Semua data wajib diisi.");
}

// Validasi NIS
if (!preg_match('/^[0-9]{10}$/', $nis)) {
    gagalTambahSiswa("NIS harus tepat 10 angka.");
}

// Validasi NISN
if (!preg_match('/^[0-9]{10}$/', $nisn)) {
    gagalTambahSiswa("NISN harus tepat 10 angka.");
}

// Validasi jenis kelamin
if (!in_array($jenis_kelamin, ['L', 'P'], true)) {
    gagalTambahSiswa("Jenis kelamin tidak valid.");
}

// Validasi status
if (!in_array((string)$status_aktif, ['0', '1'], true)) {
    gagalTambahSiswa("Status aktif tidak valid.");
}

// Cek tanggal lahir
$tanggal = DateTime::createFromFormat('!Y-m-d', $tanggal_lahir);

if (!$tanggal || $tanggal->format('Y-m-d') !== $tanggal_lahir) {
    gagalTambahSiswa("Tanggal lahir tidak valid.");
}

// Cek NIS atau NISN yang sudah terdaftar
$sql_cek = "SELECT nis FROM t_siswa WHERE nis = ? OR nisn = ?";
$stmt_cek = mysqli_prepare($koneksi, $sql_cek);

if (!$stmt_cek) {
    gagalTambahSiswa("Gagal memeriksa data: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($stmt_cek, "ss", $nis, $nisn);

if (!mysqli_stmt_execute($stmt_cek)) {
    $pesan = mysqli_stmt_error($stmt_cek);
    mysqli_stmt_close($stmt_cek);
    gagalTambahSiswa("Gagal memeriksa data: " . $pesan);
}

mysqli_stmt_store_result($stmt_cek);

if (mysqli_stmt_num_rows($stmt_cek) > 0) {
    mysqli_stmt_close($stmt_cek);
    gagalTambahSiswa("NIS atau NISN sudah terdaftar.");
}

mysqli_stmt_close($stmt_cek);

// Simpan data siswa ke database
$sql_simpan = "INSERT INTO t_siswa
    (nis, nisn, nama, jenis_kelamin, tanggal_lahir, alamat, status_aktif)
    VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt_simpan = mysqli_prepare($koneksi, $sql_simpan);

if (!$stmt_simpan) {
    gagalTambahSiswa("Gagal menyiapkan penyimpanan: " . mysqli_error($koneksi));
}

$status = (int)$status_aktif;

mysqli_stmt_bind_param(
    $stmt_simpan,
    "ssssssi",
    $nis,
    $nisn,
    $nama,
    $jenis_kelamin,
    $tanggal_lahir,
    $alamat,
    $status
);

// Jalankan penyimpanan
if (mysqli_stmt_execute($stmt_simpan)) {
    mysqli_stmt_close($stmt_simpan);

    $_SESSION['pesan_sukses'] = "Data siswa berhasil ditambahkan.";
    header("Location: kelola_siswa.php");
    exit;
}

$pesan_db = mysqli_stmt_error($stmt_simpan);
mysqli_stmt_close($stmt_simpan);

gagalTambahSiswa("Data siswa gagal disimpan: " . $pesan_db);
?>
