<?php
// export.php - Export Data ke CSV

require_once 'includes/config.php';
require_once 'includes/functions.php';

requireAdmin();

$filter_tanggal = $_GET['tanggal'] ?? date('Y-m-d');

// Ambil data dengan join ke users
$stmt = $pdo->prepare("
    SELECT a.*, u.nama as nama_user, u.email
    FROM attendance a
    JOIN users u ON a.user_id = u.id
    WHERE a.tanggal = ?
    ORDER BY u.nama ASC
");
$stmt->execute([$filter_tanggal]);
$attendances = $stmt->fetchAll();

// Set header untuk download CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="absensi_' . $filter_tanggal . '.csv"');

// BOM untuk encoding UTF-8 (agar karakter Indonesia terbaca di Excel)
echo "\xEF\xBB\xBF";

// Buat output stream
$output = fopen('php://output', 'w');

// Header kolom
fputcsv($output, [
    'No',
    'Nama Karyawan',
    'Email',
    'Tanggal',
    'Jam Masuk',
    'Jam Pulang',
    'Status'
]);

// Data
$no = 1;
foreach ($attendances as $a) {
    fputcsv($output, [
        $no++,
        $a['nama_user'],
        $a['email'],
        $a['tanggal'],
        $a['jam_masuk'],
        $a['jam_pulang'],
        $a['status']
    ]);
}

fclose($output);
exit;
