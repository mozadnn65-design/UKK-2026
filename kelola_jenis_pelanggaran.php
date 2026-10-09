
<?php
include "config/koneksi.php";

$data = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran ORDER BY id DESC");
?>

<h2>Kelola Jenis Pelanggaran</h2>

<a href="dashboard.php">Kembali ke Dashboard</a>
<br><br>

<a href="tambah_jenis_pelanggaran.php">+ Tambah Jenis Pelanggaran</a>
<br><br>

<table border="1" cellpadding="8">

<tr>
    <th>No</th>
    <th>ID Kategori</th>
    <th>Kode</th>
    <th>Nama Pelanggaran</th>
    <th>Poin</th>
    <th>Deskripsi</th>
    <th>Status</th>
    <th>Created At</th>
    <th>Updated At</th>
    <th>Aksi</th>
</tr>

<?php
$no = 1;

while ($pelanggaran = mysqli_fetch_assoc($data)) {
?>

<tr>
    <td><?php echo $no; ?></td>
    <td><?php echo $pelanggaran['pelanggaran_kategori_id']; ?></td>
    <td><?php echo $pelanggaran['kode']; ?></td>
    <td><?php echo $pelanggaran['nama']; ?></td>
    <td><?php echo $pelanggaran['poin']; ?></td>
    <td><?php echo $pelanggaran['deskripsi']; ?></td>

    <td>
        <?php
        if ($pelanggaran['status_aktif'] == 1) {
            echo "Aktif";
        } else {
            echo "Tidak Aktif";
        }
        ?>
    </td>

    <td><?php echo $pelanggaran['created_at']; ?></td>
    <td><?php echo $pelanggaran['updated_at']; ?></td>

    <td>
        <a href="edit_jenis_pelanggaran.php?id=<?php echo $pelanggaran['id']; ?>">
            Edit
        </a>
        |
        <a href="hapus_jenis_pelanggaran.php?id=<?php echo $pelanggaran['id']; ?>"
           onclick="return confirm('Yakin ingin menghapus?')">
            Hapus
        </a>
    </td>
</tr>

<?php
$no++;
}
?>

</table>

