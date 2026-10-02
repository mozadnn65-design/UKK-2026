<?php
include "config/koneksi.php";

$id = $_GET['id'];

$data = mysqli_query(
    $koneksi,
    "SELECT * FROM t_pelanggaran_kategori WHERE id='$id'"
);

$kategori = mysqli_fetch_assoc($data);

if (isset($_POST['update'])) {

    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $status = $_POST['status'];

    mysqli_query($koneksi, "UPDATE t_pelanggaran_kategori SET
        nama='$nama',
        deskripsi='$deskripsi',
        status_aktif='$status'
        WHERE id='$id'
    ");

    header("Location: kelola_kategori_pelanggaran.php");
}
?>

<h2>Edit Kategori Pelanggaran</h2>

<form method="POST">

Nama:
<br>
<input
    type="text"
    name="nama"
    value="<?php echo $kategori['nama']; ?>"
>

<br><br>

Deskripsi:
<br>
<textarea name="deskripsi"><?php echo $kategori['deskripsi']; ?></textarea>

<br><br>

Status:
<br>
<select name="status">

    <option value="1">Aktif</option>
    <option value="0">Tidak Aktif</option>

</select>

<br><br>

<button name="update">Update</button>

</form>

<br>

<a href="kelola_kategori_pelanggaran.php">Kembali</a>