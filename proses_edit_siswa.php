
<?php
// proses_edit_siswa.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = (int) ($_POST['id'] ?? 0);
$nis = trim($_POST['nis'] ?? '');
$nisn = trim($_POST['nisn'] ?? '');
$nama = trim($_POST['nama'] ?? '');
$jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
$tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
$alamat = trim($_POST['alamat'] ?? '');
$status_aktif = (int) ($_POST['status_aktif'] ?? 0);

// Validasi
if ($id <= 0 || $nis == '' || $nisn == '' || $nama == '') {
    $_SESSION['pesan_error'] = "Data siswa harus diisi!";
    header("Location: edit_siswa.php?id=$id");
    exit;
}

// Mulai transaksi
mysqli_begin_transaction($koneksi);

try {

    // Update data siswa
    $sql = "UPDATE t_siswa SET
            nis = ?,
            nisn = ?,
            nama = ?,
            jenis_kelamin = ?,
            tanggal_lahir = ?,
            alamat = ?,
            status_aktif = ?
            WHERE id = ?";

    $stmt = mysqli_prepare($koneksi, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ssssssii",
        $nis,
        $nisn,
        $nama,
        $jenis_kelamin,
        $tanggal_lahir,
        $alamat,
        $status_aktif,
        $id
    );

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception(mysqli_stmt_error($stmt));
    }

    // Samakan status penempatan dengan status siswa
    $sql2 = "UPDATE t_kelas_siswa
             SET status_aktif = ?
             WHERE siswa_id = ?";

    $stmt2 = mysqli_prepare($koneksi, $sql2);

    mysqli_stmt_bind_param(
        $stmt2,
        "ii",
        $status_aktif,
        $id
    );

    if (!mysqli_stmt_execute($stmt2)) {
        throw new Exception(mysqli_stmt_error($stmt2));
    }

    // Simpan perubahan
    mysqli_commit($koneksi);

    $_SESSION['pesan_sukses'] =
        "Data siswa dan status seluruh penempatannya berhasil diperbarui!";

    header("Location: kelola_siswa.php");
    exit;

} catch (Exception $e) {

    mysqli_rollback($koneksi);

    $_SESSION['pesan_error'] =
        "Gagal memperbarui data: " . $e->getMessage();

    header("Location: edit_siswa.php?id=$id");
    exit;
}
?>
