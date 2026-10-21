<?php
// penempatan_siswa.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Mengambil data penempatan siswa
$sql = "SELECT
            ks.id,
            s.nis,
            s.nama AS nama_siswa,
            ta.nama AS tahun_ajaran,
            k.nama AS nama_kelas,
            k.tingkat,
            k.jurusan,
            ks.tanggal_mulai,
            ks.tanggal_selesai,
            ks.status_aktif
        FROM t_kelas_siswa ks
        LEFT JOIN t_siswa s ON ks.siswa_id = s.id
        LEFT JOIN t_tahun_ajaran ta ON ks.tahun_ajaran_id = ta.id
        LEFT JOIN t_kelas k ON ks.kelas_id = k.id
        ORDER BY ks.id DESC";

$hasil = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Penempatan Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
          rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 p-4">

            <h2 class="mb-3">Penempatan Siswa</h2>

            <!-- Pesan sukses -->
            <?php if (isset($_SESSION['pesan_sukses'])) { ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['pesan_sukses']); ?>
                </div>
                <?php unset($_SESSION['pesan_sukses']); ?>
            <?php } ?>

            <!-- Pesan error -->
            <?php if (isset($_SESSION['pesan_error'])) { ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['pesan_error']); ?>
                </div>
                <?php unset($_SESSION['pesan_error']); ?>
            <?php } ?>

            <div class="mb-3">
                <a href="dashboard.php" class="btn btn-secondary">
                    Kembali ke Dashboard
                </a>

                <a href="tambah_penempatan_siswa.php"
                   class="btn btn-primary">
                    + Tambah Penempatan
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Data Penempatan Siswa</h5>
                </div>

                <div class="card-body table-responsive">

                    <table id="tabelPenempatan"
                           class="table table-bordered table-striped table-hover align-middle">

                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Tahun Ajaran</th>
                                <th>Kelas</th>
                                <th>Tingkat</th>
                                <th>Jurusan</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php
                        if ($hasil && mysqli_num_rows($hasil) > 0) {
                            $no = 1;

                            while ($row = mysqli_fetch_assoc($hasil)) {
                        ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nis'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['nama_siswa'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['tahun_ajaran'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['nama_kelas'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['tingkat'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['jurusan'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['tanggal_mulai'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['tanggal_selesai'] ?? '-'); ?></td>
                                <td>
                                    <?php if ($row['status_aktif'] == 1) { ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php } else { ?>
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php
                            }
                        }
                        ?>
                        </tbody>

                    </table>

                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    $('#tabelPenempatan').DataTable({
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ data)",
            zeroRecords: "Data tidak ditemukan",
            emptyTable: "Belum ada data penempatan",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        },
        pageLength: 10,
        order: [[0, 'asc']]
    });
});
</script>

</body>
</html>
