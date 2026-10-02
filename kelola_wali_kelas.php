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

// Ambil data wali kelas
$query = mysqli_query($koneksi, "
    SELECT
        t_wali_kelas.id,
        t_tahun_ajaran.nama AS tahun_ajaran,
        t_kelas.nama AS nama_kelas,
        t_kelas.tingkat,
        t_kelas.jurusan,
        t_guru.nip,
        t_guru.nama AS nama_guru,
        t_wali_kelas.tanggal_mulai,
        t_wali_kelas.tanggal_selesai,
        t_wali_kelas.status_aktif

    FROM t_wali_kelas

    LEFT JOIN t_tahun_ajaran
        ON t_wali_kelas.tahun_ajaran_id = t_tahun_ajaran.id

    LEFT JOIN t_kelas
        ON t_wali_kelas.kelas_id = t_kelas.id

    LEFT JOIN t_guru
        ON t_wali_kelas.guru_id = t_guru.id

    ORDER BY t_wali_kelas.id DESC
");

?>

<!DOCTYPE html>
<html>

<head>
    <title>Kelola Wali Kelas</title>
</head>

<body>

<h2>Kelola Wali Kelas</h2>

<a href="dashboard.php">Kembali ke Dashboard</a>

<br><br>

<a href="tambah_wali_kelas.php">
    + Tambah Wali Kelas
</a>

<br><br>

<table border="1" cellpadding="8" cellspacing="0">

    <tr>
        <th>No</th>
        <th>Tahun Ajaran</th>
        <th>Kelas</th>
        <th>Guru / Wali Kelas</th>
        <th>Tanggal Mulai</th>
        <th>Tanggal Selesai</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php

    $no = 1;

    while ($data = mysqli_fetch_assoc($query)) {

    ?>

    <tr>

        <td><?php echo $no; ?></td>

        <td>
            <?php echo $data['tahun_ajaran']; ?>
        </td>

        <td>
            <?php echo $data['nama_kelas']; ?>
            -
            <?php echo $data['tingkat']; ?>
            <?php echo $data['jurusan']; ?>
        </td>

        <td>
            <?php echo $data['nip']; ?>
            -
            <?php echo $data['nama_guru']; ?>
        </td>

        <td>
            <?php echo $data['tanggal_mulai']; ?>
        </td>

        <td>
            <?php echo $data['tanggal_selesai']; ?>
        </td>

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

            <a href="edit_wali_kelas.php?id=<?php echo $data['id']; ?>">
                Edit
            </a>

            |

            <a
                href="hapus_wali_kelas.php?id=<?php echo $data['id']; ?>"
                onclick="return confirm('Yakin ingin menghapus wali kelas ini?')"
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