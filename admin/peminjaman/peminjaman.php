<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/config.php';

// Cek autentikasi user
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
	header('Location: ../../index.php');
	exit();
}

//Proses Aksi (SETUJUI & KEMBALIKAN)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	$aksi = '';
	$id = null;

	// Logika untuk Mendeteksi Tombol yang Diklik (setujui_ID atau kembalikan_ID)
	foreach ($_POST as $key => $value) {
		if (strpos($key, 'setujui_') === 0) {
			$aksi = 'setujui';
			$id = (int) substr($key, 8);
			break;
		} elseif (strpos($key, 'kembalikan_') === 0) {
			$aksi = 'kembalikan';
			$id = (int) substr($key, 11);
			break;
		}
	}

	if ($id && $aksi) {
		try {
			if ($aksi === 'setujui') {
				approveLoan((int) $id);// Aksi Setujui: Mengubah status Request menjadi Dipinjamkan
				$_SESSION['notif'] = "✅ Peminjaman disetujui. Buku sudah dipinjamkan.";

			} elseif ($aksi === 'kembalikan') {
				$loan_detail = getLoanDetailsById((int) $id);//Fungsi untuk mengambil detail pinjaman dulu untuk validasi dan ID buku

				if (!$loan_detail || $loan_detail['status'] !== 'Dipinjamkan') {
					$_SESSION['notif'] = "❌ Error: Pinjaman tidak valid atau sudah diselesaikan.";
					header("Location: peminjaman.php");
					exit;
				}

				$id_buku = $loan_detail['id_buku'];
				returnLoan((int) $id, (int) $id_buku);// Aksi Kembalikan: Mengubah status Dipinjamkan menjadi Dikembalikan
				$_SESSION['notif'] = "📗 Buku telah dikembalikan.";
			}

			header("Location: peminjaman.php");
			exit;
		} catch (PDOException $e) {
			die("Terjadi kesalahan saat memproses aksi: " . $e->getMessage());
		}
	}
}

try {
	$peminjamanList = getAllLoans();//Fungsi untuk mengambil semua data peminjaman
} catch (PDOException $e) {
	die("Terjadi kesalahan saat mengambil data: " . $e->getMessage());
}

// Ambil notifikasi sesi dan hapus
$notif = $_SESSION['notif'] ?? '';
unset($_SESSION['notif']);
?>

<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>PustakaMuda</title>
	<link rel="stylesheet" href="../../assets/css/style.css">
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
				<a href="../pemustaka/pemustaka.php">👤 Pemustaka</a>
				<a href="#" class="active">📖 Peminjaman</a>
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
					<h1>📖 Daftar Peminjaman</h1>
					<p>Kelola transaksi peminjaman buku oleh pemustaka.</p>
				</div>
			</div>

			<?php if ($notif): ?>
				<div class="alert-notif"><?= htmlspecialchars($notif); ?></div>
			<?php endif; ?>

			<div class="content">
				<table class="table">
					<thead>
						<tr>
							<th>No</th>
							<th>Nama Peminjam</th>
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
									<td><?= htmlspecialchars($p['nama_peminjam']); ?></td>
									<td><?= htmlspecialchars($p['judul_buku']); ?></td>
									<td><?= $p['tgl_peminjaman'] ? date('d/m/Y', strtotime($p['tgl_peminjaman'])) : '-'; ?></td>
									<td><?= $p['max_tgl_kembali'] ? date('d/m/Y', strtotime($p['max_tgl_kembali'])) : '-'; ?>
									</td>
									<td><?= $p['tgl_kembali'] ? date('d/m/Y', strtotime($p['tgl_kembali'])) : '-'; ?></td>

									<td>
										<?php if ($p['status'] === 'Request'): ?>
											<span class="status status-request">Request</span>
										<?php elseif ($p['status'] === 'Dipinjamkan'): ?>
											<span class="status status-dipinjamkan">Dipinjamkan</span>
										<?php else: ?>
											<span class="status status-dikembalikan">Dikembalikan</span>
										<?php endif; ?>
									</td>

									<td>
										<?php if ($p['status'] === 'Request'): ?>
											<form method="post" class="form-inline">
												<button class="btn edit" type="submit" name="setujui_<?= $p['id_peminjaman']; ?>"
													value="setujui">✅ Setujui</button>
											</form>
										<?php elseif ($p['status'] === 'Dipinjamkan'): ?>
											<form method="post" class="form-inline">
												<button class="btn delete" type="submit"
													name="kembalikan_<?= $p['id_peminjaman']; ?>" value="kembalikan">↩️
													Kembalikan</button>
											</form>
										<?php else: ?>
											<span class="text-selesai">-</span>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php else: ?>
							<tr>
								<td colspan="8" class="no-data">Belum ada data peminjaman.</td>
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