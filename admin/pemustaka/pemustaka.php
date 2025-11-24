<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/config.php';

// Cek autentikasi user
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
	header('Location: ../../index.php');
	exit();
}

try {
	$pemustakaList = getAllPemustaka();//Fungsi untuk mengambil data semua pemustaka
} catch (PDOException $e) {
	die("Terjadi kesalahan saat mengambil data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>PustakaMuda</title>
	<link rel="stylesheet" href="../../assets/css/style.css?v=7">
</head>

<body>
	<div class="dashboard">
		<div class="sidebar">
			<div class="brand">
				<div class="logo">📘</div>
				<div class="brand-text">
					<h2>PustakaMuda</h2>
					<p>Admin Panel</p>
				</div>
			</div>

			<div class="menu">
				<a href="../buku/daftar_buku.php">📚 Daftar Buku</a>
				<a href="#" class="active">👤 Pemustaka</a>
				<a href="../peminjaman/peminjaman.php">📖 Peminjaman</a>
			</div>

			<a href=" #" class="admin-box-link">
				<div class=" admin-box">
					<div class="avatar"><?= strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)); ?></div>
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
					<h1>👥 Daftar Pemustaka</h1>
					<p>Data semua anggota perpustakaan (pemustaka).</p>
				</div>
			</div>

			<div class="content">
				<table class="table">
					<thead>
						<tr>
							<th>No</th>
							<th>Nama Lengkap</th>
							<th>Username</th>
							<th>Email</th>
							<th>No. Telepon</th>
						</tr>
					</thead>
					<tbody>
						<?php if (count($pemustakaList) > 0): ?>
							<?php $no = 1;
							foreach ($pemustakaList as $p): ?>
								<tr>
									<td><?= $no++; ?></td>
									<td><?= htmlspecialchars($p['nama_lengkap']); ?></td>
									<td><?= htmlspecialchars($p['username']); ?></td>
									<td><?= htmlspecialchars($p['email']); ?></td>
									<td><?= htmlspecialchars($p['no_telepon'] ?? '-'); ?></td>
								</tr>
							<?php endforeach; ?>
						<?php else: ?>
							<tr>
								<td colspan="5" class="no-data">Belum ada pemustaka terdaftar.</td>
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