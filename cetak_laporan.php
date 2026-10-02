<?php

include "config/koneksi.php";

$data = mysqli_query($koneksi, "
    SELECT 
        s.nis,
        s.nama,
        pk.nama AS pelanggaran
    FROM t_pelanggaran_siswa ps
    JOIN t_siswa s ON ps.siswa_id = s.id
    JOIN t_pelanggaran_kategori pk ON ps.pelanggaran_id = pk.id
    ORDER BY ps.id DESC
");

?>

<h2 align="center">LAPORAN PELANGGARAN SISWA</h2>

<table border="1" cellpadding="8" cellspacing="0" width="100%">

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

<script>
window.print();
</script>