<?php
// kelola_tahun_ajaran.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$sql = "SELECT * FROM t_tahun_ajaran ORDER BY tanggal_mulai DESC";
$hasil = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Tahun Ajaran</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
          rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">

            <h2>Kelola Tahun Ajaran</h2>
            <p>Selamat datang di halaman Tahun Ajaran.</p>

            <div class="mb-3">
                <a href="dashboard.php" class="btn btn-secondary">
                    Kembali ke Dashboard
                </a>

                <a href="tambah_tahun_ajaran.php" class="btn btn-primary">
                    Tambah Tahun Ajaran
                </a>
            </div>

            <!-- TABEL DATA TAHUN AJARAN -->
             <div class="card shadow-sm">
                <div class="card-body">
            <div class="table-responsive">

                <table id="tabelTahunAjaran"
                       class="table table-striped table-bordered table-hover">

                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Tahun Ajaran</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Status Aktif</th>
                            <th>Dibuat</th>
                            <th>Diubah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php while ($row = mysqli_fetch_assoc($hasil)) { ?>

                        <tr>
                            <td>
                                <?php echo htmlspecialchars($row['id']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['nama']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['tanggal_mulai'] ?? ''); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['tanggal_selesai'] ?? ''); ?>
                            </td>

                            <td>
                                <?php
                                if ($row['status_aktif'] == 1) {
                                    echo "Aktif";
                                } else {
                                    echo "Tidak Aktif";
                                }
                                ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['created_at'] ?? ''); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['updated_at'] ?? ''); ?>
                            </td>

                            <td>
                                <a href="edit_tahun_ajaran.php?id=<?php echo urlencode($row['id']); ?>"
                                   class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <a href="hapus_tahun_ajaran.php?id=<?php echo urlencode($row['id']); ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Yakin ingin menghapus data tahun ajaran ini?');">
                                    Hapus
                                </a>
                            </td>
                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

        </main>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<!-- Mengaktifkan DataTables -->
<script>
$(document).ready(function () {

    $('#tabelTahunAjaran').DataTable({

        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data tahun ajaran",
            zeroRecords: "Data tahun ajaran tidak ditemukan",
            emptyTable: "Belum ada data tahun ajaran",

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