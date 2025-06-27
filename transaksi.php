<?php
// File: transaksi.php
// Fungsi: Menampilkan daftar semua transaksi yang tercatat.

session_start();
include 'koneksi.php';

// Mengaktifkan error reporting (hanya untuk pengembangan)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Mengambil data transaksi dari database
$query = "SELECT t.id, u.nama AS nama_user, h.merk AS merk_hp, h.model AS model_hp, t.jumlah, t.total, t.tanggal, t.status_transaksi
          FROM transaksi AS t
          LEFT JOIN users AS u ON t.user_id = u.id
          LEFT JOIN handphone AS h ON t.handphone_id = h.id
          ORDER BY t.tanggal DESC";

$stmt = mysqli_prepare($koneksi, $query);

if ($stmt) {
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
} else {
    error_log("Gagal menyiapkan statement transaksi: " . mysqli_error($koneksi));
    die("Terjadi kesalahan dalam mengambil data transaksi. Silakan coba lagi nanti.");
}

$hasTransactions = $result && mysqli_num_rows($result) > 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - Admin Panel Jual HP</title>
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
        <a href="transaksi.php" class="nav-link active">Transaksi</a>
        <a href="pengguna.php" class="nav-link">Pengguna</a>
        <a href="logout.php" class="nav-link">Logout</a>
    </div>

    <div class="content">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Transaksi</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-primary">Data Transaksi</h2>
        </div>

        <table class="table table-bordered table-hover align-middle bg-white rounded shadow-sm">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pengguna</th>
                    <th>Merk HP</th>
                    <th>Model HP</th>
                    <th>Jumlah</th>
                    <th>Total Harga</th>
                    <th>Status</th>
                    <th>Tanggal Transaksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                if ($hasTransactions) {
                    mysqli_data_seek($result, 0);
                    while ($row = mysqli_fetch_assoc($result)) {
                        $status_badge_class = '';
                        switch ($row['status_transaksi']) {
                            case 'completed':
                                $status_badge_class = 'bg-success';
                                break;
                            case 'pending':
                                $status_badge_class = 'bg-warning text-dark';
                                break;
                            case 'cancelled':
                                $status_badge_class = 'bg-danger';
                                break;
                            default:
                                $status_badge_class = 'bg-secondary';
                                break;
                        }
                ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_user'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['merk_hp'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['model_hp'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['jumlah']) ?></td>
                            <td>Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
                            <td><span class="badge <?= $status_badge_class ?>"><?= htmlspecialchars(ucfirst($row['status_transaksi'])) ?></span></td>
                            <td><?= date('d-m-Y H:i', strtotime($row['tanggal'])) ?></td>
                        </tr>
                <?php
                    }
                } else {
                ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">Belum ada transaksi.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>