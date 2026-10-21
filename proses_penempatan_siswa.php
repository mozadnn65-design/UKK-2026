
<?php
// proses_penempatan_siswa.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// FUNGSI PESAN ERROR

function kembaliDenganError($pesan)
{
    $_SESSION['pesan_error'] = $pesan;
    header('Location: penempatan_siswa.php');
    exit;
}


// MEMERIKSA METODE FORM

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: penempatan_siswa.php');
    exit;
}

=
// MENGAMBIL DATA FORM

$siswa_id = (int) ($_POST['siswa_id'] ?? 0);
$tahun_ajaran_id = (int) ($_POST['tahun_ajaran_id'] ?? 0);
$kelas_id = (int) ($_POST['kelas_id'] ?? 0);
$tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
$tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
$status_aktif = isset($_POST['status_aktif'])
    ? (int) $_POST['status_aktif']
    : -1;


// VALIDASI INPUT

if (
    $siswa_id <= 0 ||
    $tahun_ajaran_id <= 0 ||
    $kelas_id <= 0 ||
    $tanggal_mulai === '' ||
    $tanggal_selesai === '' ||
    !in_array($status_aktif, [0, 1], true)
) {
    kembaliDenganError('Semua data penempatan wajib diisi dengan benar.');
}

// Memeriksa format tanggal
$mulai = DateTime::createFromFormat('!Y-m-d', $tanggal_mulai);
$selesai = DateTime::createFromFormat('!Y-m-d', $tanggal_selesai);

if (
    !$mulai ||
    !$selesai ||
    $mulai->format('Y-m-d') !== $tanggal_mulai ||
    $selesai->format('Y-m-d') !== $tanggal_selesai
) {
    kembaliDenganError('Format tanggal tidak valid.');
}

// Memeriksa urutan tanggal
if ($tanggal_selesai < $tanggal_mulai) {
    kembaliDenganError('Tanggal selesai tidak boleh sebelum tanggal mulai.');
}

// MEMERIKSA DATA SISWA

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id FROM t_siswa
     WHERE id = ? AND status_aktif = 1"
);

if (!$stmt) {
    kembaliDenganError('Gagal memeriksa data siswa.');
}

mysqli_stmt_bind_param($stmt, 'i', $siswa_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

$siswa_valid = mysqli_stmt_num_rows($stmt) > 0;
mysqli_stmt_close($stmt);

if (!$siswa_valid) {
    kembaliDenganError('Siswa tidak ditemukan atau tidak aktif.');
}


// MEMERIKSA TAHUN AJARAN

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id FROM t_tahun_ajaran
     WHERE id = ? AND status_aktif = 1"
);

if (!$stmt) {
    kembaliDenganError('Gagal memeriksa tahun ajaran.');
}

mysqli_stmt_bind_param($stmt, 'i', $tahun_ajaran_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

$tahun_valid = mysqli_stmt_num_rows($stmt) > 0;
mysqli_stmt_close($stmt);

if (!$tahun_valid) {
    kembaliDenganError('Tahun ajaran tidak ditemukan atau tidak aktif.');
}


// MEMERIKSA KELAS

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id FROM t_kelas
     WHERE id = ? AND status_aktif = 1"
);

if (!$stmt) {
    kembaliDenganError('Gagal memeriksa data kelas.');
}

mysqli_stmt_bind_param($stmt, 'i', $kelas_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

$kelas_valid = mysqli_stmt_num_rows($stmt) > 0;
mysqli_stmt_close($stmt);

if (!$kelas_valid) {
    kembaliDenganError('Kelas tidak ditemukan atau tidak aktif.');
}


// CEK PENEMPATAN AKTIF GANDA

if ($status_aktif === 1) {
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT id FROM t_kelas_siswa
         WHERE siswa_id = ?
         AND tahun_ajaran_id = ?
         AND status_aktif = 1"
    );

    if (!$stmt) {
        kembaliDenganError('Gagal memeriksa penempatan siswa.');
    }

    mysqli_stmt_bind_param(
        $stmt,
        'ii',
        $siswa_id,
        $tahun_ajaran_id
    );

    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    $sudah_ditempatkan = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);

    if ($sudah_ditempatkan) {
        kembaliDenganError(
            'Siswa sudah memiliki penempatan aktif pada tahun ajaran ini.'
        );
    }
}


// MENYIMPAN PENEMPATAN SISWA

$stmt = mysqli_prepare(
    $koneksi,
    "INSERT INTO t_kelas_siswa
    (
        siswa_id,
        tahun_ajaran_id,
        kelas_id,
        tanggal_mulai,
        tanggal_selesai,
        status_aktif
    )
    VALUES (?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    kembaliDenganError('Gagal menyiapkan penyimpanan data.');
}

mysqli_stmt_bind_param(
    $stmt,
    'iiissi',
    $siswa_id,
    $tahun_ajaran_id,
    $kelas_id,
    $tanggal_mulai,
    $tanggal_selesai,
    $status_aktif
);


// HASIL PENYIMPANAN

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);

    $_SESSION['pesan_sukses'] = 'Penempatan siswa berhasil disimpan.';
    header('Location: penempatan_siswa.php');
    exit;
}

mysqli_stmt_close($stmt);

kembaliDenganError(
    'Penempatan gagal disimpan. Periksa kembali struktur tabel database.'
);
?>
