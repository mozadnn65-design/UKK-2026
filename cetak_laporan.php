
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// SESSION
$role = $_SESSION['role'] ?? '';
$id_user = (int) ($_SESSION['id_user'] ?? 0);

// HAK AKSES
if ($role !== 'admin' && $role !== 'guru') {
    http_response_code(403);
    exit('Akses ditolak.');
}

// CEK GURU WALI KELAS
$id_guru = 0;

if ($role === 'guru') {

    $stmt_guru = mysqli_prepare($koneksi, "
        SELECT g.id
        FROM t_guru g
        INNER JOIN t_wali_kelas wk ON wk.guru_id = g.id
        WHERE g.user_id = ?
        AND wk.status_aktif = 1
        LIMIT 1
    ");

    mysqli_stmt_bind_param($stmt_guru, "i", $id_user);
    mysqli_stmt_execute($stmt_guru);

    $hasil_guru = mysqli_stmt_get_result($stmt_guru);
    $data_guru = mysqli_fetch_assoc($hasil_guru);

    mysqli_stmt_close($stmt_guru);

    if (!$data_guru) {
        http_response_code(403);
        exit('Anda bukan Guru Wali Kelas aktif.');
    }

    $id_guru = (int) $data_guru['id'];
}

// FILTER TANGGAL
$tanggal_mulai = $_GET['tanggal_mulai'] ?? '';
$tanggal_selesai = $_GET['tanggal_selesai'] ?? '';

// VALIDASI TANGGAL
if (
    ($tanggal_mulai !== '' &&
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_mulai)) ||
    ($tanggal_selesai !== '' &&
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_selesai))
) {
    exit('Format tanggal tidak valid.');
}

if (
    $tanggal_mulai !== '' &&
    $tanggal_selesai !== '' &&
    $tanggal_mulai > $tanggal_selesai
) {
    exit('Tanggal mulai tidak boleh melebihi tanggal selesai.');
}

// QUERY LAPORAN
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
        WHERE 1=1";

$types = "";
$params = [];

// FILTER GURU
if ($role === 'guru') {

    $sql .= " AND EXISTS (
        SELECT 1
        FROM t_wali_kelas wk
        WHERE wk.guru_id = ?
        AND wk.kelas_id = ps.kelas_id
        AND wk.tahun_ajaran_id = ps.tahun_ajaran_id
        AND wk.status_aktif = 1
    )";

    $types .= "i";
    $params[] = $id_guru;
}

// FILTER TANGGAL MULAI
if ($tanggal_mulai !== '') {
    $sql .= " AND ps.tanggal >= ?";
    $types .= "s";
    $params[] = $tanggal_mulai;
}

// FILTER TANGGAL SELESAI
if ($tanggal_selesai !== '') {
    $sql .= " AND ps.tanggal <= ?";
    $types .= "s";
    $params[] = $tanggal_selesai;
}

$sql .= " ORDER BY ps.tanggal DESC, ps.id DESC";

// EKSEKUSI QUERY
$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    die("Gagal mengambil laporan: " . mysqli_error($koneksi));
}

if (!empty($params)) {
    $bind_params = [$stmt, $types];

    foreach ($params as $key => $value) {
        $bind_params[] = &$params[$key];
    }

    call_user_func_array(
        'mysqli_stmt_bind_param',
        $bind_params
    );
}

mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);

$total = mysqli_num_rows($hasil);
$no = 1;

$tanggal_cetak = date('d-m-Y');
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Cetak Laporan Pelanggaran Siswa</title>

    <!-- BOOTSTRAP 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

</head>

<body class="bg-light">

<div class="container-fluid p-4">

    <!-- HEADER -->
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-primary text-white text-center p-4">

            <h2 class="fw-bold mb-2">
                LAPORAN PELANGGARAN SISWA
            </h2>

            <h6 class="mb-0">
                SISTEM INFORMASI PELANGGARAN SISWA
            </h6>

        </div>

        <div class="card-body">

            <!-- INFORMASI -->
            <div class="row mb-4">

                <div class="col-6">

                    <div class="card bg-primary-subtle border-0">

                        <div class="card-body">

                            <h6 class="text-primary fw-bold">
                                TOTAL DATA LAPORAN
                            </h6>

                            <h3 class="fw-bold text-primary mb-0">
                                <?= number_format($total); ?>
                            </h3>

                        </div>

                    </div>

                </div>

                <div class="col-6">

                    <div class="card bg-success-subtle border-0">

                        <div class="card-body">

                            <h6 class="text-success fw-bold">
                                TANGGAL CETAK
                            </h6>

                            <h4 class="fw-bold text-success mb-0">
                                <?= htmlspecialchars($tanggal_cetak); ?>
                            </h4>

                        </div>

                    </div>

                </div>

            </div>

            <!-- PERIODE LAPORAN -->
            <div class="mb-3">

                <h6 class="fw-bold">
                    Periode Laporan
                </h6>

                <p class="mb-0">

                    <?php if ($tanggal_mulai || $tanggal_selesai) { ?>

                        <?= $tanggal_mulai
                            ? htmlspecialchars($tanggal_mulai)
                            : 'Awal'; ?>

                        s/d

                        <?= $tanggal_selesai
                            ? htmlspecialchars($tanggal_selesai)
                            : 'Sekarang'; ?>

                    <?php } else { ?>

                        Semua Tanggal

                    <?php } ?>

                </p>

            </div>

            <hr>

            <!-- JUDUL TABEL -->
            <div class="mb-3">

                <h5 class="fw-bold text-primary">
                    DATA LAPORAN PELANGGARAN SISWA
                </h5>

                <p class="text-secondary mb-0">
                    Berikut adalah data laporan pelanggaran siswa.
                </p>

            </div>

            <!-- TABEL LAPORAN -->
            <div class="table-responsive">

                <table class="table table-bordered table-striped align-middle">

                    <thead class="table-primary text-center">

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

                        <?php while ($row = mysqli_fetch_assoc($hasil)) { ?>

                            <tr>

                                <td class="text-center">
                                    <?= $no++; ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['nama_siswa'] ?? '-'); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['nama_kelas'] ?? '-'); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['nama_pelanggaran'] ?? '-'); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['nama_guru'] ?? '-'); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['tanggal'] ?? '-'); ?>
                                </td>

                                <td class="text-center">
                                    <?= (int) $row['poin']; ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['tindakan'] ?? '-'); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['status'] ?? '-'); ?>
                                </td>

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

            </div>

            <!-- INFORMASI -->
            <div class="alert alert-primary">

                <strong>Informasi:</strong>

                Total terdapat <?= number_format($total); ?>
                data pelanggaran siswa dalam laporan ini.

            </div>

            <!-- TANDA TANGAN -->
            <div class="row mt-5">

                <div class="col-7"></div>

                <div class="col-5 text-center">

                    <p>
                        Mengetahui,<br>
                        Kepala Sekolah
                    </p>

                    <br><br><br>

                    <p class="fw-bold">
                        (........................................)
                    </p>

                </div>

            </div>

            <!-- TOMBOL -->
            <div class="d-flex justify-content-end gap-2 mt-4 d-print-none">

                <a href="laporan.php"
                   class="btn btn-secondary">

                    Kembali

                </a>

                <button type="button"
                        class="btn btn-success"
                        onclick="window.print()">

                    Cetak Laporan

                </button>

            </div>

        </div>

    </div>

</div>

</body>
</html>

<?php
mysqli_stmt_close($stmt);
?>

