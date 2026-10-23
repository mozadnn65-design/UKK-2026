
<?php
// proses_tambah_kelas.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Ambil data dari form
$nama = $_POST['nama'];
$tingkat = $_POST['tingkat'];
$jurusan = $_POST['jurusan'];
$status_aktif = $_POST['status_aktif'];

// Cek apakah data kosong
if ($nama == '' || $tingkat == '' || $jurusan == '') {
    $_SESSION['pesan_error'] = "Semua data harus diisi!";
    header("Location: tambah_kelas.php");
    exit;
}

// Simpan data kelas
$sql = "INSERT INTO t_kelas
        (nama, tingkat, jurusan, status_aktif, created_at, updated_at)
        VALUES
        ('$nama', '$tingkat', '$jurusan', '$status_aktif', NOW(), NOW())";

if (mysqli_query($koneksi, $sql)) {
    $_SESSION['pesan_sukses'] = "Kelas berhasil ditambahkan!";
    header("Location: kelola_kelas.php");
    exit;
} else {
    $_SESSION['pesan_error'] = "Kelas gagal ditambahkan!";
    header("Location: tambah_kelas.php");
    exit;
}
?>
