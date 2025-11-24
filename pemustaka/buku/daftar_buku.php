<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/config.php';

// Cek autentikasi user
if (!isset($_SESSION['id_user'])) {
	header('Location: ../../index.php');
	exit();
}

try {
	$bukuList = getAllBooks();//Fungsi untuk mengambil semua data buku
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
					<p><?= htmlspecialchars(ucfirst($_SESSION['role'])); ?></p>
				</div>
			</div>

			<div class="menu">
				<a href="#" class="active">📚 Daftar Buku</a>
				<a href="../peminjaman/peminjaman.php">📖 Peminjaman</a>
			</div>

			<a href="../profil/profil.php" class="admin-box-link">
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
					<p>Jelajahi koleksi buku terbaru yang siap dipinjam.</p>
				</div>
			</div>

			<?php if (isset($_SESSION['error_message'])): ?>
				<div class="alert error-message-box">
					<?= htmlspecialchars($_SESSION['error_message']); ?>
				</div>
				<?php unset($_SESSION['error_message']); // PENTING: Hapus pesan setelah ditampilkan ?>
			<?php endif; ?>
			<div class="content book-list-view">

				<?php if (count($bukuList) > 0): ?>
					<div class="book-grid">
						<?php foreach ($bukuList as $buku): ?>
							<div class="book-card">
								<div class="cover-container">
									<?php
									$coverPath = "../../assets/cover/" . $buku['cover'];
									// Menggunakan placeholder jika cover tidak ada atau file tidak ditemukan
									$placeholderUrl = "https://placehold.co/150x225/435ebe/ffffff?text=Pustaka";
									// Gunakan cover yang ada jika file ditemukan, jika tidak gunakan placeholder
									$coverSrc = (!empty($buku['cover']) && file_exists($coverPath)) ? $coverPath : $placeholderUrl;
									?>
									<img src="<?= htmlspecialchars($coverSrc); ?>"
										alt="Cover Buku: <?= htmlspecialchars($buku['judul']); ?>" class="book-cover-lg">
								</div>
								<div class="book-details">
									<h3 title="<?= htmlspecialchars($buku['judul']); ?>">
										<?= htmlspecialchars($buku['judul']); ?>
									</h3>
									<a href="detail.php?id=<?= $buku['id_buku']; ?>" class="btn pinjam-btn">Detail</a>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php else: ?>
					<div class="no-data-grid">
						Belum ada data buku dalam koleksi.
					</div>
				<?php endif; ?>

			</div>

			<div class="footer">
				© 2025 PustakaMuda - Semua Hak Dilindungi
			</div>
		</div>
	</div>

</body>

</html>