<?php
include "config/koneksi.php";

$data = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori ORDER BY id DESC");
?>

<h2>Kelola Jenis Pelanggaran</h2>

<a href="dashboard.php">Kembali</a> |
<a href="tambah_jenis_pelanggaran.php">Tambah Jenis Pelanggaran</a>

<br><br>

<table border="1" cellpadding="8">

<tr>
    <th>No</th>
    <th>Nama Pelanggaran</th>
    <th>Deskripsi</th>
    <th>Status</th>
    <th>Aksi</th>
</tr>

<?php
$no = 1;

while ($row = mysqli_fetch_assoc($data)) {
?>

<tr>
    <td><?php echo $no; ?></td>

    <td><?php echo $row['nama']; ?></td>

    <td><?php echo $row['deskripsi']; ?></td>

    <td>
        <?php
        if ($row['status_aktif'] == 1) {
            echo "Aktif";
        } else {
            echo "Tidak Aktif";
        }
        ?>
    </td>

    <td>
        <a href="edit_jenis_pelanggaran.php?id=<?php echo $row['id']; ?>">
            Edit
        </a>

        |

        <a href="hapus_jenis_pelanggaran.php?id=<?php echo $row['id']; ?>"
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