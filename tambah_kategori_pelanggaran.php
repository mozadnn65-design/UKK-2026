<?php
include "config/koneksi.php";

if (isset($_POST['simpan'])) {

    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $status = $_POST['status'];

    mysqli_query($koneksi, "INSERT INTO t_pelanggaran_kategori
        (nama, deskripsi, status_aktif)
        VALUES
        ('$nama', '$deskripsi', '$status')");

    header("Location: kelola_kategori_pelanggaran.php");
}
?>

<h2>Tambah Kategori Pelanggaran</h2>

<form method="POST">

Nama:
<br>
<input type="text" name="nama">

<br><br>

Deskripsi:
<br>
<textarea name="deskripsi"></textarea>

<br><br>

Status:
<br>
<select name="status">
    <option value="1">Aktif</option>
    <option value="0">Tidak Aktif</option>
</select>

<br><br>

<button name="simpan">Simpan</button>

</form>

<br>

<a href="kelola_kategori_pelanggaran.php">Kembali</a>