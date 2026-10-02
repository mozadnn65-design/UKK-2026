<?php

session_start();

include "config/koneksi.php";

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Hanya admin
if ($_SESSION['role'] != "admin") {
    echo "Akses ditolak!";
    exit;
}

// Ambil data tahun ajaran
$query = mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran ORDER BY id DESC");

?>

<!DOCTYPE html>
<html>

<head>
    <title>Kelola Tahun Ajaran</title>
</head>

<body>

<h2>Kelola Tahun Ajaran</h2>

<a href="dashboard.php">Kembali ke Dashboard</a>

<br><br>

<a href="tambah_tahun_ajaran.php">+ Tambah Tahun Ajaran</a>

<br><br>

<table border="1" cellpadding="8" cellspacing="0">

    <tr>
        <th>No</th>
        <th>Nama Tahun Ajaran</th>
        <th>Tanggal Mulai</th>
        <th>Tanggal Selesai</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php

    $no = 1;

    while ($tahun = mysqli_fetch_assoc($query)) {

    ?>

    <tr>

        <td><?php echo $no; ?></td>

        <td><?php echo $tahun['nama']; ?></td>

        <td><?php echo $tahun['tanggal_mulai']; ?></td>

        <td><?php echo $tahun['tanggal_selesai']; ?></td>

        <td>

            <?php

            if ($tahun['status_aktif'] == 1) {
                echo "Aktif";
            } else {
                echo "Tidak Aktif";
            }

            ?>

        </td>

        <td>

            <a href="edit_tahun_ajaran.php?id=<?php echo $tahun['id']; ?>">
                Edit
            </a>

            |

            <a
                href="hapus_tahun_ajaran.php?id=<?php echo $tahun['id']; ?>"
                onclick="return confirm('Yakin ingin menghapus tahun ajaran ini?')"
            >
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