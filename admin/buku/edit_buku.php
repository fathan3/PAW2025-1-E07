<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/config.php';

// Cek autentikasi user
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

$error_message = '';
$errors = [];


// Cek ID buku dari URL
if (!isset($_GET['id'])) {
    die("ID buku tidak ditemukan!");
}

$id = $_GET['id'];

// Ambil data buku berdasarkan ID (untuk mengisi form)
try {
    $buku = getBookById($id);
    if (!$buku) {
        die("Buku tidak ditemukan!");
    }
} catch (PDOException $e) {
    die("Kesalahan saat mengambil data: " . $e->getMessage());
}

// Inisialisasi variabel input form dengan data buku yang ada
$judul = $buku['judul'];
$penulis = $buku['penulis'];
$penerbit = $buku['penerbit'];
$tahun_terbit = $buku['tahun_terbit'];
$sinopsis = $buku['sinopsis'];
$cover = $buku['cover'];


//PROSES UPDATE BUKU (METODE POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $penulis = trim($_POST['penulis'] ?? '');
    $penerbit = trim($_POST['penerbit'] ?? '');
    $tahun_terbit = trim($_POST['tahun_terbit'] ?? '');
    $sinopsis = trim($_POST['sinopsis'] ?? '');

    $upload_error = '';
    $cover_temp = $buku['cover']; // Default cover lama jika tidak input cover baru

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
        $errors['tahun_terbit'] = 'Format tahun terbit tidak valid (harus angka antara 1900 - ' . date('Y') . ').';
    }
    if (!wajibIsi($sinopsis)) {
        $errors['sinopsis'] = 'Sinopsis wajib diisi.';
    }

    // PROSES UPLOAD COVER BARU
    if (!empty($_FILES['cover']['name'])) {
        $targetDir = "../../assets/cover/";
        $fileName = uniqid() . "_" . basename($_FILES['cover']['name']);
        $targetFile = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['cover']['tmp_name'], $targetFile)) {
                // Hapus cover lama
                if (!empty($buku['cover']) && file_exists($targetDir . $buku['cover'])) {
                    unlink($targetDir . $buku['cover']);
                }
                $cover_temp = $fileName;
            } else {
                $upload_error = "Gagal mengupload file cover.";
            }
        } else {
            $upload_error = "Hanya file JPG, JPEG, PNG, & GIF yang diperbolehkan.";
        }
    }

    // EKSEKUSI UPDATE DATABASE jika tidak ada error
    if (empty($errors) && empty($upload_error)) {
        try {
            updateBook($id, [ // Fungsi yang mengupdate data buku
                'judul' => $judul,
                'penulis' => $penulis,
                'penerbit' => $penerbit,
                'tahun_terbit' => $tahun_terbit,
                'sinopsis' => $sinopsis,
                'cover' => $cover_temp,
            ]);

            // pesan sukses dan redirect
            $_SESSION['success_message'] = "Buku '{$judul}' berhasil diperbarui.";
            header("Location: daftar_buku.php");
            exit;
        } catch (PDOException $e) {
            $error_message = "Kesalahan saat mengupdate data: " . $e->getMessage();
        }
    } else {
        if (!empty($upload_error)) {
            $error_message = $upload_error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku - PustakaMuda</title>
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
                <a href="daftar_buku.php" class="active">📚 Daftar Buku</a>
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
                    <h1>✏️ Edit Buku</h1>
                    <p>Perbarui data koleksi buku perpustakaan.</p>
                </div>
                <a href="daftar_buku.php" class="btn-add">← Kembali</a>
            </div>

            <div class="content">

                <?php if (!empty($error_message) || !empty($errors)): ?>
                    <div class="alert">
                        ❗ **Error:** <?php
                        if (!empty($error_message)) {
                            echo htmlspecialchars($error_message);
                        } else {
                            echo "Terdapat beberapa kesalahan pada input Anda. Silakan periksa kembali formulir.";
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <div class="form-card">

                    <form action="edit_buku.php?id=<?= htmlspecialchars($id); ?>" method="post"
                        enctype="multipart/form-data">

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

                        <label>Cover Lama</label>
                        <?php if (!empty($buku['cover'])): ?>
                            <img src="../../assets/cover/<?= htmlspecialchars($buku['cover']); ?>" alt="Cover"
                                class="book-cover">
                        <?php else: ?>
                            <p><i>Tidak ada cover</i></p>
                        <?php endif; ?>

                        <label>Ganti Cover (opsional)</label>
                        <input type="file" name="cover">

                        <button type="submit" class="btn-submit">💾 Simpan Perubahan</button>
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