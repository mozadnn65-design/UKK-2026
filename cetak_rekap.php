<?php
// =====================================================
// 1. MENCARI FILE AUTENTIKASI
// =====================================================

$authCandidates = [
    __DIR__ . "/middleware/auth.php",
    __DIR__ . "/includes/cek_session.php"
];

$authFile = null;

foreach ($authCandidates as $file) {
    if (file_exists($file)) {
        $authFile = $file;
        break;
    }
}

if ($authFile === null) {
    die("File autentikasi tidak ditemukan. Periksa apakah folder middleware/auth.php atau includes/cek_session.php tersedia di folder UKK-2026.");
}

require_once $authFile;


// =====================================================
// 2. MENCARI FILE KONEKSI DATABASE
// =====================================================

$configCandidates = [
    __DIR__ . "/config/koneksi.php",
    __DIR__ . "/koneksi.php"
];

$configFile = null;

foreach ($configCandidates as $file) {
    if (file_exists($file)) {
        $configFile = $file;
        break;
    }
}

if ($configFile === null) {
    die("File koneksi database tidak ditemukan. Periksa folder config/koneksi.php.");
}

require_once $configFile;


// =====================================================
// 3. MEMASTIKAN SESSION AKTIF
// =====================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// =====================================================
// 4. CEK ROLE DAN DATA USER
// =====================================================

$role = $_SESSION['role'] ?? '';

if ($role !== 'guru') {
    http_response_code(403);
    die("Akses ditolak. Halaman ini hanya untuk guru.");
}

$user_id = $_SESSION['user_id'] ?? $_SESSION['id_user'] ?? null;
$nama_guru = $_SESSION['nama'] ?? $_SESSION['nama_user'] ?? 'Guru';

if (!$user_id) {
    die("ID pengguna tidak ditemukan dalam session. Silakan login kembali.");
}


// =====================================================
// 5. CEK KONEKSI DATABASE
// =====================================================

if (!isset($koneksi) || !($koneksi instanceof mysqli)) {
    die("Koneksi database tidak ditemukan. Pastikan variabel koneksi bernama \$koneksi di config/koneksi.php.");
}

$koneksi->set_charset("utf8mb4");


// =====================================================
// 6. MENGAMBIL ID GURU
// =====================================================

$stmtGuru = $koneksi->prepare("
    SELECT id
    FROM t_guru
    WHERE user_id = ?
    LIMIT 1
");

if (!$stmtGuru) {
    die("Query data guru gagal: " . $koneksi->error);
}

$stmtGuru->bind_param("i", $user_id);
$stmtGuru->execute();

$resultGuru = $stmtGuru->get_result();
$dataGuru = $resultGuru->fetch_assoc();
$stmtGuru->close();

if (!$dataGuru) {
    die("Data guru tidak ditemukan.");
}

$guru_id = (int) $dataGuru['id'];


// =====================================================
// 7. MENGAMBIL DATA KELAS WALI
// =====================================================

$stmtKelas = $koneksi->prepare("
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
");

if (!$stmtKelas) {
    die("Query kelas gagal: " . $koneksi->error);
}

$stmtKelas->bind_param("i", $guru_id);
$stmtKelas->execute();

$resultKelas = $stmtKelas->get_result();

$daftarKelas = [];

while ($kelas = $resultKelas->fetch_assoc()) {
    $daftarKelas[] = $kelas;
}

$stmtKelas->close();


// =====================================================
// 8. MENGAMBIL DATA PELANGGARAN
// =====================================================

$stmt = $koneksi->prepare("
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
");

if (!$stmt) {
    die("Query rekap pelanggaran gagal: " . $koneksi->error);
}

$stmt->bind_param("i", $guru_id);
$stmt->execute();

$result = $stmt->get_result();

$rows = [];

$total_poin = 0;
$total_selesai = 0;
$total_proses = 0;
$total_pelanggaran = 0;

while ($data = $result->fetch_assoc()) {
    $rows[] = $data;

    $total_pelanggaran++;
    $total_poin += (int) $data['poin'];

    if (strtolower(trim($data['status'])) === 'selesai') {
        $total_selesai++;
    } else {
        $total_proses++;
    }
}

$stmt->close();


// =====================================================
// 9. TANGGAL CETAK
// =====================================================

$tanggal_cetak = date("d-m-Y");

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Rekap Pelanggaran Siswa</title>

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container-fluid py-4">

    <!-- TOMBOL -->
    <div class="d-flex justify-content-between mb-4">
        <a href="rekap_pelanggaran.php" class="btn btn-secondary">
            Kembali
        </a>

        <button onclick="window.print()" class="btn btn-primary">
            Cetak Laporan
        </button>
    </div>


    <!-- KOP LAPORAN -->
    <div class="text-center mb-4">

        <h4 class="fw-bold mb-1">
            SMK MUHAMMADIYAH KOTA TASIKMALAYA
        </h4>

        <h5 class="fw-bold">
            REKAP PELANGGARAN SISWA
        </h5>

        <hr>

    </div>


    <!-- INFORMASI GURU -->
    <div class="row mb-3">

        <div class="col-md-6">
            <table class="table table-sm table-borderless">

                <tr>
                    <td width="150">Nama Wali Kelas</td>
                    <td width="10">:</td>
                    <td><?= htmlspecialchars($nama_guru) ?></td>
                </tr>

                <tr>
                    <td>Kelas Diampu</td>
                    <td>:</td>
                    <td>
                        <?php if (count($daftarKelas) > 0): ?>

                            <?php foreach ($daftarKelas as $kelas): ?>
                                <span class="badge bg-primary">
                                    <?= htmlspecialchars($kelas['nama_kelas']) ?>
                                </span>
                            <?php endforeach; ?>

                        <?php else: ?>
                            Belum ada kelas aktif
                        <?php endif; ?>
                    </td>
                </tr>

                <tr>
                    <td>Tahun Ajaran</td>
                    <td>:</td>
                    <td>
                        <?php
                        $tahunAjaran = [];

                        foreach ($daftarKelas as $kelas) {
                            $tahunAjaran[] = $kelas['tahun_ajaran'];
                        }

                        echo htmlspecialchars(
                            implode(", ", array_unique($tahunAjaran))
                        );
                        ?>
                    </td>
                </tr>

                <tr>
                    <td>Tanggal Cetak</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($tanggal_cetak) ?></td>
                </tr>

            </table>
        </div>

    </div>


    <!-- RINGKASAN -->
    <div class="row g-3 mb-4">

        <div class="col-md-3">
            <div class="card text-bg-primary">
                <div class="card-body">
                    <h6>Total Pelanggaran</h6>
                    <h3><?= $total_pelanggaran ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-danger">
                <div class="card-body">
                    <h6>Total Poin</h6>
                    <h3><?= $total_poin ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-success">
                <div class="card-body">
                    <h6>Pelanggaran Selesai</h6>
                    <h3><?= $total_selesai ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-warning">
                <div class="card-body">
                    <h6>Dalam Proses</h6>
                    <h3><?= $total_proses ?></h3>
                </div>
            </div>
        </div>

    </div>


    <!-- TABEL REKAP -->
    <div class="table-responsive">

        <table class="table table-bordered table-striped table-sm align-middle">

            <thead class="table-dark text-center">

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

                <?php if (count($rows) > 0): ?>

                    <?php $no = 1; ?>

                    <?php foreach ($rows as $row): ?>

                        <tr>
                            <td class="text-center">
                                <?= $no++ ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['nama_siswa'] ?? '') ?>
                            </td>

                            <td class="text-center">
                                <?= htmlspecialchars($row['nama_kelas'] ?? '') ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['nama_pelanggaran'] ?? '') ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['nama_guru'] ?? '') ?>
                            </td>

                            <td class="text-center">
                                <?= !empty($row['tanggal'])
                                    ? date('d-m-Y', strtotime($row['tanggal']))
                                    : '-' ?>
                            </td>

                            <td class="text-center">
                                <?= (int) $row['poin'] ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['tindakan'] ?? '') ?>
                            </td>

                            <td class="text-center">
                                <?= htmlspecialchars($row['status'] ?? '') ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="9" class="text-center">
                            Belum ada data pelanggaran siswa.
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>


    <!-- TANDA TANGAN -->
    <div class="row mt-5">

        <div class="col-6 ms-auto text-center">

            <p>
                Tasikmalaya, <?= htmlspecialchars($tanggal_cetak) ?>
                <br>
                Wali Kelas
            </p>

            <br><br><br>

            <p class="fw-bold text-decoration-underline">
                <?= htmlspecialchars($nama_guru) ?>
            </p>

        </div>

    </div>

</div>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>
</html>