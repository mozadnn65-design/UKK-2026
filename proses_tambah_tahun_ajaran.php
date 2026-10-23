
<?php
// proses_tahun_ajaran.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Ambil data dari form
$nama = $_POST['nama'];
$tanggal_mulai = $_POST['tanggal_mulai'];
$tanggal_selesai = $_POST['tanggal_selesai'];
$status_aktif = $_POST['status_aktif'];

// Cek data kosong
if ($nama == '' || $tanggal_mulai == '' || $tanggal_selesai == '') {
    $_SESSION['pesan_error'] = "Semua data harus diisi!";
    header("Location: tambah_tahun_ajaran.php");
    exit;
}

// Cek tanggal
if ($tanggal_selesai < $tanggal_mulai) {
    $_SESSION['pesan_error'] = "Tanggal selesai tidak boleh sebelum tanggal mulai!";
    header("Location: tambah_tahun_ajaran.php");
    exit;
}

// Simpan data tahun ajaran
$sql = "INSERT INTO t_tahun_ajaran
        (nama, tanggal_mulai, tanggal_selesai, status_aktif, created_at, updated_at)
        VALUES
        ('$nama', '$tanggal_mulai', '$tanggal_selesai', '$status_aktif', NOW(), NOW())";

if (mysqli_query($koneksi, $sql)) {
    $_SESSION['pesan_sukses'] = "Tahun ajaran berhasil ditambahkan!";
    header("Location: kelola_tahun_ajaran.php");
    exit;
} else {
    $_SESSION['pesan_error'] = "Tahun ajaran gagal ditambahkan!";
    header("Location: tambah_tahun_ajaran.php");
    exit;
}
?>
