<?php
include "cek_akses.php";
cek_role(['guru']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Catatan Pelanggaran</title>
</head>
<body>

    <h1>Catatan Pelanggaran</h1>
    <p>Halaman Catatan Pelanggaran (bisa diakses: guru)</p>
    <p><a href="dashboard.php">Kembali ke Dashboard</a></p>

</body>
</html>
