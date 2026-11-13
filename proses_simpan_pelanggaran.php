<?php
// proses_simpan_pelanggaran.php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Mengambil data dari form
$siswa_id        = (int) ($_POST['siswa_id'] ?? 0);
$tahun_ajaran_id = (int) ($_POST['tahun_ajaran_id'] ?? 0);
$kelas_id        = (int) ($_POST['kelas_id'] ?? 0);
$pelanggaran_id  = (int) ($_POST['pelanggaran_id'] ?? 0);
$guru_id         = (int) ($_POST['guru_id'] ?? 0);
$tanggal         = trim($_POST['tanggal'] ?? '');
$status          = $_POST['status'] ?? 'Proses';
$keterangan      = trim($_POST['keterangan'] ?? '');
$tindakan        = trim($_POST['tindakan'] ?? '');

// Validasi format tanggal
$tanggal_valid = false;

if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
    $bagian_tanggal = explode('-', $tanggal);

    $tanggal_valid = checkdate(
        (int) $bagian_tanggal[1],
        (int) $bagian_tanggal[2],
        (int) $bagian_tanggal[0]
    );
}

// Validasi data
if (
    $siswa_id <= 0 ||
    $tahun_ajaran_id <= 0 ||
    $kelas_id <= 0 ||
    $pelanggaran_id <= 0 ||
    $guru_id <= 0 ||
    !$tanggal_valid
) {
    $_SESSION['pesan_error'] = 'Data pelanggaran belum lengkap atau tanggal tidak valid.';
    header("Location: catat_pelanggaran.php");
    exit;
}

// Mengambil nama siswa
$q = mysqli_prepare($koneksi, "SELECT nama FROM t_siswa WHERE id = ?");
mysqli_stmt_bind_param($q, "i", $siswa_id);
mysqli_stmt_execute($q);
$nama_siswa = mysqli_fetch_assoc(mysqli_stmt_get_result($q))['nama'] ?? '';

// Mengambil nama kelas
$q = mysqli_prepare($koneksi, "SELECT nama FROM t_kelas WHERE id = ?");
mysqli_stmt_bind_param($q, "i", $kelas_id);
mysqli_stmt_execute($q);
$nama_kelas = mysqli_fetch_assoc(mysqli_stmt_get_result($q))['nama'] ?? '';

// Mengambil data pelanggaran (nama, kategori, poin) dari database
$q = mysqli_prepare(
    $koneksi,
    "SELECT nama, pelanggaran_kategori_id, poin FROM t_pelanggaran WHERE id = ?"
);
mysqli_stmt_bind_param($q, "i", $pelanggaran_id);
mysqli_stmt_execute($q);
$data_pelanggaran = mysqli_fetch_assoc(mysqli_stmt_get_result($q));

$nama_pelanggaran        = $data_pelanggaran['nama'] ?? '';
$pelanggaran_kategori_id = (int) ($data_pelanggaran['pelanggaran_kategori_id'] ?? 0);
$poin                    = (int) ($data_pelanggaran['poin'] ?? 0);

// Jenis pelanggaran harus punya kategori
if ($pelanggaran_kategori_id <= 0) {
    $_SESSION['pesan_error'] = 'Pelanggaran ini belum memiliki kategori. Lengkapi kategorinya di Data Jenis Pelanggaran.';
    header("Location: catat_pelanggaran.php");
    exit;
}

// Mengambil nama guru
$q = mysqli_prepare($koneksi, "SELECT nama FROM t_guru WHERE id = ?");
mysqli_stmt_bind_param($q, "i", $guru_id);
mysqli_stmt_execute($q);
$nama_guru = mysqli_fetch_assoc(mysqli_stmt_get_result($q))['nama'] ?? '';

// Menyimpan data pelanggaran
$sql = "INSERT INTO t_pelanggaran_siswa
        (
            siswa_id,
            nama_siswa,
            tahun_ajaran_id,
            kelas_id,
            nama_kelas,
            pelanggaran_id,
            nama_pelanggaran,
            pelanggaran_kategori_id,
            guru_id,
            nama_guru,
            tanggal,
            poin,
            status,
            keterangan,
            tindakan
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    $_SESSION['pesan_error'] = 'Query simpan gagal: ' . mysqli_error($koneksi);
    header("Location: catat_pelanggaran.php");
    exit;
}

// Urutan tipe: i s i i s i s i i s s i s s s (15 kolom)
mysqli_stmt_bind_param(
    $stmt,
    "isiisisiississs",
    $siswa_id,
    $nama_siswa,
    $tahun_ajaran_id,
    $kelas_id,
    $nama_kelas,
    $pelanggaran_id,
    $nama_pelanggaran,
    $pelanggaran_kategori_id,
    $guru_id,
    $nama_guru,
    $tanggal,
    $poin,
    $status,
    $keterangan,
    $tindakan
);

// Menjalankan query
if (mysqli_stmt_execute($stmt)) {
    $_SESSION['pesan_sukses'] = 'Berhasil! Data pelanggaran siswa sudah disimpan.';
} else {
    $_SESSION['pesan_error'] = 'Gagal menyimpan pelanggaran: ' . mysqli_stmt_error($stmt);
}

mysqli_stmt_close($stmt);

header("Location: catat_pelanggaran.php");
exit;
?>