```php
<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil data dari form
$siswa_id = (int) ($_POST['siswa_id'] ?? 0);
$tahun_ajaran_id = (int) ($_POST['tahun_ajaran_id'] ?? 0);
$kelas_id = (int) ($_POST['kelas_id'] ?? 0);
$pelanggaran_id = (int) ($_POST['pelanggaran_id'] ?? 0);
$pelanggaran_kategori_id = (int) ($_POST['pelanggaran_kategori_id'] ?? 0);
$guru_id = (int) ($_POST['guru_id'] ?? 0);
$tanggal = $_POST['tanggal'] ?? '';
$poin = (int) ($_POST['poin'] ?? 0);
$status = $_POST['status'] ?? 'Proses';
$keterangan = trim($_POST['keterangan'] ?? '');
$tindakan = trim($_POST['tindakan'] ?? '');

// Validasi data
if (
    $siswa_id <= 0 || $tahun_ajaran_id <= 0 ||
    $kelas_id <= 0 || $pelanggaran_id <= 0 ||
    $guru_id <= 0 || $tanggal == ''
) {
    $_SESSION['pesan_error'] = 'Data pelanggaran belum lengkap.';
    header("Location: catat_pelanggaran.php");
    exit;
}

// Simpan pelanggaran
$sql = "INSERT INTO t_pelanggaran_siswa
        (siswa_id, tahun_ajaran_id, kelas_id, pelanggaran_id,
         pelanggaran_kategori_id, guru_id, tanggal, poin,
         status, keterangan, tindakan)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    $_SESSION['pesan_error'] = 'Query simpan gagal: ' . mysqli_error($koneksi);
    header("Location: catat_pelanggaran.php");
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    "iiiiiisisss",
    $siswa_id,
    $tahun_ajaran_id,
    $kelas_id,
    $pelanggaran_id,
    $pelanggaran_kategori_id,
    $guru_id,
    $tanggal,
    $poin,
    $status,
    $keterangan,
    $tindakan
);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['pesan_sukses'] = 'Berhasil! Data pelanggaran siswa sudah disimpan.';
} else {
    $_SESSION['pesan_error'] = 'Gagal menyimpan pelanggaran: ' .
        mysqli_stmt_error($stmt);
}

mysqli_stmt_close($stmt);

header("Location: catat_pelanggaran.php");
exit;
?>
```