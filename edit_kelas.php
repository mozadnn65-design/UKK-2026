<?php
include "config/koneksi.php";

$id = $_GET['id'];
$data = mysqli_query($koneksi, "SELECT * FROM t_kelas WHERE id='$id'");
$kelas = mysqli_fetch_assoc($data);

if (isset($_POST['update'])) {
    mysqli_query($koneksi, "UPDATE t_kelas SET
        nama='$_POST[nama]',
        tingkat='$_POST[tingkat]',
        jurusan='$_POST[jurusan]',
        status_aktif='$_POST[status]'
        WHERE id='$id'");

    header("Location: kelola_kelas.php");
}
?>

<h2>Edit Kelas</h2>

<form method="POST">
Nama: <input name="nama" value="<?php echo $kelas['nama']; ?>"><br><br>
Tingkat: <input name="tingkat" value="<?php echo $kelas['tingkat']; ?>"><br><br>
Jurusan: <input name="jurusan" value="<?php echo $kelas['jurusan']; ?>"><br><br>

Status:
<select name="status">
    <option value="1">Aktif</option>
    <option value="0">Tidak Aktif</option>
</select>

<br><br>
<button name="update">Update</button>
</form>

<a href="kelola_kelas.php">Kembali</a>