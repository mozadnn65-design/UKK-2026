
<?php
// Koneksi database
session_start();
include "config/koneksi.php";


// Mengambil data siswa dari database
$siswa = mysqli_query($koneksi, "
    SELECT id, nama
    FROM t_siswa
    ORDER BY nama ASC
");



// Mengambil pilihan nama siswa
$cari = "";

if (isset($_GET['cari'])) {
    $cari = $_GET['cari'];
}

// Mengambil riwayat pelanggaran
if ($cari != "") {
    $query = mysqli_query($koneksi, "
        SELECT * FROM t_pelanggaran_siswa
        WHERE siswa_id = '$cari'
        ORDER BY tanggal DESC
    ");
} else {
    $query = mysqli_query($koneksi, "
        SELECT * FROM t_pelanggaran_siswa
        ORDER BY tanggal DESC
    ");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Catatan Pelanggaran Siswa</title>
</head>
<body>

<h2>Catatan Pelanggaran Siswa</h2>

<a href="dashboard.php">Kembali ke Dashboard</a>

<br><br>

<h3>Cari Riwayat Pelanggaran</h3>

<form method="GET">
    Nama Siswa:

    <select name="cari">
        <option value="">Semua Siswa</option>


<?php while ($data_siswa = mysqli_fetch_assoc($siswa)) { ?>
    <option value="<?php echo $data_siswa['id']; ?>"
        <?php if ($cari == $data_siswa['id']) {
            echo "selected";
        } ?>>
        <?php echo $data_siswa['nama']; ?>
    </option>
<?php } ?>


    </select>

    <button type="submit">Cari</button>
    <a href="catat_pelanggaran.php">Reset</a>
</form>

<br>

<h3>Riwayat Pelanggaran</h3>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>No</th>
        <th>Nama Siswa</th>
        <th>Kelas</th>
        <th>Pelanggaran</th>
        <th>Nama Guru</th>
        <th>Tanggal</th>
        <th>Keterangan</th>
        <th>Poin</th>
        <th>Tindakan</th>
        <th>Status</th>
    </tr>

    <?php
    $no = 1;

    while ($data = mysqli_fetch_assoc($query)) {
    ?>

    <tr>
        <td><?php echo $no++; ?></td>
        <td><?php echo $data['nama_siswa']; ?></td>
        <td><?php echo $data['nama_kelas']; ?></td>
        <td><?php echo $data['nama_pelanggaran']; ?></td>
        <td><?php echo $data['nama_guru']; ?></td>
        <td><?php echo $data['tanggal']; ?></td>
        <td><?php echo $data['keterangan']; ?></td>
        <td><?php echo $data['poin']; ?></td>
        <td><?php echo $data['tindakan']; ?></td>
        <td><?php echo $data['status']; ?></td>
    </tr>

    <?php
    }
    ?>

</table>

</body>
</html>

