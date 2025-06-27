<?php
// File: pengguna.php
// Fungsi: Menampilkan daftar semua pengguna terdaftar.

session_start();
include 'koneksi.php';

// Mengambil semua data pengguna dari database
$stmt = mysqli_prepare($koneksi, "SELECT id, nama, email, role FROM users ORDER BY nama ASC");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

$hasUsers = $result && mysqli_num_rows($result) > 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengguna - Admin Panel Jual HP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* CSS Umum & Sidebar */
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
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4>Admin Panel</h4>
        <a href="dashboard.php" class="nav-link">Dashboard</a>
        <a href="produk.php" class="nav-link">Handphone</a>
        <a href="transaksi.php" class="nav-link">Transaksi</a>
        <a href="pengguna.php" class="nav-link active">Pengguna</a>
        <a href="logout.php" class="nav-link">Logout</a>
    </div>

    <div class="content">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pengguna</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-primary">Data Pengguna</h2>
        </div>

        <table class="table table-bordered table-hover align-middle bg-white rounded shadow-sm">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Level</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                if ($hasUsers) {
                    mysqli_data_seek($result, 0);
                    while ($row = mysqli_fetch_assoc($result)) {
                ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['role']) ?></td>
                            <td>
                                <a href="hapus_pengguna.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus pengguna <?= htmlspecialchars($row['nama']) ?>? Tindakan ini tidak bisa dibatalkan.')">Hapus</a>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">Belum ada pengguna.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>