<?php
include "config/koneksi.php";

$id = $_GET['id'];
$data = mysqli_query($koneksi, "SELECT * FROM t_guru WHERE id='$id'");
$guru = mysqli_fetch_assoc($data);

if (isset($_POST['update'])) {
    mysqli_query($koneksi, " UPDATE t_guru SET
        nip='$_POST[nip]',
        nama='$_POST[nama]',
        email='$_POST[email]',
        status_aktif='$_POST[status]',
        updated_at=now(),
        WHERE id='$id'");

    header("Location: kelola_guru.php");
}
?>

<h2>Edit Guru</h2>

<form method="POST">
NIP: <input name="nip" value="<?php echo $guru['nip']; ?>"><br><br>
Nama: <input name="nama" value="<?php echo $guru['nama']; ?>"><br><br>
Email: <input name="email" value="<?php echo $guru['email']; ?>"><br><br>


Status:
<select name="status">
    <option value="1">Aktif</option>
    <option value="0">Tidak Aktif</option>
</select>

<br><br>
<button name="update">Update</button>
</form>

<a href="kelola_guru.php">Kembali</a>