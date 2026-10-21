
<?php
session_start();
include "config/koneksi.php";

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Cek hak akses admin
if ($_SESSION['role'] != "admin") {
    echo "Akses ditolak!";
    exit;
}

// Mengambil data jenis pelanggaran
$data = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Jenis Pelanggaran</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables -->
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css" rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php include "layout/sidebar.php"; ?>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 p-4">

            <h2 class="mb-4">Kelola Jenis Pelanggaran</h2>

            <!-- TOMBOL -->
            <div class="mb-3">
                <a href="dashboard.php" class="btn btn-secondary">
                    Kembali ke Dashboard
                </a>

                <a href="tambah_jenis_pelanggaran.php" class="btn btn-primary">
                    + Tambah Jenis Pelanggaran
                </a>
            </div>

            <!-- TABEL JENIS PELANGGARAN -->
                <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Daftar Jenis Pelanggaran</h5>
                </div>

                <div class="card-body">
                    <div class="table-responsive">

                        <table id="tabelPelanggaran"
                               class="table table-striped table-bordered table-hover align-middle w-100">

                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>ID Kategori</th>
                                    <th>Kode</th>
                                    <th>Nama Pelanggaran</th>
                                    <th>Poin</th>
                                    <th>Deskripsi</th>
                                    <th>Status Aktif</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $no = 1;

                                if ($data && mysqli_num_rows($data) > 0) {
                                    while ($pelanggaran = mysqli_fetch_assoc($data)) {
                                ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $pelanggaran['pelanggaran_kategori_id']; ?></td>
                                    <td><?= $pelanggaran['kode']; ?></td>
                                    <td><?= $pelanggaran['nama']; ?></td>
                                    <td><?= $pelanggaran['poin']; ?></td>
                                    <td><?= $pelanggaran['deskripsi']; ?></td>

                                    <td>
                                        <?php if ($pelanggaran['status_aktif'] == 1) { ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php } else { ?>
                                            <span class="badge bg-danger">Tidak Aktif</span>
                                        <?php } ?>
                                    </td>

                                    <td><?= $pelanggaran['created_at']; ?></td>
                                    <td><?= $pelanggaran['updated_at']; ?></td>

                                    <td>
                                        <a href="edit_jenis_pelanggaran.php?id=<?= $pelanggaran['id']; ?>"
                                           class="btn btn-warning btn-sm mb-1">
                                            Edit
                                        </a>

                                        <a href="hapus_jenis_pelanggaran.php?id=<?= $pelanggaran['id']; ?>"
                                           class="btn btn-danger btn-sm mb-1"
                                           onclick="return confirm('Yakin ingin menghapus jenis pelanggaran ini?')">
                                            Hapus
                                        </a>
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
            </div>

        </main>
    </div>
</div>

<!-- JavaScript Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- JavaScript DataTables -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<!-- Mengaktifkan DataTables -->
<script>
$(document).ready(function () {
    $('#tabelPelanggaran').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Data tidak ditemukan",
            emptyTable: "Belum ada data jenis pelanggaran",
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
