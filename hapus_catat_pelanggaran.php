```php
<?php
include "config/koneksi.php";

// Mengambil ID penempatan
if (!isset($_GET['id'])) {
    die("ID penempatan tidak ditemukan!");
}

$id = (int) $_GET['id'];

// Mengambil data penempatan
$data = mysqli_query($koneksi, "SELECT * FROM kelas_siswa WHERE id=$id");

if (!$data) {
    die("Query gagal: " . mysqli_error($koneksi));
}

$row = mysqli_fetch_assoc($data);

if (!$row) {
    die("Data penempatan tidak ditemukan!");
}

// Proses update
if (isset($_POST['update'])) {
    $siswa = (int) $_POST['siswa_id'];
    $kelas = (int) $_POST['kelas_id'];
    $tahun = (int) $_POST['tahun_ajaran_id'];
    $tanggal = mysqli_real_escape_string(
        $koneksi, $_POST['tanggal_mulai']
    );

    $update = mysqli_query($koneksi, "UPDATE kelas_siswa SET
        siswa_id=$siswa,
        kelas_id=$kelas,
        tahun_ajaran_id=$tahun,
        tanggal_mulai='$tanggal',
        updated_at=NOW()
        WHERE id=$id");

    if ($update) {
        header("Location: penempatan_siswa.php");
        exit;
    } else {
        echo "Gagal mengubah data: " . mysqli_error($koneksi);
    }
}
?>

<h2>Edit Penempatan Siswa</h2>

<form method="POST">
    ID Siswa:<br>
    <input type="number" name="siswa_id"
        value="<?php echo $row['siswa_id']; ?>" required>
    <br><br>

    ID Kelas:<br>
    <input type="number" name="kelas_id"
        value="<?php echo $row['kelas_id']; ?>" required>
    <br><br>

    ID Tahun Ajaran:<br>
    <input type="number" name="tahun_ajaran_id"
        value="<?php echo $row['tahun_ajaran_id']; ?>" required>
    <br><br>

    Tanggal Mulai:<br>
    <input type="date" name="tanggal_mulai"
        value="<?php echo $row['tanggal_mulai']; ?>" required>
    <br><br>

    <button type="submit" name="update">Update</button>
    <a href="penempatan_siswa.php">Kembali</a>
</form>
```
