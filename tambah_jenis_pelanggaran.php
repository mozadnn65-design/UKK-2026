<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Ambil data kategori untuk dropdown
$kategori = mysqli_query($koneksi, "SELECT id, nama FROM t_pelanggaran_kategori ORDER BY nama ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Jenis Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tom Select (ketik langsung di kolom, ada dropdown) -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 ms-sm-auto px-4 py-4">
            <h2 class="mb-3">Tambah Jenis Pelanggaran</h2>

            <!-- Notifikasi -->
            <?php if (isset($_SESSION['pesan_error'])) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['pesan_error']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['pesan_error']); ?>
            <?php } ?>

            <?php if (isset($_SESSION['pesan_sukses'])) { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['pesan_sukses']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['pesan_sukses']); ?>
            <?php } ?>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    Form Tambah Jenis Pelanggaran
                </div>

                <div class="card-body">
                    <form action="proses_tambah_jenis_pelanggaran.php" method="POST">

                        <div class="mb-3">
                            <label class="form-label">Kategori Pelanggaran <span class="text-danger">*</span></label>
                            <select name="kategori_id" id="kategori_id" required>
                                <option value="">Ketik nama kategori...</option>
                                <?php while ($k = mysqli_fetch_assoc($kategori)) { ?>
                                    <option value="<?= $k['id']; ?>">
                                        <?= htmlspecialchars($k['nama']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kode Pelanggaran <span class="text-danger">*</span></label>
                            <input type="text" name="kode" class="form-control" required>
                            <div class="form-text">Kode harus unik, tidak boleh sama dengan yang sudah ada.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Pelanggaran <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Poin <span class="text-danger">*</span></label>
                            <input type="number" name="poin" class="form-control" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status_aktif" class="form-select" required>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Simpan Jenis Pelanggaran
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    new TomSelect('#kategori_id', {
        placeholder: 'Ketik nama kategori...',
        maxOptions: null
    });
</script>

</body>
</html>