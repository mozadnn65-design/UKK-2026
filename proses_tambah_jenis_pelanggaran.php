```php
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$kode = trim($_POST['kode'] ?? '');
$nama = trim($_POST['nama'] ?? '');
$poin = (int) ($_POST['poin'] ?? 0);
$deskripsi = trim($_POST['deskripsi'] ?? '');
$status_aktif = (int) ($_POST['status_aktif'] ?? 1);

if ($kode == '' || $nama == '' || $poin < 0) {
    $_SESSION['pesan_error'] = "Data wajib diisi dengan benar!";
    header("Location: tambah_jenis_pelanggaran.php");
    exit;
}

$sql = "INSERT INTO t_pelanggaran
        (kode, nama, poin, deskripsi, status_aktif, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, NOW(), NOW())";

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    die("Query gagal: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param(
    $stmt,
    "ssisi",
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
```