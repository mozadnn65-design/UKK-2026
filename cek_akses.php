<?php
session_start();

// Belum login -> kembali ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Panggil fungsi ini di setiap halaman menu, isi dengan role yang boleh masuk
function cek_role($role_diizinkan) {
    if (!in_array($_SESSION['role'], $role_diizinkan)) {
        http_response_code(403);
        echo "<!DOCTYPE html><html lang='id'><head><meta charset='UTF-8'><title>Akses Ditolak</title></head><body>";
        echo "<h1>Akses ditolak</h1>";
        echo "<p>Role Anda (" . htmlspecialchars($_SESSION['role']) . ") tidak boleh membuka halaman ini.</p>";
        echo "<p><a href='dashboard.php'>Kembali ke Dashboard</a></p>";
        echo "</body></html>";
        exit;
    }
}
