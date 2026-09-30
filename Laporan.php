<?php
include "cek_akses.php";
cek_role(['admin']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan</title>
</head>
<body>

    <h1>Laporan</h1>
    <p>Halaman Laporan (bisa diakses: admin)</p>
    <p><a href="dashboard.php">Kembali ke Dashboard</a></p>

</body>
</html>
