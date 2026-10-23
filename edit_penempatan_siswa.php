
<?php
// edit_penempatan_siswa.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

$data = mysqli_query($koneksi, "SELECT * FROM t_kelas_siswa WHERE id = $id");
$row = mysqli_fetch_assoc($data);

if (!$row) {
    $_SESSION['pesan_error'] = "Data penempatan tidak ditemukan!";
    header("Location: penempatan_siswa.php");
    exit;
}

$siswa = mysqli_query($koneksi, "SELECT id, nis, nama FROM t_siswa ORDER BY nama ASC");
$kelas = mysqli_query($koneksi, "SELECT id, nama FROM t_kelas ORDER BY nama ASC");
$tahun = mysqli_query($koneksi, "SELECT id, nama FROM t_tahun_ajaran ORDER BY nama DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Penempatan Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="container-fluid">
    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 p-4">

            <h2 class="mb-3">Edit Penempatan Siswa</h2>

            <?php if (isset($_SESSION['pesan_error'])) { ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['pesan_error']); ?>
                </div>
                <?php unset($_SESSION['pesan_error']); ?>
            <?php } ?>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    Form Edit Penempatan Siswa
                </div>

                <div class="card-body">

                    <form action="proses_edit_penempatan_siswa.php" method="POST">

                        <input type="hidden" name="id" value="<?= $row['id']; ?>">

                        <div class="mb-3">
                            <label class="form-label">Siswa</label>
                            <select name="siswa_id" class="form-select" required>
                                <option value="">-- Pilih Siswa --</option>
                                <?php while ($item = mysqli_fetch_assoc($siswa)) { ?>
                                    <option value="<?= $item['id']; ?>"
                                        <?= $item['id'] == $row['siswa_id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($item['nis'] . ' - ' . $item['nama']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tahun Ajaran</label>
                            <select name="tahun_ajaran_id" class="form-select" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                <?php while ($item = mysqli_fetch_assoc($tahun)) { ?>
                                    <option value="<?= $item['id']; ?>"
                                        <?= $item['id'] == $row['tahun_ajaran_id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($item['nama']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kelas</label>
                            <select name="kelas_id" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php while ($item = mysqli_fetch_assoc($kelas)) { ?>
                                    <option value="<?= $item['id']; ?>"
                                        <?= $item['id'] == $row['kelas_id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($item['nama']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai"
                                   class="form-control"
                                   value="<?= htmlspecialchars($row['tanggal_mulai'] ?? ''); ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai"
                                   class="form-control"
                                   value="<?= htmlspecialchars($row['tanggal_selesai'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status_aktif" class="form-select" required>
                                <option value="1" <?= $row['status_aktif'] == 1 ? 'selected' : ''; ?>>
                                    Aktif
                                </option>
                                <option value="0" <?= $row['status_aktif'] == 0 ? 'selected' : ''; ?>>
                                    Tidak Aktif
                                </option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Update Penempatan
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
