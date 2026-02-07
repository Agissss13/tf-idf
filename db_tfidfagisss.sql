-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 23 Jan 2026 pada 03.34
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
-- Database: `db_tfidf`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) DEFAULT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `isi_dokumen` text DEFAULT NULL,
  `tanggal_upload` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `documents`
--

INSERT INTO `documents` (`id`, `judul`, `kategori`, `isi_dokumen`, `tanggal_upload`) VALUES
(1, 'Mahasiswa ITB Juara Lomba', NULL, 'Mahasiswa Institut Teknologi Bandung (ITB) telah mengukir prestasi baru di ajang bergengsi dunia yakni NASA International Space Apps Challenge 2025. Acara tersebut digelar oleh perusahaan antariksa terbesar asal Amerika Serikat yakni National Aeronautics and Space Administration (NASA).\r\n\r\nEmpat mahasiswa ini berasal dari Sekolah Teknik Elektro dan Informatika (STEI) ITB. Mereka berhasil meraih tiga kategori penghargaan sekaligus.\r\n\r\nMereka membawa pulang Best Pitch NASA International Space Apps Challenge 2025, Global Nominee & Local Winner, dan Best Use of Data Nominee\r\n\r\nAdapun ketiga mahasiswa yang ikut serta dalam ajang ini yakni Thalita Zahra Sutejo (Sistem dan Teknologi Informasi 2022), Attara Majesta Ayub (Teknik Informatika 2022), Jason Fernando (Teknik Informatika 2022), dan Daryl John Sayangbati (Teknik Mesin 2022).\r\n\r\nPlatform yang dikembangkan Thalita dan kawan-kawan adalah AIM-X (Asteroid Impact Simulation and Mitigation eXperience), sebuah platform simulasi interaktif yang memungkinkan pengguna memprediksi dan menganalisis potensi dampak tumbukan asteroid menggunakan data riil dari NASA.\r\n\r\n\"Melalui AIM-X, pengguna dapat mensimulasikan berbagai skenario tumbukan berdasarkan parameter fisik asteroid seperti kecepatan, sudut tumbukan, dan jarak orbit minimum,\" kata Thalita dikutip dari laman ITB, Minggu (9/11/2025).\r\n\r\nSelain itu, inovasi mahasiswa ITB ini mampu menghitung efek lanjutan seperti menghitung ukuran kawah, gelombang kejut, tsunami, dan aktivitas seismik.\r\n\r\nDalam proses pengembangannya, tim membagi tugas mulai dari analisis data dan perhitungan fisika, desain antarmuka dan visualisasi 3D, dan integrasi database NASA ke dalam platform web.\r\n\r\nThalita mengatakan, proses pengembangan platform tersebut memiliki tantangan tersendiri seperti waktu yang terbatas. Selain itu, tantangan lainnya adalah menyelaraskan ketelitian ilmiah dengan visual yang mudah dipahami.\r\n\r\nMeski demikian, usaha mereka berbuah manis. Selain penghargaan, mereka meraih pengalaman berharga di ajang internasional tersebut.\r\n\r\n\"Selain itu, kami juga belajar mengelola proyek dengan efisien dalam waktu singkat dan membuat keputusan berbasis data secara cepat,\" ungkapnya.\r\n\r\nBagi Thalita dan tim, kompetisi ini bukan hanya tentang menang, tetapi juga pembuktian bahwa mahasiswa Indonesia mampu berkontribusi dalam isu global.\r\n\r\n\"Kami belajar pentingnya kolaborasi lintas disiplin dan bagaimana berpikir secara global dalam menghadapi isu ilmiah yang kompleks,\" katanya.\r\n\r\nIa berharap capaian ini menjadi inspirasi bagi mahasiswa lain untuk berani terlibat dalam kompetisi internasional. Ia yakin mahasiswa Indonesia juga bisa unggul dalam kompetisi astronomi.\r\n\r\n\"Kompetisi seperti NASA Space Apps Challenge membuktikan bahwa dengan semangat belajar, kerja sama tim, dan keberanian mencoba hal baru, mahasiswa Indonesia mampu bersaing di tingkat dunia. Kami ingin menunjukkan bahwa kemampuan yang dipelajari di kampus dapat diterapkan secara nyata untuk memecahkan masalah dunia,\" pungkasnya\r\n', '2026-01-23 02:28:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kalimat`
--

CREATE TABLE `kalimat` (
  `id` int(11) NOT NULL,
  `doc_id` int(11) DEFAULT NULL,
  `kalimat_asli` text DEFAULT NULL,
  `kalimat_clean` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kalimat`
--

INSERT INTO `kalimat` (`id`, `doc_id`, `kalimat_asli`, `kalimat_clean`) VALUES
(1, 1, 'Mahasiswa Institut Teknologi Bandung (ITB) telah mengukir prestasi baru di ajang bergengsi dunia yakni NASA International Space Apps Challenge 2025.', 'mahasiswa institut teknologi bandung itb ukir prestasi baru ajang gengsi dunia nasa international space apps challenge'),
(2, 1, 'Acara tersebut digelar oleh perusahaan antariksa terbesar asal Amerika Serikat yakni National Aeronautics and Space Administration (NASA).', 'acara sebut gelar usaha antariksa besar asal amerika serikat national aeronautics and space administration nasa'),
(3, 1, 'Empat mahasiswa ini berasal dari Sekolah Teknik Elektro dan Informatika (STEI) ITB.', 'empat mahasiswa asal sekolah teknik elektro informatika stei itb'),
(4, 1, 'Mereka berhasil meraih tiga kategori penghargaan sekaligus.', 'hasil raih tiga kategori harga sekaligus'),
(5, 1, 'Mereka membawa pulang Best Pitch NASA International Space Apps Challenge 2025, Global Nominee & Local Winner, dan Best Use of Data Nominee\r\n\r\nAdapun ketiga mahasiswa yang ikut serta dalam ajang ini yakni Thalita Zahra Sutejo (Sistem dan Teknologi Informasi 2022), Attara Majesta Ayub (Teknik Informatika 2022), Jason Fernando (Teknik Informatika 2022), dan Daryl John Sayangbati (Teknik Mesin 2022).', 'bawa pulang best pitch nasa international space apps challenge global nominee local winner best use of data nominee adapun tiga mahasiswa ikut ajang thalita zahra sutejo sistem teknologi informasi attara majesta ayub teknik informatika jason fernando teknik informatika daryl john sayangbati teknik mesin'),
(6, 1, 'Platform yang dikembangkan Thalita dan kawan-kawan adalah AIM-X (Asteroid Impact Simulation and Mitigation eXperience), sebuah platform simulasi interaktif yang memungkinkan pengguna memprediksi dan menganalisis potensi dampak tumbukan asteroid menggunakan data riil dari NASA.', 'platform kembang thalita kawan kawan aim x asteroid impact simulation and mitigation experience buah platform simulasi interaktif mungkin guna prediksi analis potensi dampak tumbu asteroid guna data riil nasa'),
(7, 1, '\"Melalui AIM-X, pengguna dapat mensimulasikan berbagai skenario tumbukan berdasarkan parameter fisik asteroid seperti kecepatan, sudut tumbukan, dan jarak orbit minimum,\" kata Thalita dikutip dari laman ITB, Minggu (9/11/2025).', 'lalu aim x guna simulasi bagai skenario tumbu dasar parameter fisik asteroid cepat sudut tumbu jarak orbit minimum kata thalita kutip laman itb minggu'),
(8, 1, 'Selain itu, inovasi mahasiswa ITB ini mampu menghitung efek lanjutan seperti menghitung ukuran kawah, gelombang kejut, tsunami, dan aktivitas seismik.', 'inovasi mahasiswa itb mampu hitung efek lanjut hitung ukur kawah gelombang kejut tsunami aktivitas seismik'),
(9, 1, 'Dalam proses pengembangannya, tim membagi tugas mulai dari analisis data dan perhitungan fisika, desain antarmuka dan visualisasi 3D, dan integrasi database NASA ke dalam platform web.', 'proses kembang tim bagi tugas mulai analisis data hitung fisika desain antarmuka visualisasi d integrasi database nasa platform web'),
(10, 1, 'Thalita mengatakan, proses pengembangan platform tersebut memiliki tantangan tersendiri seperti waktu yang terbatas.', 'thalita kata proses kembang platform sebut milik tantang sendiri waktu batas'),
(11, 1, 'Selain itu, tantangan lainnya adalah menyelaraskan ketelitian ilmiah dengan visual yang mudah dipahami.', 'tantang lain selaras teliti ilmiah visual mudah paham'),
(12, 1, 'Meski demikian, usaha mereka berbuah manis.', 'meski usaha buah manis'),
(13, 1, 'Selain penghargaan, mereka meraih pengalaman berharga di ajang internasional tersebut.', 'harga raih alam harga ajang internasional sebut'),
(14, 1, '\"Selain itu, kami juga belajar mengelola proyek dengan efisien dalam waktu singkat dan membuat keputusan berbasis data secara cepat,\" ungkapnya.', 'ajar kelola proyek efisien waktu singkat buat putus bas data cepat ungkap'),
(15, 1, 'Bagi Thalita dan tim, kompetisi ini bukan hanya tentang menang, tetapi juga pembuktian bahwa mahasiswa Indonesia mampu berkontribusi dalam isu global.', 'thalita tim kompetisi bukan menang bukti mahasiswa indonesia mampu kontribusi isu global'),
(16, 1, '\"Kami belajar pentingnya kolaborasi lintas disiplin dan bagaimana berpikir secara global dalam menghadapi isu ilmiah yang kompleks,\" katanya.', 'ajar penting kolaborasi lintas disiplin bagaimana pikir global hadap isu ilmiah kompleks kata'),
(17, 1, 'Ia berharap capaian ini menjadi inspirasi bagi mahasiswa lain untuk berani terlibat dalam kompetisi internasional.', 'harap capai jadi inspirasi mahasiswa berani libat kompetisi internasional'),
(18, 1, 'Ia yakin mahasiswa Indonesia juga bisa unggul dalam kompetisi astronomi.', 'yakin mahasiswa indonesia unggul kompetisi astronomi'),
(19, 1, '\"Kompetisi seperti NASA Space Apps Challenge membuktikan bahwa dengan semangat belajar, kerja sama tim, dan keberanian mencoba hal baru, mahasiswa Indonesia mampu bersaing di tingkat dunia.', 'kompetisi nasa space apps challenge bukti semangat ajar kerja sama tim berani coba baru mahasiswa indonesia mampu saing tingkat dunia'),
(20, 1, 'Kami ingin menunjukkan bahwa kemampuan yang dipelajari di kampus dapat diterapkan secara nyata untuk memecahkan masalah dunia,\" pungkasnya', 'tunjuk mampu ajar kampus terap nyata pecah masalah dunia pungkas');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kalimat`
--
ALTER TABLE `kalimat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doc_id` (`doc_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `kalimat`
--
ALTER TABLE `kalimat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `kalimat`
--
ALTER TABLE `kalimat`
  ADD CONSTRAINT `kalimat_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
