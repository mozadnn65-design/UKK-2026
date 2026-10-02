<?php

include "config/koneksi.php";

$data = mysqli_query($koneksi, "
    SELECT 
        ps.id,
        s.nis,
        s.nama,
        pk.nama AS pelanggaran
    FROM t_pelanggaran_siswa ps
    JOIN t_siswa s ON ps.siswa_id = s.id
    JOIN t_pelanggaran_kategori pk ON ps.pelanggaran_id = pk.id
    ORDER BY ps.id DESC
");

?>

<h2>Laporan Pelanggaran Siswa</h2>

<a href="dashboard.php">Kembali ke Dashboard</a>

<br><br>

<a href="cetak_laporan.php" target="_blank">
    Cetak Laporan
</a>

&nbsp;

<a href="export_laporan.php">
    Export Excel
</a>

<br><br>

<table border="1" cellpadding="8">

<tr>
    <th>No</th>
    <th>NIS</th>
    <th>Nama Siswa</th>
    <th>Jenis Pelanggaran</th>
</tr>

<?php

$no = 1;

while ($row = mysqli_fetch_assoc($data)) {

?>

<tr>

    <td><?php echo $no; ?></td>

    <td><?php echo $row['nis']; ?></td>

    <td><?php echo $row['nama']; ?></td>

    <td><?php echo $row['pelanggaran']; ?></td>

</tr>

<?php

$no++;

}

?>

</table>