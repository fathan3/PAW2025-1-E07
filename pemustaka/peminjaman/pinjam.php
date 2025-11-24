<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/config.php';

// Cek autentikasi user
if (!isset($_SESSION['id_user'])) {
    header('Location: ../../index.php');
    exit();
}

$id_user = $_SESSION['id_user'];
$id_buku = $_GET['id'] ?? null;

// Redirect jika ID buku tidak ada
if (!$id_buku) {
    $_SESSION['error_message'] = "ID buku tidak ditemukan.";
    header('Location: daftar_buku.php');
    exit;
}

//Proses Permintaan Peminjaman
try {
    $buku = getBookById($id_buku);// Fungsi untuk mengambil data buku berdasarkan id

    if (!$buku) {
        $_SESSION['error_message'] = "Buku tidak ditemukan.";
        header('Location: ../buku/daftar_buku.php');
        exit;
    }

    // Fungsi untuk mengecek apakah user sudah meminjam/request buku ini
    if (checkExistingLoan($id_user, $id_buku)) {
        $_SESSION['error_message'] = "Anda sudah memiliki permintaan pinjam tertunda untuk buku '{$buku['judul']}'.";
        header('Location: ../buku/daftar_buku.php');
        exit;
    }

    // Fungsi ini mencatat peminjaman dengan status 'Request'.
    createLoanRequest($id_user, $id_buku);

    // Set pesan sukses dan redirect
    $_SESSION['success_message'] = "Permintaan peminjaman buku '{$buku['judul']}' berhasil dikirim.";
    header('Location: peminjaman.php');
    exit;

} catch (PDOException $e) {
    $_SESSION['error_message'] = "Terjadi kesalahan sistem saat memproses peminjaman: " . $e->getMessage();
    header('Location: daftar_buku.php');
    exit;
}
?>