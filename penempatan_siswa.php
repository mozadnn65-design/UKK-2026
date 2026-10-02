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

// Ambil data penempatan siswa
$query = mysqli_query($koneksi, "
    SELECT
        t_kelas_siswa.id,
        t_siswa.nis,
        t_siswa.nisn,
        t_siswa.nama AS nama_siswa,
        t_kelas.nama AS nama_kelas,
        t_kelas.tingkat,
        t_kelas.jurusan,
        t_tahun_ajaran.nama AS tahun_ajaran,
        t_kelas_siswa.status_aktif
    FROM t_kelas_siswa
    JOIN t_siswa
        ON t_kelas_siswa.siswa_id = t_siswa.id
    JOIN t_kelas
        ON t_kelas_siswa.kelas_id = t_kelas.id
    JOIN t_tahun_ajaran
        ON t_kelas_siswa.tahun_ajaran_id = t_tahun_ajaran.id
    ORDER BY t_kelas_siswa.id DESC
");

?>

<!DOCTYPE html>
<html>

<head>
    <title>Penempatan Siswa</title>
</head>

<body>

<h2>Penempatan Siswa</h2>

<a href="dashboard.php">Kembali ke Dashboard</a>

<br><br>

<a href="tambah_penempatan.php">
    + Tambah Penempatan Siswa
</a>

<br><br>

<table border="1" cellpadding="8" cellspacing="0">

    <tr>
        <th>No</th>
        <th>NIS</th>
        <th>NISN</th>
        <th>Nama Siswa</th>
        <th>Kelas</th>
        <th>Tahun Ajaran</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php

    $no = 1;

    while ($data = mysqli_fetch_assoc($query)) {

    ?>

    <tr>

        <td><?php echo $no; ?></td>

        <td><?php echo $data['nis']; ?></td>

        <td><?php echo $data['nisn']; ?></td>

        <td><?php echo $data['nama_siswa']; ?></td>

        <td>
            <?php echo $data['nama_kelas']; ?>
            -
            <?php echo $data['tingkat']; ?>
            <?php echo $data['jurusan']; ?>
        </td>

        <td><?php echo $data['tahun_ajaran']; ?></td>

        <td>

            <?php

            if ($data['status_aktif'] == 1) {
                echo "Aktif";
            } else {
                echo "Tidak Aktif";
            }

            ?>

        </td>

        <td>

            <a href="edit_penempatan.php?id=<?php echo $data['id']; ?>">
                Edit
            </a>

            |

            <a
                href="hapus_penempatan.php?id=<?php echo $data['id']; ?>"
                onclick="return confirm('Yakin ingin menghapus penempatan siswa ini?')"
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