-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 08, 2023 at 04:00 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jgs`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `attendance_status` text NOT NULL,
  `salary` int(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `employee_id`, `date`, `attendance_status`, `salary`) VALUES
(16, 3, '2023-08-26', 'Present', 0),
(17, 4, '2023-08-26', 'Present', 0),
(18, 4, '2023-08-24', 'Present', 0),
(19, 4, '2023-08-22', 'Present', 0),
(20, 3, '2023-08-25', 'Present', 0),
(21, 3, '2023-08-24', 'Present', 0),
(22, 3, '2023-08-27', 'Present', 0),
(23, 4, '2023-08-27', 'Present', 0),
(24, 3, '2023-08-28', 'Present', 0),
(25, 4, '2023-08-28', 'Present', 0),
(26, 3, '2023-08-30', 'Present', 0),
(27, 4, '2023-08-30', 'Present', 0),
(28, 3, '2023-09-04', 'Present', 0),
(29, 4, '2023-09-04', 'Present', 0),
(30, 3, '2023-09-05', 'Present', 0),
(31, 4, '2023-09-05', 'Present', 0),
(73, 3, '2023-09-24', 'Present', 0),
(74, 4, '2023-09-24', 'Present', 0),
(75, 3, '2023-09-24', 'Present', 0),
(76, 3, '2023-09-24', 'Present', 0),
(77, 3, '2023-09-24', 'Present', 0);

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(50) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `product_price` bigint(200) NOT NULL,
  `order_qty` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `cat_id` int(100) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `cat_status` text NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`cat_id`, `category_name`, `cat_status`) VALUES
(1, 'Tools', 'Active'),
(2, 'Building materials', 'Active'),
(3, 'Plumbing Supplies', 'Active'),
(4, 'Electrical Supplies', 'Active'),
(5, 'Paint and Finishes', 'Active'),
(6, 'Hardware Accessories', 'Active'),
(7, 'Safety and Security Equipment', 'Active'),
(8, 'Fasteners and Adhesives', 'Active'),
(9, 'Lawn and Garden Supplies', 'Active'),
(11, 'HVAC Supplies', 'Active'),
(12, 'Automotive Supplies', 'Active'),
(13, 'Industrial Supplies', 'Active'),
(18, 'Office Supplies', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `employee_management`
--

CREATE TABLE `employee_management` (
  `employee_id` int(2) NOT NULL,
  `fullname` varchar(41) NOT NULL,
  `address` varchar(50) NOT NULL,
  `base_salary` bigint(200) NOT NULL,
  `gender` text NOT NULL,
  `age` int(21) NOT NULL,
  `postal_code` text NOT NULL,
  `job_title` text NOT NULL,
  `contact_num` varchar(11) NOT NULL,
  `employee_status` text NOT NULL DEFAULT 'Active' COMMENT 'Active\r\nInactive\r\n'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_management`
--

INSERT INTO `employee_management` (`employee_id`, `fullname`, `address`, `base_salary`, `gender`, `age`, `postal_code`, `job_title`, `contact_num`, `employee_status`) VALUES
(3, 'Rod Angelo Andalis', 'Camarines Sur', 500, 'Male', 21, '675', 'Assistant Manager', '09148374062', 'Active'),
(4, 'Renz Norman Palma', 'Balogo,Oas,Albay', 200, 'Male', 21, '4505', 'Manager', '09158374162', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(50) NOT NULL,
  `item_name` varchar(41) NOT NULL,
  `actual_price` bigint(50) NOT NULL,
  `retail_price` bigint(50) NOT NULL,
  `product_stock` bigint(200) NOT NULL,
  `item_category` text NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `item_details` text NOT NULL,
  `item_status` text NOT NULL DEFAULT 'A' COMMENT 'A-active\r\nI-Inactive\r\n',
  `warning_stock` int(100) NOT NULL,
  `product_unit` text NOT NULL,
  `acronym` varchar(41) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `item_name`, `actual_price`, `retail_price`, `product_stock`, `item_category`, `date_added`, `item_details`, `item_status`, `warning_stock`, `product_unit`, `acronym`) VALUES
(1, 'Acrylic Paint', 24, 23, 190, 'Paint and Finishes', '2023-08-17 22:10:53', 'Boysen, 1liter, Any Color', 'A', 20, 'PC', 'AB3PI'),
(2, 'Screwdriver', 250, 290, 48, 'Tools', '2023-08-18 00:28:41', '5mm', 'A', 5, 'PC', 'Sew2ud'),
(3, 'Screwdriver', 230, 290, 23, 'Tools', '2023-08-18 03:13:01', '55mm', 'A', 12, '', ''),
(4, 'Hollowblock', 20, 25, 20, 'Building Materials', '2023-08-18 03:26:27', '1/2 meters', 'A', 10, 'PC', 'HLLW1'),
(5, 'Paint', 4, 10, 33, 'Paint and Finishes', '2023-08-18 03:27:51', 'Boysen, 1/2liter', 'I', 10, '', ''),
(6, 'Screw', 2, 5, 322, 'Tools', '2023-08-20 02:01:19', '2cm', 'A', 10, '', ''),
(7, 'Tire', 250, 340, 0, 'Automotive Supplies', '2023-08-20 02:02:09', 'motor tire, 30', 'A', 10, '', ''),
(8, 'Plywood', 230, 260, 39, 'Building Materials', '2023-08-20 02:02:57', '3x4', 'A', 10, 'PC', 'WOSQ'),
(9, 'Bulb', 290, 340, 5, 'Electrical Supplies', '2023-08-20 02:03:54', 'white, Circle\r\n', 'A', 10, '', ''),
(10, 'Battery', 20, 45, 38, 'Electrical Supplies', '2023-08-20 02:04:47', 'Eveready, XXX', 'A', 10, '', ''),
(11, 'Faucet', 130, 150, 0, 'Plumbing Supplies', '2023-08-20 02:05:27', 'Brand, Size', 'A', 0, '', ''),
(12, 'Electric Tape', 20, 35, 61, 'Electrical Supplies', '2023-08-20 02:06:01', 'medium', 'A', 0, '', ''),
(13, 'tube', 12, 56, 231, 'Plumbing Supplies', '2023-08-26 14:20:45', 'tube 2x2', 'A', 0, '', ''),
(14, 'Screwdriver', 32, 23232, 28, 'Electrical Supplies', '2023-08-26 14:23:17', '323', 'A', 0, '', ''),
(15, 'Spag Tube', 12, 15, 200, 'HVAC Supplies', '2023-09-23 18:39:57', 'gas tube ', 'A', 0, '', ''),
(16, 'Wires Stripper', 1200, 2000, 13, 'Tools', '2023-09-23 19:09:18', 'Putol wire', 'A', 12, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `transaction_id` int(50) NOT NULL,
  `sales_transaction_code` varchar(50) NOT NULL,
  `user_id` int(2) NOT NULL,
  `product_id` int(50) NOT NULL,
  `total_amt` bigint(200) NOT NULL,
  `product_profit` bigint(200) NOT NULL,
  `product_qty` int(20) NOT NULL,
  `sale_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `employee_id` int(20) NOT NULL,
  `payment_method` text NOT NULL COMMENT 'Gcash\r\nPayMaya\r\nCash',
  `ref_number` varchar(40) DEFAULT NULL,
  `customer_name` text NOT NULL,
  `cust_address` varchar(50) NOT NULL,
  `tin` text NOT NULL,
  `buss_style` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`transaction_id`, `sales_transaction_code`, `user_id`, `product_id`, `total_amt`, `product_profit`, `product_qty`, `sale_date`, `employee_id`, `payment_method`, `ref_number`, `customer_name`, `cust_address`, `tin`, `buss_style`) VALUES
(56, 'JGS699186', 1, 4, 77250, 15450, 3090, '2023-07-31 23:06:35', 1, 'Cash', NULL, '', '', '', ''),
(57, 'JGS699181', 1, 8, 39000, 4500, 150, '2023-08-22 23:06:35', 1, 'Cash', NULL, '', '', '', ''),
(58, 'JGS539032', 1, 4, 25, 5, 1, '2023-08-23 01:24:14', 1, 'Cash', NULL, '', '', '', ''),
(59, 'JGS539032', 1, 5, 1000, 600, 100, '2023-08-23 01:24:14', 1, 'Cash', NULL, '', '', '', ''),
(60, 'JGS539032', 1, 7, 340, 90, 1, '2023-08-23 01:24:14', 1, 'Cash', NULL, '', '', '', ''),
(61, 'JGS539032', 1, 8, 10920, 1260, 42, '2023-08-23 01:24:14', 1, 'Cash', NULL, '', '', '', ''),
(62, 'JGS237122', 1, 4, 25, 5, 1, '2023-08-24 11:50:26', 1, 'Cash', NULL, '', '', '', ''),
(63, 'JGS237122', 1, 5, 30, 18, 3, '2023-08-24 11:50:26', 1, 'Cash', NULL, '', '', '', ''),
(64, 'JGS237122', 1, 6, 5, 3, 1, '2023-08-24 11:50:26', 1, 'Cash', NULL, '', '', '', ''),
(65, 'JGS313332', 1, 4, 25, 5, 1, '2023-08-24 12:01:00', 1, 'Gcash', NULL, 'Renz', 'Balogo', '12323-2323-243', 'Retailer'),
(66, 'JGS313332', 1, 5, 10, 6, 1, '2023-08-24 12:01:00', 1, 'Gcash', NULL, 'Renz', 'Balogo', '12323-2323-243', 'Retailer'),
(67, 'JGS313332', 1, 6, 5, 3, 1, '2023-08-24 12:01:00', 1, 'Gcash', NULL, 'Renz', 'Balogo', '12323-2323-243', 'Retailer'),
(68, 'JGS173088', 1, 4, 25, 5, 1, '2023-08-24 12:50:59', 1, 'Cash', NULL, '', '', '', ''),
(69, 'JGS173088', 1, 5, 10, 6, 1, '2023-08-24 12:50:59', 1, 'Cash', NULL, '', '', '', ''),
(70, 'JGS173088', 1, 7, 340, 90, 1, '2023-08-24 12:50:59', 1, 'Cash', NULL, '', '', '', ''),
(71, 'JGS833042', 1, 12, 315, 135, 9, '2023-08-24 14:19:31', 1, 'Cash', NULL, '', '', '', ''),
(72, 'JGS833042', 1, 4, 25, 5, 1, '2023-08-24 14:19:31', 1, 'Cash', NULL, '', '', '', ''),
(73, 'JGS833042', 1, 5, 40, 24, 4, '2023-08-24 14:19:31', 1, 'Cash', NULL, '', '', '', ''),
(74, 'JGS833042', 1, 6, 5, 3, 1, '2023-08-24 14:19:31', 1, 'Cash', NULL, '', '', '', ''),
(75, 'JGS936640', 1, 4, 25, 5, 1, '2023-08-25 01:59:12', 1, 'Cash', NULL, '', '', '', ''),
(76, 'JGS936640', 1, 5, 10, 6, 1, '2023-08-25 01:59:12', 1, 'Cash', NULL, '', '', '', ''),
(77, 'JGS936640', 1, 6, 5, 3, 1, '2023-08-25 01:59:12', 1, 'Cash', NULL, '', '', '', ''),
(78, 'JGS936640', 1, 7, 340, 90, 1, '2023-08-25 01:59:12', 1, 'Cash', NULL, '', '', '', ''),
(79, 'JGS936640', 1, 12, 35, 15, 1, '2023-08-25 01:59:12', 1, 'Cash', NULL, '', '', '', ''),
(80, 'JGS696763', 1, 4, 3750, 750, 150, '2023-08-25 02:01:15', 1, 'Cash', NULL, 'Renz', 'Balogo', '', ''),
(81, 'JGS234631', 1, 4, 25, 5, 1, '2023-08-25 03:48:51', 1, 'Cash', NULL, '', '', '', ''),
(82, 'JGS234631', 1, 5, 10, 6, 1, '2023-08-25 03:48:51', 1, 'Cash', NULL, '', '', '', ''),
(83, 'JGS234631', 1, 6, 5, 3, 1, '2023-08-25 03:48:51', 1, 'Cash', NULL, '', '', '', ''),
(84, 'JGS234631', 1, 7, 340, 90, 1, '2023-08-25 03:48:51', 1, 'Cash', NULL, '', '', '', ''),
(85, 'JGS234631', 1, 8, 260, 30, 1, '2023-08-25 03:48:51', 1, 'Cash', NULL, '', '', '', ''),
(86, 'JGS128888', 1, 4, 25, 5, 1, '2023-08-25 12:18:40', 1, 'Cash', NULL, '', '', '', ''),
(87, 'JGS128888', 1, 5, 10, 6, 1, '2023-08-25 12:18:40', 1, 'Cash', NULL, '', '', '', ''),
(88, 'JGS128888', 1, 9, 7140, 1050, 21, '2023-08-25 12:18:40', 1, 'Cash', NULL, '', '', '', ''),
(89, 'JGS134278', 1, 7, 4080, 1080, 12, '2023-08-25 14:42:21', 1, 'Cash', NULL, '', '', '', ''),
(90, 'JGS160697', 1, 4, 25, 5, 1, '2023-08-25 14:44:14', 1, 'Cash', NULL, '', '', '', ''),
(91, 'JGS160697', 1, 5, 10, 6, 1, '2023-08-25 14:44:14', 1, 'Cash', NULL, '', '', '', ''),
(92, 'JGS237555', 1, 4, 25, 5, 1, '2023-08-25 23:26:06', 1, 'Cash', NULL, '', '', '', ''),
(93, 'JGS237555', 1, 5, 10, 6, 1, '2023-08-25 23:26:06', 1, 'Cash', NULL, '', '', '', ''),
(94, 'JGS237555', 1, 6, 5, 3, 1, '2023-08-25 23:26:06', 1, 'Cash', NULL, '', '', '', ''),
(95, 'JGS208223', 1, 4, 25, 5, 1, '2023-08-26 14:19:59', 1, 'Cash', NULL, '', '', '', ''),
(96, 'JGS208223', 1, 5, 10, 6, 1, '2023-08-26 14:19:59', 1, 'Cash', NULL, '', '', '', ''),
(97, 'JGS208223', 1, 6, 5, 3, 1, '2023-08-26 14:19:59', 1, 'Cash', NULL, '', '', '', ''),
(98, 'JGS208223', 1, 7, 340, 90, 1, '2023-08-26 14:19:59', 1, 'Cash', NULL, '', '', '', ''),
(99, 'JGS208223', 1, 8, 260, 30, 1, '2023-08-26 14:19:59', 1, 'Cash', NULL, '', '', '', ''),
(100, 'JGS231998', 1, 4, 25, 5, 1, '2023-08-26 14:26:03', 1, 'Cash', NULL, '', '', '', ''),
(101, 'JGS523717', 1, 4, 25, 5, 1, '2023-08-26 14:32:40', 1, 'Cash', NULL, '', '', '', ''),
(102, 'JGS523717', 1, 5, 10, 6, 1, '2023-08-26 14:32:40', 1, 'Cash', NULL, '', '', '', ''),
(103, 'JGS684163', 1, 4, 75, 15, 3, '2023-08-26 14:37:42', 1, 'Cash', NULL, '', '', '', ''),
(104, 'JGS679331', 1, 6, 650, 390, 130, '2023-08-26 22:06:11', 1, 'Cash', NULL, '', '', '', ''),
(105, 'JGS744295', 1, 9, 10200, 1500, 30, '2023-08-26 22:06:53', 1, 'Cash', NULL, '', '', '', ''),
(106, 'JGS272515', 1, 4, 25, 5, 1, '2023-08-28 07:57:17', 1, 'Cash', NULL, '', '', '', ''),
(107, 'JGS272515', 1, 5, 10, 6, 1, '2023-08-28 07:57:17', 1, 'Cash', NULL, '', '', '', ''),
(108, 'JGS272515', 1, 10, 2430, 1350, 54, '2023-08-28 07:57:17', 1, 'Cash', NULL, '', '', '', ''),
(109, 'JGS240558', 1, 11, 7500, 1000, 50, '2023-08-28 07:57:31', 1, 'Cash', NULL, '', '', '', ''),
(110, 'JGS354232', 1, 12, 910, 390, 26, '2023-08-28 07:57:41', 1, 'Cash', NULL, '', '', '', ''),
(111, 'JGS371518', 1, 1, 23, -1, 1, '2023-08-29 02:15:24', 1, 'Cash', NULL, '', '', '', ''),
(112, 'JGS371518', 1, 4, 25, 5, 1, '2023-08-29 02:15:24', 1, 'Cash', NULL, '', '', '', ''),
(113, 'JGS371518', 1, 5, 10, 6, 1, '2023-08-29 02:15:24', 1, 'Cash', NULL, '', '', '', ''),
(114, 'JGS371518', 1, 6, 5, 3, 1, '2023-08-29 02:15:24', 1, 'Cash', NULL, '', '', '', ''),
(115, 'JGS371518', 1, 7, 340, 90, 1, '2023-08-29 02:15:24', 1, 'Cash', NULL, '', '', '', ''),
(116, 'JGS716841', 1, 4, 25, 5, 1, '2023-08-29 22:32:21', 1, 'Cash', NULL, '', '', '', ''),
(117, 'JGS175353', 1, 1, 529, -23, 23, '2023-08-29 22:35:22', 1, 'Cash', NULL, '', '', '', ''),
(118, 'JGS735179', 1, 5, 500, 300, 50, '2023-08-29 22:36:00', 1, 'Cash', NULL, '', '', '', ''),
(119, 'JGS948536', 1, 6, 5, 3, 1, '2023-08-29 22:36:15', 1, 'Cash', NULL, '', '', '', ''),
(120, 'JGS374900', 1, 1, 46, -2, 2, '2023-08-29 22:42:21', 1, 'Cash', NULL, '', '', '', ''),
(121, 'JGS420170', 1, 1, 23, -1, 1, '2023-08-29 22:42:51', 1, 'Cash', NULL, '', '', '', ''),
(122, 'JGS406891', 1, 1, 23, -1, 1, '2023-08-29 22:45:13', 1, 'Cash', NULL, '', '', '', ''),
(123, 'JGS857908', 1, 1, 23, -1, 1, '2023-08-29 22:45:38', 1, 'Cash', NULL, '', '', '', ''),
(124, 'JGS685032', 1, 1, 23, -1, 1, '2023-08-29 22:46:15', 1, 'Cash', NULL, '', '', '', ''),
(125, 'JGS126828', 1, 1, 23, -1, 1, '2023-08-29 22:47:40', 1, 'Cash', NULL, '', '', '', ''),
(126, 'JGS490005', 1, 1, 23, -1, 1, '2023-08-29 22:52:46', 1, 'Cash', NULL, '', '', '', ''),
(127, 'JGS636861', 1, 4, 25, 5, 1, '2023-08-29 22:53:16', 1, 'Cash', NULL, '', '', '', ''),
(128, 'JGS524727', 1, 6, 5, 3, 1, '2023-08-29 22:53:59', 1, 'Cash', NULL, '', '', '', ''),
(129, 'JGS354709', 1, 1, 23, -1, 1, '2023-08-29 22:56:27', 1, 'Cash', NULL, '', '', '', ''),
(130, 'JGS628038', 1, 1, 23, -1, 1, '2023-08-29 22:59:25', 1, 'Cash', NULL, '', '', '', ''),
(131, 'JGS618394', 1, 1, 23, -1, 1, '2023-08-29 23:03:47', 1, 'Cash', NULL, '', '', '', ''),
(132, 'JGS618394', 1, 4, 25, 5, 1, '2023-08-29 23:03:47', 1, 'Cash', NULL, '', '', '', ''),
(133, 'JGS130348', 1, 5, 40, 24, 4, '2023-08-29 23:04:09', 1, 'Cash', NULL, '', '', '', ''),
(134, 'JGS736424', 1, 1, 23, -1, 1, '2023-08-29 23:08:44', 1, 'Cash', NULL, '', '', '', ''),
(135, 'JGS736424', 1, 4, 25, 5, 1, '2023-08-29 23:08:44', 1, 'Cash', NULL, '', '', '', ''),
(136, 'JGS838905', 1, 1, 23, -1, 1, '2023-08-29 23:09:23', 1, 'Cash', NULL, '', '', '', ''),
(137, 'JGS778901', 1, 1, 23, -1, 1, '2023-08-29 23:09:51', 1, 'Cash', NULL, '', '', '', ''),
(138, 'JGS778901', 1, 4, 25, 5, 1, '2023-08-29 23:09:51', 1, 'Cash', NULL, '', '', '', ''),
(139, 'JGS897821', 1, 1, 23, -1, 1, '2023-08-29 23:10:11', 1, 'Cash', NULL, '', '', '', ''),
(140, 'JGS843737', 1, 1, 23, -1, 1, '2023-08-29 23:11:38', 1, 'Cash', NULL, '', '', '', ''),
(141, 'JGS189653', 1, 1, 23, -1, 1, '2023-08-29 23:12:02', 1, 'Cash', NULL, '', '', '', ''),
(142, 'JGS189653', 1, 5, 10, 6, 1, '2023-08-29 23:12:02', 1, 'Cash', NULL, '', '', '', ''),
(143, 'JGS317134', 1, 1, 23, -1, 1, '2023-08-29 23:12:18', 1, 'Cash', NULL, '', '', '', ''),
(144, 'JGS493583', 1, 1, 23, -1, 1, '2023-08-29 23:14:59', 1, 'Cash', NULL, '', '', '', ''),
(145, 'JGS493583', 1, 5, 10, 6, 1, '2023-08-29 23:14:59', 1, 'Cash', NULL, '', '', '', ''),
(146, 'JGS554398', 1, 1, 23, -1, 1, '2023-08-29 23:15:47', 1, 'Cash', NULL, '', '', '', ''),
(147, 'JGS554398', 1, 5, 10, 6, 1, '2023-08-29 23:15:47', 1, 'Cash', NULL, '', '', '', ''),
(148, 'JGS949907', 1, 12, 35, 15, 1, '2023-08-30 01:10:37', 1, 'Cash', NULL, '', '', '', ''),
(149, 'JGS581739', 1, 5, 30, 18, 3, '2023-08-30 03:18:04', 1, 'Cash', NULL, '', '', '', ''),
(150, 'JGS386729', 1, 5, 90, 54, 9, '2023-08-30 03:18:20', 1, 'Cash', NULL, '', '', '', ''),
(151, 'JGS478586', 1, 1, 23, -1, 1, '2023-08-30 07:31:46', 1, 'Cash', NULL, '', '', '', ''),
(152, 'JGS478586', 1, 6, 5, 3, 1, '2023-08-30 07:31:46', 1, 'Cash', NULL, '', '', '', ''),
(153, 'JGS879843', 1, 14, 69696, 69600, 3, '2023-09-04 03:10:19', 1, 'Cash', NULL, '', '', '', ''),
(154, 'JGS805819', 1, 5, 60, 36, 6, '2023-09-05 00:49:53', 1, 'Cash', NULL, '', '', '', ''),
(155, 'JGS805819', 1, 6, 5, 3, 1, '2023-09-05 00:49:53', 1, 'Cash', NULL, '', '', '', ''),
(156, 'JGS937686', 1, 5, 10, 6, 1, '2023-09-05 00:50:25', 1, 'Cash', NULL, '', '', '', ''),
(157, 'JGS188076', 1, 5, 10, 6, 1, '2023-09-05 01:23:01', 1, 'Cash', NULL, '', '', '', ''),
(158, 'JGS188076', 1, 6, 5, 3, 1, '2023-09-05 01:23:01', 1, 'Cash', NULL, '', '', '', ''),
(159, 'JGS188076', 1, 7, 340, 90, 1, '2023-09-05 01:23:01', 1, 'Cash', NULL, '', '', '', ''),
(160, 'JGS188076', 1, 8, 260, 30, 1, '2023-09-05 01:23:01', 1, 'Cash', NULL, '', '', '', ''),
(161, 'JGS428715', 1, 5, 10, 6, 1, '2023-09-21 09:17:12', 1, 'Cash', NULL, '', '', '', ''),
(162, 'JGS428715', 1, 7, 340, 90, 1, '2023-09-21 09:17:12', 1, 'Cash', NULL, '', '', '', ''),
(163, 'JGS428715', 1, 8, 260, 30, 1, '2023-09-21 09:17:12', 1, 'Cash', NULL, '', '', '', ''),
(164, 'JGS288290', 1, 7, 340, 90, 1, '2023-09-21 09:17:33', 1, 'Cash', NULL, '', '', '', ''),
(165, 'JGS288290', 1, 8, 260, 30, 1, '2023-09-21 09:17:33', 1, 'Cash', NULL, '', '', '', ''),
(166, 'JGS288290', 1, 10, 45, 25, 1, '2023-09-21 09:17:33', 1, 'Cash', NULL, '', '', '', ''),
(167, 'JGS822859', 1, 1, 115, -5, 5, '2023-09-24 11:38:21', 1, 'Cash', NULL, '', '', '', ''),
(168, 'JGS822859', 1, 2, 290, 40, 1, '2023-09-24 11:38:21', 1, 'Cash', NULL, '', '', '', ''),
(169, 'JGS822859', 1, 9, 680, 100, 2, '2023-09-24 11:38:21', 1, 'Cash', NULL, '', '', '', ''),
(170, 'JGS08112023-1', 1, 1, 23, -1, 1, '2023-11-08 00:47:54', 1, 'Cash', NULL, '', '', '', ''),
(171, 'JGS08112023-2', 1, 1, 23, -1, 1, '2023-11-08 01:09:14', 1, 'Gcash', '', '', '', '', ''),
(172, 'JGS08112023-3', 1, 1, 23, -1, 1, '2023-11-08 01:12:08', 1, 'PayMaya', '23434', '', '', '', ''),
(173, 'JGS08112023-4', 1, 1, 23, -1, 1, '2023-11-08 01:28:58', 1, 'Cash', NULL, '', '', '', ''),
(174, 'JGS08112023-4', 1, 8, 260, 30, 1, '2023-11-08 01:28:58', 1, 'Cash', NULL, '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(41) NOT NULL,
  `user_pass` varchar(50) NOT NULL,
  `user_type` text NOT NULL DEFAULT '\'Admin\'' COMMENT 'A-Admin\r\nE-Employee',
  `hashed_pass` varchar(50) NOT NULL,
  `private_key` varchar(50) NOT NULL,
  `online_offline` varchar(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `user_pass`, `user_type`, `hashed_pass`, `private_key`, `online_offline`) VALUES
(1, 'Admin_MANAGER', 'admin0102', 'Admin', '7463cb850c6def68589f70c7081c8c90', 'f5b28074ae87575fab863f1562c9ecb9', 'online'),
(3, 'Cashier1', 'cashier', 'cashier', 'bd8eecc9825ed8753f5add77435efdd0', '43f659eb4785c386933a3942bd0335bc', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `employee_management`
--
ALTER TABLE `employee_management`
  ADD PRIMARY KEY (`employee_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=241;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `employee_management`
--
ALTER TABLE `employee_management`
  MODIFY `employee_id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `transaction_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=175;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
