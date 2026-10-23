
<?php
// kelola_wali_kelas.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$sql = "SELECT
            w.*,
            k.nama AS nama_kelas,
            g.nama AS nama_guru
        FROM t_wali_kelas w
        LEFT JOIN t_kelas k ON w.kelas_id = k.id
        LEFT JOIN t_guru g ON w.guru_id = g.id
        ORDER BY w.id DESC";

$hasil = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Wali Kelas</title>

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

            <h2>Kelola Wali Kelas</h2>
            <p>Selamat datang di halaman Wali Kelas.</p>
          <?php
            if (isset($_SESSION['pesan_sukses'])) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
                echo $_SESSION['pesan_sukses'];
                echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                echo '</div>';

                unset($_SESSION['pesan_sukses']);
            }

            if (isset($_SESSION['pesan_error'])) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                echo $_SESSION['pesan_error'];
                echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                echo '</div>';

                unset($_SESSION['pesan_error']);
            }
            ?>
            <div class="mb-3">
                <a href="dashboard.php" class="btn btn-secondary">
                    Kembali ke Dashboard
                </a>

                <a href="tambah_wali_kelas.php" class="btn btn-primary">
                    Tambah Wali Kelas
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">

                    <div class="table-responsive">
                        <table id="tabelWaliKelas"
                               class="table table-striped table-bordered table-hover">

                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Tahun Ajaran ID</th>
                                    <th>Kelas</th>
                                    <th>Guru</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($hasil)) { ?>
                                    <tr>
                                        <td></td>

                                        <td>
                                            <?= htmlspecialchars($row['tahun_ajaran_id']) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['nama_kelas'] ?? '-') ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['nama_guru'] ?? '-') ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['tanggal_mulai'] ?? '') ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['tanggal_selesai'] ?? '') ?>
                                        </td>

                                        <td>
                                            <?php if ($row['status_aktif'] == 1) { ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php } else { ?>
                                                <span class="badge bg-danger">Tidak Aktif</span>
                                            <?php } ?>
                                        </td>

                                        <td>
                                            <a href="edit_wali_kelas.php?id=<?= urlencode($row['id']) ?>"
                                               class="btn btn-warning btn-sm">
                                                Edit
                                            </a>

                                            <a href="hapus_wali_kelas.php?id=<?= urlencode($row['id']) ?>"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Yakin ingin menghapus data wali kelas ini?');">
                                                Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>

                        </table>
                    </div>

                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {

    $('#tabelWaliKelas').DataTable({

        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data wali kelas",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Data wali kelas tidak ditemukan",
            emptyTable: "Belum ada data wali kelas",

            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        },

        pageLength: 10,

        // Urutkan berdasarkan ID asli, terbaru lebih dahulu
        order: [[1, 'desc']],

        // Kolom No tidak diurutkan secara langsung
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false
            }
        ],

        // Nomor otomatis mengikuti data yang ditampilkan
        drawCallback: function () {
            var api = this.api();
            var info = api.page.info();

            api.column(0, { page: 'current' })
                .nodes()
                .each(function (cell, i) {
                    cell.textContent = info.start + i + 1;
                });
        }

    });

});
</script>

</body>
</html>
