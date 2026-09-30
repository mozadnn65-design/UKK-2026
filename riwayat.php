<?php
include "cek_akses.php";
cek_role(['guru']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat</title>
</head>
<body>

    <h1>Riwayat</h1>
    <p>Halaman Riwayat (bisa diakses: guru)</p>
    <p><a href="dashboard.php">Kembali ke Dashboard</a></p>

</body>
</html>
