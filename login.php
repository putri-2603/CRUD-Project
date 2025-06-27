<?php
// File: login.php
// Fungsi: Menangani proses login pengguna.

session_start(); // Memulai sesi PHP

include 'koneksi.php'; // Memasukkan file koneksi database

// Mengaktifkan error reporting (hanya untuk pengembangan)
error_reporting(E_ALL);
ini_set('display_errors', 1);

$login_error = ''; // Variabel untuk menyimpan pesan error login

// Cek apakah pengguna sudah login, jika ya, arahkan ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Proses form ketika disubmit (metode POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Sanitasi input dari pengguna
    $email    = htmlspecialchars(trim($_POST['email']));
    $password = trim($_POST['password']); // Password tidak di-hash di sini karena akan diverifikasi

    // 2. Validasi input dasar
    if (empty($email) || empty($password)) {
        $login_error = "Email dan password harus diisi.";
    } else {
        // 3. Cari pengguna di database berdasarkan email menggunakan Prepared Statement
        $stmt = mysqli_prepare($koneksi, "SELECT id, nama, email, password, role FROM users WHERE email = ?");

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $email); // "s" untuk string (email)
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            // 4. Verifikasi password
            // HATI-HATI: Jika password di database Anda masih plaintext, GANTI ke password_verify()
            // Jika Anda menggunakan MD5 (seperti contoh SQL sebelumnya), pakai: md5($password) == $user['password']
            // SANGAT DISARANKAN: Gunakan password_hash() untuk menyimpan dan password_verify() untuk memverifikasi.
            if ($user && password_verify($password, $user['password'])) {
            // if ($user && md5($password) == $user['password']) { // Contoh jika pakai MD5, TIDAK DISARANKAN UNTUK PRODUKSI

                // Login berhasil
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nama'] = $user['nama'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                // Tambahkan log aktivitas (opsional)
                // $log_stmt = mysqli_prepare($koneksi, "INSERT INTO log_aktivitas (deskripsi_aktivitas) VALUES (?)");
                // $log_desc = $user['nama'] . " (" . $user['role'] . ") berhasil login.";
                // mysqli_stmt_bind_param($log_stmt, "s", $log_desc);
                // mysqli_stmt_execute($log_stmt);
                // mysqli_stmt_close($log_stmt);

                header("Location: dashboard.php"); // Arahkan ke halaman dashboard
                exit();
            } else {
                $login_error = "Email atau password salah.";
            }
        } else {
            error_log("Gagal menyiapkan statement login: " . mysqli_error($koneksi));
            $login_error = "Terjadi kesalahan sistem. Silakan coba lagi nanti.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Jual HP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e0f2f7; /* Latar belakang biru muda */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            max-width: 450px;
            width: 100%;
        }
        .login-container h2 {
            color: #0d47a1; /* Biru tua */
            margin-bottom: 30px;
            text-align: center;
            font-weight: bold;
        }
        .form-control:focus {
            border-color: #42a5f5; /* Biru terang saat fokus */
            box-shadow: 0 0 0 0.25rem rgba(66, 165, 245, 0.25);
        }
        .btn-primary {
            background-color: #1e88e5; /* Biru terang */
            border-color: #1e88e5;
            transition: background-color 0.3s ease;
            width: 100%;
            padding: 10px;
            font-size: 1.1em;
            margin-top: 20px;
        }
        .btn-primary:hover {
            background-color: #1565c0; /* Biru sedang saat hover */
            border-color: #1565c0;
        }
        .alert {
            margin-top: 20px;
            text-align: center;
        }
        .logo-text {
            font-size: 2.5em;
            font-weight: 800;
            color: #0d47a1;
            text-align: center;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }
        .logo-subtitle {
            font-size: 0.9em;
            color: #616161;
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-text">JUAL HP</div>
        <div class="logo-subtitle">Admin Panel Login</div>

        <?php if (!empty($login_error)): ?>
            <div class="alert alert-danger" role="alert">
                <?= $login_error ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</body>
</html>