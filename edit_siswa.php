<?php
include "config/koneksi.php";

$id = $_GET['id'];
$data = mysqli_query($koneksi, "SELECT * FROM t_siswa WHERE id='$id'");
$siswa = mysqli_fetch_assoc($data);

if (isset($_POST['update'])) {
    mysqli_query($koneksi, "UPDATE t_siswa SET
        nis='$_POST[nis]',
        nisn='$_POST[nisn]',
        nama='$_POST[nama]',
        jenis_kelamin='$_POST[jk]',
        tanggal_lahir='$_POST[tgl]',
        alamat='$_POST[alamat]',
        status_aktif='$_POST[status]'
        WHERE id='$id'");

    header("Location: kelola_siswa.php");
}
?>

<h2>Edit Siswa</h2>

<form method="POST">
NIS: <input name="nis" value="<?php echo $siswa['nis']; ?>"><br><br>
NISN: <input name="nisn" value="<?php echo $siswa['nisn']; ?>"><br><br>
Nama: <input name="nama" value="<?php echo $siswa['nama']; ?>"><br><br>

Jenis Kelamin:
<select name="jk">
    <option value="L">Laki-laki</option>
    <option value="P">Perempuan</option>
</select>

<br><br>

Tanggal Lahir:
<input type="date" name="tgl" value="<?php echo $siswa['tanggal_lahir']; ?>">

<br><br>

Alamat:
<textarea name="alamat"><?php echo $siswa['alamat']; ?></textarea>

<br><br>

Status:
<select name="status">
    <option value="1">Aktif</option>
    <option value="0">Tidak Aktif</option>
</select>

<br><br>
<button name="update">Update</button>
</form>

<a href="kelola_siswa.php">Kembali</a>