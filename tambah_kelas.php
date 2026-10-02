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

    $nama = $_POST['nama'];
    $tingkat = $_POST['tingkat'];
    $jurusan = $_POST['jurusan'];
    $status_aktif = $_POST['status_aktif'];

    $query = mysqli_query($koneksi, "INSERT INTO t_kelas
        (nama, tingkat, jurusan, status_aktif)
        VALUES
        ('$nama', '$tingkat', '$jurusan', '$status_aktif')");

    if ($query) {

        header("Location: kelola_kelas.php");
        exit;

    } else {

        echo "Data kelas gagal ditambahkan.";

    }

}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Tambah Kelas</title>
</head>

<body>

<h2>Tambah Kelas</h2>

<form method="POST">

    Nama Kelas:
    <br>
    <input type="text" name="nama" required>

    <br><br>

    Tingkat:
    <br>
    <input type="text" name="tingkat" required>

    <br><br>

    Jurusan:
    <br>
    <input type="text" name="jurusan" required>

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

    <a href="kelola_kelas.php">
        Kembali
    </a>

</form>

</body>

</html>