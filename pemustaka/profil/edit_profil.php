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
$pesan_sukses = '';
$pesan_error = '';
$errors = []; 

try {
    //Fungsi untuk mengambil data pemustaka berdasarkan id (pemustaka yang login saat ini)
    $userData = getUserDataById($id_user);

    if (!$userData) {
        session_destroy();
        header('Location: ../../index.php?error=data_tidak_ditemukan');
        exit;
    }
} catch (PDOException $e) {
    die("Terjadi kesalahan saat mengambil data profil: " . $e->getMessage());
}

// Simpan data lama untuk komparasi duplikasi saat update
$old_username = $userData['username'];
$old_email = $userData['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_baru = trim($_POST['username'] ?? '');
    $nama_lengkap_baru = trim($_POST['nama_lengkap'] ?? '');
    $email_baru = trim($_POST['email'] ?? '');
    $no_telepon_baru = trim($_POST['no_telepon'] ?? '');

    $data_baru = [
        'username' => $username_baru,
        'nama_lengkap' => $nama_lengkap_baru,
        'email' => $email_baru,
        'no_telepon' => $no_telepon_baru
    ];

    // Wajib Isi
    if (!wajibIsi($nama_lengkap_baru))
        $errors['fullname'] = "Nama lengkap wajib diisi.";
    if (!wajibIsi($username_baru))
        $errors['username'] = "Username wajib diisi.";
    if (!wajibIsi($email_baru))
        $errors['email'] = "Email wajib diisi.";
    if (!wajibIsi($no_telepon_baru))
        $errors['phone'] = "Nomor telepon wajib diisi.";

    // Validasi Format Inputan
    if (wajibIsi($nama_lengkap_baru) && !hanyaHuruf($nama_lengkap_baru)) {
        $errors['fullname'] = "Nama lengkap hanya boleh berisi huruf dan spasi.";
    }
    if (wajibIsi($username_baru) && !formatUsername($username_baru)) {
        $errors['username'] = "Username hanya huruf/angka (4-20 karakter).";
    }
    if (wajibIsi($email_baru) && !formatEmail($email_baru)) {
        $errors['email'] = "Format email tidak valid.";
    }
    if (wajibIsi($no_telepon_baru) && !formatTelepon($no_telepon_baru)) {
        $errors['phone'] = "Nomor telepon harus diawali 0 (10-13 digit).";
    }

    //Cek apakah ada error validasi sebelum melakukan update
    if (empty($errors)) {
        // Jika $errors kosong, LAKUKAN UPDATE
        $result = updatePemustakaProfile($id_user, $data_baru, $old_username, $old_email);

        if ($result['success']) {
            $pesan_sukses = $result['message'];

            // Perbarui data sesi dan muat ulang data pengguna dari DB
            $_SESSION['nama_lengkap'] = $nama_lengkap_baru;
            $userData = getUserDataById($id_user);
            
            // Perbarui data lama untuk form jika sukses
            $old_username = $userData['username'];
            $old_email = $userData['email'];

        } else {
            // Error dari fungsi update (misalnya duplikasi)
            $pesan_error = $result['message'];
        }
    } else {
        // Jika ada error validasi, tampilkan pesan error umum
        $pesan_error = "Terdapat kesalahan pada input form. Mohon periksa kembali.";
    }
    
}

// Persiapan data untuk ditampilkan di form
$nama_lengkap = htmlspecialchars($userData['nama_lengkap'] ?? '');
$avatar_initial = strtoupper(substr($nama_lengkap, 0, 1));
$role = htmlspecialchars($userData['role'] ?? $_SESSION['role']);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - PustakaMuda</title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=7">
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
                    <h1>✍️ Edit Profil</h1>
                    <p>Perbarui informasi akun Anda.</p>
                </div>
                <a href="profil.php" class="btn-add">← Kembali ke Profil</a>
            </div>

            <div class="content">

                <?php if (!empty($pesan_sukses)): ?>
                    <div class="alert-success">
                        <?= $pesan_sukses; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($pesan_error)): ?>
                    <div class="alert">
                        <?= $pesan_error; ?>
                    </div>
                <?php endif; ?>

                <div class="form-card">
                    <form action="edit_profil.php" method="POST">

                        <label for="username">Username</label>
                        <input type="text" id="username" name="username"
                            value="<?= htmlspecialchars($_POST['username'] ?? $userData['username']); ?>">
                        <?php if (isset($errors['username'])): ?>
                            <span class="error-msg"><?= $errors['username']; ?></span>
                        <?php endif; ?>

                        <label for="nama_lengkap">Nama Lengkap</label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap"
                            value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? $userData['nama_lengkap']); ?>">
                        <?php if (isset($errors['fullname'])): ?>
                            <span class="error-msg"><?= $errors['fullname']; ?></span>
                        <?php endif; ?>

                        <label for="email">Email</label>
                        <input type="text" id="email" name="email"
                            value="<?= htmlspecialchars($_POST['email'] ?? $userData['email']); ?>">
                        <?php if (isset($errors['email'])): ?>
                            <span class="error-msg"><?= $errors['email']; ?></span>
                        <?php endif; ?>

                        <label for="no_telepon">No Telepon</label>
                        <input type="text" id="no_telepon" name="no_telepon"
                            value="<?= htmlspecialchars($_POST['no_telepon'] ?? $userData['no_telepon']); ?>">
                        <?php if (isset($errors['phone'])): ?>
                            <span class="error-msg"><?= $errors['phone']; ?></span>
                        <?php endif; ?>

                        <button type="submit" class="btn-submit">Simpan Perubahan</button>
                    </form>
                </div>
            </div>

            <div class="footer">
                © 2025 PustakaMuda - Semua Hak Dilindungi
            </div>
        </div>
    </div>

</body>

</html>