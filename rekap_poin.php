<?php
include "cek_akses.php";
cek_role(['admin', 'guru']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Poin</title>
</head>
<body>

    <h1>Rekap Poin</h1>
    <p>Halaman Rekap Poin (bisa diakses: admin dan guru)</p>
    <p><a href="dashboard.php">Kembali ke Dashboard</a></p>

</body>
</html>
