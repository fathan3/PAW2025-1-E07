<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/config.php';

// Cek autentikasi user
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

// Cek ID buku dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID buku tidak valid.");
}

$id = (int) $_GET['id'];

//Ambil data buku untuk mendapatkan judul sebelum dihapus.
$buku = getBookById($id); // Fungsi untuk data buku berdasarkan id buku
$judulBuku = $buku ? htmlspecialchars($buku['judul']) : "Buku ini";

// Jika buku tidak ditemukan di database
if (!$buku) {
    die("Buku tidak ditemukan di database.");
}

//Dijalankan hanya jika parameter 'confirm=yes' dikirimkan.
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    try {
        deleteBook($id);//Fungsi untuk menghapus data buku beserta cover yang ada di assets/cover
        // Set pesan sukses ke sesi dan redirect
        $_SESSION['success_message'] = "🗑️ Buku '{$judulBuku}' berhasil dihapus secara permanen.";
        header("Location: daftar_buku.php");
        exit;
    } catch (Exception $e) {
        die("Gagal menghapus: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Hapus</title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=2">
</head>

<body>
    <div class="confirm-wrapper">
        <div class="confirm-box">
            <div class="confirm-icon">🗑️</div>
            <h2>Yakin Menghapus?</h2>
            <p>
                Anda akan menghapus buku **"<?= $judulBuku; ?>"**.<br>
                Tindakan ini tidak dapat dibatalkan dan file cover akan ikut terhapus permanen.
            </p>

            <div class="confirm-buttons">
                <a href="daftar_buku.php" class="btn-confirm no">BATAL</a>
                <a href="hapus_buku.php?id=<?= $id; ?>&confirm=yes" class="btn-confirm yes">YA, HAPUS</a>
            </div>
        </div>
    </div>
</body>

</html>