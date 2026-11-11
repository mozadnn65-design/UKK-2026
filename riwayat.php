
<?php
// riwayat.php

// Memeriksa session login
include 'includes/cek_session.php';

// Menghubungkan database
include 'config/koneksi.php';

// ======================================
// MENGAMBIL IDENTITAS GURU DARI SESSION
// ======================================

$guru_id = (int) (
    $_SESSION['guru_id']
    ?? $_SESSION['id_guru']
    ?? 0
);

$nama_login = trim(
    $_SESSION['nama_guru']
    ?? $_SESSION['nama']
    ?? $_SESSION['username']
    ?? ''
);

// ======================================
// PENCARIAN
// ======================================

$cari = isset($_GET['cari'])
    ? trim($_GET['cari'])
    : '';

// ======================================
// QUERY RIWAYAT
// ======================================

// Identitas guru harus tersedia
if ($nama_login == '') {

    $query = false;
    $jumlah = 0;
    $error_identitas = true;

} else {

    $error_identitas = false;

    // Hanya mengambil pelanggaran yang sudah selesai
    $sql = "SELECT *
            FROM t_pelanggaran_siswa
            WHERE LOWER(TRIM(IFNULL(status, ''))) = 'selesai'
            AND nama_guru = ?";

    // Pencarian
    if ($cari != '') {

        $sql .= " AND (
            nama_siswa LIKE ?
            OR nama_pelanggaran LIKE ?
            OR nama_kelas LIKE ?
            OR nama_guru LIKE ?
        )";

    }

    // Urutkan tanggal terbaru
    $sql .= " ORDER BY tanggal DESC, id DESC";

    // Prepared statement
    $stmt = mysqli_prepare($koneksi, $sql);

    if (!$stmt) {
        die('Gagal menyiapkan query: ' . mysqli_error($koneksi));
    }

    // Bind parameter
    if ($cari != '') {

        $keyword = '%' . $cari . '%';

        mysqli_stmt_bind_param(
            $stmt,
            "sssss",
            $nama_login,
            $keyword,
            $keyword,
            $keyword,
            $keyword
        );

    } else {

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $nama_login
        );

    }

    // Jalankan query
    mysqli_stmt_execute($stmt);

    // Ambil hasil
    $query = mysqli_stmt_get_result($stmt);

    if (!$query) {
        die('Gagal mengambil data riwayat.');
    }

    // Hitung jumlah data
    $jumlah = mysqli_num_rows($query);
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Riwayat Pelanggaran Siswa</title>

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

            <!-- JUDUL -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">

                <div>

                    <h2 class="fw-bold">
                        Riwayat Pelanggaran Siswa
                    </h2>

                    <p class="text-muted mb-0">
                        Daftar pelanggaran yang Anda laporkan dan sudah ditindak.
                    </p>

                </div>

                <a href="dashboard.php" class="btn btn-secondary">
                    Kembali ke Dashboard
                </a>

            </div>

            <?php if ($error_identitas) { ?>

                <!-- PERINGATAN SESSION -->
                <div class="alert alert-warning">

                    <h5 class="alert-heading">
                        Identitas Guru Belum Ditemukan
                    </h5>

                    <p class="mb-2">
                        Sistem belum dapat membaca nama guru yang sedang login.
                    </p>

                    <p class="mb-0">
                        Silakan periksa nama session pada proses login.
                        Riwayat tidak ditampilkan demi keamanan data.
                    </p>

                </div>

            <?php } else { ?>

                <!-- INFORMASI GURU -->
                <div class="alert alert-info">

                    <strong>Guru Pelapor:</strong>
                    <?= htmlspecialchars($nama_login); ?>

                    <br>

                    <small>
                        Anda hanya dapat melihat pelanggaran yang Anda laporkan sendiri.
                    </small>

                </div>

                <!-- JUMLAH RIWAYAT -->
                <div class="card shadow-sm mb-4">

                    <div class="card-body">

                        <h6 class="text-muted">
                            Jumlah Riwayat Pelanggaran Anda
                        </h6>

                        <h3 class="fw-bold text-primary">
                            <?= $jumlah; ?> data
                        </h3>

                    </div>

                </div>

                <!-- PENCARIAN -->
                <div class="card shadow-sm mb-4">

                    <div class="card-header bg-primary text-white">

                        <h5 class="mb-0">
                            Pencarian dan Filter Riwayat
                        </h5>

                    </div>

                    <div class="card-body">

                        <form method="GET" action="riwayat.php">

                            <div class="row g-3">

                                <div class="col-md-9">

                                    <label for="cari" class="form-label">
                                        Cari Riwayat
                                    </label>

                                    <input
                                        type="text"
                                        name="cari"
                                        id="cari"
                                        class="form-control"
                                        placeholder="Nama siswa, pelanggaran, kelas..."
                                        value="<?= htmlspecialchars($cari); ?>">

                                </div>

                                <div class="col-md-3 d-flex align-items-end gap-2">

                                    <button type="submit" class="btn btn-primary">
                                        Cari
                                    </button>

                                    <a href="riwayat.php" class="btn btn-secondary">
                                        Reset
                                    </a>

                                </div>

                            </div>

                        </form>

                    </div>

                </div>

                <!-- TABEL RIWAYAT -->
                <div class="card shadow-sm">

                    <div class="card-header bg-primary text-white">

                        <h5 class="mb-0">
                            Data Riwayat Pelanggaran
                        </h5>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table id="tabelRiwayat"
                                   class="table table-striped table-bordered table-hover align-middle">

                                <thead class="table-dark">

                                    <tr>

                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Jenis Pelanggaran</th>
                                        <th>Poin</th>
                                        <th>Guru Pelapor</th>
                                        <th>Keterangan</th>
                                        <th>Tindakan</th>
                                        <th>Status</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php while ($data = mysqli_fetch_assoc($query)) { ?>

                                        <tr>

                                            <td></td>

                                            <td>
                                                <?= htmlspecialchars($data['tanggal'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['nama_siswa'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['nama_kelas'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['nama_pelanggaran'] ?? '-'); ?>
                                            </td>

                                            <td class="text-center">

                                                <span class="badge bg-warning text-dark">

                                                    <?= htmlspecialchars(
                                                        (string) ($data['poin'] ?? 0)
                                                    ); ?>

                                                </span>

                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['nama_guru'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['keterangan'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['tindakan'] ?? '-'); ?>
                                            </td>

                                            <td>

                                                <span class="badge bg-success">
                                                    Selesai
                                                </span>

                                            </td>

                                        </tr>

                                    <?php } ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            <?php } ?>

        </main>

    </div>

</div>

<!-- JQUERY -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- BOOTSTRAP JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- DATATABLES -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<!-- INISIALISASI -->
<script>

$(document).ready(function () {

    $('#tabelRiwayat').DataTable({

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        searching: true,

        ordering: true,

        paging: true,

        order: [[1, 'desc']],

        columnDefs: [
            {
                orderable: false,
                searchable: false,
                targets: 0
            }
        ],

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

            infoEmpty: "Tidak ada data riwayat",

            infoFiltered: "(difilter dari _MAX_ total data)",

            zeroRecords: "Riwayat pelanggaran tidak ditemukan",

            emptyTable: "Belum ada riwayat pelanggaran",

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

<?php

// Menutup statement jika tersedia
if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}

?>
