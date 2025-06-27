<?php
// File: hapus_handphone.php
// Fungsi: Menangani proses penghapusan data handphone dari database dan file gambar terkait.

include 'koneksi.php';

// Mengaktifkan error reporting (hanya untuk pengembangan)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Validasi ID handphone dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: handphone.php?error=invalid_id");
    exit();
}

$id = (int)$_GET['id'];

$nama_gambar_lama = null;

// 2. Ambil nama file gambar sebelum menghapus data dari DB
$stmt_select_gambar = mysqli_prepare($koneksi, "SELECT gambar FROM handphone WHERE id = ?");
if ($stmt_select_gambar) {
    mysqli_stmt_bind_param($stmt_select_gambar, "i", $id);
    mysqli_stmt_execute($stmt_select_gambar);
    $result_gambar = mysqli_stmt_get_result($stmt_select_gambar);
    $row_gambar = mysqli_fetch_assoc($result_gambar);
    mysqli_stmt_close($stmt_select_gambar);

    if ($row_gambar) {
        $nama_gambar_lama = $row_gambar['gambar'];
    }
} else {
    error_log("Error preparing select statement for image deletion (ID: $id): " . mysqli_error($koneksi));
    header("Location: handphone.php?error=db_error");
    exit();
}

// 3. Hapus data dari database menggunakan Prepared Statement
$stmt_delete = mysqli_prepare($koneksi, "DELETE FROM handphone WHERE id = ?");

if ($stmt_delete) {
    mysqli_stmt_bind_param($stmt_delete, "i", $id);

    if (mysqli_stmt_execute($stmt_delete)) {
        // Jika penghapusan dari DB berhasil, coba hapus file gambar fisik
        if ($nama_gambar_lama && file_exists("../images/foto_hp/" . $nama_gambar_lama)) {
            unlink("../images/foto_hp/" . $nama_gambar_lama);
        }

        // Log aktivitas (opsional)
        // $log_stmt = mysqli_prepare($koneksi, "INSERT INTO log_aktivitas (deskripsi_aktivitas) VALUES (?)");
        // $log_desc = "Admin menghapus handphone (ID: " . $id . ")";
        // mysqli_stmt_bind_param($log_stmt, "s", $log_desc);
        // mysqli_stmt_execute($log_stmt);
        // mysqli_stmt_close($log_stmt);

        header("Location: handphone.php?status=success_delete");
        exit();
    } else {
        error_log("Gagal menghapus handphone dari database (ID: $id): " . mysqli_error($koneksi));
        header("Location: handphone.php?error=delete_failed");
        exit();
    }
    mysqli_stmt_close($stmt_delete);
} else {
    error_log("Gagal menyiapkan statement delete handphone: " . mysqli_error($koneksi));
    header("Location: handphone.php?error=db_error");
    exit();
}
?>