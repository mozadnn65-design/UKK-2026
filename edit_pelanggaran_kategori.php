<?php
// edit_pelanggaran_kategori.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// Validasi ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID kategori pelanggaran tidak valid.");
}

$id = (int) $_GET['id'];

// Ambil data kategori dengan prepared statement
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM t_pelanggaran_kategori WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);

mysqli_stmt_close($stmt);

if (!$data) {
    die("Data kategori pelanggaran tidak ditemukan.");
}

// =====================================================
// KATEGORI LAIN (untuk cek nama & deskripsi yang sama)
// =====================================================
$nama_lain = [];
$deskripsi_lain = [];

$stmt_lain = mysqli_prepare(
    $koneksi,
    "SELECT nama, deskripsi FROM t_pelanggaran_kategori WHERE id <> ?"
);

mysqli_stmt_bind_param($stmt_lain, "i", $id);
mysqli_stmt_execute($stmt_lain);

$hasil_lain = mysqli_stmt_get_result($stmt_lain);

while ($k = mysqli_fetch_assoc($hasil_lain)) {
    $nama = mb_strtolower(trim(preg_replace('/\s+/', ' ', (string) $k['nama'])));
    $deskripsi = mb_strtolower(trim(preg_replace('/\s+/', ' ', (string) ($k['deskripsi'] ?? ''))));

    if ($nama !== '') {
        $nama_lain[] = $nama;
    }

    if ($deskripsi !== '') {
        $deskripsi_lain[] = $deskripsi;
    }
}

mysqli_stmt_close($stmt_lain);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Kategori Pelanggaran</title>

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

            <h1 class="mb-4">Edit Kategori Pelanggaran</h1>

            <?php if (isset($_SESSION['pesan_sukses'])) { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['pesan_sukses']); ?>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Tutup"></button>
                </div>
                <?php unset($_SESSION['pesan_sukses']); ?>
            <?php } ?>

            <?php if (isset($_SESSION['pesan_error'])) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['pesan_error']); ?>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Tutup"></button>
                </div>
                <?php unset($_SESSION['pesan_error']); ?>
            <?php } ?>

            <!-- Notifikasi kategori / deskripsi sudah ada -->
            <div id="notifSama"
                 class="alert alert-danger alert-dismissible d-none"
                 role="alert">
                <span id="teksNotif"></span>
                <button type="button"
                        class="btn-close"
                        id="tutupNotif"
                        aria-label="Tutup"></button>
            </div>

            <div class="card shadow-sm">

                <div class="card-header bg-primary text-white">
                    Form Edit Kategori Pelanggaran
                </div>

                <div class="card-body">

                    <form action="proses_edit_pelanggaran_kategori.php"
                          method="POST"
                          id="formEditKategori">

                        <input type="hidden"
                               name="id"
                               value="<?= htmlspecialchars((string) $data['id']) ?>">

                        <!-- NAMA KATEGORI -->
                        <div class="mb-3">
                            <label for="nama" class="form-label">
                                Nama Kategori
                            </label>

                            <input type="text"
                                   name="nama"
                                   id="nama"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['nama'] ?? '') ?>"
                                   required>
                        </div>

                        <!-- DESKRIPSI -->
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">
                                Deskripsi
                            </label>

                            <textarea name="deskripsi"
                                      id="deskripsi"
                                      class="form-control"
                                      rows="4"><?= htmlspecialchars($data['deskripsi'] ?? '') ?></textarea>
                        </div>

                        <!-- STATUS AKTIF -->
                        <div class="mb-4">
                            <label for="status_aktif" class="form-label">
                                Status Aktif
                            </label>

                            <select name="status_aktif"
                                    id="status_aktif"
                                    class="form-select"
                                    required>

                                <option value="1"
                                    <?= (int) $data['status_aktif'] === 1
                                        ? 'selected' : '' ?>>
                                    Aktif
                                </option>

                                <option value="0"
                                    <?= (int) $data['status_aktif'] === 0
                                        ? 'selected' : '' ?>>
                                    Tidak Aktif
                                </option>

                            </select>
                        </div>

                        <!-- TOMBOL -->
                        <button type="submit" class="btn btn-primary">
                            Update Kategori
                        </button>

                        <a href="kelola_pelanggaran_kategori.php"
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

<!-- Cek nama & deskripsi yang sama dengan kategori lain -->
<script>

// Data kategori lain (huruf kecil) dari database, kategori ini sendiri tidak ikut
const namaLain = <?= json_encode($nama_lain, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>;
const deskripsiLain = <?= json_encode($deskripsi_lain, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>;

const form = document.getElementById('formEditKategori');
const inputNama = document.getElementById('nama');
const inputDeskripsi = document.getElementById('deskripsi');
const notif = document.getElementById('notifSama');
const teksNotif = document.getElementById('teksNotif');

// Huruf kecil, hapus spasi di pinggir, dan rapikan spasi ganda
function rapikan(teks) {
    return teks.trim().replace(/\s+/g, ' ').toLowerCase();
}

document.getElementById('tutupNotif').addEventListener('click', function () {
    notif.classList.add('d-none');
});

form.addEventListener('submit', function (e) {
    const nama = rapikan(inputNama.value);
    const deskripsi = rapikan(inputDeskripsi.value);

    const namaSama = nama !== '' && namaLain.includes(nama);
    const deskripsiSama = deskripsi !== '' && deskripsiLain.includes(deskripsi);

    let pesan = '';
    let kolom = null;

    if (namaSama && deskripsiSama) {
        pesan = 'Nama kategori dan deskripsi sudah terdaftar.';
        kolom = inputNama;
    } else if (namaSama) {
        pesan = 'Nama kategori sudah terdaftar.';
        kolom = inputNama;
    } else if (deskripsiSama) {
        pesan = 'Deskripsi sudah terdaftar.';
        kolom = inputDeskripsi;
    }

    if (pesan !== '') {
        e.preventDefault();
        teksNotif.textContent = pesan;
        notif.classList.remove('d-none');
        window.scrollTo({ top: 0, behavior: 'smooth' });
        kolom.focus();
    } else {
        notif.classList.add('d-none');
    }
});

</script>

</body>
</html>