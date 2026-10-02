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

$id = $_GET['id'];

$query = mysqli_query(
    $koneksi,
    "DELETE FROM t_siswa WHERE id = '$id'"
);

if ($query) {

    header("Location: kelola_siswa.php");
    exit;

} else {

    echo "Data siswa gagal dihapus.";

}

?>