<?php
session_start();

// Sudah login -> ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Belum login -> ke halaman login
header('Location: login.php');
exit;