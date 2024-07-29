-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 25, 2024 at 02:24 PM
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
  `image` text DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`aid`, `fname`, `lname`, `uname`, `email`, `password`, `image`, `date_updated`) VALUES
(1, 'Nonong', 'Alcantara', 'admin', 'admin123@gmail.com', '123456', NULL, NULL);

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
(1, 0, '1', '<br />\r\n<b>Warning</b>:  Trying to access array offset on value of type null in <b>C:xampphtdocs\riosystemRestroadminconfirm.php</b> on line <b>61</b><br />\r\n', '2024-07-17 22:05:54'),
(2, 0, '1', '<br />\r\n<b>Warning</b>:  Trying to access array offset on value of type null in <b>C:xampphtdocs\r\niosystemRestroadminconfirm.php</b> on line <b>61</b><br />\r\n', '2024-07-17 22:06:09'),
(3, 0, '1', '<br />\r\n<b>Warning</b>:  Trying to access array offset on value of type null in <b>C:xampphtdocs\r\niosystemRestroadminconfirm.php</b> on line <b>61</b><br />\r\n', '2024-07-17 22:09:08'),
(4, 0, '2', '<br />\r\n<b>Warning</b>:  Trying to access array offset on value of type null in <b>C:xampphtdocs\r\niosystemRestroadminconfirm.php</b> on line <b>61</b><br />\r\n', '2024-07-17 22:15:38'),
(5, 0, '1', '', '2024-07-17 22:23:25'),
(6, 66835084, '1', '<br />\r\n<b>Warning</b>:  Trying to access array offset on value of type null in <b>C:xampphtdocs\riosystemRestroadminpartialss\\_bookingStatusModal.php</b> on line <b>69</b><br />\r\n', '2024-07-17 22:45:24'),
(7, 0, '2', '<br />\r\n<b>Warning</b>:  Trying to access array offset on value of type null in <b>C:xampphtdocs\r\niosystemRestroadminconfirm.php</b> on line <b>61</b><br />\r\n', '2024-07-17 23:03:10'),
(8, 70428330, '1', '<br />\r\n<b>Warning</b>:  Trying to access array offset on value of type null in <b>C:xampphtdocs\riosystemRestroadminpartialss\\_bookingStatusModal.php</b> on line <b>69</b><br />\r\n', '2024-07-17 23:10:08'),
(9, 40431695, '2', '<br />\r\n<b>Warning</b>:  Trying to access array offset on value of type null in <b>C:xampphtdocs\riosystemRestroadminpartialss\\_bookingStatusModal.php</b> on line <b>69</b><br />\r\n', '2024-07-17 23:21:49'),
(10, 78449884, '1', '<br />\r\n<b>Warning</b>:  Trying to access array offset on value of type null in <b>C:xampphtdocs\riosystemRestroadminpartialss\\_bookingStatusModal.php</b> on line <b>69</b><br />\r\n', '2024-07-17 23:34:00'),
(11, 96403933, '1', 'thank u for booking', '2024-07-17 23:35:41'),
(12, 16959307, '1', 'aa', '2024-07-18 00:36:08'),
(13, 22050516, '2', 'aaaa', '2024-07-18 00:40:47'),
(14, 13519839, '1', 'dsgsdg', '2024-07-18 00:42:06'),
(15, 53691068, '2', 'sdfag', '2024-07-18 00:43:14'),
(16, 67780820, '2', 'CQ', '2024-07-18 00:44:31'),
(17, 88620803, '1', 'aaaaaaa', '2024-07-18 00:47:10'),
(18, 28875714, '1', 'gggg', '2024-07-18 03:30:22');

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
(1, '2024-07-24 15:38:08', 'Pending'),
(2, '2024-07-24 15:39:42', 'Pending'),
(3, '2024-07-24 15:40:04', 'Pending'),
(4, '2024-07-24 15:41:48', 'Pending'),
(5, '2024-07-24 15:42:32', 'Pending'),
(6, '2024-07-24 15:43:48', 'Pending'),
(7, '2024-07-24 15:49:16', 'Pending'),
(8, '2024-07-24 15:58:17', 'Pending'),
(9, '2024-07-24 16:04:32', 'Pending'),
(10, '2024-07-24 16:08:25', 'Pending'),
(11, '2024-07-24 16:10:38', 'Pending'),
(12, '2024-07-24 16:13:23', 'Pending'),
(13, '2024-07-24 16:13:49', 'Pending'),
(14, '2024-07-24 16:14:06', 'Paid'),
(15, '2024-07-24 16:17:31', 'Paid'),
(16, '2024-07-24 16:22:00', 'Paid'),
(17, '2024-07-24 16:23:55', 'Paid'),
(18, '2024-07-24 16:26:19', 'Paid'),
(19, '2024-07-24 16:29:18', 'Paid'),
(20, '2024-07-24 16:35:22', 'Paid'),
(21, '2024-07-24 17:52:53', 'Paid'),
(22, '2024-07-24 18:01:43', 'Pending'),
(23, '2024-07-24 18:04:08', 'Pending'),
(24, '2024-07-24 18:05:55', 'Pending'),
(25, '2024-07-24 18:06:15', 'Paid'),
(26, '2024-07-25 06:24:52', 'Paid'),
(27, '2024-07-25 06:36:48', 'Paid'),
(28, '2024-07-25 06:43:11', 'Pending');

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
(1, 3, 4, 'Aloha pizza', '149', NULL),
(2, 3, 7, 'w/ Veggies', '79', NULL),
(3, 3, 4, 'Aloha pizza', '149', NULL),
(4, 3, 7, 'w/ Veggies', '79', NULL),
(5, 3, 7, 'w/ Veggies', '79', NULL),
(6, 3, 4, 'Aloha pizza', '149', NULL),
(7, 3, 7, 'w/ Veggies', '79', NULL),
(8, 3, 4, 'Aloha pizza', '149', NULL),
(9, 3, 4, 'Aloha pizza', '149', '1'),
(10, 3, 7, 'w/ Veggies', '79', '1'),
(11, 3, 7, 'w/ Veggies', '79', '1'),
(12, 3, 9, 'kamote co', '24', '1'),
(13, 3, 4, 'Aloha pizza', '149', '1'),
(14, 3, 4, 'Aloha pizza', '149', '1'),
(15, 3, 7, 'w/ Veggies', '79', '1'),
(16, 3, 0, '', '', '1'),
(17, 3, 4, 'Aloha pizza', '149', '1'),
(18, 3, 7, 'w/ Veggies', '79', '1'),
(19, 3, 4, 'Aloha pizza', '149', '1'),
(20, 4, 5025, 'kamote chips', '50', '1'),
(21, 4, 9, 'kamote co', '24', '1'),
(22, 4, 5025, 'kamote chips', '50', '1'),
(23, 6, 7, 'w/ Veggies', '79', '1'),
(24, 6, 7, 'w/ Veggies', '79', '1'),
(25, 7, 975411, 'w/ Ham', '79', '1'),
(26, 7, 9, 'kamote co', '24', '1'),
(27, 8, 4, 'Aloha pizza', '149', '2'),
(28, 8, 4, 'Aloha pizza', '149', '1'),
(29, 8, 4, 'Aloha pizza', '149', '1'),
(30, 9, 4, 'Aloha pizza', '149', '2'),
(31, 9, 7, 'w/ Veggies', '79', '2'),
(32, 10, 4, 'Aloha pizza', '149', '3'),
(33, 10, 7, 'w/ Veggies', '79', '2'),
(34, 11, 54, 'Sizzling Tuna Sisig', '149', '1'),
(35, 11, 407, 'Cheese Sandwitch', '69', '1'),
(36, 12, 54, 'Sizzling Tuna Sisig', '149', '2'),
(37, 12, 4, 'Aloha pizza', '149', '2'),
(38, 14, 4, 'Aloha pizza', '149', '2'),
(39, 14, 7, 'w/ Veggies', '79', '3'),
(40, 15, 4, 'Aloha pizza', '149', '3'),
(41, 15, 7, 'w/ Veggies', '79', '1'),
(42, 16, 4, 'Aloha pizza', '149', '3'),
(43, 17, 4, 'Aloha pizza', '149', '5'),
(44, 18, 9, 'kamote co', '24', '2'),
(45, 18, 4, 'Aloha pizza', '149', '3'),
(46, 19, 4, 'Aloha pizza', '149', '3'),
(47, 19, 54, 'Sizzling Tuna Sisig', '149', '4'),
(48, 20, 6242, 'w/ Bacon Burger', '79', '1'),
(49, 20, 4, 'Aloha pizza', '149', '3'),
(50, 20, 54, 'Sizzling Tuna Sisig', '149', '1'),
(51, 20, 407, 'Cheese Sandwitch', '69', '1'),
(52, 21, 4, 'Aloha pizza', '149', '1'),
(53, 21, 7, 'w/ Veggies', '79', '1'),
(54, 22, 975411, 'w/ Ham', '79', '3'),
(55, 23, 4, 'Aloha pizza', '149', '1'),
(56, 24, 4, 'Aloha pizza', '149', '2'),
(57, 25, 4, 'Aloha pizza', '149', '1'),
(58, 26, 4, 'Aloha pizza', '149', '2'),
(59, 26, 7, 'w/ Veggies', '79', '4'),
(60, 26, 54, 'Sizzling Tuna Sisig', '149', '1'),
(61, 27, 4, 'Aloha pizza', '149', '2'),
(62, 28, 6242, 'w/ Bacon Burger', '79', '1');

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
(41, 33824808, '2024-07-23', '2024-07-25', 'Standard Single Room', '₱ 1700.00', '', 'kenken', 'D', 'rich@gmail.com', '0935252532', 'America', 'cash', 'Confirmed', '2024-07-23 09:39:10'),
(42, 40792995, '2024-07-25', '2024-07-31', 'Standard Twin Room', '₱ 9600.00', '', 'bebe', 'jose', 'josejasoncueva402@gmail.com', '09352525325', 'Philippines', 'cash', 'Confirmed', '2024-07-25 06:53:44');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `id` int(11) NOT NULL,
  `r_name` varchar(200) NOT NULL,
  `available` varchar(200) NOT NULL,
  `bed` varchar(200) NOT NULL,
  `bath` varchar(200) NOT NULL,
  `price` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`id`, `r_name`, `available`, `bed`, `bath`, `price`) VALUES
(9, 'Standard Single Room', '1 Available Room', '1 Bed', '1 Bath', '850'),
(10, 'Standard Twin Room', '2 Availble Room', '2 Bed', '1 Bath', '1,600');

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
(4, 'Aloha pizza', 't.jpeg', '149', '2024-07-10 22:15:48.019279'),
(7, 'w/ Veggies', 'received_7983281768432512.jpeg', '79', '2024-07-10 22:08:19.931252'),
(9, 'kamote co', 'h.jpg', '24', '2024-07-24 11:30:46.483059'),
(54, 'Sizzling Tuna Sisig', 'f.jpeg', '149', '2024-07-10 22:29:53.239489'),
(79, 'cassava ni jason', 'a.jpg', '150', '2024-07-24 09:16:14.328832'),
(407, 'Cheese Sandwitch', 'd.jpeg', '69', '2024-07-10 22:05:07.866688'),
(5025, 'kamote chips', 'a.jpg', '50', '2024-07-24 09:21:20.175306'),
(6242, 'w/ Bacon Burger', 'j.jpeg', '79', '2024-07-10 22:06:48.061692'),
(97440, 'French Fries', 'm.jpeg', '59', '2024-07-10 22:28:44.307537'),
(975411, 'w/ Ham', 'i.jpeg', '79', '2024-07-10 22:07:47.216533'),
(95866802, 'Meat overload', 'g.jpeg', '200', '2024-07-10 22:18:48.863699'),
(95866803, 'cassava ni bebe', 'a.jpg', '150', '2024-07-25 06:44:45.753929');

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
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `order_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `rpos_products`
--
ALTER TABLE `rpos_products`
  MODIFY `prod_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95866804;

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
