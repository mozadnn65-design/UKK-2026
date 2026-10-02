<?php

include "config/koneksi.php";

$id = $_GET['id'];

mysqli_query($koneksi, "
    DELETE FROM t_pelanggaran_siswa
    WHERE id='$id'
");

header("Location: catatan_pelanggaran.php");

?>