<?php

// CEK SESSION
include "includes/cek_session.php";

// AMBIL DATA SESSION
$nama = $_SESSION['nama'] ?? $_SESSION['nama_lengkap'] ?? 'Pengguna';
$email = $_SESSION['email'] ?? '-';
$role = $_SESSION['role'] ?? '-';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Aplikasi Pelanggaran Siswa</title>
</head>

<body>

    <h1>Dashboard Aplikasi Pelanggaran Siswa</h1>

    <p>Selamat datang,</p>

    <strong><?php echo htmlspecialchars($nama); ?></strong>

    <p>Email: <?php echo htmlspecialchars($email); ?></p>

    <p>Role: <?php echo htmlspecialchars($role); ?></p>

    <hr>

    <?php if ($role === 'admin'): ?>

        <h3>Admin</h3>

        <p><a href="kelola_guru.php">kelola guru</a></p>
        <p><a href="kelola_siswa.php">kelola siswa</a></p>
        <p><a href="kelola_kelas.php">kelola kelas</a></p>
        <p><a href="kelola_tahun_ajaran.php">Kelola Tahun Ajaran</a></p>
        <p><a href="penempatan_siswa.php">Penempatan Siswa</a></p>
        <p><a href="kelola_wali_kelas.php">Kelola Wali Kelas</a></p>
        <p><a href="kelola_kategori_pelanggaran.php">Kelola Kategori Pelanggaran</a></p>
        <p><a href="Laporan.php">Laporan</a></p>
        <p><a href="kelola_jenis_pelanggaran.php">Kelola Jenis Pelanggaran</a></p>
        <p><a href="catatan_pelanggaran.php">Catatan Pelanggaran</a></p>
        <p><a href="tindakan.php">Tindakan</a></p>
        <p><a href="riwayat.php">Riwayat</a></p>
        <p><a href="rekap_poin.php">Rekap Poin</a></p>

    <?php endif; ?>

    <?php if ($role === 'guru'): ?>

    <h3>Guru</h3>

    <p><a href="catatan_pelanggaran.php">Catatan Pelanggaran</a></p>
    <p><a href="tindakan.php">Tindakan</a></p>
    <p><a href="riwayat.php">Riwayat</a></p>
    <p><a href="rekap_poin.php">Rekap Poin</a></p>

    <?php endif; ?>
    

    <hr>

    <p><a href="logout.php">Logout</a></p>

</body>
</html>