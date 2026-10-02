<?php

session_start();

include "config/koneksi.php";

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Hanya admin
if ($_SESSION['role'] != "admin") {
    echo "Akses ditolak!";
    exit;
}

// Ambil data siswa
$siswa = mysqli_query(
    $koneksi,
    "SELECT * FROM t_siswa
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// Ambil data kelas
$kelas = mysqli_query(
    $koneksi,
    "SELECT * FROM t_kelas
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// Ambil tahun ajaran
$tahun = mysqli_query(
    $koneksi,
    "SELECT * FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY id DESC"
);

// Jika tombol simpan ditekan
if (isset($_POST['simpan'])) {

    $siswa_id = $_POST['siswa_id'];
    $kelas_id = $_POST['kelas_id'];
    $tahun_ajaran_id = $_POST['tahun_ajaran_id'];
    $status_aktif = $_POST['status_aktif'];

    $query = mysqli_query($koneksi, "
        INSERT INTO t_kelas_siswa
        (
            siswa_id,
            kelas_id,
            tahun_ajaran_id,
            status_aktif
        )
        VALUES
        (
            '$siswa_id',
            '$kelas_id',
            '$tahun_ajaran_id',
            '$status_aktif'
        )
    ");

    if ($query) {

        header("Location: penempatan_siswa.php");
        exit;

    } else {

        echo "Data penempatan gagal disimpan.";

    }

}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Tambah Penempatan Siswa</title>
</head>

<body>

<h2>Tambah Penempatan Siswa</h2>

<form method="POST">

    Siswa:
    <br>

    <select name="siswa_id" required>

        <option value="">
            -- Pilih Siswa --
        </option>

        <?php

        while ($data_siswa = mysqli_fetch_assoc($siswa)) {

        ?>

        <option value="<?php echo $data_siswa['id']; ?>">

            <?php echo $data_siswa['nis']; ?>
            -
            <?php echo $data_siswa['nama']; ?>

        </option>

        <?php

        }

        ?>

    </select>

    <br><br>

    Kelas:
    <br>

    <select name="kelas_id" required>

        <option value="">
            -- Pilih Kelas --
        </option>

        <?php

        while ($data_kelas = mysqli_fetch_assoc($kelas)) {

        ?>

        <option value="<?php echo $data_kelas['id']; ?>">

            <?php echo $data_kelas['nama']; ?>
            -
            <?php echo $data_kelas['tingkat']; ?>
            <?php echo $data_kelas['jurusan']; ?>

        </option>

        <?php

        }

        ?>

    </select>

    <br><br>

    Tahun Ajaran:
    <br>

    <select name="tahun_ajaran_id" required>

        <option value="">
            -- Pilih Tahun Ajaran --
        </option>

        <?php

        while ($data_tahun = mysqli_fetch_assoc($tahun)) {

        ?>

        <option value="<?php echo $data_tahun['id']; ?>">

            <?php echo $data_tahun['nama']; ?>

        </option>

        <?php

        }

        ?>

    </select>

    <br><br>

    Status:
    <br>

    <select name="status_aktif">

        <option value="1">
            Aktif
        </option>

        <option value="0">
            Tidak Aktif
        </option>

    </select>

    <br><br>

    <button type="submit" name="simpan">
        Simpan
    </button>

    <br><br>

    <a href="penempatan_siswa.php">
        Kembali
    </a>

</form>

</body>

</html>