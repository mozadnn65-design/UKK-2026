
<?php
// edit_wali_kelas.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Validasi ID
$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    exit('ID wali kelas tidak valid.');
}

// Ambil data wali kelas
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM t_wali_kelas WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);

if (!$data) {
    exit('Data wali kelas tidak ditemukan.');
}

// Ambil data pilihan
$tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_tahun_ajaran ORDER BY id DESC"
);

$kelas = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_kelas ORDER BY nama ASC"
);

$guru = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_guru ORDER BY nama ASC"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Wali Kelas</title>

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

            <h1 class="mb-4">Edit Wali Kelas</h1>

            <div class="card shadow-sm">

                <div class="card-header bg-primary text-white">
                    Form Edit Wali Kelas
                </div>

                <div class="card-body">

                    <form action="proses_edit_wali_kelas.php"
                          method="POST"
                          id="formEditWaliKelas">

                        <input type="hidden"
                               name="id"
                               value="<?= htmlspecialchars((string)$data['id']) ?>">

                        <!-- TAHUN AJARAN -->
                        <div class="mb-3">
                            <label for="tahun_ajaran_id" class="form-label">
                                Tahun Ajaran
                            </label>

                            <select name="tahun_ajaran_id"
                                    id="tahun_ajaran_id"
                                    class="form-select"
                                    required>

                                <option value="">Pilih Tahun Ajaran</option>

                                <?php while ($row = mysqli_fetch_assoc($tahun)) { ?>
                                    <option
                                        value="<?= htmlspecialchars((string)$row['id']) ?>"
                                        <?= $row['id'] == $data['tahun_ajaran_id']
                                            ? 'selected' : '' ?>>

                                        <?= htmlspecialchars($row['nama']) ?>

                                    </option>
                                <?php } ?>

                            </select>
                        </div>

                        <!-- KELAS -->
                        <div class="mb-3">
                            <label for="kelas_id" class="form-label">
                                Kelas
                            </label>

                            <select name="kelas_id"
                                    id="kelas_id"
                                    class="form-select"
                                    required>

                                <option value="">Pilih Kelas</option>

                                <?php while ($row = mysqli_fetch_assoc($kelas)) { ?>
                                    <option
                                        value="<?= htmlspecialchars((string)$row['id']) ?>"
                                        <?= $row['id'] == $data['kelas_id']
                                            ? 'selected' : '' ?>>

                                        <?= htmlspecialchars($row['nama']) ?>

                                    </option>
                                <?php } ?>

                            </select>
                        </div>

                        <!-- GURU -->
                        <div class="mb-3">
                            <label for="guru_id" class="form-label">
                                Guru
                            </label>

                            <select name="guru_id"
                                    id="guru_id"
                                    class="form-select"
                                    required>

                                <option value="">Pilih Guru</option>

                                <?php while ($row = mysqli_fetch_assoc($guru)) { ?>
                                    <option
                                        value="<?= htmlspecialchars((string)$row['id']) ?>"
                                        <?= $row['id'] == $data['guru_id']
                                            ? 'selected' : '' ?>>

                                        <?= htmlspecialchars($row['nama']) ?>

                                    </option>
                                <?php } ?>

                            </select>
                        </div>

                        <!-- TANGGAL MULAI -->
                        <div class="mb-3">
                            <label for="tanggal_mulai" class="form-label">
                                Tanggal Mulai
                            </label>

                            <input type="date"
                                   name="tanggal_mulai"
                                   id="tanggal_mulai"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['tanggal_mulai']) ?>"
                                   required>
                        </div>

                        <!-- TANGGAL SELESAI -->
                        <div class="mb-3">
                            <label for="tanggal_selesai" class="form-label">
                                Tanggal Selesai
                            </label>

                            <input type="date"
                                   name="tanggal_selesai"
                                   id="tanggal_selesai"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['tanggal_selesai']) ?>"
                                   required>
                        </div>

                        <!-- STATUS -->
                        <div class="mb-4">
                            <label for="status_aktif" class="form-label">
                                Status
                            </label>

                            <select name="status_aktif"
                                    id="status_aktif"
                                    class="form-select"
                                    required>

                                <option value="1"
                                    <?= $data['status_aktif'] == 1
                                        ? 'selected' : '' ?>>
                                    Aktif
                                </option>

                                <option value="0"
                                    <?= $data['status_aktif'] == 0
                                        ? 'selected' : '' ?>>
                                    Tidak Aktif
                                </option>

                            </select>
                        </div>

                        <!-- TOMBOL -->
                        <button type="submit" class="btn btn-primary">
                            Update Wali Kelas
                        </button>

                        <a href="kelola_wali_kelas.php"
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
