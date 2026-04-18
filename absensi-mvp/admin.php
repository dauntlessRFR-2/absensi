<?php
// admin.php - Dashboard Admin (Lihat Semua Data + Export)

require_once 'includes/config.php';
require_once 'includes/functions.php';

requireAdmin();

$nama = $_SESSION['nama'];

// Filter tanggal
$filter_tanggal = $_GET['tanggal'] ?? date('Y-m-d');

// Ambil semua data attendance dengan join ke users
$stmt = $pdo->prepare("
    SELECT a.*, u.nama as nama_user, u.email
    FROM attendance a
    JOIN users u ON a.user_id = u.id
    WHERE a.tanggal = ?
    ORDER BY a.jam_masuk ASC, u.nama ASC
");
$stmt->execute([$filter_tanggal]);
$attendances = $stmt->fetchAll();

// Hitung statistik
$total_hadir = 0;
$total_terlambat = 0;
$total_alpha = 0;

foreach ($attendances as $a) {
    if ($a['status'] === 'hadir') $total_hadir++;
    elseif ($a['status'] === 'terlambat') $total_terlambat++;
    else $total_alpha++;
}

$total_semua = count($attendances);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Absensi MVP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f6fa;
            min-height: 100vh;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .stat-card {
            text-align: center;
            padding: 30px;
            border-radius: 15px;
            color: white;
        }
        .stat-hadir { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
        .stat-terlambat { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-alpha { background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); }
        .stat-total { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .status-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
        }
        .status-hadir { background: #d4edda; color: #155724; }
        .status-terlambat { background: #fff3cd; color: #856404; }
        .status-alpha { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">📋 Admin Absensi</a>
            <div class="navbar-nav ms-auto">
                <span class="nav-item nav-link text-white">
                    👤 <?= e($nama) ?>
                </span>
                <a class="nav-item nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4 px-4">
        <!-- Statistik Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="stat-card stat-total">
                    <h3><?= $total_semua ?></h3>
                    <p class="mb-0">Total Presensi</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card stat-hadir">
                    <h3><?= $total_hadir ?></h3>
                    <p class="mb-0">Hadir Tepat Waktu</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card stat-terlambat">
                    <h3><?= $total_terlambat ?></h3>
                    <p class="mb-0">Terlambat</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card stat-alpha">
                    <h3><?= $total_alpha ?></h3>
                    <p class="mb-0">Alpha/Tidak Hadir</p>
                </div>
            </div>
        </div>

        <!-- Filter & Export -->
        <div class="card p-4 mb-4">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Filter Tanggal</label>
                    <form method="GET">
                        <div class="input-group">
                            <input type="date" class="form-control" name="tanggal" 
                                   value="<?= e($filter_tanggal) ?>">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-8 text-end">
                    <a href="export.php?tanggal=<?= e($filter_tanggal) ?>" 
                       class="btn btn-success">
                        📥 Export CSV
                    </a>
                </div>
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="card p-4">
            <h4 class="mb-4">📊 Data Kehadiran - <?= formatTanggal($filter_tanggal) ?></h4>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($attendances)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    Belum ada data pada tanggal ini
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($attendances as $a): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= e($a['nama_user']) ?></td>
                                    <td><?= e($a['email']) ?></td>
                                    <td><?= formatWaktu($a['jam_masuk']) ?></td>
                                    <td><?= formatWaktu($a['jam_pulang']) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= $a['status'] ?>">
                                            <?= ucfirst($a['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
