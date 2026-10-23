
<?php
include "config/koneksi.php";
include "includes/cek_session.php";

$nama = "Moza Dina Nabillah";
$kelas = "XII RPL 1";
$sekolah = "SMK Muhammadiyah Tasikmalaya";
$jurusan = "Rekayasa Perangkat Lunak";
$hobi = "Mendengarkan musik, membaca novel, menyanyi";
$cita_cita = "POLWAN";

$foto = "assets/img/profil_moza.jpg";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Me</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <?php include "layout/sidebar.php"; ?>

        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">

            <div class="mb-4">
                <h3 class="fw-bold text-dark">About Me</h3>
                <p class="text-secondary">
                    Mengenal lebih dekat tentang diri saya.
                </p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-9">

                    <div class="card border-0 shadow rounded-4 overflow-hidden">

                        <div class="card-header text-white text-center py-4 border-0"
                             style="background-color: #111827;">
                            <h5 class="fw-bold mb-2">MY PERSONAL PROFILE</h5>
                            <p class="mb-0 text-white-50">
                                Student of Software Engineering
                            </p>
                        </div>

                        <div class="card-body p-4 p-md-5">

                            <div class="text-center mb-4">

                                <img
                                    src="<?= file_exists($foto) ? $foto . '?v=' . filemtime($foto) : 'https://placehold.co/300x300/1d4ed8/ffffff?text=Foto+Profil'; ?>"
                                    alt="Foto Profil"
                                    width="150"
                                    height="150"
                                    class="rounded-circle border border-4 border-white shadow object-fit-cover mb-3"
                                >

                                <h4 class="fw-bold text-dark mb-1">
                                    <?= $nama; ?>
                                </h4>

                                <p class="text-primary mb-0">
                                    <?= $jurusan; ?>
                                </p>

                            </div>

                            <hr class="my-4">

                            <h6 class="fw-bold text-primary mb-3">
                                INFORMASI PRIBADI
                            </h6>

                            <div class="list-group list-group-flush mb-4">

                                <div class="list-group-item px-0 py-3">
                                    <div class="text-secondary small">Nama Lengkap</div>
                                    <div class="fw-semibold text-dark"><?= $nama; ?></div>
                                </div>

                                <div class="list-group-item px-0 py-3">
                                    <div class="text-secondary small">Kelas</div>
                                    <div class="fw-semibold text-dark"><?= $kelas; ?></div>
                                </div>

                                <div class="list-group-item px-0 py-3">
                                    <div class="text-secondary small">Sekolah</div>
                                    <div class="fw-semibold text-dark"><?= $sekolah; ?></div>
                                </div>

                                <div class="list-group-item px-0 py-3">
                                    <div class="text-secondary small">Jurusan</div>
                                    <div class="fw-semibold text-dark"><?= $jurusan; ?></div>
                                </div>

                                <div class="list-group-item px-0 py-3">
                                    <div class="text-secondary small">Hobi</div>
                                    <div class="fw-semibold text-dark"><?= $hobi; ?></div>
                                </div>

                                <div class="list-group-item px-0 py-3">
                                    <div class="text-secondary small">Cita-cita</div>
                                    <div class="fw-semibold text-dark"><?= $cita_cita; ?></div>
                                </div>

                            </div>

                            <h6 class="fw-bold text-primary mb-3">
                                TENTANG SAYA
                            </h6>

                            <p class="text-secondary lh-lg">
                                Saya adalah siswi jurusan Rekayasa Perangkat
                                Lunak yang tertarik pada teknologi dan
                                pengembangan aplikasi. Saya ingin terus belajar,
                                meningkatkan kemampuan, serta meraih cita-cita
                                melalui usaha, disiplin, dan kerja keras.
                            </p>

                            <div class="alert alert-primary border-0 rounded-3 mt-4">
                                <p class="mb-0 fst-italic">
                                    "Jangan takut bermimpi besar. Setiap langkah
                                    kecil yang dilakukan dengan sungguh-sungguh
                                    akan membawa kita lebih dekat menuju masa depan."
                                </p>
                            </div>

                            <div class="mt-4">
                                <a href="dashboard.php"
                                   class="btn btn-primary px-4">
                                    Kembali ke Dashboard
                                </a>
                            </div>

                        </div>
                    </div>

                    <p class="text-center text-secondary small mt-4">
                        © 2026 About Me — Personal Profile
                    </p>

                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

