<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/config.php';

// Cek autentikasi admin.
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
	header('Location: ../../index.php');
	exit();
}

// Ambil pesan sukses dari sesi jika ada
$success_message = '';
if (isset($_SESSION['success_message'])) {
	$success_message = $_SESSION['success_message'];
	unset($_SESSION['success_message']);
}

try {
	$bukuList = getAllBooks(); // Fungsi yang mengambil semua data buku
} catch (PDOException $e) {
	die("Terjadi kesalahan saat mengambil data buku: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>PustakaMuda</title>
	<link rel="stylesheet" href="../../assets/css/style.css?v=5">
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
				<a href="#" class="active">📚 Daftar Buku</a>
				<a href="../pemustaka/pemustaka.php">👤 Pemustaka</a>
				<a href="../peminjaman/peminjaman.php">📖 Peminjaman</a>
			</div>

			<a href="#" class="admin-box-link">
				<div class="admin-box">
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
					<h1>📗 Daftar Buku</h1>
					<p>Kelola koleksi buku untuk perpustakaan anak & remaja.</p>
				</div>
				<a href="tambah_buku.php" class="btn-add">+ Tambah Buku</a>
			</div>

			<div class="content">
				<?php if (!empty($success_message)): ?>
					<div class="alert-success">
						<?= htmlspecialchars($success_message); ?>
					</div>
				<?php endif; ?>
				<table class="table">
					<thead>
						<tr>
							<th>Cover</th>
							<th>Judul</th>
							<th>Penerbit</th>
							<th>Tahun</th>
							<th>Penulis</th>
							<th>Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php if (count($bukuList) > 0): ?>
							<?php foreach ($bukuList as $buku): ?>
								<tr>
									<td>
										<?php
										// Tampilkan cover atau placeholder jika tidak ada
										$coverPath = "../../assets/cover/" . $buku['cover'];
										if (!empty($buku['cover']) && file_exists($coverPath)):
											?>
											<img src="<?= $coverPath; ?>" alt="Cover" class="book-cover">
										<?php else: ?>
											<div class="cover">No Cover</div>
										<?php endif; ?>
									</td>
									<td><b><?= htmlspecialchars($buku['judul']); ?></b></td>
									<td><?= htmlspecialchars($buku['penerbit']); ?></td>
									<td><?= htmlspecialchars($buku['tahun_terbit']); ?></td>
									<td><span><?= htmlspecialchars($buku['penulis'] ?? 'Tidak ada'); ?></span></td>
									<td>
										<a href="edit_buku.php?id=<?= $buku['id_buku']; ?>" class="btn edit">✏️ Edit</a>
										<a href="hapus_buku.php?id=<?= $buku['id_buku']; ?>" class="btn delete">🗑 Hapus</a>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php else: ?>
							<tr>
								<td colspan="6" class="no-data">Belum ada data buku.</td>
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