
<?php
include "config/koneksi.php";

// Mengambil data penempatan siswa
$data = mysqli_query($koneksi, "
    SELECT 
        t_ks.id,
        t_s.nis,
        t_s.nama AS nama_siswa,
        ta.nama AS tahun_ajaran,
        t_k.nama AS nama_kelas,
        t_ks.tanggal_mulai,
        t_ks.tanggal_selesai,
        t_ks.status_aktif
    FROM t_kelas_siswa t_ks
    JOIN t_siswa t_s ON t_ks.siswa_id = t_s.id
    JOIN t_tahun_ajaran ta ON t_ks.tahun_ajaran_id = ta.id
    JOIN t_kelas t_k ON t_ks.kelas_id = t_k.id
    ORDER BY t_ks.id DESC
" );
?>

<h2>Penempatan Siswa</h2>

<a href="dashboard.php">Kembali ke Dashboard</a>
<br><br>

<a href="tambah_penempatan_siswa.php">+ Tambah Penempatan Siswa</a>
<br><br>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>No</th>
        <th>NIS</th>
        <th>Nama Siswa</th>
        <th>Tahun Ajaran</th>
        <th>Kelas</th>
        <th>Tanggal Mulai</th>
        <th>Tanggal Selesai</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php
    $no = 1;

    while ($row = mysqli_fetch_assoc($data)) {
    ?>
    <tr>
        <td><?php echo $no; ?></td>
        <td><?php echo $row['nis']; ?></td>
        <td><?php echo $row['nama_siswa']; ?></td>
        <td><?php echo $row['tahun_ajaran']; ?></td>
        <td><?php echo $row['nama_kelas']; ?></td>
        <td><?php echo $row['tanggal_mulai']; ?></td>
        <td><?php echo $row['tanggal_selesai']; ?></td>
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
            <a href="edit_penempatan_siswa.php?id=<?php echo $row['id']; ?>">Edit</a> |
            <a href="hapus_penempatan_siswa.php?id=<?php echo $row['id']; ?>"
               onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
        </td>
    </tr>
    <?php
        $no++;
    }
    ?>
</table>

