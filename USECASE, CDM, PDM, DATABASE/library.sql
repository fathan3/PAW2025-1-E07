-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 24 Nov 2025 pada 07.39
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`) VALUES
(1, 'admin', '$2a$12$M.lTxIT90SgkplgX4CgbqOcdv3YlC1/j4Pk0exS8kMQbLjfWCV29q');

-- --------------------------------------------------------

--
-- Struktur dari tabel `koleksi_buku`
--

CREATE TABLE `koleksi_buku` (
  `id_buku` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `penerbit` varchar(255) DEFAULT NULL,
  `penulis` varchar(255) NOT NULL,
  `tahun_terbit` int(11) DEFAULT NULL,
  `cover` varchar(255) DEFAULT NULL,
  `sinopsis` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `koleksi_buku`
--

INSERT INTO `koleksi_buku` (`id_buku`, `judul`, `penerbit`, `penulis`, `tahun_terbit`, `cover`, `sinopsis`) VALUES
(31, 'Qanza : Cerita dari Palestina - Negeri Para Nabi dan Rasul', 'm&c!', 'Merve Gulcemal', 2024, 'cerita_dari_palestina.jpg', 'Buku ini mengupas sejarah Palestina dari perspektif keagamaan, dengan fokus pada peran tanah Palestina dalam perjalanan hidup para nabi dan rasul. Pembaca akan diajak mengenal kisah-kisah penting yang terkait dengan kota suci seperti Yerusalem dan Hebron. Budaya dan Tradisi Palestina Selain sejarah, buku ini menggambarkan keunikan budaya dan tradisi masyarakat Palestina, termasuk keindahan seni, makanan khas, dan cara hidup yang penuh semangat meskipun di tengah tantangan. Realitas Kehidupan Modern di Palestina Buku ini menyentuh realitas kehidupan rakyat Palestina di tengah konflik dan perjuangan sehari-hari, namun tetap mempertahankan harapan dan semangat untuk masa depan.'),
(32, 'Ensiklopedia Kota-Kota Besar Di Dunia', 'Laksana', 'MAULI OKI N.', 2019, 'Ensiklopedia_Kota-Kota_Besar_Di_Dunia.jpg', 'Pernahkah kamu berkeinginan menjelajahi dunia? Sebelum kamu mewujudkan keinginanmu itu, yuk baca buku ensiklopedia ini! “Buku adalah jendela dunia.” Nah, dengan membaca buku ini, kamu akan mengenal kota-kota besar di segala sudut dunia. Banyak sekali kota-kota besar di dunia. Kota-kota itu tersebar di lima benua, yaitu Benua Eropa, Asia, Amerika, Afrika, dan Australia. Buku ini merangkum secara ringkas kota-kota besar di dunia. Apa sajakah kota-kota itu? Ada Roma, Seoul, Kuala Lumpur, Baghdad, Havana, Washington DC, Casablanca, Canberra, dan kota-kota lainnya. Teman-teman, yuk, kita mengelilingi dunia lewat buku ini! Selamat membaca!'),
(33, 'Ensiklopedia Anak : Otak', 'm&c!', 'DR Liam Drew', 2023, 'otak.jpg', 'Tahukah kamu kalau otak manusia memiliki lebih dari 85 milyar sel otak (neuron)? Otak juga berkembang sepenuhnya paling tidak saat kamu berusia 20 tahun. Sejak sekitar usia 30 tahun, otak manusia mulai menyusut secara bertahap. Temukan lebih banyak lagi bagaimana organ yang paling kuat di tubuhmu ini berpikir, belajar, dan selalu aktif – bahkan saat kamu tidur! Jangan lewatkan berbagai pengetahuan terkini mengenai otak yang wajib kamu ketahui!'),
(34, 'Ensiklopedia Anak : DNA', 'm&c!', 'Alison Woollard & Dr Sophie Gilbert', 2023, 'dna.jpg', 'DNA adalah molekul yang menyimpan kode kehidupan. Kita semua bergantung pada DNA untuk bertumbuh, bertahan hidup, dan bereproduksi. Mari kita cari tahu apa itu DNA dan apa yang terjadi jika DNA bermutasi!'),
(35, 'Ensiklopedia Anak : Tubuh', 'm&c!', 'Dr Bipasha Choudhury', 2023, 'tubuh.jpeg', 'Tahukah kamu bahwa kamu menumbuhkan kulit baru setiap bulannya? Atau bahwa makanan yang kamu konsumsi bisa memengaruhi tidurmu di malam hari? Kenali lebih dekat cara kerja tubuhmu, apa yang membentuknya, dan bagaimana menjaga tubuh tetap sehat melalui buku pengantar tubuh yang keren ini!'),
(36, 'Ensiklopedia Anak : Bakteria', 'm&c!', 'STEVE MOULD', 2023, 'bakteri.jpg', 'Tahukah kamu kalau bakteri pertama muncul sekitar 3,6 milyar tahun yang lalu, jauh sebelum manusia ada? Atau ada satu jenis bakteri yang menakjubkan yang tiga kali lebih lengket daripada lem super? Ayo kita lihat lebih dekat berbagai mikroba seperti bakteri, virus, dan kuman di buku tentang mikrobiologi yang mengasyikkan ini! Banyak informasi menarik yang membuatmu ingin tahu lebih banyak lagi!'),
(37, 'Komik Lieur: Bolon', 'Kawah Media', 'Sagus', 2019, 'Komik-Lieur--Bolon.jpg', 'Inilah keseharian Somad, bocah berusia 10 tahun yang bercita-cita jadi tukang bandros. Bersama teman-teman sepermainannya Richard, Cecep, Titin, Cindy, dan Iqbal, menjalani hari-hari menyenangkan masa kecil di SDN Cijangkar 1.'),
(38, 'Komik Next G: Dokter Untuk Adikku', 'Mizan Media Utama', 'Nurulita Aulia Karindra, dkk', 2019, 'dokter.jpg', 'Adik tiba-tiba saja tidak enak badan dengan gejala demam dan pilek, aduh ayah dan ibu kebetulan juga sedang tidak ada di rumah. Cimu jadi tidak bisa membawa adik berobat ke dokter, lalu bagaimana ya? Sepertinya Cimu harus berusaha merawat adiknya sendiri di rumah, setidaknya agar adiknya tidak semakin parah dan lekas sembuh.  Cimu akhirnya berusaha mengecek kondisi adik untuk menentukan perawatan apa yang bisa ia berikan. Badan adik panas, Cimu akan mencoba mengukur suhu tubuh adik dengan termostat! Hidung anak tersumbat dan sering bersin, hmm bisa jadi adik terkena flu. Cimu pun langsung bergegas mencari stok obat flu yang ada di rumahnya.  Apakah hal yang dilakukan Cimu untuk merawat adiknya sudah benar? Akan kah adik Cimu bisa segera pulih setelah dirawat oleh dokter dadakan, kakaknya Cimu?)'),
(39, 'Komik Next G: Mainan Surprise', 'Curhat Anak Bangsa', 'Maulidya Rahma Sumaedi, dkk', 2019, 'mainan.jpg', 'Maria bersemangat sekali karena hari ini ia akan membeli mainan surprise yang sudah lama diidamkannya. Selain itu, mainan surprise ini adalah mainan pertamanya. Tentu saja, Maria sangat berharap bisa dapat karakter kesukaannya. Kira-kira, harapan Maria akan terwujud tidak, ya? Apa sih sebenarnya isi mainan surprise yang menjadi incaran Maria sejak lama itu? Bagaimana rupanya ya?'),
(40, 'Interaktif Sains Cilik - Dinosaurus', 'Bhuana Ilmu Populer', 'Out of the Box', 2025, 'dinosaurus.jpg', 'Para saintis cilik pasti senang belajar tentang dinosaurus. Tarik dan lihatlah dunia dinosaurus yang keren!'),
(41, 'Suara Air yang Menangis', 'C-Klik Media', 'Anala Lashita', 2025, 'suara_air.png', '“Kenapa kamu menangis?” Daun-daun itu bertanya kepada keran air yang masih mengucurkan air.  “Aku menangis karena aku dibuang sia-sia.” Jawab air yang keluar dari keran air.  “Ah, benar juga! Siapa yang melakukan ini padamu?”  “Anak-anak yang tadi bermain bola di sini. Mereka terburu-buru pulang sehingga tidak menutupku dengan rapat! Huhuhu …” Air yang keluar dari keran itu terisak-isak.  Lihat? Air pun bisa menangis saat ia disia-siakan atau dibuang-buang! Bagaimana dengan benda-benda yang lain? Apakah mereka juga merasa sedih saat kita menyia-nyiakan atau tidak merawatnya?'),
(42, 'A Little Princess', 'Anak Hebat Indonesia', 'Frances Hodgson Burnett', 2024, 'princess.jpg', 'Sara dikirim ke sebuah sekolah asrama bergengsi di London di mana dia dianggap seperti seorang tuan putri karena kekayaannya. Namun, hidupnya berubah secara drastis tepat di hari ulang tahunnya, ketika ayahnya tiba-tiba wafat dan meninggalkan Sara menjadi gadis sebatang kara tanpa harta sepeser pun. Sara yang dahulu diperlakukan dengan istimewa di sekolah, menjadi diabaikan dan diperlakukan buruk oleh orang-orang di sekitarnya. Nona Minchin, sang kepala sekolah, akhirnya menempatkan Sara sebagai pembantu dan mengambil semua fasilitas yang dimilikinya.  Lantas bagaimana cara Sara menghadapi kesulitan dan ketidakadilan yang dialaminya? Akankah Sara dapat bertahan di dunia yang kejam tanpa sang ayah di sisi-nya?'),
(43, 'The School for Clairvoyant', 'C-Klik Media', 'Anala Lashita', 2025, 'school1.jpg', 'Xeanee adalah seorang anak yang terlahir dengan bakat istimewa. la bisa melihat makhluk tak kasat mata. Mimpinya adalah bisa sekolah di tempat yang seluruh siswanya memiliki bakat unik seperti dirinya. Sampai akhirnya, Mamanya mendapatkan selebaran tentang sekolah untuk anak-anak seperti Xeanee. Akankah Xeanee menyukai sekolah tersebut?'),
(44, 'Islamic Heroes', 'Kanak', 'Irsyad Zulfahmi', 2025, 'heroes.jpg', 'Pernahkah kamu membayangkan seorang jenderal yang tak pernah kalah dalam berperang? Atau seorang panglima perang yang memimpin ribuan pasukannya sejak ia masih remaja?  Dalam buku ini, kamu akan diajak menjelajah waktu—menemui para pahlawan Islam yang namanya terukir dalam sejarah dunia. Antara lain, Khalid sang Pedang Allah, Shalahuddin sang Penakluk Yerusalem, dan Muhammad Al-Fatih sang Pemimpi Konstantinopel.  Mereka bukan hanya hebat dalam berpedang, tapi juga hebat menjaga iman, sabar dalam ujian, cerdas dalam menentukan strategi, serta bijak dalam mengambil keputusan.  Yuk, kita berkenalan dengan sepuluh tokoh hebat yang bakal bikin kamu jadi pemberani dan baik hati!'),
(46, 'Al-Quran dan Alam Semesta', 'Kanak', 'Ina Inong', 2025, 'semesta.jpg', 'Al-Quran dan Alam Semesta : Tata Surya, Bumi, Manusia, Hewan, Tumbuhan\r\n\r\nKenapa langit tidak jatuh? Siapa yang menciptakan gunung? Bagaimana manusia pertama ada? Apakah hewan dan tumbuhan juga bertasbih?\r\n\r\nBuku ini mengajak anak-anak untuk menjelajahi alam semesta, bumi, manusia, hewan, dan tumbuhan melalui pertanyaan-pertanyaan sederhana yang sering mereka tanyakan, dan menjawabnya langsung dari sumber yang penuh hikma, yaitu Al-Qur’an.\r\n\r\nDisusun dengan gaya tanya jawab yang ramah anak, buku ini menghubungkan ilmu pengetahuan dengan keimanan, membantu anak-anak mengenal ciptaan Allah dengan cara yang menyenangkan dan mudah dipahami.\r\n\r\nBuku ini dapat dibaca sendiri atau bersama orang tua dan guru. Mari tumbuhkan rasa ingin tahu dan cinta kepada Al-Qur’an sejak dini.');

-- --------------------------------------------------------

--
-- Struktur dari tabel `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id_peminjaman` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_buku` int(11) DEFAULT NULL,
  `tgl_peminjaman` date DEFAULT NULL,
  `max_tgl_kembali` date DEFAULT NULL,
  `tgl_kembali` date DEFAULT NULL,
  `status` varchar(255) DEFAULT 'Request'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `peminjaman`
--

INSERT INTO `peminjaman` (`id_peminjaman`, `id_user`, `id_buku`, `tgl_peminjaman`, `max_tgl_kembali`, `tgl_kembali`, `status`) VALUES
(6, 13, 43, '2025-11-21', '2025-11-28', '2025-11-21', 'Dikembalikan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemustaka`
--

CREATE TABLE `pemustaka` (
  `id_user` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `no_telepon` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pemustaka`
--

INSERT INTO `pemustaka` (`id_user`, `username`, `password`, `nama_lengkap`, `email`, `no_telepon`) VALUES
(6, 'fathan13', '$2y$10$dEbYEvo8PoFeSw//YLDKGOzb0fm8p5.Jm2XpV9jQtzGl6RA8Oaiau', 'Fathan Ruhul Alam', 'fathan@gmail.com', '085798808596'),
(7, 'Ariel', '$2y$10$iwAqjc0nox3XcmIsm04IX.h/JBhc1DVwSb.mTlPOpWbvTh8fr.Xie', 'Ariel Ibnu Firmansyah MS', 'arielibnufirmansyahms@gmail.com', '085856294992'),
(8, 'Umam', '$2y$10$X55nwQVUAxHDl3dNxrMDs.DRJlvxKzV7POPTygzY/KH6yYdBLRB72', 'Umam Aziz', 'umam@gmail.com', '0812345678'),
(13, 'sandikala13', '$2y$10$.qGPGUiS1gBEcS9CyumHGOKgudZJoDW8x9T3r2L5u5iFPdLns5uOe', 'Sandikala', 'sandikala@gmail.com', '08765432178');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indeks untuk tabel `koleksi_buku`
--
ALTER TABLE `koleksi_buku`
  ADD PRIMARY KEY (`id_buku`);

--
-- Indeks untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id_peminjaman`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_buku` (`id_buku`);

--
-- Indeks untuk tabel `pemustaka`
--
ALTER TABLE `pemustaka`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `koleksi_buku`
--
ALTER TABLE `koleksi_buku`
  MODIFY `id_buku` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id_peminjaman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `pemustaka`
--
ALTER TABLE `pemustaka`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`id_buku`) REFERENCES `koleksi_buku` (`id_buku`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `pemustaka` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
