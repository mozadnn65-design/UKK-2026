<?php

include "config/koneksi.php";

// mengambil id
$id = $_GET['id'];

// mengambil data berdasarkan id
$data = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori WHERE id='$id'");
$row = mysqli_fetch_assoc($data);

// jika tombol update ditekan
if (isset($_POST['update'])) {

    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $status = $_POST['status'];

    // mengubah data
    mysqli_query($koneksi, "UPDATE t_pelanggaran_kategori SET
        nama='$nama',
        deskripsi='$deskripsi',
        status_aktif='$status',
        WHERE id='$id'
    ");

    // kembali ke halaman kelola
    header("Location: kelola_jenis_pelanggaran.php");
}

?>

<h2>Edit Jenis Pelanggaran</h2>

<form method="POST">

    Nama Pelanggaran:
    <br>
    <input type="text" name="nama" value="<?php echo $row['nama']; ?>">

    <br><br>

    Deskripsi:
    <br>
    <textarea name="deskripsi"><?php echo $row['deskripsi']; ?></textarea>

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

<a href="kelola_jenis_pelanggaran.php">Kembali</a>