<?php
// catat_pelanggaran.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Guru yang sedang login (otomatis menjadi guru pelapor)
// Dicari dari tabel t_guru berdasarkan akun (user_id) yang login
$guru_login = 0;
$user_login = (int) ($_SESSION['user_id'] ?? 0);

if ($user_login > 0) {
    $q_guru_login = mysqli_query(
        $koneksi,
        "SELECT id
         FROM t_guru
         WHERE user_id = $user_login
           AND status_aktif = 1
         LIMIT 1"
    );

    if ($q_guru_login) {
        $data_guru_login = mysqli_fetch_assoc($q_guru_login);

        if ($data_guru_login) {
            $guru_login = (int) $data_guru_login['id'];
        }
    }
}

// Daftar siswa beserta kelasnya
$siswa = mysqli_query(
    $koneksi,
    "SELECT
        s.id,
        s.nis,
        s.nama,
        ks.kelas_id,
        k.nama AS nama_kelas,
        k.tingkat,
        k.jurusan
     FROM t_siswa s
     INNER JOIN t_kelas_siswa ks
        ON s.id = ks.siswa_id
     INNER JOIN t_kelas k
        ON ks.kelas_id = k.id
     WHERE s.status_aktif = 1
       AND ks.status_aktif = 1
     ORDER BY s.nama ASC"
);

// Tahun ajaran aktif (otomatis memakai yang terbaru)
$tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY id DESC"
);
$tahun_aktif = mysqli_fetch_assoc($tahun);

// Daftar pelanggaran
$pelanggaran = mysqli_query(
    $koneksi,
    "SELECT id, nama, poin, pelanggaran_kategori_id
     FROM t_pelanggaran
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// Daftar guru (hanya dipakai jika yang login bukan guru, misalnya admin)
$guru = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_guru
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// Data catatan pelanggaran
$sql = "SELECT
            ps.*,
            ta.nama AS tahun_ajaran,
            s.nis,
            k.tingkat,
            k.jurusan
        FROM t_pelanggaran_siswa ps
        LEFT JOIN t_tahun_ajaran ta
            ON ps.tahun_ajaran_id = ta.id
        LEFT JOIN t_siswa s
            ON ps.siswa_id = s.id
        LEFT JOIN t_kelas k
            ON ps.kelas_id = k.id
        ORDER BY ps.id DESC";

$hasil = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Catat Pelanggaran Siswa</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
          rel="stylesheet">

    <!-- Tom Select (dropdown yang bisa dicari) -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css"
          rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 p-4">

            <div class="mb-4">
                <h2 class="mb-1">Catat Pelanggaran Siswa</h2>
            </div>

            <?php if (isset($_SESSION['pesan_sukses'])) { ?>
                <div class="alert alert-success">
                    <?php
                    echo htmlspecialchars($_SESSION['pesan_sukses']);
                    unset($_SESSION['pesan_sukses']);
                    ?>
                </div>
            <?php } ?>

            <?php if (isset($_SESSION['pesan_error'])) { ?>
                <div class="alert alert-danger">
                    <?php
                    echo htmlspecialchars($_SESSION['pesan_error']);
                    unset($_SESSION['pesan_error']);
                    ?>
                </div>
            <?php } ?>


            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Form Catat Pelanggaran</h5>
                </div>

                <form action="proses_simpan_pelanggaran.php"
                      method="POST">

                    <div class="card-body p-4">

                        <div class="row g-3">

                            <!-- Peringatan jika tidak ada tahun ajaran aktif -->
                            <?php if (!$tahun_aktif) { ?>

                                <div class="col-12">
                                    <div class="alert alert-warning mb-0">
                                        Belum ada tahun ajaran aktif. Aktifkan tahun ajaran
                                        terlebih dahulu sebelum mencatat pelanggaran.
                                    </div>
                                </div>

                            <?php } ?>

                            <!-- PILIH SISWA (bisa dicari) -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    Siswa <span class="text-danger">*</span>
                                </label>

                                <select name="siswa_id"
                                        id="siswa"
                                        class="form-select"
                                        required>

                                    <option value="">-- Cari / Pilih Siswa --</option>

                                    <?php while ($s = mysqli_fetch_assoc($siswa)) { ?>

                                        <option
                                            value="<?php echo $s['id']; ?>"
                                            data-kelas-id="<?php echo $s['kelas_id']; ?>">

                                            <?php echo htmlspecialchars(
                                                $s['nis'] . ' - ' . $s['nama'] .
                                                ' (' . $s['nama_kelas'] . ')'
                                            ); ?>

                                        </option>

                                    <?php } ?>

                                </select>

                                <div class="form-text">
                                    Ketik nama atau NIS siswa. Kelas terisi otomatis.
                                </div>
                            </div>

                            <!-- JENIS PELANGGARAN (bisa dicari) -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    Pelanggaran <span class="text-danger">*</span>
                                </label>

                                <select name="pelanggaran_id"
                                        id="pelanggaran"
                                        class="form-select"
                                        required>

                                    <option value="">-- Cari / Pilih Pelanggaran --</option>

                                    <?php while ($p = mysqli_fetch_assoc($pelanggaran)) { ?>

                                        <option
                                            value="<?php echo $p['id']; ?>"
                                            data-poin="<?php echo $p['poin']; ?>"
                                            data-kategori="<?php echo $p['pelanggaran_kategori_id']; ?>">

                                            <?php echo htmlspecialchars(
                                                $p['nama'] . ' (' .
                                                $p['poin'] . ' poin)'
                                            ); ?>

                                        </option>

                                    <?php } ?>

                                </select>

                                <div class="form-text">
                                    Ketik untuk mencari. Poin sudah tercantum di setiap pilihan.
                                </div>
                            </div>

                            <!-- TANGGAL -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    Tanggal <span class="text-danger">*</span>
                                </label>

                                <input type="date"
                                       name="tanggal"
                                       class="form-control"
                                       value="<?php echo date('Y-m-d'); ?>"
                                       required>
                            </div>

                            <!-- GURU PELAPOR -->
                            <?php if ($guru_login > 0) { ?>

                                <!-- Login sebagai guru: otomatis guru yang login -->
                                <input type="hidden"
                                       name="guru_id"
                                       value="<?php echo $guru_login; ?>">

                            <?php } else { ?>

                                <!-- Bukan guru (misalnya admin): pilih guru manual -->
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Guru Pelapor <span class="text-danger">*</span>
                                    </label>

                                    <select name="guru_id"
                                            class="form-select"
                                            required>

                                        <option value="">-- Pilih Guru --</option>

                                        <?php while ($g = mysqli_fetch_assoc($guru)) { ?>

                                            <option value="<?php echo $g['id']; ?>">
                                                <?php echo htmlspecialchars($g['nama']); ?>
                                            </option>

                                        <?php } ?>

                                    </select>
                                </div>

                            <?php } ?>

                            <!-- KETERANGAN -->
                            <div class="col-12">
                                <label class="form-label">Keterangan</label>

                                <textarea name="keterangan"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Tuliskan kronologi singkat pelanggaran (opsional)"></textarea>
                            </div>

                        </div>

                        <!-- Diisi otomatis (tidak ditampilkan) -->
                        <input type="hidden" name="kelas_id" id="kelas">

                        <?php if ($tahun_aktif) { ?>
                            <input type="hidden"
                                   name="tahun_ajaran_id"
                                   value="<?php echo $tahun_aktif['id']; ?>">
                        <?php } ?>

                        <!-- Diisi otomatis (tidak perlu diisi manual) -->
                        <input type="hidden" name="poin" id="poin">
                        <input type="hidden" name="pelanggaran_kategori_id" id="kategori">
                        <input type="hidden" name="status" value="belum">
                        <input type="hidden" name="tindakan" value="">

                    </div>

                    <div class="card-footer bg-white d-flex justify-content-end gap-2 py-3">

                        
                    <a href="dashboard.php" class="btn btn-secondary">
                        Kembali ke Dashboard
                    </a>
                

                        <button type="submit"
                                class="btn btn-primary px-4"
                                <?php echo $tahun_aktif ? '' : 'disabled'; ?>>
                            Simpan Pelanggaran
                        </button>

                    </div>

                </form>
            </div>

            <div class="card shadow-sm">

                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Data Catatan Pelanggaran</h5>
                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table id="tabelCatat"
                               class="table table-bordered table-striped table-hover align-middle">

                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Pelanggaran</th>
                                    <th>Guru</th>
                                    <th>Tanggal</th>
                                    <th>Poin</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>

                            <?php while ($row = mysqli_fetch_assoc($hasil)) { ?>

                                <tr>

                                    <!-- Nomor diisi otomatis oleh DataTables -->
                                    <td></td>

                                    <td>
                                        <?php echo htmlspecialchars($row['nama_siswa'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row['nama_kelas'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row['tahun_ajaran'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row['nama_pelanggaran'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row['nama_guru'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row['tanggal'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?php echo (int) ($row['poin'] ?? 0); ?>
                                    </td>

                                    <td>
                                        <?php
                                        $status_data = strtolower(
                                            trim($row['status'] ?? '')
                                        );

                                        if ($status_data == 'selesai') {
                                            echo '<span class="badge bg-success">Selesai</span>';
                                        } elseif (
                                            $status_data == 'belum' ||
                                            $status_data == 'belum ditindak'
                                        ) {
                                            echo '<span class="badge bg-danger">Belum Ditindak</span>';
                                        } else {
                                            echo '<span class="badge bg-secondary">'
                                                . htmlspecialchars($row['status'] ?? '-')
                                                . '</span>';
                                        }
                                        ?>
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

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<!-- Tom Select JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<!-- JavaScript Siswa (bisa dicari, kelas otomatis) -->
<script>

const inputKelasId = document.getElementById('kelas');

// Simpan kelas tiap siswa (dikirim otomatis lewat input tersembunyi)
const dataSiswa = {};

document.querySelectorAll('#siswa option').forEach(function (opsi) {
    if (opsi.value !== '') {
        dataSiswa[opsi.value] = {
            kelasId: opsi.dataset.kelasId || ''
        };
    }
});

new TomSelect('#siswa', {
    maxOptions: null,
    placeholder: 'Ketik nama atau NIS siswa...',
    onChange: function (nilai) {
        const data = dataSiswa[nilai];

        inputKelasId.value = data ? data.kelasId : '';
    }
});

</script>

<!-- JavaScript Jenis Pelanggaran (bisa dicari, poin & kategori otomatis) -->
<script>

const inputPoin = document.getElementById('poin');
const inputKategori = document.getElementById('kategori');

// Simpan poin & kategori tiap pelanggaran sebelum dropdown diubah jadi pencarian
const dataPelanggaran = {};

document.querySelectorAll('#pelanggaran option').forEach(function (opsi) {
    if (opsi.value !== '') {
        dataPelanggaran[opsi.value] = {
            poin: opsi.dataset.poin || '',
            kategori: opsi.dataset.kategori || ''
        };
    }
});

new TomSelect('#pelanggaran', {
    maxOptions: null,
    placeholder: 'Ketik untuk mencari pelanggaran...',
    onChange: function (nilai) {
        const data = dataPelanggaran[nilai];

        inputPoin.value = data ? data.poin : '';
        inputKategori.value = data ? data.kategori : '';
    }
});

</script>

<!-- Inisialisasi DataTables (tabel bisa dicari) -->
<script>
$(document).ready(function () {
    $('#tabelCatat').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],

        searching: true,
        ordering: true,
        paging: true,

        // Urutkan berdasarkan tanggal terbaru
        order: [[6, 'desc']],

        columnDefs: [
            {
                orderable: false,
                searchable: false,
                targets: 0
            },
            {
                className: 'text-center',
                targets: [0, 7, 8]
            },
            {
                className: 'text-nowrap',
                targets: 6
            }
        ],

        // Nomor urut menyesuaikan pencarian dan halaman
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
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data pelanggaran",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Data pelanggaran tidak ditemukan",
            emptyTable: "Belum ada catatan pelanggaran",
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