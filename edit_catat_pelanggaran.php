<?php
// edit_catat_pelanggaran.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

// Data catatan yang diedit
$stmt = mysqli_prepare($koneksi, "SELECT * FROM t_pelanggaran_siswa WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$row) {
    $_SESSION['pesan_error'] = "Data catatan pelanggaran tidak ditemukan.";
    header("Location: catat_pelanggaran.php");
    exit;
}

// Pilihan dropdown
$siswa = mysqli_query($koneksi, "SELECT id, nis, nama FROM t_siswa ORDER BY nama");
$pelanggaran = mysqli_query($koneksi, "SELECT id, kode, nama, poin FROM t_pelanggaran WHERE status_aktif = 1 OR id = " . (int) $row['pelanggaran_id'] . " ORDER BY kode");
$guru = mysqli_query($koneksi, "SELECT id, nama FROM t_guru ORDER BY nama");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Catatan Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 p-4">

            <h2 class="mb-4">Edit Catatan Pelanggaran</h2>

            <?php if (isset($_SESSION['pesan_error'])) { ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= htmlspecialchars($_SESSION['pesan_error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['pesan_error']); ?>
            <?php } ?>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    Form Edit Catatan Pelanggaran
                </div>

                <div class="card-body">
                    <form action="proses_edit_catat_pelanggaran.php" method="POST">

                        <input type="hidden" name="id" value="<?= (int) $row['id']; ?>">

                        <div class="mb-3">
                            <label class="form-label">Siswa</label>
                            <select name="siswa_id" class="form-select" required>
                                <?php while ($s = mysqli_fetch_assoc($siswa)) { ?>
                                    <option value="<?= (int) $s['id']; ?>"
                                        <?= $s['id'] == $row['siswa_id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($s['nama']); ?> (<?= htmlspecialchars($s['nis']); ?>)
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pelanggaran</label>
                            <select name="pelanggaran_id" class="form-select" required>
                                <?php while ($p = mysqli_fetch_assoc($pelanggaran)) { ?>
                                    <option value="<?= (int) $p['id']; ?>"
                                        <?= $p['id'] == $row['pelanggaran_id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($p['kode'] . ' - ' . $p['nama'] . ' (' . $p['poin'] . ' poin)'); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Guru Pencatat</label>
                            <select name="guru_id" class="form-select" required>
                                <?php while ($g = mysqli_fetch_assoc($guru)) { ?>
                                    <option value="<?= (int) $g['id']; ?>"
                                        <?= $g['id'] == $row['guru_id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($g['nama']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control"
                                   value="<?= htmlspecialchars($row['tanggal']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3" required><?= htmlspecialchars($row['keterangan']); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tindakan</label>
                            <input type="text" name="tindakan" class="form-control"
                                   value="<?= htmlspecialchars($row['tindakan']); ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <?php foreach (['tercatat', 'diproses', 'selesai'] as $st) { ?>
                                    <option value="<?= $st; ?>" <?= $row['status'] == $st ? 'selected' : ''; ?>>
                                        <?= ucfirst($st); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="catat_pelanggaran.php" class="btn btn-secondary">Kembali</a>

                    </form>
                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>