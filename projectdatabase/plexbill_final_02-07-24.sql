-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 02, 2024 at 07:18 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `plexbill_final`
--

-- --------------------------------------------------------

--
-- Table structure for table `accountantlocs`
--

CREATE TABLE `accountantlocs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(255) NOT NULL,
  `location_id` int(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `accountexpenses`
--

CREATE TABLE `accountexpenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `amount` decimal(18,3) NOT NULL,
  `date` date NOT NULL,
  `branch` varchar(255) NOT NULL,
  `user_id` int(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `account_indirect_incomes`
--

CREATE TABLE `account_indirect_incomes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `amount` decimal(18,3) NOT NULL,
  `date` date NOT NULL,
  `branch` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `file` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `credituser_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `is_user` tinyint(1) DEFAULT 0,
  `is_credituser` tinyint(1) DEFAULT 0,
  `ipaddress` varchar(45) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `countryName` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `regionName` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cityName` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`id`, `user_id`, `admin_id`, `credituser_id`, `branch_id`, `is_admin`, `is_user`, `is_credituser`, `ipaddress`, `url`, `message`, `countryName`, `regionName`, `cityName`, `created_at`, `updated_at`) VALUES
(1, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'admin logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-21 04:07:17', '2024-06-21 04:07:17'),
(2, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchcreate', 'admin created new branch named - kannur branch', 'India', 'Kerala', 'Kozhikode', '2024-06-21 04:07:27', '2024-06-21 04:07:27'),
(3, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchcreate', 'admin created new branch named - thalipparamba branch', 'India', 'Kerala', 'Kozhikode', '2024-06-21 04:07:34', '2024-06-21 04:07:34'),
(4, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/listuser', 'admin visited list user page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 04:07:37', '2024-06-21 04:07:37'),
(5, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/usercreate', 'admin created user named user_1', 'India', 'Kerala', 'Kozhikode', '2024-06-21 04:07:53', '2024-06-21 04:07:53'),
(6, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/usercreate', 'admin created user named user_2', 'India', 'Kerala', 'Kozhikode', '2024-06-21 04:08:08', '2024-06-21 04:08:08'),
(7, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/usercreate', 'admin created user named user_3', 'India', 'Kerala', 'Kozhikode', '2024-06-21 04:08:24', '2024-06-21 04:08:24'),
(8, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/listuser', 'admin visited list user page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 04:08:28', '2024-06-21 04:08:28'),
(9, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/addroles', 'admin added roles to user_1', 'India', 'Kerala', 'Kozhikode', '2024-06-21 04:08:40', '2024-06-21 04:08:40'),
(10, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/listuser', 'admin visited list user page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 04:08:41', '2024-06-21 04:08:41'),
(11, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/addroles', 'admin added roles to user_2', 'India', 'Kerala', 'Kozhikode', '2024-06-21 04:08:53', '2024-06-21 04:08:53'),
(12, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/listuser', 'admin visited list user page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 04:08:54', '2024-06-21 04:08:54'),
(13, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/addroles', 'admin added roles to user_3', 'India', 'Kerala', 'Kozhikode', '2024-06-21 04:09:11', '2024-06-21 04:09:11'),
(14, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/listuser', 'admin visited list user page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 04:09:11', '2024-06-21 04:09:11'),
(15, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/adminlogout', 'admin logged out', 'India', 'Kerala', 'Kozhikode', '2024-06-21 04:09:14', '2024-06-21 04:09:14'),
(16, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-21 04:09:19', '2024-06-21 04:09:19'),
(17, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/createcategory', 'user_1 created category stationary', 'India', 'Kerala', 'Kozhikode', '2024-06-21 04:09:36', '2024-06-21 04:09:36'),
(18, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/createcategory', 'user_1 created category card', 'India', 'Kerala', 'Kozhikode', '2024-06-21 04:09:40', '2024-06-21 04:09:40'),
(19, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/createunit', 'user_1 created new unit kg', 'India', 'Kerala', 'Kozhikode', '2024-06-21 04:09:46', '2024-06-21 04:09:46'),
(20, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/createunit', 'user_1 created new unit n', 'India', 'Kerala', 'Kozhikode', '2024-06-21 04:09:54', '2024-06-21 04:09:54'),
(21, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/productdata', 'user_1 added or edited products', 'India', 'Kerala', 'Kozhikode', '2024-06-21 04:10:13', '2024-06-21 04:10:13'),
(22, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/return', 'user_1 visited product return page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 04:19:22', '2024-06-21 04:19:22'),
(23, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 04:57:45', '2024-06-21 04:57:45'),
(24, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/salesorder_submit', 'user_1 done sales order BTSLS1', 'India', 'Kerala', 'Kozhikode', '2024-06-21 04:57:56', '2024-06-21 04:57:56'),
(25, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 04:58:10', '2024-06-21 04:58:10'),
(26, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 05:01:00', '2024-06-21 05:01:00'),
(27, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/userlogout', 'user_1 logged out', 'India', 'Kerala', 'Kozhikode', '2024-06-21 05:11:41', '2024-06-21 05:11:41'),
(28, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'admin logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-21 05:11:45', '2024-06-21 05:11:45'),
(29, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdat/1', 'admin visited kannur branch\'s reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 05:11:52', '2024-06-21 05:11:52'),
(30, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdatstock/1', 'admin visited kannur branch\'s stock report', 'India', 'Kerala', 'Kozhikode', '2024-06-21 05:11:54', '2024-06-21 05:11:54'),
(31, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/stock', 'admin visited stock report page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 05:15:47', '2024-06-21 05:15:47'),
(32, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/productdata', 'user_1 added or edited products', 'India', 'Kerala', 'Kozhikode', '2024-06-21 05:17:23', '2024-06-21 05:17:23'),
(33, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitstock_table', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-21 05:18:16', '2024-06-21 05:18:16'),
(34, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 05:18:21', '2024-06-21 05:18:21'),
(35, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-21 05:23:34', '2024-06-21 05:23:34'),
(36, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/stock', 'admin visited stock report page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 05:23:40', '2024-06-21 05:23:40'),
(37, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdat/1', 'admin visited kannur branch\'s reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 05:24:24', '2024-06-21 05:24:24'),
(38, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdatstock/1', 'admin visited kannur branch\'s stock report', 'India', 'Kerala', 'Kozhikode', '2024-06-21 05:24:26', '2024-06-21 05:24:26'),
(39, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/return', 'user_1 visited product return page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 05:24:35', '2024-06-21 05:24:35'),
(40, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/returnproduct', 'user_1 product returned', 'India', 'Kerala', 'Kozhikode', '2024-06-21 05:26:16', '2024-06-21 05:26:16'),
(41, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/return', 'user_1 visited product return page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 05:26:17', '2024-06-21 05:26:17'),
(42, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdatstock/1', 'admin visited kannur branch\'s stock report', 'India', 'Kerala', 'Kozhikode', '2024-06-21 05:26:20', '2024-06-21 05:26:20'),
(43, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/returnproduct', 'user_1 product returned', 'India', 'Kerala', 'Kozhikode', '2024-06-21 05:27:41', '2024-06-21 05:27:41'),
(44, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/return', 'user_1 visited product return page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 05:27:42', '2024-06-21 05:27:42'),
(45, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdatstock/1', 'admin visited kannur branch\'s stock report', 'India', 'Kerala', 'Kozhikode', '2024-06-21 05:27:44', '2024-06-21 05:27:44'),
(46, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/return', 'user_1 visited product return page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 05:39:06', '2024-06-21 05:39:06'),
(47, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdatstock/1', 'admin visited kannur branch\'s stock report', 'India', 'Kerala', 'Kozhikode', '2024-06-21 05:44:21', '2024-06-21 05:44:21'),
(48, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdatstock/1', 'admin visited kannur branch\'s stock report', 'India', 'Kerala', 'Kozhikode', '2024-06-21 05:47:08', '2024-06-21 05:47:08'),
(49, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdatstock/1', 'admin visited kannur branch\'s stock report', 'India', 'Kerala', 'Kozhikode', '2024-06-21 05:47:09', '2024-06-21 05:47:09'),
(50, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdatstock/1', 'admin visited kannur branch\'s stock report', 'India', 'Kerala', 'Kozhikode', '2024-06-21 05:47:51', '2024-06-21 05:47:51'),
(51, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/return', 'user_1 visited product return page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 05:50:43', '2024-06-21 05:50:43'),
(52, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/return', 'user_1 visited product return page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 05:52:23', '2024-06-21 05:52:23'),
(53, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 06:01:37', '2024-06-21 06:01:37'),
(54, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdatstock/1', 'admin visited kannur branch\'s stock report', 'India', 'Kerala', 'Kozhikode', '2024-06-21 06:05:39', '2024-06-21 06:05:39'),
(55, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdatstock/1', 'admin visited kannur branch\'s stock report', 'India', 'Kerala', 'Kozhikode', '2024-06-21 06:07:01', '2024-06-21 06:07:01'),
(56, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdatstock/1', 'admin visited kannur branch\'s stock report', 'India', 'Kerala', 'Kozhikode', '2024-06-21 06:07:02', '2024-06-21 06:07:02'),
(57, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 07:19:29', '2024-06-21 07:19:29'),
(58, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 07:30:00', '2024-06-21 07:30:00'),
(59, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdatstock/1', 'admin visited kannur branch\'s stock report', 'India', 'Kerala', 'Kozhikode', '2024-06-21 07:33:15', '2024-06-21 07:33:15'),
(60, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdatstock/1', 'admin visited kannur branch\'s stock report', 'India', 'Kerala', 'Kozhikode', '2024-06-21 07:33:16', '2024-06-21 07:33:16'),
(61, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdatstock/1', 'admin visited kannur branch\'s stock report', 'India', 'Kerala', 'Kozhikode', '2024-06-21 07:40:06', '2024-06-21 07:40:06'),
(62, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/stock', 'admin visited stock report page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 07:40:13', '2024-06-21 07:40:13'),
(63, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/stock', 'admin visited stock report page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 07:49:14', '2024-06-21 07:49:14'),
(64, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/listuser', 'admin visited list user page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 07:54:06', '2024-06-21 07:54:06'),
(65, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/userreport/1', 'admin visited user reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 07:54:10', '2024-06-21 07:54:10'),
(66, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/p_and_l_report', 'admin visited P & L report', 'India', 'Kerala', 'Kozhikode', '2024-06-21 07:54:23', '2024-06-21 07:54:23'),
(67, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/p_and_l_report', 'admin visited P & L report', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:02:30', '2024-06-21 08:02:30'),
(68, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/p_and_l_report', 'admin visited P & L report', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:03:41', '2024-06-21 08:03:41'),
(69, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/p_and_l_report', 'admin visited P & L report', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:03:44', '2024-06-21 08:03:44'),
(70, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/stock', 'admin visited stock report page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:03:50', '2024-06-21 08:03:50'),
(71, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/p_and_l_report', 'admin visited P & L report', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:04:31', '2024-06-21 08:04:31'),
(72, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/p_and_l_report', 'admin visited P & L report', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:05:37', '2024-06-21 08:05:37'),
(73, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/p_and_l_report', 'admin visited P & L report', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:05:56', '2024-06-21 08:05:56'),
(74, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/p_and_l_report', 'admin visited P & L report', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:08:16', '2024-06-21 08:08:16'),
(75, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/p_and_l_report', 'admin visited P & L report', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:08:18', '2024-06-21 08:08:18'),
(76, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/p_and_l_report', 'admin visited P & L report', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:11:51', '2024-06-21 08:11:51'),
(77, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:36:40', '2024-06-21 08:36:40'),
(78, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:37:06', '2024-06-21 08:37:06'),
(79, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/return', 'user_1 visited product return page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:37:21', '2024-06-21 08:37:21'),
(80, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/returnproduct', 'user_1 product returned', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:37:32', '2024-06-21 08:37:32'),
(81, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/return', 'user_1 visited product return page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:37:33', '2024-06-21 08:37:33'),
(82, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/userlogout', 'user_1 logged out', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:55:50', '2024-06-21 08:55:50'),
(83, 2, NULL, NULL, 2, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_2 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:55:56', '2024-06-21 08:55:56'),
(84, 2, NULL, NULL, 2, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/createcategory', 'user_2 created category card', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:56:06', '2024-06-21 08:56:06'),
(85, 2, NULL, NULL, 2, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/createunit', 'user_2 created new unit kg', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:56:10', '2024-06-21 08:56:10'),
(86, 2, NULL, NULL, 2, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/productdata', 'user_2 added or edited products', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:56:27', '2024-06-21 08:56:27'),
(87, 2, NULL, NULL, 2, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitstock_table', 'user_2 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:56:48', '2024-06-21 08:56:48'),
(88, 2, NULL, NULL, 2, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_2 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:56:50', '2024-06-21 08:56:50'),
(89, 2, NULL, NULL, 2, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitdata', 'user_2 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:57:05', '2024-06-21 08:57:05'),
(90, 2, NULL, NULL, 2, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/return', 'user_2 visited product return page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:57:32', '2024-06-21 08:57:32'),
(91, 2, NULL, NULL, 2, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/returnproduct', 'user_2 product returned', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:57:39', '2024-06-21 08:57:39'),
(92, 2, NULL, NULL, 2, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/return', 'user_2 visited product return page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:57:40', '2024-06-21 08:57:40'),
(93, 2, NULL, NULL, 2, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/userlogout', 'user_2 logged out', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:57:46', '2024-06-21 08:57:46'),
(94, 3, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_3 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:57:51', '2024-06-21 08:57:51'),
(95, 3, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_3 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:57:55', '2024-06-21 08:57:55'),
(96, 3, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitdata', 'user_3 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:58:03', '2024-06-21 08:58:03'),
(97, 3, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/return', 'user_3 visited product return page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:58:17', '2024-06-21 08:58:17'),
(98, 3, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/returnproduct', 'user_3 product returned', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:58:24', '2024-06-21 08:58:24'),
(99, 3, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/return', 'user_3 visited product return page', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:58:25', '2024-06-21 08:58:25'),
(100, 3, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/userlogout', 'user_3 logged out', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:58:50', '2024-06-21 08:58:50'),
(101, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-21 08:58:55', '2024-06-21 08:58:55'),
(102, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-22 02:17:17', '2024-06-22 02:17:17'),
(103, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 02:33:02', '2024-06-22 02:33:02'),
(104, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'admin logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-22 02:34:50', '2024-06-22 02:34:50'),
(105, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/p_and_l_report', 'admin visited P & L report', 'India', 'Kerala', 'Kozhikode', '2024-06-22 02:39:13', '2024-06-22 02:39:13'),
(106, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/stock', 'admin visited stock report page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 02:39:30', '2024-06-22 02:39:30'),
(107, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/p_and_l_report', 'admin visited P & L report', 'India', 'Kerala', 'Kozhikode', '2024-06-22 02:39:56', '2024-06-22 02:39:56'),
(108, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdat/1', 'admin visited kannur branch\'s reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 02:40:14', '2024-06-22 02:40:14'),
(109, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdatstock/1', 'admin visited kannur branch\'s stock report', 'India', 'Kerala', 'Kozhikode', '2024-06-22 02:40:18', '2024-06-22 02:40:18'),
(110, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdat/1', 'admin visited kannur branch\'s reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 02:40:22', '2024-06-22 02:40:22'),
(111, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdat/2', 'admin visited thalipparamba branch\'s reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 02:40:26', '2024-06-22 02:40:26'),
(112, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdatstock/2', 'admin visited thalipparamba branch\'s stock report', 'India', 'Kerala', 'Kozhikode', '2024-06-22 02:40:29', '2024-06-22 02:40:29'),
(113, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-22 02:53:23', '2024-06-22 02:53:23'),
(114, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 02:53:27', '2024-06-22 02:53:27'),
(115, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 02:53:47', '2024-06-22 02:53:47'),
(116, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 02:54:23', '2024-06-22 02:54:23'),
(117, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 02:55:21', '2024-06-22 02:55:21'),
(118, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/userlogout', 'user_1 logged out', 'India', 'Kerala', 'Kozhikode', '2024-06-22 04:14:40', '2024-06-22 04:14:40'),
(119, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'admin logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-22 04:14:44', '2024-06-22 04:14:44'),
(120, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/listuser', 'admin visited list user page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 04:27:46', '2024-06-22 04:27:46'),
(121, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/userreport/1', 'admin visited user reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 04:27:55', '2024-06-22 04:27:55'),
(122, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdat/1', 'admin visited kannur branch\'s reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 04:28:55', '2024-06-22 04:28:55'),
(123, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdat/1', 'admin visited kannur branch\'s reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 04:30:24', '2024-06-22 04:30:24'),
(124, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdat/1', 'admin visited kannur branch\'s reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 04:30:25', '2024-06-22 04:30:25'),
(125, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdat/1', 'admin visited kannur branch\'s reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 04:30:31', '2024-06-22 04:30:31'),
(126, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdat/1', 'admin visited kannur branch\'s reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 04:31:57', '2024-06-22 04:31:57'),
(127, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdat/1', 'admin visited kannur branch\'s reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 04:31:59', '2024-06-22 04:31:59'),
(128, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdat/1', 'admin visited kannur branch\'s reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 04:32:01', '2024-06-22 04:32:01'),
(129, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdat/1', 'admin visited kannur branch\'s reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 04:32:16', '2024-06-22 04:32:16'),
(130, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdat/1', 'admin visited kannur branch\'s reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 04:36:31', '2024-06-22 04:36:31'),
(131, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdat/1', 'admin visited kannur branch\'s reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 04:36:35', '2024-06-22 04:36:35'),
(132, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdat/1', 'admin visited kannur branch\'s reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 04:36:37', '2024-06-22 04:36:37'),
(133, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdat/1', 'admin visited kannur branch\'s reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 04:36:39', '2024-06-22 04:36:39'),
(134, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdatreturn/1', 'admin visited kannur branch\'s sales return report', 'India', 'Kerala', 'Kozhikode', '2024-06-22 04:37:18', '2024-06-22 04:37:18'),
(135, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/adminlogout', 'admin logged out', 'India', 'Kerala', 'Kozhikode', '2024-06-22 04:38:14', '2024-06-22 04:38:14'),
(136, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-22 04:38:21', '2024-06-22 04:38:21'),
(137, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/return', 'user_1 visited product return page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 04:38:27', '2024-06-22 04:38:27'),
(138, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdatreturn/1', 'admin visited kannur branch\'s sales return report', 'India', 'Kerala', 'Kozhikode', '2024-06-22 04:39:17', '2024-06-22 04:39:17'),
(139, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/listuser', 'admin visited list user page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 04:41:22', '2024-06-22 04:41:22'),
(140, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/userreport/1', 'admin visited user reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 04:41:25', '2024-06-22 04:41:25'),
(141, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/userreport/1', 'admin visited user reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 04:57:10', '2024-06-22 04:57:10'),
(142, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/userreport/1', 'admin visited user reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 04:57:11', '2024-06-22 04:57:11'),
(143, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/listuser', 'admin visited list user page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 04:57:28', '2024-06-22 04:57:28'),
(144, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/userreport/1', 'admin visited user reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 04:57:31', '2024-06-22 04:57:31'),
(145, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/adminlogout', 'admin logged out', NULL, 'Kerala', 'Tellicherry', '2024-06-22 05:00:07', '2024-06-22 05:00:07'),
(146, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-22 05:00:17', '2024-06-22 05:00:17'),
(147, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 05:00:22', '2024-06-22 05:00:22'),
(148, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-22 05:00:30', '2024-06-22 05:00:30'),
(149, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 05:12:29', '2024-06-22 05:12:29'),
(150, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-22 05:12:55', '2024-06-22 05:12:55'),
(151, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 05:30:15', '2024-06-22 05:30:15'),
(152, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-22 05:30:34', '2024-06-22 05:30:34'),
(153, NULL, NULL, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/creditcreateform', ' created credit user named credit1', 'India', 'Kerala', 'Kozhikode', '2024-06-22 05:32:11', '2024-06-22 05:32:11'),
(154, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 05:32:44', '2024-06-22 05:32:44'),
(155, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-22 05:33:11', '2024-06-22 05:33:11'),
(156, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/userlogout', 'user_1 logged out', 'India', 'Kerala', 'Kozhikode', '2024-06-22 05:35:51', '2024-06-22 05:35:51'),
(157, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'admin logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-22 05:35:54', '2024-06-22 05:35:54'),
(158, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdat/1', 'admin visited kannur branch\'s reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 05:36:00', '2024-06-22 05:36:00'),
(159, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdatreturn/1', 'admin visited kannur branch\'s sales return report', 'India', 'Kerala', 'Kozhikode', '2024-06-22 05:36:02', '2024-06-22 05:36:02'),
(160, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdat/1', 'admin visited kannur branch\'s reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 05:36:07', '2024-06-22 05:36:07'),
(161, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/listuser', 'admin visited list user page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 05:36:13', '2024-06-22 05:36:13'),
(162, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/userreport/1', 'admin visited user reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 05:36:16', '2024-06-22 05:36:16'),
(163, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 05:59:23', '2024-06-22 05:59:23'),
(164, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 05:59:28', '2024-06-22 05:59:28'),
(165, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/salesorder_submit', 'user_1 done sales order BTSLS2', 'India', 'Kerala', 'Kozhikode', '2024-06-22 05:59:34', '2024-06-22 05:59:34'),
(166, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:05:36', '2024-06-22 06:05:36'),
(167, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/salesorder_submit', 'user_1 done sales order BTSLS3', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:05:50', '2024-06-22 06:05:50'),
(168, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:08:01', '2024-06-22 06:08:01'),
(169, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/salesorder_submit', 'user_1 done sales order BTSLS4', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:08:58', '2024-06-22 06:08:58'),
(170, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:13:11', '2024-06-22 06:13:11'),
(171, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/salesorder_submit', 'user_1 done Quotation BTQUOT1', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:13:19', '2024-06-22 06:13:19'),
(172, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:13:26', '2024-06-22 06:13:26'),
(173, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/salesorder_submit', 'user_1 done performance_invoice BTPI1', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:13:40', '2024-06-22 06:13:40'),
(174, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:13:47', '2024-06-22 06:13:47'),
(175, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/deliverynote_submit', 'user_1 done delivery note.', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:14:00', '2024-06-22 06:14:00'),
(176, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:15:41', '2024-06-22 06:15:41'),
(177, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:15:44', '2024-06-22 06:15:44'),
(178, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:18:41', '2024-06-22 06:18:41'),
(179, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:26:06', '2024-06-22 06:26:06'),
(180, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:26:07', '2024-06-22 06:26:07'),
(181, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:26:09', '2024-06-22 06:26:09'),
(182, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/deliverynote_submit', 'user_1 done delivery note.', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:26:20', '2024-06-22 06:26:20'),
(183, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:26:25', '2024-06-22 06:26:25'),
(184, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/deliverynote_submit', 'user_1 done delivery note.', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:26:31', '2024-06-22 06:26:31'),
(185, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:28:14', '2024-06-22 06:28:14'),
(186, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:28:57', '2024-06-22 06:28:57'),
(187, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:29:05', '2024-06-22 06:29:05'),
(188, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/deliverynote_submit', 'user_1 done delivery note.', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:29:17', '2024-06-22 06:29:17'),
(189, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:29:23', '2024-06-22 06:29:23'),
(190, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/userlogout', 'user_1 logged out', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:38:27', '2024-06-22 06:38:27'),
(191, NULL, NULL, 1, 1, 0, 0, 1, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'credit1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:38:35', '2024-06-22 06:38:35'),
(192, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/adminlogout', 'admin logged out', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:43:01', '2024-06-22 06:43:01'),
(193, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:43:10', '2024-06-22 06:43:10'),
(194, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:43:14', '2024-06-22 06:43:14'),
(195, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitstock_table', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:44:44', '2024-06-22 06:44:44'),
(196, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:44:46', '2024-06-22 06:44:46'),
(197, NULL, NULL, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/creditcreateform', ' created credit user named credit_1', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:45:08', '2024-06-22 06:45:08'),
(198, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:45:10', '2024-06-22 06:45:10'),
(199, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:45:13', '2024-06-22 06:45:13'),
(200, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:45:35', '2024-06-22 06:45:35'),
(201, NULL, NULL, 1, 1, 0, 0, 1, '127.0.0.1', 'http://127.0.0.1:8000/creditlogout', 'credit1 logged out', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:53:47', '2024-06-22 06:53:47'),
(202, NULL, NULL, 2, 1, 0, 0, 1, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'credit_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:53:52', '2024-06-22 06:53:52'),
(203, NULL, NULL, 2, 1, 0, 0, 1, '127.0.0.1', 'http://127.0.0.1:8000/creditlogout', 'credit_1 logged out', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:53:56', '2024-06-22 06:53:56'),
(204, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:54:02', '2024-06-22 06:54:02'),
(205, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/userlogout', 'user_1 logged out', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:55:09', '2024-06-22 06:55:09'),
(206, NULL, NULL, 2, 1, 0, 0, 1, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'credit_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:55:18', '2024-06-22 06:55:18'),
(207, NULL, NULL, 2, 1, 0, 0, 1, '127.0.0.1', 'http://127.0.0.1:8000/creditlogout', 'credit_1 logged out', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:55:51', '2024-06-22 06:55:51'),
(208, NULL, NULL, 1, 1, 0, 0, 1, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'credit1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-22 06:56:00', '2024-06-22 06:56:00'),
(209, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/addsupplier_creditfund', 'user_1 added collection payment to credit supplier supplier1', 'India', 'Kerala', 'Kozhikode', '2024-06-22 08:13:15', '2024-06-22 08:13:15'),
(210, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitstock_table', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-22 08:13:53', '2024-06-22 08:13:53'),
(211, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/addsupplier_creditfund', 'user_1 added collection payment to credit supplier supplier1', 'India', 'Kerala', 'Kozhikode', '2024-06-22 08:21:24', '2024-06-22 08:21:24'),
(212, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 08:25:49', '2024-06-22 08:25:49'),
(213, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 08:25:51', '2024-06-22 08:25:51'),
(214, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-22 08:25:57', '2024-06-22 08:25:57'),
(215, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-22 08:27:48', '2024-06-22 08:27:48'),
(216, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 08:27:52', '2024-06-22 08:27:52'),
(217, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-22 08:28:00', '2024-06-22 08:28:00'),
(218, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-22 08:28:31', '2024-06-22 08:28:31'),
(219, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 08:28:36', '2024-06-22 08:28:36'),
(220, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 08:29:07', '2024-06-22 08:29:07'),
(221, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-22 08:29:16', '2024-06-22 08:29:16'),
(222, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-22 08:30:33', '2024-06-22 08:30:33'),
(223, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 08:30:36', '2024-06-22 08:30:36'),
(224, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-22 08:30:45', '2024-06-22 08:30:45'),
(225, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 08:31:33', '2024-06-22 08:31:33'),
(226, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-22 08:31:44', '2024-06-22 08:31:44'),
(227, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/addfundcredit2', 'user_1 added collection payment to credit user credit1', 'India', 'Kerala', 'Kozhikode', '2024-06-22 08:46:12', '2024-06-22 08:46:12'),
(228, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-22 08:54:09', '2024-06-22 08:54:09'),
(229, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-23 05:42:05', '2024-06-23 05:42:05'),
(230, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-23 05:42:26', '2024-06-23 05:42:26'),
(231, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-23 05:42:29', '2024-06-23 05:42:29'),
(232, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-23 05:42:34', '2024-06-23 05:42:34'),
(233, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitstock_table', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-23 05:43:03', '2024-06-23 05:43:03'),
(234, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-23 05:43:06', '2024-06-23 05:43:06'),
(235, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-23 05:44:03', '2024-06-23 05:44:03'),
(236, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-23 05:44:49', '2024-06-23 05:44:49'),
(237, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-23 06:47:58', '2024-06-23 06:47:58'),
(238, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-23 06:48:40', '2024-06-23 06:48:40'),
(239, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitdatadraft/edit/Draft1', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-23 06:49:11', '2024-06-23 06:49:11'),
(240, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-23 06:50:36', '2024-06-23 06:50:36'),
(241, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-23 06:50:41', '2024-06-23 06:50:41'),
(242, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-23 06:56:53', '2024-06-23 06:56:53'),
(243, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-23 06:57:07', '2024-06-23 06:57:07'),
(244, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-23 06:57:29', '2024-06-23 06:57:29'),
(245, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/changeproductstatus/1', 'user_1 disabled p1', 'India', 'Kerala', 'Kozhikode', '2024-06-23 06:57:37', '2024-06-23 06:57:37'),
(246, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-23 06:57:42', '2024-06-23 06:57:42'),
(247, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/changeproductstatus/1', 'user_1 enabled p1', 'India', 'Kerala', 'Kozhikode', '2024-06-23 06:57:57', '2024-06-23 06:57:57'),
(248, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-23 07:01:18', '2024-06-23 07:01:18'),
(249, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-23 07:01:26', '2024-06-23 07:01:26'),
(250, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitproductdraft/edit/1', 'user_1 added or edited products', 'India', 'Kerala', 'Kozhikode', '2024-06-23 07:23:53', '2024-06-23 07:23:53'),
(251, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitstock_table', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-23 07:25:15', '2024-06-23 07:25:15'),
(252, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-23 09:07:46', '2024-06-23 09:07:46'),
(253, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-23 12:43:02', '2024-06-23 12:43:02'),
(254, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitproductdraft/edit/Draft1', 'user_1 added or edited products', 'India', 'Kerala', 'Kozhikode', '2024-06-23 13:12:50', '2024-06-23 13:12:50'),
(255, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitproductdraft/edit/Draft1', 'user_1 added or edited products', 'India', 'Kerala', 'Kozhikode', '2024-06-23 13:16:31', '2024-06-23 13:16:31');
INSERT INTO `activities` (`id`, `user_id`, `admin_id`, `credituser_id`, `branch_id`, `is_admin`, `is_user`, `is_credituser`, `ipaddress`, `url`, `message`, `countryName`, `regionName`, `cityName`, `created_at`, `updated_at`) VALUES
(256, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-24 02:47:12', '2024-06-24 02:47:12'),
(257, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitstock_table', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 03:06:55', '2024-06-24 03:06:55'),
(258, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/edit/123', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 04:01:56', '2024-06-24 04:01:56'),
(259, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/edit/123', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 04:02:42', '2024-06-24 04:02:42'),
(260, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/edit/123', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 04:06:52', '2024-06-24 04:06:52'),
(261, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/edit/7', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 04:08:28', '2024-06-24 04:08:28'),
(262, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft8', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 04:18:40', '2024-06-24 04:18:40'),
(263, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft66', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 04:20:33', '2024-06-24 04:20:33'),
(264, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 04:25:19', '2024-06-24 04:25:19'),
(265, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 04:32:37', '2024-06-24 04:32:37'),
(266, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 04:48:01', '2024-06-24 04:48:01'),
(267, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 04:55:29', '2024-06-24 04:55:29'),
(268, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 05:01:57', '2024-06-24 05:01:57'),
(269, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/121', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 05:02:49', '2024-06-24 05:02:49'),
(270, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/121', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 05:04:10', '2024-06-24 05:04:10'),
(271, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/121', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 05:05:31', '2024-06-24 05:05:31'),
(272, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/121', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 05:11:03', '2024-06-24 05:11:03'),
(273, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/121', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 05:19:41', '2024-06-24 05:19:41'),
(274, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/121', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 05:22:17', '2024-06-24 05:22:17'),
(275, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/121', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 05:45:03', '2024-06-24 05:45:03'),
(276, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/121', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 05:46:10', '2024-06-24 05:46:10'),
(277, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/121', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 05:49:15', '2024-06-24 05:49:15'),
(278, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/121', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 05:52:05', '2024-06-24 05:52:05'),
(279, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/121', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 05:55:58', '2024-06-24 05:55:58'),
(280, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/121', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 06:57:08', '2024-06-24 06:57:08'),
(281, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-24 07:03:45', '2024-06-24 07:03:45'),
(282, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/121', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 07:31:41', '2024-06-24 07:31:41'),
(283, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/121', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 07:40:32', '2024-06-24 07:40:32'),
(284, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/121', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 07:45:15', '2024-06-24 07:45:15'),
(285, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/121', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 07:49:09', '2024-06-24 07:49:09'),
(286, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitstock_table', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 08:21:22', '2024-06-24 08:21:22'),
(287, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/121', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 08:45:07', '2024-06-24 08:45:07'),
(288, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/121', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 08:49:37', '2024-06-24 08:49:37'),
(289, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/121', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 08:51:19', '2024-06-24 08:51:19'),
(290, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/121', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 09:00:07', '2024-06-24 09:00:07'),
(291, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/122', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 09:00:59', '2024-06-24 09:00:59'),
(292, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/121', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 09:05:05', '2024-06-24 09:05:05'),
(293, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/121', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 09:17:48', '2024-06-24 09:17:48'),
(294, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-24 09:18:14', '2024-06-24 09:18:14'),
(295, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/121', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 09:19:43', '2024-06-24 09:19:43'),
(296, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/121', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 09:20:03', '2024-06-24 09:20:03'),
(297, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/121', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 09:20:11', '2024-06-24 09:20:11'),
(298, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/121', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 09:39:02', '2024-06-24 09:39:02'),
(299, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/121', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 09:42:40', '2024-06-24 09:42:40'),
(300, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/1111', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 09:52:06', '2024-06-24 09:52:06'),
(301, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-24 13:25:17', '2024-06-24 13:25:17'),
(302, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/1111', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 13:33:07', '2024-06-24 13:33:07'),
(303, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/1111', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 13:34:38', '2024-06-24 13:34:38'),
(304, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/1111', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 13:35:08', '2024-06-24 13:35:08'),
(305, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/1111', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 14:41:07', '2024-06-24 14:41:07'),
(306, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/1111', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-24 14:42:43', '2024-06-24 14:42:43'),
(307, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-25 02:32:35', '2024-06-25 02:32:35'),
(308, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 02:52:59', '2024-06-25 02:52:59'),
(309, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 02:53:12', '2024-06-25 02:53:12'),
(310, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 02:54:25', '2024-06-25 02:54:25'),
(311, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 02:54:30', '2024-06-25 02:54:30'),
(312, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 02:58:09', '2024-06-25 02:58:09'),
(313, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 02:59:17', '2024-06-25 02:59:17'),
(314, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 02:59:24', '2024-06-25 02:59:24'),
(315, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 02:59:28', '2024-06-25 02:59:28'),
(316, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 03:12:14', '2024-06-25 03:12:14'),
(317, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 03:13:00', '2024-06-25 03:13:00'),
(318, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 03:14:19', '2024-06-25 03:14:19'),
(319, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 03:14:43', '2024-06-25 03:14:43'),
(320, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 03:17:35', '2024-06-25 03:17:35'),
(321, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 03:33:19', '2024-06-25 03:33:19'),
(322, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 03:33:25', '2024-06-25 03:33:25'),
(323, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 03:33:29', '2024-06-25 03:33:29'),
(324, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 03:33:37', '2024-06-25 03:33:37'),
(325, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 03:35:11', '2024-06-25 03:35:11'),
(326, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 03:35:38', '2024-06-25 03:35:38'),
(327, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 03:37:06', '2024-06-25 03:37:06'),
(328, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 03:53:01', '2024-06-25 03:53:01'),
(329, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 03:54:48', '2024-06-25 03:54:48'),
(330, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 03:55:48', '2024-06-25 03:55:48'),
(331, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 03:55:50', '2024-06-25 03:55:50'),
(332, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 04:47:21', '2024-06-25 04:47:21'),
(333, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 04:47:22', '2024-06-25 04:47:22'),
(334, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitsalesdraft/BTSLS1', 'user_1 done sales order BTSLS2', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:13:17', '2024-06-25 05:13:17'),
(335, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitsalesdraft/BTSLS1', 'user_1 done sales order BTSLS3', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:14:13', '2024-06-25 05:14:13'),
(336, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:15:02', '2024-06-25 05:15:02'),
(337, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:15:05', '2024-06-25 05:15:05'),
(338, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:15:58', '2024-06-25 05:15:58'),
(339, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:16:01', '2024-06-25 05:16:01'),
(340, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:16:20', '2024-06-25 05:16:20'),
(341, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:16:41', '2024-06-25 05:16:41'),
(342, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:16:44', '2024-06-25 05:16:44'),
(343, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:17:10', '2024-06-25 05:17:10'),
(344, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:17:11', '2024-06-25 05:17:11'),
(345, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:17:15', '2024-06-25 05:17:15'),
(346, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:17:19', '2024-06-25 05:17:19'),
(347, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:17:39', '2024-06-25 05:17:39'),
(348, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitsalesdraft/BTSLS1', 'user_1 done sales order BTSLS1', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:19:54', '2024-06-25 05:19:54'),
(349, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:19:59', '2024-06-25 05:19:59'),
(350, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:20:11', '2024-06-25 05:20:11'),
(351, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/salesorder_submit', 'user_1 done sales order BTSLS2', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:20:52', '2024-06-25 05:20:52'),
(352, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:24:30', '2024-06-25 05:24:30'),
(353, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:24:49', '2024-06-25 05:24:49'),
(354, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:33:15', '2024-06-25 05:33:15'),
(355, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitsalesdraft/BTSLS1', 'user_1 done sales order BTSLS3', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:35:33', '2024-06-25 05:35:33'),
(356, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:50:45', '2024-06-25 05:50:45'),
(357, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:51:20', '2024-06-25 05:51:20'),
(358, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:51:44', '2024-06-25 05:51:44'),
(359, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitsalesdraft/BTSLS1', 'user_1 done sales order BTSLS4', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:51:53', '2024-06-25 05:51:53'),
(360, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:52:11', '2024-06-25 05:52:11'),
(361, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:54:15', '2024-06-25 05:54:15'),
(362, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:54:21', '2024-06-25 05:54:21'),
(363, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:55:41', '2024-06-25 05:55:41'),
(364, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:55:56', '2024-06-25 05:55:56'),
(365, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitsalesdraft/BTSLS2', 'user_1 done sales order BTSLS5', 'India', 'Kerala', 'Kozhikode', '2024-06-25 05:57:09', '2024-06-25 05:57:09'),
(366, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitsalesdraft/BTSLS1', 'user_1 done sales order BTSLS6', 'India', 'Kerala', 'Kozhikode', '2024-06-25 06:04:32', '2024-06-25 06:04:32'),
(367, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 06:09:41', '2024-06-25 06:09:41'),
(368, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 06:10:13', '2024-06-25 06:10:13'),
(369, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitsalesdraft/BTSLS1', 'user_1 done sales order BTSLS1', 'India', 'Kerala', 'Kozhikode', '2024-06-25 06:10:23', '2024-06-25 06:10:23'),
(370, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 06:10:27', '2024-06-25 06:10:27'),
(371, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/salesorder_submit', 'user_1 done sales order BTSLS2', 'India', 'Kerala', 'Kozhikode', '2024-06-25 06:10:39', '2024-06-25 06:10:39'),
(372, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 06:12:35', '2024-06-25 06:12:35'),
(373, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 06:13:13', '2024-06-25 06:13:13'),
(374, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitsalesdraft/BTSLS1', 'user_1 done sales order BTSLS1', 'India', 'Kerala', 'Kozhikode', '2024-06-25 06:13:19', '2024-06-25 06:13:19'),
(375, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 06:13:31', '2024-06-25 06:13:31'),
(376, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/salesorder_submit', 'user_1 done sales order BTSLS2', 'India', 'Kerala', 'Kozhikode', '2024-06-25 06:13:43', '2024-06-25 06:13:43'),
(377, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitstock_table', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-25 07:01:52', '2024-06-25 07:01:52'),
(378, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasereturn', 'user_1 purchase returned', 'India', 'Kerala', 'Kozhikode', '2024-06-25 07:02:09', '2024-06-25 07:02:09'),
(379, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitstock_table', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-25 07:02:44', '2024-06-25 07:02:44'),
(380, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasereturn', 'user_1 purchase returned', 'India', 'Kerala', 'Kozhikode', '2024-06-25 07:02:59', '2024-06-25 07:02:59'),
(381, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 07:31:30', '2024-06-25 07:31:30'),
(382, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/1111', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-25 08:21:09', '2024-06-25 08:21:09'),
(383, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/1111', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-25 08:22:17', '2024-06-25 08:22:17'),
(384, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/1111', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-25 08:36:26', '2024-06-25 08:36:26'),
(385, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/1111', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-25 08:38:33', '2024-06-25 08:38:33'),
(386, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 08:51:24', '2024-06-25 08:51:24'),
(387, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 08:51:40', '2024-06-25 08:51:40'),
(388, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 08:51:44', '2024-06-25 08:51:44'),
(389, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 08:51:49', '2024-06-25 08:51:49'),
(390, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 08:57:34', '2024-06-25 08:57:34'),
(391, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 08:57:38', '2024-06-25 08:57:38'),
(392, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 08:58:02', '2024-06-25 08:58:02'),
(393, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 08:59:57', '2024-06-25 08:59:57'),
(394, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:02:04', '2024-06-25 09:02:04'),
(395, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:02:12', '2024-06-25 09:02:12'),
(396, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:03:16', '2024-06-25 09:03:16'),
(397, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:03:23', '2024-06-25 09:03:23'),
(398, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:05:03', '2024-06-25 09:05:03'),
(399, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:05:10', '2024-06-25 09:05:10'),
(400, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:08:35', '2024-06-25 09:08:35'),
(401, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:09:07', '2024-06-25 09:09:07'),
(402, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:09:11', '2024-06-25 09:09:11'),
(403, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:09:17', '2024-06-25 09:09:17'),
(404, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:11:16', '2024-06-25 09:11:16'),
(405, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:11:21', '2024-06-25 09:11:21'),
(406, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:12:32', '2024-06-25 09:12:32'),
(407, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:19:07', '2024-06-25 09:19:07'),
(408, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:19:25', '2024-06-25 09:19:25'),
(409, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:38:19', '2024-06-25 09:38:19'),
(410, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:38:35', '2024-06-25 09:38:35'),
(411, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:39:38', '2024-06-25 09:39:38'),
(412, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:39:53', '2024-06-25 09:39:53'),
(413, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:51:08', '2024-06-25 09:51:08'),
(414, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:51:24', '2024-06-25 09:51:24'),
(415, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:52:26', '2024-06-25 09:52:26'),
(416, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitstock_table', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:53:51', '2024-06-25 09:53:51'),
(417, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitstock_table', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:54:16', '2024-06-25 09:54:16'),
(418, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:54:21', '2024-06-25 09:54:21'),
(419, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:54:50', '2024-06-25 09:54:50'),
(420, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/userlogout', 'user_1 logged out', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:55:44', '2024-06-25 09:55:44'),
(421, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'admin logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:55:53', '2024-06-25 09:55:53'),
(422, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdat/1', 'admin visited kannur branch\'s reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:56:04', '2024-06-25 09:56:04'),
(423, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdatstock/1', 'admin visited kannur branch\'s stock report', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:56:07', '2024-06-25 09:56:07'),
(424, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/edittransactiondetails/edit_bill/editsubmitdata', 'user_1 done BT17 editing - s', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:57:22', '2024-06-25 09:57:22'),
(425, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdatstock/1', 'admin visited kannur branch\'s stock report', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:57:28', '2024-06-25 09:57:28'),
(426, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdatstock/1', 'admin visited kannur branch\'s stock report', 'India', 'Kerala', 'Kozhikode', '2024-06-25 09:59:32', '2024-06-25 09:59:32'),
(427, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-25 12:22:37', '2024-06-25 12:22:37'),
(428, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 12:22:43', '2024-06-25 12:22:43'),
(429, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-25 12:23:24', '2024-06-25 12:23:24'),
(430, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 12:23:31', '2024-06-25 12:23:31'),
(431, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 12:23:47', '2024-06-25 12:23:47'),
(432, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 12:24:03', '2024-06-25 12:24:03'),
(433, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 12:25:04', '2024-06-25 12:25:04'),
(434, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 12:25:17', '2024-06-25 12:25:17'),
(435, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 12:26:51', '2024-06-25 12:26:51'),
(436, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 13:12:31', '2024-06-25 13:12:31'),
(437, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 13:12:48', '2024-06-25 13:12:48'),
(438, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 13:14:20', '2024-06-25 13:14:20'),
(439, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitsalesdraft/BTSLS2', 'user_1 done sales order BTSLS3', 'India', 'Kerala', 'Kozhikode', '2024-06-25 13:15:28', '2024-06-25 13:15:28'),
(440, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 13:18:58', '2024-06-25 13:18:58'),
(441, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 13:19:15', '2024-06-25 13:19:15'),
(442, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/salesorder_submit', 'user_1 done sales order BTSLS1', 'India', 'Kerala', 'Kozhikode', '2024-06-25 13:19:32', '2024-06-25 13:19:32'),
(443, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 13:19:34', '2024-06-25 13:19:34'),
(444, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitsalesdraft/BTSLS1', 'user_1 done sales order BTSLS2', 'India', 'Kerala', 'Kozhikode', '2024-06-25 13:19:39', '2024-06-25 13:19:39'),
(445, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 13:24:31', '2024-06-25 13:24:31'),
(446, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 13:27:45', '2024-06-25 13:27:45'),
(447, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 13:28:35', '2024-06-25 13:28:35'),
(448, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 13:29:30', '2024-06-25 13:29:30'),
(449, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitquotationdraft/BTQUOT1', 'user_1 done Quotation BTQUOT2', 'India', 'Kerala', 'Kozhikode', '2024-06-25 13:36:44', '2024-06-25 13:36:44'),
(450, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-25 16:19:17', '2024-06-25 16:19:17'),
(451, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 16:19:24', '2024-06-25 16:19:24'),
(452, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitquotationdraft/BTQUOT2', 'user_1 done Quotation BTQUOT2', 'India', 'Kerala', 'Kozhikode', '2024-06-25 16:19:39', '2024-06-25 16:19:39'),
(453, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 16:20:00', '2024-06-25 16:20:00'),
(454, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/salesorder_submit', 'user_1 done Quotation BTQUOT2', 'India', 'Kerala', 'Kozhikode', '2024-06-25 16:20:15', '2024-06-25 16:20:15'),
(455, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 16:33:49', '2024-06-25 16:33:49'),
(456, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 16:34:03', '2024-06-25 16:34:03'),
(457, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitquotationdraft/BTPI2', 'user_1 done Quotation BTQUOT1', 'India', 'Kerala', 'Kozhikode', '2024-06-25 16:34:10', '2024-06-25 16:34:10'),
(458, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 16:34:16', '2024-06-25 16:34:16'),
(459, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/salesorder_submit', 'user_1 done Quotation BTQUOT2', 'India', 'Kerala', 'Kozhikode', '2024-06-25 16:34:29', '2024-06-25 16:34:29'),
(460, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 16:35:14', '2024-06-25 16:35:14'),
(461, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 16:35:21', '2024-06-25 16:35:21'),
(462, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 16:35:38', '2024-06-25 16:35:38'),
(463, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 16:36:08', '2024-06-25 16:36:08'),
(464, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 16:36:22', '2024-06-25 16:36:22'),
(465, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 16:37:50', '2024-06-25 16:37:50'),
(466, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 16:38:04', '2024-06-25 16:38:04'),
(467, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitperformadraft/BTPI1', 'user_1 done performance_invoice BTPI1', 'India', 'Kerala', 'Kozhikode', '2024-06-25 16:41:12', '2024-06-25 16:41:12'),
(468, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-25 16:41:44', '2024-06-25 16:41:44'),
(469, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/salesorder_submit', 'user_1 done performance_invoice BTPI2', 'India', 'Kerala', 'Kozhikode', '2024-06-25 16:42:03', '2024-06-25 16:42:03'),
(470, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-26 02:38:17', '2024-06-26 02:38:17'),
(471, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 02:38:29', '2024-06-26 02:38:29'),
(472, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 02:42:49', '2024-06-26 02:42:49'),
(473, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 02:45:35', '2024-06-26 02:45:35'),
(474, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 02:54:11', '2024-06-26 02:54:11'),
(475, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 02:57:05', '2024-06-26 02:57:05'),
(476, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 02:57:47', '2024-06-26 02:57:47'),
(477, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 03:04:18', '2024-06-26 03:04:18'),
(478, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 03:04:20', '2024-06-26 03:04:20'),
(479, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 03:04:25', '2024-06-26 03:04:25'),
(480, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 03:05:27', '2024-06-26 03:05:27'),
(481, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'admin logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-26 03:09:21', '2024-06-26 03:09:21'),
(482, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/listuser', 'admin visited list user page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 03:09:44', '2024-06-26 03:09:44'),
(483, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/listuser', 'admin visited list user page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 03:09:53', '2024-06-26 03:09:53'),
(484, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/userreport/1', 'admin visited user reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 03:09:56', '2024-06-26 03:09:56'),
(485, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 03:18:35', '2024-06-26 03:18:35'),
(486, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 03:18:44', '2024-06-26 03:18:44'),
(487, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 03:18:53', '2024-06-26 03:18:53'),
(488, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 03:19:01', '2024-06-26 03:19:01'),
(489, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 03:19:05', '2024-06-26 03:19:05'),
(490, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 03:25:17', '2024-06-26 03:25:17'),
(491, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitdeliverydraft/BTDN1', 'user_1 done delivery note.', 'India', 'Kerala', 'Kozhikode', '2024-06-26 04:01:24', '2024-06-26 04:01:24'),
(492, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 04:03:25', '2024-06-26 04:03:25'),
(493, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/deliverynote_submit', 'user_1 done delivery note.', 'India', 'Kerala', 'Kozhikode', '2024-06-26 04:04:12', '2024-06-26 04:04:12'),
(494, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 04:06:35', '2024-06-26 04:06:35'),
(495, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 04:07:41', '2024-06-26 04:07:41'),
(496, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/deliverynote_submit', 'user_1 done delivery note.', 'India', 'Kerala', 'Kozhikode', '2024-06-26 04:08:19', '2024-06-26 04:08:19'),
(497, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 04:08:27', '2024-06-26 04:08:27'),
(498, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 04:09:14', '2024-06-26 04:09:14'),
(499, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitdeliverydraft/BTDN1', 'user_1 done delivery note.', 'India', 'Kerala', 'Kozhikode', '2024-06-26 04:09:35', '2024-06-26 04:09:35'),
(500, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/1111', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-26 04:12:59', '2024-06-26 04:12:59'),
(501, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/1111', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-26 06:12:45', '2024-06-26 06:12:45'),
(502, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/1111', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-26 06:15:29', '2024-06-26 06:15:29'),
(503, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/1111', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-26 07:13:38', '2024-06-26 07:13:38'),
(504, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/1111', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-26 07:50:09', '2024-06-26 07:50:09'),
(505, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'admin logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-26 08:26:56', '2024-06-26 08:26:56'),
(506, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdat/1', 'admin visited kannur branch\'s reports page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 08:27:05', '2024-06-26 08:27:05'),
(507, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/branchdatstock/1', 'admin visited kannur branch\'s stock report', 'India', 'Kerala', 'Kozhikode', '2024-06-26 08:27:08', '2024-06-26 08:27:08');
INSERT INTO `activities` (`id`, `user_id`, `admin_id`, `credituser_id`, `branch_id`, `is_admin`, `is_user`, `is_credituser`, `ipaddress`, `url`, `message`, `countryName`, `regionName`, `cityName`, `created_at`, `updated_at`) VALUES
(508, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/22', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-26 09:05:45', '2024-06-26 09:05:45'),
(509, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/18', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-26 09:18:49', '2024-06-26 09:18:49'),
(510, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/133', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-26 09:30:43', '2024-06-26 09:30:43'),
(511, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/1232', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-26 09:31:52', '2024-06-26 09:31:52'),
(512, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitstock_table', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-26 09:32:32', '2024-06-26 09:32:32'),
(513, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/4555', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-26 09:38:35', '2024-06-26 09:38:35'),
(514, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/344', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-26 09:46:36', '2024-06-26 09:46:36'),
(515, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/777', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-26 09:51:15', '2024-06-26 09:51:15'),
(516, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-26 12:56:35', '2024-06-26 12:56:35'),
(517, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 12:56:55', '2024-06-26 12:56:55'),
(518, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 12:59:02', '2024-06-26 12:59:02'),
(519, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 12:59:10', '2024-06-26 12:59:10'),
(520, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 13:01:47', '2024-06-26 13:01:47'),
(521, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-26 13:01:58', '2024-06-26 13:01:58'),
(522, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/55', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-26 13:30:19', '2024-06-26 13:30:19'),
(523, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitstock_table', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-26 13:31:12', '2024-06-26 13:31:12'),
(524, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/111111', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-26 13:31:39', '2024-06-26 13:31:39'),
(525, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitstock_table', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-26 13:32:17', '2024-06-26 13:32:17'),
(526, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/1234567', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-26 13:32:47', '2024-06-26 13:32:47'),
(527, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitstock_table', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-26 13:33:28', '2024-06-26 13:33:28'),
(528, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasedraft/dfgh', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-26 13:34:11', '2024-06-26 13:34:11'),
(529, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-27 02:42:57', '2024-06-27 02:42:57'),
(530, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 02:44:16', '2024-06-27 02:44:16'),
(531, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 02:57:15', '2024-06-27 02:57:15'),
(532, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:07:46', '2024-06-27 03:07:46'),
(533, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:07:52', '2024-06-27 03:07:52'),
(534, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:07:58', '2024-06-27 03:07:58'),
(535, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:08:04', '2024-06-27 03:08:04'),
(536, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/salesorder_submit', 'user_1 done sales order BTSLS3', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:09:24', '2024-06-27 03:09:24'),
(537, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:10:09', '2024-06-27 03:10:09'),
(538, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:10:11', '2024-06-27 03:10:11'),
(539, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:10:20', '2024-06-27 03:10:20'),
(540, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:10:27', '2024-06-27 03:10:27'),
(541, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:10:32', '2024-06-27 03:10:32'),
(542, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:10:36', '2024-06-27 03:10:36'),
(543, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:10:44', '2024-06-27 03:10:44'),
(544, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:10:48', '2024-06-27 03:10:48'),
(545, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:10:57', '2024-06-27 03:10:57'),
(546, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:11:05', '2024-06-27 03:11:05'),
(547, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitperformadraft/BTPI1', 'user_1 done performance_invoice BTPI3', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:13:13', '2024-06-27 03:13:13'),
(548, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:19:32', '2024-06-27 03:19:32'),
(549, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:19:41', '2024-06-27 03:19:41'),
(550, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:19:49', '2024-06-27 03:19:49'),
(551, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:20:01', '2024-06-27 03:20:01'),
(552, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitsalesdraft/BTSLS2', 'user_1 done sales order BTSLS4', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:20:09', '2024-06-27 03:20:09'),
(553, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:20:28', '2024-06-27 03:20:28'),
(554, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:20:42', '2024-06-27 03:20:42'),
(555, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitquotationdraft/BTQUOT1', 'user_1 done Quotation BTQUOT3', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:20:50', '2024-06-27 03:20:50'),
(556, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:20:57', '2024-06-27 03:20:57'),
(557, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:21:11', '2024-06-27 03:21:11'),
(558, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:21:24', '2024-06-27 03:21:24'),
(559, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:21:42', '2024-06-27 03:21:42'),
(560, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:21:47', '2024-06-27 03:21:47'),
(561, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:28:36', '2024-06-27 03:28:36'),
(562, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:28:41', '2024-06-27 03:28:41'),
(563, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:28:44', '2024-06-27 03:28:44'),
(564, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:28:57', '2024-06-27 03:28:57'),
(565, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitperformadraft/BTPI1', 'user_1 done performance_invoice BTPI4', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:29:05', '2024-06-27 03:29:05'),
(566, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:30:04', '2024-06-27 03:30:04'),
(567, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:36:36', '2024-06-27 03:36:36'),
(568, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/userlogout', 'user_1 logged out', 'India', 'Kerala', 'Kozhikode', '2024-06-27 03:50:30', '2024-06-27 03:50:30'),
(569, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 04:27:34', '2024-06-27 04:27:34'),
(570, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 04:30:10', '2024-06-27 04:30:10'),
(571, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 04:35:36', '2024-06-27 04:35:36'),
(572, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 04:39:04', '2024-06-27 04:39:04'),
(573, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 06:08:53', '2024-06-27 06:08:53'),
(574, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/salesorder_submit', 'user_1 done sales order BTSLS5', 'India', 'Kerala', 'Kozhikode', '2024-06-27 06:09:02', '2024-06-27 06:09:02'),
(575, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 06:09:06', '2024-06-27 06:09:06'),
(576, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 06:15:27', '2024-06-27 06:15:27'),
(577, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 06:25:26', '2024-06-27 06:25:26'),
(578, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/salesorder_submit', 'user_1 done sales order BTSLS6', 'India', 'Kerala', 'Kozhikode', '2024-06-27 06:25:50', '2024-06-27 06:25:50'),
(579, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 06:25:54', '2024-06-27 06:25:54'),
(580, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 06:26:13', '2024-06-27 06:26:13'),
(581, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/salesorder_submit', 'user_1 done Quotation BTQUOT4', 'India', 'Kerala', 'Kozhikode', '2024-06-27 06:26:30', '2024-06-27 06:26:30'),
(582, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 06:26:45', '2024-06-27 06:26:45'),
(583, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 06:30:08', '2024-06-27 06:30:08'),
(584, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/to_invoice/sales_order/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-27 08:34:20', '2024-06-27 08:34:20'),
(585, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 08:35:05', '2024-06-27 08:35:05'),
(586, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-27 08:35:44', '2024-06-27 08:35:44'),
(587, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 08:36:32', '2024-06-27 08:36:32'),
(588, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 08:44:56', '2024-06-27 08:44:56'),
(589, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 08:45:01', '2024-06-27 08:45:01'),
(590, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 08:56:33', '2024-06-27 08:56:33'),
(591, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/salesorder_submit', 'user_1 done sales order BTSLS7', 'India', 'Kerala', 'Kozhikode', '2024-06-27 08:56:49', '2024-06-27 08:56:49'),
(592, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 09:01:39', '2024-06-27 09:01:39'),
(593, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-27 09:57:54', '2024-06-27 09:57:54'),
(594, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-28 02:27:00', '2024-06-28 02:27:00'),
(595, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 02:27:31', '2024-06-28 02:27:31'),
(596, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-28 02:27:38', '2024-06-28 02:27:38'),
(597, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 02:43:26', '2024-06-28 02:43:26'),
(598, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 02:50:27', '2024-06-28 02:50:27'),
(599, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/return', 'user_1 visited product return page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 02:51:39', '2024-06-28 02:51:39'),
(600, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 02:51:42', '2024-06-28 02:51:42'),
(601, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 02:51:45', '2024-06-28 02:51:45'),
(602, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/user-report-submit', 'user_1 user report submitted by user_1', 'India', 'Kerala', 'Kozhikode', '2024-06-28 03:00:56', '2024-06-28 03:00:56'),
(603, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/to_invoice/quotation/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-28 03:04:45', '2024-06-28 03:04:45'),
(604, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 03:08:34', '2024-06-28 03:08:34'),
(605, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-28 03:09:12', '2024-06-28 03:09:12'),
(606, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 03:09:16', '2024-06-28 03:09:16'),
(607, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/edittransactiondetails/edit_bill/editsubmitdata', 'user_1 done BT22 editing - dfg', 'India', 'Kerala', 'Kozhikode', '2024-06-28 03:11:31', '2024-06-28 03:11:31'),
(608, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 03:13:02', '2024-06-28 03:13:02'),
(609, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/to_invoice/quotation/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-28 03:13:12', '2024-06-28 03:13:12'),
(610, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 03:21:58', '2024-06-28 03:21:58'),
(611, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/edittransactiondetails/edit_bill/editsubmitdata', 'user_1 done BT24 editing - efr', 'India', 'Kerala', 'Kozhikode', '2024-06-28 03:22:53', '2024-06-28 03:22:53'),
(612, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 03:23:22', '2024-06-28 03:23:22'),
(613, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/to_invoice/sales_order/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-28 03:23:36', '2024-06-28 03:23:36'),
(614, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/edittransactiondetails/edit_bill/editsubmitdata', 'user_1 done BT25 editing - er', 'India', 'Kerala', 'Kozhikode', '2024-06-28 03:24:06', '2024-06-28 03:24:06'),
(615, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 03:45:22', '2024-06-28 03:45:22'),
(616, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/to_invoice/quotation/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-28 03:45:32', '2024-06-28 03:45:32'),
(617, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 03:54:05', '2024-06-28 03:54:05'),
(618, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/to_invoice/quotation/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-28 03:54:16', '2024-06-28 03:54:16'),
(619, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 03:55:28', '2024-06-28 03:55:28'),
(620, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/salesorder_submit', 'user_1 done Quotation BTQUOT5', 'India', 'Kerala', 'Kozhikode', '2024-06-28 03:55:47', '2024-06-28 03:55:47'),
(621, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 03:55:50', '2024-06-28 03:55:50'),
(622, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/to_invoice/quotation/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-28 03:56:00', '2024-06-28 03:56:00'),
(623, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:14:12', '2024-06-28 04:14:12'),
(624, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:14:17', '2024-06-28 04:14:17'),
(625, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/salesorder_submit', 'user_1 done Quotation BTQUOT6', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:14:37', '2024-06-28 04:14:37'),
(626, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:14:43', '2024-06-28 04:14:43'),
(627, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/to_invoice/quotation/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:14:58', '2024-06-28 04:14:58'),
(628, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/edittransactiondetails/edit_bill/editsubmitdata', 'user_1 done BT29 editing - ygtrg', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:16:52', '2024-06-28 04:16:52'),
(629, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/user-report-submit', 'user_1 user report submitted by user_1', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:19:28', '2024-06-28 04:19:28'),
(630, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:19:54', '2024-06-28 04:19:54'),
(631, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/to_invoice/sales_order/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:20:05', '2024-06-28 04:20:05'),
(632, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/edittransactiondetails/edit_bill/editsubmitdata', 'user_1 done BT30 editing - jik', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:20:40', '2024-06-28 04:20:40'),
(633, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:20:58', '2024-06-28 04:20:58'),
(634, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/salesorder_submit', 'user_1 done Quotation BTQUOT7', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:21:05', '2024-06-28 04:21:05'),
(635, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:21:08', '2024-06-28 04:21:08'),
(636, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/to_invoice/quotation/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:21:20', '2024-06-28 04:21:20'),
(637, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/edittransactiondetails/edit_bill/editsubmitdata', 'user_1 done BT31 editing - rgre', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:21:32', '2024-06-28 04:21:32'),
(638, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/changeproductstatus/1', 'user_1 disabled p1', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:22:03', '2024-06-28 04:22:03'),
(639, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/changeproductstatus/2', 'user_1 disabled p2', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:22:07', '2024-06-28 04:22:07'),
(640, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:22:13', '2024-06-28 04:22:13'),
(641, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:22:20', '2024-06-28 04:22:20'),
(642, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/changeproductstatus/1', 'user_1 enabled p1', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:22:26', '2024-06-28 04:22:26'),
(643, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/changeproductstatus/2', 'user_1 enabled p2', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:22:29', '2024-06-28 04:22:29'),
(644, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', NULL, 'Kerala', 'Tellicherry', '2024-06-28 04:22:36', '2024-06-28 04:22:36'),
(645, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:22:44', '2024-06-28 04:22:44'),
(646, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/changeproductstatus/1', 'user_1 disabled p1', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:22:51', '2024-06-28 04:22:51'),
(647, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:22:55', '2024-06-28 04:22:55'),
(648, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:23:05', '2024-06-28 04:23:05'),
(649, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:23:22', '2024-06-28 04:23:22'),
(650, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/user-report-submit', 'user_1 user report submitted by user_1', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:30:59', '2024-06-28 04:30:59'),
(651, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:35:18', '2024-06-28 04:35:18'),
(652, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:42:01', '2024-06-28 04:42:01'),
(653, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:42:19', '2024-06-28 04:42:19'),
(654, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:42:33', '2024-06-28 04:42:33'),
(655, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:45:11', '2024-06-28 04:45:11'),
(656, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:45:15', '2024-06-28 04:45:15'),
(657, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:45:20', '2024-06-28 04:45:20'),
(658, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:45:25', '2024-06-28 04:45:25'),
(659, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:45:31', '2024-06-28 04:45:31'),
(660, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:45:49', '2024-06-28 04:45:49'),
(661, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:49:50', '2024-06-28 04:49:50'),
(662, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:56:48', '2024-06-28 04:56:48'),
(663, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 04:59:46', '2024-06-28 04:59:46'),
(664, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitpurchasereturn', 'user_1 purchase returned', 'India', 'Kerala', 'Kozhikode', '2024-06-28 05:07:27', '2024-06-28 05:07:27'),
(665, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-28 09:28:44', '2024-06-28 09:28:44'),
(666, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 09:28:50', '2024-06-28 09:28:50'),
(667, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 09:29:12', '2024-06-28 09:29:12'),
(668, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 09:29:20', '2024-06-28 09:29:20'),
(669, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 09:30:13', '2024-06-28 09:30:13'),
(670, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 09:32:56', '2024-06-28 09:32:56'),
(671, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 09:35:55', '2024-06-28 09:35:55'),
(672, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 09:37:36', '2024-06-28 09:37:36'),
(673, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-28 09:38:07', '2024-06-28 09:38:07'),
(674, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-29 02:26:09', '2024-06-29 02:26:09'),
(675, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 02:26:19', '2024-06-29 02:26:19'),
(676, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 02:26:30', '2024-06-29 02:26:30'),
(677, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 02:26:40', '2024-06-29 02:26:40'),
(678, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 02:28:39', '2024-06-29 02:28:39'),
(679, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/userlogout', 'user_1 logged out', 'India', 'Kerala', 'Kozhikode', '2024-06-29 03:05:44', '2024-06-29 03:05:44'),
(680, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-29 03:05:47', '2024-06-29 03:05:47'),
(681, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/userlogout', 'user_1 logged out', 'India', 'Kerala', 'Kozhikode', '2024-06-29 03:05:51', '2024-06-29 03:05:51'),
(682, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'admin logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-29 03:23:19', '2024-06-29 03:23:19'),
(683, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/listuser', 'admin visited list user page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 03:23:22', '2024-06-29 03:23:22'),
(684, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/adminlogout', 'admin logged out', 'India', 'Kerala', 'Kozhikode', '2024-06-29 03:23:28', '2024-06-29 03:23:28'),
(685, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-29 03:23:33', '2024-06-29 03:23:33'),
(686, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 03:23:50', '2024-06-29 03:23:50'),
(687, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 03:24:00', '2024-06-29 03:24:00'),
(688, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 03:24:07', '2024-06-29 03:24:07'),
(689, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 03:24:13', '2024-06-29 03:24:13'),
(690, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/return', 'user_1 visited product return page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 03:24:22', '2024-06-29 03:24:22'),
(691, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 03:24:25', '2024-06-29 03:24:25'),
(692, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 03:24:33', '2024-06-29 03:24:33'),
(693, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/purchaseorder_submit', 'user_1 done purchase order', 'India', 'Kerala', 'Kozhikode', '2024-06-29 03:25:33', '2024-06-29 03:25:33'),
(694, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 03:31:18', '2024-06-29 03:31:18'),
(695, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'admin logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-29 04:18:31', '2024-06-29 04:18:31'),
(696, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/p_and_l_report', 'admin visited P & L report', 'India', 'Kerala', 'Kozhikode', '2024-06-29 04:18:36', '2024-06-29 04:18:36'),
(697, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/userlogout', 'user_1 logged out', 'India', 'Kerala', 'Kozhikode', '2024-06-29 04:27:24', '2024-06-29 04:27:24'),
(698, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-29 04:27:41', '2024-06-29 04:27:41'),
(699, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 04:29:49', '2024-06-29 04:29:49'),
(700, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/salesorder_submit', 'user_1 done sales order BTSLS8', 'India', 'Kerala', 'Kozhikode', '2024-06-29 04:32:14', '2024-06-29 04:32:14'),
(701, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitstock_table', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-29 04:35:27', '2024-06-29 04:35:27'),
(702, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/to_invoice/sales_order/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-29 04:39:12', '2024-06-29 04:39:12'),
(703, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/edittransactiondetails/edit_bill/editsubmitdata', 'user_1 done BT32 editing - cv', 'India', 'Kerala', 'Kozhikode', '2024-06-29 04:43:43', '2024-06-29 04:43:43'),
(704, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 05:16:27', '2024-06-29 05:16:27'),
(705, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/edit_purchasedetails/edit_purchase/submit_editpurchase', 'user_1 done Purchase dfgh editing - rtgrtg', 'India', 'Kerala', 'Kozhikode', '2024-06-29 05:21:25', '2024-06-29 05:21:25'),
(706, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 05:44:09', '2024-06-29 05:44:09'),
(707, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitstock_table', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-29 06:02:27', '2024-06-29 06:02:27'),
(708, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/purchaseorder_submit', 'user_1 done purchase order', 'India', 'Kerala', 'Kozhikode', '2024-06-29 06:03:26', '2024-06-29 06:03:26'),
(709, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/changeproductstatus/2', 'user_1 disabled p2', 'India', 'Kerala', 'Kozhikode', '2024-06-29 07:38:48', '2024-06-29 07:38:48'),
(710, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/changeproductstatus/2', 'user_1 enabled p2', 'India', 'Kerala', 'Kozhikode', '2024-06-29 07:39:20', '2024-06-29 07:39:20'),
(711, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/changeproductstatus/1', 'user_1 enabled p1', 'India', 'Kerala', 'Kozhikode', '2024-06-29 07:39:22', '2024-06-29 07:39:22'),
(712, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/to_purchase/%7B%20purchase_order%7D/submitstock_table', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-29 09:01:26', '2024-06-29 09:01:26'),
(713, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/edit_purchasedetails/%7B%20edit_purchase%20%7D/submit_editpurchase', 'user_1 done Purchase 4265672576 editing - yhtyhth', 'India', 'Kerala', 'Kozhikode', '2024-06-29 09:04:53', '2024-06-29 09:04:53'),
(714, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/to_purchase/%7B%20purchase_order%7D/submitstock_table', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-29 09:10:05', '2024-06-29 09:10:05'),
(715, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/to_purchase/%7B%20purchase_order%7D/submitstock_table', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-29 09:12:46', '2024-06-29 09:12:46'),
(716, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/edit_purchasedetails/%7B%20edit_purchase%20%7D/submit_editpurchase', 'user_1 done Purchase 4265672576 editing - rtghtyh', 'India', 'Kerala', 'Kozhikode', '2024-06-29 09:17:45', '2024-06-29 09:17:45'),
(717, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/to_purchase/%7B%20purchase_order%7D/submitstock_table', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-29 09:18:58', '2024-06-29 09:18:58'),
(718, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/to_purchase/%7B%20purchase_order%7D/submitstock_table', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-29 09:21:54', '2024-06-29 09:21:54'),
(719, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/purchaseorder_submit', 'user_1 done purchase order', 'India', 'Kerala', 'Kozhikode', '2024-06-29 09:27:00', '2024-06-29 09:27:00'),
(720, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/to_purchase/%7B%20purchase_order%7D/submitstock_table', 'user_1 Purchased Stock', 'India', 'Kerala', 'Kozhikode', '2024-06-29 09:27:24', '2024-06-29 09:27:24'),
(721, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 09:54:06', '2024-06-29 09:54:06'),
(722, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 09:54:14', '2024-06-29 09:54:14'),
(723, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 09:54:22', '2024-06-29 09:54:22'),
(724, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 09:54:41', '2024-06-29 09:54:41'),
(725, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 09:56:02', '2024-06-29 09:56:02'),
(726, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 09:56:35', '2024-06-29 09:56:35'),
(727, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 09:57:13', '2024-06-29 09:57:13'),
(728, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 09:57:56', '2024-06-29 09:57:56'),
(729, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-29 09:59:33', '2024-06-29 09:59:33'),
(730, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 10:00:52', '2024-06-29 10:00:52'),
(731, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 10:02:58', '2024-06-29 10:02:58'),
(732, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-29 10:03:16', '2024-06-29 10:03:16'),
(733, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 10:03:35', '2024-06-29 10:03:35'),
(734, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-29 10:03:54', '2024-06-29 10:03:54'),
(735, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 10:04:22', '2024-06-29 10:04:22'),
(736, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitdata', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-06-29 10:07:22', '2024-06-29 10:07:22'),
(737, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/addfundcredit2', 'user_1 added collection payment to credit user credit1', 'India', 'Kerala', 'Kozhikode', '2024-06-29 10:10:59', '2024-06-29 10:10:59'),
(738, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/return', 'user_1 visited product return page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 10:11:26', '2024-06-29 10:11:26'),
(739, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 10:15:33', '2024-06-29 10:15:33'),
(740, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 10:17:18', '2024-06-29 10:17:18'),
(741, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 10:17:29', '2024-06-29 10:17:29'),
(742, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 10:18:58', '2024-06-29 10:18:58'),
(743, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 10:20:10', '2024-06-29 10:20:10'),
(744, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 10:21:51', '2024-06-29 10:21:51'),
(745, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 10:21:57', '2024-06-29 10:21:57'),
(746, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/salesorder_submit', 'user_1 done Quotation BTQUOT8', 'India', 'Kerala', 'Kozhikode', '2024-06-29 10:22:04', '2024-06-29 10:22:04'),
(747, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 10:22:06', '2024-06-29 10:22:06'),
(748, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 10:22:16', '2024-06-29 10:22:16'),
(749, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/userlogout', 'user_1 logged out', 'India', 'Kerala', 'Kozhikode', '2024-06-29 10:22:24', '2024-06-29 10:22:24'),
(750, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'admin logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-29 10:22:28', '2024-06-29 10:22:28'),
(751, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/p_and_l_report', 'admin visited P & L report', 'India', 'Kerala', 'Kozhikode', '2024-06-29 10:22:32', '2024-06-29 10:22:32'),
(752, NULL, 1, NULL, NULL, 1, 0, 0, '127.0.0.1', 'http://127.0.0.1:8000/adminlogout', 'admin logged out', 'India', 'Kerala', 'Kozhikode', '2024-06-29 10:22:56', '2024-06-29 10:22:56'),
(753, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-06-29 10:23:02', '2024-06-29 10:23:02'),
(754, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/purchaseorder_submit', 'user_1 done purchase order', 'India', 'Kerala', 'Kozhikode', '2024-06-29 10:26:01', '2024-06-29 10:26:01'),
(755, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-06-29 10:31:28', '2024-06-29 10:31:28'),
(756, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-07-01 02:22:03', '2024-07-01 02:22:03'),
(757, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 02:39:54', '2024-07-01 02:39:54'),
(758, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 02:40:21', '2024-07-01 02:40:21'),
(759, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 02:41:40', '2024-07-01 02:41:40');
INSERT INTO `activities` (`id`, `user_id`, `admin_id`, `credituser_id`, `branch_id`, `is_admin`, `is_user`, `is_credituser`, `ipaddress`, `url`, `message`, `countryName`, `regionName`, `cityName`, `created_at`, `updated_at`) VALUES
(760, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 02:42:35', '2024-07-01 02:42:35'),
(761, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 02:42:50', '2024-07-01 02:42:50'),
(762, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 02:43:00', '2024-07-01 02:43:00'),
(763, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 02:44:42', '2024-07-01 02:44:42'),
(764, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/return', 'user_1 visited product return page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 02:44:49', '2024-07-01 02:44:49'),
(765, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/return', 'user_1 visited product return page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 02:45:43', '2024-07-01 02:45:43'),
(766, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/return', 'user_1 visited product return page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 02:46:27', '2024-07-01 02:46:27'),
(767, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 02:46:52', '2024-07-01 02:46:52'),
(768, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 02:47:58', '2024-07-01 02:47:58'),
(769, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 02:48:24', '2024-07-01 02:48:24'),
(770, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 02:49:18', '2024-07-01 02:49:18'),
(771, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 02:56:40', '2024-07-01 02:56:40'),
(772, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 02:56:48', '2024-07-01 02:56:48'),
(773, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 02:56:52', '2024-07-01 02:56:52'),
(774, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 02:58:01', '2024-07-01 02:58:01'),
(775, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 02:58:16', '2024-07-01 02:58:16'),
(776, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 02:58:22', '2024-07-01 02:58:22'),
(777, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 02:58:32', '2024-07-01 02:58:32'),
(778, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 03:00:03', '2024-07-01 03:00:03'),
(779, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 03:00:26', '2024-07-01 03:00:26'),
(780, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 03:01:01', '2024-07-01 03:01:01'),
(781, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 03:13:52', '2024-07-01 03:13:52'),
(782, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 03:14:24', '2024-07-01 03:14:24'),
(783, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/salesorder_submit', 'user_1 done performance_invoice BTPI5', 'India', 'Kerala', 'Kozhikode', '2024-07-01 03:14:29', '2024-07-01 03:14:29'),
(784, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 03:14:34', '2024-07-01 03:14:34'),
(785, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 03:14:40', '2024-07-01 03:14:40'),
(786, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 03:17:57', '2024-07-01 03:17:57'),
(787, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 03:19:42', '2024-07-01 03:19:42'),
(788, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 03:21:21', '2024-07-01 03:21:21'),
(789, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 03:21:27', '2024-07-01 03:21:27'),
(790, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 03:23:05', '2024-07-01 03:23:05'),
(791, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 03:24:30', '2024-07-01 03:24:30'),
(792, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 03:27:15', '2024-07-01 03:27:15'),
(793, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 03:27:18', '2024-07-01 03:27:18'),
(794, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 03:27:38', '2024-07-01 03:27:38'),
(795, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/delivery_note', 'user_1 visited delivery note page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 03:27:42', '2024-07-01 03:27:42'),
(796, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/performance_invoice', 'user_1 visited performance_invoice page', 'India', 'Kerala', 'Kozhikode', '2024-07-01 03:27:55', '2024-07-01 03:27:55'),
(797, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-07-01 03:37:40', '2024-07-01 03:37:40'),
(798, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-07-02 02:25:44', '2024-07-02 02:25:44'),
(799, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-07-02 02:25:50', '2024-07-02 02:25:50'),
(800, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/superuseruser', 'user_1 logged in', 'India', 'Kerala', 'Kozhikode', '2024-07-02 02:33:49', '2024-07-02 02:33:49'),
(801, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-07-02 02:36:10', '2024-07-02 02:36:10'),
(802, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-07-02 02:36:20', '2024-07-02 02:36:20'),
(803, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-07-02 02:36:32', '2024-07-02 02:36:32'),
(804, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-07-02 02:36:45', '2024-07-02 02:36:45'),
(805, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/submitdatadraft/edit/Draft6', 'user_1 done product billing', 'India', 'Kerala', 'Kozhikode', '2024-07-02 02:36:53', '2024-07-02 02:36:53'),
(806, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-07-02 02:37:34', '2024-07-02 02:37:34'),
(807, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/dashboard', 'user_1 visited billing page', 'India', 'Kerala', 'Kozhikode', '2024-07-02 02:38:32', '2024-07-02 02:38:32'),
(808, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-07-02 02:38:40', '2024-07-02 02:38:40'),
(809, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-07-02 02:38:43', '2024-07-02 02:38:43'),
(810, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-07-02 03:09:15', '2024-07-02 03:09:15'),
(811, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-07-02 03:09:17', '2024-07-02 03:09:17'),
(812, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-07-02 03:31:05', '2024-07-02 03:31:05'),
(813, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/quotation', 'user_1 visited Quotation page', 'India', 'Kerala', 'Kozhikode', '2024-07-02 03:31:14', '2024-07-02 03:31:14'),
(814, 1, NULL, NULL, 1, 0, 1, 0, '127.0.0.1', 'http://127.0.0.1:8000/sales_order', 'user_1 visited sales order page', 'India', 'Kerala', 'Kozhikode', '2024-07-02 03:45:41', '2024-07-02 03:45:41');

-- --------------------------------------------------------

--
-- Table structure for table `addstocks`
--

CREATE TABLE `addstocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quantity` decimal(18,3) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `adminusers`
--

CREATE TABLE `adminusers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `currency` varchar(255) NOT NULL,
  `po_box` varchar(255) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `cr_number` varchar(255) DEFAULT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 - enabled, 0- disabled',
  `transpart` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `logo` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `adminusers`
--

INSERT INTO `adminusers` (`id`, `name`, `username`, `password`, `email`, `phone`, `currency`, `po_box`, `postal_code`, `cr_number`, `location`, `address`, `created_at`, `updated_at`, `status`, `transpart`, `logo`) VALUES
(1, 'admin', 'admin', '$2y$10$YltOm6DbgwoD869gZdZZF.DDR8vjAAHnAQhUrrRE.GGYoNmAcwnMS', 'abc@gmail.com', '897457646410', 'AED', '30999', '870158', '545', 'kannur', '5trgytrg', '2024-06-21 04:06:51', '2024-06-21 04:06:51', 1, 'BT', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `balance_stores`
--

CREATE TABLE `balance_stores` (
  `id` int(11) NOT NULL,
  `transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int(255) NOT NULL,
  `branch` int(255) NOT NULL,
  `old_tran_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `returnstatus` int(11) DEFAULT NULL COMMENT '0-no return, 1- return',
  `balance` decimal(18,2) DEFAULT NULL,
  `extra_give` decimal(18,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `billdraft`
--

CREATE TABLE `billdraft` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `product_id` int(255) DEFAULT NULL,
  `quantity` decimal(18,3) DEFAULT NULL,
  `remain_quantity` decimal(18,3) NOT NULL DEFAULT 0.000,
  `unit` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `one_pro_buycost` decimal(18,3) DEFAULT NULL,
  `one_pro_buycost_rate` decimal(18,3) DEFAULT NULL,
  `inclusive_rate` decimal(18,3) DEFAULT NULL,
  `exclusive_rate` decimal(18,3) DEFAULT NULL,
  `mrp` decimal(18,3) DEFAULT NULL,
  `price` decimal(18,3) DEFAULT NULL,
  `price_wo_discount` decimal(18,3) DEFAULT NULL,
  `vat_amount` decimal(18,3) DEFAULT NULL,
  `fixed_vat` int(11) DEFAULT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `trn_number` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `payment_type` int(255) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `netrate` decimal(18,3) DEFAULT NULL,
  `total_amount` decimal(18,3) DEFAULT NULL,
  `totalamount_wo_discount` decimal(18,3) DEFAULT NULL,
  `discount_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'none, percentage, amount',
  `discount` decimal(18,3) DEFAULT NULL,
  `discount_amount` decimal(18,3) DEFAULT NULL,
  `buycostadd` decimal(18,3) DEFAULT NULL,
  `buycost_rate_add` decimal(18,3) DEFAULT NULL,
  `credit_user_id` int(255) DEFAULT NULL,
  `amount` decimal(18,3) DEFAULT NULL,
  `cash_user_id` int(255) DEFAULT NULL,
  `vat_type` int(255) DEFAULT NULL COMMENT '1-inclusive, 2- exclusive',
  `total_discount_type` int(11) DEFAULT NULL COMMENT '0 - none, 1- percentage, 2 -amount',
  `total_discount_percent` decimal(18,3) DEFAULT NULL,
  `total_discount_amount` decimal(18,3) DEFAULT NULL,
  `bill_grand_total` decimal(18,3) DEFAULT NULL,
  `bill_grand_total_wo_discount` decimal(18,3) DEFAULT NULL,
  `edit` int(11) DEFAULT NULL COMMENT '1- edited',
  `edit_comment` varchar(255) DEFAULT NULL,
  `to_invoice` int(11) DEFAULT NULL COMMENT '1- sales order, 2-quotation',
  `sales_order_trans_ID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `billdraft`
--

INSERT INTO `billdraft` (`id`, `product_name`, `product_id`, `quantity`, `remain_quantity`, `unit`, `one_pro_buycost`, `one_pro_buycost_rate`, `inclusive_rate`, `exclusive_rate`, `mrp`, `price`, `price_wo_discount`, `vat_amount`, `fixed_vat`, `branch`, `transaction_id`, `customer_name`, `trn_number`, `phone`, `payment_type`, `created_at`, `updated_at`, `user_id`, `email`, `netrate`, `total_amount`, `totalamount_wo_discount`, `discount_type`, `discount`, `discount_amount`, `buycostadd`, `buycost_rate_add`, `credit_user_id`, `amount`, `cash_user_id`, `vat_type`, `total_discount_type`, `total_discount_percent`, `total_discount_amount`, `bill_grand_total`, `bill_grand_total_wo_discount`, `edit`, `edit_comment`, `to_invoice`, `sales_order_trans_ID`) VALUES
(3, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 14.286, NULL, 15.000, 14.286, 14.286, 0.714, 5, '1', 'Draft1', '214054', NULL, NULL, 1, '2024-06-23 06:57:05', '2024-06-23 06:57:05', 1, NULL, 15.000, 15.000, 15.000, 'none', 0.000, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0.000, 0.000, 45.000, 45.000, NULL, NULL, NULL, NULL),
(4, 'p2', 2, 1.000, 1.000, 'kg', 20.000, 21.000, 28.571, NULL, 30.000, 28.571, 28.571, 1.429, 5, '1', 'Draft1', '214054', NULL, NULL, 1, '2024-06-23 06:57:05', '2024-06-23 06:57:05', 1, NULL, 30.000, 30.000, 30.000, 'none', 0.000, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0.000, 0.000, 45.000, 45.000, NULL, NULL, NULL, NULL),
(5, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 14.286, NULL, 15.000, 14.286, 14.286, 0.714, 5, '1', 'Draft2', '478616', NULL, NULL, 1, '2024-06-23 07:01:25', '2024-06-23 07:01:25', 1, NULL, 15.000, 15.000, 15.000, 'none', 0.000, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0.000, 0.000, 15.000, 15.000, NULL, NULL, NULL, NULL),
(6, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 14.286, NULL, 15.000, 14.286, 14.286, 0.714, 5, '1', 'Draft3', 'credit1', NULL, NULL, 3, '2024-07-01 07:32:00', '2024-07-01 07:32:00', 1, NULL, 15.000, 15.000, 15.000, 'none', 0.000, NULL, NULL, NULL, 1, 4.000, NULL, 1, 0, 0.000, 0.000, 15.000, 15.000, NULL, NULL, NULL, NULL),
(7, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 14.286, NULL, 15.000, 14.286, 14.286, 0.714, 5, '1', 'Draft4', '449702', NULL, NULL, 2, '2024-07-01 07:47:41', '2024-07-01 07:47:41', 1, NULL, 15.000, 15.000, 15.000, 'none', 0.000, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0.000, 0.000, 15.000, 15.000, NULL, NULL, NULL, NULL),
(8, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 14.286, NULL, 15.000, 14.286, 14.286, 0.714, 5, '1', 'Draft5', '524613', NULL, NULL, 4, '2024-07-01 07:48:59', '2024-07-01 07:48:59', 1, NULL, 15.000, 15.000, 15.000, 'none', 0.000, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0.000, 0.000, 15.000, 15.000, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bill_histories`
--

CREATE TABLE `bill_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `trans_id` varchar(255) NOT NULL,
  `product_id` int(255) NOT NULL,
  `puid` varchar(255) NOT NULL,
  `pid` int(255) NOT NULL,
  `receipt_no` varchar(255) DEFAULT NULL,
  `sold_quantity` decimal(18,3) NOT NULL,
  `remain_sold_quantity` decimal(18,3) DEFAULT NULL,
  `branch_id` int(255) NOT NULL,
  `user_id` int(255) NOT NULL,
  `Purchase_buycost` decimal(18,3) NOT NULL,
  `Purchase_Buycost_Rate` decimal(18,3) DEFAULT NULL,
  `billing_Sellingcost` decimal(18,3) DEFAULT NULL,
  `billing_inclusive_rate` decimal(18,3) DEFAULT NULL,
  `billing_exclusive_rate` decimal(18,3) DEFAULT NULL,
  `netrate` decimal(18,3) DEFAULT NULL,
  `discount_type` varchar(30) DEFAULT NULL COMMENT 'none, percentage, amount',
  `discount` decimal(18,3) DEFAULT NULL,
  `discount_amount` decimal(18,3) DEFAULT NULL,
  `return_discount` decimal(18,3) DEFAULT NULL,
  `return_discount_amount` decimal(18,3) DEFAULT NULL,
  `total_discount_type` int(11) DEFAULT NULL COMMENT '0 - none, 1- percentage, 2 -amount',
  `total_discount_percent` decimal(18,3) DEFAULT NULL,
  `total_discount_amount` decimal(18,3) DEFAULT NULL,
  `bill_grand_total` decimal(18,3) DEFAULT NULL,
  `bill_grand_total_wo_discount` decimal(18,3) DEFAULT NULL,
  `return_total_discount_percent` decimal(18,3) DEFAULT NULL,
  `return_total_discount_amt` decimal(18,3) DEFAULT NULL,
  `return_grand_total` decimal(18,3) DEFAULT NULL,
  `return_grand_total_wo_discount` decimal(18,3) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bill_histories`
--

INSERT INTO `bill_histories` (`id`, `trans_id`, `product_id`, `puid`, `pid`, `receipt_no`, `sold_quantity`, `remain_sold_quantity`, `branch_id`, `user_id`, `Purchase_buycost`, `Purchase_Buycost_Rate`, `billing_Sellingcost`, `billing_inclusive_rate`, `billing_exclusive_rate`, `netrate`, `discount_type`, `discount`, `discount_amount`, `return_discount`, `return_discount_amount`, `total_discount_type`, `total_discount_percent`, `total_discount_amount`, `bill_grand_total`, `bill_grand_total_wo_discount`, `return_total_discount_percent`, `return_total_discount_amt`, `return_grand_total`, `return_grand_total_wo_discount`, `created_at`, `updated_at`) VALUES
(1, 'BT1', 1, 'PID11111', 1, '122121', 3.000, 1.000, 1, 1, 10.000, 10.500, 15.000, 14.250, NULL, 14.250, 'percentage', 5.000, 0.750, 5.000, 0.750, 2, 3.503, 5.000, 137.750, 142.750, 3.500, 2.251, 62.000, 64.250, '2024-06-21 05:23:33', '2024-06-21 05:27:40'),
(2, 'BT1', 2, 'PID11212', 2, '122121', 4.000, 2.000, 1, 1, 20.000, 21.000, 30.000, 25.000, NULL, 25.000, 'amount', 16.670, 5.000, 16.670, 10.002, 2, 3.503, 5.000, 137.750, 142.750, 3.500, 2.251, 62.000, 64.250, '2024-06-21 05:23:33', '2024-06-21 05:27:40'),
(3, 'BT2', 2, 'PID11212', 2, '122121', 4.000, 2.000, 1, 1, 20.000, 21.000, 30.000, 25.000, NULL, 25.000, 'amount', 16.667, 5.000, 16.667, 10.000, 0, 0.000, 0.000, 100.000, 100.000, 0.000, 0.000, 50.000, 50.000, '2024-06-21 08:37:05', '2024-06-21 08:37:32'),
(4, 'BT3', 3, 'PID22331', 3, 'tyhyt', 2.000, 1.000, 2, 2, 10.000, 10.400, 20.000, 19.000, NULL, 19.000, 'percentage', 5.000, 1.000, 5.000, 1.000, 1, 5.000, 1.900, 36.100, 38.000, 5.000, 0.950, 18.050, 19.000, '2024-06-21 08:57:04', '2024-06-21 08:57:39'),
(5, 'BT4', 1, 'PID11111', 1, '122121', 2.000, 1.000, 1, 3, 10.000, 10.500, 15.000, 14.550, NULL, 14.550, 'percentage', 3.000, 0.450, 3.000, 0.450, 0, 0.000, 0.000, 29.100, 29.100, 0.000, 0.000, 14.550, 14.550, '2024-06-21 08:58:03', '2024-06-21 08:58:24'),
(6, 'BT5', 1, 'PID11111', 1, '122121', 1.000, 1.000, 1, 1, 10.000, 10.500, 15.000, 14.286, NULL, 15.000, 'none', 0.000, NULL, NULL, NULL, 0, 0.000, 0.000, 15.000, 15.000, NULL, NULL, NULL, NULL, '2024-06-22 05:00:30', '2024-06-22 05:00:30'),
(7, 'BT6', 1, 'PID11111', 1, '122121', 2.000, 2.000, 1, 1, 10.000, 10.500, 15.000, 14.250, NULL, 14.250, 'percentage', 5.000, 0.750, NULL, NULL, 2, 14.035, 4.000, 24.500, 28.500, NULL, NULL, NULL, NULL, '2024-06-22 05:12:55', '2024-06-22 05:12:55'),
(8, 'BT7', 2, 'PID11212', 2, '122121', 1.000, 1.000, 1, 1, 20.000, 21.000, 20.000, 15.000, NULL, 15.000, 'amount', 25.000, 5.000, NULL, NULL, 0, 0.000, 0.000, 15.000, 15.000, NULL, NULL, NULL, NULL, '2024-06-22 05:30:34', '2024-06-22 05:30:34'),
(9, 'BT8', 2, 'PID11212', 2, '122121', 1.000, 1.000, 1, 1, 20.000, 21.000, 30.000, 25.000, NULL, 25.000, 'amount', 16.667, 5.000, NULL, NULL, 1, 5.000, 1.250, 23.750, 25.000, NULL, NULL, NULL, NULL, '2024-06-22 05:33:10', '2024-06-22 05:33:10'),
(10, 'BT9', 1, 'PID11141', 4, 'grtgtrg', 2.000, 2.000, 1, 1, 10.000, 10.500, 15.000, 10.000, NULL, 10.000, 'amount', 33.333, 5.000, NULL, NULL, 2, 30.000, 6.000, 14.000, 20.000, NULL, NULL, NULL, NULL, '2024-06-22 06:45:34', '2024-06-22 06:45:34'),
(11, 'BT10', 1, 'PID11141', 4, 'grtgtrg', 1.000, 1.000, 1, 1, 10.000, 10.500, 15.000, 14.286, NULL, 15.000, 'none', 0.000, NULL, NULL, NULL, 0, 0.000, 0.000, 15.000, 15.000, NULL, NULL, NULL, NULL, '2024-06-22 08:25:56', '2024-06-22 08:25:56'),
(12, 'BT11', 1, 'PID11141', 4, 'grtgtrg', 2.000, 2.000, 1, 1, 10.000, 10.500, 15.000, 14.250, NULL, 14.250, 'percentage', 5.000, 0.750, NULL, NULL, 0, 0.000, 0.000, 28.500, 28.500, NULL, NULL, NULL, NULL, '2024-06-22 08:27:59', '2024-06-22 08:27:59'),
(13, 'BT12', 1, 'PID11141', 4, 'grtgtrg', 2.000, 2.000, 1, 1, 10.000, 10.500, 15.000, NULL, 14.700, 15.435, 'percentage', 2.000, 0.300, NULL, NULL, 0, 0.000, 0.000, 30.870, 30.870, NULL, NULL, NULL, NULL, '2024-06-22 08:29:15', '2024-06-22 08:29:15'),
(14, 'BT13', 1, 'PID11141', 4, 'grtgtrg', 1.000, 1.000, 1, 1, 10.000, 10.500, 15.000, 14.250, NULL, 14.250, 'percentage', 5.000, 0.750, NULL, NULL, 0, 0.000, 0.000, 14.250, 14.250, NULL, NULL, NULL, NULL, '2024-06-22 08:30:45', '2024-06-22 08:30:45'),
(15, 'BT14', 1, 'PID11141', 4, 'grtgtrg', 1.000, 1.000, 1, 1, 10.000, 10.500, 15.000, NULL, 14.250, 14.963, 'percentage', 5.000, 0.750, NULL, NULL, 0, 0.000, 0.000, 14.960, 14.960, NULL, NULL, NULL, NULL, '2024-06-22 08:31:43', '2024-06-22 08:31:43'),
(16, 'BT15', 1, 'PID11141', 4, 'grtgtrg', 1.000, 1.000, 1, 1, 10.000, 10.500, 15.000, 10.000, NULL, 10.000, 'amount', 33.333, 5.000, NULL, NULL, 2, 50.000, 5.000, 5.000, 10.000, NULL, NULL, NULL, NULL, '2024-06-23 05:42:25', '2024-06-23 05:42:25'),
(17, 'BT16', 1, 'PID11171', 7, '111', 1.000, 1.000, 1, 1, 10.000, 10.500, 15.000, 10.000, NULL, 10.000, 'amount', 33.333, 5.000, NULL, NULL, 2, 14.286, 5.000, 30.000, 35.000, NULL, NULL, NULL, NULL, '2024-06-23 06:49:11', '2024-06-23 06:49:11'),
(18, 'BT16', 2, 'PID11242', 5, 'grtgtrg', 1.000, 1.000, 1, 1, 20.000, 21.000, 30.000, 25.000, NULL, 25.000, 'amount', 16.667, 5.000, NULL, NULL, 2, 14.286, 5.000, 30.000, 35.000, NULL, NULL, NULL, NULL, '2024-06-23 06:49:11', '2024-06-23 06:49:11'),
(19, 'BT17', 6, 'PID116141', 15, '111e', 2.000, 2.000, 1, 1, 10.000, 11.000, 20.000, 19.000, NULL, 19.000, 'amount', 5.000, 1.000, NULL, NULL, 0, 0.000, 0.000, 76.000, 76.000, NULL, NULL, NULL, NULL, '2024-06-25 09:54:50', '2024-06-25 09:57:20'),
(20, 'BT17', 6, 'PID116151', 16, 'sadsd', 2.000, 2.000, 1, 1, 10.000, 11.000, 20.000, 19.000, NULL, 19.000, 'amount', 1.000, 0.380, NULL, NULL, 0, 0.000, 0.000, 76.000, 76.000, NULL, NULL, NULL, NULL, '2024-06-25 09:54:50', '2024-06-25 09:57:20'),
(21, 'BT18', 1, 'PID11171', 7, '111', 1.000, 1.000, 1, 1, 10.000, 10.500, 15.000, 14.286, NULL, 15.000, 'none', 0.000, 0.000, NULL, NULL, 0, 0.000, 0.000, 15.000, 15.000, NULL, NULL, NULL, NULL, '2024-06-27 03:38:10', '2024-06-27 03:38:10'),
(22, 'BT19', 5, 'PID115191', 20, '14', 1.000, 1.000, 1, 1, 100.000, 110.000, 1000.000, 997.000, NULL, 997.000, 'amount', 0.300, 3.000, NULL, NULL, 2, 0.401, 4.000, 993.000, 997.000, NULL, NULL, NULL, NULL, '2024-06-27 08:34:20', '2024-06-27 08:34:20'),
(23, 'BT20', 5, 'PID115191', 20, '14', 1.000, 1.000, 1, 1, 100.000, 110.000, 1000.000, 997.000, NULL, 997.000, 'amount', 0.300, 3.000, NULL, NULL, 2, 0.401, 4.000, 993.000, 997.000, NULL, NULL, NULL, NULL, '2024-06-27 08:35:43', '2024-06-27 08:35:43'),
(24, 'BT21', 1, 'PID11171', 7, '111', 1.000, 1.000, 1, 1, 10.000, 10.500, 15.000, 14.286, NULL, 15.000, 'none', 0.000, NULL, NULL, NULL, 0, 0.000, 0.000, 15.000, 15.000, NULL, NULL, NULL, NULL, '2024-06-28 02:27:38', '2024-06-28 02:27:38'),
(25, 'BT22', 6, 'PID116151', 16, 'sadsd', 1.000, 1.000, 1, 1, 10.000, 11.000, 20.000, 15.000, NULL, 15.000, 'amount', 25.000, 5.000, NULL, NULL, 2, 6.667, 1.000, 14.000, 15.000, NULL, NULL, NULL, NULL, '2024-06-28 03:04:44', '2024-06-28 03:11:30'),
(26, 'BT23', 6, 'PID116181', 19, '13', 1.000, 1.000, 1, 1, 10.000, 11.000, 20.000, 15.000, NULL, 15.000, 'amount', 25.000, 5.000, NULL, NULL, 2, 6.667, 1.000, 14.000, 15.000, NULL, NULL, NULL, NULL, '2024-06-28 03:09:12', '2024-06-28 03:09:12'),
(27, 'BT24', 1, 'PID11171', 7, '111', 1.000, 1.000, 1, 1, 10.000, 10.500, 15.000, 14.286, NULL, 15.000, 'none', 0.000, 0.000, NULL, NULL, 0, 0.000, 0.000, 15.000, 15.000, NULL, NULL, NULL, NULL, '2024-06-28 03:13:11', '2024-06-28 03:22:52'),
(28, 'BT25', 2, 'PID11242', 5, 'grtgtrg', 1.000, 1.000, 1, 1, 20.000, 21.000, 30.000, 27.000, NULL, 27.000, 'amount', 10.000, 3.000, NULL, NULL, 1, 5.000, 1.350, 25.650, 27.000, NULL, NULL, NULL, NULL, '2024-06-28 03:23:36', '2024-06-28 03:24:06'),
(29, 'BT26', 1, 'PID11171', 7, '111', 1.000, 1.000, 1, 1, 10.000, 10.500, 15.000, 10.000, NULL, 10.000, 'amount', 33.333, 5.000, NULL, NULL, 2, 10.000, 1.000, 9.000, 10.000, NULL, NULL, NULL, NULL, '2024-06-28 03:45:31', '2024-06-28 03:49:50'),
(30, 'BT27', 1, 'PID11171', 7, '111', 1.000, 1.000, 1, 1, 10.000, 10.500, 15.000, 10.000, NULL, 10.000, 'amount', 33.333, 5.000, NULL, NULL, 2, 10.000, 1.000, 9.000, 10.000, NULL, NULL, NULL, NULL, '2024-06-28 03:54:15', '2024-06-28 03:54:15'),
(31, 'BT28', 1, 'PID11171', 7, '111', 1.000, 1.000, 1, 1, 10.000, 10.500, 15.000, 10.000, NULL, 10.000, 'amount', 33.333, 5.000, NULL, NULL, 0, 0.000, 0.000, 10.000, 10.000, NULL, NULL, NULL, NULL, '2024-06-28 03:55:59', '2024-06-28 03:55:59'),
(32, 'BT29', 5, 'PID115191', 20, '14', 1.000, 1.000, 1, 1, 100.000, 110.000, 1000.000, 960.000, NULL, 960.000, 'percentage', 4.000, 40.000, NULL, NULL, 1, 5.000, 145.350, 2761.650, 2907.000, NULL, NULL, NULL, NULL, '2024-06-28 04:14:57', '2024-06-28 04:16:51'),
(33, 'BT29', 5, 'PID115211', 22, '141', 2.000, 2.000, 1, 1, 100.000, 110.000, 1000.000, 960.000, NULL, 960.000, 'percentage', 4.000, 40.000, NULL, NULL, 1, 5.000, 145.350, 2761.650, 2907.000, NULL, NULL, NULL, NULL, '2024-06-28 04:14:57', '2024-06-28 04:16:51'),
(34, 'BT29', 2, 'PID11242', 5, 'grtgtrg', 1.000, 1.000, 1, 1, 20.000, 21.000, 30.000, 27.000, NULL, 27.000, 'amount', 10.000, 3.000, NULL, NULL, 1, 5.000, 145.350, 2761.650, 2907.000, NULL, NULL, NULL, NULL, '2024-06-28 04:14:57', '2024-06-28 04:16:51'),
(35, 'BT30', 6, 'PID116181', 19, '13', 1.000, 1.000, 1, 1, 10.000, 11.000, 20.000, 18.182, NULL, 20.000, 'none', 0.000, 0.000, NULL, NULL, 0, 0.000, 0.000, 20.000, 20.000, NULL, NULL, NULL, NULL, '2024-06-28 04:20:04', '2024-06-28 04:20:39'),
(36, 'BT31', 2, 'PID11242', 5, 'grtgtrg', 1.000, 1.000, 1, 1, 20.000, 21.000, 30.000, 28.571, NULL, 30.000, 'none', 0.000, 0.000, NULL, NULL, 0, 0.000, 0.000, 30.000, 30.000, NULL, NULL, NULL, NULL, '2024-06-28 04:21:20', '2024-06-28 04:21:31'),
(37, 'BT32', 8, 'PID118401', 42, 'gdsgg', 2.000, 2.000, 1, 1, 100.000, 110.000, 1000.000, 909.091, NULL, 1000.000, 'none', NULL, 0.000, NULL, NULL, 2, 0.245, 5.000, 2035.000, 2040.000, NULL, NULL, NULL, NULL, '2024-06-29 04:39:11', '2024-06-29 04:43:42'),
(38, 'BT32', 6, 'PID116181', 19, '13', 2.000, 2.000, 1, 1, 10.000, 11.000, 20.000, 18.182, NULL, 20.000, 'none', NULL, 0.000, NULL, NULL, 2, 0.245, 5.000, 2035.000, 2040.000, NULL, NULL, NULL, NULL, '2024-06-29 04:39:11', '2024-06-29 04:43:42'),
(39, 'BT33', 1, 'PID11171', 7, '111', 2.000, 2.000, 1, 1, 10.000, 10.500, 15.000, 14.286, NULL, 15.000, 'none', 0.000, NULL, NULL, NULL, 2, 0.917, 4.000, 432.200, 436.200, NULL, NULL, NULL, NULL, '2024-06-29 09:59:32', '2024-06-29 09:59:32'),
(40, 'BT33', 2, 'PID11242', 5, 'grtgtrg', 2.000, 2.000, 1, 1, 20.000, 21.000, 30.000, 29.100, NULL, 29.100, 'percentage', 3.000, 0.900, NULL, NULL, 2, 0.917, 4.000, 432.200, 436.200, NULL, NULL, NULL, NULL, '2024-06-29 09:59:32', '2024-06-29 09:59:32'),
(41, 'BT33', 4, 'PID11481', 8, '1233', 3.000, 3.000, 1, 1, 100.000, 110.000, 120.000, 116.000, NULL, 116.000, 'amount', 3.333, 4.000, NULL, NULL, 2, 0.917, 4.000, 432.200, 436.200, NULL, NULL, NULL, NULL, '2024-06-29 09:59:32', '2024-06-29 09:59:32'),
(42, 'BT34', 2, 'PID11242', 5, 'grtgtrg', 2.000, 2.000, 1, 1, 20.000, 21.000, 30.000, 26.000, NULL, 26.000, 'amount', 13.333, 4.000, NULL, NULL, 0, 0.000, 0.000, 52.000, 52.000, NULL, NULL, NULL, NULL, '2024-06-29 10:03:15', '2024-06-29 10:03:15'),
(43, 'BT35', 2, 'PID11242', 5, 'grtgtrg', 2.000, 2.000, 1, 1, 20.000, 21.000, 30.000, 28.800, NULL, 28.800, 'percentage', 4.000, 1.200, NULL, NULL, 2, 6.944, 4.000, 53.600, 57.600, NULL, NULL, NULL, NULL, '2024-06-29 10:03:54', '2024-06-29 10:03:54'),
(44, 'BT36', 2, 'PID11261', 6, '122121FGBF', 2.000, 2.000, 1, 1, 20.000, 21.000, 30.000, 28.800, NULL, 28.800, 'percentage', 4.000, 1.200, NULL, NULL, 0, 0.000, 0.000, 57.600, 57.600, NULL, NULL, NULL, NULL, '2024-06-29 10:07:22', '2024-06-29 10:07:22'),
(45, 'BT37', 2, 'PID11261', 6, '122121FGBF', 2.000, 2.000, 1, 1, 20.000, 21.000, 30.000, 28.571, NULL, 30.000, 'none', 0.000, NULL, NULL, NULL, 0, 0.000, 0.000, 60.000, 60.000, NULL, NULL, NULL, NULL, '2024-07-02 02:36:52', '2024-07-02 02:36:52');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `location` varchar(255) NOT NULL,
  `branchname` varchar(255) NOT NULL,
  `mobile` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `location`, `branchname`, `mobile`, `created_at`, `updated_at`) VALUES
(1, 'kannur', 'kannur branch', '89454545452', '2024-06-21 04:07:27', '2024-06-21 04:07:27'),
(2, 'thalipparamba', 'thalipparamba branch', '123456', '2024-06-21 04:07:33', '2024-06-21 04:07:33');

-- --------------------------------------------------------

--
-- Table structure for table `buyproducts`
--

CREATE TABLE `buyproducts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_id` int(255) NOT NULL,
  `quantity` decimal(18,3) NOT NULL,
  `remain_quantity` decimal(18,3) NOT NULL DEFAULT 0.000,
  `unit` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `one_pro_buycost` decimal(18,3) NOT NULL,
  `one_pro_buycost_rate` decimal(18,3) DEFAULT NULL,
  `inclusive_rate` decimal(18,3) DEFAULT NULL,
  `exclusive_rate` decimal(18,3) DEFAULT NULL,
  `mrp` decimal(18,3) NOT NULL,
  `price` decimal(18,3) NOT NULL,
  `price_wo_discount` decimal(18,3) DEFAULT NULL,
  `vat_amount` decimal(18,3) NOT NULL,
  `fixed_vat` int(11) NOT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `trn_number` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `payment_type` int(255) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `netrate` decimal(18,3) DEFAULT NULL,
  `total_amount` decimal(18,3) DEFAULT NULL,
  `totalamount_wo_discount` decimal(18,3) DEFAULT NULL,
  `discount_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'none, percentage, amount',
  `discount` decimal(18,3) DEFAULT NULL,
  `discount_amount` decimal(18,3) DEFAULT NULL,
  `buycostadd` decimal(18,3) DEFAULT NULL,
  `buycost_rate_add` decimal(18,3) DEFAULT NULL,
  `credit_user_id` int(255) DEFAULT NULL,
  `cash_user_id` int(255) DEFAULT NULL,
  `vat_type` int(255) DEFAULT NULL COMMENT '1-inclusive, 2- exclusive',
  `total_discount_type` int(11) DEFAULT NULL COMMENT '0 - none, 1- percentage, 2 -amount',
  `total_discount_percent` decimal(18,3) DEFAULT NULL,
  `total_discount_amount` decimal(18,3) DEFAULT NULL,
  `bill_grand_total` decimal(18,3) DEFAULT NULL,
  `bill_grand_total_wo_discount` decimal(18,3) DEFAULT NULL,
  `edit` int(11) DEFAULT NULL COMMENT '1- edited',
  `edit_comment` varchar(255) DEFAULT NULL,
  `to_invoice` int(11) DEFAULT NULL COMMENT '1- sales order, 2-quotation',
  `sales_order_trans_ID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `quotation_trans_ID` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `buyproducts`
--

INSERT INTO `buyproducts` (`id`, `product_name`, `product_id`, `quantity`, `remain_quantity`, `unit`, `one_pro_buycost`, `one_pro_buycost_rate`, `inclusive_rate`, `exclusive_rate`, `mrp`, `price`, `price_wo_discount`, `vat_amount`, `fixed_vat`, `branch`, `transaction_id`, `customer_name`, `trn_number`, `phone`, `payment_type`, `created_at`, `updated_at`, `user_id`, `email`, `netrate`, `total_amount`, `totalamount_wo_discount`, `discount_type`, `discount`, `discount_amount`, `buycostadd`, `buycost_rate_add`, `credit_user_id`, `cash_user_id`, `vat_type`, `total_discount_type`, `total_discount_percent`, `total_discount_amount`, `bill_grand_total`, `bill_grand_total_wo_discount`, `edit`, `edit_comment`, `to_invoice`, `sales_order_trans_ID`, `quotation_trans_ID`) VALUES
(1, 'p1', 1, 3.000, 1.000, 'kg', 10.000, 10.500, 14.250, NULL, 15.000, 40.714, 42.857, 2.036, 5, '1', 'BT1', '501751', NULL, NULL, 1, '2024-06-21 05:23:33', '2024-06-21 05:27:40', 1, NULL, 14.250, 42.750, 45.000, 'percentage', 5.000, 0.750, 30.000, 31.500, NULL, NULL, 1, 2, 3.503, 5.000, 137.750, 142.750, NULL, NULL, NULL, NULL, NULL),
(2, 'p2', 2, 4.000, 2.000, 'kg', 20.000, 21.000, 25.000, NULL, 30.000, 95.238, 114.286, 4.762, 5, '1', 'BT1', '501751', NULL, NULL, 1, '2024-06-21 05:23:33', '2024-06-21 05:27:40', 1, NULL, 25.000, 100.000, 120.000, 'amount', 16.670, 5.000, 80.000, 84.000, NULL, NULL, 1, 2, 3.503, 5.000, 137.750, 142.750, NULL, NULL, NULL, NULL, NULL),
(3, 'p2', 2, 4.000, 2.000, 'kg', 20.000, 21.000, 25.000, NULL, 30.000, 95.238, 114.286, 4.762, 5, '1', 'BT2', '400911', NULL, NULL, 1, '2024-06-21 08:37:05', '2024-06-21 08:37:32', 1, NULL, 25.000, 100.000, 120.000, 'amount', 16.667, 5.000, 80.000, 84.000, NULL, NULL, 1, 0, 0.000, 0.000, 100.000, 100.000, NULL, NULL, NULL, NULL, NULL),
(4, 'p1', 3, 2.000, 1.000, 'kg', 10.000, 10.400, 19.000, NULL, 20.000, 36.190, 38.095, 1.810, 5, '2', 'BT3', '610603', NULL, NULL, 1, '2024-06-21 08:57:04', '2024-06-21 08:57:39', 2, NULL, 19.000, 38.000, 40.000, 'percentage', 5.000, 1.000, 20.000, 20.800, NULL, NULL, 1, 1, 5.000, 1.900, 36.100, 38.000, NULL, NULL, NULL, NULL, NULL),
(5, 'p1', 1, 2.000, 1.000, 'kg', 10.000, 10.500, 14.550, NULL, 15.000, 27.714, 28.571, 1.386, 5, '1', 'BT4', '675368', NULL, NULL, 1, '2024-06-21 08:58:03', '2024-06-21 08:58:24', 3, NULL, 14.550, 29.100, 30.000, 'percentage', 3.000, 0.450, 20.000, 21.000, NULL, NULL, 1, 0, 0.000, 0.000, 29.100, 29.100, NULL, NULL, NULL, NULL, NULL),
(6, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 14.286, NULL, 15.000, 14.286, 14.286, 0.714, 5, '1', 'BT5', '822957', NULL, NULL, 1, '2024-06-22 05:00:30', '2024-06-22 05:00:30', 1, NULL, 15.000, 15.000, 15.000, 'none', 0.000, NULL, 10.000, 10.500, NULL, NULL, 1, 0, 0.000, 0.000, 15.000, 15.000, NULL, NULL, NULL, NULL, NULL),
(7, 'p1', 1, 2.000, 2.000, 'kg', 10.000, 10.500, 14.250, NULL, 15.000, 27.143, 28.571, 1.357, 5, '1', 'BT6', '549141', NULL, NULL, 1, '2024-06-22 05:12:55', '2024-06-22 05:12:55', 1, NULL, 14.250, 28.500, 30.000, 'percentage', 5.000, 0.750, 20.000, 21.000, NULL, NULL, 1, 2, 14.035, 4.000, 24.500, 28.500, NULL, NULL, NULL, NULL, NULL),
(8, 'p2', 2, 1.000, 1.000, 'kg', 20.000, 21.000, 15.000, NULL, 20.000, 14.286, 19.048, 0.714, 5, '1', 'BT7', '615690', NULL, NULL, 1, '2024-06-22 05:30:34', '2024-06-22 05:30:34', 1, NULL, 15.000, 15.000, 20.000, 'amount', 25.000, 5.000, 20.000, 21.000, NULL, NULL, 1, 0, 0.000, 0.000, 15.000, 15.000, NULL, NULL, NULL, NULL, NULL),
(9, 'p2', 2, 1.000, 1.000, 'kg', 20.000, 21.000, 25.000, NULL, 30.000, 23.810, 28.571, 1.190, 5, '1', 'BT8', 'credit1', NULL, NULL, 3, '2024-06-22 05:33:10', '2024-06-22 05:33:10', 1, NULL, 25.000, 25.000, 30.000, 'amount', 16.667, 5.000, 20.000, 21.000, 1, NULL, 1, 1, 5.000, 1.250, 23.750, 25.000, NULL, NULL, NULL, NULL, NULL),
(10, 'p1', 1, 2.000, 2.000, 'kg', 10.000, 10.500, 10.000, NULL, 15.000, 19.048, 28.571, 0.952, 5, '1', 'BT9', 'credit_1', NULL, '07899325534', 3, '2024-06-22 06:45:34', '2024-06-22 06:45:34', 1, 'abc@gmail.com', 10.000, 20.000, 30.000, 'amount', 33.333, 5.000, 20.000, 21.000, 2, NULL, 1, 2, 30.000, 6.000, 14.000, 20.000, NULL, NULL, NULL, NULL, NULL),
(11, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 14.286, NULL, 15.000, 14.286, 14.286, 0.714, 5, '1', 'BT10', '152117', NULL, NULL, 1, '2024-06-22 08:25:56', '2024-06-22 08:25:56', 1, NULL, 15.000, 15.000, 15.000, 'none', 0.000, NULL, 10.000, 10.500, NULL, NULL, 1, 0, 0.000, 0.000, 15.000, 15.000, NULL, NULL, NULL, NULL, NULL),
(12, 'p1', 1, 2.000, 2.000, 'kg', 10.000, 10.500, 14.250, NULL, 15.000, 27.143, 28.571, 1.357, 5, '1', 'BT11', '272879', NULL, NULL, 1, '2024-06-22 08:27:59', '2024-06-22 08:27:59', 1, NULL, 14.250, 28.500, 30.000, 'percentage', 5.000, 0.750, 20.000, 21.000, NULL, NULL, 1, 0, 0.000, 0.000, 28.500, 28.500, NULL, NULL, NULL, NULL, NULL),
(13, 'p1', 1, 2.000, 2.000, 'kg', 10.000, 10.500, NULL, 14.700, 15.000, 29.400, 30.000, 1.470, 5, '1', 'BT12', '347365', NULL, NULL, 1, '2024-06-22 08:29:15', '2024-06-22 08:29:15', 1, NULL, 15.435, 30.870, 31.500, 'percentage', 2.000, 0.300, 20.000, 21.000, NULL, NULL, 2, 0, 0.000, 0.000, 30.870, 30.870, NULL, NULL, NULL, NULL, NULL),
(14, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 14.250, NULL, 15.000, 13.571, 14.286, 0.679, 5, '1', 'BT13', '436489', NULL, NULL, 1, '2024-06-22 08:30:45', '2024-06-22 08:30:45', 1, NULL, 14.250, 14.250, 15.000, 'percentage', 5.000, 0.750, 10.000, 10.500, NULL, NULL, 1, 0, 0.000, 0.000, 14.250, 14.250, NULL, NULL, NULL, NULL, NULL),
(15, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, NULL, 14.250, 15.000, 14.250, 15.000, 0.713, 5, '1', 'BT14', '493597', NULL, NULL, 1, '2024-06-22 08:31:43', '2024-06-22 08:31:43', 1, NULL, 14.963, 14.963, 15.750, 'percentage', 5.000, 0.750, 10.000, 10.500, NULL, NULL, 2, 0, 0.000, 0.000, 14.960, 14.960, NULL, NULL, NULL, NULL, NULL),
(16, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 10.000, NULL, 15.000, 9.524, 14.286, 0.476, 5, '1', 'BT15', '727980', NULL, NULL, 1, '2024-06-23 05:42:25', '2024-06-23 05:42:25', 1, NULL, 10.000, 10.000, 15.000, 'amount', 33.333, 5.000, 10.000, 10.500, NULL, NULL, 1, 2, 50.000, 5.000, 5.000, 10.000, NULL, NULL, NULL, NULL, NULL),
(17, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 10.000, NULL, 15.000, 9.524, 14.286, 0.476, 5, '1', 'BT16', 'zahid', NULL, '09947609015', 1, '2024-06-23 06:49:11', '2024-06-23 06:49:11', 1, 'zahid234579@gmail.com', 10.000, 10.000, 15.000, 'amount', 33.333, 5.000, 10.000, 10.500, NULL, NULL, 1, 2, 14.286, 5.000, 30.000, 35.000, NULL, NULL, NULL, NULL, NULL),
(18, 'p2', 2, 1.000, 1.000, 'kg', 20.000, 21.000, 25.000, NULL, 30.000, 23.810, 28.571, 1.190, 5, '1', 'BT16', 'zahid', NULL, '09947609015', 1, '2024-06-23 06:49:11', '2024-06-23 06:49:11', 1, 'zahid234579@gmail.com', 25.000, 25.000, 30.000, 'amount', 16.667, 5.000, 20.000, 21.000, NULL, NULL, 1, 2, 14.286, 5.000, 30.000, 35.000, NULL, NULL, NULL, NULL, NULL),
(20, 'car', 6, 4.000, 4.000, 'kg', 10.000, 11.000, 19.000, NULL, 20.000, 69.091, 72.727, 6.909, 10, '1', 'BT17', '661439', NULL, NULL, 1, '2024-06-25 09:54:50', '2024-06-25 09:57:20', 1, NULL, 19.000, 76.000, 80.000, 'amount', 5.000, 1.000, 40.000, 44.000, NULL, NULL, 1, 0, 0.000, 0.000, 76.000, 76.000, 1, 's', NULL, NULL, NULL),
(21, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 14.286, NULL, 15.000, 14.286, 14.286, 0.714, 5, '1', 'BT18', 'a', NULL, NULL, 1, '2024-06-27 03:38:10', '2024-06-27 03:38:10', 1, NULL, 15.000, 15.000, 15.000, 'none', 0.000, 0.000, 10.000, 10.500, NULL, NULL, 1, 0, 0.000, 0.000, 15.000, 15.000, NULL, NULL, 1, 'BTSLS4', NULL),
(22, 'van', 5, 1.000, 1.000, 'kg', 100.000, 110.000, 997.000, NULL, 1000.000, 906.364, 909.091, 90.636, 10, '1', 'BT19', 'Zahid Ap', NULL, NULL, 1, '2024-06-27 08:34:20', '2024-06-27 08:34:20', 1, NULL, 997.000, 997.000, 1000.000, 'amount', 0.300, 3.000, 100.000, 110.000, NULL, NULL, 1, 2, 0.401, 4.000, 993.000, 997.000, NULL, NULL, 1, 'BTSLS6', NULL),
(23, 'van', 5, 1.000, 1.000, 'kg', 100.000, 110.000, 997.000, NULL, 1000.000, 906.364, 909.091, 90.636, 10, '1', 'BT20', '705919', NULL, NULL, 1, '2024-06-27 08:35:43', '2024-06-27 08:35:43', 1, NULL, 997.000, 997.000, 1000.000, 'amount', 0.300, 3.000, 100.000, 110.000, NULL, NULL, 1, 2, 0.401, 4.000, 993.000, 997.000, NULL, NULL, NULL, NULL, NULL),
(24, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 14.286, NULL, 15.000, 14.286, 14.286, 0.714, 5, '1', 'BT21', '52224', NULL, NULL, 1, '2024-06-28 02:27:38', '2024-06-28 02:27:38', 1, NULL, 15.000, 15.000, 15.000, 'none', 0.000, NULL, 10.000, 10.500, NULL, NULL, 1, 0, 0.000, 0.000, 15.000, 15.000, NULL, NULL, NULL, NULL, NULL),
(26, 'car', 6, 1.000, 1.000, 'kg', 10.000, 11.000, 15.000, NULL, 20.000, 13.636, 18.182, 1.364, 10, '1', 'BT23', '514849', NULL, NULL, 1, '2024-06-28 03:09:12', '2024-06-28 03:09:12', 1, NULL, 15.000, 15.000, 20.000, 'amount', 25.000, 5.000, 10.000, 11.000, NULL, NULL, 1, 2, 6.667, 1.000, 14.000, 15.000, NULL, NULL, NULL, NULL, NULL),
(27, 'car', 6, 1.000, 1.000, 'kg', 10.000, 11.000, 15.000, NULL, 20.000, 13.636, 18.182, 1.364, 10, '1', 'BT22', 'za', NULL, NULL, 1, '2024-06-28 03:04:44', '2024-06-28 03:11:30', 1, NULL, 15.000, 15.000, 20.000, 'amount', 25.000, 5.000, 10.000, 11.000, NULL, NULL, 1, 2, 6.667, 1.000, 14.000, 15.000, 1, 'dfg', 2, NULL, NULL),
(29, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 14.286, NULL, 15.000, 14.286, 14.286, 0.714, 5, '1', 'BT24', 'Zahid Ap', NULL, NULL, 1, '2024-06-28 03:13:11', '2024-06-28 03:22:52', 1, NULL, 15.000, 15.000, 15.000, 'none', 0.000, 0.000, 10.000, 10.500, NULL, NULL, 1, 0, 0.000, 0.000, 15.000, 15.000, 1, 'efr', 2, NULL, NULL),
(31, 'p2', 2, 1.000, 1.000, 'kg', 20.000, 21.000, 27.000, NULL, 30.000, 25.714, 28.571, 1.286, 5, '1', 'BT25', 'xw', NULL, NULL, 1, '2024-06-28 03:23:36', '2024-06-28 03:24:06', 1, NULL, 27.000, 27.000, 30.000, 'amount', 10.000, 3.000, 20.000, 21.000, NULL, NULL, 1, 1, 5.000, 1.350, 25.650, 27.000, 1, 'er', 1, 'BTSLS7', NULL),
(36, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 10.000, NULL, 15.000, 9.524, 14.286, 0.476, 5, '1', 'BT26', '656328', NULL, NULL, 1, '2024-06-28 03:45:31', '2024-06-28 03:49:50', 1, NULL, 10.000, 10.000, 15.000, 'amount', 33.333, 5.000, 10.000, 10.500, NULL, NULL, 1, 2, 10.000, 1.000, 9.000, 10.000, 1, 'ryh', 2, NULL, NULL),
(37, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 10.000, NULL, 15.000, 9.524, 14.286, 0.476, 5, '1', 'BT27', '630274', NULL, NULL, 1, '2024-06-28 03:54:15', '2024-06-28 03:54:15', 1, NULL, 10.000, 10.000, 15.000, 'amount', 33.333, 5.000, 10.000, 10.500, NULL, NULL, 1, 2, 10.000, 1.000, 9.000, 10.000, NULL, NULL, 2, NULL, 'BTQUOT1'),
(38, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 10.000, NULL, 15.000, 9.524, 14.286, 0.476, 5, '1', 'BT28', 'zamu', NULL, NULL, 2, '2024-06-28 03:55:59', '2024-06-28 03:55:59', 1, NULL, 10.000, 10.000, 15.000, 'amount', 33.333, 5.000, 10.000, 10.500, NULL, NULL, 1, 0, 0.000, 0.000, 10.000, 10.000, NULL, NULL, 2, NULL, 'BTQUOT5'),
(41, 'van', 5, 3.000, 3.000, 'kg', 100.000, 110.000, 960.000, NULL, 1000.000, 2618.182, 2727.273, 261.818, 10, '1', 'BT29', '458156', NULL, NULL, 1, '2024-06-28 04:14:57', '2024-06-28 04:16:51', 1, NULL, 960.000, 2880.000, 3000.000, 'percentage', 4.000, 40.000, 300.000, 330.000, NULL, NULL, 1, 1, 5.000, 145.350, 2761.650, 2907.000, 1, 'ygtrg', 2, NULL, 'BTQUOT6'),
(42, 'p2', 2, 1.000, 1.000, 'kg', 20.000, 21.000, 27.000, NULL, 30.000, 25.714, 28.571, 1.286, 5, '1', 'BT29', '458156', NULL, NULL, 1, '2024-06-28 04:14:57', '2024-06-28 04:16:51', 1, NULL, 27.000, 27.000, 30.000, 'amount', 10.000, 3.000, 20.000, 21.000, NULL, NULL, 1, 1, 5.000, 145.350, 2761.650, 2907.000, 1, 'ygtrg', 2, NULL, 'BTQUOT6'),
(44, 'car', 6, 1.000, 1.000, 'kg', 10.000, 11.000, 18.182, NULL, 20.000, 18.182, 18.182, 1.818, 10, '1', 'BT30', 'mu', NULL, NULL, 1, '2024-06-28 04:20:04', '2024-06-28 04:20:39', 1, NULL, 20.000, 20.000, 20.000, 'none', 0.000, 0.000, 10.000, 11.000, NULL, NULL, 1, 0, 0.000, 0.000, 20.000, 20.000, 1, 'jik', 1, 'BTSLS5', NULL),
(46, 'p2', 2, 1.000, 1.000, 'kg', 20.000, 21.000, 28.571, NULL, 30.000, 28.571, 28.571, 1.429, 5, '1', 'BT31', '858839', NULL, NULL, 1, '2024-06-28 04:21:20', '2024-06-28 04:21:31', 1, NULL, 30.000, 30.000, 30.000, 'none', 0.000, 0.000, 20.000, 21.000, NULL, NULL, 1, 0, 0.000, 0.000, 30.000, 30.000, 1, 'rgre', 2, NULL, 'BTQUOT7'),
(49, '2', 8, 2.000, 2.000, 'kg', 100.000, 110.000, 909.091, NULL, 1000.000, 1818.182, 1818.182, 181.818, 10, '1', 'BT32', '790002', NULL, NULL, 1, '2024-06-29 04:39:11', '2024-06-29 04:43:42', 1, NULL, 1000.000, 2000.000, 2000.000, 'none', NULL, 0.000, 200.000, 220.000, NULL, NULL, 1, 2, 0.245, 5.000, 2035.000, 2040.000, 1, 'cv', 1, 'BTSLS8', NULL),
(50, 'car', 6, 2.000, 2.000, 'kg', 10.000, 11.000, 18.182, NULL, 20.000, 36.364, 36.364, 3.636, 10, '1', 'BT32', '790002', NULL, NULL, 1, '2024-06-29 04:39:11', '2024-06-29 04:43:42', 1, NULL, 20.000, 40.000, 40.000, 'none', NULL, 0.000, 20.000, 22.000, NULL, NULL, 1, 2, 0.245, 5.000, 2035.000, 2040.000, 1, 'cv', 1, 'BTSLS8', NULL),
(51, 'p1', 1, 2.000, 2.000, 'kg', 10.000, 10.500, 14.286, NULL, 15.000, 28.572, 28.572, 1.429, 5, '1', 'BT33', 'credit1', NULL, NULL, 3, '2024-06-29 09:59:31', '2024-06-29 09:59:32', 1, NULL, 15.000, 30.000, 30.000, 'none', 0.000, NULL, 20.000, 21.000, 1, NULL, 1, 2, 0.917, 4.000, 432.200, 436.200, NULL, NULL, NULL, NULL, NULL),
(52, 'p2', 2, 2.000, 2.000, 'kg', 20.000, 21.000, 29.100, NULL, 30.000, 55.429, 57.143, 2.771, 5, '1', 'BT33', 'credit1', NULL, NULL, 3, '2024-06-29 09:59:32', '2024-06-29 09:59:32', 1, NULL, 29.100, 58.200, 60.000, 'percentage', 3.000, 0.900, 40.000, 42.000, 1, NULL, 1, 2, 0.917, 4.000, 432.200, 436.200, NULL, NULL, NULL, NULL, NULL),
(53, 'p3', 4, 3.000, 3.000, 'kg', 100.000, 110.000, 116.000, NULL, 120.000, 316.364, 327.273, 31.636, 10, '1', 'BT33', 'credit1', NULL, NULL, 3, '2024-06-29 09:59:32', '2024-06-29 09:59:32', 1, NULL, 116.000, 348.000, 360.000, 'amount', 3.333, 4.000, 300.000, 330.000, 1, NULL, 1, 2, 0.917, 4.000, 432.200, 436.200, NULL, NULL, NULL, NULL, NULL),
(54, 'p2', 2, 2.000, 2.000, 'kg', 20.000, 21.000, 26.000, NULL, 30.000, 49.524, 57.143, 2.476, 5, '1', 'BT34', 'credit1', NULL, NULL, 3, '2024-06-29 10:03:15', '2024-06-29 10:03:15', 1, NULL, 26.000, 52.000, 60.000, 'amount', 13.333, 4.000, 40.000, 42.000, 1, NULL, 1, 0, 0.000, 0.000, 52.000, 52.000, NULL, NULL, NULL, NULL, NULL),
(55, 'p2', 2, 2.000, 2.000, 'kg', 20.000, 21.000, 28.800, NULL, 30.000, 54.857, 57.143, 2.743, 5, '1', 'BT35', 'credit1', NULL, NULL, 1, '2024-06-29 10:03:54', '2024-06-29 10:03:54', 1, NULL, 28.800, 57.600, 60.000, 'percentage', 4.000, 1.200, 40.000, 42.000, NULL, 1, 1, 2, 6.944, 4.000, 53.600, 57.600, NULL, NULL, NULL, NULL, NULL),
(56, 'p2', 2, 2.000, 2.000, 'kg', 20.000, 21.000, 28.800, NULL, 30.000, 54.857, 57.143, 2.743, 5, '1', 'BT36', 'credit1', NULL, NULL, 1, '2024-06-29 10:07:22', '2024-06-29 10:07:22', 1, NULL, 28.800, 57.600, 60.000, 'percentage', 4.000, 1.200, 40.000, 42.000, NULL, 1, 1, 0, 0.000, 0.000, 57.600, 57.600, NULL, NULL, NULL, NULL, NULL),
(57, 'p2', 2, 2.000, 2.000, 'kg', 20.000, 21.000, 28.571, NULL, 30.000, 57.142, 57.142, 2.857, 5, '1', 'BT37', 'credit1', NULL, NULL, 3, '2024-07-02 02:36:52', '2024-07-02 02:36:52', 1, NULL, 30.000, 60.000, 60.000, 'none', 0.000, NULL, 40.000, 42.000, 1, NULL, 1, 0, 0.000, 0.000, 60.000, 60.000, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cash_notes`
--

CREATE TABLE `cash_notes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `trans_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `branch` int(11) NOT NULL,
  `notes` int(255) DEFAULT NULL,
  `quantity` int(255) DEFAULT NULL,
  `note_type_total` int(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `cash_notes`
--

INSERT INTO `cash_notes` (`id`, `user_id`, `trans_id`, `branch`, `notes`, `quantity`, `note_type_total`, `created_at`, `updated_at`) VALUES
(1, 1, 'UR4', 1, 5, 2, 10, '2024-06-28 03:00:56', '2024-06-28 03:00:56'),
(2, 1, 'UR5', 1, 5, 11, 55, '2024-06-28 04:19:28', '2024-06-28 04:19:28'),
(3, 1, 'UR6', 1, 5, 1, 5, '2024-06-28 04:30:58', '2024-06-28 04:30:58');

-- --------------------------------------------------------

--
-- Table structure for table `cash_trans_statements`
--

CREATE TABLE `cash_trans_statements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cash_user_id` int(255) NOT NULL,
  `cash_username` varchar(255) NOT NULL,
  `user_id` int(255) NOT NULL,
  `location` int(255) NOT NULL,
  `collected_amount` decimal(18,3) DEFAULT NULL,
  `updated_balance` decimal(18,3) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `payment_type` int(255) DEFAULT NULL,
  `cheque_number` bigint(255) DEFAULT NULL,
  `depositing_date` date DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `edit_tran` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cash_trans_statements`
--

INSERT INTO `cash_trans_statements` (`id`, `cash_user_id`, `cash_username`, `user_id`, `location`, `collected_amount`, `updated_balance`, `transaction_id`, `comment`, `payment_type`, `cheque_number`, `depositing_date`, `reference_number`, `bank_name`, `edit_tran`, `created_at`, `updated_at`) VALUES
(1, 1, 'credit1', 1, 1, 0.000, 53.600, 'BT35', 'Invoice', 1, NULL, NULL, NULL, NULL, NULL, '2024-06-29 10:03:54', '2024-06-29 10:03:54'),
(2, 1, 'credit1', 1, 1, 57.600, 111.200, 'BT36', 'Invoice', 1, NULL, NULL, NULL, NULL, NULL, '2024-06-29 10:07:22', '2024-06-29 10:07:22');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `access` int(255) NOT NULL DEFAULT 1,
  `user_id` int(255) NOT NULL,
  `branch_id` int(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category_name`, `access`, `user_id`, `branch_id`, `created_at`, `updated_at`) VALUES
(1, 'stationary', 1, 1, 1, '2024-06-21 04:09:35', '2024-06-21 04:09:35'),
(2, 'card', 1, 1, 1, '2024-06-21 04:09:39', '2024-06-21 04:09:39'),
(3, 'card', 1, 2, 2, '2024-06-21 08:56:06', '2024-06-21 08:56:06');

-- --------------------------------------------------------

--
-- Table structure for table `creditsummaries`
--

CREATE TABLE `creditsummaries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `credituser_id` int(255) NOT NULL,
  `due_amount` decimal(18,3) DEFAULT NULL,
  `collected_amount` decimal(18,3) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `creditnote` decimal(18,3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `creditsummaries`
--

INSERT INTO `creditsummaries` (`id`, `credituser_id`, `due_amount`, `collected_amount`, `created_at`, `updated_at`, `creditnote`) VALUES
(1, 1, 567.950, 40.000, NULL, NULL, 5.000),
(2, 2, 14.000, 0.000, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `creditusers`
--

CREATE TABLE `creditusers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `location` int(255) NOT NULL,
  `admin_id` int(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `admin_status` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `trn_number` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `user_id` int(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `creditusers`
--

INSERT INTO `creditusers` (`id`, `name`, `username`, `location`, `admin_id`, `password`, `status`, `admin_status`, `created_at`, `updated_at`, `trn_number`, `phone`, `email`, `user_id`) VALUES
(1, 'credit1', 'credit1', 1, 1, '$2y$10$qdfzynrP.WuNc1SSIA6cm.YQdNCgmuzVxh/vLm7yUxSbjOB0S8Uwq', 1, 1, '2024-06-22 05:32:10', '2024-06-22 05:32:10', NULL, NULL, NULL, 1),
(2, 'credit_1', 'credit_1', 1, 1, '$2y$10$w7Th29AF49X/85eKCPDtduIOhAnkIV80P2o5adpNGXD9PtOLzNXXm', 1, 1, '2024-06-22 06:45:08', '2024-06-22 06:45:08', NULL, '07899325534', 'abc@gmail.com', 1);

-- --------------------------------------------------------

--
-- Table structure for table `credit_supplier_transactions`
--

CREATE TABLE `credit_supplier_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `credit_supplier_id` int(255) NOT NULL,
  `credit_supplier_username` varchar(255) NOT NULL,
  `user_id` int(255) NOT NULL,
  `location` int(255) NOT NULL,
  `due` decimal(18,3) DEFAULT NULL,
  `Invoice_due` decimal(18,3) DEFAULT NULL,
  `collectedamount` decimal(18,3) DEFAULT NULL,
  `updated_balance` decimal(18,3) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `reciept_no` varchar(255) DEFAULT NULL,
  `payment_type` int(255) DEFAULT NULL COMMENT '1-cash, 2-check	',
  `check_number` bigint(255) DEFAULT NULL,
  `depositing_date` date DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `edit_Purchase` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `credit_supplier_transactions`
--

INSERT INTO `credit_supplier_transactions` (`id`, `credit_supplier_id`, `credit_supplier_username`, `user_id`, `location`, `due`, `Invoice_due`, `collectedamount`, `updated_balance`, `comment`, `reciept_no`, `payment_type`, `check_number`, `depositing_date`, `bank_name`, `reference_number`, `edit_Purchase`, `created_at`, `updated_at`) VALUES
(1, 1, 'supplier1', 1, 1, NULL, NULL, 10.000, -10.000, 'Payment Made', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-22 08:13:15', '2024-06-22 08:13:15'),
(2, 1, 'supplier1', 1, 1, -10.000, 210.000, NULL, 200.000, 'Bill', '122121FGBF', NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-22 08:13:53', '2024-06-22 08:13:53'),
(3, 1, 'supplier1', 1, 1, 200.000, NULL, 10.000, 190.000, 'Payment Made', '122121FGBF', 1, NULL, NULL, 'ifc', '673457846', NULL, '2024-06-22 08:21:23', '2024-06-22 08:21:23'),
(4, 1, 'supplier1', 1, 1, 190.000, 1310.000, NULL, 1500.000, 'Bill', '4265672576', NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-29 06:02:27', '2024-06-29 06:02:27'),
(5, 1, 'supplier1', 1, 1, 1500.000, 220.000, NULL, 1720.000, 'Bill', '4265672576', NULL, NULL, NULL, NULL, NULL, 1, '2024-06-29 06:02:27', '2024-06-29 09:04:52'),
(6, 1, 'supplier1', 1, 1, 1720.000, 52.000, NULL, 1668.000, 'Bill', '4265672576', NULL, NULL, NULL, NULL, NULL, 1, '2024-06-29 06:02:27', '2024-06-29 09:17:45'),
(7, 1, 'supplier1', 1, 1, 1668.000, 1992.000, NULL, 3660.000, 'Bill', '678956735', NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-29 09:18:58', '2024-06-29 09:18:58');

-- --------------------------------------------------------

--
-- Table structure for table `credit_transactions`
--

CREATE TABLE `credit_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `credituser_id` int(255) NOT NULL,
  `credit_username` varchar(255) NOT NULL,
  `user_id` int(255) NOT NULL,
  `location` int(255) NOT NULL,
  `due` decimal(18,3) DEFAULT NULL,
  `Invoice_due` decimal(18,3) DEFAULT NULL,
  `collected_amount` decimal(18,3) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `credit_note` decimal(18,3) DEFAULT NULL,
  `updated_balance` decimal(18,3) DEFAULT 0.000,
  `comment` varchar(255) DEFAULT NULL,
  `payment_type` int(255) DEFAULT NULL,
  `cheque_number` bigint(255) DEFAULT NULL,
  `depositing_date` date DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `edit_tran` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `credit_transactions`
--

INSERT INTO `credit_transactions` (`id`, `credituser_id`, `credit_username`, `user_id`, `location`, `due`, `Invoice_due`, `collected_amount`, `transaction_id`, `credit_note`, `updated_balance`, `comment`, `payment_type`, `cheque_number`, `depositing_date`, `reference_number`, `bank_name`, `edit_tran`, `created_at`, `updated_at`) VALUES
(1, 1, 'credit1', 1, 1, 0.000, 23.750, NULL, 'BT8', NULL, 23.750, 'Invoice', NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-22 05:33:10', '2024-06-22 05:33:10'),
(2, 2, 'credit_1', 1, 1, 0.000, 14.000, NULL, 'BT9', NULL, 14.000, 'Invoice', NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-22 06:45:34', '2024-06-22 06:45:34'),
(3, 1, 'credit1', 1, 1, 23.750, NULL, 10.000, 'BT8', 5.000, 8.750, 'Payment & Credit Note', NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-22 08:46:11', '2024-06-22 08:46:11'),
(4, 1, 'credit1', 1, 1, 8.750, 432.200, 10.000, 'BT33', NULL, 430.950, 'Invoice', NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-29 09:59:32', '2024-06-29 09:59:32'),
(5, 1, 'credit1', 1, 1, 430.950, 52.000, 10.000, 'BT34', NULL, 472.950, 'Invoice', NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-29 10:03:15', '2024-06-29 10:03:15'),
(6, 1, 'credit1', 1, 1, 472.950, NULL, NULL, 'BT8', NULL, 472.950, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-29 10:10:59', '2024-06-29 10:10:59'),
(7, 1, 'credit1', 1, 1, 472.950, 60.000, 10.000, 'BT37', NULL, 522.950, 'Invoice', NULL, NULL, NULL, NULL, NULL, NULL, '2024-07-02 02:36:52', '2024-07-02 02:36:52');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_notes`
--

CREATE TABLE `delivery_notes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_id` int(255) NOT NULL,
  `quantity` decimal(18,3) NOT NULL,
  `remain_quantity` decimal(18,3) NOT NULL DEFAULT 0.000,
  `unit` varchar(30) DEFAULT NULL,
  `one_pro_buycost` decimal(18,3) NOT NULL,
  `one_pro_buycost_rate` decimal(18,3) DEFAULT NULL,
  `inclusive_rate` decimal(18,3) DEFAULT NULL,
  `exclusive_rate` decimal(18,3) DEFAULT NULL,
  `mrp` decimal(18,3) NOT NULL,
  `price` decimal(18,3) NOT NULL,
  `price_wo_discount` decimal(18,3) DEFAULT NULL,
  `vat_amount` decimal(18,3) NOT NULL,
  `fixed_vat` int(11) NOT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `trn_number` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `payment_type` int(11) NOT NULL DEFAULT 1,
  `user_id` int(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `netrate` decimal(18,3) DEFAULT NULL,
  `total_amount` decimal(18,3) DEFAULT NULL,
  `totalamount_wo_discount` decimal(18,3) DEFAULT NULL,
  `discount_type` varchar(30) DEFAULT NULL COMMENT '	none, percentage, amount',
  `discount` decimal(18,3) DEFAULT NULL,
  `discount_amount` decimal(18,3) DEFAULT NULL,
  `buycostadd` decimal(18,3) DEFAULT NULL,
  `credit_user_id` bigint(255) DEFAULT NULL,
  `cash_user_id` bigint(255) DEFAULT NULL,
  `vat_type` int(255) DEFAULT NULL,
  `total_discount_type` int(11) DEFAULT NULL COMMENT '0 - none, 1- percentage, 2 -amount	',
  `total_discount_percent` decimal(18,3) DEFAULT NULL,
  `total_discount_amount` decimal(18,3) DEFAULT NULL,
  `bill_grand_total` decimal(18,3) DEFAULT NULL,
  `bill_grand_total_wo_discount` decimal(18,3) DEFAULT NULL,
  `location_delivery` varchar(255) DEFAULT NULL,
  `area` varchar(255) DEFAULT NULL,
  `villa_no` varchar(255) DEFAULT NULL,
  `flat_no` varchar(255) DEFAULT NULL,
  `land_mark` varchar(255) DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery_notes`
--

INSERT INTO `delivery_notes` (`id`, `product_name`, `product_id`, `quantity`, `remain_quantity`, `unit`, `one_pro_buycost`, `one_pro_buycost_rate`, `inclusive_rate`, `exclusive_rate`, `mrp`, `price`, `price_wo_discount`, `vat_amount`, `fixed_vat`, `branch`, `transaction_id`, `customer_name`, `trn_number`, `phone`, `payment_type`, `user_id`, `email`, `netrate`, `total_amount`, `totalamount_wo_discount`, `discount_type`, `discount`, `discount_amount`, `buycostadd`, `credit_user_id`, `cash_user_id`, `vat_type`, `total_discount_type`, `total_discount_percent`, `total_discount_amount`, `bill_grand_total`, `bill_grand_total_wo_discount`, `location_delivery`, `area`, `villa_no`, `flat_no`, `land_mark`, `delivery_date`, `created_at`, `updated_at`) VALUES
(1, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 13.000, NULL, 15.000, 12.381, 14.286, 0.619, 5, '1', 'BTDN1', 'zahid', NULL, '09947609015', 1, 1, 'zahid234579@gmail.com', 13.000, 13.000, 15.000, 'amount', 13.333, 2.000, NULL, NULL, NULL, 1, 2, 23.077, 3.000, 10.000, 13.000, 'dubaihhh', 'kannurrrrr', '321111', '1234', 'payangadiiiii', '2024-06-01', '2024-06-26 04:08:19', '2024-06-26 04:08:19'),
(2, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 13.000, NULL, 15.000, 12.381, 14.286, 0.619, 5, '1', 'BTDN2', 'mubbi', NULL, '566577777', 1, 1, 'miugg234579@gmail.com', 13.000, 13.000, 15.000, 'amount', 13.333, 2.000, NULL, NULL, NULL, 1, 2, 23.077, 3.000, 10.000, 13.000, 'dubaihhh', 'kannurrrrr', '321111', '1234', 'payangadiiiii', '2024-06-03', '2024-06-26 04:09:34', '2024-06-26 04:09:34');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_notes_draft`
--

CREATE TABLE `delivery_notes_draft` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `product_id` int(255) DEFAULT NULL,
  `quantity` decimal(18,3) DEFAULT NULL,
  `remain_quantity` decimal(18,3) NOT NULL DEFAULT 0.000,
  `unit` varchar(30) DEFAULT NULL,
  `one_pro_buycost` decimal(18,3) DEFAULT NULL,
  `one_pro_buycost_rate` decimal(18,3) DEFAULT NULL,
  `inclusive_rate` decimal(18,3) DEFAULT NULL,
  `exclusive_rate` decimal(18,3) DEFAULT NULL,
  `mrp` decimal(18,3) DEFAULT NULL,
  `price` decimal(18,3) DEFAULT NULL,
  `price_wo_discount` decimal(18,3) DEFAULT NULL,
  `vat_amount` decimal(18,3) DEFAULT NULL,
  `fixed_vat` int(11) DEFAULT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `trn_number` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `payment_type` int(11) NOT NULL DEFAULT 1,
  `user_id` int(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `netrate` decimal(18,3) DEFAULT NULL,
  `total_amount` decimal(18,3) DEFAULT NULL,
  `totalamount_wo_discount` decimal(18,3) DEFAULT NULL,
  `discount_type` varchar(30) DEFAULT NULL COMMENT '	none, percentage, amount',
  `discount` decimal(18,3) DEFAULT NULL,
  `discount_amount` decimal(18,3) DEFAULT NULL,
  `buycostadd` decimal(18,3) DEFAULT NULL,
  `credit_user_id` bigint(255) DEFAULT NULL,
  `cash_user_id` bigint(255) DEFAULT NULL,
  `vat_type` int(255) DEFAULT NULL,
  `total_discount_type` int(11) DEFAULT NULL COMMENT '0 - none, 1- percentage, 2 -amount	',
  `total_discount_percent` decimal(18,3) DEFAULT NULL,
  `total_discount_amount` decimal(18,3) DEFAULT NULL,
  `bill_grand_total` decimal(18,3) DEFAULT NULL,
  `bill_grand_total_wo_discount` decimal(18,3) DEFAULT NULL,
  `location_delivery` varchar(255) DEFAULT NULL,
  `area` varchar(255) DEFAULT NULL,
  `villa_no` varchar(255) DEFAULT NULL,
  `flat_no` varchar(255) DEFAULT NULL,
  `land_mark` varchar(255) DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery_notes_draft`
--

INSERT INTO `delivery_notes_draft` (`id`, `product_name`, `product_id`, `quantity`, `remain_quantity`, `unit`, `one_pro_buycost`, `one_pro_buycost_rate`, `inclusive_rate`, `exclusive_rate`, `mrp`, `price`, `price_wo_discount`, `vat_amount`, `fixed_vat`, `branch`, `transaction_id`, `customer_name`, `trn_number`, `phone`, `payment_type`, `user_id`, `email`, `netrate`, `total_amount`, `totalamount_wo_discount`, `discount_type`, `discount`, `discount_amount`, `buycostadd`, `credit_user_id`, `cash_user_id`, `vat_type`, `total_discount_type`, `total_discount_percent`, `total_discount_amount`, `bill_grand_total`, `bill_grand_total_wo_discount`, `location_delivery`, `area`, `villa_no`, `flat_no`, `land_mark`, `delivery_date`, `created_at`, `updated_at`) VALUES
(2, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 14.286, NULL, 15.000, 14.286, 14.286, 0.714, 5, '1', 'BTDN1', '143013', NULL, NULL, 1, 1, NULL, 15.000, 15.000, 15.000, 'none', 0.000, NULL, NULL, NULL, NULL, 1, 0, 0.000, 0.000, 15.000, 15.000, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-26 12:59:09', '2024-06-26 12:59:09'),
(3, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 14.286, NULL, 15.000, 14.286, 14.286, 0.714, 5, '1', 'BTDN2', '151261', NULL, NULL, 1, 1, NULL, 15.000, 15.000, 15.000, 'none', 0.000, NULL, NULL, NULL, NULL, 1, 0, 0.000, 0.000, 15.000, 15.000, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-26 12:59:28', '2024-06-26 12:59:28'),
(4, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 14.286, NULL, 15.000, 14.286, 14.286, 0.714, 5, '1', 'BTDN3', '151261', NULL, NULL, 1, 1, NULL, 15.000, 15.000, 15.000, 'none', 0.000, NULL, NULL, NULL, NULL, 1, 0, 0.000, 0.000, 15.000, 15.000, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-26 13:00:40', '2024-06-26 13:00:40'),
(5, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 14.286, NULL, 15.000, 14.286, 14.286, 0.714, 5, '1', 'BTDN4', '151261', NULL, NULL, 1, 1, NULL, 15.000, 15.000, 15.000, 'none', 0.000, NULL, NULL, NULL, NULL, 1, 0, 0.000, 0.000, 15.000, 15.000, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-26 13:01:08', '2024-06-26 13:01:08'),
(6, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 14.286, NULL, 15.000, 14.286, 14.286, 0.714, 5, '1', 'BTDN5', '307242', NULL, NULL, 1, 1, NULL, 15.000, 15.000, 15.000, 'none', 0.000, NULL, NULL, NULL, NULL, 1, 0, 0.000, 0.000, 15.000, 15.000, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-26 13:01:56', '2024-06-26 13:01:56');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `finalreports`
--

CREATE TABLE `finalreports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `file` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fundhistories`
--

CREATE TABLE `fundhistories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `due` decimal(18,3) NOT NULL,
  `amount` decimal(18,3) NOT NULL,
  `credituser_id` int(255) NOT NULL,
  `user_id` int(255) NOT NULL,
  `location` int(255) NOT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `trans_id` varchar(255) DEFAULT NULL,
  `credit_note` decimal(18,5) DEFAULT NULL,
  `payment_type` int(255) DEFAULT NULL,
  `cheque_number` bigint(255) DEFAULT NULL,
  `depositing_date` date DEFAULT NULL,
  `reference_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bank_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fundhistories`
--

INSERT INTO `fundhistories` (`id`, `username`, `due`, `amount`, `credituser_id`, `user_id`, `location`, `status`, `created_at`, `updated_at`, `trans_id`, `credit_note`, `payment_type`, `cheque_number`, `depositing_date`, `reference_number`, `bank_name`) VALUES
(1, 'credit1', 23.750, 10.000, 1, 1, 1, NULL, '2024-06-22 08:46:11', '2024-06-22 08:46:11', 'BT8', 5.00000, NULL, NULL, NULL, NULL, NULL),
(2, 'credit1', 440.950, 10.000, 1, 1, 1, NULL, '2024-06-29 09:59:32', '2024-06-29 09:59:32', 'BT33', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'credit1', 482.950, 10.000, 1, 1, 1, NULL, '2024-06-29 10:03:15', '2024-06-29 10:03:15', 'BT34', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'credit1', 532.950, 10.000, 1, 1, 1, NULL, '2024-07-02 02:36:52', '2024-07-02 02:36:52', 'BT37', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hrusercreations`
--

CREATE TABLE `hrusercreations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `location` int(255) NOT NULL,
  `admin_id` int(255) NOT NULL,
  `joining_date` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `user_id` int(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` int(255) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hruserroles`
--

CREATE TABLE `hruserroles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` int(255) NOT NULL,
  `user_id` int(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoicedatas`
--

CREATE TABLE `invoicedatas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `from_name` varchar(255) NOT NULL,
  `from_number` varchar(255) NOT NULL,
  `from_trnnumber` varchar(255) NOT NULL,
  `from_email` varchar(255) DEFAULT NULL,
  `from_address` varchar(255) NOT NULL,
  `invoice_type` int(255) NOT NULL,
  `to_name` varchar(255) NOT NULL,
  `to_number` varchar(255) NOT NULL,
  `to_trnnumber` varchar(255) NOT NULL,
  `to_email` varchar(255) DEFAULT NULL,
  `to_address` varchar(255) NOT NULL,
  `heading` varchar(255) NOT NULL,
  `footer` varchar(255) DEFAULT NULL,
  `due_date` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoiceproducts`
--

CREATE TABLE `invoiceproducts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_id` int(255) DEFAULT NULL,
  `quantity` decimal(18,3) NOT NULL,
  `mrp` decimal(18,3) NOT NULL,
  `price` decimal(18,3) NOT NULL,
  `vat_amount` decimal(18,3) NOT NULL,
  `fixed_vat` decimal(18,3) NOT NULL,
  `branch` int(255) NOT NULL,
  `payment_type` int(255) NOT NULL,
  `user_id` int(255) NOT NULL,
  `invoice_id` int(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_types`
--

CREATE TABLE `invoice_types` (
  `id` int(255) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice_types`
--

INSERT INTO `invoice_types` (`id`, `name`) VALUES
(1, 'LPO'),
(2, 'INVOICE'),
(3, 'QUOTATION');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2021_09_29_052354_create_multi_models_table', 1),
(6, '2021_09_29_055556_create_buyproducts_table', 2),
(7, '2021_10_01_061507_create_products_table', 3),
(8, '2021_10_02_040254_create_returnproducts_table', 4),
(9, '2021_10_02_043555_create_billdesks_table', 5),
(10, '2021_10_02_065855_create_inventoryusers_table', 6),
(11, '2021_10_04_075155_create_analyticusers_table', 7),
(12, '2021_10_04_085843_create_adminusers_table', 8),
(13, '2021_10_04_085918_create_managerusers_table', 8),
(14, '2021_10_04_085938_create_customersupportusers_table', 8),
(15, '2021_10_04_093039_create_teamleaderusers_table', 9),
(16, '2021_10_04_100558_create_marketingusers_table', 10),
(17, '2021_10_04_104741_create_superusers_table', 11),
(18, '2021_10_05_085525_create_softwareusers_table', 12),
(19, '2021_10_09_060211_create_branchs_table', 13),
(20, '2021_10_12_082016_create_stockdetails_table', 14),
(21, '2021_10_16_071358_create_stockdats_table', 15),
(22, '2021_10_20_082829_create_accountexpenses_table', 16),
(23, '2021_10_21_071130_create_categories_table', 17),
(24, '2021_10_28_063735_create_creditusers_table', 18),
(25, '2021_10_28_071309_create_fundhistories_table', 19),
(26, '2021_11_01_052402_create_accountantlocs_table', 20),
(27, '2021_11_10_102407_create_salarydatas_table', 21),
(28, '2021_11_22_090914_create_returnpurchases_table', 22),
(29, '2021_11_25_143053_create_finalreports_table', 23),
(30, '2021_11_30_102639_create_hrusercreations_table', 24),
(31, '2021_11_30_102838_create_hruserroles_table', 25),
(32, '2022_02_16_092856_create_plexpayusers_table', 26),
(33, '2022_09_05_100909_create_units_table', 27),
(35, '2022_09_09_095655_create_suppliers_table', 28);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` int(11) NOT NULL,
  `module` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `module`) VALUES
(1, 'billing'),
(2, 'analytics'),
(3, 'inventory'),
(4, 'customersupport'),
(5, 'teamleader'),
(6, 'manager'),
(7, 'marketing'),
(8, 'branches'),
(9, 'warehouse'),
(10, 'hr'),
(11, 'accountant'),
(12, 'user'),
(13, 'billingv2'),
(14, 'credit'),
(15, 'plexpay'),
(16, 'supplier'),
(17, 'salesorder'),
(18, 'deliverynote'),
(19, 'purchaseorder'),
(20, 'quotation'),
(21, 'performance_invoice'),
(22, 'salesorder_to_invoice'),
(23, 'sunmi_print'),
(24, 'pdf_prints'),
(25, 'all_pdfsunmi_prints'),
(26, 'quotation_to_invoice'),
(27, 'purchaseorder_to_purchase');

-- --------------------------------------------------------

--
-- Table structure for table `module_roles`
--

CREATE TABLE `module_roles` (
  `id` int(11) NOT NULL,
  `user_id` int(255) NOT NULL,
  `module_id` int(255) NOT NULL,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `module_roles`
--

INSERT INTO `module_roles` (`id`, `user_id`, `module_id`, `created_at`, `updated_at`) VALUES
(0, 1, 1, '2024-07-02 03:09:42.000000', '2024-07-02 03:09:42.000000'),
(0, 1, 3, '2024-07-02 03:09:42.000000', '2024-07-02 03:09:42.000000'),
(0, 1, 11, '2024-07-02 03:09:42.000000', '2024-07-02 03:09:42.000000'),
(0, 1, 8, '2024-07-02 03:09:42.000000', '2024-07-02 03:09:42.000000'),
(0, 1, 12, '2024-07-02 03:09:42.000000', '2024-07-02 03:09:42.000000'),
(0, 1, 14, '2024-07-02 03:09:42.000000', '2024-07-02 03:09:42.000000'),
(0, 1, 16, '2024-07-02 03:09:42.000000', '2024-07-02 03:09:42.000000'),
(0, 1, 17, '2024-07-02 03:09:42.000000', '2024-07-02 03:09:42.000000'),
(0, 1, 18, '2024-07-02 03:09:42.000000', '2024-07-02 03:09:42.000000'),
(0, 1, 19, '2024-07-02 03:09:42.000000', '2024-07-02 03:09:42.000000'),
(0, 1, 20, '2024-07-02 03:09:42.000000', '2024-07-02 03:09:42.000000'),
(0, 1, 21, '2024-07-02 03:09:42.000000', '2024-07-02 03:09:42.000000'),
(0, 1, 22, '2024-07-02 03:09:42.000000', '2024-07-02 03:09:42.000000'),
(0, 1, 23, '2024-07-02 03:09:42.000000', '2024-07-02 03:09:42.000000'),
(0, 1, 24, '2024-07-02 03:09:42.000000', '2024-07-02 03:09:42.000000'),
(0, 1, 25, '2024-07-02 03:09:42.000000', '2024-07-02 03:09:42.000000'),
(0, 1, 26, '2024-07-02 03:09:42.000000', '2024-07-02 03:09:42.000000'),
(0, 1, 27, '2024-07-02 03:09:42.000000', '2024-07-02 03:09:42.000000'),
(0, 1, 28, '2024-07-02 03:09:42.000000', '2024-07-02 03:09:42.000000');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(255) NOT NULL,
  `product_id` int(255) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pand_l_amounts`
--

CREATE TABLE `pand_l_amounts` (
  `id` int(11) NOT NULL,
  `opening_stock_amt` decimal(18,2) DEFAULT NULL,
  `closing_stock_amt` decimal(18,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pand_l_s`
--

CREATE TABLE `pand_l_s` (
  `id` int(11) NOT NULL,
  `days` int(11) DEFAULT NULL,
  `openingstock` decimal(18,3) DEFAULT NULL,
  `closingstock` decimal(18,3) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pand_l_s`
--

INSERT INTO `pand_l_s` (`id`, `days`, `openingstock`, `closingstock`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, NULL, '2024-06-21 04:09:18', '2024-06-21 04:09:18'),
(2, NULL, NULL, NULL, '2024-06-22 02:17:17', '2024-06-22 02:17:17'),
(3, NULL, NULL, NULL, '2024-06-23 12:43:02', '2024-06-23 12:43:02'),
(4, NULL, NULL, NULL, '2024-06-24 02:47:11', '2024-06-24 02:47:11'),
(5, NULL, -2.000, -2.000, '2024-06-25 02:32:34', '2024-06-25 02:32:34'),
(6, NULL, -2.000, -2.000, '2024-06-26 02:38:16', '2024-06-26 02:38:16'),
(7, NULL, -2.000, -2.000, '2024-06-27 02:42:55', '2024-06-27 02:42:55'),
(8, NULL, -3.000, -3.000, '2024-06-28 02:27:00', '2024-06-28 02:27:00'),
(9, NULL, -3.000, -3.000, '2024-06-29 02:26:09', '2024-06-29 02:26:09'),
(10, NULL, -3.000, -3.000, '2024-07-01 02:22:02', '2024-07-01 02:22:02'),
(11, NULL, -3.000, -3.000, '2024-07-02 02:25:43', '2024-07-02 02:25:43');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`id`, `type`) VALUES
(1, 'CASH'),
(2, 'BANK'),
(3, 'CREDIT'),
(4, 'POS CARD');

-- --------------------------------------------------------

--
-- Table structure for table `performance_invoices`
--

CREATE TABLE `performance_invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_id` int(255) NOT NULL,
  `quantity` decimal(18,3) NOT NULL,
  `remain_quantity` decimal(18,3) NOT NULL DEFAULT 0.000,
  `unit` varchar(30) DEFAULT NULL,
  `one_pro_buycost` decimal(18,3) NOT NULL,
  `one_pro_buycost_rate` decimal(18,3) DEFAULT NULL,
  `mrp` decimal(18,3) NOT NULL,
  `inclusive_rate` decimal(18,3) DEFAULT NULL,
  `exclusive_rate` decimal(18,3) DEFAULT NULL,
  `price` decimal(18,3) NOT NULL,
  `price_wo_discount` decimal(18,3) DEFAULT NULL,
  `vat_amount` decimal(18,3) NOT NULL,
  `fixed_vat` int(11) NOT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `trn_number` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `payment_type` int(11) NOT NULL DEFAULT 1,
  `user_id` int(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `netrate` decimal(18,3) DEFAULT NULL,
  `total_amount` decimal(18,3) DEFAULT NULL,
  `totalamount_wo_discount` decimal(18,3) DEFAULT NULL,
  `discount_type` varchar(30) DEFAULT NULL COMMENT '	none, percentage, amount',
  `discount` decimal(18,3) DEFAULT NULL,
  `discount_amount` decimal(18,3) DEFAULT NULL,
  `buycostadd` decimal(18,3) DEFAULT NULL,
  `credit_user_id` bigint(255) DEFAULT NULL,
  `cash_user_id` bigint(255) DEFAULT NULL,
  `vat_type` int(255) DEFAULT NULL,
  `total_discount_type` int(11) DEFAULT NULL COMMENT '0 - none, 1- percentage, 2 -amount	',
  `total_discount_percent` decimal(18,3) DEFAULT NULL,
  `total_discount_amount` decimal(18,3) DEFAULT NULL,
  `bill_grand_total` decimal(18,3) DEFAULT NULL,
  `bill_grand_total_wo_discount` decimal(18,3) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `performance_invoices`
--

INSERT INTO `performance_invoices` (`id`, `product_name`, `product_id`, `quantity`, `remain_quantity`, `unit`, `one_pro_buycost`, `one_pro_buycost_rate`, `mrp`, `inclusive_rate`, `exclusive_rate`, `price`, `price_wo_discount`, `vat_amount`, `fixed_vat`, `branch`, `transaction_id`, `customer_name`, `trn_number`, `phone`, `payment_type`, `user_id`, `email`, `netrate`, `total_amount`, `totalamount_wo_discount`, `discount_type`, `discount`, `discount_amount`, `buycostadd`, `credit_user_id`, `cash_user_id`, `vat_type`, `total_discount_type`, `total_discount_percent`, `total_discount_amount`, `bill_grand_total`, `bill_grand_total_wo_discount`, `created_at`, `updated_at`) VALUES
(1, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 15.000, 11.000, NULL, 10.476, 14.286, 0.524, 5, '1', 'BTPI1', '870947', NULL, NULL, 1, 1, NULL, 11.000, 11.000, 15.000, 'amount', 26.667, 4.000, NULL, NULL, NULL, 1, 2, 18.182, 2.000, 9.000, 11.000, '2024-06-25 16:41:11', '2024-06-25 16:41:11'),
(2, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 15.000, 11.000, NULL, 10.476, 14.286, 0.524, 5, '1', 'BTPI2', '104454', NULL, NULL, 1, 1, NULL, 11.000, 11.000, 15.000, 'amount', 26.667, 4.000, NULL, NULL, NULL, 1, 2, 18.182, 2.000, 9.000, 11.000, '2024-06-25 16:42:02', '2024-06-25 16:42:02'),
(3, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 15.000, 14.286, NULL, 14.286, 14.286, 0.714, 5, '1', 'BTPI3', '66417', NULL, NULL, 1, 1, NULL, 15.000, 15.000, 15.000, 'none', 0.000, NULL, NULL, NULL, NULL, 1, 0, 0.000, 0.000, 15.000, 15.000, '2024-06-27 03:13:13', '2024-06-27 03:13:13'),
(4, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 15.000, 14.286, NULL, 14.286, 14.286, 0.714, 5, '1', 'BTPI4', 'c', NULL, NULL, 1, 1, NULL, 15.000, 15.000, 15.000, 'none', 0.000, NULL, NULL, NULL, NULL, 1, 0, 0.000, 0.000, 15.000, 15.000, '2024-06-27 03:29:05', '2024-06-27 03:29:05'),
(5, 'p3', 4, 2.000, 2.000, 'kg', 100.000, 110.000, 120.000, 109.091, NULL, 218.182, 218.182, 21.818, 10, '1', 'BTPI5', '64476', NULL, NULL, 1, 1, NULL, 120.000, 240.000, 240.000, 'none', 0.000, NULL, NULL, NULL, NULL, 1, 0, 0.000, 0.000, 240.000, 240.000, '2024-07-01 03:14:29', '2024-07-01 03:14:29');

-- --------------------------------------------------------

--
-- Table structure for table `performance_invoices_draft`
--

CREATE TABLE `performance_invoices_draft` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `product_id` int(255) DEFAULT NULL,
  `quantity` decimal(18,3) DEFAULT NULL,
  `remain_quantity` decimal(18,3) NOT NULL DEFAULT 0.000,
  `unit` varchar(30) DEFAULT NULL,
  `one_pro_buycost` decimal(18,3) DEFAULT NULL,
  `one_pro_buycost_rate` decimal(18,3) DEFAULT NULL,
  `mrp` decimal(18,3) DEFAULT NULL,
  `inclusive_rate` decimal(18,3) DEFAULT NULL,
  `exclusive_rate` decimal(18,3) DEFAULT NULL,
  `price` decimal(18,3) DEFAULT NULL,
  `price_wo_discount` decimal(18,3) DEFAULT NULL,
  `vat_amount` decimal(18,3) DEFAULT NULL,
  `fixed_vat` int(11) DEFAULT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `trn_number` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `payment_type` int(11) NOT NULL DEFAULT 1,
  `user_id` int(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `netrate` decimal(18,3) DEFAULT NULL,
  `total_amount` decimal(18,3) DEFAULT NULL,
  `totalamount_wo_discount` decimal(18,3) DEFAULT NULL,
  `discount_type` varchar(30) DEFAULT NULL COMMENT '	none, percentage, amount',
  `discount` decimal(18,3) DEFAULT NULL,
  `discount_amount` decimal(18,3) DEFAULT NULL,
  `buycostadd` decimal(18,3) DEFAULT NULL,
  `credit_user_id` bigint(255) DEFAULT NULL,
  `cash_user_id` bigint(255) DEFAULT NULL,
  `vat_type` int(255) DEFAULT NULL,
  `total_discount_type` int(11) DEFAULT NULL COMMENT '0 - none, 1- percentage, 2 -amount	',
  `total_discount_percent` decimal(18,3) DEFAULT NULL,
  `total_discount_amount` decimal(18,3) DEFAULT NULL,
  `bill_grand_total` decimal(18,3) DEFAULT NULL,
  `bill_grand_total_wo_discount` decimal(18,3) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `performance_invoices_draft`
--

INSERT INTO `performance_invoices_draft` (`id`, `product_name`, `product_id`, `quantity`, `remain_quantity`, `unit`, `one_pro_buycost`, `one_pro_buycost_rate`, `mrp`, `inclusive_rate`, `exclusive_rate`, `price`, `price_wo_discount`, `vat_amount`, `fixed_vat`, `branch`, `transaction_id`, `customer_name`, `trn_number`, `phone`, `payment_type`, `user_id`, `email`, `netrate`, `total_amount`, `totalamount_wo_discount`, `discount_type`, `discount`, `discount_amount`, `buycostadd`, `credit_user_id`, `cash_user_id`, `vat_type`, `total_discount_type`, `total_discount_percent`, `total_discount_amount`, `bill_grand_total`, `bill_grand_total_wo_discount`, `created_at`, `updated_at`) VALUES
(2, 'p3', 4, 1.000, 1.000, 'kg', 100.000, 110.000, 120.000, 109.091, NULL, 109.091, 109.091, 10.909, 10, '1', 'BTPI1', '74795', NULL, NULL, 1, 1, NULL, 120.000, 120.000, 120.000, 'none', 0.000, NULL, NULL, NULL, NULL, 1, 0, 0.000, 0.000, 120.000, 120.000, '2024-07-01 03:14:39', '2024-07-01 03:14:39');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plexpayusers`
--

CREATE TABLE `plexpayusers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `plexbill_userid` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `printer_statuses`
--

CREATE TABLE `printer_statuses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `branch` int(11) NOT NULL,
  `printerstate` varchar(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `productdraft`
--

CREATE TABLE `productdraft` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `productdetails` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `unit` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `buy_cost` decimal(18,3) DEFAULT NULL,
  `rate` decimal(18,3) DEFAULT NULL,
  `purchase_vat` decimal(18,3) DEFAULT NULL,
  `inclusive_rate` decimal(18,3) DEFAULT NULL,
  `inclusive_vat_amount` decimal(18,3) DEFAULT NULL,
  `selling_cost` decimal(18,2) DEFAULT NULL,
  `vat` decimal(18,3) DEFAULT NULL,
  `user_id` int(255) DEFAULT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `category_id` int(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `stock` decimal(18,3) NOT NULL DEFAULT 0.000,
  `remaining_stock` decimal(18,3) NOT NULL DEFAULT 0.000,
  `product_code` varchar(50) DEFAULT NULL,
  `barcode` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `draft_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `productdraft`
--

INSERT INTO `productdraft` (`id`, `product_name`, `productdetails`, `unit`, `buy_cost`, `rate`, `purchase_vat`, `inclusive_rate`, `inclusive_vat_amount`, `selling_cost`, `vat`, `user_id`, `branch`, `category_id`, `created_at`, `updated_at`, `status`, `stock`, `remaining_stock`, `product_code`, `barcode`, `image`, `draft_id`) VALUES
(4, 'nnn', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '1', NULL, '2024-06-23 13:18:48', '2024-06-23 13:18:48', 1, 0.000, 0.000, '123988790', '123988790', NULL, 'Draft1'),
(5, 'nn', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '1', NULL, '2024-06-23 13:18:48', '2024-06-23 13:18:48', 1, 0.000, 0.000, '123988791', '123988791', NULL, 'Draft1'),
(6, 'bhfhg', NULL, 'kg', 10.000, 10.400, 4.000, NULL, NULL, NULL, NULL, 1, '1', NULL, '2024-07-02 02:41:43', '2024-07-02 02:41:43', 1, 0.000, 0.000, '116997201', '116997201', NULL, 'Draft2');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `productdetails` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `unit` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `buy_cost` decimal(18,3) NOT NULL,
  `rate` decimal(18,3) DEFAULT NULL,
  `purchase_vat` decimal(18,3) DEFAULT NULL,
  `inclusive_rate` decimal(18,3) DEFAULT NULL,
  `inclusive_vat_amount` decimal(18,3) DEFAULT NULL,
  `selling_cost` decimal(18,2) NOT NULL,
  `vat` decimal(18,3) NOT NULL,
  `user_id` int(255) DEFAULT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `category_id` int(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `stock` decimal(18,3) NOT NULL DEFAULT 0.000,
  `remaining_stock` decimal(18,3) NOT NULL DEFAULT 0.000,
  `product_code` varchar(50) DEFAULT NULL,
  `barcode` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_name`, `productdetails`, `unit`, `buy_cost`, `rate`, `purchase_vat`, `inclusive_rate`, `inclusive_vat_amount`, `selling_cost`, `vat`, `user_id`, `branch`, `category_id`, `created_at`, `updated_at`, `status`, `stock`, `remaining_stock`, `product_code`, `barcode`, `image`) VALUES
(1, 'p1', NULL, 'kg', 10.000, 10.500, 5.000, 14.286, 0.714, 15.00, 5.000, 1, '1', 1, '2024-06-21 04:10:12', '2024-06-29 09:59:31', 1, 402.000, 375.000, '180092753', '180092753', NULL),
(2, 'p2', NULL, 'kg', 20.000, 21.000, 5.000, 28.571, 1.429, 30.00, 5.000, 1, '1', 1, '2024-06-21 05:17:22', '2024-07-02 02:36:52', 1, 76.000, 56.000, '157152598', '157152598', NULL),
(3, 'p1', NULL, 'kg', 10.000, 10.400, 4.000, 19.048, 0.952, 20.00, 5.000, 2, '2', 3, '2024-06-21 08:56:27', '2024-06-21 08:57:39', 1, 10.000, 9.000, '130304709', '130304709', NULL),
(4, 'p3', NULL, 'kg', 100.000, 110.000, 10.000, 109.091, 10.909, 120.00, 10.000, 1, '1', 2, '2024-06-23 07:23:53', '2024-06-29 09:59:32', 1, 41.000, 38.000, '152621825', '152621825', NULL),
(5, 'van', NULL, 'kg', 100.000, 110.000, 10.000, 909.091, 90.909, 1000.00, 10.000, 1, '1', 1, '2024-06-23 13:12:49', '2024-06-28 04:16:51', 1, 45.000, 40.000, '174381121', '174381121', NULL),
(6, 'car', NULL, 'kg', 10.000, 11.000, 10.000, 18.182, 1.818, 20.00, 10.000, 1, '1', 1, '2024-06-23 13:12:49', '2024-06-29 09:18:58', 1, 62.000, 53.000, '174381122', '174381122', NULL),
(7, '3', NULL, 'n', 100.000, 110.000, 10.000, 909.091, 90.909, 1000.00, 10.000, 1, '1', 2, '2024-06-23 13:16:31', '2024-06-29 09:18:58', 1, 5.000, 5.000, '167676189', '167676189', NULL),
(8, '2', NULL, 'kg', 100.000, 110.000, 10.000, 909.091, 90.909, 1000.00, 10.000, 1, '1', 1, '2024-06-23 13:16:31', '2024-06-29 04:39:11', 1, 2.000, 0.000, '167676190', '167676190', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `purchasedraft`
--

CREATE TABLE `purchasedraft` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(255) DEFAULT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `reciept_no` varchar(255) NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `product` int(255) DEFAULT NULL,
  `rate` decimal(18,3) DEFAULT NULL,
  `vat` decimal(18,3) DEFAULT NULL,
  `buycost` decimal(18,3) DEFAULT NULL,
  `inclusive_rate` decimal(18,3) DEFAULT NULL,
  `sellingcost` decimal(18,2) DEFAULT NULL,
  `is_box_or_dozen` int(11) DEFAULT NULL COMMENT '1-box, 2-dozen',
  `unit` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `box_dozen_count` int(255) DEFAULT NULL,
  `quantity` decimal(18,3) DEFAULT NULL,
  `remain_stock_quantity` decimal(18,3) NOT NULL DEFAULT 0.000,
  `price` decimal(18,3) DEFAULT NULL,
  `price_without_vat` decimal(18,3) DEFAULT NULL,
  `vat_amount` decimal(18,3) DEFAULT NULL,
  `vat_percentage` decimal(18,5) DEFAULT NULL,
  `payment_mode` int(11) DEFAULT NULL COMMENT '1 - cash, 2 - credit	',
  `supplier` varchar(255) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `status` int(255) NOT NULL DEFAULT 1,
  `edit` int(11) DEFAULT NULL COMMENT '1-edited',
  `edit_comment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `draft_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchasedraft`
--

INSERT INTO `purchasedraft` (`id`, `user_id`, `branch`, `reciept_no`, `comment`, `product`, `rate`, `vat`, `buycost`, `inclusive_rate`, `sellingcost`, `is_box_or_dozen`, `unit`, `box_dozen_count`, `quantity`, `remain_stock_quantity`, `price`, `price_without_vat`, `vat_amount`, `vat_percentage`, `payment_mode`, `supplier`, `supplier_id`, `invoice_date`, `created_at`, `updated_at`, `file`, `status`, `edit`, `edit_comment`, `draft_id`) VALUES
(8, 1, '1', 'yjyjyu', NULL, 1, 10.500, 5.000, 10.000, NULL, 15.00, 1, 'kg', 1, 10.000, 10.000, 105.000, 100.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-07-01 03:29:41', '2024-07-01 03:29:41', NULL, 1, NULL, NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(255) NOT NULL,
  `branch` varchar(255) NOT NULL,
  `purchase_order_id` varchar(255) NOT NULL,
  `reciept_no` varchar(255) NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `product` int(255) DEFAULT NULL,
  `buycost` decimal(18,2) NOT NULL,
  `sellingcost` decimal(18,2) DEFAULT NULL,
  `rate` decimal(18,3) DEFAULT NULL,
  `vat` decimal(18,3) DEFAULT NULL,
  `is_box_or_dozen` int(11) NOT NULL COMMENT '1-box, 2-dozen',
  `unit` varchar(30) DEFAULT NULL,
  `box_dozen_count` int(255) DEFAULT NULL,
  `quantity` decimal(18,3) NOT NULL,
  `remain_stock_quantity` decimal(18,3) NOT NULL DEFAULT 0.000,
  `price` decimal(18,3) NOT NULL,
  `price_without_vat` decimal(18,3) NOT NULL,
  `vat_amount` decimal(18,3) DEFAULT NULL,
  `vat_percentage` decimal(18,3) DEFAULT NULL,
  `payment_mode` int(11) NOT NULL COMMENT '1 - cash, 2 - credit	',
  `supplier` varchar(255) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `file` varchar(255) NOT NULL,
  `status` int(255) NOT NULL DEFAULT 1,
  `purchase_done` int(11) DEFAULT NULL COMMENT '1- purchase done',
  `purchase_trans` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `user_id`, `branch`, `purchase_order_id`, `reciept_no`, `comment`, `product`, `buycost`, `sellingcost`, `rate`, `vat`, `is_box_or_dozen`, `unit`, `box_dozen_count`, `quantity`, `remain_stock_quantity`, `price`, `price_without_vat`, `vat_amount`, `vat_percentage`, `payment_mode`, `supplier`, `supplier_id`, `delivery_date`, `created_at`, `updated_at`, `file`, `status`, `purchase_done`, `purchase_trans`) VALUES
(1, 1, '1', 'BTPRSO1', 'ergrtg', NULL, 4, 100.00, 120.00, 110.000, 10.000, 3, 'kg', NULL, 10.000, 10.000, 1100.000, 1000.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-29 03:25:32', '2024-06-29 03:25:32', '', 1, 1, 'ergrtg'),
(2, 1, '1', 'BTPRSO2', '678956735', NULL, 2, 20.00, 30.00, 21.000, 5.000, 3, 'kg', NULL, 10.000, 10.000, 210.000, 200.000, NULL, NULL, 2, 'supplier1', 1, '2024-06-29', '2024-06-29 06:03:26', '2024-06-29 06:03:26', '', 1, 1, '678956735'),
(3, 1, '1', 'BTPRSO2', '678956735', NULL, 4, 100.00, 120.00, 110.000, 10.000, 1, 'kg', 1, 10.000, 10.000, 1100.000, 1000.000, NULL, NULL, 2, 'supplier1', 1, '2024-06-29', '2024-06-29 06:03:26', '2024-06-29 06:03:26', '', 1, 1, '678956735'),
(4, 1, '1', 'BTPRSO2', '678956735', NULL, 6, 10.00, 20.00, 11.000, 10.000, 2, 'kg', 1, 12.000, 12.000, 132.000, 120.000, NULL, NULL, 2, 'supplier1', 1, '2024-06-29', '2024-06-29 06:03:26', '2024-06-29 06:03:26', '', 1, 1, '678956735'),
(5, 1, '1', 'BTPRSO3', 'rfegrtg', 'rtgtrg', 2, 20.00, 30.00, 21.000, 5.000, 3, 'kg', NULL, 2.000, 2.000, 42.000, 40.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-29 09:27:00', '2024-06-29 09:27:00', '', 1, 1, 'rfegrtg'),
(6, 1, '1', 'BTPRSO4', 'trgrtg', NULL, 2, 20.00, 30.00, 21.000, 5.000, 3, 'kg', NULL, 10.000, 10.000, 210.000, 200.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-29 10:26:00', '2024-06-29 10:26:00', '', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `quotations`
--

CREATE TABLE `quotations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_id` bigint(255) NOT NULL,
  `quantity` decimal(18,3) NOT NULL,
  `remain_quantity` decimal(18,3) NOT NULL DEFAULT 0.000,
  `unit` varchar(30) DEFAULT NULL,
  `one_pro_buycost` decimal(18,3) DEFAULT NULL,
  `one_pro_buycost_rate` decimal(18,3) DEFAULT NULL,
  `mrp` decimal(18,3) NOT NULL,
  `inclusive_rate` decimal(18,3) DEFAULT NULL,
  `exclusive_rate` decimal(18,3) DEFAULT NULL,
  `price` decimal(18,3) NOT NULL,
  `price_wo_discount` decimal(18,3) DEFAULT NULL,
  `vat_amount` decimal(18,3) NOT NULL,
  `fixed_vat` int(11) NOT NULL,
  `branch` bigint(255) DEFAULT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `trn_number` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `payment_type` int(11) DEFAULT 1 COMMENT '1- cash, 2- bank, 3-credit, 4-postcard',
  `user_id` bigint(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `netrate` decimal(18,3) DEFAULT NULL,
  `total_amount` decimal(18,3) DEFAULT NULL,
  `totalamount_wo_discount` decimal(18,3) DEFAULT NULL,
  `discount_type` varchar(30) DEFAULT NULL COMMENT 'none, percentage, amount	',
  `discount` decimal(18,3) DEFAULT NULL,
  `discount_amount` decimal(18,3) DEFAULT NULL,
  `buycostadd` decimal(18,3) DEFAULT NULL,
  `buycost_rate_add` decimal(18,3) DEFAULT NULL,
  `credit_user_id` bigint(255) DEFAULT NULL,
  `cash_user_id` bigint(255) DEFAULT NULL,
  `vat_type` int(255) DEFAULT NULL,
  `total_discount_type` int(11) DEFAULT NULL COMMENT '	0 - none, 1- percentage, 2 -amount	',
  `total_discount_percent` decimal(18,3) DEFAULT NULL,
  `total_discount_amount` decimal(18,3) DEFAULT NULL,
  `bill_grand_total` decimal(18,3) DEFAULT NULL,
  `bill_grand_total_wo_discount` decimal(18,3) DEFAULT NULL,
  `invoice_done` int(11) DEFAULT NULL COMMENT '1- invoice done',
  `invoice_trans` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quotations`
--

INSERT INTO `quotations` (`id`, `product_name`, `product_id`, `quantity`, `remain_quantity`, `unit`, `one_pro_buycost`, `one_pro_buycost_rate`, `mrp`, `inclusive_rate`, `exclusive_rate`, `price`, `price_wo_discount`, `vat_amount`, `fixed_vat`, `branch`, `transaction_id`, `customer_name`, `trn_number`, `phone`, `payment_type`, `user_id`, `email`, `netrate`, `total_amount`, `totalamount_wo_discount`, `discount_type`, `discount`, `discount_amount`, `buycostadd`, `buycost_rate_add`, `credit_user_id`, `cash_user_id`, `vat_type`, `total_discount_type`, `total_discount_percent`, `total_discount_amount`, `bill_grand_total`, `bill_grand_total_wo_discount`, `invoice_done`, `invoice_trans`, `created_at`, `updated_at`) VALUES
(1, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 15.000, 10.000, NULL, 9.524, 14.286, 0.476, 5, 1, 'BTQUOT1', '630274', NULL, NULL, 1, 1, NULL, 10.000, 10.000, 15.000, 'amount', 33.333, 5.000, NULL, NULL, NULL, NULL, 1, 2, 10.000, 1.000, 9.000, 10.000, 1, 'BT27', '2024-06-25 16:34:09', '2024-06-25 16:34:09'),
(2, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 15.000, 10.000, NULL, 9.524, 14.286, 0.476, 5, 1, 'BTQUOT2', '656328', NULL, NULL, 1, 1, NULL, 10.000, 10.000, 15.000, 'amount', 33.333, 5.000, NULL, NULL, NULL, NULL, 1, 2, 10.000, 1.000, 9.000, 10.000, 1, 'BT26', '2024-06-25 16:34:28', '2024-06-25 16:34:28'),
(3, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 15.000, 14.286, NULL, 14.286, 14.286, 0.714, 5, 1, 'BTQUOT3', 'b', NULL, NULL, 1, 1, NULL, 15.000, 15.000, 15.000, 'none', 0.000, NULL, NULL, NULL, NULL, NULL, 1, 0, 0.000, 0.000, 15.000, 15.000, 1, 'BT24', '2024-06-27 03:20:50', '2024-06-27 03:20:50'),
(4, 'car', 6, 1.000, 1.000, 'kg', 10.000, 11.000, 20.000, 15.000, NULL, 13.636, 18.182, 1.364, 10, 1, 'BTQUOT4', '973882', NULL, NULL, 1, 1, NULL, 15.000, 15.000, 20.000, 'amount', 25.000, 5.000, NULL, NULL, NULL, NULL, 1, 2, 6.667, 1.000, 14.000, 15.000, 1, 'BT22', '2024-06-27 06:26:29', '2024-06-27 06:26:29'),
(5, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 15.000, 10.000, NULL, 9.524, 14.286, 0.476, 5, 1, 'BTQUOT5', 'zamu', NULL, NULL, 2, 1, NULL, 10.000, 10.000, 15.000, 'amount', 33.333, 5.000, NULL, NULL, NULL, NULL, 1, 0, 0.000, 0.000, 10.000, 10.000, 1, 'BT28', '2024-06-28 03:55:46', '2024-06-28 03:55:46'),
(6, 'van', 5, 2.000, 2.000, 'kg', 100.000, 110.000, 1000.000, 960.000, NULL, 1745.455, 1818.182, 174.545, 10, 1, 'BTQUOT6', '458156', NULL, NULL, 1, 1, NULL, 960.000, 1920.000, 2000.000, 'percentage', 4.000, 40.000, NULL, NULL, NULL, NULL, 1, 1, 5.000, 98.700, 1875.300, 1974.000, 1, 'BT29', '2024-06-28 04:14:36', '2024-06-28 04:14:36'),
(7, 'p2', 2, 2.000, 2.000, 'kg', 20.000, 21.000, 30.000, 27.000, NULL, 51.429, 57.143, 2.571, 5, 1, 'BTQUOT6', '458156', NULL, NULL, 1, 1, NULL, 27.000, 54.000, 60.000, 'amount', 10.000, 3.000, NULL, NULL, NULL, NULL, 1, 1, 5.000, 98.700, 1875.300, 1974.000, 1, 'BT29', '2024-06-28 04:14:36', '2024-06-28 04:14:36'),
(8, 'p2', 2, 1.000, 1.000, 'kg', 20.000, 21.000, 30.000, 28.571, NULL, 28.571, 28.571, 1.429, 5, 1, 'BTQUOT7', '858839', NULL, NULL, 1, 1, NULL, 30.000, 30.000, 30.000, 'none', 0.000, NULL, NULL, NULL, NULL, NULL, 1, 0, 0.000, 0.000, 30.000, 30.000, 1, 'BT31', '2024-06-28 04:21:04', '2024-06-28 04:21:04'),
(9, 'p1', 1, 2.000, 2.000, 'kg', 10.000, 10.500, 15.000, 14.286, NULL, 28.572, 28.572, 1.429, 5, 1, 'BTQUOT8', '918031', NULL, NULL, 1, 1, NULL, 15.000, 30.000, 30.000, 'none', 0.000, NULL, NULL, NULL, NULL, NULL, 1, 0, 0.000, 0.000, 30.000, 30.000, NULL, NULL, '2024-06-29 10:22:03', '2024-06-29 10:22:03');

-- --------------------------------------------------------

--
-- Table structure for table `quotations_draft`
--

CREATE TABLE `quotations_draft` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `product_id` bigint(255) DEFAULT NULL,
  `quantity` decimal(18,3) DEFAULT NULL,
  `remain_quantity` decimal(18,3) NOT NULL DEFAULT 0.000,
  `unit` varchar(30) DEFAULT NULL,
  `one_pro_buycost` decimal(18,3) DEFAULT NULL,
  `one_pro_buycost_rate` decimal(18,3) DEFAULT NULL,
  `mrp` decimal(18,3) DEFAULT NULL,
  `inclusive_rate` decimal(18,3) DEFAULT NULL,
  `exclusive_rate` decimal(18,3) DEFAULT NULL,
  `price` decimal(18,3) DEFAULT NULL,
  `price_wo_discount` decimal(18,3) DEFAULT NULL,
  `vat_amount` decimal(18,3) DEFAULT NULL,
  `fixed_vat` int(11) DEFAULT NULL,
  `branch` bigint(255) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `trn_number` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `payment_type` int(11) DEFAULT 1 COMMENT '1- cash, 2- bank, 3-credit, 4-postcard',
  `user_id` bigint(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `netrate` decimal(18,3) DEFAULT NULL,
  `total_amount` decimal(18,3) DEFAULT NULL,
  `totalamount_wo_discount` decimal(18,3) DEFAULT NULL,
  `discount_type` varchar(30) DEFAULT NULL COMMENT 'none, percentage, amount	',
  `discount` decimal(18,3) DEFAULT NULL,
  `discount_amount` decimal(18,3) DEFAULT NULL,
  `buycostadd` decimal(18,3) DEFAULT NULL,
  `buycost_rate_add` decimal(18,3) DEFAULT NULL,
  `credit_user_id` bigint(255) DEFAULT NULL,
  `cash_user_id` bigint(255) DEFAULT NULL,
  `vat_type` int(255) DEFAULT NULL,
  `total_discount_type` int(11) DEFAULT NULL COMMENT '	0 - none, 1- percentage, 2 -amount	',
  `total_discount_percent` decimal(18,3) DEFAULT NULL,
  `total_discount_amount` decimal(18,3) DEFAULT NULL,
  `bill_grand_total` decimal(18,3) DEFAULT NULL,
  `bill_grand_total_wo_discount` decimal(18,3) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quotations_draft`
--

INSERT INTO `quotations_draft` (`id`, `product_name`, `product_id`, `quantity`, `remain_quantity`, `unit`, `one_pro_buycost`, `one_pro_buycost_rate`, `mrp`, `inclusive_rate`, `exclusive_rate`, `price`, `price_wo_discount`, `vat_amount`, `fixed_vat`, `branch`, `transaction_id`, `customer_name`, `trn_number`, `phone`, `payment_type`, `user_id`, `email`, `netrate`, `total_amount`, `totalamount_wo_discount`, `discount_type`, `discount`, `discount_amount`, `buycostadd`, `buycost_rate_add`, `credit_user_id`, `cash_user_id`, `vat_type`, `total_discount_type`, `total_discount_percent`, `total_discount_amount`, `bill_grand_total`, `bill_grand_total_wo_discount`, `created_at`, `updated_at`) VALUES
(2, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 15.000, 14.286, NULL, 14.286, 14.286, 0.714, 5, 1, 'BTQUOT1', '957201', NULL, NULL, 1, 1, NULL, 15.000, 15.000, 15.000, 'none', 0.000, NULL, NULL, NULL, NULL, NULL, 1, 0, 0.000, 0.000, 15.000, 15.000, '2024-06-28 04:22:43', '2024-06-28 04:22:43');

-- --------------------------------------------------------

--
-- Table structure for table `rawilk_prints`
--

CREATE TABLE `rawilk_prints` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `branch` int(11) NOT NULL,
  `printername` varchar(255) NOT NULL,
  `printer_id` varchar(255) NOT NULL,
  `status` varchar(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `returnproducts`
--

CREATE TABLE `returnproducts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` decimal(18,3) NOT NULL,
  `unit` varchar(30) DEFAULT NULL,
  `one_pro_buycost` decimal(18,3) NOT NULL,
  `one_pro_buycost_rate` decimal(18,3) DEFAULT NULL,
  `inclusive_rate` decimal(18,3) DEFAULT NULL,
  `mrp` decimal(18,3) NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `price` decimal(18,3) NOT NULL,
  `price_wo_discount` decimal(18,3) DEFAULT NULL,
  `vat` decimal(18,3) NOT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int(255) DEFAULT NULL,
  `product_id` int(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `trn_number` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `netrate` decimal(18,3) DEFAULT NULL,
  `total_amount` decimal(18,3) DEFAULT NULL,
  `totalamount_wo_discount` decimal(18,3) DEFAULT NULL,
  `discount` decimal(18,3) DEFAULT NULL,
  `discount_amount` decimal(18,3) DEFAULT NULL,
  `payment_type` int(255) DEFAULT NULL,
  `fixed_vat` decimal(18,3) DEFAULT NULL,
  `vat_amount` decimal(18,3) DEFAULT NULL,
  `buycostaddreturn` decimal(18,3) DEFAULT NULL,
  `buycost_rate_addreturn` decimal(18,3) DEFAULT NULL,
  `creditusers_id` int(255) DEFAULT NULL,
  `cash_users_id` int(255) DEFAULT NULL,
  `vat_type` int(255) DEFAULT NULL,
  `total_discount_percent` decimal(18,3) DEFAULT NULL,
  `total_discount_amount` decimal(18,3) DEFAULT NULL,
  `grand_total` decimal(18,3) DEFAULT NULL,
  `grand_total_wo_discount` decimal(18,3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `returnproducts`
--

INSERT INTO `returnproducts` (`id`, `product_name`, `quantity`, `unit`, `one_pro_buycost`, `one_pro_buycost_rate`, `inclusive_rate`, `mrp`, `transaction_id`, `price`, `price_wo_discount`, `vat`, `branch`, `created_at`, `updated_at`, `user_id`, `product_id`, `email`, `trn_number`, `phone`, `netrate`, `total_amount`, `totalamount_wo_discount`, `discount`, `discount_amount`, `payment_type`, `fixed_vat`, `vat_amount`, `buycostaddreturn`, `buycost_rate_addreturn`, `creditusers_id`, `cash_users_id`, `vat_type`, `total_discount_percent`, `total_discount_amount`, `grand_total`, `grand_total_wo_discount`) VALUES
(1, 'p1', 1.000, 'kg', 10.000, 10.500, 14.250, 15.000, 'BT1', 13.571, 14.250, 0.000, '1', '2024-06-21 05:26:16', '2024-06-21 05:26:16', 1, 1, NULL, NULL, NULL, 14.250, 14.250, 15.000, 5.000, 0.750, 1, 5.000, 0.679, 10.000, 10.500, NULL, 0, 1, 3.500, 0.499, 13.750, 14.250),
(2, 'p1', 1.000, 'kg', 10.000, 10.500, 14.250, 15.000, 'BT1', 13.571, 14.250, 0.000, '1', '2024-06-21 05:27:40', '2024-06-21 05:27:40', 1, 1, NULL, NULL, NULL, 14.250, 14.250, 15.000, 5.000, 0.750, 1, 5.000, 0.679, 10.000, 10.500, NULL, 0, 1, 3.500, 2.251, 62.000, 64.250),
(3, 'p2', 2.000, 'kg', 20.000, 21.000, 25.000, 30.000, 'BT1', 47.617, 50.000, 0.000, '1', '2024-06-21 05:27:40', '2024-06-21 05:27:40', 1, 2, NULL, NULL, NULL, 24.999, 49.998, 60.000, 16.670, 10.002, 1, 5.000, 2.381, 40.000, 42.000, NULL, 0, 1, 3.500, 2.251, 62.000, 64.250),
(4, 'p2', 2.000, 'kg', 20.000, 21.000, 25.000, 30.000, 'BT2', 47.619, 50.000, 0.000, '1', '2024-06-21 08:37:32', '2024-06-21 08:37:32', 1, 2, NULL, NULL, NULL, 25.000, 50.000, 60.000, 16.667, 10.000, 1, 5.000, 2.381, 40.000, 42.000, NULL, 0, 1, 0.000, 0.000, 50.000, 50.000),
(5, 'p1', 1.000, 'kg', 10.000, 10.400, 19.000, 20.000, 'BT3', 18.095, 19.000, 0.000, '2', '2024-06-21 08:57:39', '2024-06-21 08:57:39', 2, 3, NULL, NULL, NULL, 19.000, 19.000, 20.000, 5.000, 1.000, 1, 5.000, 0.905, 10.000, 10.400, NULL, 0, 1, 5.000, 0.950, 18.050, 19.000),
(6, 'p1', 1.000, 'kg', 10.000, 10.500, 14.550, 15.000, 'BT4', 13.857, 14.550, 0.000, '1', '2024-06-21 08:58:24', '2024-06-21 08:58:24', 3, 1, NULL, NULL, NULL, 14.550, 14.550, 15.000, 3.000, 0.450, 1, 5.000, 0.693, 10.000, 10.500, NULL, 0, 1, 0.000, 0.000, 14.550, 14.550);

-- --------------------------------------------------------

--
-- Table structure for table `returnpurchases`
--

CREATE TABLE `returnpurchases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reciept_no` varchar(255) NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `product_id` int(255) NOT NULL,
  `quantity` decimal(18,3) NOT NULL,
  `unit` varchar(30) DEFAULT NULL,
  `rate` decimal(18,3) DEFAULT NULL,
  `vat` decimal(18,3) DEFAULT NULL,
  `buycost` decimal(18,3) DEFAULT NULL,
  `amount` decimal(18,3) NOT NULL,
  `amount_without_vat` decimal(18,3) DEFAULT NULL,
  `vat_amount` decimal(18,3) DEFAULT NULL,
  `vat_percentage` decimal(18,5) DEFAULT NULL,
  `shop_name` varchar(255) NOT NULL,
  `suppplierid` int(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch` int(255) DEFAULT NULL,
  `user_id` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `returnpurchases`
--

INSERT INTO `returnpurchases` (`id`, `reciept_no`, `comment`, `product_id`, `quantity`, `unit`, `rate`, `vat`, `buycost`, `amount`, `amount_without_vat`, `vat_amount`, `vat_percentage`, `shop_name`, `suppplierid`, `created_at`, `updated_at`, `branch`, `user_id`) VALUES
(1, 'req', NULL, 1, 1.000, 'kg', 10.500, 5.000, 10.000, 10.500, 10.000, NULL, NULL, 'supplier1', 1, '2024-06-25 07:02:09', '2024-06-25 07:02:09', 1, 1),
(2, 'teq', NULL, 1, 1.000, 'kg', 10.500, 5.000, 10.000, 10.500, 10.000, NULL, NULL, 'supplier1', 1, '2024-06-25 07:02:59', '2024-06-25 07:02:59', 1, 1),
(3, '22', NULL, 1, 1.000, 'kg', 10.500, 5.000, 10.000, 10.500, 10.000, NULL, NULL, 'supplier1', 1, '2024-06-28 05:07:27', '2024-06-28 05:07:27', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(255) NOT NULL,
  `role_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(1, 'bill'),
(2, 'inventory'),
(3, 'analytics'),
(4, 'customersupport'),
(5, 'manager'),
(6, 'marketing'),
(7, 'teamleader'),
(8, 'hr'),
(9, 'accountant'),
(10, 'billtouch'),
(11, 'credit'),
(15, 'plexpay'),
(17, 'salesorder'),
(18, 'deliverynote'),
(19, 'purchaseorder'),
(20, 'quotation'),
(21, 'performance_invoice'),
(23, 'sunmi_print'),
(26, 'credit_user');

-- --------------------------------------------------------

--
-- Table structure for table `salarydatas`
--

CREATE TABLE `salarydatas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(255) NOT NULL,
  `branch_id` int(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `salary` int(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_orders`
--

CREATE TABLE `sales_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_id` int(255) NOT NULL,
  `quantity` decimal(18,3) NOT NULL,
  `remain_quantity` decimal(18,3) NOT NULL DEFAULT 0.000,
  `unit` varchar(30) DEFAULT NULL,
  `one_pro_buycost` decimal(18,3) NOT NULL,
  `one_pro_buycost_rate` decimal(18,3) DEFAULT NULL,
  `inclusive_rate` decimal(18,3) DEFAULT NULL,
  `exclusive_rate` decimal(18,3) DEFAULT NULL,
  `mrp` decimal(18,3) NOT NULL,
  `price` decimal(18,3) NOT NULL,
  `price_wo_discount` decimal(18,3) DEFAULT NULL,
  `vat_amount` decimal(18,3) NOT NULL,
  `fixed_vat` int(11) NOT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `trn_number` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `payment_type` int(11) NOT NULL DEFAULT 1,
  `user_id` int(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `netrate` decimal(18,3) DEFAULT NULL,
  `total_amount` decimal(18,3) DEFAULT NULL,
  `totalamount_wo_discount` decimal(18,3) DEFAULT NULL,
  `discount_type` varchar(30) DEFAULT NULL COMMENT 'none, percentage, amount',
  `discount` decimal(18,3) DEFAULT NULL,
  `discount_amount` decimal(18,3) DEFAULT NULL,
  `buycostadd` decimal(18,3) DEFAULT NULL,
  `credit_user_id` bigint(255) DEFAULT NULL,
  `cash_user_id` bigint(255) DEFAULT NULL,
  `vat_type` int(255) DEFAULT NULL,
  `total_discount_type` int(11) DEFAULT NULL COMMENT '0 - none, 1- percentage, 2 -amount	',
  `total_discount_percent` decimal(18,3) DEFAULT NULL,
  `total_discount_amount` decimal(18,3) DEFAULT NULL,
  `bill_grand_total` decimal(18,3) DEFAULT NULL,
  `bill_grand_total_wo_discount` decimal(18,3) DEFAULT NULL,
  `invoice_done` int(11) DEFAULT NULL COMMENT '1- invoice done',
  `invoice_trans` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_orders`
--

INSERT INTO `sales_orders` (`id`, `product_name`, `product_id`, `quantity`, `remain_quantity`, `unit`, `one_pro_buycost`, `one_pro_buycost_rate`, `inclusive_rate`, `exclusive_rate`, `mrp`, `price`, `price_wo_discount`, `vat_amount`, `fixed_vat`, `branch`, `transaction_id`, `customer_name`, `trn_number`, `phone`, `payment_type`, `user_id`, `email`, `netrate`, `total_amount`, `totalamount_wo_discount`, `discount_type`, `discount`, `discount_amount`, `buycostadd`, `credit_user_id`, `cash_user_id`, `vat_type`, `total_discount_type`, `total_discount_percent`, `total_discount_amount`, `bill_grand_total`, `bill_grand_total_wo_discount`, `invoice_done`, `invoice_trans`, `created_at`, `updated_at`) VALUES
(1, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 10.000, NULL, 15.000, 9.524, 14.286, 0.476, 5, '1', 'BTSLS1', 'za', NULL, NULL, 1, 1, NULL, 10.000, 10.000, 15.000, 'amount', 33.333, 5.000, NULL, NULL, NULL, 1, 2, 50.000, 5.000, 5.000, 10.000, NULL, NULL, '2024-06-25 13:19:31', '2024-06-25 13:19:31'),
(2, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 10.000, NULL, 15.000, 9.524, 14.286, 0.476, 5, '1', 'BTSLS2', 'za', NULL, NULL, 1, 1, NULL, 10.000, 10.000, 15.000, 'amount', 33.333, 5.000, NULL, NULL, NULL, 1, 2, 50.000, 5.000, 5.000, 10.000, NULL, NULL, '2024-06-25 13:19:38', '2024-06-25 13:19:38'),
(3, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 14.286, NULL, 15.000, 14.286, 14.286, 0.714, 5, '1', 'BTSLS3', '79044', NULL, NULL, 1, 1, NULL, 15.000, 15.000, 15.000, 'none', 0.000, NULL, NULL, NULL, NULL, 1, 0, 0.000, 0.000, 15.000, 15.000, NULL, NULL, '2024-06-27 03:09:24', '2024-06-27 03:09:24'),
(4, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 14.286, NULL, 15.000, 14.286, 14.286, 0.714, 5, '1', 'BTSLS4', 'a', NULL, NULL, 1, 1, NULL, 15.000, 15.000, 15.000, 'none', 0.000, NULL, NULL, NULL, NULL, 1, 0, 0.000, 0.000, 15.000, 15.000, 1, 'BT18', '2024-06-27 03:20:08', '2024-06-27 03:20:08'),
(5, 'car', 6, 1.000, 1.000, 'kg', 10.000, 11.000, 18.182, NULL, 20.000, 18.182, 18.182, 1.818, 10, '1', 'BTSLS5', 'mu', NULL, NULL, 1, 1, NULL, 20.000, 20.000, 20.000, 'none', 0.000, NULL, NULL, NULL, NULL, 1, 0, 0.000, 0.000, 20.000, 20.000, 1, 'BT30', '2024-06-27 06:09:02', '2024-06-27 06:09:02'),
(6, 'van', 5, 1.000, 1.000, 'kg', 100.000, 110.000, 997.000, NULL, 1000.000, 906.364, 909.091, 90.636, 10, '1', 'BTSLS6', '926754', NULL, NULL, 1, 1, NULL, 997.000, 997.000, 1000.000, 'amount', 0.300, 3.000, NULL, NULL, NULL, 1, 2, 0.401, 4.000, 993.000, 997.000, 1, 'BT19', '2024-06-27 06:25:50', '2024-06-27 06:25:50'),
(7, 'p2', 2, 1.000, 1.000, 'kg', 20.000, 21.000, 27.000, NULL, 30.000, 25.714, 28.571, 1.286, 5, '1', 'BTSLS7', '993711', NULL, NULL, 1, 1, NULL, 27.000, 27.000, 30.000, 'amount', 10.000, 3.000, NULL, NULL, NULL, 1, 1, 5.000, 1.350, 25.650, 27.000, 1, 'BT25', '2024-06-27 08:56:49', '2024-06-27 08:56:49'),
(8, '2', 8, 2.000, 2.000, 'kg', 100.000, 110.000, 995.000, NULL, 1000.000, 1809.091, 1818.182, 180.909, 10, '1', 'BTSLS8', '790002', NULL, NULL, 1, 1, NULL, 995.000, 1990.000, 2000.000, 'amount', 0.500, 5.000, NULL, NULL, NULL, 1, 2, 0.251, 5.000, 1985.000, 1990.000, 1, 'BT32', '2024-06-29 04:32:13', '2024-06-29 04:32:13');

-- --------------------------------------------------------

--
-- Table structure for table `sales_orders_draft`
--

CREATE TABLE `sales_orders_draft` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `product_id` int(255) DEFAULT NULL,
  `quantity` decimal(18,3) DEFAULT NULL,
  `remain_quantity` decimal(18,3) NOT NULL DEFAULT 0.000,
  `unit` varchar(30) DEFAULT NULL,
  `one_pro_buycost` decimal(18,3) DEFAULT NULL,
  `one_pro_buycost_rate` decimal(18,3) DEFAULT NULL,
  `inclusive_rate` decimal(18,3) DEFAULT NULL,
  `exclusive_rate` decimal(18,3) DEFAULT NULL,
  `mrp` decimal(18,3) DEFAULT NULL,
  `price` decimal(18,3) DEFAULT NULL,
  `price_wo_discount` decimal(18,3) DEFAULT NULL,
  `vat_amount` decimal(18,3) DEFAULT NULL,
  `fixed_vat` int(11) DEFAULT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `trn_number` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `payment_type` int(11) NOT NULL DEFAULT 1,
  `user_id` int(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `netrate` decimal(18,3) DEFAULT NULL,
  `total_amount` decimal(18,3) DEFAULT NULL,
  `totalamount_wo_discount` decimal(18,3) DEFAULT NULL,
  `discount_type` varchar(30) DEFAULT NULL COMMENT 'none, percentage, amount',
  `discount` decimal(18,3) DEFAULT NULL,
  `discount_amount` decimal(18,3) DEFAULT NULL,
  `buycostadd` decimal(18,3) DEFAULT NULL,
  `credit_user_id` bigint(255) DEFAULT NULL,
  `cash_user_id` bigint(255) DEFAULT NULL,
  `vat_type` int(255) DEFAULT NULL,
  `total_discount_type` int(11) DEFAULT NULL COMMENT '0 - none, 1- percentage, 2 -amount	',
  `total_discount_percent` decimal(18,3) DEFAULT NULL,
  `total_discount_amount` decimal(18,3) DEFAULT NULL,
  `bill_grand_total` decimal(18,3) DEFAULT NULL,
  `bill_grand_total_wo_discount` decimal(18,3) DEFAULT NULL,
  `invoice_done` int(11) DEFAULT NULL COMMENT '1- invoice done',
  `invoice_trans` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_orders_draft`
--

INSERT INTO `sales_orders_draft` (`id`, `product_name`, `product_id`, `quantity`, `remain_quantity`, `unit`, `one_pro_buycost`, `one_pro_buycost_rate`, `inclusive_rate`, `exclusive_rate`, `mrp`, `price`, `price_wo_discount`, `vat_amount`, `fixed_vat`, `branch`, `transaction_id`, `customer_name`, `trn_number`, `phone`, `payment_type`, `user_id`, `email`, `netrate`, `total_amount`, `totalamount_wo_discount`, `discount_type`, `discount`, `discount_amount`, `buycostadd`, `credit_user_id`, `cash_user_id`, `vat_type`, `total_discount_type`, `total_discount_percent`, `total_discount_amount`, `bill_grand_total`, `bill_grand_total_wo_discount`, `invoice_done`, `invoice_trans`, `created_at`, `updated_at`) VALUES
(2, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 14.286, NULL, 15.000, 14.286, 14.286, 0.714, 5, '1', 'BTSLS1', '773201', NULL, NULL, 1, 1, NULL, 15.000, 15.000, 15.000, 'none', 0.000, NULL, NULL, NULL, NULL, 1, 0, 0.000, 0.000, 15.000, 15.000, NULL, NULL, '2024-06-27 03:19:40', '2024-06-27 03:19:40'),
(4, 'p1', 1, 1.000, 1.000, 'kg', 10.000, 10.500, 14.286, NULL, 15.000, 14.286, 14.286, 0.714, 5, '1', 'BTSLS2', 'g', NULL, NULL, 1, 1, NULL, 15.000, 15.000, 15.000, 'none', 0.000, NULL, NULL, NULL, NULL, 1, 0, 0.000, 0.000, 15.000, 15.000, NULL, NULL, '2024-06-27 03:37:31', '2024-06-27 03:37:31');

-- --------------------------------------------------------

--
-- Table structure for table `softwareusers`
--

CREATE TABLE `softwareusers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `joined_date` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `admin_id` int(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `access` int(255) NOT NULL DEFAULT 1,
  `admin_status` int(11) DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `last_logout` timestamp NULL DEFAULT NULL,
  `login_ipaddress` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `softwareusers`
--

INSERT INTO `softwareusers` (`id`, `name`, `username`, `password`, `location`, `joined_date`, `email`, `admin_id`, `created_at`, `updated_at`, `status`, `access`, `admin_status`, `last_login`, `last_logout`, `login_ipaddress`) VALUES
(1, 'user_1', 'user_1', '$2y$10$LgUCwtrUSaZtldOlwCILYexmb7kubU4ICqLYgvgtTtoH.AaKdmrty', '1', '2024-06-21', 'arcvff5@gmail.com', 1, '2024-06-21 04:07:53', '2024-06-21 04:07:53', 1, 1, 1, '2024-07-02 02:33:49', '2024-06-29 10:22:23', '127.0.0.1'),
(2, 'user_2', 'user_2', '$2y$10$KHiyQzlM/5rKv2UkyuTmaOZDUTERY9Au38Ea.L3VmLfAkAFfiKM9a', '2', '2024-06-21', 'arygrtg45@gmail.com', 1, '2024-06-21 04:08:07', '2024-06-21 04:08:07', 0, 1, 1, '2024-06-21 08:55:55', '2024-06-21 08:57:45', '127.0.0.1'),
(3, 'user_3', 'user_3', '$2y$10$odFA2pUQo/w23BBgfbFwhuFtv/NkN3SEnFzQK5UJwf49apzIr0Wza', '1', '2024-06-21', 'abc@gmail.com', 1, '2024-06-21 04:08:24', '2024-06-21 04:08:24', 0, 1, 1, '2024-06-21 08:57:50', '2024-06-21 08:58:50', '127.0.0.1');

-- --------------------------------------------------------

--
-- Table structure for table `stockdats`
--

CREATE TABLE `stockdats` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` int(255) NOT NULL,
  `user_id` int(255) NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `stock_num` decimal(18,3) NOT NULL,
  `one_pro_buycost` decimal(18,3) DEFAULT NULL,
  `one_pro_buycost_rate` decimal(18,3) DEFAULT NULL,
  `one_pro_sellingcost` decimal(18,2) DEFAULT NULL,
  `one_pro_inclusive_rate` decimal(18,3) DEFAULT NULL,
  `netrate` decimal(18,3) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stockdats`
--

INSERT INTO `stockdats` (`id`, `product_id`, `user_id`, `transaction_id`, `stock_num`, `one_pro_buycost`, `one_pro_buycost_rate`, `one_pro_sellingcost`, `one_pro_inclusive_rate`, `netrate`, `created_at`, `updated_at`) VALUES
(4, 1, 1, 'BT1', 1.000, 10.000, 10.500, 15.00, 14.250, 14.250, '2024-06-21 05:27:40', '2024-06-21 05:27:40'),
(5, 2, 1, 'BT1', 2.000, 20.000, 21.000, 30.00, 25.000, 25.000, '2024-06-21 05:27:40', '2024-06-21 05:27:40'),
(7, 2, 1, 'BT2', 2.000, 20.000, 21.000, 30.00, 25.000, 25.000, '2024-06-21 08:37:32', '2024-06-21 08:37:32'),
(9, 3, 2, 'BT3', 1.000, 10.000, 10.400, 20.00, 19.000, 19.000, '2024-06-21 08:57:39', '2024-06-21 08:57:39'),
(11, 1, 3, 'BT4', 1.000, 10.000, 10.500, 15.00, 14.550, 14.550, '2024-06-21 08:58:24', '2024-06-21 08:58:24'),
(12, 1, 1, 'BT5', 1.000, 10.000, 10.500, 15.00, 14.286, 15.000, '2024-06-22 05:00:30', '2024-06-22 05:00:30'),
(13, 1, 1, 'BT6', 2.000, 10.000, 10.500, 15.00, 14.250, 14.250, '2024-06-22 05:12:55', '2024-06-22 05:12:55'),
(14, 2, 1, 'BT7', 1.000, 20.000, 21.000, 20.00, 15.000, 15.000, '2024-06-22 05:30:34', '2024-06-22 05:30:34'),
(15, 2, 1, 'BT8', 1.000, 20.000, 21.000, 30.00, 25.000, 25.000, '2024-06-22 05:33:10', '2024-06-22 05:33:10'),
(16, 1, 1, 'BT9', 2.000, 10.000, 10.500, 15.00, 10.000, 10.000, '2024-06-22 06:45:34', '2024-06-22 06:45:34'),
(17, 1, 1, 'BT10', 1.000, 10.000, 10.500, 15.00, 14.286, 15.000, '2024-06-22 08:25:56', '2024-06-22 08:25:56'),
(18, 1, 1, 'BT11', 2.000, 10.000, 10.500, 15.00, 14.250, 14.250, '2024-06-22 08:27:59', '2024-06-22 08:27:59'),
(19, 1, 1, 'BT12', 2.000, 10.000, 10.500, 15.00, NULL, 15.435, '2024-06-22 08:29:15', '2024-06-22 08:29:15'),
(20, 1, 1, 'BT13', 1.000, 10.000, 10.500, 15.00, 14.250, 14.250, '2024-06-22 08:30:45', '2024-06-22 08:30:45'),
(21, 1, 1, 'BT14', 1.000, 10.000, 10.500, 15.00, NULL, 14.963, '2024-06-22 08:31:43', '2024-06-22 08:31:43'),
(22, 1, 1, 'BT15', 1.000, 10.000, 10.500, 15.00, 10.000, 10.000, '2024-06-23 05:42:25', '2024-06-23 05:42:25'),
(23, 1, 1, 'BT16', 1.000, 10.000, 10.500, 15.00, 10.000, 10.000, '2024-06-23 06:49:11', '2024-06-23 06:49:11'),
(24, 2, 1, 'BT16', 1.000, 20.000, 21.000, 30.00, 25.000, 25.000, '2024-06-23 06:49:11', '2024-06-23 06:49:11'),
(26, 6, 1, 'BT17', 4.000, 10.000, 11.000, 20.00, 19.000, 19.000, '2024-06-25 09:54:50', '2024-06-25 09:57:20'),
(27, 1, 1, 'BT18', 1.000, 10.000, 10.500, 15.00, 14.286, 15.000, '2024-06-27 03:38:10', '2024-06-27 03:38:10'),
(28, 5, 1, 'BT19', 1.000, 100.000, 110.000, 1000.00, 997.000, 997.000, '2024-06-27 08:34:20', '2024-06-27 08:34:20'),
(29, 5, 1, 'BT20', 1.000, 100.000, 110.000, 1000.00, 997.000, 997.000, '2024-06-27 08:35:43', '2024-06-27 08:35:43'),
(30, 1, 1, 'BT21', 1.000, 10.000, 10.500, 15.00, 14.286, 15.000, '2024-06-28 02:27:38', '2024-06-28 02:27:38'),
(32, 6, 1, 'BT23', 1.000, 10.000, 11.000, 20.00, 15.000, 15.000, '2024-06-28 03:09:12', '2024-06-28 03:09:12'),
(33, 6, 1, 'BT22', 1.000, 10.000, 11.000, 20.00, 15.000, 15.000, '2024-06-28 03:04:44', '2024-06-28 03:11:30'),
(35, 1, 1, 'BT24', 1.000, 10.000, 10.500, 15.00, 14.286, 15.000, '2024-06-28 03:13:11', '2024-06-28 03:22:52'),
(37, 2, 1, 'BT25', 1.000, 20.000, 21.000, 30.00, 27.000, 27.000, '2024-06-28 03:23:36', '2024-06-28 03:24:06'),
(42, 1, 1, 'BT26', 1.000, 10.000, 10.500, 15.00, 10.000, 10.000, '2024-06-28 03:45:31', '2024-06-28 03:49:50'),
(43, 1, 1, 'BT27', 1.000, 10.000, 10.500, 15.00, 10.000, 10.000, '2024-06-28 03:54:15', '2024-06-28 03:54:15'),
(44, 1, 1, 'BT28', 1.000, 10.000, 10.500, 15.00, 10.000, 10.000, '2024-06-28 03:55:59', '2024-06-28 03:55:59'),
(47, 5, 1, 'BT29', 3.000, 100.000, 110.000, 1000.00, 960.000, 960.000, '2024-06-28 04:14:57', '2024-06-28 04:16:51'),
(48, 2, 1, 'BT29', 1.000, 20.000, 21.000, 30.00, 27.000, 27.000, '2024-06-28 04:14:57', '2024-06-28 04:16:51'),
(50, 6, 1, 'BT30', 1.000, 10.000, 11.000, 20.00, 18.182, 20.000, '2024-06-28 04:20:04', '2024-06-28 04:20:39'),
(52, 2, 1, 'BT31', 1.000, 20.000, 21.000, 30.00, 28.571, 30.000, '2024-06-28 04:21:20', '2024-06-28 04:21:31'),
(55, 8, 1, 'BT32', 2.000, 100.000, 110.000, 1000.00, 909.091, 1000.000, '2024-06-29 04:39:11', '2024-06-29 04:43:42'),
(56, 6, 1, 'BT32', 2.000, 10.000, 11.000, 20.00, 18.182, 20.000, '2024-06-29 04:39:11', '2024-06-29 04:43:42'),
(57, 1, 1, 'BT33', 2.000, 10.000, 10.500, 15.00, 14.286, 15.000, '2024-06-29 09:59:31', '2024-06-29 09:59:31'),
(58, 2, 1, 'BT33', 2.000, 20.000, 21.000, 30.00, 29.100, 29.100, '2024-06-29 09:59:32', '2024-06-29 09:59:32'),
(59, 4, 1, 'BT33', 3.000, 100.000, 110.000, 120.00, 116.000, 116.000, '2024-06-29 09:59:32', '2024-06-29 09:59:32'),
(60, 2, 1, 'BT34', 2.000, 20.000, 21.000, 30.00, 26.000, 26.000, '2024-06-29 10:03:15', '2024-06-29 10:03:15'),
(61, 2, 1, 'BT35', 2.000, 20.000, 21.000, 30.00, 28.800, 28.800, '2024-06-29 10:03:54', '2024-06-29 10:03:54'),
(62, 2, 1, 'BT36', 2.000, 20.000, 21.000, 30.00, 28.800, 28.800, '2024-06-29 10:07:22', '2024-06-29 10:07:22'),
(63, 2, 1, 'BT37', 2.000, 20.000, 21.000, 30.00, 28.571, 30.000, '2024-07-02 02:36:52', '2024-07-02 02:36:52');

-- --------------------------------------------------------

--
-- Table structure for table `stockdetails`
--

CREATE TABLE `stockdetails` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(255) NOT NULL,
  `branch` varchar(255) NOT NULL,
  `reciept_no` varchar(255) NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `product` int(255) DEFAULT NULL,
  `rate` decimal(18,3) DEFAULT NULL,
  `vat` decimal(18,3) DEFAULT NULL,
  `buycost` decimal(18,3) NOT NULL,
  `inclusive_rate` decimal(18,3) DEFAULT NULL,
  `sellingcost` decimal(18,2) DEFAULT NULL,
  `is_box_or_dozen` int(11) NOT NULL COMMENT '1-box, 2-dozen',
  `unit` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `box_dozen_count` int(255) DEFAULT NULL,
  `quantity` decimal(18,3) NOT NULL,
  `remain_stock_quantity` decimal(18,3) NOT NULL DEFAULT 0.000,
  `price` decimal(18,3) NOT NULL,
  `price_without_vat` decimal(18,3) DEFAULT NULL,
  `vat_amount` decimal(18,3) DEFAULT NULL,
  `vat_percentage` decimal(18,5) DEFAULT NULL,
  `payment_mode` int(11) NOT NULL COMMENT '1 - cash, 2 - credit	',
  `supplier` varchar(255) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `file` varchar(255) NOT NULL,
  `status` int(255) NOT NULL DEFAULT 1,
  `edit` int(11) DEFAULT NULL COMMENT '1-edited',
  `edit_comment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `to_purchase` int(11) DEFAULT NULL COMMENT '1 - purcahse order to purchase',
  `purchase_order_trans_ID` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stockdetails`
--

INSERT INTO `stockdetails` (`id`, `user_id`, `branch`, `reciept_no`, `comment`, `product`, `rate`, `vat`, `buycost`, `inclusive_rate`, `sellingcost`, `is_box_or_dozen`, `unit`, `box_dozen_count`, `quantity`, `remain_stock_quantity`, `price`, `price_without_vat`, `vat_amount`, `vat_percentage`, `payment_mode`, `supplier`, `supplier_id`, `invoice_date`, `created_at`, `updated_at`, `file`, `status`, `edit`, `edit_comment`, `to_purchase`, `purchase_order_trans_ID`) VALUES
(1, 1, '1', '122121', NULL, 1, 10.500, 5.000, 10.000, NULL, 15.00, 3, 'kg', NULL, 5.000, 5.000, 52.500, 50.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-21 05:18:15', '2024-06-21 05:18:15', '', 1, NULL, NULL, NULL, NULL),
(2, 1, '1', '122121', NULL, 2, 21.000, 5.000, 20.000, NULL, 30.00, 1, 'kg', 1, 6.000, 6.000, 126.000, 120.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-21 05:18:15', '2024-06-21 05:18:15', '', 1, NULL, NULL, NULL, NULL),
(3, 2, '2', 'tyhyt', NULL, 3, 10.400, 4.000, 10.000, NULL, 20.00, 3, 'kg', NULL, 10.000, 10.000, 104.000, 100.000, NULL, NULL, 1, 'supplier_1', 2, NULL, '2024-06-21 08:56:47', '2024-06-21 08:56:47', '', 1, NULL, NULL, NULL, NULL),
(4, 1, '1', 'grtgtrg', NULL, 1, 10.500, 5.000, 10.000, NULL, 15.00, 3, 'kg', NULL, 10.000, 10.000, 105.000, 100.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-22 06:44:43', '2024-06-22 06:44:43', '', 1, NULL, NULL, NULL, NULL),
(5, 1, '1', 'grtgtrg', NULL, 2, 21.000, 5.000, 20.000, NULL, 30.00, 3, 'kg', NULL, 10.000, 10.000, 210.000, 200.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-22 06:44:43', '2024-06-22 06:44:43', '', 1, NULL, NULL, NULL, NULL),
(6, 1, '1', '122121FGBF', NULL, 2, 21.000, 5.000, 20.000, NULL, 30.00, 1, 'kg', 1, 10.000, 10.000, 210.000, 200.000, NULL, NULL, 2, 'supplier1', 1, NULL, '2024-06-22 08:13:53', '2024-06-22 08:13:53', '', 1, NULL, NULL, NULL, NULL),
(7, 1, '1', '111', NULL, 1, 10.500, 5.000, 10.000, NULL, 15.00, 3, 'kg', NULL, 10.000, 10.000, 105.000, 100.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-23 05:43:02', '2024-06-23 05:43:02', '', 1, NULL, NULL, NULL, NULL),
(8, 1, '1', '1233', NULL, 4, 110.000, 10.000, 100.000, NULL, 120.00, 3, 'kg', NULL, 10.000, 10.000, 1100.000, 1000.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-23 07:25:15', '2024-06-23 07:25:15', '', 1, NULL, NULL, NULL, NULL),
(9, 1, '1', '1221215', NULL, 4, 110.000, 10.000, 100.000, NULL, 120.00, 3, 'kg', NULL, 1.000, 1.000, 110.000, 100.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-24 03:06:54', '2024-06-24 03:06:54', '', 1, NULL, NULL, NULL, NULL),
(10, 1, '1', '12', '2222', 1, 10.500, 5.000, 10.000, NULL, 15.00, 3, 'kg', NULL, 100.000, 100.000, 1050.000, 1000.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-24 08:21:21', '2024-06-24 08:21:21', '', 1, NULL, NULL, NULL, NULL),
(12, 1, '1', 'req', NULL, 1, 10.500, 5.000, 10.000, NULL, 15.00, 3, 'kg', NULL, 2.000, 1.000, 21.000, 20.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-25 07:01:51', '2024-06-25 07:02:09', '', 1, NULL, NULL, NULL, NULL),
(13, 1, '1', 'teq', NULL, 1, 10.500, 5.000, 10.000, NULL, 15.00, 3, 'kg', NULL, 2.000, 1.000, 21.000, 20.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-25 07:02:43', '2024-06-25 07:02:59', '', 1, NULL, NULL, NULL, NULL),
(14, 1, '1', 'teq', NULL, 2, 21.000, 5.000, 20.000, NULL, 30.00, 3, 'kg', NULL, 3.000, 3.000, 63.000, 60.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-25 07:02:43', '2024-06-25 07:02:43', '', 1, NULL, NULL, NULL, NULL),
(15, 1, '1', '111e', NULL, 6, 11.000, 10.000, 10.000, NULL, 20.00, 3, 'kg', NULL, 2.000, 2.000, 22.000, 20.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-25 09:53:51', '2024-06-25 09:53:51', '', 1, NULL, NULL, NULL, NULL),
(16, 1, '1', 'sadsd', NULL, 6, 11.000, 10.000, 10.000, NULL, 20.00, 3, 'kg', NULL, 3.000, 3.000, 33.000, 30.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-25 09:54:15', '2024-06-25 09:54:15', '', 1, NULL, NULL, NULL, NULL),
(17, 1, '1', '1111', NULL, 1, 10.500, 5.000, 10.000, NULL, 15.00, 3, 'kg', NULL, 2.000, 2.000, 21.000, 20.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-26 08:18:17', '2024-06-26 08:18:17', '', 1, NULL, NULL, NULL, NULL),
(18, 1, '1', '1111', NULL, 2, 21.000, 5.000, 20.000, NULL, 30.00, 3, 'kg', NULL, 2.000, 2.000, 42.000, 40.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-26 08:18:17', '2024-06-26 08:18:17', '', 1, NULL, NULL, NULL, NULL),
(19, 1, '1', '13', 'zahid', 6, 11.000, 10.000, 10.000, NULL, 20.00, 3, 'kg', NULL, 12.000, 12.000, 132.000, 120.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-26 08:22:32', '2024-06-26 08:22:32', '', 1, NULL, NULL, NULL, NULL),
(20, 1, '1', '14', 'mubbi', 5, 110.000, 10.000, 100.000, NULL, 1000.00, 3, 'kg', NULL, 3.000, 3.000, 330.000, 300.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-26 08:28:32', '2024-06-26 08:28:32', '', 1, NULL, NULL, NULL, NULL),
(21, 1, '1', '15', 'zahidap', 6, 11.000, 10.000, 10.000, NULL, 20.00, 3, 'kg', NULL, 1.000, 1.000, 11.000, 10.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-26 08:30:54', '2024-06-26 08:30:54', '', 1, NULL, NULL, NULL, NULL),
(22, 1, '1', '141', NULL, 5, 110.000, 10.000, 100.000, NULL, 1000.00, 3, 'kg', NULL, 2.000, 2.000, 220.000, 200.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-26 08:49:27', '2024-06-26 08:49:27', '', 1, NULL, NULL, NULL, NULL),
(23, 1, '1', '151', NULL, 5, 110.000, 10.000, 100.000, NULL, 1000.00, 3, 'kg', NULL, 2.000, 2.000, 220.000, 200.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-26 08:50:01', '2024-06-26 08:50:01', '', 1, NULL, NULL, NULL, NULL),
(24, 1, '1', '1511', NULL, 6, 11.000, 10.000, 10.000, NULL, 20.00, 3, 'kg', NULL, 3.000, 3.000, 33.000, 30.000, NULL, NULL, 2, 'supplier1', 1, NULL, '2024-06-26 08:51:12', '2024-06-26 08:51:12', '', 1, NULL, NULL, NULL, NULL),
(25, 1, '1', '21', NULL, 6, 11.000, 10.000, 10.000, NULL, 20.00, 3, 'kg', NULL, 2.000, 2.000, 22.000, 20.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-26 08:56:54', '2024-06-26 08:56:54', '', 1, NULL, NULL, NULL, NULL),
(26, 1, '1', '22', NULL, 1, 10.500, 5.000, 10.000, NULL, 15.00, 3, 'kg', NULL, 12.000, 11.000, 126.000, 120.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-26 09:05:45', '2024-06-28 05:07:27', '', 1, NULL, NULL, NULL, NULL),
(27, 1, '1', '18', NULL, 6, 11.000, 10.000, 10.000, NULL, 20.00, 3, 'kg', NULL, 1.000, 1.000, 11.000, 10.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-26 09:18:48', '2024-06-26 09:18:48', '', 1, NULL, NULL, NULL, NULL),
(28, 1, '1', '133', NULL, 1, 10.500, 5.000, 10.000, NULL, 15.00, 2, 'kg', NULL, 12.000, 12.000, 126.000, 120.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-26 09:30:43', '2024-06-26 09:30:43', '', 1, NULL, NULL, NULL, NULL),
(29, 1, '1', '1232', NULL, 6, 11.000, 10.000, 10.000, NULL, 20.00, 1, 'kg', NULL, 2.000, 2.000, 22.000, 20.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-26 09:31:52', '2024-06-26 09:31:52', '', 1, NULL, NULL, NULL, NULL),
(30, 1, '1', '12333', NULL, 6, 11.000, 10.000, 10.000, NULL, 20.00, 1, 'kg', 1, 2.000, 2.000, 22.000, 20.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-26 09:32:31', '2024-06-26 09:32:31', '', 1, NULL, NULL, NULL, NULL),
(31, 1, '1', '66', NULL, 5, 110.000, 10.000, 100.000, NULL, 1000.00, 1, 'kg', NULL, 2.000, 2.000, 220.000, 200.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-26 09:36:13', '2024-06-26 09:36:13', '', 1, NULL, NULL, NULL, NULL),
(32, 1, '1', '4555', NULL, 1, 10.500, 5.000, 10.000, NULL, 15.00, 1, 'kg', NULL, 2.000, 2.000, 21.000, 20.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-26 09:38:34', '2024-06-26 09:38:34', '', 1, NULL, NULL, NULL, NULL),
(33, 1, '1', '344', NULL, 6, 11.000, 10.000, 10.000, NULL, 20.00, 1, 'kg', NULL, 2.000, 2.000, 22.000, 20.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-26 09:46:36', '2024-06-26 09:46:36', '', 1, NULL, NULL, NULL, NULL),
(34, 1, '1', '777', NULL, 6, 11.000, 10.000, 10.000, NULL, 20.00, 1, 'kg', NULL, 0.000, 0.000, 22.000, 20.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-26 09:51:14', '2024-06-26 09:51:14', '', 1, NULL, NULL, NULL, NULL),
(35, 1, '1', '55', NULL, 6, 11.000, 10.000, 10.000, NULL, 20.00, 1, 'kg', 1, 2.000, 2.000, 22.000, 20.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-26 13:30:18', '2024-06-26 13:30:18', '', 1, NULL, NULL, NULL, NULL),
(36, 1, '1', '234', NULL, 5, 110.000, 10.000, 100.000, NULL, 1000.00, 2, 'kg', 1, 12.000, 12.000, 1320.000, 1200.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-26 13:31:11', '2024-06-26 13:31:11', '', 1, NULL, NULL, NULL, NULL),
(37, 1, '1', '111111', NULL, 5, 110.000, 10.000, 100.000, NULL, 1000.00, 2, 'kg', 1, 12.000, 12.000, 1320.000, 1200.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-26 13:31:38', '2024-06-26 13:31:38', '', 1, NULL, NULL, NULL, NULL),
(38, 1, '1', '12345', NULL, 6, 11.000, 10.000, 10.000, NULL, 20.00, 1, 'kg', 1, 2.000, 2.000, 22.000, 20.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-26 13:32:17', '2024-06-26 13:32:17', '', 1, NULL, NULL, NULL, NULL),
(39, 1, '1', '1234567', NULL, 6, 11.000, 10.000, 10.000, NULL, 20.00, 1, 'kg', 1, 2.000, 2.000, 22.000, 20.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-26 13:32:46', '2024-06-26 13:32:46', '', 1, NULL, NULL, NULL, NULL),
(40, 1, '1', '12345678', NULL, 5, 110.000, 10.000, 100.000, NULL, 1000.00, 3, 'kg', NULL, 12.000, 12.000, 1320.000, 1200.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-26 13:33:27', '2024-06-26 13:33:27', '', 1, NULL, NULL, NULL, NULL),
(41, 1, '1', 'dfgh', NULL, 6, 11.000, 10.000, 10.000, NULL, 20.00, 3, 'kg', NULL, 12.000, 12.000, 132.000, 120.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-26 13:34:10', '2024-06-29 05:21:25', '', 1, 1, 'rtgrtg', NULL, NULL),
(42, 1, '1', 'gdsgg', NULL, 8, 110.000, 10.000, 100.000, NULL, 1000.00, 3, 'kg', NULL, 2.000, 2.000, 220.000, 200.000, NULL, NULL, 1, 'gdfgdf', 3, NULL, '2024-06-29 04:35:27', '2024-06-29 04:35:27', '', 1, NULL, NULL, NULL, NULL),
(43, 1, '1', '4265672576', '78i78i7i', 4, 110.000, 10.000, 100.000, NULL, 120.00, 3, 'kg', NULL, 10.000, 10.000, 1100.000, 1000.000, NULL, NULL, 2, 'supplier1', 1, NULL, '2024-06-29 06:02:27', '2024-06-29 09:17:45', '', 1, 1, 'rtghtyh', NULL, NULL),
(44, 1, '1', '4265672576', '78i78i7i', 2, 21.000, 5.000, 20.000, NULL, 30.00, 1, 'kg', 1, 12.000, 12.000, 252.000, 240.000, NULL, NULL, 2, 'supplier1', 1, NULL, '2024-06-29 06:02:27', '2024-06-29 09:17:45', '', 1, 1, 'rtghtyh', NULL, NULL),
(45, 1, '1', '4265672576', '78i78i7i', 1, 10.500, 5.000, 10.000, NULL, 15.00, 3, 'kg', NULL, 12.000, 12.000, 126.000, 120.000, NULL, NULL, 2, 'supplier1', 1, NULL, '2024-06-29 06:02:27', '2024-06-29 09:17:45', '', 1, NULL, NULL, NULL, NULL),
(46, 1, '1', '678956735', 'yththty', 2, 21.000, 5.000, 20.000, NULL, 30.00, 3, 'kg', NULL, 10.000, 10.000, 210.000, 200.000, NULL, NULL, 2, 'supplier1', 1, NULL, '2024-06-29 09:18:58', '2024-06-29 09:18:58', '', 1, NULL, NULL, 1, 'BTPRSO2'),
(47, 1, '1', '678956735', 'yththty', 4, 110.000, 10.000, 100.000, NULL, 120.00, 1, 'kg', 1, 10.000, 10.000, 1100.000, 1000.000, NULL, NULL, 2, 'supplier1', 1, NULL, '2024-06-29 09:18:58', '2024-06-29 09:18:58', '', 1, NULL, NULL, 1, 'BTPRSO2'),
(48, 1, '1', '678956735', 'yththty', 6, 11.000, 10.000, 10.000, NULL, 20.00, 2, 'kg', 1, 12.000, 12.000, 132.000, 120.000, NULL, NULL, 2, 'supplier1', 1, NULL, '2024-06-29 09:18:58', '2024-06-29 09:18:58', '', 1, NULL, NULL, 1, 'BTPRSO2'),
(49, 1, '1', '678956735', 'yththty', 7, 110.000, 10.000, 100.000, NULL, 1000.00, 3, 'n', NULL, 5.000, 5.000, 550.000, 500.000, NULL, NULL, 2, 'supplier1', 1, NULL, '2024-06-29 09:18:58', '2024-06-29 09:18:58', '', 1, NULL, NULL, 1, 'BTPRSO2'),
(50, 1, '1', 'ergrtg', NULL, 4, 110.000, 10.000, 100.000, NULL, 120.00, 3, 'kg', NULL, 10.000, 10.000, 1100.000, 1000.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-29 09:21:53', '2024-06-29 09:21:53', '', 1, NULL, NULL, 1, 'BTPRSO1'),
(51, 1, '1', 'rfegrtg', 'rtgtrg', 2, 21.000, 5.000, 20.000, NULL, 30.00, 3, 'kg', NULL, 2.000, 2.000, 42.000, 40.000, NULL, NULL, 1, 'supplier1', 1, NULL, '2024-06-29 09:27:23', '2024-06-29 09:27:23', '', 1, NULL, NULL, 1, 'BTPRSO3');

-- --------------------------------------------------------

--
-- Table structure for table `stockhistories`
--

CREATE TABLE `stockhistories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` int(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `buycost` decimal(18,3) DEFAULT NULL,
  `rate` decimal(18,3) DEFAULT NULL,
  `vat` decimal(18,3) DEFAULT NULL,
  `sellingcost` decimal(18,2) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `receipt_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` decimal(18,3) DEFAULT NULL,
  `remain_qantity` decimal(18,3) DEFAULT NULL,
  `sell_qantity` decimal(18,3) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stockhistories`
--

INSERT INTO `stockhistories` (`id`, `product_id`, `user_id`, `buycost`, `rate`, `vat`, `sellingcost`, `product_name`, `receipt_no`, `quantity`, `remain_qantity`, `sell_qantity`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 10.000, 10.500, 5.000, 15.00, NULL, '122121', 5.000, 5.000, 5.000, '2024-06-21 05:18:15', '2024-06-21 05:18:15'),
(2, 2, 1, 20.000, 21.000, 5.000, 30.00, NULL, '122121', 6.000, 6.000, 6.000, '2024-06-21 05:18:16', '2024-06-21 05:18:16'),
(3, 3, 2, 10.000, 10.400, 4.000, 20.00, NULL, 'tyhyt', 10.000, 10.000, 10.000, '2024-06-21 08:56:47', '2024-06-21 08:56:47'),
(4, 1, 1, 10.000, 10.500, 5.000, 15.00, NULL, 'grtgtrg', 10.000, 10.000, 10.000, '2024-06-22 06:44:43', '2024-06-22 06:44:43'),
(5, 2, 1, 20.000, 21.000, 5.000, 30.00, NULL, 'grtgtrg', 10.000, 10.000, 10.000, '2024-06-22 06:44:43', '2024-06-22 06:44:43'),
(6, 2, 1, 20.000, 21.000, 5.000, 30.00, NULL, '122121FGBF', 10.000, 10.000, 10.000, '2024-06-22 08:13:53', '2024-06-22 08:13:53'),
(7, 1, 1, 10.000, 10.500, 5.000, 15.00, NULL, '111', 10.000, 10.000, 10.000, '2024-06-23 05:43:02', '2024-06-23 05:43:02'),
(8, 4, 1, 100.000, 110.000, 10.000, 120.00, NULL, '1233', 10.000, 10.000, 10.000, '2024-06-23 07:25:15', '2024-06-23 07:25:15'),
(9, 4, 1, 100.000, 110.000, 10.000, 120.00, NULL, '1221215', 1.000, 1.000, 1.000, '2024-06-24 03:06:54', '2024-06-24 03:06:54'),
(10, 1, 1, 10.000, 10.500, 5.000, 15.00, NULL, '12', 100.000, 100.000, 100.000, '2024-06-24 08:21:21', '2024-06-24 08:21:21'),
(12, 1, 1, 10.000, 10.500, 5.000, 15.00, NULL, 'req', 2.000, 1.000, 2.000, '2024-06-25 07:01:51', '2024-06-25 07:02:09'),
(13, 1, 1, 10.000, 10.500, 5.000, 15.00, NULL, 'teq', 2.000, 1.000, 2.000, '2024-06-25 07:02:43', '2024-06-25 07:02:59'),
(14, 2, 1, 20.000, 21.000, 5.000, 30.00, NULL, 'teq', 3.000, 3.000, 3.000, '2024-06-25 07:02:43', '2024-06-25 07:02:43'),
(15, 6, 1, 10.000, 11.000, 10.000, 20.00, NULL, '111e', 2.000, 2.000, 2.000, '2024-06-25 09:53:51', '2024-06-25 09:53:51'),
(16, 6, 1, 10.000, 11.000, 10.000, 20.00, NULL, 'sadsd', 3.000, 3.000, 3.000, '2024-06-25 09:54:15', '2024-06-25 09:54:15'),
(17, 1, 1, 10.000, 10.500, 5.000, 15.00, NULL, '1111', 2.000, 2.000, 2.000, '2024-06-26 08:18:17', '2024-06-26 08:18:17'),
(18, 2, 1, 20.000, 21.000, 5.000, 30.00, NULL, '1111', 2.000, 2.000, 2.000, '2024-06-26 08:18:17', '2024-06-26 08:18:17'),
(19, 6, 1, 10.000, 11.000, 10.000, 20.00, NULL, '13', 12.000, 12.000, 12.000, '2024-06-26 08:22:32', '2024-06-26 08:22:32'),
(20, 5, 1, 100.000, 110.000, 10.000, 1000.00, NULL, '14', 3.000, 3.000, 3.000, '2024-06-26 08:28:32', '2024-06-26 08:28:32'),
(21, 6, 1, 10.000, 11.000, 10.000, 20.00, NULL, '15', 1.000, 1.000, 1.000, '2024-06-26 08:30:54', '2024-06-26 08:30:54'),
(22, 5, 1, 100.000, 110.000, 10.000, 1000.00, NULL, '141', 2.000, 2.000, 2.000, '2024-06-26 08:49:27', '2024-06-26 08:49:27'),
(23, 5, 1, 100.000, 110.000, 10.000, 1000.00, NULL, '151', 2.000, 2.000, 2.000, '2024-06-26 08:50:01', '2024-06-26 08:50:01'),
(24, 6, 1, 10.000, 11.000, 10.000, 20.00, NULL, '1511', 3.000, 3.000, 3.000, '2024-06-26 08:51:12', '2024-06-26 08:51:12'),
(25, 6, 1, 10.000, 11.000, 10.000, 20.00, NULL, '21', 2.000, 2.000, 2.000, '2024-06-26 08:56:54', '2024-06-26 08:56:54'),
(26, 1, 1, 10.000, 10.500, 5.000, 15.00, NULL, '22', 12.000, 11.000, 12.000, '2024-06-26 09:05:45', '2024-06-28 05:07:27'),
(27, 6, 1, 10.000, 11.000, 10.000, 20.00, NULL, '18', 1.000, 1.000, 1.000, '2024-06-26 09:18:48', '2024-06-26 09:18:48'),
(28, 1, 1, 10.000, 10.500, 5.000, 15.00, NULL, '133', 12.000, 12.000, 12.000, '2024-06-26 09:30:43', '2024-06-26 09:30:43'),
(29, 6, 1, 10.000, 11.000, 10.000, 20.00, NULL, '1232', 2.000, 2.000, 2.000, '2024-06-26 09:31:52', '2024-06-26 09:31:52'),
(30, 6, 1, 10.000, 11.000, 10.000, 20.00, NULL, '12333', 2.000, 2.000, 2.000, '2024-06-26 09:32:31', '2024-06-26 09:32:31'),
(31, 1, 1, 10.000, 10.500, 5.000, 15.00, NULL, '4555', 2.000, 2.000, 2.000, '2024-06-26 09:38:34', '2024-06-26 09:38:34'),
(32, 6, 1, 10.000, 11.000, 10.000, 20.00, NULL, '344', 2.000, 2.000, 2.000, '2024-06-26 09:46:36', '2024-06-26 09:46:36'),
(33, 6, 1, 10.000, 11.000, 10.000, 20.00, NULL, '777', 2.000, 2.000, 2.000, '2024-06-26 09:51:14', '2024-06-26 09:51:14'),
(34, 6, 1, 10.000, 11.000, 10.000, 20.00, NULL, '55', 2.000, 2.000, 2.000, '2024-06-26 13:30:18', '2024-06-26 13:30:18'),
(35, 5, 1, 100.000, 110.000, 10.000, 1000.00, NULL, '234', 12.000, 12.000, 12.000, '2024-06-26 13:31:11', '2024-06-26 13:31:11'),
(36, 5, 1, 100.000, 110.000, 10.000, 1000.00, NULL, '111111', 12.000, 12.000, 12.000, '2024-06-26 13:31:38', '2024-06-26 13:31:38'),
(37, 6, 1, 10.000, 11.000, 10.000, 20.00, NULL, '12345', 2.000, 2.000, 2.000, '2024-06-26 13:32:17', '2024-06-26 13:32:17'),
(38, 6, 1, 10.000, 11.000, 10.000, 20.00, NULL, '1234567', 2.000, 2.000, 2.000, '2024-06-26 13:32:46', '2024-06-26 13:32:46'),
(39, 5, 1, 100.000, 110.000, 10.000, 1000.00, NULL, '12345678', 12.000, 12.000, 12.000, '2024-06-26 13:33:27', '2024-06-26 13:33:27'),
(40, 6, 1, 10.000, 11.000, 10.000, 20.00, NULL, 'dfgh', 12.000, 12.000, 12.000, '2024-06-26 13:34:10', '2024-06-29 05:21:25'),
(41, 8, 1, 100.000, 110.000, 10.000, 1000.00, NULL, 'gdsgg', 2.000, 2.000, 2.000, '2024-06-29 04:35:27', '2024-06-29 04:35:27'),
(42, 4, 1, 100.000, 110.000, 10.000, 120.00, NULL, '4265672576', 10.000, 10.000, 10.000, '2024-06-29 06:02:27', '2024-06-29 09:17:45'),
(43, 2, 1, 20.000, 21.000, 5.000, 30.00, NULL, '4265672576', 12.000, 12.000, 12.000, '2024-06-29 06:02:27', '2024-06-29 09:17:45'),
(44, 1, 1, 10.000, 10.500, 5.000, 15.00, NULL, '4265672576', 12.000, 12.000, 12.000, '2024-06-29 06:02:27', '2024-06-29 09:17:45'),
(45, 2, 1, 20.000, 21.000, 5.000, 30.00, NULL, '678956735', 10.000, 10.000, 10.000, '2024-06-29 09:18:58', '2024-06-29 09:18:58'),
(46, 4, 1, 100.000, 110.000, 10.000, 120.00, NULL, '678956735', 10.000, 10.000, 10.000, '2024-06-29 09:18:58', '2024-06-29 09:18:58'),
(47, 6, 1, 10.000, 11.000, 10.000, 20.00, NULL, '678956735', 12.000, 12.000, 12.000, '2024-06-29 09:18:58', '2024-06-29 09:18:58'),
(48, 7, 1, 100.000, 110.000, 10.000, 1000.00, NULL, '678956735', 5.000, 5.000, 5.000, '2024-06-29 09:18:58', '2024-06-29 09:18:58'),
(49, 4, 1, 100.000, 110.000, 10.000, 120.00, NULL, 'ergrtg', 10.000, 10.000, 10.000, '2024-06-29 09:21:53', '2024-06-29 09:21:53'),
(50, 2, 1, 20.000, 21.000, 5.000, 30.00, NULL, 'rfegrtg', 2.000, 2.000, 2.000, '2024-06-29 09:27:23', '2024-06-29 09:27:23');

-- --------------------------------------------------------

--
-- Table structure for table `stock_purchase_reports`
--

CREATE TABLE `stock_purchase_reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` int(255) NOT NULL,
  `receipt_no` varchar(255) NOT NULL,
  `purchase_trans_id` varchar(255) NOT NULL COMMENT 'PID-BranchID-UserID-ProductID-Purchaserandomno-Productcountrandom',
  `product_id` int(255) NOT NULL,
  `user_id` int(255) NOT NULL,
  `branch_id` int(255) NOT NULL,
  `PBuycost` decimal(18,3) NOT NULL,
  `PBuycostRate` decimal(18,3) DEFAULT NULL,
  `PSellcost` decimal(18,3) NOT NULL,
  `quantity` decimal(18,3) NOT NULL,
  `remain_main_quantity` decimal(18,3) DEFAULT 0.000,
  `sell_quantity` decimal(18,3) DEFAULT 0.000,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_purchase_reports`
--

INSERT INTO `stock_purchase_reports` (`id`, `purchase_id`, `receipt_no`, `purchase_trans_id`, `product_id`, `user_id`, `branch_id`, `PBuycost`, `PBuycostRate`, `PSellcost`, `quantity`, `remain_main_quantity`, `sell_quantity`, `created_at`, `updated_at`) VALUES
(1, 1, '122121', 'PID11111', 1, 1, 1, 10.000, 10.500, 15.000, 5.000, 5.000, 0.000, '2024-06-21 05:18:15', '2024-06-22 05:12:55'),
(2, 2, '122121', 'PID11212', 2, 1, 1, 20.000, 21.000, 30.000, 6.000, 6.000, 0.000, '2024-06-21 05:18:16', '2024-06-22 05:33:10'),
(3, 3, 'tyhyt', 'PID22331', 3, 2, 2, 10.000, 10.400, 20.000, 10.000, 10.000, 9.000, '2024-06-21 08:56:47', '2024-06-21 08:57:04'),
(4, 4, 'grtgtrg', 'PID11141', 1, 1, 1, 10.000, 10.500, 15.000, 10.000, 10.000, 0.000, '2024-06-22 06:44:43', '2024-06-23 05:42:25'),
(5, 5, 'grtgtrg', 'PID11242', 2, 1, 1, 20.000, 21.000, 30.000, 10.000, 10.000, 0.000, '2024-06-22 06:44:43', '2024-06-29 10:03:54'),
(6, 6, '122121FGBF', 'PID11261', 2, 1, 1, 20.000, 21.000, 30.000, 10.000, 10.000, 6.000, '2024-06-22 08:13:53', '2024-07-02 02:36:52'),
(7, 7, '111', 'PID11171', 1, 1, 1, 10.000, 10.500, 15.000, 10.000, 10.000, 1.000, '2024-06-23 05:43:02', '2024-06-29 09:59:32'),
(8, 8, '1233', 'PID11481', 4, 1, 1, 100.000, 110.000, 120.000, 10.000, 10.000, 7.000, '2024-06-23 07:25:15', '2024-06-29 09:59:32'),
(9, 9, '1221215', 'PID11491', 4, 1, 1, 100.000, 110.000, 120.000, 1.000, 1.000, 1.000, '2024-06-24 03:06:54', '2024-06-24 03:06:54'),
(10, 10, '12', 'PID111101', 1, 1, 1, 10.000, 10.500, 15.000, 100.000, 100.000, 100.000, '2024-06-24 08:21:21', '2024-06-24 08:21:21'),
(11, 12, 'req', 'PID111111', 1, 1, 1, 10.000, 10.500, 15.000, 2.000, 1.000, 1.000, '2024-06-25 07:01:51', '2024-06-25 07:02:09'),
(12, 13, 'teq', 'PID111121', 1, 1, 1, 10.000, 10.500, 15.000, 2.000, 1.000, 1.000, '2024-06-25 07:02:43', '2024-06-25 07:02:59'),
(13, 14, 'teq', 'PID112122', 2, 1, 1, 20.000, 21.000, 30.000, 3.000, 3.000, 3.000, '2024-06-25 07:02:43', '2024-06-25 07:02:43'),
(14, 15, '111e', 'PID116141', 6, 1, 1, 10.000, 11.000, 20.000, 2.000, 2.000, 0.000, '2024-06-25 09:53:51', '2024-06-25 09:54:50'),
(15, 16, 'sadsd', 'PID116151', 6, 1, 1, 10.000, 11.000, 20.000, 3.000, 3.000, 0.000, '2024-06-25 09:54:15', '2024-06-28 03:04:44'),
(16, 17, '1111', 'PID111161', 1, 1, 1, 10.000, 10.500, 15.000, 2.000, 2.000, 2.000, '2024-06-26 08:18:17', '2024-06-26 08:18:17'),
(17, 18, '1111', 'PID112162', 2, 1, 1, 20.000, 21.000, 30.000, 2.000, 2.000, 2.000, '2024-06-26 08:18:17', '2024-06-26 08:18:17'),
(18, 19, '13', 'PID116181', 6, 1, 1, 10.000, 11.000, 20.000, 12.000, 12.000, 8.000, '2024-06-26 08:22:32', '2024-06-29 04:39:11'),
(19, 20, '14', 'PID115191', 5, 1, 1, 100.000, 110.000, 1000.000, 3.000, 3.000, 0.000, '2024-06-26 08:28:32', '2024-06-28 04:14:57'),
(20, 21, '15', 'PID116201', 6, 1, 1, 10.000, 11.000, 20.000, 1.000, 1.000, 1.000, '2024-06-26 08:30:54', '2024-06-26 08:30:54'),
(21, 22, '141', 'PID115211', 5, 1, 1, 100.000, 110.000, 1000.000, 2.000, 2.000, 0.000, '2024-06-26 08:49:27', '2024-06-28 04:16:51'),
(22, 23, '151', 'PID115221', 5, 1, 1, 100.000, 110.000, 1000.000, 2.000, 2.000, 2.000, '2024-06-26 08:50:01', '2024-06-26 08:50:01'),
(23, 24, '1511', 'PID116231', 6, 1, 1, 10.000, 11.000, 20.000, 3.000, 3.000, 3.000, '2024-06-26 08:51:12', '2024-06-26 08:51:12'),
(24, 25, '21', 'PID116241', 6, 1, 1, 10.000, 11.000, 20.000, 2.000, 2.000, 2.000, '2024-06-26 08:56:54', '2024-06-26 08:56:54'),
(25, 26, '22', 'PID111251', 1, 1, 1, 10.000, 10.500, 15.000, 12.000, 11.000, 11.000, '2024-06-26 09:05:45', '2024-06-28 05:07:27'),
(26, 27, '18', 'PID116261', 6, 1, 1, 10.000, 11.000, 20.000, 1.000, 1.000, 1.000, '2024-06-26 09:18:48', '2024-06-26 09:18:48'),
(27, 28, '133', 'PID111271', 1, 1, 1, 10.000, 10.500, 15.000, 12.000, 12.000, 12.000, '2024-06-26 09:30:43', '2024-06-26 09:30:43'),
(28, 29, '1232', 'PID116281', 6, 1, 1, 10.000, 11.000, 20.000, 2.000, 2.000, 2.000, '2024-06-26 09:31:52', '2024-06-26 09:31:52'),
(29, 30, '12333', 'PID116291', 6, 1, 1, 10.000, 11.000, 20.000, 2.000, 2.000, 2.000, '2024-06-26 09:32:31', '2024-06-26 09:32:31'),
(30, 32, '4555', 'PID111301', 1, 1, 1, 10.000, 10.500, 15.000, 2.000, 2.000, 2.000, '2024-06-26 09:38:34', '2024-06-26 09:38:34'),
(31, 33, '344', 'PID116311', 6, 1, 1, 10.000, 11.000, 20.000, 2.000, 2.000, 2.000, '2024-06-26 09:46:36', '2024-06-26 09:46:36'),
(32, 34, '777', 'PID116321', 6, 1, 1, 10.000, 11.000, 20.000, 0.000, 0.000, 0.000, '2024-06-26 09:51:14', '2024-06-26 09:51:14'),
(33, 35, '55', 'PID116331', 6, 1, 1, 10.000, 11.000, 20.000, 2.000, 2.000, 2.000, '2024-06-26 13:30:18', '2024-06-26 13:30:18'),
(34, 36, '234', 'PID115341', 5, 1, 1, 100.000, 110.000, 1000.000, 12.000, 12.000, 12.000, '2024-06-26 13:31:11', '2024-06-26 13:31:11'),
(35, 37, '111111', 'PID115351', 5, 1, 1, 100.000, 110.000, 1000.000, 12.000, 12.000, 12.000, '2024-06-26 13:31:38', '2024-06-26 13:31:38'),
(36, 38, '12345', 'PID116361', 6, 1, 1, 10.000, 11.000, 20.000, 2.000, 2.000, 2.000, '2024-06-26 13:32:17', '2024-06-26 13:32:17'),
(37, 39, '1234567', 'PID116371', 6, 1, 1, 10.000, 11.000, 20.000, 2.000, 2.000, 2.000, '2024-06-26 13:32:46', '2024-06-26 13:32:46'),
(38, 40, '12345678', 'PID115381', 5, 1, 1, 100.000, 110.000, 1000.000, 12.000, 12.000, 12.000, '2024-06-26 13:33:27', '2024-06-26 13:33:27'),
(39, 41, 'dfgh', 'PID116391', 6, 1, 1, 10.000, 11.000, 20.000, 12.000, 12.000, 12.000, '2024-06-26 13:34:10', '2024-06-29 05:21:25'),
(40, 42, 'gdsgg', 'PID118401', 8, 1, 1, 100.000, 110.000, 1000.000, 2.000, 2.000, 0.000, '2024-06-29 04:35:27', '2024-06-29 04:39:11'),
(41, 43, '4265672576', 'PID114411', 4, 1, 1, 100.000, 110.000, 120.000, 10.000, 10.000, 10.000, '2024-06-29 06:02:27', '2024-06-29 09:17:45'),
(42, 44, '4265672576', 'PID112412', 2, 1, 1, 20.000, 21.000, 30.000, 12.000, 12.000, 12.000, '2024-06-29 06:02:27', '2024-06-29 09:17:45'),
(43, 45, '4265672576', 'PID111433', 1, 1, 1, 10.000, 10.500, 15.000, 12.000, 12.000, 12.000, '2024-06-29 06:02:27', '2024-06-29 09:17:45'),
(44, 46, '678956735', 'PID112441', 2, 1, 1, 20.000, 21.000, 30.000, 10.000, 10.000, 10.000, '2024-06-29 09:18:58', '2024-06-29 09:18:58'),
(45, 47, '678956735', 'PID114442', 4, 1, 1, 100.000, 110.000, 120.000, 10.000, 10.000, 10.000, '2024-06-29 09:18:58', '2024-06-29 09:18:58'),
(46, 48, '678956735', 'PID116443', 6, 1, 1, 10.000, 11.000, 20.000, 12.000, 12.000, 12.000, '2024-06-29 09:18:58', '2024-06-29 09:18:58'),
(47, 49, '678956735', 'PID117444', 7, 1, 1, 100.000, 110.000, 1000.000, 5.000, 5.000, 5.000, '2024-06-29 09:18:58', '2024-06-29 09:18:58'),
(48, 50, 'ergrtg', 'PID114481', 4, 1, 1, 100.000, 110.000, 120.000, 10.000, 10.000, 10.000, '2024-06-29 09:21:53', '2024-06-29 09:21:53'),
(49, 51, 'rfegrtg', 'PID112491', 2, 1, 1, 20.000, 21.000, 30.000, 2.000, 2.000, 2.000, '2024-06-29 09:27:23', '2024-06-29 09:27:23');

-- --------------------------------------------------------

--
-- Table structure for table `superusers`
--

CREATE TABLE `superusers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `superusers`
--

INSERT INTO `superusers` (`id`, `username`, `password`, `created_at`, `updated_at`) VALUES
(1, 'super1', '$2y$10$cLDoWKWf2EOv4O0hwzsDrOkMytgba/GcDkmhTVMYRihlr3ZZ50yfW', NULL, '2022-10-01 20:51:31');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `mobile` bigint(20) DEFAULT NULL,
  `email` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `trn_number` varchar(255) DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `adminuser` int(11) DEFAULT NULL,
  `softwareuser` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `mobile`, `email`, `trn_number`, `address`, `location`, `adminuser`, `softwareuser`, `created_at`, `updated_at`) VALUES
(1, 'supplier1', NULL, NULL, NULL, NULL, '1', NULL, 1, '2024-06-21 05:18:15', '2024-06-21 05:18:15'),
(2, 'supplier_1', NULL, NULL, NULL, NULL, '2', NULL, 2, '2024-06-21 08:56:47', '2024-06-21 08:56:47'),
(3, 'gdfgdf', NULL, NULL, NULL, NULL, '1', NULL, 1, '2024-06-29 04:35:27', '2024-06-29 04:35:27');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_credits`
--

CREATE TABLE `supplier_credits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` int(255) NOT NULL,
  `due_amt` decimal(18,3) DEFAULT 0.000,
  `collected_amt` decimal(18,3) DEFAULT 0.000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supplier_credits`
--

INSERT INTO `supplier_credits` (`id`, `supplier_id`, `due_amt`, `collected_amt`, `created_at`, `updated_at`) VALUES
(1, 1, 3713.000, 40.000, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `supplier_fund_histories`
--

CREATE TABLE `supplier_fund_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supplierid` int(255) NOT NULL,
  `suppliername` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `due` decimal(18,3) NOT NULL,
  `collectedamount` decimal(18,3) NOT NULL,
  `userid` int(255) NOT NULL,
  `branch` int(255) NOT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `reciept_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_type` int(255) DEFAULT NULL COMMENT '1-cash, 2-check',
  `check_number` bigint(255) DEFAULT NULL,
  `depositing_date` date DEFAULT NULL,
  `reference_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bank_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `supplier_fund_histories`
--

INSERT INTO `supplier_fund_histories` (`id`, `supplierid`, `suppliername`, `due`, `collectedamount`, `userid`, `branch`, `status`, `reciept_no`, `payment_type`, `check_number`, `depositing_date`, `reference_number`, `bank_name`, `created_at`, `updated_at`) VALUES
(1, 1, 'supplier1', 0.000, 10.000, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-22 08:12:19', '2024-06-22 08:12:19'),
(2, 1, 'supplier1', -10.000, 10.000, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-22 08:12:37', '2024-06-22 08:12:37'),
(3, 1, 'supplier1', -20.000, 10.000, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-06-22 08:13:15', '2024-06-22 08:13:15'),
(4, 1, 'supplier1', 180.000, 10.000, 1, 1, NULL, '122121FGBF', 1, NULL, NULL, '673457846', 'ifc', '2024-06-22 08:21:23', '2024-06-22 08:21:23');

-- --------------------------------------------------------

--
-- Table structure for table `termsandconditions`
--

CREATE TABLE `termsandconditions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `termsandcondition` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `invoice_id` int(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `unit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `unit`, `user_id`, `branch_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'kg', 1, 1, 1, '2024-06-21 04:09:46', '2024-06-21 04:09:46'),
(2, 'n', 1, 1, 1, '2024-06-21 04:09:53', '2024-06-21 04:09:53'),
(3, 'kg', 2, 2, 1, '2024-06-21 08:56:10', '2024-06-21 08:56:10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_reports`
--

CREATE TABLE `user_reports` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `trans_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `opening_balance` decimal(18,3) DEFAULT NULL,
  `total_sales` int(255) DEFAULT NULL,
  `total_sales_amount` decimal(18,3) DEFAULT NULL,
  `total_amount` decimal(18,3) DEFAULT NULL,
  `total_cash` decimal(18,3) DEFAULT NULL,
  `branch` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_reports`
--

INSERT INTO `user_reports` (`id`, `user_id`, `trans_id`, `opening_balance`, `total_sales`, `total_sales_amount`, `total_amount`, `total_cash`, `branch`, `created_at`, `updated_at`) VALUES
(1, 1, 'UR1', 1000.000, 0, 0.000, 1000.000, 110.000, 1, '2024-06-28 02:27:14', '2024-06-28 02:27:14'),
(2, 1, 'UR2', 1000.000, 1, 15.000, 1015.000, 10.000, 1, '2024-06-28 02:27:49', '2024-06-28 02:27:49'),
(3, 1, 'UR3', 1000.000, 1, 15.000, 1015.000, 10.000, 1, '2024-06-28 02:28:26', '2024-06-28 02:28:26'),
(4, 1, 'UR4', 100.000, 1, 15.000, 115.000, 10.000, 1, '2024-06-28 03:00:56', '2024-06-28 03:00:56'),
(5, 1, 'UR5', 1000.000, 9, 2885.900, 3885.900, 55.000, 1, '2024-06-28 04:19:28', '2024-06-28 04:19:28'),
(6, 1, 'UR6', 1000.000, 11, 2935.900, 3935.900, 5.000, 1, '2024-06-28 04:30:58', '2024-06-28 04:30:58');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL,
  `user_id` int(100) NOT NULL,
  `role_id` int(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2024-06-21 04:08:39', '2024-06-21 04:08:39'),
(2, 1, 2, '2024-06-21 04:08:39', '2024-06-21 04:08:39'),
(3, 1, 9, '2024-06-21 04:08:39', '2024-06-21 04:08:39'),
(4, 1, 11, '2024-06-21 04:08:39', '2024-06-21 04:08:39'),
(5, 1, 17, '2024-06-21 04:08:39', '2024-06-21 04:08:39'),
(6, 1, 18, '2024-06-21 04:08:39', '2024-06-21 04:08:39'),
(7, 1, 19, '2024-06-21 04:08:39', '2024-06-21 04:08:39'),
(8, 1, 20, '2024-06-21 04:08:39', '2024-06-21 04:08:39'),
(9, 1, 21, '2024-06-21 04:08:39', '2024-06-21 04:08:39'),
(10, 1, 26, '2024-06-21 04:08:39', '2024-06-21 04:08:39'),
(11, 2, 1, '2024-06-21 04:08:52', '2024-06-21 04:08:52'),
(12, 2, 2, '2024-06-21 04:08:52', '2024-06-21 04:08:52'),
(13, 2, 9, '2024-06-21 04:08:52', '2024-06-21 04:08:52'),
(14, 2, 11, '2024-06-21 04:08:52', '2024-06-21 04:08:52'),
(15, 2, 17, '2024-06-21 04:08:52', '2024-06-21 04:08:52'),
(16, 2, 18, '2024-06-21 04:08:52', '2024-06-21 04:08:52'),
(17, 2, 19, '2024-06-21 04:08:52', '2024-06-21 04:08:52'),
(18, 2, 20, '2024-06-21 04:08:52', '2024-06-21 04:08:52'),
(19, 2, 21, '2024-06-21 04:08:52', '2024-06-21 04:08:52'),
(20, 2, 26, '2024-06-21 04:08:52', '2024-06-21 04:08:52'),
(21, 3, 1, '2024-06-21 04:09:10', '2024-06-21 04:09:10'),
(22, 3, 2, '2024-06-21 04:09:10', '2024-06-21 04:09:10'),
(23, 3, 9, '2024-06-21 04:09:10', '2024-06-21 04:09:10'),
(24, 3, 11, '2024-06-21 04:09:10', '2024-06-21 04:09:10'),
(25, 3, 17, '2024-06-21 04:09:10', '2024-06-21 04:09:10'),
(26, 3, 18, '2024-06-21 04:09:10', '2024-06-21 04:09:10'),
(27, 3, 19, '2024-06-21 04:09:10', '2024-06-21 04:09:10'),
(28, 3, 20, '2024-06-21 04:09:10', '2024-06-21 04:09:10'),
(29, 3, 21, '2024-06-21 04:09:10', '2024-06-21 04:09:10'),
(30, 3, 26, '2024-06-21 04:09:10', '2024-06-21 04:09:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accountantlocs`
--
ALTER TABLE `accountantlocs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `accountexpenses`
--
ALTER TABLE `accountexpenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `account_indirect_incomes`
--
ALTER TABLE `account_indirect_incomes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `addstocks`
--
ALTER TABLE `addstocks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `adminusers`
--
ALTER TABLE `adminusers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `balance_stores`
--
ALTER TABLE `balance_stores`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `billdraft`
--
ALTER TABLE `billdraft`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bill_histories`
--
ALTER TABLE `bill_histories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `buyproducts`
--
ALTER TABLE `buyproducts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cash_notes`
--
ALTER TABLE `cash_notes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cash_trans_statements`
--
ALTER TABLE `cash_trans_statements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `creditsummaries`
--
ALTER TABLE `creditsummaries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `creditusers`
--
ALTER TABLE `creditusers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `credit_supplier_transactions`
--
ALTER TABLE `credit_supplier_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `credit_transactions`
--
ALTER TABLE `credit_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `delivery_notes`
--
ALTER TABLE `delivery_notes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `delivery_notes_draft`
--
ALTER TABLE `delivery_notes_draft`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `finalreports`
--
ALTER TABLE `finalreports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fundhistories`
--
ALTER TABLE `fundhistories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hrusercreations`
--
ALTER TABLE `hrusercreations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hruserroles`
--
ALTER TABLE `hruserroles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoicedatas`
--
ALTER TABLE `invoicedatas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoiceproducts`
--
ALTER TABLE `invoiceproducts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoice_types`
--
ALTER TABLE `invoice_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pand_l_amounts`
--
ALTER TABLE `pand_l_amounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pand_l_s`
--
ALTER TABLE `pand_l_s`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `performance_invoices`
--
ALTER TABLE `performance_invoices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `performance_invoices_draft`
--
ALTER TABLE `performance_invoices_draft`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `plexpayusers`
--
ALTER TABLE `plexpayusers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `printer_statuses`
--
ALTER TABLE `printer_statuses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `productdraft`
--
ALTER TABLE `productdraft`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchasedraft`
--
ALTER TABLE `purchasedraft`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quotations`
--
ALTER TABLE `quotations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quotations_draft`
--
ALTER TABLE `quotations_draft`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rawilk_prints`
--
ALTER TABLE `rawilk_prints`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `returnproducts`
--
ALTER TABLE `returnproducts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `returnpurchases`
--
ALTER TABLE `returnpurchases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `salarydatas`
--
ALTER TABLE `salarydatas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales_orders_draft`
--
ALTER TABLE `sales_orders_draft`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `softwareusers`
--
ALTER TABLE `softwareusers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stockdats`
--
ALTER TABLE `stockdats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stockdetails`
--
ALTER TABLE `stockdetails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stockhistories`
--
ALTER TABLE `stockhistories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_purchase_reports`
--
ALTER TABLE `stock_purchase_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supplier_credits`
--
ALTER TABLE `supplier_credits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supplier_fund_histories`
--
ALTER TABLE `supplier_fund_histories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `termsandconditions`
--
ALTER TABLE `termsandconditions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_reports`
--
ALTER TABLE `user_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accountantlocs`
--
ALTER TABLE `accountantlocs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `accountexpenses`
--
ALTER TABLE `accountexpenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `account_indirect_incomes`
--
ALTER TABLE `account_indirect_incomes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=815;

--
-- AUTO_INCREMENT for table `addstocks`
--
ALTER TABLE `addstocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `adminusers`
--
ALTER TABLE `adminusers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `balance_stores`
--
ALTER TABLE `balance_stores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `billdraft`
--
ALTER TABLE `billdraft`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `bill_histories`
--
ALTER TABLE `bill_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `buyproducts`
--
ALTER TABLE `buyproducts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `cash_notes`
--
ALTER TABLE `cash_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cash_trans_statements`
--
ALTER TABLE `cash_trans_statements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `creditsummaries`
--
ALTER TABLE `creditsummaries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `creditusers`
--
ALTER TABLE `creditusers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `credit_supplier_transactions`
--
ALTER TABLE `credit_supplier_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `credit_transactions`
--
ALTER TABLE `credit_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `delivery_notes`
--
ALTER TABLE `delivery_notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `delivery_notes_draft`
--
ALTER TABLE `delivery_notes_draft`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `finalreports`
--
ALTER TABLE `finalreports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fundhistories`
--
ALTER TABLE `fundhistories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `hrusercreations`
--
ALTER TABLE `hrusercreations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hruserroles`
--
ALTER TABLE `hruserroles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoicedatas`
--
ALTER TABLE `invoicedatas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoiceproducts`
--
ALTER TABLE `invoiceproducts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_types`
--
ALTER TABLE `invoice_types`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pand_l_amounts`
--
ALTER TABLE `pand_l_amounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pand_l_s`
--
ALTER TABLE `pand_l_s`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `performance_invoices`
--
ALTER TABLE `performance_invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `performance_invoices_draft`
--
ALTER TABLE `performance_invoices_draft`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `plexpayusers`
--
ALTER TABLE `plexpayusers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `printer_statuses`
--
ALTER TABLE `printer_statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `productdraft`
--
ALTER TABLE `productdraft`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `purchasedraft`
--
ALTER TABLE `purchasedraft`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `quotations`
--
ALTER TABLE `quotations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `quotations_draft`
--
ALTER TABLE `quotations_draft`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rawilk_prints`
--
ALTER TABLE `rawilk_prints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `returnproducts`
--
ALTER TABLE `returnproducts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `returnpurchases`
--
ALTER TABLE `returnpurchases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `salarydatas`
--
ALTER TABLE `salarydatas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_orders`
--
ALTER TABLE `sales_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sales_orders_draft`
--
ALTER TABLE `sales_orders_draft`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `softwareusers`
--
ALTER TABLE `softwareusers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stockdats`
--
ALTER TABLE `stockdats`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `stockdetails`
--
ALTER TABLE `stockdetails`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `stockhistories`
--
ALTER TABLE `stockhistories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `stock_purchase_reports`
--
ALTER TABLE `stock_purchase_reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `supplier_credits`
--
ALTER TABLE `supplier_credits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `supplier_fund_histories`
--
ALTER TABLE `supplier_fund_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `termsandconditions`
--
ALTER TABLE `termsandconditions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_reports`
--
ALTER TABLE `user_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
