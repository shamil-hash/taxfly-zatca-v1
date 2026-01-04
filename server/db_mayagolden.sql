-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 21, 2022 at 08:39 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.0.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_mayagolden`
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
  `comment` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(18,3) NOT NULL,
  `month` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `adminusers`
--

CREATE TABLE `adminusers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `po_box` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cr_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `adminusers`
--

INSERT INTO `adminusers` (`id`, `name`, `username`, `password`, `email`, `phone`, `currency`, `po_box`, `postal_code`, `cr_number`, `created_at`, `updated_at`) VALUES
(1, 'whiteline', 'whiteline', '$2y$10$oJhIELS1Ig0U5yCQjJdIM.rRj83awAOfUhRlxapmse4RsNH2TKieS', 'email@', '00000000', '', '00000', '00000', '00000', '2022-02-17 02:44:24', '2022-02-17 02:44:24');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branchname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `location`, `branchname`, `mobile`, `created_at`, `updated_at`) VALUES
(1, 'branch1', 'BRANCH 1', 0, '2022-02-17 02:46:03', '2022-02-17 02:46:03');

-- --------------------------------------------------------

--
-- Table structure for table `buyproducts`
--

CREATE TABLE `buyproducts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` int(255) NOT NULL,
  `quantity` decimal(18,2) NOT NULL,
  `mrp` decimal(18,3) NOT NULL,
  `price` decimal(18,3) NOT NULL,
  `vat_amount` decimal(18,3) NOT NULL,
  `fixed_vat` int(11) NOT NULL,
  `branch` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trn_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_type` int(255) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int(255) DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_amount` decimal(18,3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `buyproducts`
--

INSERT INTO `buyproducts` (`id`, `product_name`, `product_id`, `quantity`, `mrp`, `price`, `vat_amount`, `fixed_vat`, `branch`, `transaction_id`, `customer_name`, `trn_number`, `phone`, `payment_type`, `created_at`, `updated_at`, `user_id`, `email`, `total_amount`) VALUES
(1, 'product 1', 1, '2.00', '40.000', '80.000', '4.000', 5, '1', 'BT1', '1645077900146', NULL, NULL, 1, '2022-02-17 04:35:07', '2022-02-17 04:35:07', 1, NULL, '84.000');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(255) NOT NULL,
  `branch_id` int(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category_name`, `user_id`, `branch_id`, `created_at`, `updated_at`) VALUES
(2, 'FOOD', 1, 1, '2022-02-21 05:36:11', '2022-02-21 05:36:11');

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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `creditsummaries`
--

INSERT INTO `creditsummaries` (`id`, `credituser_id`, `due_amount`, `collected_amount`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, '10.000', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `creditusers`
--

CREATE TABLE `creditusers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` int(255) NOT NULL,
  `admin_id` int(255) NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `trn_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `creditusers`
--

INSERT INTO `creditusers` (`id`, `name`, `username`, `location`, `admin_id`, `password`, `status`, `created_at`, `updated_at`, `trn_number`, `phone`, `email`) VALUES
(1, 'CREDIT USER', 'credituser', 1, 1, '$2y$10$CyuE7ufKz0DSo6y9AmV1cue57KMNitP3sGSY4m5J8.3.6zgx/5CSu', 1, '2022-02-17 04:39:21', '2022-02-17 04:39:21', '000000', '00000000000', 'email@');

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
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `finalreports`
--

CREATE TABLE `finalreports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(255) NOT NULL,
  `date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fundhistories`
--

CREATE TABLE `fundhistories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `due` decimal(18,3) NOT NULL,
  `amount` decimal(18,3) NOT NULL,
  `credituser_id` int(255) NOT NULL,
  `user_id` int(255) NOT NULL,
  `location` int(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fundhistories`
--

INSERT INTO `fundhistories` (`id`, `username`, `due`, `amount`, `credituser_id`, `user_id`, `location`, `created_at`, `updated_at`) VALUES
(1, 'credituser', '0.000', '10.000', 1, 1, 1, '2022-02-17 04:40:31', '2022-02-17 04:40:31');

-- --------------------------------------------------------

--
-- Table structure for table `hrusercreations`
--

CREATE TABLE `hrusercreations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` int(255) NOT NULL,
  `admin_id` int(255) NOT NULL,
  `joining_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `from_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_trnnumber` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_type` int(255) NOT NULL,
  `to_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `to_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `to_trnnumber` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `to_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `heading` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `footer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `due_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoiceproducts`
--

CREATE TABLE `invoiceproducts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
(32, '2022_02_16_092856_create_plexpayusers_table', 26);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` int(11) NOT NULL,
  `module` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(15, 'plexpay');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `module_roles`
--

INSERT INTO `module_roles` (`id`, `user_id`, `module_id`, `created_at`, `updated_at`) VALUES
(0, 1, 1, '2022-02-17 02:44:40.000000', '2022-02-17 02:44:40.000000'),
(0, 1, 3, '2022-02-17 02:44:40.000000', '2022-02-17 02:44:40.000000'),
(0, 1, 11, '2022-02-17 02:44:40.000000', '2022-02-17 02:44:40.000000'),
(0, 1, 8, '2022-02-17 02:44:40.000000', '2022-02-17 02:44:40.000000'),
(0, 1, 12, '2022-02-17 02:44:40.000000', '2022-02-17 02:44:40.000000'),
(0, 1, 14, '2022-02-17 02:44:40.000000', '2022-02-17 02:44:40.000000'),
(0, 1, 15, '2022-02-17 02:44:40.000000', '2022-02-17 02:44:40.000000');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(255) NOT NULL,
  `product_id` int(255) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`id`, `type`) VALUES
(1, 'CASH'),
(2, 'BANK'),
(3, 'CREDIT');

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
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `user_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plexbill_userid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `plexpayusers`
--

INSERT INTO `plexpayusers` (`id`, `user_id`, `password`, `plexbill_userid`, `created_at`, `updated_at`) VALUES
(1, 'SHOP20', '12345', '1', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `buy_cost` decimal(18,2) NOT NULL,
  `selling_cost` decimal(18,2) NOT NULL,
  `vat` decimal(18,3) NOT NULL DEFAULT 5.000,
  `user_id` int(255) DEFAULT NULL,
  `branch` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` int(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `stock` decimal(18,2) NOT NULL DEFAULT 0.00,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_name`, `buy_cost`, `selling_cost`, `vat`, `user_id`, `branch`, `category_id`, `created_at`, `updated_at`, `status`, `stock`, `image`) VALUES
(1, 'product 1', '34.00', '40.00', '5.000', 1, '1', 2, '2022-02-17 04:34:58', '2022-02-21 05:36:18', 1, '15.00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `returnproducts`
--

CREATE TABLE `returnproducts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(18,2) NOT NULL,
  `mrp` decimal(18,2) NOT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(18,2) NOT NULL,
  `vat` decimal(18,2) NOT NULL,
  `branch` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int(255) DEFAULT NULL,
  `product_id` int(255) DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trn_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_amount` decimal(18,3) DEFAULT NULL,
  `payment_type` int(255) DEFAULT NULL,
  `fixed_vat` decimal(18,3) DEFAULT NULL,
  `vat_amount` decimal(18,3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `returnpurchases`
--

CREATE TABLE `returnpurchases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reciept_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(18,2) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `shop_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch` int(255) DEFAULT NULL,
  `user_id` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `returnpurchases`
--

INSERT INTO `returnpurchases` (`id`, `reciept_no`, `comment`, `quantity`, `amount`, `shop_name`, `created_at`, `updated_at`, `branch`, `user_id`) VALUES
(1, 'tyty', 'sdfsdfsdf', '54.00', '3454354.00', 'ytytyrrt', '2022-02-17 04:57:34', '2022-02-17 04:57:34', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(255) NOT NULL,
  `role_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(15, 'plexpay');

-- --------------------------------------------------------

--
-- Table structure for table `salarydatas`
--

CREATE TABLE `salarydatas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(255) NOT NULL,
  `date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `salary` int(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `softwareusers`
--

CREATE TABLE `softwareusers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `joined_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admin_id` int(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `access` int(255) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `softwareusers`
--

INSERT INTO `softwareusers` (`id`, `name`, `username`, `password`, `location`, `joined_date`, `email`, `admin_id`, `created_at`, `updated_at`, `status`, `access`) VALUES
(1, 'STORE', 'STORE', '$2y$10$On89kDd9pnKkfeT9WxSoi.lC2G5X0D1CVy0eMwpKxxALoZGNFPI9u', '1', '2022-02-15', 'email@', 1, '2022-02-17 02:46:53', '2022-02-17 02:46:53', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `stockdats`
--

CREATE TABLE `stockdats` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` int(255) NOT NULL,
  `user_id` int(255) NOT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stock_num` decimal(18,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stockdats`
--

INSERT INTO `stockdats` (`id`, `product_id`, `user_id`, `transaction_id`, `stock_num`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'BT1', '2.00', '2022-02-17 04:35:07', '2022-02-17 04:35:07');

-- --------------------------------------------------------

--
-- Table structure for table `stockdetails`
--

CREATE TABLE `stockdetails` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(255) NOT NULL,
  `branch` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reciept_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(18,3) NOT NULL,
  `price` decimal(18,3) NOT NULL,
  `shopname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int(255) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stockdetails`
--

INSERT INTO `stockdetails` (`id`, `user_id`, `branch`, `reciept_no`, `comment`, `quantity`, `price`, `shopname`, `created_at`, `updated_at`, `file`, `status`) VALUES
(1, 1, '1', 'tyty', '565', '5.000', '654.000', 'ytytyrrt', '2022-02-17 04:57:14', '2022-02-17 04:57:14', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `stockhistories`
--

CREATE TABLE `stockhistories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` int(255) DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` decimal(18,3) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stockhistories`
--

INSERT INTO `stockhistories` (`id`, `product_id`, `product_name`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, '10.000', '2022-02-17 04:53:26', '2022-02-17 04:53:26'),
(2, 1, NULL, '5.000', '2022-02-17 04:57:14', '2022-02-17 04:57:14');

-- --------------------------------------------------------

--
-- Table structure for table `superusers`
--

CREATE TABLE `superusers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `superusers`
--

INSERT INTO `superusers` (`id`, `username`, `password`, `created_at`, `updated_at`) VALUES
(1, 'super1', '$2y$10$/ozVQi8EwJ3v3wP/lrhNIuaK6XSrUaP8CIdymmdl8c7gr2FiJXHnW', NULL, '2022-01-03 20:43:00');

-- --------------------------------------------------------

--
-- Table structure for table `termsandconditions`
--

CREATE TABLE `termsandconditions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `termsandcondition` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_id` int(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(10, 1, 1, '2022-02-21 05:39:15', '2022-02-21 05:39:15'),
(11, 1, 2, '2022-02-21 05:39:15', '2022-02-21 05:39:15'),
(12, 1, 9, '2022-02-21 05:39:15', '2022-02-21 05:39:15'),
(13, 1, 11, '2022-02-21 05:39:15', '2022-02-21 05:39:15'),
(14, 1, 15, '2022-02-21 05:39:15', '2022-02-21 05:39:15');

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
-- Indexes for table `adminusers`
--
ALTER TABLE `adminusers`
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
-- Indexes for table `plexpayusers`
--
ALTER TABLE `plexpayusers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
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
-- Indexes for table `termsandconditions`
--
ALTER TABLE `termsandconditions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
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
-- AUTO_INCREMENT for table `adminusers`
--
ALTER TABLE `adminusers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `buyproducts`
--
ALTER TABLE `buyproducts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `creditsummaries`
--
ALTER TABLE `creditsummaries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `creditusers`
--
ALTER TABLE `creditusers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `finalreports`
--
ALTER TABLE `finalreports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fundhistories`
--
ALTER TABLE `fundhistories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plexpayusers`
--
ALTER TABLE `plexpayusers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `returnproducts`
--
ALTER TABLE `returnproducts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `returnpurchases`
--
ALTER TABLE `returnpurchases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `salarydatas`
--
ALTER TABLE `salarydatas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `softwareusers`
--
ALTER TABLE `softwareusers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stockdats`
--
ALTER TABLE `stockdats`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stockdetails`
--
ALTER TABLE `stockdetails`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stockhistories`
--
ALTER TABLE `stockhistories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `termsandconditions`
--
ALTER TABLE `termsandconditions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
