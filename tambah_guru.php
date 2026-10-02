<?php

session_start();

include "config/koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] != "admin") {
    echo "Akses ditolak!";
    exit;
}

if (isset($_POST['simpan'])) {

    $nip = $_POST['nip'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $status = $_POST['status_aktif'];

    $query = mysqli_query($koneksi, "INSERT INTO t_guru
        (nip, nama, email, status_aktif, created_at, updated_at)
        VALUES
        ('$nip', '$nama', '$email', '$status', NOW(), NOW())");

    if ($query) {

        header("Location: kelola_guru.php");
        exit;

    } else {

        echo "Data guru gagal ditambahkan.";

    }

}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Tambah Guru</title>
</head>

<body>

<h2>Tambah Guru</h2>

<form method="POST">

    NIP:
    <br>
    <input type="text" name="nip" required>

    <br><br>

    Nama:
    <br>
    <input type="text" name="nama" required>

    <br><br>

    Email:
    <br>
    <input type="email" name="email" required>

    <br><br>

    Status:
    <br>

    <select name="status_aktif">

        <option value="aktif">Aktif</option>

        <option value="tidak aktif">
            Tidak Aktif
        </option>

    </select>

    <br><br>

<button type="submit" name="simpan">
    Simpan
</button>

<br><br>

<a href="kelola_guru.php">Kembali</a>

<br><br>

</form>

</body>

</html>