<?php
include "config/koneksi.php";

$data = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori ORDER BY id DESC");
?>

<h2>Kelola Kategori Pelanggaran</h2>

<a href="dashboard.php">Kembali ke Dashboard</a> 
<br><br>
<a href="tambah_kategori_pelanggaran.php">+ Tambah Kategori</a>

<br><br>

<table border="1" cellpadding="8">

<tr>
    <th>No</th>
    <th>Nama</th>
    <th>Deskripsi</th>
    <th>Status</th>
    <th>Aksi</th>
</tr>

<?php
$no = 1;

while ($kategori = mysqli_fetch_assoc($data)) {
?>

<tr>

    <td><?php echo $no; ?></td>

    <td><?php echo $kategori['nama']; ?></td>

    <td><?php echo $kategori['deskripsi']; ?></td>

    <td>
        <?php
        if ($kategori['status_aktif'] == 1) {
            echo "Aktif";
        } else {
            echo "Tidak Aktif";
        }
        ?>
    </td>

    <td>
        <a href="edit_kategori_pelanggaran.php?id=<?php echo $kategori['id']; ?>">
            Edit
        </a>

        |

        <a href="hapus_kategori_pelanggaran.php?id=<?php echo $kategori['id']; ?>"
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