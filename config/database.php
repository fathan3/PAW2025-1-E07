<?php

// =========================
// BAGIAN VALIDASI INPUT
// =========================

function wajibIsi($data)
{
    return trim($data) !== '';
}

function hanyaHuruf($data)
{
    // Hanya Huruf & Spasi (Untuk Nama)
    return preg_match("/^[a-zA-Z\s]+$/", $data);
}

function formatUsername($data)
{
    // Format Username (Huruf, Angka, 4-20 digit)
    return preg_match("/^[a-zA-Z0-9]+$/", $data) && strlen($data) >= 4 && strlen($data) <= 20;
}

function formatEmail($data)
{
    // Format Email Valid
    return filter_var($data, FILTER_VALIDATE_EMAIL) !== false;
}

function formatTelepon($data)
{
    // Format Telepon (Angka saja, awalan 0, 10-13 digit)
    return preg_match("/^0[0-9]{9,12}$/", $data);
}

function kekuatanPassword($data)
{
    // Kekuatan Password (Min 8, Ada Huruf Besar, Huruf Kecil, Angka)
    if (strlen($data) < 8)
        return false;
    if (!preg_match("/[A-Z]/", $data))
        return false;
    if (!preg_match("/[a-z]/", $data))
        return false;
    if (!preg_match("/[0-9]/", $data))
        return false;
    return true;
}

function hanyaAngka($data)
{
    return preg_match("/^[0-9]+$/", $data);
}

function validasiTahunTerbit($tahun, $min_tahun = 1900)
{
    // Validasi Tahun Terbit (Angka saja, di antara tahun min dan tahun saat ini)
    $max_tahun = (int) date('Y');
    $tahun_int = (int) $tahun;

    return hanyaAngka($tahun) && ($tahun_int >= $min_tahun) && ($tahun_int <= $max_tahun);
}

// =========================
// BAGIAN AUTHENTIKASI & REGISTRASI
// =========================

function duplikasiUser($username, $email)
{
    // Cek duplikasi username atau email di tabel pemustaka saat registrasi
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pemustaka WHERE username = :username OR email = :email");
    $stmt->execute(['username' => $username, 'email' => $email]);
    return $stmt->fetchColumn() > 0;
}

function registerPemustaka($fullname, $username, $email, $password, $phone)
{
    global $pdo;
    try {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Query INSERT data pemustaka baru
        $stmt = $pdo->prepare("INSERT INTO pemustaka (username, password, nama_lengkap, email, no_telepon) 
            VALUES (:username, :password, :nama_lengkap, :email, :no_telepon)");

        return $stmt->execute([
            'username' => $username,
            'password' => $hashedPassword,
            'nama_lengkap' => $fullname,
            'email' => $email,
            'no_telepon' => $phone,
        ]);
    } catch (PDOException $e) {
        return false;
    }
}

function authenticateUser(PDO $pdo, string $username, string $password)
{
    // Cek di Tabel Admin
    $stmtAdmin = $pdo->prepare("SELECT id_admin AS id_user, username, password FROM admin WHERE username = :username LIMIT 1");
    $stmtAdmin->bindParam(':username', $username);
    $stmtAdmin->execute();
    $user = $stmtAdmin->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Admin ditemukan dan diverifikasi.
        $user['role'] = 'admin';
        $user['nama_lengkap'] = $user['username'];
        $user['email'] = '';
        return $user;
    }

    // Cek di Tabel Pemustaka (jika gagal di admin)
    $stmtPemustaka = $pdo->prepare("SELECT id_user, username, password, nama_lengkap, email FROM pemustaka WHERE username = :username LIMIT 1");
    $stmtPemustaka->bindParam(':username', $username);
    $stmtPemustaka->execute();
    $user = $stmtPemustaka->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Pemustaka ditemukan dan diverifikasi.
        $user['role'] = 'pemustaka';
        return $user;
    }

    return null; // Gagal di kedua tabel
}

// =========================
// BAGIAN FUNGSI BUKU (koleksi_buku)
// =========================

function getAllBooks()
{
    // Ambil semua data buku
    global $pdo;
    $sql = "
    SELECT 
      id_buku, judul, penerbit, penulis, tahun_terbit, cover
    FROM koleksi_buku
    ORDER BY id_buku DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getBookById(int $id)
{
    // Ambil 1 buku berdasarkan ID
    global $pdo;
    $stmt = $pdo->prepare("
    SELECT id_buku, judul, penerbit, tahun_terbit, penulis, sinopsis, cover
    FROM koleksi_buku
    WHERE id_buku = :id
    ");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

function createBook(array $data)
{
    // Tambah data buku baru
    global $pdo;
    $stmt = $pdo->prepare("
    INSERT INTO koleksi_buku (judul, penerbit, tahun_terbit, penulis, sinopsis, cover)
    VALUES (:judul, :penerbit, :tahun_terbit, :penulis, :sinopsis, :cover)
    ");
    $stmt->execute([
        'judul' => $data['judul'],
        'penerbit' => $data['penerbit'],
        'tahun_terbit' => $data['tahun_terbit'],
        'penulis' => $data['penulis'],
        'sinopsis' => $data['sinopsis'],
        'cover' => $data['cover'],
    ]);
}

function updateBook(int $id, array $data)
{
    // Update data buku
    global $pdo;
    $stmt = $pdo->prepare("
    UPDATE koleksi_buku
    SET judul = :judul, penerbit = :penerbit, tahun_terbit = :tahun_terbit, 
        penulis = :penulis, sinopsis = :sinopsis, cover = :cover
    WHERE id_buku = :id
    ");
    $stmt->execute([
        'judul' => $data['judul'],
        'penerbit' => $data['penerbit'],
        'tahun_terbit' => $data['tahun_terbit'],
        'penulis' => $data['penulis'],
        'sinopsis' => $data['sinopsis'],
        'cover' => $data['cover'],
        'id' => $id
    ]);
}

function deleteBook(int $id)
{
    // Hapus buku dan file cover-nya
    global $pdo;

    // Ambil nama cover
    $stmt = $pdo->prepare("SELECT cover FROM koleksi_buku WHERE id_buku = :id");
    $stmt->execute(['id' => $id]);
    $buku = $stmt->fetch();

    // Hapus file fisik cover jika ada
    if ($buku && !empty($buku['cover'])) {
        $path = __DIR__ . "/../assets/cover/" . $buku['cover'];
        if (file_exists($path)) {
            unlink($path);
        }
    }

    // Hapus data buku dari database
    $stmt = $pdo->prepare("DELETE FROM koleksi_buku WHERE id_buku = :id");
    $stmt->execute(['id' => $id]);
}

// =========================
// BAGIAN FUNGSI PEMINJAMAN
// =========================

function getAllLoans()
{
    // Ambil semua daftar peminjaman (untuk admin)
    global $pdo;
    $stmt = $pdo->prepare("
    SELECT 
      p.id_peminjaman, u.nama_lengkap AS nama_peminjam, b.judul AS judul_buku,
      p.tgl_peminjaman, p.max_tgl_kembali, p.tgl_kembali, p.status
    FROM peminjaman p
    JOIN pemustaka u ON p.id_user = u.id_user
    JOIN koleksi_buku b ON p.id_buku = b.id_buku
    ORDER BY p.id_peminjaman DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

function createLoanRequest(int $id_user, int $id_buku)
{
    // Buat permintaan peminjaman baru dengan status 'Request'
    global $pdo;
    $stmt = $pdo->prepare("
        INSERT INTO peminjaman (id_user, id_buku, status)
        VALUES (:id_user, :id_buku, 'Request')
    ");
    $stmt->execute(['id_user' => $id_user, 'id_buku' => $id_buku]);
}

function approveLoan(int $id)
{
    // Setujui peminjaman: update status, tgl pinjam, dan max tgl kembali (+7 hari)
    global $pdo;
    $tgl_peminjaman = date('Y-m-d');
    $max_tgl_kembali = date('Y-m-d', strtotime('+7 days'));
    $status = 'Dipinjamkan';

    $stmt = $pdo->prepare("
    UPDATE peminjaman 
    SET status = :status, tgl_peminjaman = :pinjam, max_tgl_kembali = :maxkembali 
    WHERE id_peminjaman = :id
    ");

    $stmt->execute([
        'status' => $status,
        'pinjam' => $tgl_peminjaman,
        'maxkembali' => $max_tgl_kembali,
        'id' => $id,
    ]);
}

function checkExistingLoan(int $id_user, int $id_buku)
{
    // Cek apakah user sudah memiliki pinjaman aktif (Request atau Dipinjamkan) untuk buku ini
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM peminjaman 
        WHERE id_user = :id_user AND id_buku = :id_buku AND status IN ('Request', 'Dipinjamkan')
    ");

    $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
    $stmt->bindParam(':id_buku', $id_buku, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchColumn() > 0;
}

function getLoanDetailsById(int $id_peminjaman)
{
    // Mengambil detail peminjaman berdasarkan ID pinjaman
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT p.*, b.judul AS judul_buku 
        FROM peminjaman p 
        JOIN koleksi_buku b ON p.id_buku = b.id_buku
        WHERE p.id_peminjaman = :id
    ");
    $stmt->bindParam(':id', $id_peminjaman, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getLoansByUserId(int $id_user)
{
    // Mengambil daftar peminjaman spesifik untuk pengguna yang sedang login
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT 
            p.id_peminjaman, p.id_user, b.judul AS judul_buku,
            p.tgl_peminjaman, p.max_tgl_kembali, p.tgl_kembali, p.status
        FROM peminjaman p
        JOIN koleksi_buku b ON p.id_buku = b.id_buku
        WHERE p.id_user = :id_user
        ORDER BY p.id_peminjaman DESC
    ");
    $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function returnLoan(int $id_peminjaman, int $id_buku)
{
    // Mencatat pengembalian buku: update status dan tanggal kembali
    global $pdo;
    $tgl_kembali = date('Y-m-d');
    $status = 'Dikembalikan';

    $stmt = $pdo->prepare("
      UPDATE peminjaman 
      SET status = :status, tgl_kembali = :tgl 
      WHERE id_peminjaman = :id
    ");

    $stmt->execute([
        'status' => $status,
        'tgl' => $tgl_kembali,
        'id' => $id_peminjaman,
    ]);
}

// =========================
// BAGIAN FUNGSI PEMUSTAKA
// =========================

function getAllPemustaka()
{
    // Ambil semua daftar pengguna (Pemustaka)
    global $pdo;
    $stmt = $pdo->prepare("
    SELECT nama_lengkap, username, email, no_telepon
    FROM pemustaka
    ORDER BY nama_lengkap ASC
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

function getUserDataById($id)
{
    // Ambil data profil pemustaka berdasarkan ID
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM pemustaka WHERE id_user = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function checkDuplicateProfileUpdate(int $id_user, string $newUsername, string $newEmail)
{
    // Cek duplikasi username atau email (mengabaikan ID pengguna saat ini)
    global $pdo;
    $stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM pemustaka 
    WHERE (username = :username OR email = :email) AND id_user != :id_user
    ");
    $stmt->execute([
        'username' => $newUsername,
        'email' => $newEmail,
        'id_user' => $id_user
    ]);

    return $stmt->fetchColumn() > 0;
}

function executeProfileUpdate(int $id_user, array $data)
{
    // Eksekusi query UPDATE data profil
    global $pdo;
    $sql = "UPDATE pemustaka SET username = :username, nama_lengkap = :nama_lengkap, email = :email, no_telepon = :no_telepon WHERE id_user = :id_user";

    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':username', $data['username']);
    $stmt->bindParam(':nama_lengkap', $data['nama_lengkap']);
    $stmt->bindParam(':email', $data['email']);
    $stmt->bindParam(':no_telepon', $data['no_telepon']);
    $stmt->bindParam(':id_user', $id_user);

    return $stmt->execute();
}

function updatePemustakaProfile(int $id_user, array $data, string $oldUsername, string $oldEmail)
{
    $newUsername = $data['username'];
    $newEmail = $data['email'];

    try {
        //Verifikasi username/email baru hanya jika ada perubahan dari data lama.
        if ($newUsername !== $oldUsername || $newEmail !== $oldEmail) {
            if (checkDuplicateProfileUpdate($id_user, $newUsername, $newEmail)) {
                return ['success' => false, 'message' => "Username atau Email sudah digunakan oleh pengguna lain."];
            }
        }
        //Melakukan pembaruan data di database.
        if (executeProfileUpdate($id_user, $data)) {
            return ['success' => true, 'message' => "Profil berhasil diperbarui."];
        } else {
            return ['success' => false, 'message' => "Gagal mengeksekusi update database."];
        }

    } catch (PDOException $e) {
        return ['success' => false, 'message' => "Kesalahan Database: " . $e->getMessage()];
    }
}
?>