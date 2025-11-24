<?php
session_start();
require_once 'config/database.php';
require_once 'config/config.php';

$success_message = '';
$errors = [];
$fullname = $username = $email = $password = $phone = '';

if (isset($_SESSION['login_message'])) {
	$success_message = $_SESSION['login_message'];
	unset($_SESSION['login_message']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$fullname = trim($_POST['fullname'] ?? '');
	$username = trim($_POST['username'] ?? '');
	$email = trim($_POST['email'] ?? '');
	$password = trim($_POST['password'] ?? '');
	$phone = trim($_POST['phone'] ?? '');

	//Validasi Wajib Isi
	if (!wajibIsi($fullname))
		$errors['fullname'] = "Nama lengkap wajib diisi.";
	if (!wajibIsi($username))
		$errors['username'] = "Username wajib diisi.";
	if (!wajibIsi($email))
		$errors['email'] = "Email wajib diisi.";
	if (!wajibIsi($password))
		$errors['password'] = "Password wajib diisi.";
	if (!wajibIsi($phone))
		$errors['phone'] = "Nomor telepon wajib diisi.";

	//Validasi Format Inputan
	if (wajibIsi($fullname) && !hanyaHuruf($fullname)) {
		$errors['fullname'] = "Nama lengkap hanya boleh berisi huruf dan spasi.";
	}
	if (wajibIsi($username) && !formatUsername($username)) {
		$errors['username'] = "Username hanya huruf/angka (4-20 karakter).";
	}
	if (wajibIsi($email) && !formatEmail($email)) {
		$errors['email'] = "Format email tidak valid.";
	}
	if (wajibIsi($phone) && !formatTelepon($phone)) {
		$errors['phone'] = "Nomor telepon harus diawali 0 (10-13 digit).";
	}
	if (wajibIsi($password) && !kekuatanPassword($password)) {
		$errors['password'] = "Password min. 8 karakter, harus ada Huruf Besar, kecil & angka.";
	}

	if (empty($errors)) {
		if (duplikasiUser($username, $email)) { //Fungsi untuk mengecek duplikat username & email
			$errors['username'] = "Username atau email sudah terdaftar.";
		}
	}

	//Simpan Data
	if (empty($errors)) {
		if (registerPemustaka($fullname, $username, $email, $password, $phone)) {
			// Pendaftaran berhasil, redirect ke halaman login dengan pesan sukses
			$_SESSION['login_message'] = "Pendaftaran berhasil! Silakan login.";
			header("Location: index.php");
			exit;
		} else {
			$errors['general'] = "Terjadi kesalahan sistem saat menyimpan data.";
		}
	}
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>PustakaMuda - Daftar</title>
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
				<h2>Buat Akun Baru</h2>
				<p>Lengkapi data berikut untuk membuat akun perpustakaan.</p>

				<?php if (!empty($errors['general'])): ?>
					<div class="error-msg"><?= htmlspecialchars($errors['general']); ?></div>
				<?php endif; ?>

				<form method="post" action="register.php">
					<label>Nama Lengkap</label>
					<input name="fullname" value="<?= htmlspecialchars($fullname); ?>">
					<?php if (!empty($errors['fullname'])): ?>
						<div class="error-msg"><?= htmlspecialchars($errors['fullname']); ?></div>
					<?php endif; ?>

					<label>Username</label>
					<input name="username" value="<?= htmlspecialchars($username); ?>">
					<?php if (!empty($errors['username'])): ?>
						<div class="error-msg"><?= htmlspecialchars($errors['username']); ?></div>
					<?php endif; ?>

					<label>Email</label>
					<input name="email" value="<?= htmlspecialchars($email); ?>">
					<?php if (!empty($errors['email'])): ?>
						<div class="error-msg"><?= htmlspecialchars($errors['email']); ?></div>
					<?php endif; ?>

					<label>Password</label>
					<input name="password" type="password" value="<?= htmlspecialchars($password); ?>">
					<?php if (!empty($errors['password'])): ?>
						<div class="error-msg"><?= htmlspecialchars($errors['password']); ?></div>
					<?php endif; ?>

					<label>No. Telepon</label>
					<input name="phone" value="<?= htmlspecialchars($phone); ?>">
					<?php if (!empty($errors['phone'])): ?>
						<div class="error-msg"><?= htmlspecialchars($errors['phone']); ?></div>
					<?php endif; ?>

					<button type="submit" class="btn-auth">Daftar Sekarang</button>
				</form>

				<div class="auth-footer">
					Sudah punya akun? <a href="index.php">Masuk di sini</a>
				</div>
			</div>
		</div>
	</div>
</body>

</html>