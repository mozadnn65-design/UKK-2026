<?php

session_start();

include "config/koneksi.php";

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Hanya admin
if ($_SESSION['role'] != "admin") {
    echo "Akses ditolak!";
    exit;
}

// Jika tombol simpan ditekan
if (isset($_POST['simpan'])) {

    $nama = $_POST['nama'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $status_aktif = $_POST['status_aktif'];

    $query = mysqli_query($koneksi, "INSERT INTO t_tahun_ajaran
        (nama, tanggal_mulai, tanggal_selesai, status_aktif)
        VALUES
        ('$nama', '$tanggal_mulai', '$tanggal_selesai', '$status_aktif')");

    if ($query) {
        header("Location: kelola_tahun_ajaran.php");
        exit;
    } else {
        echo "Data tahun ajaran gagal ditambahkan.";
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Tambah Tahun Ajaran</title>
</head>

<body>

<h2>Tambah Tahun Ajaran</h2>

<form method="POST">

    Nama Tahun Ajaran:
    <br>
    <input type="text" name="nama" placeholder="Contoh: 2026/2027" required>

    <br><br>

    Tanggal Mulai:
    <br>
    <input type="date" name="tanggal_mulai" required>

    <br><br>

    Tanggal Selesai:
    <br>
    <input type="date" name="tanggal_selesai" required>

    <br><br>

    Status:
    <br>

    <select name="status_aktif">

        <option value="1">
            Aktif
        </option>

        <option value="0">
            Tidak Aktif
        </option>

    </select>

    <br><br>

    <button type="submit" name="simpan">
        Simpan
    </button>

    <br><br>

    <a href="kelola_tahun_ajaran.php">
        Kembali
    </a>

</form>

</body>

</html>