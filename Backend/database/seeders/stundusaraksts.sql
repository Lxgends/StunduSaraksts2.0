-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 26, 2025 at 04:53 PM
-- Server version: 5.7.24
-- PHP Version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `stundusaraksts`
--

-- --------------------------------------------------------

--
-- Table structure for table `datums`
--

CREATE TABLE `datums` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `PirmaisDatums` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `PedejaisDatums` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `datums`
--

INSERT INTO `datums` (`id`, `PirmaisDatums`, `PedejaisDatums`, `created_at`, `updated_at`) VALUES
(1, '2025-02-24', '2025-02-28', '2025-02-24 09:00:30', '2025-02-25 16:44:32'),
(2, '2025-03-03', '2025-03-07', '2025-02-25 16:44:08', '2025-02-25 16:44:08'),
(3, '2025-03-10', '2025-03-14', '2025-02-25 20:23:33', '2025-02-25 20:23:33');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ieplanot_stundu`
--

CREATE TABLE `ieplanot_stundu` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `skaitlis` int(11) NOT NULL,
  `kurssID` bigint(20) UNSIGNED NOT NULL,
  `laiksID` bigint(20) UNSIGNED NOT NULL,
  `datumsID` bigint(20) UNSIGNED NOT NULL,
  `stundaID` bigint(20) UNSIGNED NOT NULL,
  `pasniedzejsID` bigint(20) UNSIGNED NOT NULL,
  `kabinetaID` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ieplanot_stundu`
--

INSERT INTO `ieplanot_stundu` (`id`, `skaitlis`, `kurssID`, `laiksID`, `datumsID`, `stundaID`, `pasniedzejsID`, `kabinetaID`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 1, 1, 1, '2025-02-25 12:57:53', '2025-02-25 13:04:37'),
(2, 1, 2, 1, 1, 2, 2, 1, '2025-02-25 13:15:55', '2025-02-25 13:15:55'),
(3, 2, 1, 1, 1, 1, 3, 5, '2025-02-26 12:26:43', '2025-02-26 12:26:43'),
(4, 2, 1, 2, 1, 1, 3, 7, '2025-02-26 12:27:19', '2025-02-26 12:27:19'),
(5, 5, 1, 6, 1, 2, 2, 1, '2025-02-26 14:09:30', '2025-02-26 14:09:30'),
(6, 5, 1, 7, 1, 2, 2, 1, '2025-02-26 14:09:56', '2025-02-26 14:09:56'),
(7, 1, 1, 2, 1, 2, 3, 5, '2025-02-26 15:23:52', '2025-02-26 15:23:52'),
(8, 1, 1, 3, 1, 1, 1, 1, '2025-02-26 15:24:10', '2025-02-26 15:24:10'),
(9, 3, 1, 3, 1, 1, 1, 1, '2025-02-26 15:26:58', '2025-02-26 15:26:58'),
(10, 3, 1, 1, 1, 1, 1, 1, '2025-02-26 16:42:22', '2025-02-26 16:42:22');

-- --------------------------------------------------------

--
-- Table structure for table `kabinets`
--

CREATE TABLE `kabinets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vieta` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `skaitlis` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kabinets`
--

INSERT INTO `kabinets` (`id`, `vieta`, `skaitlis`, `created_at`, `updated_at`) VALUES
(1, 'Cēsis', '203', '2025-02-24 08:29:44', '2025-02-24 08:29:44'),
(2, 'Cēsis', '301', '2025-02-24 08:35:18', '2025-02-24 08:35:18'),
(3, 'Cēsis', '309', '2025-02-24 08:35:24', '2025-02-24 08:35:24'),
(4, 'Priekuļi', '1', '2025-02-24 08:41:41', '2025-02-24 08:41:41'),
(5, 'Cēsis', '201', '2025-02-25 12:49:26', '2025-02-25 12:49:26'),
(6, 'Cēsis', 'Sporta Zāle', '2025-02-25 12:52:29', '2025-02-25 12:52:29'),
(7, 'Cēsis', '110', '2025-02-25 19:53:39', '2025-02-25 19:53:39');

-- --------------------------------------------------------

--
-- Table structure for table `kurss`
--

CREATE TABLE `kurss` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `Nosaukums` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kurss`
--

INSERT INTO `kurss` (`id`, `Nosaukums`, `created_at`, `updated_at`) VALUES
(1, 'IPb21', '2025-02-24 07:09:37', '2025-02-24 07:09:37'),
(2, 'KM21', '2025-02-24 07:15:19', '2025-02-24 07:15:19'),
(3, 'IPa21', '2025-02-24 07:15:30', '2025-02-24 07:15:30'),
(4, 'DA21', '2025-02-25 19:54:40', '2025-02-25 19:54:40'),
(5, 'IP20', '2025-02-25 19:54:48', '2025-02-25 19:54:48');

-- --------------------------------------------------------

--
-- Table structure for table `laiks`
--

CREATE TABLE `laiks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `DienasTips` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sakumalaiks` time NOT NULL,
  `beigulaiks` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `laiks`
--

INSERT INTO `laiks` (`id`, `DienasTips`, `sakumalaiks`, `beigulaiks`, `created_at`, `updated_at`) VALUES
(1, 'normal', '08:30:00', '09:50:00', '2025-02-24 09:15:25', '2025-02-25 21:04:01'),
(2, 'normal', '10:10:00', '11:30:00', '2025-02-25 17:33:53', '2025-02-25 17:33:53'),
(3, 'normal', '12:30:00', '13:50:00', '2025-02-25 19:44:47', '2025-02-25 20:15:19'),
(4, 'normal', '14:00:00', '15:20:00', '2025-02-25 19:45:03', '2025-02-25 20:15:39'),
(5, 'normal', '15:30:00', '16:50:00', '2025-02-25 19:46:39', '2025-02-25 20:15:57'),
(6, 'short', '08:10:00', '09:30:00', '2025-02-25 19:47:10', '2025-02-25 20:16:17'),
(7, 'short', '09:40:00', '11:00:00', '2025-02-25 19:47:11', '2025-02-25 20:16:34'),
(8, 'short', '11:10:00', '12:30:00', '2025-02-25 20:08:50', '2025-02-25 20:16:56'),
(9, 'short', '13:00:00', '14:20:00', '2025-02-25 20:09:27', '2025-02-25 20:17:19'),
(10, 'short', '14:30:00', '15:50:00', '2025-02-25 20:09:41', '2025-02-25 20:17:46');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(7, '2025_02_23_194226_create_table_kurss', 2),
(19, '2024_02_23_194226_create_table_kurss', 3),
(20, '2024_02_24_070854_create_datums', 3),
(21, '2024_02_24_070950_create_laiks', 3),
(22, '2024_02_24_072449_create_kabinets', 3),
(23, '2025_02_23_194403_create_table_pasniedzejs', 3),
(24, '2025_02_23_194837_create_table_stunda', 3),
(25, '2025_02_23_195251_create_nedelas_stundas', 4),
(26, '2025_02_24_172923_create_ieplanot_stundu', 5);

-- --------------------------------------------------------

--
-- Table structure for table `nedelas_stundas`
--

CREATE TABLE `nedelas_stundas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `skaitlis` int(11) NOT NULL,
  `kurssID` bigint(20) UNSIGNED NOT NULL,
  `laiksID` bigint(20) UNSIGNED NOT NULL,
  `datumsID` bigint(20) UNSIGNED NOT NULL,
  `stundaID` bigint(20) UNSIGNED NOT NULL,
  `pasniedzejsID` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pasniedzejs`
--

CREATE TABLE `pasniedzejs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `Vards` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Uzvards` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `KabinetsID` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pasniedzejs`
--

INSERT INTO `pasniedzejs` (`id`, `Vards`, `Uzvards`, `KabinetsID`, `created_at`, `updated_at`) VALUES
(1, 'Jēkabs', 'Krīgerts', 1, '2025-02-24 08:20:45', '2025-02-24 08:31:16'),
(2, 'Andris', 'Lapsiņš', 2, '2025-02-24 08:35:01', '2025-02-24 08:35:32'),
(3, 'Kārlis', 'Braķis', 7, '2025-02-25 19:53:19', '2025-02-26 16:25:06');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stunda`
--

CREATE TABLE `stunda` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `Nosaukums` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stunda`
--

INSERT INTO `stunda` (`id`, `Nosaukums`, `created_at`, `updated_at`) VALUES
(1, 'Matemātika', '2025-02-24 09:49:33', '2025-02-24 09:49:33'),
(2, 'Angļu Valoda', '2025-02-24 15:21:26', '2025-02-24 15:21:26');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Markuss', 'ipb21.m.vimba@vtdt.edu.lv', NULL, '$2y$12$QI1ZkapZpfMr6Hej..O.nOsYYxneKUlJrpUUmVGKyLG3kZS4gEAg6', 'vDVyA4mufvT49daklKp1trYbWum290DuEuQSjietQey8wQYmsx5U73hHqLrK', '2025-02-23 17:39:48', '2025-02-23 17:39:48'),
(2, 'test', 'test@test.test', NULL, '$2y$12$is.lig9Hx3JCL/GkSAqhgOGgCZDRFktgpX6SDyO3GiDRUlbeajCx2', NULL, '2025-02-24 09:56:31', '2025-02-25 20:22:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `datums`
--
ALTER TABLE `datums`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `ieplanot_stundu`
--
ALTER TABLE `ieplanot_stundu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ieplanot_stundu_kurssid_foreign` (`kurssID`),
  ADD KEY `ieplanot_stundu_laiksid_foreign` (`laiksID`),
  ADD KEY `ieplanot_stundu_datumsid_foreign` (`datumsID`),
  ADD KEY `ieplanot_stundu_stundaid_foreign` (`stundaID`),
  ADD KEY `ieplanot_stundu_pasniedzejsid_foreign` (`pasniedzejsID`),
  ADD KEY `ieplanot_stundu_kabinetaid_foreign` (`kabinetaID`);

--
-- Indexes for table `kabinets`
--
ALTER TABLE `kabinets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kurss`
--
ALTER TABLE `kurss`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `laiks`
--
ALTER TABLE `laiks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nedelas_stundas`
--
ALTER TABLE `nedelas_stundas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nedelas_stundas_kurssid_foreign` (`kurssID`),
  ADD KEY `nedelas_stundas_laiksid_foreign` (`laiksID`),
  ADD KEY `nedelas_stundas_datumsid_foreign` (`datumsID`),
  ADD KEY `nedelas_stundas_stundaid_foreign` (`stundaID`),
  ADD KEY `nedelas_stundas_pasniedzejsid_foreign` (`pasniedzejsID`);

--
-- Indexes for table `pasniedzejs`
--
ALTER TABLE `pasniedzejs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pasniedzejs_kabinetsid_foreign` (`KabinetsID`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `stunda`
--
ALTER TABLE `stunda`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `datums`
--
ALTER TABLE `datums`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ieplanot_stundu`
--
ALTER TABLE `ieplanot_stundu`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `kabinets`
--
ALTER TABLE `kabinets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `kurss`
--
ALTER TABLE `kurss`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `laiks`
--
ALTER TABLE `laiks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `nedelas_stundas`
--
ALTER TABLE `nedelas_stundas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pasniedzejs`
--
ALTER TABLE `pasniedzejs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stunda`
--
ALTER TABLE `stunda`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ieplanot_stundu`
--
ALTER TABLE `ieplanot_stundu`
  ADD CONSTRAINT `ieplanot_stundu_datumsid_foreign` FOREIGN KEY (`datumsID`) REFERENCES `datums` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ieplanot_stundu_kabinetaid_foreign` FOREIGN KEY (`kabinetaID`) REFERENCES `kabinets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ieplanot_stundu_kurssid_foreign` FOREIGN KEY (`kurssID`) REFERENCES `kurss` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ieplanot_stundu_laiksid_foreign` FOREIGN KEY (`laiksID`) REFERENCES `laiks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ieplanot_stundu_pasniedzejsid_foreign` FOREIGN KEY (`pasniedzejsID`) REFERENCES `pasniedzejs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ieplanot_stundu_stundaid_foreign` FOREIGN KEY (`stundaID`) REFERENCES `stunda` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `nedelas_stundas`
--
ALTER TABLE `nedelas_stundas`
  ADD CONSTRAINT `nedelas_stundas_datumsid_foreign` FOREIGN KEY (`datumsID`) REFERENCES `datums` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `nedelas_stundas_kurssid_foreign` FOREIGN KEY (`kurssID`) REFERENCES `kurss` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `nedelas_stundas_laiksid_foreign` FOREIGN KEY (`laiksID`) REFERENCES `laiks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `nedelas_stundas_pasniedzejsid_foreign` FOREIGN KEY (`pasniedzejsID`) REFERENCES `pasniedzejs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `nedelas_stundas_stundaid_foreign` FOREIGN KEY (`stundaID`) REFERENCES `stunda` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pasniedzejs`
--
ALTER TABLE `pasniedzejs`
  ADD CONSTRAINT `pasniedzejs_kabinetsid_foreign` FOREIGN KEY (`KabinetsID`) REFERENCES `kabinets` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
