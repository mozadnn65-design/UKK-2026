
<?php
include "config/koneksi.php";

// Mengambil data pilihan
$siswa = mysqli_query($koneksi, "SELECT * FROM t_siswa");
$kelas = mysqli_query($koneksi, "SELECT * FROM t_kelas");
$tahun = mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran");

// Menyimpan data
if (isset($_POST['simpan'])) {
    $siswa_id = $_POST['siswa_id'];
    $kelas_id = $_POST['kelas_id'];
    $tahun_id = $_POST['tahun_id'];
    $tanggal = $_POST['tanggal_mulai'];

    mysqli_query($koneksi, "INSERT INTO kelas_siswa
    (siswa_id, kelas_id, tahun_ajaran_id, tanggal_mulai, status_aktif)
    VALUES ('$siswa_id', '$kelas_id', '$tahun_id', '$tanggal', 1)");

    header("Location: penempatan_siswa.php");
    exit;
}
?>

<h2>Tambah Penempatan Siswa</h2>

<form method="POST">
    Siswa:
    <select name="siswa_id" required>
        <?php while ($s = mysqli_fetch_assoc($siswa)) { ?>
            <option value="<?php echo $s['id']; ?>">
                <?php echo $s['nama']; ?>
            </option>
        <?php } ?>
    </select>
    <br><br>

    Kelas:
    <select name="kelas_id" required>
        <?php while ($k = mysqli_fetch_assoc($kelas)) { ?>
            <option value="<?php echo $k['id']; ?>">
                <?php echo $k['nama']; ?>
            </option>
        <?php } ?>
    </select>
    <br><br>

    Tahun Ajaran:
    <select name="tahun_id" required>
        <?php while ($t = mysqli_fetch_assoc($tahun)) { ?>
            <option value="<?php echo $t['id']; ?>">
                <?php echo $t['nama']; ?>
            </option>
        <?php } ?>
    </select>
    <br><br>

    Tanggal Mulai:
    <input type="date" name="tanggal_mulai" required>
    <br><br>

    <button type="submit" name="simpan">Simpan</button>
    <br><br>
    <a href="penempatan_siswa.php">Kembali</a>
</form>

