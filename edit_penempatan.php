<?php
include "config/koneksi.php";

$id = $_GET['id'];
$data = mysqli_query($koneksi, "SELECT * FROM t_kelas_siswa WHERE id='$id'");
$p = mysqli_fetch_assoc($data);

if (isset($_POST['update'])) {
    mysqli_query($koneksi, "UPDATE t_kelas_siswa SET
        siswa_id='$_POST[siswa]',
        kelas_id='$_POST[kelas]',
        tahun_ajaran_id='$_POST[tahun]',
        status_aktif='$_POST[status]'
        WHERE id='$id'");

    header("Location: penempatan_siswa.php");
}
?>

<h2>Edit Penempatan</h2>

<form method="POST">

Siswa:
<select name="siswa">
<?php
$q = mysqli_query($koneksi, "SELECT * FROM t_siswa");
while ($s = mysqli_fetch_assoc($q)) {
    echo "<option value='$s[id]'>$s[nama]</option>";
}
?>
</select>

<br><br>

Kelas:
<select name="kelas">
<?php
$q = mysqli_query($koneksi, "SELECT * FROM t_kelas");
while ($k = mysqli_fetch_assoc($q)) {
    echo "<option value='$k[id]'>$k[nama]</option>";
}
?>
</select>

<br><br>

Tahun Ajaran:
<select name="tahun">
<?php
$q = mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran");
while ($t = mysqli_fetch_assoc($q)) {
    echo "<option value='$t[id]'>$t[nama]</option>";
}
?>
</select>

<br><br>

Status:
<select name="status">
    <option value="1">Aktif</option>
    <option value="0">Tidak Aktif</option>
</select>

<br><br>

<button name="update">Update</button>

</form>

<a href="penempatan_siswa.php">Kembali</a>