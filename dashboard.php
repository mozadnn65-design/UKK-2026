
<?php
// Memeriksa session login
include "includes/cek_session.php";

// Mengambil data pengguna
$nama = $_SESSION['nama'] ?? $_SESSION['nama_lengkap'] ?? 'Pengguna';
$email = $_SESSION['email'] ?? '-';
$role = $_SESSION['role'] ?? '-';

// Menghubungkan database
include "config/koneksi.php";

// Menghitung jumlah data dari database
function hitungData($koneksi, $tabel)
{
    $tabelDiizinkan = ['t_siswa', 't_guru', 't_kelas'];

    if (!in_array($tabel, $tabelDiizinkan, true)) {
        return 0;
    }

    $sql = "SELECT COUNT(*) AS total FROM `$tabel`";
    $hasil = mysqli_query($koneksi, $sql);

    if (!$hasil) {
        die(
            "Gagal menghitung data pada tabel " .
            htmlspecialchars($tabel) .
            ". Periksa nama tabel di database. Error: " .
            htmlspecialchars(mysqli_error($koneksi))
        );
    }

    $data = mysqli_fetch_assoc($hasil);

    return (int) $data['total'];
}

// Jumlah data otomatis
$total_siswa = hitungData($koneksi, 't_siswa');
$total_guru = hitungData($koneksi, 't_guru');
$total_kelas = hitungData($koneksi, 't_kelas');
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <?php
        include "layout/sidebar.php";
        ?>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">

            <!-- HEADER DASHBOARD -->
            <div class="bg-dark text-white rounded-3 p-4 mb-4 shadow-sm">

                <h2 class="fw-bold mb-2">
                    Dashboard
                </h2>

                <p class="mb-1 text-white-50">
                    Selamat datang kembali,
                    <b class="text-white">
                        <?php echo htmlspecialchars($nama); ?>
                    </b>!
                </p>

                <p class="mb-0 text-white-50">
                    Sistem Informasi Pelanggaran Siswa
                </p>

            </div>


            <!-- INFORMASI PENGGUNA -->
            <div class="card border-0 shadow-sm mb-4 rounded-3">

                <div class="card-body p-4">

                    <h5 class="fw-bold text-dark mb-3">
                        Informasi Pengguna
                    </h5>

                    <hr>

                    <div class="row">

                        <div class="col-md-4 mb-3 mb-md-0">

                            <small class="text-secondary">
                                Nama Pengguna
                            </small>

                            <p class="fw-semibold text-dark mb-0">
                                <?php echo htmlspecialchars($nama); ?>
                            </p>

                        </div>

                        <div class="col-md-4 mb-3 mb-md-0">

                            <small class="text-secondary">
                                Email
                            </small>

                            <p class="fw-semibold text-dark mb-0">
                                <?php echo htmlspecialchars($email); ?>
                            </p>

                        </div>

                        <div class="col-md-4">

                            <small class="text-secondary">
                                Role Pengguna
                            </small>

                            <p class="mb-0">
                                <span class="badge bg-dark px-3 py-2">
                                    <?php echo htmlspecialchars($role); ?>
                                </span>
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <?php if ($role == 'admin') { ?>

                <!-- DASHBOARD ADMIN -->

                <div class="mb-3">

                    <h5 class="fw-bold text-dark">
                        Statistik Data Sekolah
                    </h5>

                    <p class="text-secondary">
                        Ringkasan jumlah data yang tersimpan di sistem.
                    </p>

                </div>


                <div class="row g-3">

                    <!-- CARD DATA SISWA -->
                    <div class="col-md-4">

                        <div class="card border-0 shadow-sm rounded-3 h-100">

                            <div class="card-body p-4">

                                <p class="text-secondary mb-2">
                                    Total Data Siswa
                                </p>

                                <h2 class="fw-bold text-primary">
                                    <?php echo $total_siswa; ?>
                                </h2>

                                <hr>

                                <a href="kelola_siswa.php"
                                   class="btn btn-primary w-100">
                                    Lihat Data Siswa
                                </a>

                            </div>

                        </div>

                    </div>


                    <!-- CARD DATA GURU -->
                    <div class="col-md-4">

                        <div class="card border-0 shadow-sm rounded-3 h-100">

                            <div class="card-body p-4">

                                <p class="text-secondary mb-2">
                                    Total Data Guru
                                </p>

                                <h2 class="fw-bold text-primary">
                                    <?php echo $total_guru; ?>
                                </h2>

                                <hr>

                                <a href="kelola_guru.php"
                                   class="btn btn-primary w-100"">
                                    Lihat Data Guru
                                </a>

                            </div>

                        </div>

                    </div>


                    <!-- CARD DATA KELAS -->
                    <div class="col-md-4">

                        <div class="card border-0 shadow-sm rounded-3 h-100">

                            <div class="card-body p-4">

                                <p class="text-secondary mb-2">
                                    Total Data Kelas
                                </p>

                                <h2 class="fw-bold text-primary">
                                    <?php echo $total_kelas; ?>
                                </h2>

                                <hr>

                                <a href="kelola_kelas.php"
                                   class="btn btn-primary w-100"">
                                    Lihat Data Kelas
                                </a>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- INFORMASI ADMIN -->
                <div class="card border-0 shadow-sm rounded-3 mt-4">

                    <div class="card-body p-4">

                        <h6 class="fw-bold text-dark">
                            Informasi Administrator
                        </h6>

                        <p class="text-secondary mb-0">
                            Anda memiliki akses untuk mengelola data siswa,
                            data guru, dan data kelas pada sistem.
                        </p>

                    </div>

                </div>


            <?php } elseif ($role == 'guru') { ?>


                <!-- DASHBOARD GURU -->

                <div class="mb-3">

                    <h5 class="fw-bold text-dark">
                        Menu Operasional Guru
                    </h5>

                    <p class="text-secondary">
                        Pilih menu yang ingin Anda gunakan.
                    </p>

                </div>


                <div class="row g-3">

                    <!-- CATAT PELANGGARAN -->
                    <div class="col-md-4">

                        <div class="card border-0 shadow-sm rounded-3 h-100">

                            <div class="card-body p-4">

                                <h5 class="fw-bold text-primary mb-3">
                                    Catat Pelanggaran
                                </h5>

                                <p class="text-secondary">
                                    Mencatat pelanggaran siswa
                                    dengan mudah dan cepat.
                                </p>

                                <a href="catat_pelanggaran.php"
                                   class="btn btn-primary w-100">
                                    Catat Sekarang
                                </a>

                            </div>

                        </div>

                    </div>


                    <!-- RIWAYAT PELANGGARAN -->
                    <div class="col-md-4">

                        <div class="card border-0 shadow-sm rounded-3 h-100">

                            <div class="card-body p-4">

                                <h5 class="fw-bold text-dark mb-3">
                                    Riwayat Pelanggaran
                                </h5>

                                <p class="text-secondary">
                                    Melihat daftar dan riwayat
                                    pelanggaran siswa.
                                </p>

                                <a href="riwayat.php"
                                   class="btn btn-outline-primary w-100">
                                    Lihat Riwayat
                                </a>

                            </div>

                        </div>

                    </div>


                    <!-- REKAP POIN -->
                    <div class="col-md-4">

                        <div class="card border-0 shadow-sm rounded-3 h-100">

                            <div class="card-body p-4">

                                <h5 class="fw-bold text-dark mb-3">
                                    Rekap Poin
                                </h5>

                                <p class="text-secondary">
                                    Melihat jumlah poin dan
                                    rekap pelanggaran siswa.
                                </p>

                                <a href="rekap_poin.php"
                                   class="btn btn-dark w-100">
                                    Lihat Rekap
                                </a>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- INFORMASI GURU -->
                <div class="card border-0 shadow-sm rounded-3 mt-4">

                    <div class="card-body p-4">

                        <h6 class="fw-bold text-dark">
                            Informasi Guru
                        </h6>

                        <p class="text-secondary mb-0">
                            Selamat bekerja. Silakan gunakan menu yang tersedia
                            untuk mencatat dan memantau pelanggaran siswa.
                        </p>

                    </div>

                </div>


            <?php } else { ?>


                <!-- ROLE TIDAK DIKENALI -->

                <div class="alert alert-danger">

                    <h5 class="fw-bold">
                        Role Tidak Dikenali
                    </h5>

                    <p class="mb-0">
                        Role pengguna tidak dikenali.
                        Silakan hubungi administrator.
                    </p>

                </div>


            <?php } ?>


            <!-- FOOTER -->
            <div class="text-center text-secondary mt-5 mb-3">

                <small>
                    Sistem Informasi Pelanggaran Siswa
                    &copy; <?php echo date('Y'); ?>
                </small>

            </div>

        </main>

    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
