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

$id = $_GET['id'];

$query = mysqli_query(
    $koneksi,
    "DELETE FROM t_guru WHERE id = '$id'"
);

if ($query) {

    header("Location: kelola_guru.php");
    exit;

} else {

    echo "Data guru gagal dihapus.";

}

?>