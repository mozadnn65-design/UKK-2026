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

// Ambil tahun ajaran aktif
$tahun = mysqli_query(
    $koneksi,
    "SELECT * FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY id DESC"
);

// Ambil kelas aktif
$kelas = mysqli_query(
    $koneksi,
    "SELECT * FROM t_kelas
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// Ambil guru aktif
$guru = mysqli_query(
    $koneksi,
    "SELECT * FROM t_guru
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// Jika tombol simpan ditekan
if (isset($_POST['simpan'])) {

    $tahun_ajaran_id = $_POST['tahun_ajaran_id'];
    $kelas_id = $_POST['kelas_id'];
    $guru_id = $_POST['guru_id'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $status_aktif = $_POST['status_aktif'];

    $query = mysqli_query($koneksi, "
        INSERT INTO t_wali_kelas
        (
            tahun_ajaran_id,
            kelas_id,
            guru_id,
            tanggal_mulai,
            tanggal_selesai,
            status_aktif
        )
        VALUES
        (
            '$tahun_ajaran_id',
            '$kelas_id',
            '$guru_id',
            '$tanggal_mulai',
            '$tanggal_selesai',
            '$status_aktif'
        )
    ");

    if ($query) {

        header("Location: kelola_wali_kelas.php");
        exit;

    } else {

        echo "Data wali kelas gagal ditambahkan.";

    }

}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Tambah Wali Kelas</title>
</head>

<body>

<h2>Tambah Wali Kelas</h2>

<form method="POST">

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

    Guru / Wali Kelas:
    <br>

    <select name="guru_id" required>

        <option value="">
            -- Pilih Guru --
        </option>

        <?php

        while ($data_guru = mysqli_fetch_assoc($guru)) {

        ?>

        <option value="<?php echo $data_guru['id']; ?>">

            <?php echo $data_guru['nip']; ?>
            -
            <?php echo $data_guru['nama']; ?>

        </option>

        <?php

        }

        ?>

    </select>

    <br><br>

    Tanggal Mulai:
    <br>

    <input
        type="date"
        name="tanggal_mulai"
        required
    >

    <br><br>

    Tanggal Selesai:
    <br>

    <input
        type="date"
        name="tanggal_selesai"
        required
    >

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

    <a href="kelola_wali_kelas.php">
        Kembali
    </a>

</form>

</body>

</html>