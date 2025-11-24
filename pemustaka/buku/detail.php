<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/config.php';

// Cek autentikasi user
if (!isset($_SESSION['id_user'])) {
    header('Location: ../../index.php');
    exit();
}

$id_buku = $_GET['id'] ?? null;

// Redirect jika ID buku tidak ada
if (!$id_buku) {
    header('Location: index.php');
    exit;
}

try {
    $buku = getBookById($id_buku);//Fungsi untuk mengambil 1 buku berdasarkan id

    if (!$buku) {
        // Buku tidak ditemukan
        $_SESSION['error'] = "Buku dengan ID tersebut tidak ditemukan.";
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    die("Terjadi kesalahan saat mengambil data buku: " . $e->getMessage());
}

// Persiapan path cover (menggunakan placeholder jika file cover tidak ditemukan)
$coverPath = "../../assets/cover/" . $buku['cover'];
$placeholderUrl = "https://placehold.co/300x450/435ebe/ffffff?text=Pustaka";
$coverSrc = (!empty($buku['cover']) && file_exists($coverPath)) ? $coverPath : $placeholderUrl;

?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Buku: <?= htmlspecialchars($buku['judul']); ?> | PustakaMuda</title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=7">
</head>

<body>

    <div class="dashboard">
        <div class="sidebar">
            <div class="brand">
                <div class="logo">📘</div>
                <div class="brand-text">
                    <h2>PustakaMuda</h2>
                    <p>Pemustaka</p>
                </div>
            </div>

            <div class="menu">
                <a href="daftar_buku.php">📚 Daftar Buku</a>
                <a href="../peminjaman/peminjaman.php">📖 Peminjaman</a>
            </div>

            <a href="../profil/profil.php" class="admin-box-link">
                <div class="admin-box">
                    <div class="avatar">A</div>
                    <div class="info">
                        <p><?= htmlspecialchars($_SESSION['nama_lengkap']); ?></p>
                        <small><?= htmlspecialchars(ucfirst($_SESSION['role'])); ?></small>
                    </div>
                </div>
            </a>
        </div>

        <div class="main">
            <div class="topbar">
                <div class="left-top">
                    <span>Halo, <?= htmlspecialchars($_SESSION['nama_lengkap']); ?>! 👋</span>
                </div>
                <div class="right-top">
                    <a href="../../logout.php" class="logout-btn">Logout</a>
                </div>
            </div>

            <div class="page-header">
                <div>
                    <h1>📚 Detail Buku</h1>
                    <p>Informasi lengkap mengenai buku <?= htmlspecialchars($buku['judul']); ?>.</p>
                </div>
                <a href="daftar_buku.php" class="btn-add">← Kembali ke Daftar</a>
            </div>

            <div class="content">
                <div class="book-detail-card">

                    <div class="detail-cover">
                        <img src="<?= htmlspecialchars($coverSrc); ?>"
                            alt="Cover Buku: <?= htmlspecialchars($buku['judul']); ?>">
                    </div>

                    <div class="detail-info">
                        <h1><?= htmlspecialchars($buku['judul']); ?></h1>
                        <p>Oleh: **<?= htmlspecialchars($buku['penulis']); ?>**</p>

                        <div class="detail-meta">
                            <div><strong>Penerbit</strong>: <?= htmlspecialchars($buku['penerbit']); ?></div>
                            <div><strong>Tahun Terbit</strong>: <?= htmlspecialchars($buku['tahun_terbit']); ?></div>
                        </div>

                        <div class="sinopsis-box">
                            <h2>Sinopsis</h2>
                            <pre><?= htmlspecialchars($buku['sinopsis']); ?></pre>
                        </div>

                        <div class="action-bar-fixed">
                            <a href="../peminjaman/pinjam.php?id=<?= $buku['id_buku']; ?>"
                                class="btn-pinjam-detail">Pinjam
                                Sekarang</a>
                        </div>

                    </div>
                    <div class="floating-space"></div>
                </div>


            </div>
            <div class="footer">
                © 2025 PustakaMuda - Semua Hak Dilindungi
            </div>
        </div>
    </div>
</body>

</html>