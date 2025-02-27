-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 27 Feb 2025 pada 10.22
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
-- Database: `moju`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `artikel`
--

CREATE TABLE `artikel` (
  `id_artikel` int(11) NOT NULL,
  `judul` longtext NOT NULL,
  `content` longtext DEFAULT NULL,
  `link` longtext DEFAULT NULL,
  `jenis` varchar(100) DEFAULT NULL,
  `asset` varchar(255) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `create_at` datetime DEFAULT current_timestamp(),
  `update_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `comment`
--

CREATE TABLE `comment` (
  `id_comment` int(11) NOT NULL,
  `id_diskusi` int(11) DEFAULT NULL,
  `content_comment` longtext DEFAULT NULL,
  `parent_diskusi` int(11) DEFAULT NULL,
  `parent_komen` int(11) DEFAULT NULL,
  `parent_reply` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `diskusi`
--

CREATE TABLE `diskusi` (
  `id_diskusi` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `mood` varchar(150) DEFAULT NULL,
  `id_comment` int(11) DEFAULT NULL,
  `create_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `diskusi`
--

INSERT INTO `diskusi` (`id_diskusi`, `id_user`, `content`, `mood`, `id_comment`, `create_at`) VALUES
(1, 1, 'dfdf', NULL, NULL, '2024-12-12 22:08:22');

-- --------------------------------------------------------

--
-- Struktur dari tabel `islogin`
--

CREATE TABLE `islogin` (
  `id_islogin` int(11) NOT NULL,
  `login_at` datetime DEFAULT current_timestamp(),
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `islogin`
--

INSERT INTO `islogin` (`id_islogin`, `login_at`, `id_user`) VALUES
(1, '2025-02-27 16:20:49', 1),
(2, '2024-12-12 22:09:04', 3),
(3, '2024-11-24 01:06:52', 7),
(4, '2024-11-24 16:04:05', 8),
(5, '2025-02-27 14:19:09', 9);

-- --------------------------------------------------------

--
-- Struktur dari tabel `journal`
--

CREATE TABLE `journal` (
  `id_journal` char(36) NOT NULL DEFAULT uuid(),
  `id_user` int(11) DEFAULT NULL,
  `create_at` datetime DEFAULT current_timestamp(),
  `update_at` datetime DEFAULT NULL,
  `judul` varchar(255) NOT NULL,
  `isi` longtext DEFAULT NULL,
  `mood` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `journal`
--

INSERT INTO `journal` (`id_journal`, `id_user`, `create_at`, `update_at`, `judul`, `isi`, `mood`) VALUES
('0859dccc-e59ef370-9fa32efa-fd4e51c9', 1, '2024-11-24 01:38:14', NULL, '11111', '1111', 'default mood'),
('a35b9a39-ec8043d7-171ee055-1b20b524', 1, '2024-11-24 01:38:30', '2024-11-24 01:38:54', 'kkjkj', '1111121212', 'default mood'),
('bf15e629-eedc264e-acdf09a2-18eddcf4', 1, '2024-12-12 22:06:49', NULL, 'Untitled', 'abcde22xqxq556jawa', 'Bingung'),
('e7c4cc6b-58e669dc-1f9c7c7f-27d087ac', 1, '2024-12-12 22:05:56', NULL, 'Untitled', 'hello', 'Bingung');

-- --------------------------------------------------------

--
-- Struktur dari tabel `like_unlike`
--

CREATE TABLE `like_unlike` (
  `id_like_unlike` int(11) NOT NULL,
  `like` bigint(20) DEFAULT 0,
  `unlike` bigint(20) DEFAULT 0,
  `id_user` int(11) DEFAULT NULL,
  `id_diskusi` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `like_unlike`
--

INSERT INTO `like_unlike` (`id_like_unlike`, `like`, `unlike`, `id_user`, `id_diskusi`, `created_at`) VALUES
(1, 1, 0, 1, 1, '2024-12-12 15:08:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `foto_profile` varchar(255) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `token` varchar(64) NOT NULL,
  `expires_token` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `nama`, `email`, `tanggal_lahir`, `foto_profile`, `username`, `role`, `password`, `created_at`, `token`, `expires_token`) VALUES
(1, 'user111', 'user@gmail.com', '2024-11-30', 'Assets/img/foto-profile/674222538d1b2_Kejarkom_Firewall_Cisco_KEL-6.pdf', 'makanan', 'user', '$2y$10$bEmQmxeq6v3x00EHcOL58u84dEoiKNJnCwT7SKm7V.XybIUozqbA.', '2024-11-23 07:10:07', '', NULL),
(3, 'admin', 'admin@gmail.com', '2024-11-23', NULL, NULL, 'admin', '$2y$10$p3CPcAxEe6QIQQUUVUjCReoQVFO.kclNrmf5jNvQBbOpol8TfBbyu', '2024-11-23 07:22:30', '', NULL),
(4, 'sadsad', 'user1212@gmail.com', '2024-10-30', NULL, 'Anonymous#4812', 'user', '$2y$10$ziPsIF1X/STj/oVzcLG1T.ttaONwLNA7X4rhWLCGNdJqauIC4g8Bu', '2024-11-23 09:16:54', '', NULL),
(5, 'makanan', 'use12r@gmail.com', '2024-11-05', NULL, 'Anonymous#6153', 'user', '$2y$10$rBEBMa4eqGYNYU9ZKc806uMsp.er0f76fdD4r9VdRGBv9/9Zi00pi', '2024-11-23 09:17:55', '', NULL),
(6, '1212', 'userdf@gmail.com', '2024-11-05', 'Assets/img/foto-profile/Default.png', 'Anonymous#1234', 'user', '$2y$10$SwmNxLroQNu6157wgOF07u41EwJ8CvbXujr7WF8.OctmpV/rQU5lG', '2024-11-23 09:26:18', '', NULL),
(7, 'user123', 'user123@gmail.com', '2024-10-30', 'Assets/img/foto-profile/Default.png', 'Anonymous#7395', 'user', '$2y$10$L8LfipWEubeyIKUEdPgvJu34zAtl8l5esEferkVDpDZeNFaFVwmdu', '2024-11-23 16:40:27', '', NULL),
(8, 'fahmi', 'fahmishark34@gmail.com', '2024-11-24', 'Assets/img/foto-profile/Default.png', 'Anonymous#1017', 'user', '$2y$10$j5MOgTFU80uya0lUqMFlfOJB4Qnos4tYK3MMhxK5NLDyQH2DTeEY6', '2024-11-24 07:44:24', '8b14aef9d00dbe51a5436d21bca8787ac5da19ea4632b3c0809e709520580c30', '2024-11-24 18:56:31'),
(9, 'maknan2', 'user43@gmail.com', '2025-02-27', 'Assets/img/foto-profile/Default.png', 'Anonymous#7321', 'user', '$2y$10$ioNMeCKKNlliXtV53dvInuOx5lvo0TUwd8IEuxHIRJEm/x/Qdo3yW', '2025-02-27 07:19:03', '', NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `artikel`
--
ALTER TABLE `artikel`
  ADD PRIMARY KEY (`id_artikel`),
  ADD KEY `idx_artikel_user` (`id_user`);

--
-- Indeks untuk tabel `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id_comment`),
  ADD KEY `idx_comment_diskusi` (`id_diskusi`);

--
-- Indeks untuk tabel `diskusi`
--
ALTER TABLE `diskusi`
  ADD PRIMARY KEY (`id_diskusi`),
  ADD KEY `idx_diskusi_user` (`id_user`),
  ADD KEY `fk_diskusi_comment` (`id_comment`);

--
-- Indeks untuk tabel `islogin`
--
ALTER TABLE `islogin`
  ADD PRIMARY KEY (`id_islogin`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `journal`
--
ALTER TABLE `journal`
  ADD PRIMARY KEY (`id_journal`),
  ADD KEY `idx_journal_user` (`id_user`);

--
-- Indeks untuk tabel `like_unlike`
--
ALTER TABLE `like_unlike`
  ADD PRIMARY KEY (`id_like_unlike`),
  ADD KEY `id_diskusi` (`id_diskusi`),
  ADD KEY `idx_like_unlike_user_diskusi` (`id_user`,`id_diskusi`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `username_2` (`username`),
  ADD KEY `idx_user_email` (`email`),
  ADD KEY `idx_user_username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `artikel`
--
ALTER TABLE `artikel`
  MODIFY `id_artikel` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `comment`
--
ALTER TABLE `comment`
  MODIFY `id_comment` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `diskusi`
--
ALTER TABLE `diskusi`
  MODIFY `id_diskusi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `islogin`
--
ALTER TABLE `islogin`
  MODIFY `id_islogin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `like_unlike`
--
ALTER TABLE `like_unlike`
  MODIFY `id_like_unlike` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `artikel`
--
ALTER TABLE `artikel`
  ADD CONSTRAINT `artikel_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`id_diskusi`) REFERENCES `diskusi` (`id_diskusi`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `diskusi`
--
ALTER TABLE `diskusi`
  ADD CONSTRAINT `diskusi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_diskusi_comment` FOREIGN KEY (`id_comment`) REFERENCES `comment` (`id_comment`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `islogin`
--
ALTER TABLE `islogin`
  ADD CONSTRAINT `islogin_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `journal`
--
ALTER TABLE `journal`
  ADD CONSTRAINT `journal_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `like_unlike`
--
ALTER TABLE `like_unlike`
  ADD CONSTRAINT `like_unlike_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `like_unlike_ibfk_2` FOREIGN KEY (`id_diskusi`) REFERENCES `diskusi` (`id_diskusi`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
