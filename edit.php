<?php
// File: edit_handphone.php
// Fungsi: Menangani proses edit data handphone yang sudah ada.

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

// 2. Ambil data handphone yang akan diedit menggunakan Prepared Statement
$stmt_select = mysqli_prepare($koneksi, "SELECT * FROM handphone WHERE id = ?");
if ($stmt_select) {
    mysqli_stmt_bind_param($stmt_select, "i", $id);
    mysqli_stmt_execute($stmt_select);
    $result_select = mysqli_stmt_get_result($stmt_select);
    $data = mysqli_fetch_assoc($result_select);
    mysqli_stmt_close($stmt_select);

    if (!$data) {
        header("Location: handphone.php?error=handphone_not_found");
        exit();
    }
} else {
    error_log("Error preparing select statement for edit handphone: " . mysqli_error($koneksi));
    die("Terjadi kesalahan database saat mengambil data handphone.");
}

// 3. Proses form ketika disubmit (metode POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitasi dan validasi input dari pengguna
    $merk        = htmlspecialchars(trim($_POST['merk']));
    $model       = htmlspecialchars(trim($_POST['model']));
    $harga       = filter_var($_POST['harga'], FILTER_VALIDATE_INT);
    $stok        = filter_var($_POST['stok'], FILTER_VALIDATE_INT);
    $kondisi     = htmlspecialchars(trim($_POST['kondisi']));
    $spesifikasi = htmlspecialchars(trim($_POST['spesifikasi']));
    $deskripsi   = htmlspecialchars(trim($_POST['deskripsi']));

    // Validasi dasar
    if (empty($merk) || empty($model) || $harga === false || $harga <= 0 || $stok === false || $stok < 0 || empty($kondisi) || empty($spesifikasi) || empty($deskripsi)) {
        echo "<script>alert('Semua field harus diisi dengan benar dan harga/stok harus valid!'); window.location.href='edit_handphone.php?id=$id';</script>";
        exit();
    }

    $gambar_baru = $data['gambar']; // Default: gunakan nama gambar lama

    // Penanganan upload gambar baru (jika ada)
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $targetDir    = "../images/foto_hp/";
        $gambar_baru  = basename($_FILES["gambar"]["name"]);
        $targetFilePath = $targetDir . $gambar_baru;
        $fileType     = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
        if (in_array($fileType, $allowTypes)) {
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $targetFilePath)) {
                // Jika upload berhasil, hapus gambar lama jika berbeda dan file lama ada
                if (!empty($data['gambar']) && $data['gambar'] != $gambar_baru && file_exists($targetDir . $data['gambar'])) {
                    unlink($targetDir . $data['gambar']);
                }
            } else {
                echo "<script>alert('Gagal mengunggah gambar baru! Silakan coba lagi.'); window.location.href='edit_handphone.php?id=$id';</script>";
                exit();
            }
        } else {
            echo "<script>alert('Maaf, hanya file JPG, JPEG, PNG, & GIF yang diizinkan untuk gambar baru!'); window.location.href='edit_handphone.php?id=$id';</script>";
            exit();
        }
    }

    // 4. Update data di database menggunakan Prepared Statement
    $stmt_update = mysqli_prepare($koneksi, "UPDATE handphone SET merk=?, model=?, harga=?, stok=?, kondisi=?, spesifikasi=?, deskripsi=?, gambar=? WHERE id=?");

    if ($stmt_update) {
        mysqli_stmt_bind_param($stmt_update, "ssiissssi", $merk, $model, $harga, $stok, $kondisi, $spesifikasi, $deskripsi, $gambar_baru, $id);

        if (mysqli_stmt_execute($stmt_update)) {
            // Log aktivitas (opsional)
            // $log_stmt = mysqli_prepare($koneksi, "INSERT INTO log_aktivitas (deskripsi_aktivitas) VALUES (?)");
            // $log_desc = "Admin memperbarui HP: " . $merk . " " . $model . " (ID: " . $id . ")";
            // mysqli_stmt_bind_param($log_stmt, "s", $log_desc);
            // mysqli_stmt_execute($log_stmt);
            // mysqli_stmt_close($log_stmt);

            header("Location: handphone.php?status=success_edit");
            exit();
        } else {
            echo "<script>alert('Gagal memperbarui handphone: " . mysqli_error($koneksi) . "');</script>";
        }
        mysqli_stmt_close($stmt_update);
    } else {
        error_log("Gagal menyiapkan statement update handphone: " . mysqli_error($koneksi));
        echo "<script>alert('Terjadi kesalahan database saat menyiapkan update query.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Handphone: <?= htmlspecialchars($data['merk'] . ' ' . $data['model']) ?></title>
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
        .img-thumbnail {
            border: 1px solid #ddd;
            padding: 3px;
            border-radius: 8px;
            background-color: #fff;
            object-fit: cover;
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
        <h3>Edit Handphone</h3>
        <form method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="merk" class="form-label">Merk HP</label>
                    <input type="text" name="merk" id="merk" class="form-control" value="<?= htmlspecialchars($data['merk']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="model" class="form-label">Model HP</label>
                    <input type="text" name="model" id="model" class="form-control" value="<?= htmlspecialchars($data['model']) ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="harga" class="form-label">Harga</label>
                    <input type="number" name="harga" id="harga" class="form-control" value="<?= htmlspecialchars($data['harga']) ?>" required min="0">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="stok" class="form-label">Stok</label>
                    <input type="number" name="stok" id="stok" class="form-control" value="<?= htmlspecialchars($data['stok']) ?>" required min="0">
                </div>
            </div>
            <div class="mb-3">
                <label for="kondisi" class="form-label">Kondisi HP</label>
                <select name="kondisi" id="kondisi" class="form-control" required>
                    <option value="baru" <?= $data['kondisi'] == 'baru' ? 'selected' : '' ?>>Baru</option>
                    <option value="bekas" <?= $data['kondisi'] == 'bekas' ? 'selected' : '' ?>>Bekas</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="spesifikasi" class="form-label">Spesifikasi (singkat)</label>
                <textarea name="spesifikasi" id="spesifikasi" class="form-control" rows="3" required><?= htmlspecialchars($data['spesifikasi']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi Lengkap Produk</label>
                <textarea name="deskripsi" id="deskripsi" class="form-control" rows="5" required><?= htmlspecialchars($data['deskripsi']) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Gambar Saat Ini</label><br>
                <?php
                $imagePath = '../images/foto_hp/' . htmlspecialchars($data['gambar']);
                if (!empty($data['gambar']) && file_exists($imagePath)) {
                    echo '<img src="' . $imagePath . '" width="150" height="150" class="img-thumbnail mb-2" alt="Gambar Handphone Saat Ini">';
                } else {
                    echo '<p class="text-muted">Tidak ada gambar saat ini.</p>';
                }
                ?>
            </div>
            <div class="mb-3">
                <label for="gambar" class="form-label">Ganti Gambar (opsional)</label>
                <input type="file" name="gambar" id="gambar" class="form-control" accept="image/*">
                <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</small>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button class="btn btn-primary" type="submit">Update Handphone</button>
                <a href="handphone.php" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</body>
</html>