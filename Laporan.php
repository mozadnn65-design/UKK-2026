
<?php
// Menampilkan error untuk membantu debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include "config/koneksi.php";

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'] ?? '';
$data = false;

// ADMIN: melihat semua laporan
if ($role == 'admin') {

    $data = mysqli_query($koneksi, "
        SELECT
            ps.id,
            s.nis,
            s.nama,
            pk.nama AS pelanggaran
        FROM t_pelanggaran_siswa ps
        JOIN t_siswa s ON ps.siswa_id = s.id
        JOIN t_pelanggaran_kategori pk ON ps.pelanggaran_id = pk.id
        ORDER BY ps.id DESC
    ");

    if (!$data) {
        die("Query laporan gagal: " . mysqli_error($koneksi));
    }

// GURU: hanya wali kelas aktif
} elseif ($role == 'guru') {

    $user_id = (int) $_SESSION['user_id'];

    $query_wali = mysqli_prepare($koneksi, "
        SELECT
            wk.kelas_id,
            wk.tahun_ajaran_id
        FROM t_guru g
        JOIN t_wali_kelas wk ON wk.guru_id = g.id
        JOIN t_tahun_ajaran ta ON ta.id = wk.tahun_ajaran_id
        WHERE g.user_id = ?
          AND wk.status_aktif = 1
          AND ta.status_aktif = 1
        LIMIT 1
    ");

    if (!$query_wali) {
        die("Query wali kelas gagal: " . mysqli_error($koneksi));
    }

    mysqli_stmt_bind_param($query_wali, "i", $user_id);
    mysqli_stmt_execute($query_wali);
    $result_wali = mysqli_stmt_get_result($query_wali);
    $wali = mysqli_fetch_assoc($result_wali);

    if (!$wali) {
        echo "<script>
            alert('Akses ditolak! Anda bukan wali kelas aktif.');
            window.location.href = 'dashboard.php';
        </script>";
        exit;
    }

    $kelas_id = (int) $wali['kelas_id'];
    $tahun_ajaran_id = (int) $wali['tahun_ajaran_id'];

    $stmt = mysqli_prepare($koneksi, "
        SELECT
            ps.id,
            s.nis,
            s.nama,
            pk.nama AS pelanggaran
        FROM t_pelanggaran_siswa ps
        JOIN t_siswa s ON ps.siswa_id = s.id
        JOIN t_pelanggaran_kategori pk ON ps.pelanggaran_id = pk.id
        JOIN t_kelas_siswa ks ON ks.siswa_id = s.id
        WHERE ks.kelas_id = ?
          AND ks.tahun_ajaran_id = ?
          AND ks.status_aktif = 1
        ORDER BY ps.id DESC
    ");

    if (!$stmt) {
        die("Query laporan wali kelas gagal: " . mysqli_error($koneksi));
    }

    mysqli_stmt_bind_param($stmt, "ii", $kelas_id, $tahun_ajaran_id);
    mysqli_stmt_execute($stmt);
    $data = mysqli_stmt_get_result($stmt);

// Role lain ditolak
} else {
    echo "<script>
        alert('Akses ditolak! Halaman ini hanya untuk admin dan wali kelas.');
        window.location.href = 'dashboard.php';
    </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laporan Pelanggaran Siswa</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DataTables -->
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <?php include "layout/sidebar.php"; ?>

        <main class="col-md-9 col-lg-10 p-4">

            <h2>Laporan Pelanggaran Siswa</h2>
            <p>Daftar laporan pelanggaran yang dilakukan siswa.</p>

            <a href="dashboard.php" class="btn btn-secondary mb-3">
                Kembali ke Dashboard
            </a>

            <a href="cetak_laporan.php" target="_blank"
               class="btn btn-primary mb-3">
                Cetak Laporan
            </a>

            <a href="export_laporan.php"
               class="btn btn-success mb-3">
                Export Excel
            </a>

            <div class="card shadow-sm">
                <div class="card-body">

                    <div class="table-responsive">
                        <table id="tabelLaporan"
                               class="table table-bordered table-striped align-middle">

                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th>Jenis Pelanggaran</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $no = 1;

                                if ($data && mysqli_num_rows($data) > 0) {
                                    while ($row = mysqli_fetch_assoc($data)) {
                                ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= htmlspecialchars($row['nis'] ?? ''); ?></td>
                                        <td><?= htmlspecialchars($row['nama'] ?? ''); ?></td>
                                        <td><?= htmlspecialchars($row['pelanggaran'] ?? ''); ?></td>
                                    </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>

                        </table>
                    </div>

                </div>
            </div>

        </main>
    </div>
</div>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.js"></script>

<script>
$(document).ready(function () {
    $('#tabelLaporan').DataTable({
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            zeroRecords: "Data tidak ditemukan",
            emptyTable: "Belum ada data laporan pelanggaran",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        },
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false
            }
        ]
    });
});
</script>

</body>
</html>
