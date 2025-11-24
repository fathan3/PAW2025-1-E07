<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/config.php';

// Cek autentikasi user
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
	header('Location: ../../index.php');
	exit();
}

$upload_error = '';
$errors = [];
$judul = $penulis = $penerbit = $tahun_terbit = $sinopsis = $cover = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Ambil dan bersihkan input
	$judul = trim($_POST['judul'] ?? '');
	$penulis = trim($_POST['penulis'] ?? '');
	$penerbit = trim($_POST['penerbit'] ?? '');
	$tahun_terbit = trim($_POST['tahun_terbit'] ?? '');
	$sinopsis = trim($_POST['sinopsis'] ?? '');
	$cover = null;

	// VALIDASI INPUT FORM
	if (!wajibIsi($judul)) {
		$errors['judul'] = 'Judul buku wajib diisi.';
	}
	if (!wajibIsi($penulis)) {
		$errors['penulis'] = 'Nama penulis wajib diisi.';
	}
	if (!wajibIsi($penerbit)) {
		$errors['penerbit'] = 'Nama penerbit wajib diisi.';
	}
	if (!wajibIsi($tahun_terbit)) {
		$errors['tahun_terbit'] = 'Tahun terbit wajib diisi.';
	} elseif (!validasiTahunTerbit($tahun_terbit)) {
		$errors['tahun_terbit'] = 'Format tahun terbit tidak valid.';
	}
	if (!wajibIsi($sinopsis)) {
		$errors['sinopsis'] = 'Sinopsis wajib diisi.';
	}

	// UPLOAD COVER
	if (!empty($_FILES['cover']['name'])) {
		$targetDir = "../../assets/cover/";
		$originalFileName = basename($_FILES['cover']['name']);
		$fileName = str_replace(' ', '_', $originalFileName);
		$targetFile = $targetDir . $fileName;
		$fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
		$allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

		if (!in_array($fileType, $allowedTypes)) {
			$upload_error = "Hanya file JPG, JPEG, PNG, & GIF yang diperbolehkan.";
		} else {
			if (move_uploaded_file($_FILES['cover']['tmp_name'], $targetFile)) {
				$cover = $fileName;
			} else {
				$upload_error = "Gagal mengupload file cover.";
			}
		}
	}
	// EKSEKUSI PENYIMPANAN KE DATABASE
	if (empty($errors) && empty($upload_error)) {
		try {
			createBook([ // Fungsi untuk menambahkan data buku
				'judul' => $judul,
				'penulis' => $penulis,
				'penerbit' => $penerbit,
				'tahun_terbit' => $tahun_terbit,
				'sinopsis' => $sinopsis,
				'cover' => $cover,
			]);

			// Set pesan sukses dan redirect
			$_SESSION['success_message'] = "Buku '{$judul}' berhasil ditambahkan.";
			header('Location: daftar_buku.php');
			exit;
		} catch (PDOException $e) {
			$upload_error = "Terjadi kesalahan database saat menyimpan buku: " . $e->getMessage();
		}
	}
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Tambah Buku - PustakaMuda</title>
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
				<a href="../buku/daftar_buku.php" class="active">📚 Daftar Buku</a>
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
					<h1>➕ Tambah Buku</h1>
					<p>Isi data buku baru ke dalam koleksi perpustakaan.</p>
				</div>
				<a href="daftar_buku.php" class="btn-add">← Kembali</a>
			</div>

			<div class="content">

				<?php if (!empty($upload_error)): ?>
					<div class="alert">
						<?= htmlspecialchars($upload_error); ?>
					</div>
				<?php endif; ?>

				<div class="form-card">

					<form action="tambah_buku.php" method="post" enctype="multipart/form-data">

						<label>Judul Buku</label>
						<input type="text" name="judul" value="<?= htmlspecialchars($judul); ?>">
						<?php if (!empty($errors['judul'])): ?>
							<div class="error-msg"><?= htmlspecialchars($errors['judul']); ?></div>
						<?php endif; ?>

						<label>Penulis</label>
						<input type="text" name="penulis" value="<?= htmlspecialchars($penulis); ?>">
						<?php if (!empty($errors['penulis'])): ?>
							<div class="error-msg"><?= htmlspecialchars($errors['penulis']); ?></div>
						<?php endif; ?>

						<label>Penerbit</label>
						<input type="text" name="penerbit" value="<?= htmlspecialchars($penerbit); ?>">
						<?php if (!empty($errors['penerbit'])): ?>
							<div class="error-msg"><?= htmlspecialchars($errors['penerbit']); ?></div>
						<?php endif; ?>

						<label>Tahun Terbit</label>
						<input type="text" name="tahun_terbit" value="<?= htmlspecialchars($tahun_terbit); ?>"
							placeholder="Contoh: <?= date('Y'); ?>">
						<?php if (!empty($errors['tahun_terbit'])): ?>
							<div class="error-msg"><?= htmlspecialchars($errors['tahun_terbit']); ?></div>
						<?php endif; ?>

						<label>Sinopsis</label>
                        <textarea name="sinopsis" class="input-sinopsis"><?= htmlspecialchars($sinopsis); ?></textarea>
                        <?php if (!empty($errors['sinopsis'])): ?>
                            <div class="error-msg"><?= htmlspecialchars($errors['sinopsis']); ?></div>
                        <?php endif; ?>

						<label>Cover Buku (opsional)</label>
						<input type="file" name="cover">

						<button type="submit" class="btn-submit">💾 Simpan Buku</button>
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