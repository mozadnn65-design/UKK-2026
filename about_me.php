<?php
include "config/koneksi.php";
include "includes/cek_session.php";

// Data diri
$nama = "Moza Dina Nabillah";
$kelas = "XII RPL 1";
$sekolah = "SMK Muhammadiyah Tasikmalaya";
$jurusan = "Rekayasa Perangkat Lunak";
$hobi = "Mendengarkan musik, membaca novel, menyanyi";
$cita_cita = "POLWAN";
$foto = "assets\css/img.jpeg";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Saya</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR (kiri) -->
        <?php include "layout/sidebar.php"; ?>

        <!-- ISI HALAMAN (kanan) -->
        <div class="col-md-9 col-lg-10 p-4">

            <div class="mb-4">
                <h2 class="fw-bold text-dark">Tentang Saya</h2>
                <p class="text-secondary">Kenali lebih dekat tentang diri saya</p>
            </div>

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">

                <!-- Profil -->
                <div class="text-white text-center p-5"
                     style="background: linear-gradient(135deg, #080d18, #0756a5);">

                    <img src="<?= $foto; ?>"
                         alt="Foto Profil"
                         class="rounded-circle border border-4 border-white shadow mb-3"
                         width="150"
                         height="150"
                         style="object-fit: cover;">

                    <h2 class="fw-bold"><?= $nama; ?></h2>
                    <p class="mb-1"><?= $kelas; ?></p>
                    <p class="mb-0"><?= $jurusan; ?></p>

                </div>

                <div class="card-body p-4">

                    <!-- Informasi Pribadi -->
                    <h4 class="fw-bold text-primary mb-3">Informasi Pribadi</h4>

                    <div class="row g-3 mb-4">

                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded-3 h-100">
                                <small class="text-secondary">Nama Lengkap</small>
                                <h6 class="fw-bold mb-0"><?= $nama; ?></h6>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded-3 h-100">
                                <small class="text-secondary">Kelas</small>
                                <h6 class="fw-bold mb-0"><?= $kelas; ?></h6>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded-3 h-100">
                                <small class="text-secondary">Sekolah</small>
                                <h6 class="fw-bold mb-0"><?= $sekolah; ?></h6>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded-3 h-100">
                                <small class="text-secondary">Jurusan</small>
                                <h6 class="fw-bold mb-0"><?= $jurusan; ?></h6>
                            </div>
                        </div>

                    </div>

                    <!-- Hobi dan Cita-Cita -->
                    <h4 class="fw-bold text-primary mb-3">Hobi dan Cita-Cita</h4>

                    <div class="row g-3 mb-4">

                        <div class="col-md-6">
                            <div class="p-4 rounded-3 text-white h-100"
                                 style="background: #111827;">
                                <h5 class="fw-bold">Hobi Saya</h5>
                                <p class="mb-0"><?= $hobi; ?></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-4 rounded-3 text-white h-100"
                                 style="background: #0756a5;">
                                <h5 class="fw-bold">Cita-Cita Saya</h5>
                                <h4 class="fw-bold mb-0"><?= $cita_cita; ?></h4>
                            </div>
                        </div>

                    </div>

                    <!-- Tentang Diri -->
                    <h4 class="fw-bold text-primary mb-3">Tentang Diri Saya</h4>

                    <div class="bg-light p-4 rounded-3 mb-4">
                        <p class="mb-0 text-secondary">
                            Halo! Nama saya <?= $nama; ?>. Saya merupakan siswi
                            kelas <?= $kelas; ?> di <?= $sekolah; ?>,
                            jurusan <?= $jurusan; ?>.
                            Saya senang mendengarkan musik, membaca novel,
                            dan menyanyi. Saya juga memiliki cita-cita menjadi
                            seorang POLWAN.
                        </p>
                    </div>

                    <!-- Motivasi -->
                    <div class="text-center p-4 rounded-3 text-white"
                         style="background: linear-gradient(135deg, #080d18, #0756a5);">

                        <h5 class="fw-bold">Motivasi Saya</h5>

                        <p class="mb-0 fst-italic">
                            "Jangan takut bermimpi besar, karena setiap usaha
                            dan doa akan membawa kita lebih dekat dengan tujuan."
                        </p>

                    </div>

                    <div class="mt-4">
                        <a href="dashboard.php" class="btn btn-dark px-4">
                            Kembali ke Halaman Utama
                        </a>
                    </div>

                </div>
            </div>

        </div>
        <!-- /ISI HALAMAN -->

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>