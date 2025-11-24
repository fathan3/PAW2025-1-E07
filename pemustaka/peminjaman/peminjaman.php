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
$peminjamanList = [];
$error_message = '';

//Menangani pengiriman form saat Pemustaka mengklik tombol "Kembalikan".
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_peminjaman = null;
    $aksi = '';

    // Mendeteksi tombol 'kembalikan_ID'
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'kembalikan_') === 0) {
            $aksi = 'kembalikan';
            $id_peminjaman = (int) substr($key, 11);
            break;
        }
    }

    if ($id_peminjaman && $aksi === 'kembalikan') {
        try {
            //Ambil detail pinjaman untuk mendapatkan id Buku
            $loan_detail = getLoanDetailsById((int) $id_peminjaman);

            $id_buku = $loan_detail['id_buku'];

            //Catat pengembalian buku di database
            returnLoan((int) $id_peminjaman, (int) $id_buku);

            $_SESSION['success_message'] = "Pengembalian buku '{$loan_detail['judul_buku']}' berhasil dicatat.";
            header("Location: peminjaman.php");
            exit;
        } catch (Exception $e) {
            $error_message = $e->getMessage();
        } catch (PDOException $e) {
            $error_message = "Terjadi kesalahan sistem: " . $e->getMessage();
        }
    }
}

//Mengambil daftar peminjaman spesifik untuk user yang sedang login.
try {
    $peminjamanList = getLoansByUserId($id_user);
} catch (PDOException $e) {
    $error_message = "Terjadi kesalahan saat mengambil data: " . $e->getMessage();
}

// Menangani notifikasi sukses/error sesi
$notif = $_SESSION['success_message'] ?? '';
$error_notif = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

if ($error_message) {
    $error_notif = $error_message;
}

$nama_lengkap = htmlspecialchars($_SESSION['nama_lengkap']);
$role = htmlspecialchars($_SESSION['role']);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Saya - PustakaMuda</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
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
                <a href="../buku/daftar_buku.php">📚 Daftar Buku</a>
                <a href="#" class="active">📖 Peminjaman</a>
            </div>

            <a href="../profil/profil.php" class="admin-box-link">
                <div class="admin-box">
                    <div class="avatar"><?= strtoupper(substr($nama_lengkap, 0, 1)); ?></div>
                    <div class="info">
                        <p><?= $nama_lengkap; ?></p>
                        <small><?= ucfirst($role); ?></small>
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
                    <h1>📖 Peminjaman Saya</h1>
                    <p>Daftar buku yang sedang Anda pinjam atau minta pinjam.</p>
                </div>
            </div>

            <?php if ($notif): ?>
                <div class="alert-notif"><?= htmlspecialchars($notif); ?></div>
            <?php endif; ?>
            <?php if ($error_notif): ?>
                <div class="alert"><?= htmlspecialchars($error_notif); ?></div>
            <?php endif; ?>

            <div class="content">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul Buku</th>
                            <th>Tanggal Pinjam</th>
                            <th>Maks Tanggal Kembali</th>
                            <th>Tanggal Kembali</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($peminjamanList) > 0): ?>
                            <?php $no = 1;
                            foreach ($peminjamanList as $p): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($p['judul_buku']); ?></td>
                                    <td><?= $p['tgl_peminjaman'] ? date('d/m/Y', strtotime($p['tgl_peminjaman'])) : '-'; ?></td>
                                    <td><?= $p['max_tgl_kembali'] ? date('d/m/Y', strtotime($p['max_tgl_kembali'])) : '-'; ?>
                                    </td>
                                    <td><?= $p['tgl_kembali'] ? date('d/m/Y', strtotime($p['tgl_kembali'])) : '-'; ?></td>
                                    <td>
                                        <?php if ($p['status'] === 'Request'): ?>
                                            <span class="status status-request">Request</span>
                                        <?php elseif ($p['status'] === 'Dipinjamkan'): ?>
                                            <span class="status status-dipinjamkan">Dipinjam</span>
                                        <?php else: ?>
                                            <span class="status status-dikembalikan">Dikembalikan</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if ($p['status'] === 'Dipinjamkan'): ?>
                                            <form method="post" class="form-inline">
                                                <button class="btn delete" type="submit"
                                                    name="kembalikan_<?= $p['id_peminjaman']; ?>" value="kembalikan">
                                                    ↩️ Kembalikan
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span>-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="no-data">Anda belum memiliki riwayat peminjaman.</td>
                            </tr>
                        <?php endif; ?>
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