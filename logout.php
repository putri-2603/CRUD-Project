<?php
// File: logout.php
// Fungsi: Menghancurkan sesi dan mengarahkan pengguna ke halaman login.

session_start();      // Memulai sesi
session_unset();      // Menghapus semua variabel sesi
session_destroy();    // Menghancurkan sesi

// Mengarahkan pengguna kembali ke halaman login
// PASTIKAN PATH INI SESUAI DENGAN LOKASI FILE LOGIN.PHP ANDA
header("Location: login.php"); // Opsi 1: Path relatif, jika login.php dan logout.php di folder yang sama
// header("Location: /BACKENDKU/login.php"); // Opsi 2: Path absolut, GANTI /BACKENDKU/ dengan nama folder proyek Anda di localhost
exit(); // Penting: Pastikan tidak ada kode lain yang dieksekusi setelah redirect
?>
