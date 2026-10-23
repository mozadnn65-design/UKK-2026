
<?php
// proses_tambah_pelanggaran_kategori.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$nama = trim($_POST['nama'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');
$status_aktif = $_POST['status_aktif'] ?? '1';

if ($nama == '') {
    $_SESSION['pesan_error'] = "Nama kategori harus diisi!";
    header("Location: tambah_pelanggaran_kategori.php");
    exit;
}

$sql = "INSERT INTO t_pelanggaran_kategori
        (nama, deskripsi, status_aktif, created_at, updated_at)
        VALUES (?, ?, ?, NOW(), NOW())";

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    $_SESSION['pesan_error'] = "Query gagal: " . mysqli_error($koneksi);
    header("Location: tambah_pelanggaran_kategori.php");
    exit;
}

mysqli_stmt_bind_param($stmt, "ssi", $nama, $deskripsi, $status_aktif);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);

    $_SESSION['pesan_sukses'] = "Kategori pelanggaran berhasil ditambahkan!";
    header("Location: kelola_pelanggaran_kategori.php");
    exit;
} else {
    $_SESSION['pesan_error'] = "Gagal menyimpan: " . mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);

    header("Location: tambah_pelanggaran_kategori.php");
    exit;
}
?>
