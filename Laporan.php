<?php
// laporan.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

date_default_timezone_set('Asia/Jakarta');

// =====================================================
// DATA SEKOLAH (ubah sesuai data sebenarnya)
// =====================================================
$nama_sekolah    = 'SMK MUHAMMADIYAH';
$alamat_sekolah  = 'Jl. Rumah Sakit No. 29, Kelurahan Empangsari, Kecamatan Tawang, Kota Tasikmalaya, Jawa Barat 46113';
$telepon_sekolah = '(0265) 327380';
$email_sekolah   = 'smksmuhammadiyahtsm@gmail.com';
$kota_ttd        = 'Tasikmalaya';
$logo_sekolah    = 'assets\css/img.logo.png';

// =====================================================
// FUNGSI BANTU
// =====================================================
if (!function_exists('tanggal_indo')) {
    function tanggal_indo($tgl)
    {
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $t = strtotime((string) $tgl);

        if (!$t) {
            return '-';
        }

        return date('d', $t) . ' ' . $bulan[(int) date('n', $t)] . ' ' . date('Y', $t);
    }
}

// Tanggal dengan bulan berupa angka, contoh: 14-11-2026 (dipakai di cetak laporan)
if (!function_exists('tanggal_angka')) {
    function tanggal_angka($tgl)
    {
        $t = strtotime((string) $tgl);

        if (!$t) {
            return '-';
        }

        return date('d-m-Y', $t);
    }
}

if (!function_exists('tanggal_valid')) {
    function tanggal_valid($tanggal)
    {
        if ($tanggal === '') {
            return true;
        }

        $date = DateTime::createFromFormat('!Y-m-d', $tanggal);

        return $date && $date->format('Y-m-d') === $tanggal;
    }
}

if (!function_exists('teks')) {
    function teks($nilai)
    {
        $nilai = trim((string) ($nilai ?? ''));

        return htmlspecialchars($nilai !== '' ? $nilai : '-', ENT_QUOTES, 'UTF-8');
    }
}

// =====================================================
// SESSION DAN HAK AKSES
// =====================================================
$role    = $_SESSION['role'] ?? '';
$id_user = (int) ($_SESSION['user_id'] ?? $_SESSION['id_user'] ?? 0);

if ($role !== 'admin' && $role !== 'guru') {
    http_response_code(403);
    exit('Akses ditolak. Halaman ini hanya dapat diakses oleh Admin dan Guru Wali Kelas.');
}

// Jika guru, pastikan wali kelas aktif
$id_guru = 0;

if ($role === 'guru') {

    $stmt_guru = mysqli_prepare(
        $koneksi,
        "SELECT g.id
         FROM t_guru g
         INNER JOIN t_wali_kelas wk ON wk.guru_id = g.id
         WHERE g.user_id = ?
           AND wk.status_aktif = 1
         LIMIT 1"
    );

    mysqli_stmt_bind_param($stmt_guru, "i", $id_user);
    mysqli_stmt_execute($stmt_guru);

    $hasil_guru = mysqli_stmt_get_result($stmt_guru);
    $data_guru  = mysqli_fetch_assoc($hasil_guru);

    mysqli_stmt_close($stmt_guru);

    if (!$data_guru) {
        http_response_code(403);
        exit('Akses ditolak. Anda bukan Guru Wali Kelas yang aktif.');
    }

    $id_guru = (int) $data_guru['id'];
}

// =====================================================
// FILTER TANGGAL
// =====================================================
$tanggal_mulai   = trim($_GET['tanggal_mulai'] ?? '');
$tanggal_selesai = trim($_GET['tanggal_selesai'] ?? '');

if (!tanggal_valid($tanggal_mulai) || !tanggal_valid($tanggal_selesai)) {
    exit('Format tanggal tidak valid.');
}

if (
    $tanggal_mulai !== '' &&
    $tanggal_selesai !== '' &&
    $tanggal_mulai > $tanggal_selesai
) {
    exit('Tanggal mulai tidak boleh melebihi tanggal selesai.');
}

// =====================================================
// QUERY LAPORAN
// =====================================================
$sql = "SELECT
            ps.id,
            ps.nama_siswa,
            ps.nama_kelas,
            ps.nama_pelanggaran,
            ps.nama_guru,
            ps.tanggal,
            ps.poin,
            ps.tindakan,
            ps.status
        FROM t_pelanggaran_siswa ps
        WHERE 1 = 1";

$types  = "";
$params = [];

if ($role === 'guru') {

    $sql .= " AND EXISTS (
                SELECT 1
                FROM t_wali_kelas wk
                WHERE wk.guru_id = ?
                  AND wk.kelas_id = ps.kelas_id
                  AND wk.tahun_ajaran_id = ps.tahun_ajaran_id
                  AND wk.status_aktif = 1
            )";

    $types   .= "i";
    $params[] = $id_guru;
}

if ($tanggal_mulai !== '') {
    $sql     .= " AND ps.tanggal >= ?";
    $types   .= "s";
    $params[] = $tanggal_mulai;
}

if ($tanggal_selesai !== '') {
    $sql     .= " AND ps.tanggal <= ?";
    $types   .= "s";
    $params[] = $tanggal_selesai;
}

$sql .= " ORDER BY ps.tanggal DESC, ps.id DESC";

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    die("Gagal menyiapkan laporan: " . htmlspecialchars(mysqli_error($koneksi)));
}

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);

$laporan    = [];
$total_poin = 0;

while ($row = mysqli_fetch_assoc($hasil)) {
    $laporan[]   = $row;
    $total_poin += (int) $row['poin'];
}

mysqli_stmt_close($stmt);

$total         = count($laporan);
$tanggal_cetak = tanggal_angka(date('Y-m-d'));
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laporan Pelanggaran Siswa</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
          rel="stylesheet">
</head>

<body>

<!-- =====================================================
     TAMPILAN LAYAR (sidebar + tabel). Disembunyikan saat dicetak.
     ===================================================== -->
<div class="container-fluid d-print-none">
    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">

            <h2 class="fw-bold mb-1">Laporan Pelanggaran Siswa</h2>

            <p class="text-secondary small mb-4">
                Halaman laporan data pelanggaran siswa.
            </p>

            <!-- INFORMASI AKSES -->
            <div class="alert alert-info">
                <?php if ($role === 'admin') { ?>
                    Anda login sebagai <strong>Admin</strong>.
                    Anda dapat melihat seluruh laporan pelanggaran siswa.
                <?php } else { ?>
                    Anda login sebagai <strong>Guru Wali Kelas</strong>.
                    Anda hanya dapat melihat laporan dari kelas yang menjadi tanggung jawab Anda.
                <?php } ?>
            </div>

            <!-- FILTER TANGGAL -->
            <div class="card shadow-sm mb-4">

                <div class="card-header bg-primary text-white">
                    Filter Laporan
                </div>

                <div class="card-body">

                    <form method="GET" action="laporan.php">

                        <div class="row">

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tanggal Mulai</label>

                                <input type="date"
                                       name="tanggal_mulai"
                                       class="form-control"
                                       value="<?= htmlspecialchars($tanggal_mulai); ?>">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tanggal Selesai</label>

                                <input type="date"
                                       name="tanggal_selesai"
                                       class="form-control"
                                       value="<?= htmlspecialchars($tanggal_selesai); ?>">
                            </div>

                            <div class="col-md-4 mb-3 d-flex align-items-end gap-2">

                                <button type="submit" class="btn btn-primary">
                                    Tampilkan
                                </button>

                                <a href="laporan.php" class="btn btn-secondary">
                                    Reset
                                </a>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

            <!-- KOTAK STATISTIK -->
            <div class="row g-3 mb-4">

                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="small text-secondary">Total Data Laporan</div>
                            <div class="fs-2 fw-bold text-primary"><?= number_format($total); ?></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="small text-secondary">Total Poin</div>
                            <div class="fs-2 fw-bold text-primary"><?= number_format($total_poin); ?></div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- TABEL LAPORAN -->
            <div class="card shadow-sm">

                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

                    <span class="fw-bold">Data Laporan Pelanggaran Siswa</span>

                    <!-- Mencetak di halaman yang sama (tanpa sidebar) -->
                    <button type="button"
                            class="btn btn-success btn-sm"
                            onclick="window.print()">
                        Cetak Laporan
                    </button>

                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table id="tabelLaporan"
                               class="table table-striped table-bordered table-hover align-middle w-100">

                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Nama Pelanggaran</th>
                                    <th>Guru Pencatat</th>
                                    <th>Tanggal</th>
                                    <th>Poin</th>
                                    <th>Tindakan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach ($laporan as $row) { ?>

                                    <?php
                                    $status_data = strtolower(trim((string) ($row['status'] ?? '')));

                                    if ($status_data === 'selesai') {
                                        $warna_status = 'bg-success';
                                    } elseif ($status_data === 'proses') {
                                        $warna_status = 'bg-warning text-dark';
                                    } elseif ($status_data === 'ditindaklanjuti') {
                                        $warna_status = 'bg-primary';
                                    } else {
                                        $warna_status = 'bg-secondary';
                                    }
                                    ?>

                                    <tr>
                                        <td></td>

                                        <td><?= teks($row['nama_siswa']); ?></td>

                                        <td><?= teks($row['nama_kelas']); ?></td>

                                        <td><?= teks($row['nama_pelanggaran']); ?></td>

                                        <td><?= teks($row['nama_guru']); ?></td>

                                        <!-- data-order supaya pengurutan memakai tanggal asli -->
                                        <!-- TANGGAL -->
                                        <td>
                                            <?= htmlspecialchars($row['tanggal'] ?? '-'); ?>
                                        </td>

                                        <!-- data-order supaya pengurutan memakai angka, bukan teks -->
                                        <td class="text-center" data-order="<?= (int) $row['poin']; ?>">
                                            <span class="badge bg-warning text-dark">
                                                <?= (int) $row['poin']; ?> poin
                                            </span>
                                        </td>

                                        <td><?= teks($row['tindakan']); ?></td>

                                        <td class="text-center">
                                            <span class="badge <?= $warna_status; ?>">
                                                <?= teks($row['status']); ?>
                                            </span>
                                        </td>
                                    </tr>

                                <?php } ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

            <div class="mt-3">
                <a href="dashboard.php" class="btn btn-secondary">
                    Kembali ke Dashboard
                </a>
            </div>

        </main>

    </div>
</div>

<!-- =====================================================
     TAMPILAN CETAK (hanya muncul saat dicetak, tanpa sidebar)
     ===================================================== -->
<div class="d-none d-print-block p-4">

    <!-- KOP SURAT -->
    <div class="d-flex align-items-center border-bottom border-dark border-3 pb-3 mb-4">

        <?php if ($logo_sekolah !== '' && file_exists($logo_sekolah)) { ?>
            <img src="<?= htmlspecialchars($logo_sekolah); ?>"
                 alt="Logo"
                 width="90"
                 height="90"
                 class="me-3">
        <?php } ?>

        <div class="text-center flex-grow-1">

            <div class="fw-bold small">
                MAJELIS PENDIDIKAN DASAR DAN MENENGAH
            </div>

            <div class="fw-bold small">
                PIMPINAN DAERAH MUHAMMADIYAH
            </div>

            <div class="fs-2 fw-bold text-success">
                <?= htmlspecialchars($nama_sekolah); ?>
            </div>

            <div class="small">
                <?= htmlspecialchars($alamat_sekolah); ?>
            </div>

            <div class="small">
                Telepon: <?= htmlspecialchars($telepon_sekolah); ?>
                |
                Email: <?= htmlspecialchars($email_sekolah); ?>
            </div>

        </div>

    </div>

    <!-- JUDUL -->
    <div class="text-center mb-4">

        <h4 class="fw-bold text-decoration-underline mb-1">
            LAPORAN PELANGGARAN SISWA
        </h4>

        <div>
            Periode:
            <?php if ($tanggal_mulai !== '' || $tanggal_selesai !== '') { ?>
                <?= $tanggal_mulai !== '' ? htmlspecialchars(tanggal_angka($tanggal_mulai)) : 'Awal'; ?>
                s/d
                <?= $tanggal_selesai !== '' ? htmlspecialchars(tanggal_angka($tanggal_selesai)) : 'Sekarang'; ?>
            <?php } else { ?>
                Semua Tanggal
            <?php } ?>
        </div>

    </div>

    <!-- RINGKASAN -->
    <div class="row mb-3 small">

        <div class="col-6">
            <div>Total Data : <strong><?= number_format($total); ?></strong> pelanggaran</div>
            <div>Total Poin : <strong><?= number_format($total_poin); ?></strong></div>
        </div>

        <div class="col-6 text-end">
            Tanggal Cetak : <strong><?= htmlspecialchars($tanggal_cetak); ?></strong>
        </div>

    </div>

    <!-- TABEL CETAK (semua data, tanpa pagination) -->
    <table class="table table-bordered border-dark table-sm align-middle small">

        <thead class="table-success text-center">
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Nama Pelanggaran</th>
                <th>Guru Pencatat</th>
                <th>Tanggal</th>
                <th>Poin</th>
                <th>Tindakan</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>

        <?php if ($total > 0) { ?>

            <?php $no = 1; ?>

            <?php foreach ($laporan as $row) { ?>

                <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td><?= teks($row['nama_siswa']); ?></td>
                    <td class="text-center"><?= teks($row['nama_kelas']); ?></td>
                    <td><?= teks($row['nama_pelanggaran']); ?></td>
                    <td><?= teks($row['nama_guru']); ?></td>
                    <td class="text-center text-nowrap">
                        <?= !empty($row['tanggal']) ? htmlspecialchars(tanggal_angka($row['tanggal'])) : '-'; ?>
                    </td>
                    <td class="text-center"><?= (int) $row['poin']; ?></td>
                    <td><?= teks($row['tindakan']); ?></td>
                    <td class="text-center"><?= teks($row['status']); ?></td>
                </tr>

            <?php } ?>

        <?php } else { ?>

            <tr>
                <td colspan="9" class="text-center py-4">
                    Belum ada data laporan.
                </td>
            </tr>

        <?php } ?>

        </tbody>

    </table>

    <p class="small">
        Total terdapat <strong><?= number_format($total); ?></strong>
        data pelanggaran siswa dalam laporan ini.
    </p>

    <!-- TANDA TANGAN -->
    <div class="row mt-5">

        <div class="col-7"></div>

        <div class="col-5 text-center">

            <div>
                <?= htmlspecialchars($kota_ttd); ?>, <?= htmlspecialchars($tanggal_cetak); ?>
            </div>

            <div>Mengetahui,</div>

            <div>Kepala <?= htmlspecialchars($nama_sekolah); ?></div>

            <div class="py-4 my-3"></div>

            <div class="fw-bold">(........................................)</div>

            <div>NBM. ........................</div>

        </div>

    </div>

</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<!-- DataTables Bootstrap 5 -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<!-- Inisialisasi DataTables -->
<script>
$(document).ready(function () {

    $('#tabelLaporan').DataTable({

        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data laporan",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Data laporan tidak ditemukan",
            emptyTable: "Belum ada data pelanggaran",

            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        },

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        order: [[5, 'desc']],

        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false
            },
            {
                targets: [0, 2, 6, 8],
                className: 'text-center'
            }
        ],

        // Nomor urut otomatis
        drawCallback: function () {

            var api = this.api();
            var info = api.page.info();

            api.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                cell.innerHTML = info.start + i + 1;
            });

        }

    });

});
</script>

</body>
</html>