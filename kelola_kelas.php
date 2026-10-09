
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

// Mengambil data kelas
$query = mysqli_query($koneksi, "SELECT * FROM t_kelas ORDER BY id DESC");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Kelas</title>
</head>

<body>

<h2>Kelola Kelas</h2>

<a href="dashboard.php">Kembali ke Dashboard</a>
<br><br>

<a href="tambah_kelas.php">+ Tambah Kelas</a>
<br><br>

<table border="1" cellpadding="8" cellspacing="0">

    <tr>
        <th>No</th>
        <th>Nama Kelas</th>
        <th>Tingkat</th>
        <th>Jurusan</th>
        <th>Status</th>
        <th>Created At</th>
        <th>Updated At</th>
        <th>Aksi</th>
    </tr>

    <?php
    $no = 1;

    while ($kelas = mysqli_fetch_assoc($query)) {
    ?>

    <tr>
        <td><?php echo $no; ?></td>
        <td><?php echo $kelas['nama']; ?></td>
        <td><?php echo $kelas['tingkat']; ?></td>
        <td><?php echo $kelas['jurusan']; ?></td>

        <td>
            <?php
            if ($kelas['status_aktif'] == 1) {
                echo "Aktif";
            } else {
                echo "Tidak Aktif";
            }
            ?>
        </td>

        <td><?php echo $kelas['created_at']; ?></td>
        <td><?php echo $kelas['updated_at']; ?></td>

        <td>
            <a href="edit_kelas.php?id=<?php echo $kelas['id']; ?>">Edit</a>
            |
            <a href="hapus_kelas.php?id=<?php echo $kelas['id']; ?>"
               onclick="return confirm('Yakin ingin menghapus kelas ini?')">
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

