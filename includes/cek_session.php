<?php

session_start();

// CEK LOGIN
if (!isset($_SESSION['user_id'])) {

    header("Location: ../login.php");
    exit;

}


// FUNGSI CEK ROLE
function cek_role($role_diizinkan)
{

    if (!in_array($_SESSION['role'], $role_diizinkan)) {

        http_response_code(403);

        echo "<!DOCTYPE html>";
        echo "<html lang='id'>";
        echo "<head>";
        echo "<meta charset='UTF-8'>";
        echo "<title>Akses Ditolak</title>";
        echo "</head>";

        echo "<body>";

        echo "<h1>Akses ditolak</h1>";

        echo "<p>Role Anda (" .
             htmlspecialchars($_SESSION['role']) .
             ") tidak boleh membuka halaman ini.</p>";

        echo "<p>";
        echo "<a href='../dashboard.php'>";
        echo "Kembali ke Dashboard";
        echo "</a>";
        echo "</p>";

        echo "</body>";
        echo "</html>";

        exit;
    }
}

?>