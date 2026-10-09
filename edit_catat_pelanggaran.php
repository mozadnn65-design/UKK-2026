<?php
include "config/koneksi.php";

$id = $_GET['id'];

$data = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_siswa WHERE id='$id'");
$row = mysqli_fetch_assoc($data);

$siswa = mysqli_query($koneksi, "SELECT * FROM t_siswa");
$pelanggaran = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori");

if (isset($_POST['simpan'])) {
    $siswa_id = $_POST['siswa_id'];
    $pelanggaran_id = $_POST['pelanggaran_id'];

    mysqli_query($koneksi, "UPDATE t_pelanggaran_siswa SET siswa_id='$siswa_id', pelanggaran_id='$pelanggaran_id' WHERE id='$id'");

    header("Location: catatan_pelanggaran.php");
}
?>

<h2>Edit Catatan Pelanggaran</h2>

<form method="POST">
    Siswa:
    <select name="siswa_id">
        <?php while ($s = mysqli_fetch_assoc($siswa)) { ?>
            <option value="<?php echo $s['id']; ?>"
                <?php if ($s['id'] == $row['siswa_id']) echo "selected"; ?>>
                <?php echo $s['nama']; ?>
            </option>
        <?php } ?>
    </select>

    <br><br>

    Pelanggaran:
    <select name="pelanggaran_id">
        <?php while ($p = mysqli_fetch_assoc($pelanggaran)) { ?>
            <option value="<?php echo $p['id']; ?>"
                <?php if ($p['id'] == $row['pelanggaran_id']) echo "selected"; ?>>
                <?php echo $p['nama']; ?>
            </option>
        <?php } ?>
    </select>

    <br><br>
    <button type="submit" name="simpan">Simpan</button>
</form>
