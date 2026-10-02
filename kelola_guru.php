<?php

session_start();

include "config/koneksi.php";

// Cek apakah sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Cek role
if ($_SESSION['role'] != "admin") {
    echo "Akses ditolak!";
    exit;
}

// Mengambil data guru
$query = mysqli_query($koneksi, "SELECT * FROM t_guru ORDER BY id DESC");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Guru</title>
</head>

<body>

<h2>Kelola Guru</h2>

<a href="dashboard.php">Kembali ke Dashboard</a>
<br><br>

<a href="tambah_guru.php">+ Tambah Guru</a>

<br><br>

<table border="1" cellpadding="8" cellspacing="0">

    <tr>
        <th>No</th>
        <th>NIP</th>
        <th>Nama</th>
        <th>Email</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php

    $no = 1;

    while ($guru = mysqli_fetch_assoc($query)) {

    ?>

    <tr>

        <td><?php echo $no; ?></td>

        <td><?php echo $guru['nip']; ?></td>

        <td><?php echo $guru['nama']; ?></td>

        <td><?php echo $guru['email']; ?></td>

        <td><?php echo $guru['status_aktif']; ?></td>

        <td>

            <a href="edit_guru.php?id=<?php echo $guru['id']; ?>">
                Edit
            </a>

            |

            <a href="hapus_guru.php?id=<?php echo $guru['id']; ?>">
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