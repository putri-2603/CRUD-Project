<?php
// File: dashboard.php
// Fungsi: Menampilkan halaman dashboard admin dengan ringkasan data dan aktivitas terbaru.

include 'koneksi.php'; // Memasukkan file koneksi database

// --- PENGAMBILAN DATA DARI DATABASE ---

// 1. Ambil data jumlah handphone (sebelumnya produk)
// Menggunakan prepared statement untuk keamanan (meskipun tanpa parameter langsung, tetap praktik baik)
$stmt_hp = mysqli_prepare($koneksi, "SELECT COUNT(*) AS total FROM handphone");
mysqli_stmt_execute($stmt_hp);
$result_hp = mysqli_stmt_get_result($stmt_hp);
$totalHandphone = mysqli_fetch_assoc($result_hp)['total'];
mysqli_stmt_close($stmt_hp); // Tutup statement

// 2. Ambil data jumlah transaksi (asumsi ada tabel 'transaksi')
$stmt_transaksi = mysqli_prepare($koneksi, "SELECT COUNT(*) AS total FROM transaksi WHERE status_transaksi = 'completed'"); // Hanya transaksi yang selesai
mysqli_stmt_execute($stmt_transaksi);
$result_transaksi = mysqli_stmt_get_result($stmt_transaksi);
$totalTransaksi = mysqli_fetch_assoc($result_transaksi)['total'];
mysqli_stmt_close($stmt_transaksi);

// 3. Ambil data jumlah pengguna (asumsi nama tabelnya 'users')
$stmt_user = mysqli_prepare($koneksi, "SELECT COUNT(*) AS total FROM users");
mysqli_stmt_execute($stmt_user);
$result_user = mysqli_stmt_get_result($stmt_user);
$totalUser = mysqli_fetch_assoc($result_user)['total'];
mysqli_stmt_close($stmt_user);

// 4. Mengambil aktivitas terbaru dari database (membutuhkan tabel 'log_aktivitas')
// Jika tabel belum ada atau kosong, akan menggunakan dummy data sebagai fallback.
$aktivitas = [];
$stmt_aktivitas = mysqli_prepare($koneksi, "SELECT deskripsi_aktivitas, waktu_aktivitas FROM log_aktivitas ORDER BY waktu_aktivitas DESC LIMIT 5");

if ($stmt_aktivitas) {
    mysqli_stmt_execute($stmt_aktivitas);
    $result_aktivitas = mysqli_stmt_get_result($stmt_aktivitas);
    while ($row = mysqli_fetch_assoc($result_aktivitas)) {
        // Format waktu aktivitas agar lebih mudah dibaca
        $aktivitas[] = htmlspecialchars($row['deskripsi_aktivitas']) . " pada " . date('d M Y H:i', strtotime($row['waktu_aktivitas']));
    }
    mysqli_stmt_close($stmt_aktivitas);
} else {
    // Log error jika prepared statement untuk aktivitas gagal
    error_log("Error preparing statement for activities: " . mysqli_error($koneksi));
}

// Fallback ke dummy data jika tidak ada aktivitas dari database atau ada masalah
if (empty($aktivitas)) {
    $aktivitas = [
        "Admin menambahkan HP baru",
        "Pengguna melakukan transaksi",
        "Pengguna logout",
        "Admin memperbarui detail HP"
    ];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Jual HP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* CSS Umum */
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e0f2f7; /* Latar belakang biru muda */
        }

        /* Sidebar Styling */
        .sidebar {
            width: 220px;
            background-color: #0d47a1; /* Sidebar biru tua */
            min-height: 100vh;
            color: #fff;
            position: fixed;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar h4 {
            padding: 20px;
            margin: 0;
            background-color: #1565c0; /* Header sidebar biru sedang */
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
            background-color: #1e88e5; /* Hover dan active link biru terang */
            text-decoration: none;
            color: #fff;
        }
        .nav-link .badge {
            font-size: 0.75em;
            padding: 0.3em 0.6em;
            border-radius: 0.25rem;
        }

        /* Main Content Styling */
        .content {
            margin-left: 220px;
            padding: 30px;
            background-color: #e0f2f7; /* Konsisten dengan body background */
        }
        .breadcrumb {
            background-color: transparent;
            padding-left: 0;
            margin-bottom: 25px;
        }
        .breadcrumb-item a {
            color: #0d47a1; /* Warna link breadcrumb */
            text-decoration: none;
        }
        .breadcrumb-item.active {
            color: #616161; /* Warna teks active breadcrumb */
        }

        /* Card Info Styling */
        .card-info {
            padding: 25px;
            color: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }
        .card-info:hover {
            transform: translateY(-5px);
        }
        .card-info h5 {
            margin-bottom: 10px;
            font-weight: normal;
            opacity: 0.9;
        }
        .card-info h3 {
            margin: 0;
            font-size: 2.5em;
            font-weight: bold;
        }

        /* Warna Kartu */
        .card-blue-light { background-color: #42a5f5; } /* Biru terang untuk HP */
        .card-green-accent { background-color: #66bb6a; } /* Hijau cerah untuk transaksi */
        .card-orange-accent { background-color: #ffa726; color: black; } /* Oranye untuk pengguna */

        /* Aktivitas Box Styling */
        .activity-box {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .activity-box h5 {
            margin-bottom: 15px;
            color: #0d47a1;
            font-weight: bold;
        }
        .activity-box .list-group-item {
            border: none;
            border-bottom: 1px solid #eee;
            padding: 10px 0;
            color: #424242;
        }
        .activity-box .list-group-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4>Admin Panel</h4>
        <a href="dashboard.php" class="nav-link active">Dashboard</a>
        <a href="produk.php" class="nav-link">Handphone <span class="badge bg-light text-primary"><?= $totalHandphone ?></span></a>
        <a href="transaksi.php" class="nav-link">Transaksi <span class="badge bg-light text-success"><?= $totalTransaksi ?></span></a>
        <a href="pengguna.php" class="nav-link">Pengguna <span class="badge bg-light text-warning"><?= $totalUser ?></span></a>
        <a href="logout.php" class="nav-link">Logout</a>
    </div>

    <div class="content">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
        </nav>

        <h2 class="mb-4 text-primary">Dashboard Overview</h2>
        <p class="text-secondary">Selamat datang di panel admin. Berikut adalah ringkasan data penting dan aktivitas terbaru:</p>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card-info card-blue-light">
                    <h5>Total Handphone</h5>
                    <h3><?= $totalHandphone ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-info card-green-accent">
                    <h5>Transaksi Selesai</h5>
                    <h3><?= $totalTransaksi ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-info card-orange-accent">
                    <h5>Total Pengguna</h5>
                    <h3><?= $totalUser ?></h3>
                </div>
            </div>
        </div>

        <div class="activity-box">
            <h5>Aktivitas Terbaru</h5>
            <ul class="list-group">
                <?php foreach ($aktivitas as $act): ?>
                    <li class="list-group-item"><?= $act ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</body>
</html>