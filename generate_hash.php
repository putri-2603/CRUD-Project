<?php
// File: generate_hash.php
// Digunakan untuk menghasilkan hash password
$password_to_hash = "admin123"; // <-- Pastikan ini password yang SAMA persis dengan yang akan Anda ketik di form login
$hashed_password = password_hash($password_to_hash, PASSWORD_DEFAULT);
echo "Password plaintext: " . $password_to_hash . "<br>";
echo "Password Hashed: <strong>" . $hashed_password . "</strong>";
?>