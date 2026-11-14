<?php
// rekap_poin.php

// ============================================
// 1. KONEKSI DAN SESSION
// ============================================
include 'includes/cek_session.php';
include 'config/koneksi.php';

date_default_timezone_set('Asia/Jakarta');

// ============================================
// DATA SEKOLAH (ubah sesuai data sebenarnya)
// ============================================
$nama_sekolah    = 'SMK MUHAMMADIYAH';
$alamat_sekolah  = 'Jl. Rumah Sakit No. 29, Kelurahan Empangsari, Kecamatan Tawang, Kota Tasikmalaya, Jawa Barat 46113';
$telepon_sekolah = '(0265) 327380';
$email_sekolah   = 'smksmuhammadiyahtsm@gmail.com';
$kota_ttd        = 'Tasikmalaya';
$logo_sekolah    = 'assets\css/img.logo.png';

// ============================================
// FUNGSI BANTU
// ============================================
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

if (!function_exists('teks')) {
    function teks($nilai)
    {
        $nilai = trim((string) ($nilai ?? ''));

        return htmlspecialchars($nilai !== '' ? $nilai : '-', ENT_QUOTES, 'UTF-8');
    }
}

// ============================================
// 2. MENGAMBIL INPUT PENCARIAN DAN FILTER
// ============================================
$cari         = trim($_GET['cari'] ?? '');
$kelas_filter = (int) ($_GET['kelas'] ?? 0);

// ============================================
// 3. MENGAMBIL DATA KELAS UNTUK FILTER
// ============================================
$query_kelas = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_kelas
     ORDER BY nama ASC"
);

if (!$query_kelas) {
    die("Gagal mengambil data kelas: " . htmlspecialchars(mysqli_error($koneksi)));
}

$data_kelas        = [];
$nama_kelas_dipilih = 'Semua Kelas';

while ($kelas = mysqli_fetch_assoc($query_kelas)) {
    $data_kelas[] = $kelas;

    if ($kelas_filter > 0 && (int) $kelas['id'] === $kelas_filter) {
        $nama_kelas_dipilih = $kelas['nama'];
    }
}

// ============================================
// 4. QUERY REKAP POIN SISWA
// ============================================
$sql = "SELECT
            s.id AS siswa_id,
            s.nis,
            s.nama AS nama_siswa,
            COALESCE(k.nama, '-') AS nama_kelas,
            COALESCE(p.total_poin, 0) AS total_poin,
            COALESCE(p.jumlah_pelanggaran, 0) AS jumlah_pelanggaran
        FROM t_siswa s

        LEFT JOIN (
            SELECT
                siswa_id,
                SUM(poin) AS total_poin,
                COUNT(*) AS jumlah_pelanggaran
            FROM t_pelanggaran_siswa
            GROUP BY siswa_id
        ) p ON p.siswa_id = s.id

        LEFT JOIN (
            SELECT
                siswa_id,
                MAX(kelas_id) AS kelas_id
            FROM t_kelas_siswa
            WHERE status_aktif = 1
            GROUP BY siswa_id
        ) ks ON ks.siswa_id = s.id

        LEFT JOIN t_kelas k ON k.id = ks.kelas_id

        WHERE 1 = 1";

$types  = "";
$params = [];

// Pencarian siswa
if ($cari !== '') {
    $sql .= " AND (s.nama LIKE ? OR s.nis LIKE ?)";

    $like     = '%' . $cari . '%';
    $types   .= "ss";
    $params[] = $like;
    $params[] = $like;
}

// Filter kelas
if ($kelas_filter > 0) {
    $sql .= " AND k.id = ?";

    $types   .= "i";
    $params[] = $kelas_filter;
}

// Urutan poin tertinggi
$sql .= " ORDER BY total_poin DESC, s.nama ASC";

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    die("Gagal menyiapkan rekap poin: " . htmlspecialchars(mysqli_error($koneksi)));
}

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$query_rekap = mysqli_stmt_get_result($stmt);

// ============================================
// 5. MENGHITUNG RINGKASAN
// ============================================
$data_rekap       = [];
$total_poin_semua = 0;
$total_catatan    = 0;

while ($data = mysqli_fetch_assoc($query_rekap)) {
    $data_rekap[] = $data;

    $total_poin_semua += (int) $data['total_poin'];
    $total_catatan    += (int) $data['jumlah_pelanggaran'];
}

mysqli_stmt_close($stmt);

$jumlah_siswa  = count($data_rekap);
$tanggal_cetak = tanggal_indo(date('Y-m-d'));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Rekap Poin Pelanggaran Siswa</title>

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

            <!-- JUDUL HALAMAN -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                <div>
                    <h2>Rekap Poin Pelanggaran Siswa</h2>
                    <p class="text-muted mb-0">
                        Ringkasan jumlah pelanggaran dan poin setiap siswa.
                    </p>
                </div>

                <div class="d-flex gap-2">
                    <a href="dashboard.php" class="btn btn-secondary">
                        Kembali ke Dashboard
                    </a>

                    <!-- Mencetak di halaman yang sama (tanpa sidebar) -->
                    <button type="button"
                            onclick="window.print()"
                            class="btn btn-success">
                        Cetak Rekap
                    </button>
                </div>
            </div>

            <!-- RINGKASAN -->
            <div class="row g-3 mb-4">

                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <p class="text-muted mb-1">Jumlah Siswa</p>
                            <div class="fs-2 fw-bold text-primary">
                                <?= number_format($jumlah_siswa); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <p class="text-muted mb-1">Total Poin</p>
                            <div class="fs-2 fw-bold text-primary">
                                <?= number_format($total_poin_semua); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <p class="text-muted mb-1">Jumlah Catatan Pelanggaran</p>
                            <div class="fs-2 fw-bold text-primary">
                                <?= number_format($total_catatan); ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- PENCARIAN DAN FILTER -->
            <div class="card shadow-sm mb-4">

                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Pencarian dan Filter Siswa</h5>
                </div>

                <div class="card-body">

                    <form method="GET" action="rekap_poin.php">

                        <div class="row g-3">

                            <div class="col-md-5">
                                <label for="cari" class="form-label">Cari Siswa</label>

                                <input type="text"
                                       name="cari"
                                       id="cari"
                                       class="form-control"
                                       placeholder="Masukkan nama atau NIS siswa"
                                       value="<?= htmlspecialchars($cari); ?>">
                            </div>

                            <div class="col-md-4">
                                <label for="kelas" class="form-label">Filter Kelas</label>

                                <select name="kelas" id="kelas" class="form-select">

                                    <option value="">Semua Kelas</option>

                                    <?php foreach ($data_kelas as $kelas) { ?>
                                        <option value="<?= (int) $kelas['id']; ?>"
                                            <?= $kelas_filter === (int) $kelas['id'] ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($kelas['nama']); ?>
                                        </option>
                                    <?php } ?>

                                </select>
                            </div>

                            <div class="col-md-3 d-flex align-items-end gap-2">

                                <button type="submit" class="btn btn-primary">
                                    Cari
                                </button>

                                <a href="rekap_poin.php" class="btn btn-secondary">
                                    Reset
                                </a>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

            <!-- TABEL REKAP -->
            <div class="card shadow-sm">

                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Data Rekap Poin Siswa</h5>
                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table id="tabelRekap"
                               class="table table-striped table-bordered table-hover align-middle w-100">

                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Jumlah Pelanggaran</th>
                                    <th>Total Poin</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach ($data_rekap as $data) { ?>

                                    <tr>
                                        <!-- Nomor otomatis oleh DataTables -->
                                        <td></td>

                                        <td><?= teks($data['nis']); ?></td>

                                        <td><?= teks($data['nama_siswa']); ?></td>

                                        <td><?= teks($data['nama_kelas']); ?></td>

                                        <td class="text-center">
                                            <?= (int) $data['jumlah_pelanggaran']; ?>
                                        </td>

                                        <td class="text-center" data-order="<?= (int) $data['total_poin']; ?>">
                                            <span class="badge bg-warning text-dark">
                                                <?= (int) $data['total_poin']; ?> poin
                                            </span>
                                        </td>
                                    </tr>

                                <?php } ?>

                            </tbody>

                        </table>

                    </div>

                    <p class="text-muted mb-0 mt-3">
                        Total poin dihitung berdasarkan catatan pada
                        tabel t_pelanggaran_siswa.
                    </p>

                </div>

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
            REKAP POIN PELANGGARAN SISWA
        </h4>

        <div>
            Kelas: <?= htmlspecialchars($nama_kelas_dipilih); ?>
            <?php if ($cari !== '') { ?>
                | Pencarian: "<?= htmlspecialchars($cari); ?>"
            <?php } ?>
        </div>

    </div>

    <!-- RINGKASAN -->
    <div class="row mb-3 small">

        <div class="col-6">
            <div>Jumlah Siswa : <strong><?= number_format($jumlah_siswa); ?></strong></div>
            <div>Total Poin : <strong><?= number_format($total_poin_semua); ?></strong></div>
            <div>Jumlah Catatan Pelanggaran : <strong><?= number_format($total_catatan); ?></strong></div>
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
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Jumlah Pelanggaran</th>
                <th>Total Poin</th>
            </tr>
        </thead>

        <tbody>

        <?php if ($jumlah_siswa > 0) { ?>

            <?php $no = 1; ?>

            <?php foreach ($data_rekap as $data) { ?>

                <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td><?= teks($data['nis']); ?></td>
                    <td><?= teks($data['nama_siswa']); ?></td>
                    <td class="text-center"><?= teks($data['nama_kelas']); ?></td>
                    <td class="text-center"><?= (int) $data['jumlah_pelanggaran']; ?></td>
                    <td class="text-center"><?= (int) $data['total_poin']; ?></td>
                </tr>

            <?php } ?>

        <?php } else { ?>

            <tr>
                <td colspan="6" class="text-center py-4">
                    Belum ada data rekap poin siswa.
                </td>
            </tr>

        <?php } ?>

        </tbody>

    </table>

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

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<!-- Inisialisasi DataTables -->
<script>
$(document).ready(function () {

    $('#tabelRekap').DataTable({

        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],

        searching: true,
        ordering: true,
        paging: true,

        // Total poin tertinggi ditampilkan lebih dahulu
        order: [[5, 'desc'], [2, 'asc']],

        // Kolom No tidak perlu diurutkan atau dicari
        columnDefs: [
            {
                orderable: false,
                searchable: false,
                targets: 0
            },
            {
                targets: [0, 4, 5],
                className: 'text-center'
            }
        ],

        // Nomor mengikuti hasil pencarian dan halaman aktif
        drawCallback: function () {

            var api = this.api();
            var nomorAwal = api.page.info().start;

            api.column(0, {
                search: 'applied',
                order: 'applied'
            }).nodes().each(function (cell, i) {
                cell.innerHTML = nomorAwal + i + 1;
            });

        },

        language: {
            search: "Cari di tabel:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data rekap",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Data siswa tidak ditemukan",
            emptyTable: "Belum ada data rekap poin siswa",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        }

    });

});
</script>

</body>
</html>