<?php
// koneksi database
$host = 'localhost';     
$dbname = 'library';     
$username = 'root';      
$password = '';          

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

try {
    // Buat instance PDO
    $pdo = new PDO($dsn, $username, $password);

    // Set error mode ke exception agar mudah di-debug
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Set fetch mode default ke associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Tampilkan pesan error jika koneksi gagal
    die("Koneksi ke database gagal: " . $e->getMessage());
}

// Mulai session di sini agar tersedia di semua halaman
session_start();
?>