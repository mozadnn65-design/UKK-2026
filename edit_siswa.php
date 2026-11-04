<?php
// edit_siswa.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Ambil ID siswa dari URL
$id = $_GET['id'];

// Ambil data siswa dari database
$data = mysqli_query($koneksi, "SELECT * FROM t_siswa WHERE id='$id'");
$siswa = mysqli_fetch_assoc($data);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="container-fluid">
    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 p-4">

            <h2>Edit Siswa</h2>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    Form Edit Siswa
                </div>

                <div class="card-body">

                    <form action="proses_edit_siswa.php" method="POST">

                        <input type="hidden" name="id"
                               value="<?= $siswa['id']; ?>">

                        <div class="mb-3">
                            <label class="form-label">NIS</label>
                            <input type="text" name="nis"
                                   class="form-control" maxlength="10"
                                   value="<?= $siswa['nis']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NISN</label>
                            <input type="text" name="nisn"
                                   class="form-control" maxlength="10"
                                   value="<?= $siswa['nisn']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Siswa</label>
                            <input type="text" name="nama"
                                   class="form-control"
                                   value="<?= $siswa['nama']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Jenis Kelamin</label>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="jenis_kelamin" id="laki"
                                       value="L" required>
                                <label class="form-check-label" for="laki">Laki-laki</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio"
                                       name="jenis_kelamin" id="perempuan"
                                       value="P" required>
                                <label class="form-check-label" for="perempuan">Perempuan</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir"
                                   class="form-control"
                                   min="2000-01-01" max="<?= date('Y-m-d'); ?>"
                                   value="<?= $siswa['tanggal_lahir']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control"
                                      required><?= $siswa['alamat']; ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status Aktif</label>
                            <select name="status_aktif" class="form-select" required>
                                <option value="1"
                                    <?= $siswa['status_aktif'] == 1 ? 'selected' : ''; ?>>
                                    Aktif
                                </option>

                                <option value="0"
                                    <?= $siswa['status_aktif'] == 0 ? 'selected' : ''; ?>>
                                    Tidak Aktif
                                </option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Simpan Perubahan
                        </button>

                        <a href="kelola_siswa.php" class="btn btn-secondary">
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