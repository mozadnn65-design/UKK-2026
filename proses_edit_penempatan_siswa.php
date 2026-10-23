
<?php
// proses_edit_penempatan_siswa.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = (int) $_POST['id'];
$siswa_id = (int) $_POST['siswa_id'];
$tahun_ajaran_id = (int) $_POST['tahun_ajaran_id'];
$kelas_id = (int) $_POST['kelas_id'];
$tanggal_mulai = $_POST['tanggal_mulai'];
$tanggal_selesai = $_POST['tanggal_selesai'] ?: null;
$status_aktif = (int) $_POST['status_aktif'];

if ($id <= 0 || $siswa_id <= 0 || $tahun_ajaran_id <= 0 ||
    $kelas_id <= 0 || $tanggal_mulai == '') {
    $_SESSION['pesan_error'] = "Data wajib harus diisi!";
    header("Location: edit_penempatan_siswa.php?id=$id");
    exit;
}

if ($tanggal_selesai != null && $tanggal_selesai < $tanggal_mulai) {
    $_SESSION['pesan_error'] = "Tanggal selesai tidak boleh sebelum tanggal mulai!";
    header("Location: edit_penempatan_siswa.php?id=$id");
    exit;
}

$sql = "UPDATE t_kelas_siswa SET
        siswa_id = ?,
        tahun_ajaran_id = ?,
        kelas_id = ?,
        tanggal_mulai = ?,
        tanggal_selesai = ?,
        status_aktif = ?
        WHERE id = ?";

$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param(
    $stmt,
    "iiissii",
    $siswa_id,
    $tahun_ajaran_id,
    $kelas_id,
    $tanggal_mulai,
    $tanggal_selesai,
    $status_aktif,
    $id
);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['pesan_sukses'] = "Penempatan siswa berhasil diubah!";
    header("Location: penempatan_siswa.php");
} else {
    $_SESSION['pesan_error'] = "Penempatan siswa gagal diubah!";
    header("Location: edit_penempatan_siswa.php?id=$id");
}

mysqli_stmt_close($stmt);
exit;
?>
