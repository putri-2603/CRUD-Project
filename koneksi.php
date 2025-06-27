<?php
// File: koneksi.php
// Fungsi: Mengelola koneksi ke database MySQL

$host     = "localhost";
$username = "root";
$password = "";
$database = "jual_hp_db"; // NAMA DATABASE UNTUK PENJUALAN HP

// Membuat koneksi ke database
$koneksi = mysqli_connect($host, $username, $password, $database);

// Memeriksa koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Mengatur karakter set koneksi ke UTF-8 untuk mendukung berbagai karakter
mysqli_set_charset($koneksi, "utf8mb4"); // Menggunakan utf8mb4 untuk dukungan emoji
?>