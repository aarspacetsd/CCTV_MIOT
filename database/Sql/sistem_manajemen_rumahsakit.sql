-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 31 Jul 2025 pada 17.10
-- Versi server: 8.0.42-0ubuntu0.20.04.1
-- Versi PHP: 8.4.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sistem_manajemen_rumahsakit`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `appointments`
--

CREATE TABLE `appointments` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `appointment_date` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `patient_id` int UNSIGNED NOT NULL,
  `doctor_id` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `appointments`
--

INSERT INTO `appointments` (`id`, `name`, `description`, `time`, `appointment_date`, `status`, `patient_id`, `doctor_id`, `created_at`, `updated_at`) VALUES
(1, 'testing_12', NULL, '10:11', '2022-02-21 17:00:00', 0, 1, 3, '2025-07-31 00:21:10', '2025-07-31 00:59:22');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `departments`
--

CREATE TABLE `departments` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `departments`
--

INSERT INTO `departments` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'aaaaa', '2025-07-29 14:29:14', '2025-07-29 14:29:14'),
(2, 'Kardio_test', '2025-07-31 00:18:29', '2025-07-31 00:18:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `doctors`
--

CREATE TABLE `doctors` (
  `id` int UNSIGNED NOT NULL,
  `employee_id` int UNSIGNED NOT NULL,
  `fee` double NOT NULL,
  `opd_charge` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `doctors`
--

INSERT INTO `doctors` (`id`, `employee_id`, `fee`, `opd_charge`, `created_at`, `updated_at`) VALUES
(3, 2, 111111, 222222, '2025-07-31 00:15:32', '2025-07-31 00:15:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `doctor_referreds`
--

CREATE TABLE `doctor_referreds` (
  `id` int UNSIGNED NOT NULL,
  `doctor_id` int UNSIGNED NOT NULL,
  `invoice_id` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `employees`
--

CREATE TABLE `employees` (
  `id` int UNSIGNED NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `education` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `certificate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `speciality` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `working_day` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `in_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `out_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department_id` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `employees`
--

INSERT INTO `employees` (`id`, `first_name`, `middle_name`, `last_name`, `email`, `address`, `phone`, `education`, `description`, `certificate`, `speciality`, `working_day`, `in_time`, `out_time`, `type`, `department_id`, `created_at`, `updated_at`) VALUES
(2, 'DR.Ahmad', 'Akmal', 'Rijal', 'ahmadakmalrijala@gmail.com', 'Asrama', '0985335556529', 'S1', 'spesialis ginjal dalam', '-', 'Ginjal', 'Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday', '07:00', '22:22', 'Doctor', 2, '2025-07-31 00:04:23', '2025-07-31 00:18:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `examination_results`
--

CREATE TABLE `examination_results` (
  `id` int UNSIGNED NOT NULL,
  `test_report_id` int UNSIGNED NOT NULL,
  `macroscopic_result` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `microscopic_result` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `result` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `hospitals`
--

CREATE TABLE `hospitals` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slogan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pan_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `registration_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_prefix` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'LWC-',
  `patient_prefix` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'LWC-',
  `tax_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_percent` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `hospitals`
--

INSERT INTO `hospitals` (`id`, `name`, `slogan`, `logo`, `address`, `contact`, `email`, `pan_no`, `registration_no`, `invoice_message`, `website`, `description`, `invoice_prefix`, `patient_prefix`, `tax_type`, `tax_percent`, `created_at`, `updated_at`) VALUES
(1, 'Lokanthali Wellness Clinic', 'Enhancing Life, Excelling In Care...', 'uploads/logo.png', 'Lokanthali-1, Madhyapur Thimi, Bhaktpur, Nepal', '+9779860479432, +9779846288255', 'lwc2074@gmail.com', '1234567890', 'REG-qpf43KWS', 'Thank you for choosing Lokanthali Wellness Clinic. We appreciate your trust in our services.', 'https://lwc.health.com', 'Our motto: healthy life. Lokanthali Wellness Clinic is dedicated to providing comprehensive and compassionate healthcare services.', 'LWC-', 'LWC-', 'Health Tax', 5, '2025-07-31 01:02:15', '2025-07-31 01:02:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `invoices`
--

CREATE TABLE `invoices` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `invoice_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Cash',
  `comment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_amount` double NOT NULL,
  `sub_total` double NOT NULL,
  `tax_amount` double NOT NULL,
  `discount` double DEFAULT NULL,
  `cash` double DEFAULT NULL,
  `patient_id` int UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `invoices`
--

INSERT INTO `invoices` (`id`, `user_id`, `invoice_no`, `payment_type`, `comment`, `total_amount`, `sub_total`, `tax_amount`, `discount`, `cash`, `patient_id`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 8, '1', 'Cash', NULL, 222833.1, 222222, 10611.1, 10000, 300000, 1, 1, '2025-07-31 01:02:49', '2025-07-31 01:02:49', NULL),
(2, 8, '2', 'Cash', 'test', 232283.1, 222222, 11061.1, 1000, 1000000, 1, 1, '2025-07-31 01:58:14', '2025-07-31 01:58:14', NULL),
(3, 8, '3', 'cash', NULL, 128333.1, 122222, 6111.1, 0, 1000000, 1, 1, '2025-07-31 08:26:12', '2025-07-31 08:26:12', NULL),
(4, 8, '4', 'cash', NULL, 128333.1, 122222, 6111.1, 0, 1000000, 1, 1, '2025-07-31 08:26:51', '2025-07-31 08:26:51', NULL),
(5, 8, '5', 'cash', NULL, 128333.1, 122222, 6111.1, 0, 2222222222, 1, 1, '2025-07-31 08:31:16', '2025-07-31 08:31:16', NULL),
(6, 8, '6', 'cash', NULL, 128333.1, 122222, 6111.1, 0, 555555555, 1, 1, '2025-07-31 08:32:03', '2025-07-31 08:32:03', NULL),
(7, 8, '7', 'cash', NULL, 12.6, 12, 0.6, 0, 33333333, 1, 1, '2025-07-31 08:33:21', '2025-07-31 08:33:21', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `invoice_returns`
--

CREATE TABLE `invoice_returns` (
  `id` int UNSIGNED NOT NULL,
  `invoice_id` int UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `return_amount` double NOT NULL,
  `return_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `menu_items`
--

CREATE TABLE `menu_items` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#',
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `menu_item_role`
--

CREATE TABLE `menu_item_role` (
  `menu_item_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_05_15_133418_create_hospitals_table', 1),
(5, '2025_05_15_133453_create_departments_table', 1),
(6, '2025_05_15_133515_create_employees_table', 1),
(7, '2025_05_15_133535_create_doctors_table', 1),
(8, '2025_05_15_133601_create_services_table', 1),
(9, '2025_05_15_133623_create_patients_table', 1),
(10, '2025_05_15_133652_create_appointments_table', 1),
(11, '2025_05_15_133710_create_invoices_table', 1),
(12, '2025_05_15_133739_create_service_sales_table', 1),
(13, '2025_05_15_133801_create_temps_table', 1),
(14, '2025_05_15_133815_create_tests_table', 1),
(15, '2025_05_15_133837_create_test_references_table', 1),
(16, '2025_05_15_133911_create_test_test_reference_table', 1),
(17, '2025_05_15_133939_create_reports_table', 1),
(18, '2025_05_15_133959_create_opd_sales_table', 1),
(19, '2025_05_15_134019_create_test_reports_table', 1),
(20, '2025_05_15_134042_create_test_results_table', 1),
(21, '2025_05_15_134125_create_invoice_returns_table', 1),
(22, '2025_05_15_134151_create_doctor_referreds_table', 1),
(23, '2025_05_15_134210_create_packages_table', 1),
(24, '2025_05_15_134228_create_package_tests_table', 1),
(25, '2025_05_15_134248_create_package_sales_table', 1),
(26, '2025_05_15_134335_create_reference_results_table', 1),
(27, '2025_05_15_134351_create_test_examinations_table', 1),
(28, '2025_05_15_134411_create_examination_results_table', 1),
(29, '2025_05_15_134430_create_test_antibiotics_table', 1),
(30, '2025_05_15_134447_create_test_test_antibiotic_table', 1),
(31, '2025_05_15_134504_create_test_stains_table', 1),
(32, '2025_05_15_134521_create_test_reference_results_table', 1),
(33, '2025_05_15_134539_create_test_antibiotic_results_table', 1),
(34, '2025_05_22_205131_create_personal_access_tokens_table', 1),
(35, '2025_07_29_082953_create_permission_tables', 1),
(36, '2025_07_29_192242_create_menu_items_table', 1),
(37, '2025_07_29_192248_create_menu_item_role_table', 1),
(38, '2025_07_29_215101_create_menu_items_table', 2),
(40, '2025_07_29_215114_create_menu_item_role_table', 3),
(41, '2025_07_31_085121_add_patient_id_to_opd_sales_table', 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(7, 'App\\Models\\User', 5),
(8, 'App\\Models\\User', 7),
(7, 'App\\Models\\User', 8);

-- --------------------------------------------------------

--
-- Struktur dari tabel `opd_sales`
--

CREATE TABLE `opd_sales` (
  `id` int UNSIGNED NOT NULL,
  `opd_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `doctor_id` int UNSIGNED NOT NULL,
  `patient_id` int UNSIGNED DEFAULT NULL,
  `invoice_id` int UNSIGNED NOT NULL,
  `doctor_fee` float NOT NULL,
  `opd_charge` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `opd_sales`
--

INSERT INTO `opd_sales` (`id`, `opd_name`, `doctor_id`, `patient_id`, `invoice_id`, `doctor_fee`, `opd_charge`, `status`, `created_at`, `updated_at`) VALUES
(1, 'OPD Charge(DR.Ahmad Rijal)', 3, 1, 1, 111111, 222222, 0, '2025-07-31 01:02:49', '2025-07-31 01:02:49'),
(2, 'OPD Charge(DR.Ahmad Rijal)', 3, 1, 2, 111111, 222222, 0, '2025-07-31 01:58:14', '2025-07-31 01:58:14');

-- --------------------------------------------------------

--
-- Struktur dari tabel `packages`
--

CREATE TABLE `packages` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` float NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `package_sales`
--

CREATE TABLE `package_sales` (
  `id` int UNSIGNED NOT NULL,
  `package_id` int UNSIGNED NOT NULL,
  `invoice_id` int UNSIGNED NOT NULL,
  `patient_id` int UNSIGNED NOT NULL,
  `package_price` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `package_tests`
--

CREATE TABLE `package_tests` (
  `id` int UNSIGNED NOT NULL,
  `package_id` int UNSIGNED NOT NULL,
  `test_id` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `patients`
--

CREATE TABLE `patients` (
  `id` int UNSIGNED NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `age` int NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('Male','Female','Other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `birth_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Nepal',
  `state` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Bagmati',
  `district` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Kathmandu',
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occupation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `relative_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `relative_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marital_status` enum('single','married','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `blood_group` enum('A+','A-','B+','AB+','AB-','B-','O+','O-') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `patients`
--

INSERT INTO `patients` (`id`, `first_name`, `middle_name`, `last_name`, `email`, `age`, `phone`, `gender`, `birth_date`, `country`, `state`, `district`, `location`, `occupation`, `description`, `relative_name`, `relative_phone`, `marital_status`, `blood_group`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Ahmad', 'Akmal', 'Rijal', 'ahmadakmalrijala@gmail.com', 22, '-', 'Male', '2004-02-22', '-', '-', '-', '-', '-', '-', '-', '-', 'single', 'A-', '2025-07-31 00:19:44', '2025-07-31 00:19:44', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'post-list', 'web', '2025-07-29 13:46:59', '2025-07-29 13:46:59'),
(2, 'post-create', 'web', '2025-07-29 13:46:59', '2025-07-29 13:46:59'),
(3, 'post-edit', 'web', '2025-07-29 13:46:59', '2025-07-29 13:46:59'),
(4, 'post-delete', 'web', '2025-07-29 13:46:59', '2025-07-29 13:46:59'),
(5, 'user-list', 'web', '2025-07-29 13:46:59', '2025-07-29 13:46:59'),
(6, 'user-create', 'web', '2025-07-29 13:46:59', '2025-07-29 13:46:59'),
(7, 'user-edit', 'web', '2025-07-29 13:46:59', '2025-07-29 13:46:59'),
(8, 'user-delete', 'web', '2025-07-29 13:46:59', '2025-07-29 13:46:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
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
-- Struktur dari tabel `reference_results`
--

CREATE TABLE `reference_results` (
  `id` int UNSIGNED NOT NULL,
  `test_report_id` int UNSIGNED NOT NULL,
  `test_reference_id` int UNSIGNED NOT NULL,
  `result` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `flag` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `reports`
--

CREATE TABLE `reports` (
  `id` int UNSIGNED NOT NULL,
  `patient_id` int UNSIGNED NOT NULL,
  `doctor_id` int DEFAULT NULL,
  `report` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `result` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(5, 'user', 'web', '2025-07-29 13:46:59', '2025-07-29 13:46:59'),
(7, 'admin', 'web', '2025-07-29 13:46:59', '2025-07-29 13:46:59'),
(8, 'Perawat', 'web', '2025-07-29 14:38:18', '2025-07-29 14:38:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 5),
(1, 7),
(2, 7),
(3, 7),
(4, 7),
(5, 7),
(6, 7),
(7, 7),
(8, 7);

-- --------------------------------------------------------

--
-- Struktur dari tabel `services`
--

CREATE TABLE `services` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` double NOT NULL,
  `department_id` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `services`
--

INSERT INTO `services` (`id`, `name`, `amount`, `department_id`, `created_at`, `updated_at`) VALUES
(1, 'testing_12', 122222, 1, '2025-07-31 00:18:05', '2025-07-31 00:18:05'),
(2, 'Service_bill', 12, 2, '2025-07-31 08:33:03', '2025-07-31 08:33:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `service_sales`
--

CREATE TABLE `service_sales` (
  `id` int UNSIGNED NOT NULL,
  `service_id` int NOT NULL,
  `service_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` double NOT NULL,
  `invoice_id` int UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `service_sales`
--

INSERT INTO `service_sales` (`id`, `service_id`, `service_name`, `amount`, `invoice_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'testing_12', 122222, 3, 1, '2025-07-31 08:26:12', '2025-07-31 08:26:12'),
(2, 1, 'testing_12', 122222, 4, 1, '2025-07-31 08:26:51', '2025-07-31 08:26:51'),
(3, 1, 'testing_12', 122222, 5, 1, '2025-07-31 08:31:16', '2025-07-31 08:31:16'),
(4, 1, 'testing_12', 122222, 6, 1, '2025-07-31 08:32:03', '2025-07-31 08:32:03'),
(5, 2, 'Service_bill', 12, 7, 1, '2025-07-31 08:33:21', '2025-07-31 08:33:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0Zye1PlWXKTkYH1w7Nb4bUQMeRLegEXxAUv3ynqE', 8, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiUU1ZUGgyaVdzdFc3bERNVHFiSkY1RHVHbm1TUGJ0QTk0Y2JUQW95dCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2Rhc2hib2FyZCI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwOi8vbG9jYWxob3N0OjgwMDAvZGFzaGJvYXJkL2xhYi10ZXN0L21pY3JvYmlvbG9neS10ZXN0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6ODt9', 1753978894),
('1JtSstAxtDyCc5NtxhhvSHQhMZ8HjhYYJhukPRaY', 8, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiU2QyaUUwSXU0aHZHUEtTaVl5QVFyZ25hVHRwY3liUWlvblBKdkhpdSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1NzoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2Rhc2hib2FyZC9sYWItdGVzdC9oYWVtYXRvbG9neS10ZXN0Ijt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9kYXNoYm9hcmQvbGFiLXRlc3QvaGFlbWF0b2xvZ3ktdGVzdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjg7fQ==', 1753976970),
('1lsqX3IqmEo1z6n7uPllnO8ISorpKEkiMNXeV7Dn', NULL, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaWF2UUFPMmZNaDFmZVI1RlFXODBpTnBadm5BR0R0amxtWERpOW96MSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo2MzoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2Rhc2hib2FyZC9pbnZvaWNlLWJpbGwvYWxsLWludm9pY2UtcmVwb3J0Ijt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1753974524),
('1mtCuBdZwyEI4IX2EvGvc111GkPliETcp8WgBQeh', 8, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoibWdZUnVid1dHM2FSMXBSeTlCVm4zT3RKWGJoQ2t2M2pjRHRja0tERSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1MzoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2Rhc2hib2FyZC9sYWItdGVzdC9leGFtaW5hdGlvbnMiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czo1ODoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2Rhc2hib2FyZC9sYWItdGVzdC9taWNyb2Jpb2xvZ3ktdGVzdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjg7fQ==', 1753979743),
('30Y9RLA77b4pe30rFzqohW8E3W6xV8pnbJK7C2WB', 8, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoic016SWMzeW1HZVNuemx3QVlnQlV2RTlaRjk4YlRFczRtRmNXRTA0NiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1MjoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2Rhc2hib2FyZC9sYWItdGVzdC9zdGFpbi10ZXN0cyI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjUyOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvZGFzaGJvYXJkL2xhYi10ZXN0L3N0YWluLXRlc3RzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6ODt9', 1753978322),
('3Kr0nbyHtAyvVvFkHKitWeZFgAEIYLtu2EnYnCqD', NULL, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiekRiMHRGdUNrRHhmamEySGc2ajk0a2pQQ2g0WGlENld3TUV5aDhETiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1753978417),
('9ulvanp6F3A0m8gIBY1C6Dw0C3vvuXWxPwwtiLy3', 8, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoienVaaXFHbkpPdjJEbDdEN2s3WFZ3NHZtNlJocUJIOGM0bmkyWmhycyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2Rhc2hib2FyZCI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjQ2OiJodHRwOi8vbG9jYWxob3N0OjgwMDAvZGFzaGJvYXJkL2FkbWluL3BhY2thZ2VzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6ODt9', 1753974673),
('cbbLSlQWrvDWt0H3GoyKkuSAqrz32ZYfgcFski9I', NULL, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMzVWMGoyb0pDUXNaUkpWZXRWY3BtVXY0NmxJWVhuaDBVU2JROG5LTyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1MjoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2Rhc2hib2FyZC9sYWItdGVzdC9zdGFpbi10ZXN0cyI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjI3OiJodHRwOi8vbG9jYWxob3N0OjgwMDAvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1753978418),
('cJnfHfnraAFasZPfJpnW4qnvE15Uu7MflMls0ODk', 8, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiWmtpUDZFanMwUU1UT1FJNXNNZEx3c2h1Y2VNNG9QMEFvQjhhQ0lIbCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo2MzoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2Rhc2hib2FyZC9pbnZvaWNlLWJpbGwvYWxsLWludm9pY2UtcmVwb3J0Ijt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo4O30=', 1753974821),
('CLQ9laSygoLEozB5jMn7BoXEhfnvYcQmZt8kBabr', 8, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNklQZ2E1VHhPTkI4Vk5raERZWWV1aml5U0FpMndMVnIxeW42cG4yQiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9kYXNoYm9hcmQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo4O30=', 1753978437),
('D00HTe89d5AYDbFwdceXuQZq0yrX25tYPzR7ZSSm', NULL, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQzlpZGVQTjcxYzdoaTc4d1MyY1EyZ1pqREhmSmE1c1FNcTFFVFcxYyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2Rhc2hib2FyZCI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjI3OiJodHRwOi8vbG9jYWxob3N0OjgwMDAvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1753976809),
('G0uYGeUiuWOefjaAuQEQvQsNlpe1txqkeT7AXgAA', 8, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiMzgzdGY1dURWQkY0enNsSGNWVmRjZ2dEQ1FDcWJUMnlqZ1VMUUx4ZiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1NzoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2Rhc2hib2FyZC9sYWItdGVzdC9oYWVtYXRvbG9neS10ZXN0Ijt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTY6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9kYXNoYm9hcmQvbGFiLXRlc3QvdGVzdC1yZWZlcmVuY2VzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6ODt9', 1753977595),
('hBeuOhycsMOLgrjoHw75igY5Qq79iDcbkdW3WDul', 8, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiQmtremtlY0RoUGJsR1hacDhBN1Zwa0lySG1vZEVRVnNHNTJvT1FxUiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2Rhc2hib2FyZCI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6ODt9', 1753976021),
('jijUgfyVYBS7tkIqHxoRwOBgrP20aAZyu4qLxTbs', NULL, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibWJuRW9qZVZBWHZ0OGNhQVNtcW84VFVHQ3hJQkQ2MlJvaDZWWmJIdCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1753978596),
('jM1J8lSKGksc2dgMIzYCS2oTkTiY0LcXD485MkPz', 8, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiNzFXelRqcG13b3VUVG9wRTlNdWNlWWRrU1ZPQkpBTnNxYTRxaU81ZCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo2MzoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2Rhc2hib2FyZC9pbnZvaWNlLWJpbGwvb3BkLWludm9pY2UvY3JlYXRlIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hcGkvb3BkLXNhbGVzLzMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo4O30=', 1753974723),
('kM0DExnhFuEANhWB5dxO7xNNk1mB9c3OprBrqcYl', NULL, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMFNpVDdNelVLOHRjbUdRbjZPZUo2SG9UOU1Sb1N6Um16Unp6QVpXcSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1753978987),
('Ky2AVJE05AEZliHjeu9jJ1SNa0V9wW23WdjkAQ6u', 8, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoieTYyRGFxeWpESG85d25Td3JtZXRrTVlGYUY3dWtvaTVudnpOcFFOQyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDg6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9kYXNoYm9hcmQvbGFiLXRlc3QvcmVwb3J0cyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6NDg6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9kYXNoYm9hcmQvbGFiLXRlc3QvcmVwb3J0cyI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjg7fQ==', 1753979629),
('ly51k4HgyxneYi8taMk9DwJSxAHXvGyb44hGe0mP', 8, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiUWRQcTdiY1BRamlaT09nRE1jNENZTFAxS0tZc0ZlbGJFTFJaaFY0ZiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2Rhc2hib2FyZCI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU3OiJodHRwOi8vbG9jYWxob3N0OjgwMDAvZGFzaGJvYXJkL2xhYi10ZXN0L2hhZW1hdG9sb2d5LXRlc3QiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo4O30=', 1753976625),
('NkH1ZsvW19ME5EnVC1sfX7W03byB8EI9acJDnO9s', NULL, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicGwya3B5NWtVc2hlVk9iaHdLRE9JSTVXckprZkhtR0FLVXZYS3pYaSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1NjoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2Rhc2hib2FyZC9sYWItdGVzdC90ZXN0LXJlZmVyZW5jZXMiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czo1NjoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2Rhc2hib2FyZC9sYWItdGVzdC90ZXN0LXJlZmVyZW5jZXMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1753977726),
('OSUHaPgUBGJqf0qykaWBqPaKUBs8Mm5Uka0OQrHr', 8, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiUHZ3ZWFOYUh2VkhVZGhVR2VJNjN2MzlVZUg0eEpoVGRVUHJVVFpVVyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NjoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2Rhc2hib2FyZC9sYWItdGVzdC90ZXN0cyI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjY3OiJodHRwOi8vbG9jYWxob3N0OjgwMDAvZGFzaGJvYXJkL2ludm9pY2UtYmlsbC9wYWNrYWdlLWludm9pY2UvY3JlYXRlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6ODt9', 1753981755),
('r3mLxzGnyty64ubhhW5rWvAvyjwoLfJfGzk9kILg', NULL, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoia3pKMjRXODB5eUtlblFZMmNiRk5IeU11djZ0M1hrUWFTd3VKd0U4ZSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2Rhc2hib2FyZCI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1753977120),
('TKRCW6zT6W5GnqehV5Rh3PXFyuUoQBA8z9pnlg4E', 8, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiOTc5dkZyRkZYMGlYWHZIQXJyREtwRUViaVZnUFp3ekxmWlZrbkREMiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2Rhc2hib2FyZCI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU2OiJodHRwOi8vbG9jYWxob3N0OjgwMDAvZGFzaGJvYXJkL2xhYi10ZXN0L3Rlc3QtcmVmZXJlbmNlcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjg7fQ==', 1753977777),
('vaPfmJRTm5qGtHgg6Xv07OB2VcB36LkRiMZkGP5y', 8, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiT1VuYjlKVlJCOTQ2d2wyRHpjWjZUUk1zRElCUmxXQVU1VkM2em9NTyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1NjoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2Rhc2hib2FyZC9sYWItdGVzdC90ZXN0LXJlZmVyZW5jZXMiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czo1NzoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2Rhc2hib2FyZC9sYWItdGVzdC9oYWVtYXRvbG9neS10ZXN0Ijt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6ODt9', 1753977994),
('vOaION2hDP4WGFVjgTmekkkYJqJT5BiCJApFgXGS', 8, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiWE44a1UzWDR0cUJ2cDR6UldGZHhkSE9OQmRpTDNhSmVBWmRQRUJJZyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NjoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2Rhc2hib2FyZC9sYWItdGVzdC90ZXN0cyI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6ODt9', 1753978030),
('VQFwu9azWBMq60zWiW0OXtU8fbOnKySixcN4Sx9z', 8, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoicmZaNElVNkswUmlzdzVOYzk1ZmU0NHBVSDFiY2hQaFN4UEJ0eUw4SSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1ODoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2Rhc2hib2FyZC9sYWItdGVzdC9taWNyb2Jpb2xvZ3ktdGVzdCI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjU4OiJodHRwOi8vbG9jYWxob3N0OjgwMDAvZGFzaGJvYXJkL2xhYi10ZXN0L21pY3JvYmlvbG9neS10ZXN0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6ODt9', 1753979030),
('xKnGGp2lHHbn8kxq1ltfkmxkNDhMnwIbsykOYuby', NULL, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMVNlQmVXUklCUW5jc3FRa0NPZEthOUR1dnZXTzdieFBDWE8zVzA0dCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2Rhc2hib2FyZCI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1753976411),
('xvHHobxP0nraSt4xXhNah3CfuolkmnjtlmFEK9S7', 8, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiM3VqZjlxU2tNeEFtQXMyTkZJeUo2d1pNam1Kb0ROaHY3Tk12VTlVNSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1MjoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2Rhc2hib2FyZC9sYWItdGVzdC9zdGFpbi10ZXN0cyI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjUyOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvZGFzaGJvYXJkL2xhYi10ZXN0L3N0YWluLXRlc3RzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6ODt9', 1753979667);

-- --------------------------------------------------------

--
-- Struktur dari tabel `temps`
--

CREATE TABLE `temps` (
  `id` int UNSIGNED NOT NULL,
  `service_id` int NOT NULL,
  `service_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tests`
--

CREATE TABLE `tests` (
  `id` int UNSIGNED NOT NULL,
  `service_id` int UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `report_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tests`
--

INSERT INTO `tests` (`id`, `service_id`, `name`, `report_type`, `description`, `created_at`, `updated_at`) VALUES
(1, 2, 'test12', 'haematology', NULL, '2025-07-31 08:48:41', '2025-07-31 08:48:41'),
(2, 2, 'test_stain', 'stain', NULL, '2025-07-31 09:36:35', '2025-07-31 09:36:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `test_antibiotics`
--

CREATE TABLE `test_antibiotics` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `test_antibiotics`
--

INSERT INTO `test_antibiotics` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'test12', '2025-07-31 09:23:50', '2025-07-31 09:23:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `test_antibiotic_results`
--

CREATE TABLE `test_antibiotic_results` (
  `id` int UNSIGNED NOT NULL,
  `test_report_id` int NOT NULL,
  `test_antibiotic_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `result` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `test_examinations`
--

CREATE TABLE `test_examinations` (
  `id` int UNSIGNED NOT NULL,
  `test_id` int UNSIGNED NOT NULL,
  `macroscopics` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `microscopics` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `test_references`
--

CREATE TABLE `test_references` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `range` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `test_references`
--

INSERT INTO `test_references` (`id`, `name`, `unit`, `range`, `parent_id`, `created_at`, `updated_at`) VALUES
(1, 'test12', 'ml', NULL, NULL, '2025-07-31 09:05:15', '2025-07-31 09:05:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `test_reference_results`
--

CREATE TABLE `test_reference_results` (
  `id` int UNSIGNED NOT NULL,
  `test_report_id` int NOT NULL,
  `test_reference_id` int NOT NULL,
  `result` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `flag` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `test_reports`
--

CREATE TABLE `test_reports` (
  `id` int UNSIGNED NOT NULL,
  `report_id` int UNSIGNED NOT NULL,
  `test_id` int UNSIGNED NOT NULL,
  `report_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sample` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `test_results`
--

CREATE TABLE `test_results` (
  `id` int UNSIGNED NOT NULL,
  `test_report_id` int UNSIGNED NOT NULL,
  `result` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `test_stains`
--

CREATE TABLE `test_stains` (
  `id` int UNSIGNED NOT NULL,
  `test_id` int UNSIGNED NOT NULL,
  `test_names` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `test_stains`
--

INSERT INTO `test_stains` (`id`, `test_id`, `test_names`, `created_at`, `updated_at`) VALUES
(2, 2, '[\"test1\",\"test2\"]', '2025-07-31 09:42:42', '2025-07-31 09:42:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `test_test_antibiotic`
--

CREATE TABLE `test_test_antibiotic` (
  `id` int UNSIGNED NOT NULL,
  `test_id` int NOT NULL,
  `test_antibiotic_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `test_test_reference`
--

CREATE TABLE `test_test_reference` (
  `id` int UNSIGNED NOT NULL,
  `test_id` int NOT NULL,
  `test_reference_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `test_test_reference`
--

INSERT INTO `test_test_reference` (`id`, `test_id`, `test_reference_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(5, 'Administrator', 'ahmad@gmail.com', NULL, '$2y$12$8m2MoeoYW0MdS1aJ30VEyemkfMr6KlsJ1JGjsfvjwpOAGoYQzNMrG', NULL, '2025-07-29 14:15:40', '2025-07-29 14:15:40'),
(7, 'ahmad', 'perawat@gmail.com', NULL, '$2y$12$8zgZOfWjtk7GAjCLgGayjudWNUSJS4MX4RKN2SS/.OkZSg5Imhk86', NULL, '2025-07-29 14:45:24', '2025-07-29 15:05:35'),
(8, 'admin@gmail.com', 'admin@gmail.com', NULL, '$2y$12$a0/eMwRSuWqCUoQUw98Jv.jBKVYnbxQV4MoPo4SEJHxnNLHI8D3fm', NULL, '2025-07-29 15:02:18', '2025-07-29 15:02:18');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `doctor_referreds`
--
ALTER TABLE `doctor_referreds`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `examination_results`
--
ALTER TABLE `examination_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `examination_results_test_report_id_foreign` (`test_report_id`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `hospitals`
--
ALTER TABLE `hospitals`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `invoice_returns`
--
ALTER TABLE `invoice_returns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_returns_invoice_id_foreign` (`invoice_id`),
  ADD KEY `invoice_returns_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_items_parent_id_foreign` (`parent_id`);

--
-- Indeks untuk tabel `menu_item_role`
--
ALTER TABLE `menu_item_role`
  ADD PRIMARY KEY (`menu_item_id`,`role_id`),
  ADD KEY `menu_item_role_role_id_foreign` (`role_id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indeks untuk tabel `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indeks untuk tabel `opd_sales`
--
ALTER TABLE `opd_sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `opd_sales_doctor_id_foreign` (`doctor_id`),
  ADD KEY `opd_sales_invoice_id_foreign` (`invoice_id`),
  ADD KEY `opd_sales_patient_id_foreign` (`patient_id`);

--
-- Indeks untuk tabel `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `package_sales`
--
ALTER TABLE `package_sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `package_sales_package_id_foreign` (`package_id`),
  ADD KEY `package_sales_invoice_id_foreign` (`invoice_id`),
  ADD KEY `package_sales_patient_id_foreign` (`patient_id`);

--
-- Indeks untuk tabel `package_tests`
--
ALTER TABLE `package_tests`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indeks untuk tabel `reference_results`
--
ALTER TABLE `reference_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reference_results_test_report_id_foreign` (`test_report_id`),
  ADD KEY `reference_results_test_reference_id_foreign` (`test_reference_id`);

--
-- Indeks untuk tabel `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reports_patient_id_foreign` (`patient_id`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indeks untuk tabel `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indeks untuk tabel `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `service_sales`
--
ALTER TABLE `service_sales`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `temps`
--
ALTER TABLE `temps`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `test_antibiotics`
--
ALTER TABLE `test_antibiotics`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `test_antibiotic_results`
--
ALTER TABLE `test_antibiotic_results`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `test_examinations`
--
ALTER TABLE `test_examinations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `test_examinations_test_id_foreign` (`test_id`);

--
-- Indeks untuk tabel `test_references`
--
ALTER TABLE `test_references`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `test_reference_results`
--
ALTER TABLE `test_reference_results`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `test_reports`
--
ALTER TABLE `test_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `test_reports_test_id_foreign` (`test_id`),
  ADD KEY `test_reports_report_id_foreign` (`report_id`);

--
-- Indeks untuk tabel `test_results`
--
ALTER TABLE `test_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `test_results_test_report_id_foreign` (`test_report_id`);

--
-- Indeks untuk tabel `test_stains`
--
ALTER TABLE `test_stains`
  ADD PRIMARY KEY (`id`),
  ADD KEY `test_stains_test_id_foreign` (`test_id`);

--
-- Indeks untuk tabel `test_test_antibiotic`
--
ALTER TABLE `test_test_antibiotic`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `test_test_reference`
--
ALTER TABLE `test_test_reference`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `doctor_referreds`
--
ALTER TABLE `doctor_referreds`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `examination_results`
--
ALTER TABLE `examination_results`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `hospitals`
--
ALTER TABLE `hospitals`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `invoice_returns`
--
ALTER TABLE `invoice_returns`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT untuk tabel `opd_sales`
--
ALTER TABLE `opd_sales`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `packages`
--
ALTER TABLE `packages`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `package_sales`
--
ALTER TABLE `package_sales`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `package_tests`
--
ALTER TABLE `package_tests`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `reference_results`
--
ALTER TABLE `reference_results`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `services`
--
ALTER TABLE `services`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `service_sales`
--
ALTER TABLE `service_sales`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `temps`
--
ALTER TABLE `temps`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tests`
--
ALTER TABLE `tests`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `test_antibiotics`
--
ALTER TABLE `test_antibiotics`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `test_antibiotic_results`
--
ALTER TABLE `test_antibiotic_results`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `test_examinations`
--
ALTER TABLE `test_examinations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `test_references`
--
ALTER TABLE `test_references`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `test_reference_results`
--
ALTER TABLE `test_reference_results`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `test_reports`
--
ALTER TABLE `test_reports`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `test_results`
--
ALTER TABLE `test_results`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `test_stains`
--
ALTER TABLE `test_stains`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `test_test_antibiotic`
--
ALTER TABLE `test_test_antibiotic`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `test_test_reference`
--
ALTER TABLE `test_test_reference`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `examination_results`
--
ALTER TABLE `examination_results`
  ADD CONSTRAINT `examination_results_test_report_id_foreign` FOREIGN KEY (`test_report_id`) REFERENCES `test_reports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `invoice_returns`
--
ALTER TABLE `invoice_returns`
  ADD CONSTRAINT `invoice_returns_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_returns_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `menu_item_role`
--
ALTER TABLE `menu_item_role`
  ADD CONSTRAINT `menu_item_role_menu_item_id_foreign` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `menu_item_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `opd_sales`
--
ALTER TABLE `opd_sales`
  ADD CONSTRAINT `opd_sales_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `opd_sales_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `opd_sales_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `package_sales`
--
ALTER TABLE `package_sales`
  ADD CONSTRAINT `package_sales_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `package_sales_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `package_sales_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `reference_results`
--
ALTER TABLE `reference_results`
  ADD CONSTRAINT `reference_results_test_reference_id_foreign` FOREIGN KEY (`test_reference_id`) REFERENCES `test_references` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reference_results_test_report_id_foreign` FOREIGN KEY (`test_report_id`) REFERENCES `test_reports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `test_examinations`
--
ALTER TABLE `test_examinations`
  ADD CONSTRAINT `test_examinations_test_id_foreign` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `test_reports`
--
ALTER TABLE `test_reports`
  ADD CONSTRAINT `test_reports_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `test_reports_test_id_foreign` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `test_results`
--
ALTER TABLE `test_results`
  ADD CONSTRAINT `test_results_test_report_id_foreign` FOREIGN KEY (`test_report_id`) REFERENCES `test_reports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `test_stains`
--
ALTER TABLE `test_stains`
  ADD CONSTRAINT `test_stains_test_id_foreign` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
