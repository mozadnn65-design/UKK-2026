<?php

session_start();

include "config/koneksi.php";

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Cek admin
if ($_SESSION['role'] != "admin") {
    echo "Akses ditolak!";
    exit;
}

// Mengambil data siswa
$query = mysqli_query($koneksi, "SELECT * FROM t_siswa ORDER BY id DESC");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Siswa</title>
</head>

<body>

<h2>Kelola Siswa</h2>

<a href="dashboard.php">Kembali ke Dashboard</a>
<br><br>

<a href="tambah_siswa.php">+ Tambah Siswa</a>
<br><br>

<table border="1" cellpadding="8" cellspacing="0">

    <tr>
        <th>No</th>
        <th>NIS</th>
        <th>NISN</th>
        <th>Nama</th>
        <th>Jenis Kelamin</th>
        <th>Tanggal Lahir</th>
        <th>Alamat</th>
        <th>Status</th>
        <th>Created At</th>
        <th>Updated At</th>
        <th>Aksi</th>
    </tr>

    <?php
    $no = 1;

    while ($siswa = mysqli_fetch_assoc($query)) {
    ?>

    <tr>
        <td><?php echo $no; ?></td>
        <td><?php echo $siswa['nis']; ?></td>
        <td><?php echo $siswa['nisn']; ?></td>
        <td><?php echo $siswa['nama']; ?></td>
        <td><?php echo $siswa['jenis_kelamin']; ?></td>
        <td><?php echo $siswa['tanggal_lahir']; ?></td>
        <td><?php echo $siswa['alamat']; ?></td>

        <td>
            <?php
            if ($siswa['status_aktif'] == 1) {
                echo "Aktif";
            } else {
                echo "Tidak Aktif";
            }
            ?>
        </td>

        <td><?php echo $siswa['created_at']; ?></td>
        <td><?php echo $siswa['updated_at']; ?></td>

        <td>
            <a href="edit_siswa.php?id=<?php echo $siswa['id']; ?>">Edit</a>
            |
            <a href="hapus_siswa.php?id=<?php echo $siswa['id']; ?>"
               onclick="return confirm('Yakin ingin menghapus siswa ini?')">
                Hapus
            </a>
        </td>
    </tr>

    <?php
        $no++;
    }
    ?>

</table>

</body>
</html>
