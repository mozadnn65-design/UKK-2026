<?php

session_start();

include "config/koneksi.php";

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Cek admin
if ($_SESSION['role'] != "admin") {
    echo "Akses ditolak!";
    exit;
}

// Proses simpan
if (isset($_POST['simpan'])) {

    $nis = $_POST['nis'];
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $alamat = $_POST['alamat'];
    $status_aktif = $_POST['status_aktif'];

    $query = mysqli_query($koneksi, "INSERT INTO t_siswa
        (nis, nisn, nama, jenis_kelamin, tanggal_lahir, alamat, status_aktif)
        VALUES
        ('$nis', '$nisn', '$nama', '$jenis_kelamin', '$tanggal_lahir', '$alamat', '$status_aktif')");

    if ($query) {

        header("Location: kelola_siswa.php");
        exit;

    } else {

        echo "Data siswa gagal ditambahkan.";

    }

}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Tambah Siswa</title>
</head>

<body>

<h2>Tambah Siswa</h2>

<form method="POST">

    NIS:
    <br>
    <input type="text" name="nis" required>

    <br><br>

    NISN:
    <br>
    <input type="text" name="nisn">

    <br><br>

    Nama:
    <br>
    <input type="text" name="nama" required>

    <br><br>

    Jenis Kelamin:
    <br>

    <select name="jenis_kelamin" required>

        <option value="">-- Pilih --</option>

        <option value="L">
            Laki-laki
        </option>

        <option value="P">
            Perempuan
        </option>

    </select>

    <br><br>

    Tanggal Lahir:
    <br>
    <input type="date" name="tanggal_lahir" required>

    <br><br>

    Alamat:
    <br>

    <textarea name="alamat" rows="4" cols="40"></textarea>

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

    <a href="kelola_siswa.php">
        Kembali
    </a>

</form>

</body>

</html>