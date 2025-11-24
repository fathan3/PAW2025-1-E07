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

try {
    $userData = getUserDataById($id_user);//Fungsi untuk mengambil data pengguna dari database berdasarkan ID sesi.

    if (!$userData) {
        // Hapus sesi dan lempar ke login jika data profil tidak valid
        session_destroy();
        header('Location: ../../index.php?error=data_tidak_ditemukan');
        exit;
    }
} catch (PDOException $e) {
    die("Terjadi kesalahan saat mengambil data profil: " . $e->getMessage());
}

// Persiapan variabel untuk tampilan
$nama_lengkap = htmlspecialchars($userData['nama_lengkap'] ?? $_SESSION['nama_lengkap']);
$role = htmlspecialchars($userData['role'] ?? $_SESSION['role']);
$avatar_initial = strtoupper(substr($nama_lengkap, 0, 1));
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna - PustakaMuda</title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=4">
</head>

<body>

    <div class="dashboard">
        <div class="sidebar">
            <div class="brand">
                <div class="logo">📘</div>
                <div class="brand-text">
                    <h2>PustakaMuda</h2>
                    <p><?= htmlspecialchars(ucfirst($role)); ?></p>
                </div>
            </div>

            <div class="menu">
                <a href="../buku/daftar_buku.php">📚 Daftar Buku</a>
                <a href="../peminjaman/peminjaman.php">📖 Peminjaman</a>
            </div>

            <a href="profil.php" class="admin-box-link">
                <div class="admin-box">
                    <div class="avatar"><?= $avatar_initial; ?></div>
                    <div class="info">
                        <p><?= $nama_lengkap; ?></p>
                        <small><?= htmlspecialchars(ucfirst($role)); ?></small>
                    </div>
                </div>
            </a>
        </div>

        <div class="main">
            <div class="topbar">
                <div class="left-top">
                    <span>Halo, <?= $nama_lengkap; ?>! 👋</span>
                </div>
                <div class="right-top">
                    <a href="../../logout.php" class="logout-btn">Logout</a>
                </div>
            </div>

            <div class="page-header">
                <div>
                    <h1>👤 Profil Pengguna</h1>
                    <p>Informasi akun Anda.</p>
                </div>
                <a href="edit_profil.php" class="btn-add">Edit Profil</a>
            </div>

            <div class="content">
                <table class="table">
                    <tbody>
                        <tr>
                            <th>Username</th>
                            <td><?= htmlspecialchars($userData['username'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <th>Nama Lengkap</th>
                            <td><?= $nama_lengkap; ?></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><?= htmlspecialchars($userData['email'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <th>No Telepon</th>
                            <td><?= htmlspecialchars($userData['no_telepon'] ?? '-'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="footer">
                © 2025 PustakaMuda - Semua Hak Dilindungi
            </div>
        </div>
    </div>

</body>

</html>