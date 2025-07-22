-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 11, 2025 at 01:28 PM
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
-- Database: `restrozone`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `adm_id` int(11) NOT NULL,
  `username` varchar(222) NOT NULL,
  `password` varchar(222) NOT NULL,
  `email` varchar(222) NOT NULL,
  `code` varchar(222) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`adm_id`, `username`, `password`, `email`, `code`, `date`) VALUES
(4, 'restrozone', '1aace3d5eb0881ff73f83d0f208be345', 'admin@restrozone.com', '', '2025-05-31 12:34:52');

-- --------------------------------------------------------

--
-- Table structure for table `dishes`
--

CREATE TABLE `dishes` (
  `d_id` int(11) NOT NULL,
  `rs_id` int(11) NOT NULL,
  `title` varchar(222) NOT NULL,
  `slogan` varchar(222) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `img` varchar(222) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `dishes`
--

INSERT INTO `dishes` (`d_id`, `rs_id`, `title`, `slogan`, `price`, `img`) VALUES
(1, 1, 'Noodles', 'A flavorful stir-fry of soft, slurpy noodles tossed with crunchy vegetables, aromatic garlic, soy sauce.', 60.00, '680748aea8a9b.jpg'),
(2, 1, 'Manchurian', 'A popular Indo-Chinese dish featuring crispy fried vegetable or chicken balls soaked in a tangy, spicy Manchurian.', 60.00, '680748cec65f7.jpg'),
(4, 1, 'Dumplings', 'Steamed or fried parcels filled with finely chopped vegetables, chicken, or paneer, seasoned with Asian herbs and spices.', 70.00, '68074908a4fd6.jpg'),
(5, 2, 'Dosa', 'A thin, crispy crepe made from a fermented rice and lentil batter, dosa is golden brown and served hot', 40.00, '68074942b44d2.jpg'),
(6, 2, 'Idli', 'Soft, fluffy, and steamed to perfection, idlis are a staple South Indian breakfast', 40.00, '6807496e3d166.jpg'),
(7, 2, 'Mendu wada', 'Crispy on the outside, soft and airy inside, medu vadas are savory lentil doughnuts made from urad dal.', 80.00, '680749966330e.jpg'),
(8, 2, 'Biryani', 'Aromatic and richly spiced, South Indian biryani blends fragrant basmati rice with marinated meat.', 110.00, '680749bf0ad6a.jpg'),
(9, 3, 'Aloo paratha', 'stuffed potato flatbread served with curd or pickle.', 50.00, '6807483fc813f.jpg'),
(10, 3, 'Choole Bhatura', 'spicy chickpeas served with deep-fried bread.', 70.00, '68074807e08a9.jpg'),
(11, 3, 'Chicken', 'tandoori chicken in a creamy tomato gravy.', 200.00, '680747e382b81.jpg'),
(12, 3, 'Fish curry', 'Fish curry is a flavorful dish made with tender fish pieces simmered in a spiced, tangy gravy of onions, tomatoes, and traditional Indian spices', 180.00, '680747a92e9f6.jpg'),
(13, 4, 'Misal Pav', 'A spicy sprouted moth bean curry (usal) topped with crunchy farsan, chopped onions, coriander.', 35.00, '68074720cf2b7.jpg'),
(14, 4, 'Vada pav', 'Served with our traditional spicy queso and marinara sauce.', 20.00, '680746ce45bf7.jpg'),
(15, 4, 'Batata Vada', 'Mashed potatoes seasoned with mustard seeds, garlic, ginger, and green chilies.', 30.00, '680746a22a36a.jpg'),
(16, 4, 'Poha', 'Flattened rice lightly tempered with mustard seeds, turmeric, curry leaves, and green chilies.', 35.00, '6807464a0e8ec.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `remark`
--

CREATE TABLE `remark` (
  `id` int(11) NOT NULL,
  `frm_id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `remark` mediumtext NOT NULL,
  `remarkDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restaurant`
--

CREATE TABLE `restaurant` (
  `rs_id` int(11) NOT NULL,
  `c_id` int(11) NOT NULL,
  `title` varchar(222) NOT NULL,
  `email` varchar(222) NOT NULL,
  `phone` varchar(222) NOT NULL,
  `url` varchar(222) NOT NULL,
  `o_hr` varchar(222) NOT NULL,
  `c_hr` varchar(222) NOT NULL,
  `o_days` varchar(222) NOT NULL,
  `address` text NOT NULL,
  `image` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `restaurant`
--

INSERT INTO `restaurant` (`rs_id`, `c_id`, `title`, `email`, `phone`, `url`, `o_hr`, `c_hr`, `o_days`, `address`, `image`, `date`) VALUES
(1, 6, 'Chinese', 'nthavern@mail.com', '3547854700', 'www.northstreettavern.com', '--Select your Hours--', '--Select your Hours--', '--Select your Days--', '\"The ultimate mood-lifter – Chinese food never disappoints!\"', '680745be5d5ec.jpg', '2025-04-22 07:31:10'),
(2, 7, 'South Indian', 'eataly@gmail.com', '0557426406', 'www.eataly.com', '--Select your Hours--', '--Select your Hours--', '--Select your Days--', '\"The crunch of dosa, the punch of chutney – unbeatable!\"', '680745881cd81.jpg', '2025-04-22 07:30:16'),
(3, 8, 'North Indian', 'nanxiangbao45@mail.com', '1458745855', 'www.nanxiangbao45.com', '--Select your Hours--', '--Select your Hours--', '--Select your Days--', '\"From street chaat to royal thalis – North India has it all!\"', '68074564af665.jpg', '2025-04-22 07:29:40'),
(4, 5, 'Maharashtra', 'hbg@mail.com', '6545687458', 'www.hbg.com', '--Select your Hours--', '--Select your Hours--', '--Select your Days--', '\"Authentic Maharashtrian flavors that hit the soul!\"', '6807453508c89.jpg', '2025-04-22 07:28:53');

-- --------------------------------------------------------

--
-- Table structure for table `res_category`
--

CREATE TABLE `res_category` (
  `c_id` int(11) NOT NULL,
  `c_name` varchar(222) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `res_category`
--

INSERT INTO `res_category` (`c_id`, `c_name`, `date`) VALUES
(5, 'Maharashtrian', '2025-04-22 07:26:36'),
(6, 'Chinese', '2025-04-22 07:26:40'),
(7, 'South Indian', '2025-04-22 07:26:44'),
(8, 'North Indian', '2025-04-22 07:26:48');

-- --------------------------------------------------------

--
-- Table structure for table `table_bookings`
--

CREATE TABLE `table_bookings` (
  `u_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `people` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `table_bookings`
--

INSERT INTO `table_bookings` (`u_id`, `name`, `email`, `phone`, `date`, `time`, `people`) VALUES
(3, 'Mayuri Dhongadi', 'mayuridhongadi@gmail.com', '09730693440', '2025-05-08', '17:16:00', 4),
(4, 'Mayuri Dhongadi', 'mayuridhongadi@gmail.com', '09730693440', '2025-05-08', '15:27:00', 4),
(5, 'nidhi thool', 'dco.2023.nsthool@bitwardha.ac.in', '09552298218', '2025-05-31', '16:45:00', 2),
(6, 'nidhi thool', 'dco.2023.nsthool@bitwardha.ac.in', '09552298218', '2025-05-31', '19:05:00', 3),
(9, 'nidhi thool', 'dco.2023.nsthool@bitwardha.ac.in', '09552298218', '0000-00-00', '00:00:00', 0),
(10, 'Mayuri Dhongadi', 'mayuridhongadi@gmail.com', '09730693440', '2025-05-08', '17:16:00', 4),
(11, 'Mayuri Dhongadi', 'mayuridhongadi@gmail.com', '09730693440', '2025-05-08', '16:26:00', 4),
(22, 'Mayuri Dhongadi', 'mayuridhongadi@gmail.com', '09730693440', '2025-06-08', '11:50:00', 4),
(23, 'Mayuri Dhongadi', 'mayuridhongadi@gmail.com', '09730693440', '2025-06-08', '15:44:00', 4),
(24, 'Mayuri Dhongadi', 'mayuridhongadi@gmail.com', '09730693440', '2025-06-08', '15:53:00', 4),
(25, 'Mayuri Dhongadi', 'mayuridhongadi@gmail.com', '09730693440', '2025-06-08', '01:30:00', 4),
(26, 'nidhi thool', 'dco.2023.nsthool@bitwardha.ac.in', '09552298218', '2025-06-12', '12:14:00', 2),
(27, 'nidhi thool', 'dco.2023.nsthool@bitwardha.ac.in', '09552298218', '2025-06-12', '12:29:00', 2),
(28, 'nidhi thool', 'dco.2023.nsthool@bitwardha.ac.in', '09552298218', '2025-06-12', '18:42:00', 2),
(29, 'nidhi thool', 'dco.2023.nsthool@bitwardha.ac.in', '09552298218', '2025-06-12', '12:55:00', 2),
(30, 'nidhi thool', 'dco.2023.nsthool@bitwardha.ac.in', '09552298218', '2025-06-12', '13:06:00', 2),
(31, 'Mayuri Dhongadi', 'mayuridhongadi@gmail.com', '09730693440', '2025-06-20', '17:08:00', 2),
(32, 'Mayuri Dhongadi', 'mayuridhongadi@gmail.com', '09730693440', '2025-05-11', '13:13:00', 4),
(33, 'aryan', 'mayuridhongadi@gmail.com', '09730693440', '2025-05-11', '16:10:00', 4),
(34, 'aryan', 'mayuridhongadi@gmail.com', '09730693440', '2025-05-11', '13:14:00', 4),
(35, 'ansh', 'dco.2023.mvdhongadi@bitwardha.ac.in', '45678', '2025-06-11', '16:15:00', 4),
(36, 'aryan', 'mayuridhongadi@gmail.com', '09730693440', '2025-06-09', '13:21:00', 4),
(37, 'aryan', 'mayuridhongadi@gmail.com', '09730693440', '2025-06-09', '13:47:00', 4),
(38, 'aryan', 'mayuridhongadi@gmail.com', '09730693440', '2025-06-09', '14:08:00', 4),
(39, 'nidhi thool', 'dco.2023.nsthool@bitwardha.ac.in', '09552298218', '2025-06-12', '18:42:00', 2),
(40, 'Mayuri Dhongadi', 'mayuridhongadi@gmail.com', '09730693440', '2025-06-26', '18:14:00', 4),
(41, 'ridhi', 'nidhithool@gmail.com', '09552298218', '2025-06-04', '14:29:00', 3),
(42, 'samyak', 'mayuridhongadi@gmail.com', '09730693440', '2025-06-26', '18:38:00', 4);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `u_id` int(11) NOT NULL,
  `username` varchar(222) NOT NULL,
  `f_name` varchar(222) NOT NULL,
  `l_name` varchar(222) NOT NULL,
  `email` varchar(222) NOT NULL,
  `phone` varchar(222) NOT NULL,
  `password` varchar(222) NOT NULL,
  `address` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`u_id`, `username`, `f_name`, `l_name`, `email`, `phone`, `password`, `address`, `status`, `date`) VALUES
(9, 'ansh', 'Ansh', 'Shivhare', 'anshshivhare9@gmail.com', '08793132994', 'a1f4ee8d6bff1f407a7d78d34b06b569', 'plot no: 434 mohota science collage road chandan nagar', 1, '2025-04-22 09:28:40'),
(10, 'Mayuri', 'Mayuri', 'Dhongadi', 'mayuridhongadi@gmail.com', '09730693440', 'e3e50a7f670fc69f7bb278a986a7847e', '', 1, '2025-04-25 05:38:40'),
(11, 'nidhi', 'nidhi', 'thool', 'mayuridhongadi@gmail1.com', '09730693440', '6fcc80da1cda148ae03f19c241f04997', 'anji', 1, '2025-05-07 08:11:35'),
(12, 'jane', 'Mayuri', 'Dhongadi', 'mayuridhongadi@gmail.com', '09730693440', 'f5bdefc62591e50d247b4b35cd1f542b', 'anji', 1, '2025-06-02 06:00:22'),
(13, '', 'mayuri', 'dhongadi', 'mayuridhongadi@gmail.com', '1234567890', '', 'anji', 1, '2025-05-30 08:37:32');

-- --------------------------------------------------------

--
-- Table structure for table `users_orders`
--

CREATE TABLE `users_orders` (
  `o_id` int(11) NOT NULL,
  `u_id` int(11) NOT NULL,
  `title` varchar(222) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` varchar(222) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users_orders`
--

INSERT INTO `users_orders` (`o_id`, `u_id`, `title`, `quantity`, `price`, `status`, `date`) VALUES
(34, 1, 'Dumplings', 1, 70.00, NULL, '2025-06-02 08:53:42'),
(35, 2, 'Choole Bhatura', 1, 70.00, NULL, '2025-06-02 08:53:55'),
(36, 12, 'Aloo paratha', 1, 50.00, NULL, '2025-06-02 06:43:09'),
(37, 12, 'Chicken', 1, 200.00, NULL, '2025-06-02 08:45:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`adm_id`);

--
-- Indexes for table `dishes`
--
ALTER TABLE `dishes`
  ADD PRIMARY KEY (`d_id`);

--
-- Indexes for table `remark`
--
ALTER TABLE `remark`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `restaurant`
--
ALTER TABLE `restaurant`
  ADD PRIMARY KEY (`rs_id`);

--
-- Indexes for table `res_category`
--
ALTER TABLE `res_category`
  ADD PRIMARY KEY (`c_id`);

--
-- Indexes for table `table_bookings`
--
ALTER TABLE `table_bookings`
  ADD PRIMARY KEY (`u_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`u_id`);

--
-- Indexes for table `users_orders`
--
ALTER TABLE `users_orders`
  ADD PRIMARY KEY (`o_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `adm_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `dishes`
--
ALTER TABLE `dishes`
  MODIFY `d_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `remark`
--
ALTER TABLE `remark`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `restaurant`
--
ALTER TABLE `restaurant`
  MODIFY `rs_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `res_category`
--
ALTER TABLE `res_category`
  MODIFY `c_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `table_bookings`
--
ALTER TABLE `table_bookings`
  MODIFY `u_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `u_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users_orders`
--
ALTER TABLE `users_orders`
  MODIFY `o_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
