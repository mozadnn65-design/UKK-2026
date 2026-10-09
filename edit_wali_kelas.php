<?php
include "config/koneksi.php";

$id = $_GET['id'];
$data = mysqli_query($koneksi, "SELECT * FROM t_wali_kelas WHERE id='$id'");
$wali = mysqli_fetch_assoc($data);

if (isset($_POST['update'])) {
    mysqli_query($koneksi, "UPDATE t_wali_kelas SET
        tahun_ajaran_id='$_POST[tahun]',
        kelas_id='$_POST[kelas]',
        guru_id='$_POST[guru]',
        tanggal_mulai='$_POST[mulai]',
        tanggal_selesai='$_POST[selesai]',
        status_aktif='$_POST[status]',
        WHERE id='$id'");

    header("Location: kelola_wali_kelas.php");
}
?>

<h2>Edit Wali Kelas</h2>

<form method="POST">

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

Guru:
<select name="guru">
<?php
$q = mysqli_query($koneksi, "SELECT * FROM t_guru");
while ($g = mysqli_fetch_assoc($q)) {
    echo "<option value='$g[id]'>$g[nama]</option>";
}
?>
</select>

<br><br>

Mulai:
<input type="date" name="mulai" value="<?php echo $wali['tanggal_mulai']; ?>">

<br><br>

Selesai:
<input type="date" name="selesai" value="<?php echo $wali['tanggal_selesai']; ?>">

<br><br>

Status:
<select name="status">
    <option value="1">Aktif</option>
    <option value="0">Tidak Aktif</option>
</select>

<br><br>

<button name="update">Update</button>

</form>

<a href="kelola_wali_kelas.php">Kembali</a>