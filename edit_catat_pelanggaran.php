<?php
// edit_catat_pelanggaran.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$role = $_SESSION['role'] ?? '';
$id   = (int) ($_GET['id'] ?? 0);

// =====================================================
// MENGAMBIL DATA CATATAN PELANGGARAN
// =====================================================
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM t_pelanggaran_siswa WHERE id = ? LIMIT 1"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

mysqli_stmt_close($stmt);

if (!$row) {
    $_SESSION['pesan_error'] = "Data catatan pelanggaran tidak ditemukan.";
    header("Location: catat_pelanggaran.php");
    exit;
}

// Catatan yang sudah selesai tidak dapat diedit (sama seperti tombol Edit di daftar)
if (strtolower(trim((string) ($row['status'] ?? ''))) === 'selesai') {
    $_SESSION['pesan_error'] = "Catatan pelanggaran yang sudah selesai tidak dapat diedit.";
    header("Location: catat_pelanggaran.php");
    exit;
}

$siswa_id_lama       = (int) $row['siswa_id'];
$pelanggaran_id_lama = (int) $row['pelanggaran_id'];
$guru_id_lama        = (int) $row['guru_id'];
$kelas_id_lama       = (int) ($row['kelas_id'] ?? 0);

// =====================================================
// DAFTAR SISWA BESERTA KELASNYA
// =====================================================
$stmt_siswa = mysqli_prepare(
    $koneksi,
    "SELECT
        s.id,
        s.nis,
        s.nama,
        ks.kelas_id,
        k.nama AS nama_kelas
     FROM t_siswa s
     LEFT JOIN t_kelas_siswa ks
        ON ks.siswa_id = s.id
       AND ks.status_aktif = 1
     LEFT JOIN t_kelas k
        ON k.id = ks.kelas_id
     WHERE (s.status_aktif = 1 AND ks.kelas_id IS NOT NULL)
        OR s.id = ?
     ORDER BY s.nama ASC"
);

mysqli_stmt_bind_param($stmt_siswa, "i", $siswa_id_lama);
mysqli_stmt_execute($stmt_siswa);

$siswa = mysqli_stmt_get_result($stmt_siswa);

// =====================================================
// DAFTAR PELANGGARAN
// =====================================================
$stmt_pel = mysqli_prepare(
    $koneksi,
    "SELECT id, nama, poin, pelanggaran_kategori_id
     FROM t_pelanggaran
     WHERE status_aktif = 1
        OR id = ?
     ORDER BY nama ASC"
);

mysqli_stmt_bind_param($stmt_pel, "i", $pelanggaran_id_lama);
mysqli_stmt_execute($stmt_pel);

$pelanggaran_hasil = mysqli_stmt_get_result($stmt_pel);

$daftar_pelanggaran = [];

// Nilai awal poin dan kategori (dari pelanggaran yang sedang dipilih)
$poin_awal     = (string) ($row['poin'] ?? '');
$kategori_awal = (string) ($row['pelanggaran_kategori_id'] ?? '');

while ($p = mysqli_fetch_assoc($pelanggaran_hasil)) {
    $daftar_pelanggaran[] = $p;

    if ((int) $p['id'] === $pelanggaran_id_lama) {
        $poin_awal     = (string) $p['poin'];
        $kategori_awal = (string) $p['pelanggaran_kategori_id'];
    }
}

mysqli_stmt_close($stmt_pel);

// Guru pelapor dan tahun ajaran tidak ditampilkan (otomatis, tetap seperti catatan awal)
$tahun_ajaran_id = (int) ($row['tahun_ajaran_id'] ?? 0);

// =====================================================
// DAFTAR STATUS
// =====================================================
$daftar_status = ['Ditindaklanjuti', 'Proses', 'Selesai'];

$status_lama = (string) ($row['status'] ?? '');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Edit Catatan Pelanggaran</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Tom Select (dropdown yang bisa dicari) -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css"
          rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 p-4">

            <div class="mb-4">
                <h2 class="mb-1">Edit Catatan Pelanggaran</h2>
            </div>

            <?php if (isset($_SESSION['pesan_sukses'])) { ?>
                <div class="alert alert-success">
                    <?php
                    echo htmlspecialchars($_SESSION['pesan_sukses']);
                    unset($_SESSION['pesan_sukses']);
                    ?>
                </div>
            <?php } ?>

            <?php if (isset($_SESSION['pesan_error'])) { ?>
                <div class="alert alert-danger">
                    <?php
                    echo htmlspecialchars($_SESSION['pesan_error']);
                    unset($_SESSION['pesan_error']);
                    ?>
                </div>
            <?php } ?>

            <div class="card shadow-sm mb-4">

                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Form Edit Pelanggaran</h5>
                </div>

                <form action="proses_edit_catat_pelanggaran.php"
                      method="POST">

                    <div class="card-body p-4">

                        <input type="hidden"
                               name="id"
                               value="<?= (int) $row['id']; ?>">

                        <div class="row g-3">

                            <!-- PILIH SISWA (bisa dicari) -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    Siswa <span class="text-danger">*</span>
                                </label>

                                <select name="siswa_id"
                                        id="siswa"
                                        class="form-select"
                                        required>

                                    <option value="">-- Cari / Pilih Siswa --</option>

                                    <?php while ($s = mysqli_fetch_assoc($siswa)) { ?>

                                        <?php
                                        $kelas_option = $s['kelas_id'] ?? '';

                                        if ($kelas_option === '' && (int) $s['id'] === $siswa_id_lama) {
                                            $kelas_option = $kelas_id_lama;
                                        }
                                        ?>

                                        <option value="<?= (int) $s['id']; ?>"
                                                data-kelas-id="<?= htmlspecialchars((string) $kelas_option); ?>"
                                                <?= (int) $s['id'] === $siswa_id_lama ? 'selected' : ''; ?>>

                                            <?= htmlspecialchars(
                                                $s['nis'] . ' - ' . $s['nama'] .
                                                ' (' . ($s['nama_kelas'] ?? '-') . ')'
                                            ); ?>

                                        </option>

                                    <?php } ?>

                                </select>

                                <div class="form-text">
                                    Ketik nama atau NIS siswa. Kelas terisi otomatis.
                                </div>
                            </div>

                            <!-- JENIS PELANGGARAN (bisa dicari) -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    Pelanggaran <span class="text-danger">*</span>
                                </label>

                                <select name="pelanggaran_id"
                                        id="pelanggaran"
                                        class="form-select"
                                        required>

                                    <option value="">-- Cari / Pilih Pelanggaran --</option>

                                    <?php foreach ($daftar_pelanggaran as $p) { ?>

                                        <option value="<?= (int) $p['id']; ?>"
                                                data-poin="<?= htmlspecialchars((string) $p['poin']); ?>"
                                                data-kategori="<?= htmlspecialchars((string) $p['pelanggaran_kategori_id']); ?>"
                                                <?= (int) $p['id'] === $pelanggaran_id_lama ? 'selected' : ''; ?>>

                                            <?= htmlspecialchars(
                                                $p['nama'] . ' (' . $p['poin'] . ' poin)'
                                            ); ?>

                                        </option>

                                    <?php } ?>

                                </select>

                                <div class="form-text">
                                    Ketik untuk mencari. Poin sudah tercantum di setiap pilihan.
                                </div>
                            </div>

                            <!-- TANGGAL -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    Tanggal <span class="text-danger">*</span>
                                </label>

                                <input type="date"
                                       name="tanggal"
                                       class="form-control"
                                       value="<?= !empty($row['tanggal']) ? htmlspecialchars(date('Y-m-d', strtotime($row['tanggal']))) : ''; ?>"
                                       required>
                            </div>

                            <!-- STATUS -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    Status <span class="text-danger">*</span>
                                </label>

                                <select name="status"
                                        class="form-select"
                                        required>

                                    <?php foreach ($daftar_status as $st) { ?>
                                        <option value="<?= htmlspecialchars($st); ?>"
                                            <?= $st === $status_lama ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($st); ?>
                                        </option>
                                    <?php } ?>

                                </select>
                            </div>

                            <!-- KETERANGAN -->
                            <div class="col-12">
                                <label class="form-label">Keterangan</label>

                                <textarea name="keterangan"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Tuliskan kronologi singkat pelanggaran (opsional)"><?= htmlspecialchars((string) ($row['keterangan'] ?? '')); ?></textarea>
                            </div>

                        </div>

                        <!-- Diisi otomatis (tidak ditampilkan) -->
                        <input type="hidden"
                               name="guru_id"
                               value="<?= $guru_id_lama; ?>">

                        <input type="hidden"
                               name="kelas_id"
                               id="kelas"
                               value="<?= $kelas_id_lama; ?>">

                        <input type="hidden"
                               name="tahun_ajaran_id"
                               value="<?= $tahun_ajaran_id; ?>">

                        <!-- Diisi otomatis (tidak perlu diisi manual) -->
                        <input type="hidden"
                               name="poin"
                               id="poin"
                               value="<?= htmlspecialchars($poin_awal); ?>">

                        <input type="hidden"
                               name="pelanggaran_kategori_id"
                               id="kategori"
                               value="<?= htmlspecialchars($kategori_awal); ?>">

                        <input type="hidden"
                               name="tindakan"
                               value="<?= htmlspecialchars((string) ($row['tindakan'] ?? '')); ?>">

                    </div>

                    <div class="card-footer bg-white d-flex justify-content-end gap-2 py-3">

                        <a href="catat_pelanggaran.php" class="btn btn-secondary">
                            Kembali
                        </a>

                        <button type="submit" class="btn btn-primary px-4">
                            Simpan Perubahan
                        </button>

                    </div>

                </form>

            </div>

        </main>

    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- Tom Select JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<!-- JavaScript Siswa (bisa dicari, kelas otomatis) -->
<script>

const inputKelasId = document.getElementById('kelas');

// Simpan kelas tiap siswa (dikirim otomatis lewat input tersembunyi)
const dataSiswa = {};

document.querySelectorAll('#siswa option').forEach(function (opsi) {
    if (opsi.value !== '') {
        dataSiswa[opsi.value] = {
            kelasId: opsi.dataset.kelasId || ''
        };
    }
});

new TomSelect('#siswa', {
    maxOptions: null,
    placeholder: 'Ketik nama atau NIS siswa...',
    onChange: function (nilai) {
        const data = dataSiswa[nilai];

        inputKelasId.value = data ? data.kelasId : '';
    }
});

</script>

<!-- JavaScript Jenis Pelanggaran (bisa dicari, poin & kategori otomatis) -->
<script>

const inputPoin = document.getElementById('poin');
const inputKategori = document.getElementById('kategori');

// Simpan poin & kategori tiap pelanggaran sebelum dropdown diubah jadi pencarian
const dataPelanggaran = {};

document.querySelectorAll('#pelanggaran option').forEach(function (opsi) {
    if (opsi.value !== '') {
        dataPelanggaran[opsi.value] = {
            poin: opsi.dataset.poin || '',
            kategori: opsi.dataset.kategori || ''
        };
    }
});

new TomSelect('#pelanggaran', {
    maxOptions: null,
    placeholder: 'Ketik untuk mencari pelanggaran...',
    onChange: function (nilai) {
        const data = dataPelanggaran[nilai];

        inputPoin.value = data ? data.poin : '';
        inputKategori.value = data ? data.kategori : '';
    }
});

</script>

</body>
</html>