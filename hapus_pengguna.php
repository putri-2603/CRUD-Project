<?php
// File: hapus_pengguna.php
// Fungsi: Menangani proses penghapusan data pengguna dari database.

include 'koneksi.php';

// Mengaktifkan error reporting (hanya untuk pengembangan)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Validasi ID pengguna dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: pengguna.php?error=invalid_id");
    exit();
}

$id = (int)$_GET['id'];

// 2. Hapus data dari database menggunakan Prepared Statement
$stmt = mysqli_prepare($koneksi, "DELETE FROM users WHERE id = ?");

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        // Log aktivitas (opsional)
        // $log_stmt = mysqli_prepare($koneksi, "INSERT INTO log_aktivitas (deskripsi_aktivitas) VALUES (?)");
        // $log_desc = "Admin menghapus pengguna (ID: " . $id . ")";
        // mysqli_stmt_bind_param($log_stmt, "s", $log_desc);
        // mysqli_stmt_execute($log_stmt);
        // mysqli_stmt_close($log_stmt);

        header("Location: pengguna.php?status=success_delete");
        exit();
    } else {
        error_log("Gagal menghapus pengguna (ID: $id): " . mysqli_error($koneksi));
        header("Location: pengguna.php?error=delete_failed");
        exit();
    }
    mysqli_stmt_close($stmt);
} else {
    error_log("Gagal menyiapkan statement delete pengguna: " . mysqli_error($koneksi));
    header("Location: pengguna.php?error=db_error");
    exit();
}
?>