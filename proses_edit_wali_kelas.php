<?php
// proses_edit_wali_kelas.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Metode request tidak valid.');
}

// Ambil & bersihkan input
$id              = (int) ($_POST['id'] ?? 0);
$tahun_ajaran_id = (int) ($_POST['tahun_ajaran_id'] ?? 0);
$kelas_id        = (int) ($_POST['kelas_id'] ?? 0);
$guru_id         = (int) ($_POST['guru_id'] ?? 0);
$tanggal_mulai   = trim($_POST['tanggal_mulai'] ?? '');
$tanggal_selesai = trim($_POST['tanggal_selesai'] ?? '');
$status_aktif    = (int) ($_POST['status_aktif'] ?? 0);

if ($id <= 0) {
    exit('ID wali kelas tidak valid.');
}

// Fungsi bantu: kembali ke form edit dengan pesan error
function kembaliDenganError($id, $pesan)
{
    $_SESSION['error_wali_kelas'] = $pesan;
    $_SESSION['old_wali_kelas'] = [
        'tahun_ajaran_id' => $_POST['tahun_ajaran_id'] ?? '',
        'kelas_id'        => $_POST['kelas_id'] ?? '',
        'guru_id'         => $_POST['guru_id'] ?? '',
        'tanggal_mulai'   => $_POST['tanggal_mulai'] ?? '',
        'tanggal_selesai' => $_POST['tanggal_selesai'] ?? '',
        'status_aktif'    => $_POST['status_aktif'] ?? '1',
    ];
    header('Location: edit_wali_kelas.php?id=' . (int) $id);
    exit;
}

// Validasi dasar
if ($tahun_ajaran_id <= 0 || $kelas_id <= 0 || $guru_id <= 0) {
    kembaliDenganError($id, 'Tahun ajaran, kelas, dan guru wajib dipilih.');
}

// Tahun ajaran harus yang aktif: 2026/2027
$tahunAktifNama = '2026/2027';

$cekTahun = mysqli_prepare(
    $koneksi,
    "SELECT id FROM t_tahun_ajaran WHERE id = ? AND nama = ?"
);
mysqli_stmt_bind_param($cekTahun, "is", $tahun_ajaran_id, $tahunAktifNama);
mysqli_stmt_execute($cekTahun);
if (!mysqli_fetch_assoc(mysqli_stmt_get_result($cekTahun))) {
    kembaliDenganError($id, 'Tahun ajaran harus ' . $tahunAktifNama . ' (tahun ajaran aktif).');
}

if ($tanggal_mulai === '' || $tanggal_selesai === '') {
    kembaliDenganError($id, 'Tanggal mulai dan tanggal selesai wajib diisi.');
}

if ($tanggal_selesai < $tanggal_mulai) {
    kembaliDenganError($id, 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.');
}

if (!in_array($status_aktif, [0, 1], true)) {
    kembaliDenganError($id, 'Status tidak valid.');
}

// Pastikan data yang diedit ada
$cek = mysqli_prepare($koneksi, "SELECT id FROM t_wali_kelas WHERE id = ?");
mysqli_stmt_bind_param($cek, "i", $id);
mysqli_stmt_execute($cek);
if (!mysqli_fetch_assoc(mysqli_stmt_get_result($cek))) {
    exit('Data wali kelas tidak ditemukan.');
}

// Cek duplikat: kelas yang sama + tahun ajaran yang sama
// dan sudah punya wali kelas yang AKTIF (hanya jika data ini disimpan sebagai aktif)
if ($status_aktif === 1) {
    $stmt = mysqli_prepare($koneksi, "
        SELECT k.nama AS kelas, t.nama AS tahun_ajaran, g.nama AS guru
        FROM t_wali_kelas w
        JOIN t_kelas k        ON k.id = w.kelas_id
        JOIN t_tahun_ajaran t ON t.id = w.tahun_ajaran_id
        LEFT JOIN t_guru g    ON g.id = w.guru_id
        WHERE w.kelas_id        = ?
          AND w.tahun_ajaran_id = ?
          AND w.status_aktif    = 1
          AND w.id             != ?
        LIMIT 1
    ");
    mysqli_stmt_bind_param($stmt, "iii", $kelas_id, $tahun_ajaran_id, $id);
    mysqli_stmt_execute($stmt);
    $dup = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($dup) {
        $pesan = 'Kelas ' . $dup['kelas']
               . ' sudah memiliki wali kelas aktif pada tahun ajaran '
               . $dup['tahun_ajaran'];
        if (!empty($dup['guru'])) {
            $pesan .= ' (' . $dup['guru'] . ')';
        }
        $pesan .= '.';
        kembaliDenganError($id, $pesan);
    }
}

// Update data
$update = mysqli_prepare($koneksi, "
    UPDATE t_wali_kelas
    SET tahun_ajaran_id = ?,
        kelas_id        = ?,
        guru_id         = ?,
        tanggal_mulai   = ?,
        tanggal_selesai = ?,
        status_aktif    = ?
    WHERE id = ?
");
mysqli_stmt_bind_param(
    $update,
    "iiissii",
    $tahun_ajaran_id,
    $kelas_id,
    $guru_id,
    $tanggal_mulai,
    $tanggal_selesai,
    $status_aktif,
    $id
);

if (mysqli_stmt_execute($update)) {
    unset($_SESSION['old_wali_kelas']);
    $_SESSION['sukses_wali_kelas'] = 'Data wali kelas berhasil diperbarui.';
    header('Location: kelola_wali_kelas.php');
    exit;
}

kembaliDenganError($id, 'Gagal memperbarui data wali kelas. Silakan coba lagi.');