
<?php
require_once __DIR__ . "/../middleware/auth.php";
require_once __DIR__ . "/../config/koneksi.php";

/* =====================================
   CEK AKSES GURU
===================================== */
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'guru') {
    exit("Anda tidak memiliki akses.");
}

if (!isset($koneksi) || !$koneksi) {
    exit("Koneksi database tidak tersedia.");
}

$user_id = (int) ($_SESSION['user_id'] ?? 0);
$nama = $_SESSION['nama'] ?? 'Wali Kelas';

/* =====================================
   AMBIL DATA GURU
===================================== */
$sql_guru = "
    SELECT id
    FROM t_guru
    WHERE user_id = ?
    LIMIT 1
";

$query_guru = mysqli_prepare($koneksi, $sql_guru);

if (!$query_guru) {
    exit("Error query guru: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($query_guru, "i", $user_id);
mysqli_stmt_execute($query_guru);

$result_guru = mysqli_stmt_get_result($query_guru);
$data_guru = mysqli_fetch_assoc($result_guru);

mysqli_stmt_close($query_guru);

if (!$data_guru) {
    exit("Data guru tidak ditemukan.");
}

$guru_id = (int) $data_guru['id'];

/* =====================================
   CEK WALI KELAS AKTIF
===================================== */
$sql_wali = "
    SELECT
        wk.kelas_id,
        wk.tahun_ajaran_id,
        k.nama AS nama_kelas,
        ta.nama AS tahun_ajaran
    FROM t_wali_kelas wk
    INNER JOIN t_kelas k
        ON k.id = wk.kelas_id
    INNER JOIN t_tahun_ajaran ta
        ON ta.id = wk.tahun_ajaran_id
    WHERE wk.guru_id = ?
      AND wk.status_aktif = 1
      AND ta.status_aktif = 1
    ORDER BY k.nama ASC
";

$query_wali = mysqli_prepare($koneksi, $sql_wali);

if (!$query_wali) {
    exit("Error query wali kelas: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($query_wali, "i", $guru_id);
mysqli_stmt_execute($query_wali);

$result_wali = mysqli_stmt_get_result($query_wali);

$daftar_wali = [];

while ($wali = mysqli_fetch_assoc($result_wali)) {
    $daftar_wali[] = $wali;
}

mysqli_stmt_close($query_wali);

if (count($daftar_wali) == 0) {
    exit("Anda bukan wali kelas aktif.");
}

/* =====================================
   AMBIL REKAP PELANGGARAN
   BERDASARKAN PENEMPATAN
   KELAS SISWA YANG AKTIF
===================================== */
$sql_rekap = "
    SELECT
        ps.nama_siswa,
        k.nama AS nama_kelas,
        ps.nama_pelanggaran,
        ps.nama_guru,
        ps.tanggal,
        ps.keterangan,
        ps.poin,
        ps.tindakan,
        ps.status,
        ta.nama AS tahun_ajaran

    FROM t_pelanggaran_siswa ps

    INNER JOIN t_tahun_ajaran ta
        ON ta.id = ps.tahun_ajaran_id

    INNER JOIN t_kelas_siswa ks
        ON ks.siswa_id = ps.siswa_id
        AND ks.tahun_ajaran_id = ps.tahun_ajaran_id
        AND ks.status_aktif = 1

    INNER JOIN t_kelas k
        ON k.id = ks.kelas_id

    WHERE EXISTS (
        SELECT 1
        FROM t_wali_kelas wk
        WHERE wk.guru_id = ?
          AND wk.kelas_id = ks.kelas_id
          AND wk.tahun_ajaran_id = ks.tahun_ajaran_id
          AND wk.status_aktif = 1
    )

    AND ta.status_aktif = 1

    ORDER BY
        k.nama ASC,
        ps.nama_siswa ASC,
        ps.tanggal DESC
";

$query_rekap = mysqli_prepare($koneksi, $sql_rekap);

if (!$query_rekap) {
    exit("Error query rekap: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($query_rekap, "i", $guru_id);
mysqli_stmt_execute($query_rekap);

$result_rekap = mysqli_stmt_get_result($query_rekap);

/* =====================================
   HITUNG TOTAL REKAP
===================================== */
$total_poin = 0;
$total_selesai = 0;
$total_proses = 0;
$total_pelanggaran = 0;

$rows = [];

while ($data = mysqli_fetch_assoc($result_rekap)) {

    $rows[] = $data;

    $total_pelanggaran++;
    $total_poin += (int) $data['poin'];

    if ($data['status'] == 'Selesai') {
        $total_selesai++;
    } else {
        $total_proses++;
    }
}

mysqli_stmt_close($query_rekap);

$halamanAktif = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Cetak Rekap Pelanggaran - Wali Kelas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        @media print {

            @page {
                size: landscape;
                margin: 10mm;
            }

            body {
                background: white !important;
            }

            .sidebar,
            .d-print-none {
                display: none !important;
            }

            .main-content {
                width: 100% !important;
                max-width: 100% !important;
                flex: 0 0 100% !important;
            }

            .container-fluid {
                padding: 0 !important;
            }

            .table {
                font-size: 10px;
            }

            .table th,
            .table td {
                padding: 5px;
            }

            .card {
                break-inside: avoid;
            }
        }
    </style>

</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <aside class="sidebar col-md-3 col-lg-2 bg-dark text-white min-vh-100 p-3">

            <h5 class="fw-bold mb-1">Sistem Informasi</h5>
            <h6 class="fw-bold">Pelanggaran Siswa</h6>

            <p class="text-secondary small mb-4">
                SMK Muhammadiyah Kota Tasikmalaya
            </p>

            <p class="text-secondary small fw-bold mb-2">
                MENU NAVIGASI
            </p>

            <nav class="nav flex-column gap-1">

                <a href="../dashboard.php"
                   class="nav-link text-white rounded py-2">
                    Dashboard
                </a>

                <a href="data_siswa.php"
                   class="nav-link text-white rounded py-2">
                    Data Siswa
                </a>

                <a href="rekap_pelanggaran.php"
                   class="nav-link text-white rounded py-2">
                    Rekap Pelanggaran
                </a>

                <a href="cetak_rekap.php"
                   class="nav-link active bg-primary text-white rounded py-2">
                    Cetak Rekap
                </a>

                <a href="../about_me.php"
                   class="nav-link text-white rounded py-2">
                    About Me
                </a>

                <hr class="border-secondary my-2">

                <a href="../logout.php"
                   class="nav-link text-danger rounded py-2">
                    Logout
                </a>

            </nav>

        </aside>

        <!-- KONTEN UTAMA -->
        <main class="main-content col-md-9 col-lg-10 p-3 p-md-4">

            <!-- HEADER -->
            <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">

                <div>
                    <h4 class="fw-bold mb-1">
                        Cetak Rekap Pelanggaran
                    </h4>

                    <p class="text-muted mb-0">
                        Wali Kelas: <?= htmlspecialchars($nama) ?>
                    </p>
                </div>

                <div class="d-flex gap-2">

                    <a href="rekap_pelanggaran.php"
                       class="btn btn-secondary">
                        Kembali
                    </a>

                    <button type="button"
                            onclick="window.print()"
                            class="btn btn-primary">
                        Cetak Rekap
                    </button>

                </div>

            </div>

            <!-- ISI LAPORAN -->
            <div class="bg-white p-3 p-md-4 rounded shadow-sm">

                <!-- JUDUL -->
                <div class="text-center mb-4">

                    <h4 class="fw-bold mb-1">
                        SMK MUHAMMADIYAH KOTA TASIKMALAYA
                    </h4>

                    <h5 class="fw-bold">
                        REKAP PELANGGARAN SISWA
                    </h5>

                    <p class="mb-1">
                        Sistem Informasi Pelanggaran Siswa
                    </p>

                    <hr>

                </div>

                <!-- INFORMASI WALI KELAS -->
                <div class="mb-3">

                    <p class="mb-1">
                        <strong>Wali Kelas:</strong>
                        <?= htmlspecialchars($nama) ?>
                    </p>

                    <p class="mb-1">
                        <strong>Kelas Diampu:</strong>

                        <?php
                        $nama_kelas = [];

                        foreach ($daftar_wali as $wali) {
                            $nama_kelas[] = $wali['nama_kelas'];
                        }

                        echo htmlspecialchars(
                            implode(', ', array_unique($nama_kelas))
                        );
                        ?>
                    </p>

                    <p class="mb-1">
                        <strong>Tahun Ajaran:</strong>

                        <?php
                        $nama_tahun = [];

                        foreach ($daftar_wali as $wali) {
                            $nama_tahun[] = $wali['tahun_ajaran'];
                        }

                        echo htmlspecialchars(
                            implode(', ', array_unique($nama_tahun))
                        );
                        ?>
                    </p>

                    <p class="mb-1">
                        <strong>Tanggal Cetak:</strong>
                        <?= date('d-m-Y') ?>
                    </p>

                </div>

                <!-- RINGKASAN -->
                <div class="row g-2 mb-4">

                    <div class="col-md-3 col-6">
                        <div class="border rounded p-2">
                            <small>Total Pelanggaran</small>

                            <h5 class="fw-bold mb-0">
                                <?= number_format($total_pelanggaran, 0, ',', '.') ?>
                            </h5>
                        </div>
                    </div>

                    <div class="col-md-3 col-6">
                        <div class="border rounded p-2">
                            <small>Total Poin</small>

                            <h5 class="fw-bold mb-0">
                                <?= number_format($total_poin, 0, ',', '.') ?>
                            </h5>
                        </div>
                    </div>

                    <div class="col-md-3 col-6">
                        <div class="border rounded p-2">
                            <small>Selesai</small>

                            <h5 class="fw-bold mb-0">
                                <?= number_format($total_selesai, 0, ',', '.') ?>
                            </h5>
                        </div>
                    </div>

                    <div class="col-md-3 col-6">
                        <div class="border rounded p-2">
                            <small>Diproses</small>

                            <h5 class="fw-bold mb-0">
                                <?= number_format($total_proses, 0, ',', '.') ?>
                            </h5>
                        </div>
                    </div>

                </div>

                <!-- TABEL REKAP -->
                <div class="table-responsive">

                    <table class="table table-bordered table-striped align-middle">

                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Jenis Pelanggaran</th>
                                <th>Guru Pencatat</th>
                                <th>Tanggal</th>
                                <th>Poin</th>
                                <th>Tindakan</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (count($rows) > 0) { ?>

                                <?php foreach ($rows as $index => $data) { ?>

                                    <tr>

                                        <td>
                                            <?= $index + 1 ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['nama_siswa']) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['nama_kelas']) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['nama_pelanggaran']) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['nama_guru']) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['tanggal']) ?>
                                        </td>

                                        <td>
                                            <?= (int) $data['poin'] ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['tindakan'] ?? '-') ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['status'] ?? '-') ?>
                                        </td>

                                    </tr>

                                <?php } ?>

                            <?php } else { ?>

                                <tr>
                                    <td colspan="9" class="text-center">
                                        Belum ada data pelanggaran.
                                    </td>
                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>

                <!-- TANDA TANGAN -->
                <div class="row mt-5">

                    <div class="col-7"></div>

                    <div class="col-5 text-center">

                        <p>
                            Tasikmalaya, <?= date('d-m-Y') ?>
                        </p>

                        <p>Wali Kelas</p>

                        <br><br><br>

                        <p class="fw-bold text-decoration-underline mb-0">
                            <?= htmlspecialchars($nama) ?>
                        </p>

                    </div>

                </div>

            </div>

        </main>

    </div>
</div>

</body>
</html>
