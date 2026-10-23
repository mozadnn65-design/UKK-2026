
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

$query = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran WHERE id = $id");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Data jenis pelanggaran tidak ditemukan!");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Jenis Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 ms-sm-auto px-4 py-4">
            <h2 class="mb-3">Edit Jenis Pelanggaran</h2>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    Form Edit Jenis Pelanggaran
                </div>

                <div class="card-body">
                    <form action="proses_edit_jenis_pelanggaran.php" method="POST">

                        <input type="hidden" name="id" value="<?= $data['id']; ?>">

                        <div class="mb-3">
                            <label class="form-label">Kode Pelanggaran</label>
                            <input type="text" name="kode" class="form-control"
                                   value="<?= $data['kode']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Pelanggaran</label>
                            <input type="text" name="nama" class="form-control"
                                   value="<?= $data['nama']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Poin</label>
                            <input type="number" name="poin" class="form-control"
                                   min="0" value="<?= $data['poin']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3"><?= $data['deskripsi']; ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status_aktif" class="form-select" required>
                                <option value="1" <?= $data['status_aktif'] == 1 ? 'selected' : ''; ?>>
                                    Aktif
                                </option>
                                <option value="0" <?= $data['status_aktif'] == 0 ? 'selected' : ''; ?>>
                                    Tidak Aktif
                                </option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Update Jenis Pelanggaran
                        </button>

                        <a href="kelola_jenis_pelanggaran.php" class="btn btn-secondary">
                            Kembali
                        </a>

                    </form>
                </div>
            </div>
        </main>

    </div>
</div>

</body>
</html>
