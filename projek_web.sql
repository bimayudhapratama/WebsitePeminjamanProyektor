-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 27, 2026 at 12:24 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `projek_web`
--

-- --------------------------------------------------------

--
-- Table structure for table `peminjam`
--

CREATE TABLE `peminjam` (
  `id` int NOT NULL,
  `nik` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `instansi` varchar(150) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `foto_ktp` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `peminjam`
--

INSERT INTO `peminjam` (`id`, `nik`, `nama`, `instansi`, `created_at`, `foto_ktp`) VALUES
(1, '320608580949234', 'Mulyadi', 'Jakarta', '2026-01-27 02:59:31', 'ktp_69783bca18fc1.png'),
(2, '327498234', 'Abduurohim', 'Jawa Timur', '2026-01-27 03:25:15', 'ktp_1769484315_735.png'),
(3, '3206998745628765', 'Jennie', 'Mundu', '2026-01-27 04:02:21', 'ktp_1769486541_978.png');

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman_proyektor`
--

CREATE TABLE `peminjaman_proyektor` (
  `id` int NOT NULL,
  `id_peminjam` int DEFAULT NULL,
  `kode_proyektor` int NOT NULL,
  `nama_peminjam` varchar(100) DEFAULT NULL,
  `instansi` varchar(100) DEFAULT NULL,
  `tanggal_pinjam` date DEFAULT NULL,
  `tanggal_kembali` date DEFAULT NULL,
  `lama_pinjam` int DEFAULT '0',
  `merk_proyektor` varchar(100) DEFAULT NULL,
  `status_peminjaman` enum('Dipinjam','Dikembalikan') DEFAULT 'Dipinjam'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `peminjaman_proyektor`
--

INSERT INTO `peminjaman_proyektor` (`id`, `id_peminjam`, `kode_proyektor`, `nama_peminjam`, `instansi`, `tanggal_pinjam`, `tanggal_kembali`, `lama_pinjam`, `merk_proyektor`, `status_peminjaman`) VALUES
(21, 2, 11, NULL, 'Jawa Timur', '2026-01-27', '2026-01-27', 2, 'BenQ MX532', 'Dikembalikan'),
(22, 3, 12, NULL, 'Mundu', '2026-01-27', '2026-01-27', 1, 'ViewSonic Projector M2 Smart LED', 'Dikembalikan'),
(23, 3, 14, NULL, 'Mundu', '2026-01-27', '2026-01-28', 1, 'Epson EB-1485Fi', 'Dikembalikan'),
(24, 2, 14, NULL, 'Jawa Timur', '2026-01-27', '2026-01-30', 1, 'Epson EB-1485Fi', 'Dikembalikan');

-- --------------------------------------------------------

--
-- Table structure for table `stokproyektor`
--

CREATE TABLE `stokproyektor` (
  `id` int NOT NULL,
  `merk` varchar(50) NOT NULL,
  `stok` int NOT NULL,
  `foto` varchar(255) NOT NULL,
  `harga` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `stokproyektor`
--

INSERT INTO `stokproyektor` (`id`, `merk`, `stok`, `foto`, `harga`) VALUES
(11, 'BenQ MX532', 1, 'PS_20260120151414_6819669d.jpg', 60000.00),
(12, 'ViewSonic Projector M2 Smart LED', 70, 'PS_20260126213028_7411dc9d.jpg', 50000.00),
(13, 'Ezzrale Projector Wireless EZ320', 8, 'PS_20260126213055_be5720e4.jpg', 100000.00),
(14, 'Epson EB-1485Fi', 66, 'PS_20260126213138_d20d0d00.jpg', 73000.00),
(15, 'Lenovo Yoga 7000 Smart Projector', 48, 'PS_20260126213802_89f18b0b.jpg', 20000.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','penyewa','mahasiswa','dosen','kaprodi','dekan') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$3IiyXZ2flo7IIIoCcLN8euNL2NN0/9qrSxW5.mlflvKChqntNLi0.', 'admin', '2025-12-23 02:06:59'),
(5, 'Bheem', '$2y$10$UdVy63DfXSWsWTbiqDKepeG1i/JpGXejTSsLp672qbVDNjgzCtd9e', 'penyewa', '2026-01-20 01:52:18'),
(8, 'fatan', '$2y$10$pZf8wHBg9l/KYoCStKJn9.izSsOFn9NJHxE.xybENLFEMvt26cRvi', 'penyewa', '2026-01-26 14:41:32'),
(9, 'siti', '$2y$10$AjMKHR50nStRj18fh2Um5ucVli3vvWBno.wGmUWwOVOyZ3OSjEbMi', 'penyewa', '2026-01-26 14:41:42'),
(10, 'bangbung', '$2y$10$rD4hAezhys6JQkwyCouEb.oW8PMfCVAkt52n1RG8j4oZ7Rd8XAwV.', 'penyewa', '2026-01-26 14:42:27'),
(11, 'bh2000an', '$2y$10$HSzVQqhVFPj1XAp89FDHB.oH95T77ftye3lueasfC7GbGAyjnyCDO', 'penyewa', '2026-01-26 14:43:42'),
(12, 'tangtingtung', '$2y$10$SqJSCo8.lkPpEteqme6wb.mHWpB9abSOwNMTebws5LrA4cKyfxj3O', 'penyewa', '2026-01-26 14:51:40'),
(14, 'imam bonjol', '$2y$10$pHulKaLa9z2gB/L7mME5xuQfKKU2fIT6W/sp.MSLaq.nYg/L1xp1i', 'penyewa', '2026-01-26 14:53:16'),
(15, 'cut nyakdien', '$2y$10$8eEffo8FN54Z9kjhsDcANOdRkc9VQSoXw8ADIOB7HA6/3F8mtOjti', 'penyewa', '2026-01-26 14:54:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `peminjam`
--
ALTER TABLE `peminjam`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nik` (`nik`);

--
-- Indexes for table `peminjaman_proyektor`
--
ALTER TABLE `peminjaman_proyektor`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stokproyektor`
--
ALTER TABLE `stokproyektor`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `peminjam`
--
ALTER TABLE `peminjam`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `peminjaman_proyektor`
--
ALTER TABLE `peminjaman_proyektor`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `stokproyektor`
--
ALTER TABLE `stokproyektor`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
