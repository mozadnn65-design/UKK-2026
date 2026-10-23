```php
<?php
// proses_edit_pelanggaran_kategori.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = (int) ($_POST['id'] ?? 0);
$nama = trim($_POST['nama'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');
$status_aktif = $_POST['status_aktif'] ?? '1';

if ($id <= 0 || $nama == '') {
    $_SESSION['pesan_error'] = "Data tidak valid!";
    header("Location: kelola_pelanggaran_kategori.php");
    exit;
}

$sql = "UPDATE t_pelanggaran_kategori
        SET nama = ?,
            deskripsi = ?,
            status_aktif = ?,
            updated_at = NOW()
        WHERE id = ?";

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    $_SESSION['pesan_error'] = "Query gagal: " . mysqli_error($koneksi);
    header("Location: edit_pelanggaran_kategori.php?id=" . $id);
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    "ssii",
    $nama,
    $deskripsi,
    $status_aktif,
    $id
);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['pesan_sukses'] = "Kategori pelanggaran berhasil diperbarui!";
    mysqli_stmt_close($stmt);
    header("Location: kelola_pelanggaran_kategori.php");
    exit;
} else {
    $_SESSION['pesan_error'] = "Gagal mengedit kategori: " . mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);
    header("Location: edit_pelanggaran_kategori.php?id=" . $id);
    exit;
}
?>
```