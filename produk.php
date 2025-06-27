<?php
// File: handphone.php
// Fungsi: Menampilkan daftar semua handphone yang tersedia.

session_start(); // Memulai sesi (jika diperlukan untuk otentikasi)
include 'koneksi.php'; // Memasukkan file koneksi database

// Mengambil semua data handphone dari database
// Menggunakan prepared statement untuk konsistensi, meskipun tanpa parameter input.
$stmt = mysqli_prepare($koneksi, "SELECT * FROM handphone ORDER BY id DESC"); // Urutkan berdasarkan ID terbaru
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

// Mengecek apakah ada data handphone yang diambil
$hasHandphones = $result && mysqli_num_rows($result) > 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Handphone - Admin Panel Jual HP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* CSS Umum & Sidebar (disesuaikan dengan tema biru) */
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e0f2f7;
        }
        .sidebar {
            width: 220px;
            background-color: #0d47a1;
            min-height: 100vh;
            color: #fff;
            position: fixed;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar h4 {
            padding: 20px;
            margin: 0;
            background-color: #1565c0;
            text-align: center;
            font-weight: bold;
        }
        .nav-link {
            color: #fff;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .nav-link:hover, .nav-link.active {
            background-color: #1e88e5;
            text-decoration: none;
            color: #fff;
        }

        /* Main Content Styling */
        .content {
            margin-left: 220px;
            padding: 30px;
            background-color: #e0f2f7;
        }
        .breadcrumb {
            background-color: transparent;
            padding-left: 0;
            margin-bottom: 25px;
        }
        .breadcrumb-item a {
            color: #0d47a1;
            text-decoration: none;
        }
        .breadcrumb-item.active {
            color: #616161;
        }

        /* Tabel Styling */
        .table th {
            background-color: #1565c0; /* Header tabel biru sedang */
            color: white;
            vertical-align: middle;
        }
        .table td {
            vertical-align: middle;
            max-width: 150px; /* Batasi lebar kolom deskripsi/spesifikasi */
            overflow: hidden;
            text-overflow: ellipsis; /* Tambahkan elipsis jika teks terlalu panjang */
            white-space: nowrap; /* Cegah teks pindah baris */
        }
        .img-thumbnail {
            border-radius: 8px;
            object-fit: cover;
        }
        .table-responsive {
            overflow-x: auto; /* Memastikan tabel bisa di-scroll horizontal */
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4>Admin Panel</h4>
        <a href="dashboard.php" class="nav-link">Dashboard</a>
        <a href="produk.php" class="nav-link active">Handphone</a>
        <a href="transaksi.php" class="nav-link">Transaksi</a>
        <a href="pengguna.php" class="nav-link">Pengguna</a>
        <a href="logout.php" class="nav-link">Logout</a>
    </div>

    <div class="content">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Handphone</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-primary">Data Handphone</h2>
            <a href="tambah_produk.php" class="btn btn-primary" style="background-color: #1e88e5; border-color: #1e88e5;">+ Tambah Handphone</a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle bg-white rounded shadow-sm">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Merk</th>
                        <th>Model</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Kondisi</th>
                        <th>Spesifikasi</th>
                        <th>Deskripsi</th>
                        <th>Gambar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    if ($hasHandphones) {
                        mysqli_data_seek($result, 0);
                        while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['merk']) ?></td>
                                <td><?= htmlspecialchars($row['model']) ?></td>
                                <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                <td><?= htmlspecialchars($row['stok']) ?></td>
                                <td><span class="badge <?= ($row['kondisi'] == 'baru') ? 'bg-success' : 'bg-info' ?>"><?= htmlspecialchars(ucfirst($row['kondisi'])) ?></span></td>
                                <td title="<?= htmlspecialchars($row['spesifikasi']) ?>"><?= htmlspecialchars($row['spesifikasi']) ?></td>
                                <td title="<?= htmlspecialchars($row['deskripsi']) ?>"><?= htmlspecialchars($row['deskripsi']) ?></td>
                                <td>
                                    <?php
                                    $imagePath = '../images/foto_hp/' . htmlspecialchars($row['gambar']); // Sesuaikan folder gambar HP
                                    if (!empty($row['gambar']) && file_exists($imagePath)) {
                                        echo '<img src="' . $imagePath . '" width="80" height="80" class="img-thumbnail" alt="Gambar HP">';
                                    } else {
                                        echo '<img src="../images/placeholder.png" width="80" height="80" class="img-thumbnail" alt="Gambar Tidak Tersedia">';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="detail_handphone.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">Detail</a>
                                    <a href="edit_handphone.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="hapus_handphone.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus handphone ini? Ini juga akan menghapus gambar terkait.')">Hapus</a>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                    ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted">Belum ada data handphone.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>