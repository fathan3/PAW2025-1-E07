<?php

$host = 'localhost';
$dbname = 'library';
$username = 'root';
$password = '';

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

try {
    // Membuat instance PDO
    $pdo = new PDO($dsn, $username, $password);

    // Mengatur mode error dan fetch
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Menangani kegagalan koneksi
    die("Koneksi ke database gagal: " . $e->getMessage());
}

?>