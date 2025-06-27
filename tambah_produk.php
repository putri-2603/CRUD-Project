<?php
// File: tambah_handphone.php
// Fungsi: Menangani penambahan data handphone baru ke database.

include 'koneksi.php';

// Mengaktifkan error reporting (hanya untuk pengembangan)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Sanitasi dan validasi input dari pengguna
    $merk       = htmlspecialchars(trim($_POST['merk']));
    $model      = htmlspecialchars(trim($_POST['model']));
    $harga      = filter_var($_POST['harga'], FILTER_VALIDATE_INT);
    $stok       = filter_var($_POST['stok'], FILTER_VALIDATE_INT);
    $kondisi    = htmlspecialchars(trim($_POST['kondisi']));
    $spesifikasi= htmlspecialchars(trim($_POST['spesifikasi']));
    $deskripsi  = htmlspecialchars(trim($_POST['deskripsi']));

    // Validasi dasar
    if (empty($merk) || empty($model) || $harga === false || $harga <= 0 || $stok === false || $stok < 0 || empty($kondisi) || empty($spesifikasi) || empty($deskripsi)) {
        echo "<script>alert('Semua field harus diisi dengan benar dan harga/stok harus valid!'); window.location.href='tambah_handphone.php';</script>";
        exit(); // Hentikan eksekusi script
    }

    $gambar = ''; // Variabel untuk menyimpan nama file gambar
    // 2. Penanganan upload gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $targetDir    = "../images/foto_hp/"; // Folder tujuan upload gambar HP
        $gambar       = basename($_FILES["gambar"]["name"]); // Nama file gambar
        $targetFilePath = $targetDir . $gambar;
        $fileType     = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION)); // Ekstensi file

        // Izinkan hanya format gambar tertentu
        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
        if (in_array($fileType, $allowTypes)) {
            // Pindahkan file yang diunggah ke folder tujuan
            if (!move_uploaded_file($_FILES["gambar"]["tmp_name"], $targetFilePath)) {
                echo "<script>alert('Gagal mengunggah gambar! Silakan coba lagi.'); window.location.href='tambah_handphone.php';</script>";
                exit();
            }
        } else {
            echo "<script>alert('Maaf, hanya file JPG, JPEG, PNG, & GIF yang diizinkan untuk gambar HP!'); window.location.href='tambah_handphone.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Gambar HP harus diunggah!'); window.location.href='tambah_handphone.php';</script>";
        exit();
    }

    // 3. Masukkan data ke database menggunakan Prepared Statement
    $stmt = mysqli_prepare($koneksi, "INSERT INTO handphone (merk, model, harga, stok, kondisi, spesifikasi, deskripsi, gambar) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssiissss", $merk, $model, $harga, $stok, $kondisi, $spesifikasi, $deskripsi, $gambar);

        if (mysqli_stmt_execute($stmt)) {
            // Log aktivitas (opsional)
            // $log_stmt = mysqli_prepare($koneksi, "INSERT INTO log_aktivitas (deskripsi_aktivitas) VALUES (?)");
            // $log_desc = "Admin menambahkan HP baru: " . $merk . " " . $model;
            // mysqli_stmt_bind_param($log_stmt, "s", $log_desc);
            // mysqli_stmt_execute($log_stmt);
            // mysqli_stmt_close($log_stmt);

            header("Location: handphone.php?status=success_add");
            exit(); // Penting: Hentikan eksekusi script
        } else {
            // Jika gagal eksekusi query
            echo "<script>alert('Gagal menambahkan handphone: " . mysqli_error($koneksi) . "');</script>";
        }
        mysqli_stmt_close($stmt); // Tutup statement
    } else {
        // Jika gagal menyiapkan statement
        echo "<script>alert('Terjadi kesalahan database saat menyiapkan query: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Handphone</title>
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
            max-width: 650px;
        }
        h3 {
            color: #0d47a1;
            margin-bottom: 25px;
            text-align: center;
        }
        .form-control:focus {
            border-color: #42a5f5;
            box-shadow: 0 0 0 0.25rem rgba(66, 165, 245, 0.25);
        }
        .btn-primary {
            background-color: #1e88e5;
            border-color: #1e88e5;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #1565c0;
            border-color: #1565c0;
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
        <h3>Tambah Handphone Baru</h3>
        <form method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="merk" class="form-label">Merk HP</label>
                    <input type="text" name="merk" id="merk" class="form-control" required placeholder="Contoh: Samsung, iPhone">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="model" class="form-label">Model HP</label>
                    <input type="text" name="model" id="model" class="form-control" required placeholder="Contoh: Galaxy S24, iPhone 15 Pro">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="harga" class="form-label">Harga</label>
                    <input type="number" name="harga" id="harga" class="form-control" required min="0" placeholder="Contoh: 15000000">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="stok" class="form-label">Stok</label>
                    <input type="number" name="stok" id="stok" class="form-control" required min="0" placeholder="Jumlah unit tersedia">
                </div>
            </div>
            <div class="mb-3">
                <label for="kondisi" class="form-label">Kondisi HP</label>
                <select name="kondisi" id="kondisi" class="form-control" required>
                    <option value="">Pilih Kondisi</option>
                    <option value="baru">Baru</option>
                    <option value="bekas">Bekas</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="spesifikasi" class="form-label">Spesifikasi (singkat)</label>
                <textarea name="spesifikasi" id="spesifikasi" class="form-control" rows="3" required placeholder="Contoh: RAM 8GB, Internal 128GB, Kamera 50MP, Chipset Exynos 2400"></textarea>
                <small class="form-text text-muted">Isi spesifikasi kunci HP.</small>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi Lengkap Produk</label>
                <textarea name="deskripsi" id="deskripsi" class="form-control" rows="5" required placeholder="Tulis deskripsi lengkap tentang handphone ini..."></textarea>
            </div>
            <div class="mb-3">
                <label for="gambar" class="form-label">Gambar Handphone</label>
                <input type="file" name="gambar" id="gambar" class="form-control" accept="image/*" required>
                <small class="form-text text-muted">Hanya file JPG, JPEG, PNG, GIF yang diizinkan.</small>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button class="btn btn-primary" type="submit">Simpan Handphone</button>
                <a href="handphone.php" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</body>
</html>