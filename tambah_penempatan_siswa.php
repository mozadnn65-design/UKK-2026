<?php
// tambah_penempatan_siswa.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Hanya siswa yang belum ditempatkan pada tahun ajaran aktif
$siswa = mysqli_query($koneksi, "
    SELECT s.id, s.nis, s.nama
    FROM t_siswa s
    WHERE NOT EXISTS (
        SELECT 1
        FROM t_kelas_siswa ks
        JOIN t_tahun_ajaran ta ON ta.id = ks.tahun_ajaran_id
        WHERE ks.siswa_id = s.id
          AND ta.status_aktif = 1
    )
    ORDER BY s.nama ASC
");

$kelas = mysqli_query($koneksi, "SELECT id, nama FROM t_kelas ORDER BY nama ASC");

// Hanya tahun ajaran yang aktif
$tahun = mysqli_query($koneksi, "
    SELECT id, nama
    FROM t_tahun_ajaran
    WHERE status_aktif = 1
    ORDER BY nama DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Penempatan Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="container-fluid">
    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 p-4">

            <h2 class="mb-3">Tambah Penempatan Siswa</h2>

            <?php if (isset($_SESSION['pesan_error'])) { ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['pesan_error']); ?>
                </div>
                <?php unset($_SESSION['pesan_error']); ?>
            <?php } ?>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    Form Tambah Penempatan Siswa
                </div>

                <div class="card-body">

                    <form action="proses_tambah_penempatan_siswa.php" method="POST">

                        <div class="mb-3">
                            <label class="form-label">Siswa</label>
                            <select name="siswa_id" class="form-select" required>
                                <option value="">-- Pilih Siswa --</option>
                                <?php if (mysqli_num_rows($siswa) > 0) { ?>
                                    <?php while ($row = mysqli_fetch_assoc($siswa)) { ?>
                                        <option value="<?= $row['id']; ?>">
                                            <?= htmlspecialchars($row['nama']); ?>
                                        </option>
                                    <?php } ?>
                                <?php } else { ?>
                                    <option value="" disabled>Semua siswa sudah ditempatkan</option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tahun Ajaran</label>
                            <select name="tahun_ajaran_id" class="form-select" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                <?php if (mysqli_num_rows($tahun) > 0) { ?>
                                    <?php while ($row = mysqli_fetch_assoc($tahun)) { ?>
                                        <option value="<?= $row['id']; ?>">
                                            <?= htmlspecialchars($row['nama']); ?>
                                        </option>
                                    <?php } ?>
                                <?php } else { ?>
                                    <option value="" disabled>Belum ada tahun ajaran aktif</option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kelas</label>
                            <select name="kelas_id" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php while ($row = mysqli_fetch_assoc($kelas)) { ?>
                                    <option value="<?= $row['id']; ?>">
                                        <?= htmlspecialchars($row['nama']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai"
                                   class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai"
                                   class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status_aktif" class="form-select" required>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Simpan Penempatan
                        </button>

                        <a href="penempatan_siswa.php" class="btn btn-secondary">
                            Kembali
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