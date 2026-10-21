
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

    $user_id = $_SESSION['user_id'];
    $nip = $_POST['nip'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $status = $_POST['status_aktif'];

    $stmt = mysqli_prepare($koneksi, "INSERT INTO t_guru
        (user_id, nip, nama, email, status_aktif, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, NOW(), NOW())");

    mysqli_stmt_bind_param(
        $stmt,
        "issss",
        $user_id,
        $nip,
        $nama,
        $email,
        $status
    );

    $query = mysqli_stmt_execute($stmt);

    if ($query) {

        header("Location: kelola_guru.php");
        exit;

    } else {

        echo "Data guru gagal ditambahkan.";

    }

    mysqli_stmt_close($stmt);
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
        <option value="tidak aktif">Tidak Aktif</option>
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
