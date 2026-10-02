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

// Menghapus data kelas
$query = mysqli_query(
    $koneksi,
    "DELETE FROM t_kelas WHERE id = '$id'"
);

if ($query) {

    header("Location: kelola_kelas.php");
    exit;

} else {

    echo "Data kelas gagal dihapus.";

}

?>