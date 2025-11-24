<?php
session_start();
require_once 'config/database.php';
require_once 'config/config.php';

// Inisialisasi pesan
$error_message = '';
$success_message = '';

// Ambil pesan sukses dari sesi (register)
if (isset($_SESSION['login_message'])) {
	$success_message = $_SESSION['login_message'];
	unset($_SESSION['login_message']);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = trim($_POST['username'] ?? '');
	$password = trim($_POST['password'] ?? '');

	// Validasi wajib isi
	if (!wajibIsi($username) || !wajibIsi($password)) {
		$error_message = "❗ Username dan password wajib diisi.";
	} else {
		try {
			// Otentikasi pengguna (Admin atau Pemustaka)
			$user = authenticateUser($pdo, $username, $password);

			if ($user) {
				// Otentikasi Berhasil: Set data sesi
				$_SESSION['id_user'] = $user['id_user'];
				$_SESSION['nama_lengkap'] = $user['nama_lengkap'];
				$_SESSION['role'] = $user['role'];
				$_SESSION['email'] = $user['email'];

				// Redirect sesuai role
				if ($user['role'] === 'admin') {
					header('Location: admin/buku/daftar_buku.php');
				} else {
					header('Location: pemustaka/buku/daftar_buku.php');
				}
				exit();
			} else {
				// Gagal Otentikasi
				$error_message = "❌ Username atau password salah.";
			}

		} catch (PDOException $e) {
			$error_message = "Terjadi kesalahan sistem: " . $e->getMessage();
		}
	}
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>PustakaMuda</title>
	<link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="auth-layout">
	<div class="auth-wrapper">

		<div class="auth-left">
			<div class="brand-logo">📘</div>
			<h1>PustakaMuda</h1>
			<p>Mulai petualangan bacamu hari ini di PustakaMuda.</p>
		</div>

		<div class="auth-right">
			<div class="auth-form">
				<h2>Masuk</h2>
				<p>Selamat datang kembali! Silakan login ke akunmu.</p>

				<?php if (!empty($success_message)): ?>
					<div class="alert-success">
						<?= htmlspecialchars($success_message); ?>
					</div>
				<?php endif; ?>

				<?php if (!empty($error_message)): ?>
					<div class="alert">
						<?= htmlspecialchars($error_message); ?>
					</div>
				<?php endif; ?>

				<form action="index.php" method="POST">
					<label for="username">Username</label>
					<input type="text" id="username" name="username" placeholder="Masukkan username">

					<label for="password">Password</label>
					<input type="password" id="password" name="password" placeholder="Masukkan password">

					<button type="submit" class="btn-auth">Masuk Sekarang</button>
				</form>

				<div class="auth-footer">
					<p>Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
				</div>
			</div>
		</div>

	</div>
</body>

</html>