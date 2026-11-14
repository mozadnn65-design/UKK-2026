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

mysqli_stmt_close($stmt);

if (!$data) {
    die("Data kelas tidak ditemukan.");
}

// =====================================================
// PILIHAN DROPDOWN
// =====================================================
$daftar_tingkat = ['X', 'XI', 'XII'];
// Singkatan => nama lengkap (yang disimpan ke database adalah singkatannya)
$daftar_jurusan = [
    'RPL'  => 'Rekayasa Perangkat Lunak',
    'TKJ'  => 'Teknik Komputer dan Jaringan',
    'TKR'  => 'Teknik Kendaraan Ringan',
    'TBSM' => 'Teknik dan Bisnis Sepeda Motor',
    'BD'   => 'Bisnis Digital',
];

$tingkat_lama = (string) ($data['tingkat'] ?? '');
$jurusan_lama = (string) ($data['jurusan'] ?? '');

// Jika nilai lama tidak ada di daftar, tetap tampilkan agar tidak hilang
if ($tingkat_lama !== '' && !in_array($tingkat_lama, $daftar_tingkat, true)) {
    $daftar_tingkat[] = $tingkat_lama;
}

if ($jurusan_lama !== '' && !array_key_exists($jurusan_lama, $daftar_jurusan)) {
    $daftar_jurusan[$jurusan_lama] = $jurusan_lama;
}

// =====================================================
// NAMA KELAS LAIN (untuk cek kelas yang sama)
// =====================================================
$nama_kelas_lain = [];

$stmt_lain = mysqli_prepare(
    $koneksi,
    "SELECT nama FROM t_kelas WHERE id <> ?"
);

mysqli_stmt_bind_param($stmt_lain, "i", $id);
mysqli_stmt_execute($stmt_lain);

$hasil_lain = mysqli_stmt_get_result($stmt_lain);

while ($k = mysqli_fetch_assoc($hasil_lain)) {
    $nama_kelas_lain[] = mb_strtolower(trim((string) $k['nama']));
}

mysqli_stmt_close($stmt_lain);
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
                    Form Edit Kelas
                </div>

                <div class="card-body">

                    <form action="proses_edit_kelas.php"
                          method="POST"
                          id="formEditKelas">

                        <input type="hidden"
                               name="id"
                               value="<?= htmlspecialchars((string) $data['id']) ?>">

                        <!-- Dikirim menggantikan dropdown saat dropdown dikunci oleh JavaScript -->
                        <input type="hidden" name="tingkat" id="tingkat_kunci" disabled>
                        <input type="hidden" name="jurusan" id="jurusan_kunci" disabled>

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

                            <select name="tingkat"
                                    id="tingkat"
                                    class="form-select"
                                    required>

                                <option value="">-- Pilih Tingkat --</option>

                                <?php foreach ($daftar_tingkat as $t) { ?>
                                    <option value="<?= htmlspecialchars($t) ?>"
                                        <?= $t === $tingkat_lama ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($t) ?>
                                    </option>
                                <?php } ?>

                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="jurusan" class="form-label">
                                Jurusan
                            </label>

                            <select name="jurusan"
                                    id="jurusan"
                                    class="form-select"
                                    required>

                                <option value="">-- Pilih Jurusan --</option>

                                <?php foreach ($daftar_jurusan as $singkatan => $nama_lengkap) { ?>
                                    <option value="<?= htmlspecialchars((string) $singkatan) ?>"
                                        <?= (string) $singkatan === $jurusan_lama ? 'selected' : '' ?>>
                                        <?= htmlspecialchars(
                                            $nama_lengkap === (string) $singkatan
                                                ? $nama_lengkap
                                                : $nama_lengkap . ' (' . $singkatan . ')'
                                        ) ?>
                                    </option>
                                <?php } ?>

                            </select>

                            <div class="form-text">
                                Otomatis mengikuti nama kelas (contoh: XII RPL 1 menjadi RPL) dan tidak bisa diubah.
                            </div>
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
                                    <?= (int) $data['status_aktif'] === 1 ? 'selected' : '' ?>>
                                    Aktif
                                </option>

                                <option value="0"
                                    <?= (int) $data['status_aktif'] === 0 ? 'selected' : '' ?>>
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

<!-- Cek nama kelas yang sama -->
<script>

// Nama kelas lain (huruf kecil) dari database
const namaKelasLain = <?= json_encode($nama_kelas_lain, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>;

const form = document.getElementById('formEditKelas');
const inputNama = document.getElementById('nama');
const notif = document.getElementById('notifKelasSama');

function kelasSudahAda() {
    const nama = inputNama.value.trim().toLowerCase();

    return nama !== '' && namaKelasLain.includes(nama);
}

const selectTingkat = document.getElementById('tingkat');
const selectJurusan = document.getElementById('jurusan');

const kunciTingkat = document.getElementById('tingkat_kunci');
const kunciJurusan = document.getElementById('jurusan_kunci');

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

// Terapkan juga saat halaman pertama kali dibuka
isiDariNama();

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