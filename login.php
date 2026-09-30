<?php

session_start();

// Jika sudah login, langsung ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login Pelanggaran Siswa</title>
</head>

<body>

    <h1>Login Aplikasi Pelanggaran Siswa</h1>

    <?php

    if (isset($_SESSION['pesan_error'])) {

        echo "<p>" . htmlspecialchars($_SESSION['pesan_error']) . "</p>";

        unset($_SESSION['pesan_error']);
    }

    ?>

    <form action="proses_login.php" method="POST" autocomplete="off">

        <table>

            <tr>
                <td>Email</td>
                <td>:</td>
                <td>
                    <input
                        type="email"
                        name="email"
                        value=""
                        autocomplete="off"
                        required>
                </td>
            </tr>

            <tr>
                <td>Password</td>
                <td>:</td>
                <td>
                    <input
                        type="password"
                        name="password"
                        value=""
                        autocomplete="new-password"
                        required>
                </td>
            </tr>

            <tr>
                <td colspan="3">
                    <input type="submit" value="Login">
                </td>
            </tr>

        </table>

    </form>

</body>

</html>