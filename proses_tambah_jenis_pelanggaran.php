<?php
// proses_tambah_jenis_pelanggaran.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Mengambil data dari form
$pelanggaran_kategori_id = (int) ($_POST['kategori_id'] ?? 0);
$kode         = trim($_POST['kode'] ?? '');
$nama         = trim($_POST['nama'] ?? '');
$poin         = (int) ($_POST['poin'] ?? 0);
$deskripsi    = trim($_POST['deskripsi'] ?? '');
$status_aktif = (int) ($_POST['status_aktif'] ?? 1);

// Validasi data wajib
if ($pelanggaran_kategori_id <= 0 || $kode == '' || $nama == '' || $poin < 0) {
    $_SESSION['pesan_error'] = "Data wajib diisi dengan benar!";
    header("Location: tambah_jenis_pelanggaran.php");
    exit;
}

// Cek kode sudah dipakai atau belum
$cek = mysqli_prepare($koneksi, "SELECT id FROM t_pelanggaran WHERE kode = ?");
mysqli_stmt_bind_param($cek, "s", $kode);
mysqli_stmt_execute($cek);
mysqli_stmt_store_result($cek);

if (mysqli_stmt_num_rows($cek) > 0) {
    $_SESSION['pesan_error'] = "Kode pelanggaran '" . $kode . "' sudah digunakan, gunakan kode lain!";
    header("Location: tambah_jenis_pelanggaran.php");
    exit;
}
mysqli_stmt_close($cek);

// Simpan data
$sql = "INSERT INTO t_pelanggaran
        (pelanggaran_kategori_id, kode, nama, poin, deskripsi, status_aktif, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    $_SESSION['pesan_error'] = "Query gagal: " . mysqli_error($koneksi);
    header("Location: tambah_jenis_pelanggaran.php");
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    "issisi",
    $pelanggaran_kategori_id,
    $kode,
    $nama,
    $poin,
    $deskripsi,
    $status_aktif
);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['pesan_sukses'] = "Jenis pelanggaran berhasil ditambahkan!";
    header("Location: kelola_jenis_pelanggaran.php");
    exit;
} else {
    $_SESSION['pesan_error'] = "Gagal menambahkan data: " . mysqli_stmt_error($stmt);
    header("Location: tambah_jenis_pelanggaran.php");
    exit;
}
?>