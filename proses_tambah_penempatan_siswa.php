
<?php
// proses_tambah_penempatan_siswa.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$siswa_id = (int) $_POST['siswa_id'];
$tahun_ajaran_id = (int) $_POST['tahun_ajaran_id'];
$kelas_id = (int) $_POST['kelas_id'];
$tanggal_mulai = $_POST['tanggal_mulai'];
$tanggal_selesai = $_POST['tanggal_selesai'] ?: null;
$status_aktif = (int) $_POST['status_aktif'];

if ($siswa_id <= 0 || $tahun_ajaran_id <= 0 || $kelas_id <= 0 || $tanggal_mulai == '') {
    $_SESSION['pesan_error'] = "Data wajib harus diisi!";
    header("Location: tambah_penempatan_siswa.php");
    exit;
}

if ($tanggal_selesai != null && $tanggal_selesai < $tanggal_mulai) {
    $_SESSION['pesan_error'] = "Tanggal selesai tidak boleh sebelum tanggal mulai!";
    header("Location: tambah_penempatan_siswa.php");
    exit;
}

$sql = "INSERT INTO t_kelas_siswa
        (siswa_id, tahun_ajaran_id, kelas_id, tanggal_mulai,
         tanggal_selesai, status_aktif)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param(
    $stmt,
    "iiissi",
    $siswa_id,
    $tahun_ajaran_id,
    $kelas_id,
    $tanggal_mulai,
    $tanggal_selesai,
    $status_aktif
);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['pesan_sukses'] = "Penempatan siswa berhasil ditambahkan!";
    header("Location: penempatan_siswa.php");
} else {
    $_SESSION['pesan_error'] = "Penempatan siswa gagal ditambahkan!";
    header("Location: tambah_penempatan_siswa.php");
}

mysqli_stmt_close($stmt);
exit;
?>
