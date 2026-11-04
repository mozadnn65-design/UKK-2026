php
<?php
// proses_tahun_ajaran.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Ambil data dari form
$nama = trim($_POST['nama'] ?? '');
$tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
$tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
$status_aktif = $_POST['status_aktif'] ?? '';

// Cek data kosong
if ($nama == '' || $tanggal_mulai == '' || $tanggal_selesai == '' || $status_aktif == '') {
    $_SESSION['pesan_error'] = "Semua data harus diisi!";
    header("Location: tambah_tahun_ajaran.php");
    exit;
}

// Validasi status aktif
if (!in_array($status_aktif, ['0', '1'], true)) {
    $_SESSION['pesan_error'] = "Status tahun ajaran tidak valid!";
    header("Location: tambah_tahun_ajaran.php");
    exit;
}

// Cek tanggal
if ($tanggal_selesai < $tanggal_mulai) {
    $_SESSION['pesan_error'] = "Tanggal selesai tidak boleh sebelum tanggal mulai!";
    header("Location: tambah_tahun_ajaran.php");
    exit;
}

// ========================================
// CEK TAHUN AJARAN AKTIF
// ========================================

if ($status_aktif == '1') {

    $cek = mysqli_prepare(
        $koneksi,
        "SELECT id FROM t_tahun_ajaran
         WHERE status_aktif = 1
         LIMIT 1"
    );

    mysqli_stmt_execute($cek);
    $hasil_cek = mysqli_stmt_get_result($cek);

    if (mysqli_num_rows($hasil_cek) > 0) {

        $_SESSION['pesan_error'] =
            "Gagal menambahkan tahun ajaran! 
             Sudah ada tahun ajaran yang berstatus Aktif. 
             Nonaktifkan tahun ajaran sebelumnya terlebih dahulu.";

        header("Location: tambah_tahun_ajaran.php");
        exit;
    }

    mysqli_stmt_close($cek);
}

// ========================================
// SIMPAN DATA TAHUN AJARAN
// ========================================

$sql = mysqli_prepare(
    $koneksi,
    "INSERT INTO t_tahun_ajaran
    (nama, tanggal_mulai, tanggal_selesai, status_aktif, created_at, updated_at)
    VALUES (?, ?, ?, ?, NOW(), NOW())"
);

mysqli_stmt_bind_param(
    $sql,
    "sssi",
    $nama,
    $tanggal_mulai,
    $tanggal_selesai,
    $status_aktif
);

if (mysqli_stmt_execute($sql)) {

    $_SESSION['pesan_sukses'] = "Tahun ajaran berhasil ditambahkan!";

    header("Location: kelola_tahun_ajaran.php");
    exit;

} else {

    // Jika database menolak karena duplikasi tahun ajaran aktif
    if (mysqli_errno($koneksi) == 1062) {
        $_SESSION['pesan_error'] =
            "Gagal! Tahun ajaran aktif sudah tersedia. 
             Hanya boleh ada satu tahun ajaran aktif.";
    } else {
        $_SESSION['pesan_error'] = "Tahun ajaran gagal ditambahkan!";
    }

    header("Location: tambah_tahun_ajaran.php");
    exit;
}

mysqli_stmt_close($sql);
?>