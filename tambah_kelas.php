<?php
// tambah_kelas.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// =====================================================
// PILIHAN DROPDOWN
// =====================================================
$daftar_tingkat = ['X', 'XI', 'XII'];

// Singkatan => nama lengkap (yang disimpan ke database adalah singkatannya)
$daftar_jurusan = [
    'RPL' => 'Rekayasa Perangkat Lunak',
    'TKJ' => 'Teknik Komputer dan Jaringan',
    'TKR' => 'Teknik Kendaraan Ringan',
    'TSM' => 'Teknik Sepeda Motor',
    'BD'  => 'Bisnis Digital',
];

// =====================================================
// NAMA KELAS YANG SUDAH ADA (untuk cek kelas yang sama)
// =====================================================
$nama_kelas_ada = [];

$hasil_kelas = mysqli_query($koneksi, "SELECT nama FROM t_kelas");

while ($k = mysqli_fetch_assoc($hasil_kelas)) {
    $nama_kelas_ada[] = mb_strtolower(trim((string) $k['nama']));
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Kelas</title>

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

            <h1 class="mb-4">Tambah Kelas</h1>

            <?php if (isset($_SESSION['pesan_sukses'])) { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php
                    echo htmlspecialchars($_SESSION['pesan_sukses']);
                    unset($_SESSION['pesan_sukses']);
                    ?>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Tutup"></button>
                </div>
            <?php } ?>

            <?php if (isset($_SESSION['pesan_error'])) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php
                    echo htmlspecialchars($_SESSION['pesan_error']);
                    unset($_SESSION['pesan_error']);
                    ?>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Tutup"></button>
                </div>
            <?php } ?>

            <!-- Notifikasi kelas sudah terdaftar -->
            <div id="notifKelasSama"
                 class="alert alert-danger alert-dismissible d-none"
                 role="alert">
                Nama kelas sudah terdaftar.
                <button type="button"
                        class="btn-close"
                        id="tutupNotif"
                        aria-label="Tutup"></button>
            </div>

            <div class="card shadow-sm">

                <div class="card-header bg-primary text-white">
                    Form Tambah Kelas
                </div>

                <div class="card-body">

                    <form action="proses_tambah_kelas.php"
                          method="POST"
                          id="formTambahKelas">

                        <!-- Dikirim menggantikan dropdown saat dropdown dikunci oleh JavaScript -->
                        <input type="hidden" name="tingkat" id="tingkat_kunci" disabled>
                        <input type="hidden" name="jurusan" id="jurusan_kunci" disabled>

                        <!-- NAMA KELAS -->
                        <div class="mb-3">
                            <label for="nama" class="form-label">
                                Nama Kelas
                            </label>

                            <input type="text"
                                   name="nama"
                                   id="nama"
                                   class="form-control"
                                   placeholder="Contoh: X RPL 1"
                                   required>
                        </div>

                        <!-- DROPDOWN TINGKAT -->
                        <div class="mb-3">
                            <label for="tingkat" class="form-label">
                                Tingkat
                            </label>

                            <select name="tingkat"
                                    id="tingkat"
                                    class="form-select"
                                    required>

                                <option value="" selected disabled>
                                    -- Pilih Tingkat --
                                </option>

                                <?php foreach ($daftar_tingkat as $t) { ?>
                                    <option value="<?= htmlspecialchars($t) ?>">
                                        <?= htmlspecialchars($t) ?>
                                    </option>
                                <?php } ?>

                            </select>

                           
                        </div>

                        <!-- DROPDOWN JURUSAN -->
                        <div class="mb-3">
                            <label for="jurusan" class="form-label">
                                Jurusan
                            </label>

                            <select name="jurusan"
                                    id="jurusan"
                                    class="form-select"
                                    required>

                                <option value="" selected disabled>
                                    -- Pilih Jurusan --
                                </option>

                                <?php foreach ($daftar_jurusan as $singkatan => $nama_lengkap) { ?>
                                    <option value="<?= htmlspecialchars((string) $singkatan) ?>">
                                        <?= htmlspecialchars($nama_lengkap . ' (' . $singkatan . ')') ?>
                                    </option>
                                <?php } ?>

                            </select>

                            
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

                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>

                            </select>
                        </div>

                        <!-- TOMBOL -->
                        <button type="submit" class="btn btn-primary">
                            Simpan Kelas
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

<!-- Tingkat & jurusan otomatis, cek nama kelas yang sama -->
<script>

// Nama kelas yang sudah ada (huruf kecil) dari database
const namaKelasAda = <?= json_encode($nama_kelas_ada, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>;

const form = document.getElementById('formTambahKelas');
const inputNama = document.getElementById('nama');
const notif = document.getElementById('notifKelasSama');
const selectTingkat = document.getElementById('tingkat');
const selectJurusan = document.getElementById('jurusan');
const kunciTingkat = document.getElementById('tingkat_kunci');
const kunciJurusan = document.getElementById('jurusan_kunci');

function kelasSudahAda() {
    const nama = inputNama.value.trim().toLowerCase();

    return nama !== '' && namaKelasAda.includes(nama);
}

// Isi tingkat & jurusan otomatis dari nama kelas (contoh: "XII RPL 1" -> XII, RPL).
// Jika cocok, dropdown dikunci (tidak bisa diubah) dan nilainya dikirim lewat input tersembunyi.
// Jika nama kelas tidak mengandung tingkat/jurusan yang dikenal, dropdown bisa dipilih manual.
function terapkan(pilihan, kunci, kata) {
    let cocok = null;

    for (const opsi of pilihan.options) {
        if (opsi.value !== '' && kata.includes(opsi.value.toUpperCase())) {
            cocok = opsi.value;
            break;
        }
    }

    if (cocok) {
        pilihan.value = cocok;
        pilihan.disabled = true;
        kunci.value = cocok;
        kunci.disabled = false;
    } else {
        pilihan.disabled = false;
        kunci.disabled = true;
    }
}

function isiDariNama() {
    const kata = inputNama.value.toUpperCase().split(/[\s\-_]+/);

    terapkan(selectTingkat, kunciTingkat, kata);
    terapkan(selectJurusan, kunciJurusan, kata);
}

inputNama.addEventListener('input', isiDariNama);

document.getElementById('tutupNotif').addEventListener('click', function () {
    notif.classList.add('d-none');
});

// Pastikan tingkat & jurusan sesuai nama kelas, lalu cek kelas yang sama
form.addEventListener('submit', function (e) {
    isiDariNama();

    if (kelasSudahAda()) {
        e.preventDefault();
        notif.classList.remove('d-none');
        window.scrollTo({ top: 0, behavior: 'smooth' });
        inputNama.focus();
    } else {
        notif.classList.add('d-none');
    }
});

</script>

</body>
</html>