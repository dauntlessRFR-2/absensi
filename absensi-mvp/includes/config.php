<?php
// includes/config.php - Konfigurasi Database

$host = 'localhost';
$dbname = 'absensi_mvp';
$username = 'root';
$password = ''; // Kosong untuk XAMPP/Laragon, sesuaikan jika di hosting

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// Waktu toleransi terlambat (dalam format HH:MM)
define('WAKTU_MASUK_TOLERANSI', '08:00:00');
define('WAKTU_PULANG_MINIMAL', '16:00:00');
