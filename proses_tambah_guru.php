
<?php
// proses_tambah_guru.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// Ambil data dari form
$nip = trim($_POST['nip'] ?? '');
$nama = trim($_POST['nama'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$konfirmasi = $_POST['konfirmasi_password'] ?? '';
$status = $_POST['status_aktif'] ?? 'aktif';

// Cek NIP harus 18 angka
if (!preg_match('/^[0-9]{18}$/', $nip)) {
    $_SESSION['pesan_error'] = 'NIP harus terdiri dari 18 angka!';
    header('Location: tambah_guru.php');
    exit;
}

// Cek data tidak boleh kosong
if ($nama == '' || $email == '' || $password == '') {
    $_SESSION['pesan_error'] = 'Semua data wajib diisi!';
    header('Location: tambah_guru.php');
    exit;
}

// Cek email valid
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['pesan_error'] = 'Format email tidak valid!';
    header('Location: tambah_guru.php');
    exit;
}

// Cek password
if ($password != $konfirmasi) {
    $_SESSION['pesan_error'] = 'Konfirmasi password tidak sama!';
    header('Location: tambah_guru.php');
    exit;
}

// Cek email sudah digunakan
$cek = mysqli_prepare($koneksi, "SELECT id FROM t_users WHERE email = ?");
mysqli_stmt_bind_param($cek, "s", $email);
mysqli_stmt_execute($cek);
$hasil = mysqli_stmt_get_result($cek);

if (mysqli_num_rows($hasil) > 0) {
    $_SESSION['pesan_error'] = 'Email sudah digunakan!';
    header('Location: tambah_guru.php');
    exit;
}

// Simpan data dengan aman
mysqli_begin_transaction($koneksi);

try {
    // Enkripsi password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $role = 'guru';

    // Simpan akun ke t_users
    $sql_user = mysqli_prepare(
        $koneksi,
        "INSERT INTO t_users (name, email, password, role)
         VALUES (?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $sql_user,
        "ssss",
        $nama,
        $email,
        $password_hash,
        $role
    );

    mysqli_stmt_execute($sql_user);

    // Ambil ID akun yang baru dibuat
    $user_id = mysqli_insert_id($koneksi);

    // Simpan data guru ke t_guru
    $sql_guru = mysqli_prepare(
        $koneksi,
        "INSERT INTO t_guru
         (nip, nama, email, status_aktif, user_id, created_at, updated_at)
         VALUES (?, ?, ?, ?, ?, NOW(), NOW())"
    );

    mysqli_stmt_bind_param(
        $sql_guru,
        "ssssi",
        $nip,
        $nama,
        $email,
        $status,
        $user_id
    );

    mysqli_stmt_execute($sql_guru);

    // Simpan jika keduanya berhasil
    mysqli_commit($koneksi);

    $_SESSION['pesan_sukses'] = 'Guru berhasil terdaftar!';
    header('Location: kelola_guru.php');
    exit;

} catch (Exception $e) {
    // Batalkan jika terjadi kesalahan
    mysqli_rollback($koneksi);

    $_SESSION['pesan_error'] = 'Gagal menambahkan guru. Periksa kembali data dan struktur tabel.';
    header('Location: tambah_guru.php');
    exit;
}
?>
