-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 14, 2025 at 04:52 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventory_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `page` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `description`, `page`, `created_at`) VALUES
(1, 1, 'Delete', 'Deleted category: ty', 'categorie.php', '2025-10-09 07:34:18'),
(2, 1, 'Add', 'Added purchase: Product Box Varieties (Qty: 35) from Supplier catenzaangelmae', 'add_purchase.php', '2025-10-09 07:37:36'),
(3, 1, 'Email Sent', 'Purchase order email sent to supplier catenzaangelmae (catenzaangelmae@gmail.com) for product Box Varieties.', 'add_purchase.php', '2025-10-09 07:37:45'),
(4, 1, 'Sale Added', '1 product(s) sold. Transaction #67 | Total ₱10.00', 'reciept.php?id=67', '2025-10-09 07:40:28'),
(5, 6, 'Sale Added', '1 product(s) sold. Transaction #68 | Total ₱5.00', 'recieptV2.php?id=68', '2025-10-09 07:43:15'),
(6, 1, 'Request Approved', 'Stock request #1 for product ID 14 was approved', 'manage_requests.php', '2025-10-09 07:48:55'),
(7, 1, 'Delete Media', 'Deleted media file: Classic Desktop Tape Dispenser 38.jpg (ID: 11)', 'media.php', '2025-10-09 07:50:47'),
(8, 1, 'Delete Product', 'Deleted product: pa (ID: 22)', 'product.php', '2025-10-09 07:53:07'),
(9, 1, 'Add Product', 'Added new product: pa | Qty: 4 packs | Buy: ₱6 | Sell: ₱6', 'product.php', '2025-10-09 07:55:48'),
(10, 1, 'Update Request Status', 'Request ID 10 marked as \'rejected\'.', 'manage_requests.php', '2025-10-09 07:58:25'),
(11, 1, 'Bryan Bernal', 'Edited product: pa', '', '2025-10-09 08:09:30'),
(12, 1, 'Updated Product: pa', '', '', '2025-10-09 08:18:57'),
(13, 1, 'Edit Product', 'Updated product \'pa\' (ID: 23) | Qty: 7 packs | Buy: ₱6.00 | Sell: ₱13.00', 'product.php', '2025-10-09 08:23:58'),
(14, 1, 'Update Account Info Failed', 'User \'Admin\' attempted to update account but no changes were made or query failed.', 'edit_account.php', '2025-10-09 09:18:07'),
(15, 1, 'Delete Product', 'Deleted product: pa (ID: 23)', 'product.php', '2025-10-09 09:50:28'),
(16, 1, 'Sale Added', '1 product(s) sold. Transaction #69 | Total ₱7.99', 'reciept.php?id=69', '2025-10-09 13:32:33'),
(17, 1, 'Upload Photo', 'Uploaded new photo: ', 'media.php', '2025-10-09 13:35:53'),
(18, 1, 'Upload Photo', 'Uploaded new photo: ', 'media.php', '2025-10-09 13:36:32'),
(19, 1, 'Edit Product', 'Updated product \'Youngs Town  Sardines W\' (ID: 8) | Qty: 99 cans | Buy: ₱13.00 | Sell: ₱20.00', 'product.php', '2025-10-09 13:37:00'),
(20, 1, 'Upload Photo', 'Uploaded new photo: ', 'media.php', '2025-10-10 06:08:27'),
(21, 1, 'Request Approved', 'Stock request #2 for product ID 14 was approved', 'manage_requests.php', '2025-10-10 06:12:09'),
(22, 1, 'Edit Transaction', 'Updated transaction (ID: 69). 1 item(s) modified.', 'edit_transaction.php', '2025-10-11 08:52:56'),
(23, 1, 'Restore Product Quantity', 'Restored 1 units to product \'Coke\' after sale deletion. New quantity: 12.', 'sales.php', '2025-10-11 09:22:26'),
(24, 1, 'Delete Sale', 'Deleted sale record (Sale ID: 130) for product \'Coke\' | Quantity: 1', 'sales.php', '2025-10-11 09:22:26'),
(25, 1, 'Edit Transaction', 'Updated transaction (ID: 62). 1 item(s) modified.', 'edit_transaction.php', '2025-10-11 09:24:47'),
(26, 1, 'Edit Transaction', 'Updated transaction (ID: 49). 3 item(s) modified.', 'edit_transaction.php', '2025-10-11 09:25:55'),
(27, 2, 'Login', 'User logged in', 'auth.php', '2025-10-12 02:34:01'),
(28, 2, 'Login', 'User logged in', 'auth.php', '2025-10-12 02:34:15'),
(29, 1, 'User Login', 'User Admin logged in.', 'auth.php', '2025-10-12 02:36:20'),
(30, 1, 'User Login', 'User Admin logged in.', 'auth.php', '2025-10-12 07:41:21'),
(31, 1, 'Login', 'User  (ID: 1) logged in.', 'auth.php', '2025-10-12 07:53:45'),
(32, 6, 'Login', 'User  (ID: 6) logged in.', 'auth.php', '2025-10-12 07:59:11'),
(33, 1, 'Login', 'User  (ID: 1) logged in.', 'auth.php', '2025-10-12 07:59:21'),
(34, 1, 'Login', 'Logged in as ', 'auth.php', '2025-10-12 08:01:11'),
(35, 1, 'Login', 'Logged in as ', 'auth.php', '2025-10-12 08:06:20'),
(36, 1, 'User Login', 'User Admin logged in.', 'auth.php', '2025-10-12 08:10:06'),
(37, 1, 'Edit Product', 'Updated product \'Hasbro Marvel Legends Series Toys\' (ID: 10) | Qty: 96 pcs | Buy: ₱219.00 | Sell: ₱322.00', 'product.php', '2025-10-12 08:11:18'),
(38, 1, 'User Login', 'User Admin logged in.', 'auth.php', '2025-10-12 08:25:05'),
(39, 1, 'User Login', 'User Admin logged in.', 'auth.php', '2025-10-13 12:19:06'),
(40, 1, 'User Login', 'User Admin logged in.', 'auth.php', '2025-10-13 12:54:27'),
(41, 2, 'User Login', 'User Manager logged in.', 'auth.php', '2025-10-13 13:11:55'),
(42, 2, 'User Login', 'User Manager logged in.', 'auth.php', '2025-10-13 13:12:56'),
(43, 2, 'User Login', 'User Manager logged in.', 'auth.php', '2025-10-13 13:14:58'),
(44, 2, 'User Login', 'User Manager logged in.', 'auth.php', '2025-10-13 13:17:45'),
(45, 1, 'User Login', 'User Admin logged in.', 'auth.php', '2025-10-13 13:18:10'),
(46, 6, 'User Login', 'User cashier logged in.', 'auth.php', '2025-10-13 13:20:58'),
(47, 1, 'User Login', 'User Admin logged in.', 'auth.php', '2025-10-13 13:26:52'),
(48, 1, 'User Login', 'User Admin logged in.', 'auth.php', '2025-10-14 01:00:04'),
(49, 1, 'User Login', 'User Admin logged in.', 'auth.php', '2025-10-14 01:07:02'),
(50, 1, 'User Login', 'User Admin logged in.', 'auth.php', '2025-10-14 01:29:57'),
(51, 1, 'Sale Added', '11 product(s) sold. Transaction #70 | Total ₱2,282.99', 'reciept.php?id=70', '2025-10-14 01:45:33');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Demo Category'),
(9, 'Direct Material '),
(3, 'Finished Goods'),
(5, 'Machinery'),
(4, 'Packing Materials'),
(2, 'Raw Materials'),
(8, 'Stationery Items'),
(6, 'Work in Progres');

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` int(11) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`id`, `file_name`, `file_type`) VALUES
(3, 'Hasbro Marvel Legends Series Toys.jpg', 'image/jpeg'),
(4, 'download.jfif', 'image/jfif'),
(5, 'coke.png', 'image/png'),
(8, 'Mommy oni wildcard.png', 'image/png'),
(10, 'vecteezy_steel-beams-isolated-on-transparent-background_49735629.png', 'image/png'),
(20, 'vecteezy_fish-on-transparent-background_48718627.png', 'image/png'),
(21, 'images.jfif', 'image/jpeg'),
(22, 'Screenshot 2025-06-29 184604.png', 'image/png');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `quantity` varchar(50) DEFAULT NULL,
  `unit` varchar(50) DEFAULT 'pcs',
  `buy_price` decimal(25,2) DEFAULT NULL,
  `sale_price` decimal(25,2) NOT NULL,
  `categorie_id` int(11) UNSIGNED NOT NULL,
  `media_id` int(11) DEFAULT 0,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `low_stock_threshold` int(11) DEFAULT 5,
  `unit_id` int(11) DEFAULT NULL,
  `quantity_in_base_unit` float DEFAULT 0,
  `supplier_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `quantity`, `unit`, `buy_price`, `sale_price`, `categorie_id`, `media_id`, `date`, `low_stock_threshold`, `unit_id`, `quantity_in_base_unit`, `supplier_id`) VALUES
(1, 'Demo Product', '98', 'box', 100.00, 500.00, 1, 0, '2025-10-14 01:45:33', 5, NULL, 0, NULL),
(2, 'Box Varieties', '11954', 'box', 55.00, 130.00, 4, 0, '2025-10-14 01:45:33', 5, NULL, 0, NULL),
(3, 'Wheat', '106', 'kg', 2.00, 5.00, 2, 9, '2025-10-09 07:43:15', 5, NULL, 0, NULL),
(4, 'Timber', '1194', NULL, 780.00, 1069.00, 2, 0, '2025-09-24 00:58:15', 5, NULL, 0, NULL),
(5, 'W1848 Oscillating Floor Drill Press', '22', NULL, 299.00, 494.00, 5, 0, '2025-10-07 03:12:42', 5, NULL, 0, NULL),
(6, 'Portable Band Saw XBP02Z', '37', 'pcs', 280.00, 500.00, 5, 4, '2025-10-14 01:45:33', 5, NULL, 0, NULL),
(7, 'Life Breakfast Cereal-3 Pk', '76', 'box', 3.99, 7.99, 3, 0, '2025-10-14 01:45:33', 5, NULL, 0, NULL),
(8, 'Youngs Town  Sardines W', '96', 'cans', 13.00, 20.00, 1, 21, '2025-10-11 09:25:55', 5, NULL, 0, NULL),
(9, 'Disney Woody - Action Figure', '64', 'pcs', 29.00, 55.00, 3, 12, '2025-10-14 01:45:33', 5, NULL, 0, NULL),
(10, 'Hasbro Marvel Legends Series Toys', '95', 'pcs', 219.00, 322.00, 3, 0, '2025-10-14 01:45:33', 5, NULL, 0, NULL),
(11, 'Packing Chips', '0', 'packs', 21.00, 31.00, 4, 0, '2025-10-09 07:48:12', 5, NULL, 0, NULL),
(12, 'Classic Desktop Tape Dispenser 38', '149', 'roll', 5.00, 10.00, 8, 11, '2025-10-14 01:45:33', 5, NULL, 0, NULL),
(13, 'Small Bubble Cushioning Wrap', '180', NULL, 8.00, 19.00, 4, 0, '2025-10-14 01:45:33', 5, NULL, 0, NULL),
(14, 'Fish', '14', 'kg', 12.00, 20.00, 2, 13, '2025-10-14 01:45:33', 5, NULL, 0, NULL),
(16, 'Coke', '11', 'L', 15.00, 20.00, 9, 5, '2025-10-14 01:45:33', 5, NULL, 0, NULL),
(17, 'Steel', '26', 'pcs', 135.00, 200.00, 9, 10, '2025-10-04 07:51:17', 5, NULL, 0, NULL),
(19, 'Mommy Oni Wildcard', '578', 'box', 69.00, 699.00, 1, 8, '2025-10-14 01:45:33', 5, NULL, 0, NULL),
(20, 'Wire', '6821', 'roll', 100.00, 150.00, 1, 0, '2025-10-08 12:02:13', 5, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `purchase_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `description` text DEFAULT NULL,
  `status` enum('Pending','Received','Canceled') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `product_id`, `supplier_id`, `quantity`, `purchase_date`, `description`, `status`) VALUES
(40, 5, 2, 1, '2025-10-07 03:13:05', 'k7800', 'Canceled'),
(41, 2, 2, 1, '2025-10-02 16:00:00', 'N/A', 'Canceled'),
(42, 8, 2, 2, '2025-10-09 16:00:00', 'N/A', 'Canceled'),
(43, 9, 2, 3, '2025-10-07 03:12:50', 'N/A', 'Received'),
(44, 5, 2, 2, '2025-10-07 03:12:41', 'N/A', 'Received'),
(45, 3, 2, 7, '2025-10-05 12:58:16', 'N/A', 'Received'),
(46, 3, 2, 3, '2025-10-07 03:12:59', 'N/A', 'Canceled'),
(47, 11, 4, 23, '2025-10-05 12:57:45', 'tapos na kayo', 'Received'),
(48, 14, 2, 2, '2025-10-07 03:12:46', 'kdøs', 'Received'),
(49, 14, 4, 2, '2025-10-05 12:57:55', 'lj ', 'Received'),
(50, 14, 4, 23, '2025-10-06 16:00:00', 'patulong sa documentation', 'Pending'),
(51, 14, 4, 23, '2025-10-06 16:00:00', 'patulong sa documentation', 'Pending'),
(52, 14, 4, 23, '2025-10-06 16:00:00', 'patulong sa documentation', 'Pending'),
(53, 14, 4, 23, '2025-10-06 16:00:00', 'patulong sa documentation', 'Pending'),
(54, 14, 4, 23, '2025-10-06 16:00:00', 'patulong sa documentation', 'Pending'),
(55, 14, 4, 23, '2025-10-06 16:00:00', 'patulong sa documentation', 'Pending'),
(56, 12, 5, 23, '2025-10-07 16:00:00', 'we want to inform you that the item last time was damaged, we want to make sure that all of the delivered item is taken care of carefully', 'Pending'),
(57, 9, 5, 23, '2025-10-07 16:00:00', 'NKSOSPSAIANSCPACSPINDSWFXPIIP', 'Pending'),
(58, 8, 6, 23, '2025-10-07 16:00:00', 'edn.skalsæoc3ruæbwouc', 'Pending'),
(59, 5, 6, 23, '2025-10-08 16:00:00', 'l.nøipnøk', 'Pending'),
(60, 2, 6, 35, '2025-10-08 16:00:00', '7', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) UNSIGNED NOT NULL,
  `transaction_id` int(10) UNSIGNED DEFAULT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(25,2) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `transaction_id`, `product_id`, `qty`, `price`, `date`) VALUES
(68, 13, 10, 1, 322.00, '2025-08-21 02:01:13'),
(69, 13, 16, 1, 20.00, '2025-08-21 02:01:13'),
(70, 13, 17, 1, 200.00, '2025-08-21 02:01:13'),
(72, 14, 8, 1, 20.00, '2025-08-21 02:36:56'),
(73, 14, 9, 1, 55.00, '2025-08-21 02:36:56'),
(74, 15, 4, 1, 1069.00, '2025-08-21 06:06:22'),
(75, 16, 3, 1, 5.00, '2025-08-22 01:30:02'),
(76, 16, 4, 1, 1069.00, '2025-08-22 01:30:02'),
(77, 16, 5, 1, 494.00, '2025-08-22 01:30:02'),
(78, 17, 5, 1, 494.00, '2025-08-22 06:39:18'),
(79, 18, 7, 1, 7.99, '2025-08-22 06:43:56'),
(80, 19, 6, 1, 500.00, '2025-08-23 23:21:58'),
(81, 19, 17, 1, 200.00, '2025-08-23 23:21:58'),
(82, 19, 14, 1, 20.00, '2025-08-23 23:21:58'),
(83, 20, 5, 1, 494.00, '2025-08-24 06:20:16'),
(84, 20, 9, 1, 55.00, '2025-08-24 06:20:16'),
(86, 20, 8, 1, 20.00, '2025-08-24 06:20:16'),
(87, 21, 17, 1, 200.00, '2025-08-24 21:33:03'),
(88, 22, 1, 1, 500.00, '2025-08-24 21:49:47'),
(89, 23, 7, 9, 7.99, '2025-10-04 07:47:56'),
(90, 23, 8, 1, 20.00, '2025-08-26 00:01:09'),
(91, 23, 13, 1, 19.00, '2025-08-26 00:01:09'),
(92, 24, 11, 1, 31.00, '2025-08-27 05:21:08'),
(93, 24, 9, 1, 55.00, '2025-08-27 05:21:08'),
(94, 25, 2, 1, 130.00, '2025-08-28 07:28:49'),
(95, 25, 8, 1, 20.00, '2025-08-28 07:28:49'),
(96, 26, 2, 6, 130.00, '2025-09-16 03:39:50'),
(97, 27, 8, 1, 20.00, '2025-08-29 05:34:25'),
(98, 27, 12, 1, 10.00, '2025-08-29 05:34:25'),
(99, 28, 2, 1, 130.00, '2025-08-29 15:04:46'),
(100, 29, 12, 1, 10.00, '2025-08-31 03:57:58'),
(101, 30, 13, 1, 19.00, '2025-08-31 13:36:05'),
(102, 30, 6, 1, 500.00, '2025-08-31 13:36:05'),
(103, 31, 3, 4, 5.00, '2025-09-02 10:34:56'),
(104, 32, 12, 1, 10.00, '2025-08-31 16:34:45'),
(105, 33, 3, 1, 5.00, '2025-09-01 06:11:58'),
(106, 34, 8, 1, 20.00, '2025-09-02 10:35:35'),
(107, 34, 17, 1, 200.00, '2025-09-02 10:35:35'),
(108, 35, 8, 1, 20.00, '2025-09-04 02:32:53'),
(109, 36, 9, 1, 55.00, '2025-09-05 00:12:07'),
(110, 36, 11, 1, 31.00, '2025-09-05 00:12:07'),
(111, 37, 11, 1, 31.00, '2025-09-06 12:14:45'),
(120, 43, 3, 1, 5.00, '2025-09-08 14:14:53'),
(121, 44, 3, 1, 5.00, '2025-09-08 14:16:38'),
(122, 45, 19, 1, 699.00, '2025-09-09 13:26:40'),
(123, 46, 8, 1, 20.00, '2025-09-10 11:07:11'),
(124, 47, 3, 1, 5.00, '2025-09-11 06:55:46'),
(125, 47, 17, 1, 200.00, '2025-09-11 06:55:46'),
(126, 48, 9, 1, 55.00, '2025-10-04 14:01:31'),
(127, 49, 8, 4, 20.00, '2025-10-11 09:25:55'),
(128, 49, 2, 1, 130.00, '2025-09-16 00:32:44'),
(129, 49, 12, 1, 10.00, '2025-09-16 00:32:44'),
(174, 62, 14, 2, 20.00, '2025-10-11 09:24:47'),
(175, 63, 16, 3, 20.00, '2025-10-08 05:12:56'),
(176, 64, 2, 1, 130.00, '2025-10-09 01:20:39'),
(177, 65, 1, 1, 500.00, '2025-10-09 01:24:02'),
(178, 66, 16, 1, 20.00, '2025-10-09 01:27:21'),
(179, 67, 12, 1, 10.00, '2025-10-09 07:40:28'),
(180, 68, 3, 1, 5.00, '2025-10-09 07:43:15'),
(181, 69, 7, 4, 7.99, '2025-10-11 08:52:56'),
(182, 70, 2, 1, 130.00, '2025-10-14 01:45:33'),
(183, 70, 12, 1, 10.00, '2025-10-14 01:45:33'),
(184, 70, 16, 1, 20.00, '2025-10-14 01:45:33'),
(185, 70, 1, 1, 500.00, '2025-10-14 01:45:33'),
(186, 70, 9, 1, 55.00, '2025-10-14 01:45:33'),
(187, 70, 14, 1, 20.00, '2025-10-14 01:45:33'),
(188, 70, 10, 1, 322.00, '2025-10-14 01:45:33'),
(189, 70, 7, 1, 7.99, '2025-10-14 01:45:33'),
(190, 70, 19, 1, 699.00, '2025-10-14 01:45:33'),
(191, 70, 6, 1, 500.00, '2025-10-14 01:45:33'),
(192, 70, 13, 1, 19.00, '2025-10-14 01:45:33');

-- --------------------------------------------------------

--
-- Table structure for table `stock_requests`
--

CREATE TABLE `stock_requests` (
  `id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `requested_by` int(11) UNSIGNED NOT NULL,
  `request_date` datetime NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_requests`
--

INSERT INTO `stock_requests` (`id`, `product_id`, `requested_by`, `request_date`, `status`) VALUES
(1, 14, 6, '2025-09-19 17:05:02', 'pending'),
(2, 14, 6, '2025-09-19 17:20:15', 'approved'),
(3, 14, 6, '2025-09-19 20:46:26', 'approved'),
(4, 14, 6, '2025-09-19 21:01:04', 'approved'),
(5, 14, 6, '2025-09-19 21:06:33', 'approved'),
(6, 14, 6, '2025-09-20 09:14:20', 'approved'),
(7, 14, 6, '2025-09-25 21:57:47', 'approved'),
(8, 14, 6, '2025-10-04 19:03:13', 'approved'),
(9, 14, 6, '2025-10-04 19:04:20', 'approved'),
(10, 11, 6, '2025-10-09 09:37:01', 'rejected');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `contact_person`, `phone`, `email`, `address`, `created_at`) VALUES
(2, 'Banana Floor Wax', 'by the way I am not sure ', '1325252626678', 'bry587681@gmail.com', 'ug,l', '2025-09-20 08:56:09'),
(3, 'Inventory', 'Rommel ', '8320023', 'rommelbernal906@gmail.com', 'vbksdaouvouonolnkjcudjkxmewløkafkkmø', '2025-09-24 09:01:04'),
(4, 'Pos', 'guil', '1325252626678', 'guillianromaces9@gmail.com', 'djlckkaøaø', '2025-09-27 10:00:35'),
(5, 'avenido things', 'faisal', '09466263159', 'faisalmedal2222@gmail.com', '161kasunduan street', '2025-10-08 04:25:47'),
(6, 'catenzaangelmae', 'Angel mae', '097765545', 'catenzaangelmae@gmail.com', 'ijjnunu', '2025-10-08 05:11:16');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `txn_time` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `total` decimal(25,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `txn_time`, `user_id`, `total`) VALUES
(13, '2025-08-21 10:01:13', 1, 561.00),
(14, '2025-08-21 10:36:56', 1, 75.00),
(15, '2025-08-21 14:06:22', 1, 1069.00),
(16, '2025-08-22 09:30:02', 1, 1568.00),
(17, '2025-08-22 14:39:18', 5, 494.00),
(18, '2025-08-22 14:43:56', 1, 7.99),
(19, '2025-08-24 07:21:58', 1, 720.00),
(20, '2025-08-24 14:20:16', 5, 576.99),
(21, '2025-08-25 05:33:03', 5, 200.00),
(22, '2025-08-25 05:49:47', 5, 500.00),
(23, '2025-08-26 08:01:09', 1, 46.99),
(24, '2025-08-27 13:21:08', 1, 86.00),
(25, '2025-08-28 15:28:49', 1, 150.00),
(26, '2025-08-28 21:38:06', 1, 130.00),
(27, '2025-08-29 13:34:25', 2, 30.00),
(28, '2025-08-29 23:04:46', 2, 130.00),
(29, '2025-08-31 11:57:58', 2, 10.00),
(30, '2025-08-31 21:36:05', 1, 519.00),
(31, '2025-09-01 00:14:32', 1, 5.00),
(32, '2025-09-01 00:34:45', 1, 10.00),
(33, '2025-09-01 14:11:58', 5, 5.00),
(34, '2025-09-02 18:35:35', 1, 220.00),
(35, '2025-09-04 10:32:53', 1, 20.00),
(36, '2025-09-05 08:12:07', 2, 86.00),
(37, '2025-09-06 20:14:45', 1, 31.00),
(43, '2025-09-08 22:14:53', 1, 5.00),
(44, '2025-09-08 22:16:38', 1, 5.00),
(45, '2025-09-09 21:26:40', 1, 699.00),
(46, '2025-09-10 19:07:11', 1, 20.00),
(47, '2025-09-11 14:55:46', 1, 205.00),
(48, '2025-09-11 14:57:29', 1, 110.00),
(49, '2025-09-16 08:32:44', 1, 180.00),
(62, '2025-10-04 19:02:55', 6, 200.00),
(63, '2025-10-08 13:12:56', 1, 60.00),
(64, '2025-10-09 09:20:39', 1, 130.00),
(65, '2025-10-09 09:24:02', 6, 500.00),
(66, '2025-10-09 09:27:21', 6, 20.00),
(67, '2025-10-09 15:40:28', 1, 10.00),
(68, '2025-10-09 15:43:15', 6, 5.00),
(69, '2025-10-09 21:32:33', 1, 7.99),
(70, '2025-10-14 09:45:33', 1, 2282.99);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_level` int(11) NOT NULL,
  `image` varchar(255) DEFAULT 'no_image.jpg',
  `status` int(1) NOT NULL,
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `user_level`, `image`, `status`, `last_login`) VALUES
(1, 'Bryan Bernal', 'Admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 1, 'e33ge7jh1.png', 1, '2025-10-14 01:29:57'),
(2, 'Batman', 'Manager', '1a8565a9dc72048ba03b4156be3e569f22771f23', 2, '687qo7ay2.jfif', 1, '2025-10-13 13:17:45'),
(6, 'Superman ', 'cashier', 'a5b42198e3fb950b5ab0d0067cbe077a41da1245', 3, 'k2joptkw6.jfif', 1, '2025-10-13 13:20:58');

-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

CREATE TABLE `user_groups` (
  `id` int(11) NOT NULL,
  `group_name` varchar(150) NOT NULL,
  `group_level` int(11) NOT NULL,
  `group_status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_groups`
--

INSERT INTO `user_groups` (`id`, `group_name`, `group_level`, `group_status`) VALUES
(1, 'Admin', 1, 1),
(2, 'Manager', 2, 1),
(3, 'Cashier ', 3, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `categorie_id` (`categorie_id`),
  ADD KEY `media_id` (`media_id`),
  ADD KEY `unit_id` (`unit_id`),
  ADD KEY `fk_supplier` (`supplier_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `fk_sales_txn` (`transaction_id`);

--
-- Indexes for table `stock_requests`
--
ALTER TABLE `stock_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `requested_by` (`requested_by`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_level` (`user_level`);

--
-- Indexes for table `user_groups`
--
ALTER TABLE `user_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `group_level` (`group_level`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;

--
-- AUTO_INCREMENT for table `stock_requests`
--
ALTER TABLE `stock_requests`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_groups`
--
ALTER TABLE `user_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchases_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_requests`
--
ALTER TABLE `stock_requests`
  ADD CONSTRAINT `fk_stock_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_stock_user` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
