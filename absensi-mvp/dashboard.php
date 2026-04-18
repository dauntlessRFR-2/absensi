<?php
// dashboard.php - Dashboard User (Check-in/Check-out & Riwayat)

require_once 'includes/config.php';
require_once 'includes/functions.php';

requireLogin();

$user_id = $_SESSION['user_id'];
$nama = $_SESSION['nama'];

// Proses Check-in
if (isset($_POST['checkin'])) {
    $tanggal = date('Y-m-d');
    $jam_masuk = date('H:i:s');
    
    try {
        $stmt = $pdo->prepare("INSERT INTO attendance (user_id, tanggal, jam_masuk) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $tanggal, $jam_masuk]);
        $success = 'Check-in berhasil pada ' . formatWaktu($jam_masuk);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $error = 'Anda sudah check-in hari ini';
        } else {
            $error = 'Terjadi kesalahan saat check-in';
        }
    }
}

// Proses Check-out
if (isset($_POST['checkout'])) {
    $tanggal = date('Y-m-d');
    $jam_pulang = date('H:i:s');
    
    try {
        $stmt = $pdo->prepare("UPDATE attendance SET jam_pulang = ? WHERE user_id = ? AND tanggal = ?");
        $stmt->execute([$jam_pulang, $user_id, $tanggal]);
        $success = 'Check-out berhasil pada ' . formatWaktu($jam_pulang);
    } catch (PDOException $e) {
        $error = 'Terjadi kesalahan saat check-out';
    }
}

// Cek status kehadiran hari ini
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT * FROM attendance WHERE user_id = ? AND tanggal = ?");
$stmt->execute([$user_id, $today]);
$todayAttendance = $stmt->fetch();

// Ambil riwayat kehadiran (7 hari terakhir)
$stmt = $pdo->prepare("
    SELECT * FROM attendance 
    WHERE user_id = ? 
    ORDER BY tanggal DESC, id DESC 
    LIMIT 30
");
$stmt->execute([$user_id]);
$riwayat = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Absensi MVP</title>
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
        .btn-checkin {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            color: white;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: bold;
        }
        .btn-checkout {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
            border: none;
            color: white;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: bold;
        }
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
        <div class="container">
            <a class="navbar-brand" href="#">📋 Absensi MVP</a>
            <div class="navbar-nav ms-auto">
                <span class="nav-item nav-link text-white">
                    👤 <?= e($nama) ?>
                </span>
                <a class="nav-item nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= e($success) ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>

        <!-- Card Presensi Hari Ini -->
        <div class="row mb-4">
            <div class="col-md-6 mx-auto">
                <div class="card p-4">
                    <h4 class="text-center mb-4">📅 Presensi Hari Ini</h4>
                    <p class="text-center text-muted mb-4">
                        <?= formatTanggal($today) ?>
                    </p>
                    
                    <?php if ($todayAttendance): ?>
                        <div class="text-center mb-3">
                            <p class="mb-2">✅ Anda sudah check-in</p>
                            <p><strong>Jam Masuk:</strong> <?= formatWaktu($todayAttendance['jam_masuk']) ?></p>
                            <?php if ($todayAttendance['jam_pulang']): ?>
                                <p><strong>Jam Pulang:</strong> <?= formatWaktu($todayAttendance['jam_pulang']) ?></p>
                                <span class="status-badge status-<?= $todayAttendance['status'] ?>">
                                    Status: <?= ucfirst($todayAttendance['status']) ?>
                                </span>
                            <?php else: ?>
                                <form method="POST" class="mt-3">
                                    <button type="submit" name="checkout" class="btn btn-checkout w-100"
                                            onclick="return confirm('Yakin ingin check-out?')">
                                        🏠 Check-Out
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <p class="mb-3">Belum melakukan presensi hari ini</p>
                            <form method="POST">
                                <button type="submit" name="checkin" class="btn btn-checkin w-100"
                                        onclick="return confirm('Yakin ingin check-in?')">
                                    ✨ Check-In
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Riwayat Kehadiran -->
        <div class="card p-4">
            <h4 class="mb-4">📊 Riwayat Kehadiran (30 Hari Terakhir)</h4>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($riwayat)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada data kehadiran</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($riwayat as $r): ?>
                                <tr>
                                    <td><?= formatTanggal($r['tanggal']) ?></td>
                                    <td><?= formatWaktu($r['jam_masuk']) ?></td>
                                    <td><?= formatWaktu($r['jam_pulang']) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= $r['status'] ?>">
                                            <?= ucfirst($r['status']) ?>
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
