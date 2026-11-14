<?php
// cetak_rekap.php

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

// ============================================
// LOGO SEKOLAH
// Pakai tanda "/" (bukan "\") dan samakan huruf besar/kecil
// dengan nama file asli di hosting. Daftar di bawah dicek berurutan,
// yang pertama ditemukan akan dipakai.
// ============================================
$kandidat_logo = [
    'assets/css/img/logo.png',
    'assets/css/img.logo.png',
    'assets/img/logo.png',
    'assets/images/logo.png',
    'assets/logo.png',
    'img/logo.png',
    'images/logo.png',
    'logo.png',
];

$logo_sekolah = '';

foreach ($kandidat_logo as $path) {
    if (is_file(__DIR__ . '/' . $path)) {
        $logo_sekolah = $path;
        break;
    }
}

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
// 2. INPUT FILTER (sama dengan rekap_poin.php)
// ============================================
$cari         = trim($_GET['cari'] ?? '');
$kelas_filter = (int) ($_GET['kelas'] ?? 0);

// ============================================
// 3. NAMA KELAS YANG DIPILIH
// ============================================
$nama_kelas_dipilih = 'Semua Kelas';

if ($kelas_filter > 0) {
    $stmt_kelas = mysqli_prepare($koneksi, "SELECT nama FROM t_kelas WHERE id = ?");
    mysqli_stmt_bind_param($stmt_kelas, "i", $kelas_filter);
    mysqli_stmt_execute($stmt_kelas);

    $row_kelas = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_kelas));

    if ($row_kelas) {
        $nama_kelas_dipilih = $row_kelas['nama'];
    }

    mysqli_stmt_close($stmt_kelas);
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

if ($cari !== '') {
    $sql .= " AND (s.nama LIKE ? OR s.nis LIKE ?)";

    $like     = '%' . $cari . '%';
    $types   .= "ss";
    $params[] = $like;
    $params[] = $like;
}

if ($kelas_filter > 0) {
    $sql .= " AND k.id = ?";

    $types   .= "i";
    $params[] = $kelas_filter;
}

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
// 5. RINGKASAN
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

// Link kembali (membawa filter yang sama)
$query_kembali = http_build_query(array_filter([
    'cari'  => $cari,
    'kelas' => $kelas_filter > 0 ? $kelas_filter : null,
]));
$link_kembali = 'rekap_poin.php' . ($query_kembali !== '' ? '?' . $query_kembali : '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Cetak Rekap Poin Pelanggaran Siswa</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container bg-white p-4 my-3 shadow-sm">

    <!-- TOMBOL (tidak ikut tercetak) -->
    <div class="d-flex justify-content-end gap-2 mb-3 d-print-none">

        <a href="<?= htmlspecialchars($link_kembali); ?>" class="btn btn-secondary">
            Kembali
        </a>

        <button type="button" onclick="window.print()" class="btn btn-success">
            Cetak
        </button>

    </div>

    <!-- KOP SURAT -->
    <div class="d-flex align-items-center border-bottom border-dark border-3 pb-3 mb-4">

        <?php if ($logo_sekolah !== '') { ?>
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

    <!-- TABEL (semua data, tanpa pagination) -->
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

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- Dialog cetak otomatis saat halaman selesai dimuat -->
<script>
window.addEventListener('load', function () {
    window.print();
});
</script>

</body>
</html>