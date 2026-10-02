<?php
include "config/koneksi.php";

$id = $_GET['id'];
$data = mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran WHERE id='$id'");
$tahun = mysqli_fetch_assoc($data);

if (isset($_POST['update'])) {
    mysqli_query($koneksi, "UPDATE t_tahun_ajaran SET
        nama='$_POST[nama]',
        tanggal_mulai='$_POST[mulai]',
        tanggal_selesai='$_POST[selesai]',
        status_aktif='$_POST[status]'
        WHERE id='$id'");

    header("Location: kelola_tahun_ajaran.php");
}
?>

<h2>Edit Tahun Ajaran</h2>

<form method="POST">
Nama: <input name="nama" value="<?php echo $tahun['nama']; ?>"><br><br>

Mulai:
<input type="date" name="mulai" value="<?php echo $tahun['tanggal_mulai']; ?>"><br><br>

Selesai:
<input type="date" name="selesai" value="<?php echo $tahun['tanggal_selesai']; ?>"><br><br>

Status:
<select name="status">
    <option value="1">Aktif</option>
    <option value="0">Tidak Aktif</option>
</select>

<br><br>
<button name="update">Update</button>
</form>

<a href="kelola_tahun_ajaran.php">Kembali</a>