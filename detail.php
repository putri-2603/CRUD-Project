<?php
// File: detail_handphone.php
// Fungsi: Menampilkan detail lengkap dari sebuah handphone berdasarkan ID.

include 'koneksi.php';

// Mengaktifkan error reporting (hanya untuk pengembangan)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Validasi parameter ID dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: handphone.php?error=invalid_id");
    exit();
}

$id = (int)$_GET['id'];

// 2. Mengambil data handphone dari database menggunakan Prepared Statement
$stmt = mysqli_prepare($koneksi, "SELECT * FROM handphone WHERE id = ?");

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result || mysqli_num_rows($result) == 0) {
        header("Location: handphone.php?error=handphone_not_found");
        exit();
    }

    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
} else {
    error_log("Error preparing select statement for handphone detail: " . mysqli_error($koneksi));
    die("Terjadi kesalahan database. Silakan coba lagi nanti.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Handphone: <?= htmlspecialchars($data['merk'] . ' ' . $data['model']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e0f2f7;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            max-width: 750px;
        }
        h3 {
            color: #0d47a1;
            margin-bottom: 25px;
            text-align: center;
        }
        .card-body h5 {
            color: #0d47a1;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .card-text strong {
            color: #3f51b5;
        }
        .img-fluid.rounded-start {
            border-radius: 8px !important;
            object-fit: cover;
            width: 100%;
            height: 250px; /* Tinggi gambar agar konsisten */
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            transition: background-color 0.3s ease;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Detail Handphone</h3>

        <div class="card mb-4 shadow-sm">
            <div class="row g-0">
                <div class="col-md-5">
                    <?php
                    $imagePath = '../images/foto_hp/' . htmlspecialchars($data['gambar']);
                    if (!empty($data['gambar']) && file_exists($imagePath)) {
                        echo '<img src="' . $imagePath . '" class="img-fluid rounded-start" alt="Gambar Handphone">';
                    } else {
                        echo '<img src="../images/placeholder.png" class="img-fluid rounded-start" alt="Gambar Tidak Tersedia">';
                    }
                    ?>
                </div>
                <div class="col-md-7">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($data['merk'] . ' ' . $data['model']) ?></h5>
                        <p class="card-text"><strong>Harga:</strong> Rp <?= number_format($data['harga'], 0, ',', '.') ?></p>
                        <p class="card-text"><strong>Stok:</strong> <?= htmlspecialchars($data['stok']) ?> unit</p>
                        <p class="card-text"><strong>Kondisi:</strong> <span class="badge <?= ($data['kondisi'] == 'baru') ? 'bg-success' : 'bg-info' ?>"><?= htmlspecialchars(ucfirst($data['kondisi'])) ?></span></p>
                        <p class="card-text"><strong>Spesifikasi:</strong><br><?= nl2br(htmlspecialchars($data['spesifikasi'])) ?></p>
                        <p class="card-text"><strong>Deskripsi:</strong><br><?= nl2br(htmlspecialchars($data['deskripsi'])) ?></p>
                        <p class="card-text"><small class="text-muted">ID HP: <?= $data['id'] ?></small></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="handphone.php" class="btn btn-secondary mt-3">Kembali ke Daftar Handphone</a>
        </div>
    </div>
</body>
</html>