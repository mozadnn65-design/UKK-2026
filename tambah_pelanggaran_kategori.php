<?php
// tambah_pelanggaran_kategori.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// =====================================================
// KATEGORI YANG SUDAH ADA (untuk cek nama & deskripsi yang sama)
// =====================================================
$nama_ada = [];
$deskripsi_ada = [];

$hasil_kategori = mysqli_query($koneksi, "SELECT nama, deskripsi FROM t_pelanggaran_kategori");

if ($hasil_kategori) {
    while ($k = mysqli_fetch_assoc($hasil_kategori)) {
        $nama = mb_strtolower(trim(preg_replace('/\s+/', ' ', (string) $k['nama'])));
        $deskripsi = mb_strtolower(trim(preg_replace('/\s+/', ' ', (string) ($k['deskripsi'] ?? ''))));

        if ($nama !== '') {
            $nama_ada[] = $nama;
        }

        if ($deskripsi !== '') {
            $deskripsi_ada[] = $deskripsi;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Kategori Pelanggaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body>
<div class="container-fluid">
    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 p-4">

            <h1 class="mb-4">Tambah Kategori Pelanggaran</h1>

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
                    Form Tambah Kategori Pelanggaran
                </div>

                <div class="card-body">

                    <form action="proses_tambah_pelanggaran_kategori.php"
                          method="POST"
                          id="formTambahKategori">

                        <div class="mb-3">
                            <label for="nama" class="form-label">
                                Nama Kategori
                            </label>

                            <input type="text"
                                   name="nama"
                                   id="nama"
                                   class="form-control"
                                   placeholder="Contoh: Pelanggaran Ringan"
                                   maxlength="100"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">
                                Deskripsi
                            </label>

                            <textarea name="deskripsi"
                                      id="deskripsi"
                                      class="form-control"
                                      rows="4"
                                      placeholder="Masukkan deskripsi kategori pelanggaran"></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="status_aktif" class="form-label">
                                Status Aktif
                            </label>

                            <select name="status_aktif"
                                    id="status_aktif"
                                    class="form-select"
                                    required>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Simpan Kategori
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

<!-- Cek nama & deskripsi yang sama -->
<script>

// Data kategori yang sudah ada (huruf kecil) dari database
const namaAda = <?= json_encode($nama_ada, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>;
const deskripsiAda = <?= json_encode($deskripsi_ada, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>;

const form = document.getElementById('formTambahKategori');
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

    let pesan = '';
    let kolom = null;

    if (nama !== '' && namaAda.includes(nama)) {
        pesan = 'Nama kategori sudah terdaftar.';
        kolom = inputNama;
    } else if (deskripsi !== '' && deskripsiAda.includes(deskripsi)) {
        pesan = 'Deskripsi sudah digunakan oleh kategori lain.';
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