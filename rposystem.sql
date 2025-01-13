-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 08, 2024 at 04:12 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rposystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `aid` int(11) NOT NULL,
  `fname` varchar(200) NOT NULL,
  `lname` varchar(200) NOT NULL,
  `uname` varchar(200) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expire` datetime DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`aid`, `fname`, `lname`, `uname`, `email`, `password`, `reset_token`, `token_expire`, `date_updated`) VALUES
(1, 'Jason', 'Cueva', 'admin', 'riomanagement123@gmail.com', '$2y$10$/Bp8ZesO7iFvJIALn.qYguHKuEnyo0qIccoH4qcigX2Nz5XeuneAq', NULL, NULL, '2024-10-04 21:33:57');

-- --------------------------------------------------------

--
-- Table structure for table `booking_status`
--

CREATE TABLE `booking_status` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `booking_status` varchar(255) NOT NULL COMMENT '1=Confirmed;\r\n2=Rejected;',
  `remarks` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_status`
--

INSERT INTO `booking_status` (`id`, `booking_id`, `booking_status`, `remarks`, `created`) VALUES
(15, 53691068, '2', 'sdfag', '2024-07-18 00:43:14'),
(16, 67780820, '2', 'CQ', '2024-07-18 00:44:31'),
(17, 88620803, '1', 'aaaaaaa', '2024-07-18 00:47:10'),
(18, 28875714, '1', 'gggg', '2024-07-18 03:30:22');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` bigint(11) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `verified` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id`, `name`, `email`, `password`, `phone`, `date_created`, `verified`) VALUES
(56, 'Jose Jason Cueva', 'josejasoncueva402@gmail.com', '$2y$10$861kObD6.Pm5AwgTbURrpeR70tz3S7k6j..KqYtN.LU8BLUmMMiwy', 9887865565, '2024-10-05 04:35:10', 1),
(57, 'eric', 'fordkylie99@gmail.com', '$2y$10$OeryxUUjGrWt9hahwdo41eLOTzneA2D42S74fSu3nE.xSGEnqDwPe', 9887865565, '2024-10-06 04:43:23', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `order_date`, `status`) VALUES
(71, '2024-10-06 09:05:49', 'Rejected'),
(72, '2024-10-06 09:13:35', 'Reserved'),
(73, '2024-10-06 09:15:00', 'Reserved');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `order_detail_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `prod_id` int(11) DEFAULT NULL,
  `prod_name` varchar(255) DEFAULT NULL,
  `prod_price` varchar(255) DEFAULT NULL,
  `quantity` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`order_detail_id`, `order_id`, `prod_id`, `prod_name`, `prod_price`, `quantity`) VALUES
(121, 71, 1, 'Flavored Beer', '70', '1'),
(122, 72, 1, 'Flavored Beer', '70', '1'),
(123, 73, 1, 'Flavored Beer', '70', '1');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `checkin_date` date NOT NULL,
  `checkout_date` date NOT NULL,
  `r_name` varchar(200) NOT NULL,
  `amount` varchar(225) NOT NULL,
  `title` varchar(10) NOT NULL,
  `first_name` varchar(225) NOT NULL,
  `last_name` varchar(225) NOT NULL,
  `email` varchar(225) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `country` varchar(50) NOT NULL,
  `payment` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `booking_id`, `checkin_date`, `checkout_date`, `r_name`, `amount`, `title`, `first_name`, `last_name`, `email`, `phone`, `country`, `payment`, `status`, `created_at`) VALUES
(42, 40792995, '2024-07-25', '2024-07-31', 'Standard Twin Room', '₱ 9600.00', '', 'bebe', 'jose', 'josejasoncueva402@gmail.com', '09352525325', 'Philippines', 'cash', 'Confirmed', '2024-07-25 06:53:44'),
(43, 66364352, '2024-09-29', '2024-10-01', 'Standard Single Room', '₱ 1700.00', '', 'jose', 'cueva', 'dong@gmail.com', '09887865565', '', 'cash', 'Pending', '2024-09-29 02:51:25'),
(44, 67763305, '2024-09-30', '2024-10-01', 'Standard Twin Room', '₱ 1600.00', '', 'jose', 'cueva', 'josejasoncueva402@gmail.com', '09887865565', '', 'cash', 'Pending', '2024-09-30 06:52:24'),
(45, 10111290, '2024-10-02', '2024-10-03', 'Standard Twin Room', '₱ 3200.00', '', 'jose', 'cueva-o', 'josejasoncueva402@gmail.com', '09887865565', '', 'cash', 'Pending', '2024-10-02 09:34:53'),
(46, 52177605, '2024-10-02', '2024-10-03', 'Standard Single Room', '₱ 850.00', '', 'jose', 'cueva-o', 'josejasoncueva402@gmail.com', '09887865565', '', 'cash', 'Pending', '2024-10-02 09:35:24'),
(47, 77650134, '2024-10-02', '2024-10-03', 'Standard Single Room', '₱ 850.00', '', 'jose', 'cueva', 'josejasoncueva402@gmail.com', '09887865565', '', 'cash', 'Pending', '2024-10-02 09:41:28'),
(48, 75125746, '2024-10-04', '2024-10-05', 'Standard Single Room', '₱ 1233.00', '', 'jose', 'cueva', 'josejasoncueva402@gmail.com', '09887865565', '', 'cash', 'Confirmed', '2024-10-04 08:36:56');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `id` int(11) NOT NULL,
  `r_name` varchar(200) NOT NULL,
  `r_img` varchar(200) NOT NULL,
  `available` varchar(200) NOT NULL,
  `bed` varchar(200) NOT NULL,
  `bath` varchar(200) NOT NULL,
  `price` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`id`, `r_name`, `r_img`, `available`, `bed`, `bath`, `price`) VALUES
(33, 'Standard Single Room', '6700dc3111bd1.jpg', '1 Available Room', '1 Bed', '1 Bath', '1233'),
(34, 'Standard Twin Room', '6700dc56537e4.jpg', '1 Available Room', '1 Bed', '1 Bath', '850');

-- --------------------------------------------------------

--
-- Table structure for table `rpos_products`
--

CREATE TABLE `rpos_products` (
  `prod_id` int(11) NOT NULL,
  `prod_name` varchar(200) NOT NULL,
  `prod_img` varchar(200) NOT NULL,
  `prod_price` varchar(200) NOT NULL,
  `created_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `rpos_products`
--

INSERT INTO `rpos_products` (`prod_id`, `prod_name`, `prod_img`, `prod_price`, `created_at`) VALUES
(1, 'Flavored Beer', 'Flavor beer.jpeg', '70', '2024-09-18 06:02:31.092414'),
(2, 'Hawaiian Pizza Small', 'Hawaiian pizza.jpeg', '149', '2024-09-18 05:32:33.151979'),
(6, 'Grenade', '451837173_907304558081811_5239184907447010418_n.jpg', '180', '2024-09-18 07:26:58.717920'),
(8, 'Blue Git', 'j.jpg', '180', '2024-09-18 06:01:32.547378'),
(52, 'Pancit Bihon', 'Pancit bihon.jpeg', '189', '2024-09-18 05:25:55.615384'),
(70, 'Hot Silog', 'hot silog.jpeg', '119', '2024-09-18 05:16:01.776840'),
(77, 'Red Horse', '427770547_401771799170279_5540115988153060509_n.jpg', '140', '2024-09-18 05:21:50.439554'),
(485, 'Coke Litro', '1l coke.jpg', '60', '2024-09-18 05:31:04.971040'),
(788, 'w/ Veggies', 'received_7983281768432512.jpeg', '79', '2024-09-18 05:35:38.025140'),
(30796, 'Pork', 'pork.jpeg', '89', '2024-09-18 06:48:31.119508'),
(63093, 'Ice Tea Pitcher', 'Ice tea pitcher.jpeg', '60', '2024-09-18 07:26:11.732454'),
(6908742, 'Yummy Hotdog Medium', 'b.jpeg', '159', '2024-09-18 05:28:57.471844'),
(8586476, 'Yummy Hotdog Small', 'b.jpeg', '149', '2024-09-18 05:26:54.157595'),
(600000003, 'Chicken Pastel', 'chicken pastil.jpeg', '89', '2024-09-18 05:13:47.268709'),
(2147483647, 'Peanut Butter Choco Shake', '450849766_1714368762721890_120875686019331137_n.jpg', '89', '2024-09-18 05:22:47.422189');

-- --------------------------------------------------------

--
-- Table structure for table `rpos_staff`
--

CREATE TABLE `rpos_staff` (
  `id` int(11) NOT NULL,
  `staff_name` varchar(255) NOT NULL,
  `staff_last_name` varchar(255) NOT NULL,
  `staff_email` varchar(100) NOT NULL,
  `staff_password` varchar(255) NOT NULL,
  `staff_gender` enum('Male','Female') NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rpos_staff`
--

INSERT INTO `rpos_staff` (`id`, `staff_name`, `staff_last_name`, `staff_email`, `staff_password`, `staff_gender`, `date_created`) VALUES
(31, 'jose', 'cueva', 'jason@gmail.com', 'Jason&123', 'Male', '2024-10-05 05:38:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`aid`);

--
-- Indexes for table `booking_status`
--
ALTER TABLE `booking_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_detail_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rpos_products`
--
ALTER TABLE `rpos_products`
  ADD PRIMARY KEY (`prod_id`);

--
-- Indexes for table `rpos_staff`
--
ALTER TABLE `rpos_staff`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `aid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `booking_status`
--
ALTER TABLE `booking_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `order_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `rpos_products`
--
ALTER TABLE `rpos_products`
  MODIFY `prod_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2147483648;

--
-- AUTO_INCREMENT for table `rpos_staff`
--
ALTER TABLE `rpos_staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
