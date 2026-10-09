
<?php
include "config/koneksi.php";

$id = $_GET['id'];

mysqli_query($koneksi, "DELETE FROM kelas_siswa WHERE id='$id'");

header("Location: penempatan_siswa.php");
exit;
?>

