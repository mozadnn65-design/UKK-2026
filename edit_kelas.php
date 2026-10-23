
<?php
// edit_kelas.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID kelas tidak valid.");
}

$id = (int) $_GET['id'];

$stmt = mysqli_prepare($koneksi, "SELECT * FROM t_kelas WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);

if (!$data) {
    die("Data kelas tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Kelas</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 p-4">

            <h1 class="mb-4">Edit Kelas</h1>

            <div class="card shadow-sm">

                <div class="card-header bg-primary text-white">
                    Form Edit Kelas
                </div>

                <div class="card-body">

                    <form action="proses_edit_kelas.php"
                          method="POST"
                          id="formEditKelas">

                        <input type="hidden"
                               name="id"
                               value="<?= htmlspecialchars((string)$data['id']) ?>">

                        <div class="mb-3">
                            <label for="nama" class="form-label">
                                Nama Kelas
                            </label>

                            <input type="text"
                                   name="nama"
                                   id="nama"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['nama'] ?? '') ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="tingkat" class="form-label">
                                Tingkat
                            </label>

                            <input type="text"
                                   name="tingkat"
                                   id="tingkat"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['tingkat'] ?? '') ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="jurusan" class="form-label">
                                Jurusan
                            </label>

                            <input type="text"
                                   name="jurusan"
                                   id="jurusan"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['jurusan'] ?? '') ?>"
                                   required>
                        </div>

                        <div class="mb-4">
                            <label for="status_aktif" class="form-label">
                                Status Aktif
                            </label>

                            <select name="status_aktif"
                                    id="status_aktif"
                                    class="form-select"
                                    required>

                                <option value="1"
                                    <?= $data['status_aktif'] == 1 ? 'selected' : '' ?>>
                                    Aktif
                                </option>

                                <option value="0"
                                    <?= $data['status_aktif'] == 0 ? 'selected' : '' ?>>
                                    Tidak Aktif
                                </option>

                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Update Kelas
                        </button>

                        <a href="kelola_kelas.php"
                           class="btn btn-secondary">
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
