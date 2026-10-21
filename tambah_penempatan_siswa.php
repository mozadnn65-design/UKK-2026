
<?php
// tambah_penempatan_siswa.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Mengambil daftar siswa aktif
$siswa = mysqli_query(
    $koneksi,
    "SELECT id, nis, nama
     FROM t_siswa
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// Mengambil daftar tahun ajaran aktif
$tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY id DESC"
);

// Mengambil daftar kelas aktif
$kelas = mysqli_query(
    $koneksi,
    "SELECT id, nama, tingkat, jurusan
     FROM t_kelas
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Penempatan Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 p-4">

            <h2 class="mb-3">Tambah Penempatan Siswa</h2>

            <a href="penempatan_siswa.php" class="btn btn-secondary mb-3">
                Kembali
            </a>

            <?php if (isset($_SESSION['pesan_error'])) { ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['pesan_error']); ?>
                </div>
                <?php unset($_SESSION['pesan_error']); ?>
            <?php } ?>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    Form Penempatan Siswa
                </div>

                <div class="card-body">

                    <form action="proses_penempatan_siswa.php" method="POST">

                        <!-- Pilih siswa -->
                        <div class="mb-3">
                            <label for="siswa_id" class="form-label">
                                Siswa
                            </label>

                            <select name="siswa_id" id="siswa_id"
                                    class="form-select" required>
                                <option value="">-- Pilih Siswa --</option>

                                <?php if ($siswa) {
                                    while ($s = mysqli_fetch_assoc($siswa)) { ?>
                                        <option value="<?= $s['id']; ?>">
                                            <?= htmlspecialchars($s['nis'] . ' - ' . $s['nama']); ?>
                                        </option>
                                <?php }
                                } ?>
                            </select>
                        </div>

                        <!-- Pilih tahun ajaran -->
                        <div class="mb-3">
                            <label for="tahun_ajaran_id" class="form-label">
                                Tahun Ajaran
                            </label>

                            <select name="tahun_ajaran_id" id="tahun_ajaran_id"
                                    class="form-select" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>

                                <?php if ($tahun) {
                                    while ($ta = mysqli_fetch_assoc($tahun)) { ?>
                                        <option value="<?= $ta['id']; ?>">
                                            <?= htmlspecialchars($ta['nama']); ?>
                                        </option>
                                <?php }
                                } ?>
                            </select>
                        </div>

                        <!-- Pilih kelas -->
                        <div class="mb-3">
                            <label for="kelas_id" class="form-label">
                                Kelas
                            </label>

                            <select name="kelas_id" id="kelas_id"
                                    class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>

                                <?php if ($kelas) {
                                    while ($k = mysqli_fetch_assoc($kelas)) { ?>
                                        <option value="<?= $k['id']; ?>">
                                            <?= htmlspecialchars(
                                                $k['nama'] . ' - Tingkat ' .
                                                $k['tingkat'] . ' - ' . $k['jurusan']
                                            ); ?>
                                        </option>
                                <?php }
                                } ?>
                            </select>
                        </div>

                        <!-- Tanggal penempatan -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_mulai" class="form-label">
                                    Tanggal Mulai
                                </label>

                                <input type="date" name="tanggal_mulai"
                                       id="tanggal_mulai"
                                       class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tanggal_selesai" class="form-label">
                                    Tanggal Selesai
                                </label>

                                <input type="date" name="tanggal_selesai"
                                       id="tanggal_selesai"
                                       class="form-control" required>
                            </div>
                        </div>

                        <!-- Status penempatan -->
                        <div class="mb-3">
                            <label for="status_aktif" class="form-label">
                                Status
                            </label>

                            <select name="status_aktif" id="status_aktif"
                                    class="form-select" required>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Simpan Penempatan
                        </button>

                        <a href="penempatan_siswa.php"
                           class="btn btn-secondary">
                            Batal
                        </a>

                    </form>

                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
