<?php
include "config/koneksi.php";

$id = $_GET['id'];

mysqli_query(
    $koneksi,
    "DELETE FROM t_pelanggaran_kategori WHERE id='$id'"
);

header("Location: kelola_kategori_pelanggaran.php");
?>