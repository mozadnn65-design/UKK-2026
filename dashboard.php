<?php
// Memeriksa session login
include "includes/cek_session.php";

// Mengambil data pengguna
$nama = $_SESSION['nama'] ?? $_SESSION['nama_lengkap'] ?? 'Pengguna';
$email = $_SESSION['email'] ?? '-';
$role = $_SESSION['role'] ?? '-';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Pelanggaran Siswa</title>
</head>
<body>

    <h1>Dashboard Aplikasi Pelanggaran Siswa</h1>

    <!-- Menampilkan data pengguna -->
    <p>Selamat datang, <b><?php echo $nama; ?></b></p>
    <p>Email: <?php echo $email; ?></p>
    <p>Role: <?php echo $role; ?></p>

    <hr>

    <?php
    // Menu untuk admin
    if ($role == 'admin') {
        echo "<h3>Menu Admin</h3>";
        echo '<p><a href="kelola_guru.php">Kelola Guru</a></p>';
        echo '<p><a href="kelola_siswa.php">Kelola Siswa</a></p>';
        echo '<p><a href="kelola_kelas.php">Kelola Kelas</a></p>';
        echo '<p><a href="kelola_tahun_ajaran.php">Kelola Tahun Ajaran</a></p>';
        echo '<p><a href="penempatan_siswa.php">Penempatan Siswa</a></p>';
        echo '<p><a href="kelola_wali_kelas.php">Kelola Wali Kelas</a></p>';
        echo '<p><a href="kelola_kategori_pelanggaran.php">Kelola Kategori Pelanggaran</a></p>';
        echo '<p><a href="kelola_jenis_pelanggaran.php">Kelola Jenis Pelanggaran</a></p>';
        echo '<p><a href="Laporan.php">Laporan</a></p>';
    }

    // Menu untuk guru
    if ($role == 'guru') {
        echo "<h3>Menu Guru</h3>";
        echo '<p><a href="catat_pelanggaran.php">Catat Pelanggaran</a></p>';
        echo '<p><a href="tindakan.php">Tindakan</a></p>';
        echo '<p><a href="riwayat.php">Riwayat</a></p>';
        echo '<p><a href="rekap_poin.php">Rekap Poin</a></p>';
    }
    ?>

    <hr>

    <!-- Tombol logout -->
    <p><a href="logout.php">Logout</a></p>

</body>
</html>

