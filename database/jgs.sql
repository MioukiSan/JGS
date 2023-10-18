-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 18, 2023 at 12:56 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

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
-- Table structure for table `employee_management`
--

CREATE TABLE `employee_management` (
  `employee_id` int(2) NOT NULL,
  `fullname` varchar(41) NOT NULL,
  `address` varchar(50) NOT NULL,
  `basic_salary` bigint(200) NOT NULL,
  `daily_wage` bigint(200) NOT NULL,
  `gender` text NOT NULL,
  `postal_code` text NOT NULL,
  `job_title` text NOT NULL,
  `contact_num` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `item_status` text NOT NULL DEFAULT 'A' COMMENT 'A-active\r\nI-Inactive\r\n'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `item_name`, `actual_price`, `retail_price`, `product_stock`, `item_category`, `date_added`, `item_details`, `item_status`) VALUES
(1, 'Acrylic Paint', 24, 23, 45, 'Paint and Finishes', '2023-08-17 22:10:53', 'Boysen, 1liter, Any Color', 'Inactive'),
(2, 'Screwdriver', 250, 290, 30, 'Tools', '2023-08-18 00:28:41', '5mm', 'A'),
(3, 'Screwdriver', 230, 290, 40, 'Tools', '2023-08-18 03:13:01', '55mm', 'A'),
(4, 'Hollowblock', 20, 25, 1000, 'Building Materials', '2023-08-18 03:26:27', '1/2 meters', 'A'),
(5, 'Paint', 4, 10, 260, 'Paint and Finishes', '2023-08-18 03:27:51', 'Boysen, 1/2liter', 'A');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `sales_transaction_code` int(50) NOT NULL,
  `user_id` int(2) NOT NULL,
  `product_id` int(50) NOT NULL,
  `total_amt` bigint(200) NOT NULL,
  `product_profit` bigint(200) NOT NULL,
  `product_qty` int(20) NOT NULL,
  `sale_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `employee_id` int(20) NOT NULL,
  `payment_method` text NOT NULL COMMENT 'Gcash\r\nPayMaya\r\nCash'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(41) NOT NULL,
  `user_pass` varchar(50) NOT NULL,
  `user_type` text NOT NULL DEFAULT '\'Admin\'' COMMENT 'A-Admin\r\nE-Employee',
  `hashed_pass` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `user_pass`, `user_type`, `hashed_pass`) VALUES
(1, 'admin', 'admin', 'Admin', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`);

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
  ADD PRIMARY KEY (`sales_transaction_code`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_management`
--
ALTER TABLE `employee_management`
  MODIFY `employee_id` int(2) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sales_transaction_code` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
