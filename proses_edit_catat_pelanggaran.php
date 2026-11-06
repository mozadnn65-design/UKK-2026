
<?php
// proses_edit_catat_pelanggaran.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Mengambil data dari form
$id = (int) ($_POST['id'] ?? 0);
$siswa_id = (int) ($_POST['siswa_id'] ?? 0);
$pelanggaran_id = (int) ($_POST['pelanggaran_id'] ?? 0);
$guru_id = (int) ($_POST['guru_id'] ?? 0);
$tanggal = trim($_POST['tanggal'] ?? '');
$keterangan = trim($_POST['keterangan'] ?? '');
$tindakan = trim($_POST['tindakan'] ?? '');
$status = trim($_POST['status'] ?? '');

// Validasi data
if (
    $id <= 0 ||
    $siswa_id <= 0 ||
    $pelanggaran_id <= 0 ||
    $guru_id <= 0 ||
    $tanggal == '' ||
    $keterangan == '' ||
    $tindakan == '' ||
    !in_array($status, ['tercatat', 'diproses', 'selesai'])
) {
    $_SESSION['pesan_error'] = "Semua data wajib diisi dengan benar.";
    header("Location: edit_catat_pelanggaran.php?id=" . $id);
    exit;
}

// Mengecek apakah data tersedia
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id FROM t_pelanggaran_siswa WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$cek = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($cek) == 0) {
    $_SESSION['pesan_error'] = "Data catatan pelanggaran tidak ditemukan.";
    header("Location: catat_pelanggaran.php");
    exit;
}

// Memperbarui data pelanggaran
$stmt = mysqli_prepare(
    $koneksi,
    "UPDATE t_pelanggaran_siswa
     SET siswa_id = ?,
         pelanggaran_id = ?,
         guru_id = ?,
         tanggal = ?,
         keterangan = ?,
         tindakan = ?,
         status = ?
     WHERE id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "iiissssi",
    $siswa_id,
    $pelanggaran_id,
    $guru_id,
    $tanggal,
    $keterangan,
    $tindakan,
    $status,
    $id
);

// Menjalankan query
if (mysqli_stmt_execute($stmt)) {
    $_SESSION['pesan_sukses'] = "Catatan pelanggaran berhasil diperbarui.";
    header("Location: catat_pelanggaran.php");
    exit;
} else {
    $_SESSION['pesan_error'] = "Gagal memperbarui catatan pelanggaran.";
    header("Location: edit_catat_pelanggaran.php?id=" . $id);
    exit;
}
?>
