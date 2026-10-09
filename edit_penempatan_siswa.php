
<?php
include "config/koneksi.php";

$id = $_GET['id'];

$data = mysqli_query($koneksi, "SELECT * FROM t_kelas_siswa WHERE id='$id'");
$row = mysqli_fetch_assoc($data);

if (isset($_POST['update'])) {
    $siswa = $_POST['siswa_id'];
    $kelas = $_POST['kelas_id'];
    $tahun = $_POST['tahun_id'];
    $tanggal = $_POST['tanggal_mulai'];

    mysqli_query($koneksi, "UPDATE kelas_siswa SET
        siswa_id='$siswa',
        kelas_id='$kelas',
        tahun_ajaran_id='$tahun',
        tanggal_mulai='$tanggal',
        updated_at=NOW(),
        WHERE id='$id'");

    header("Location: penempatan_siswa.php");
    exit;
}
?>

<h2>Edit Penempatan Siswa</h2>

<form method="POST">
    ID Siswa:
    <input type="number" name="siswa_id"
        value="<?php echo $row['siswa_id']; ?>" required>
    <br><br>

    ID Kelas:
    <input type="number" name="kelas_id"
        value="<?php echo $row['kelas_id']; ?>" required>
    <br><br>

    ID Tahun Ajaran:
    <input type="number" name="tahun_id"
        value="<?php echo $row['tahun_ajaran_id']; ?>" required>
    <br><br>

    Tanggal Mulai:
    <input type="date" name="tanggal_mulai"
        value="<?php echo $row['tanggal_mulai']; ?>" required>
    <br><br>

    <button type="submit" name="update">Update</button>
    <br><br>
    <a href="penempatan_siswa.php">Kembali</a>
</form>

