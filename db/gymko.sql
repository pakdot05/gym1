-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 31, 2024 at 01:53 AM
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
-- Database: `gymko`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_cred`
--

CREATE TABLE `admin_cred` (
  `admin_id` int(11) NOT NULL,
  `admin_name` varchar(50) NOT NULL,
  `admin_pass` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_cred`
--

INSERT INTO `admin_cred` (`admin_id`, `admin_name`, `admin_pass`) VALUES
(1, 'admin', '12345'),
(2, 'shan', 'shan');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `phonenum` varchar(50) NOT NULL,
  `note` varchar(150) NOT NULL,
  `trainor_name` varchar(50) NOT NULL,
  `timeslot` varchar(50) NOT NULL,
  `status` int(20) NOT NULL DEFAULT 0,
  `trainor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `name`, `email`, `date`, `phonenum`, `note`, `trainor_name`, `timeslot`, `status`, `trainor_id`) VALUES
(253, 53, 'Jefferson tana', 'kenshin2@gmail.com', '0000-00-00', '639551351741', '', 'MassFuel', '13:00PM-13:30PM', 0, NULL),
(255, 55, 'Jefferson tana', 'jeffersontana071@gmail.com', '0000-00-00', '639654321111', '', 'Jefferson Tana', '14:00PM-14:30PM', 0, NULL),
(256, 56, 'Juan', 'jeffersontana71@gmail.com', '2024-10-30', '639543212112', '', 'Jefferson Tana', '14:00PM-14:30PM', 0, NULL),
(257, 54, 'Jefferson tana', 'jeffersonjpoy09551351741@gmail.com', '2024-10-30', '639551351701', '', 'Jefferson Tana', '14:30PM-15:00PM', 0, NULL),
(259, 58, 'Jefferson', 'geafitnessg@gmail.com', '2024-10-31', '639987654333', '', 'MassFuel', '09:00AM-09:30AM', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `carousel`
--

CREATE TABLE `carousel` (
  `carousel_id` int(50) NOT NULL,
  `image` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carousel`
--

INSERT INTO `carousel` (`carousel_id`, `image`) VALUES
(25, 'IMG_27452.webp'),
(31, 'IMG_80437.jpeg'),
(32, 'IMG_91293.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `contact_details`
--

CREATE TABLE `contact_details` (
  `contact_id` int(11) NOT NULL,
  `address` varchar(50) NOT NULL,
  `gmap` varchar(100) NOT NULL,
  `pn1` bigint(20) NOT NULL,
  `pn2` bigint(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `twt` varchar(100) NOT NULL,
  `ig` varchar(100) NOT NULL,
  `fb` varchar(100) NOT NULL,
  `iframe` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_details`
--

INSERT INTO `contact_details` (`contact_id`, `address`, `gmap`, `pn1`, `pn2`, `email`, `twt`, `ig`, `fb`, `iframe`) VALUES
(1, 'Gea fitness Ward Ill, Minglanilla, Cebu.', 'https://maps.app.goo.gl/YBSpfeaNkwX7sqRN7', 639507628230, 639551351741, 'minglanillageafitnessgym@gmail.com', 'https://twitter.com/geafitness', 'https://www.instagram.com/fitnes_gym__ig/', 'https://www.facebook.com/GEAFitnessGym', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3926.3210581520524!2d123.794304!3d10.2458327!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33a99dad5dd3f5dd:0x5873a2b4e671e9d8!2sGEA Fitness!5e0!3m2!1sen!2sph!4v1719042371849!5m2!1sen!2sph');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_status` varchar(50) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `pid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `product_name`, `quantity`, `price`, `total_price`, `payment_method`, `payment_status`, `user_name`, `user_email`, `contact_number`, `address`, `created_at`, `pid`) VALUES
(37, 34, 'HerbalLife', 1, 50.00, 50.00, 'cash', 'Paid', 'JEFF', 'marckasayan0008@gmail.com', '639507628230', 'Unit C, Realfa Bldg, San Isidro Road, Talisay, 6045 Cebu', '2024-10-17 05:55:22', 0),
(38, 34, 'HerbalLife', 1, 50.00, 50.00, 'E-Wallet', 'Paid', 'JEFF', 'marckasayan0008@gmail.com', '639507628230', 'Unit C, Realfa Bldg, San Isidro Road, Talisay, 6045 Cebu', '2024-10-17 05:56:10', 6),
(39, 34, 'HerbalLife', 1, 50.00, 50.00, 'E-Wallet', 'Paid', 'JEFF', 'marckasayan0008@gmail.com', '639507628230', 'Unit C, Realfa Bldg, San Isidro Road, Talisay, 6045 Cebu', '2024-10-17 05:56:12', 6),
(40, 34, 'HerbalLife', 1, 50.00, 50.00, 'E-Wallet', 'Paid', 'JEFF', 'marckasayan0008@gmail.com', '639507628230', 'Unit C, Realfa Bldg, San Isidro Road, Talisay, 6045 Cebu', '2024-10-17 06:52:57', 6),
(41, 34, 'MassFuel', 1, 100.00, 100.00, 'cash', 'Paid', 'JEFF', 'marckasayan0008@gmail.com', '639507628230', 'Unit C, Realfa Bldg, San Isidro Road, Talisay, 6045 Cebu', '2024-10-17 06:55:08', 0),
(42, 34, 'MassFuel', 1, 100.00, 100.00, 'cash', 'Paid', 'JEFF', 'marckasayan0008@gmail.com', '639507628230', 'Unit C, Realfa Bldg, San Isidro Road, Talisay, 6045 Cebu', '2024-10-17 06:55:45', 0),
(43, 34, 'HerbalLife', 1, 50.00, 50.00, 'cash', 'pending', 'JEFF', 'marckasayan0008@gmail.com', '639507628230', 'Unit C, Realfa Bldg, San Isidro Road, Talisay, 6045 Cebu', '2024-10-17 07:15:21', 0),
(44, 34, 'HerbalLife', 1, 50.00, 50.00, 'cash', 'pending', 'JEFF', 'marckasayan0008@gmail.com', '639507628230', 'Unit C, Realfa Bldg, San Isidro Road, Talisay, 6045 Cebu', '2024-10-17 07:16:25', 0),
(45, 36, 'MassFuel', 1, 100.00, 100.00, 'E-Wallet', 'Paid', 'juan delacruz', 'marckasayan00011@gmail.com', '639507628230', 'Unit C, Realfa Bldg, San Isidro Road, Talisay, 6045 Cebu\r\n0258,0076,0607', '2024-10-17 09:33:45', 7),
(46, 36, 'HerbalLife', 10, 50.00, 500.00, 'cash', 'Paid', 'juan delacruz', 'marckasayan00011@gmail.com', '639507628230', 'Unit C, Realfa Bldg, San Isidro Road, Talisay, 6045 Cebu\r\n0258,0076,0607', '2024-10-17 09:35:11', 0),
(47, 36, 'MassFuel', 1, 100.00, 100.00, 'E-Wallet', 'Paid', 'juan delacruz', 'marckasayan00011@gmail.com', '639507628230', 'Unit C, Realfa Bldg, San Isidro Road, Talisay, 6045 Cebu\r\n0258,0076,0607', '2024-10-17 09:39:52', 7),
(48, 36, 'MassFuel', 1, 100.00, 100.00, 'E-Wallet', 'Paid', 'juan delacruz', 'marckasayan00011@gmail.com', '639507628230', 'Unit C, Realfa Bldg, San Isidro Road, Talisay, 6045 Cebu\r\n0258,0076,0607', '2024-10-17 11:16:25', 7),
(49, 36, 'HerbalLifeNutrition', 1, 200.00, 200.00, 'E-Wallet', 'Paid', 'juan delacruz', 'marckasayan00011@gmail.com', '639507628230', 'Unit C, Realfa Bldg, San Isidro Road, Talisay, 6045 Cebu\r\n0258,0076,0607', '2024-10-17 11:32:48', 8),
(50, 44, 'chk', 1, 23.00, 23.00, 'E-Wallet', 'Paid', 'vdfg', 'geafitness06@gmail.com', '639614765538', '0085,Tagjaguimit,City of Naga, Cebu', '2024-10-23 05:30:03', 23),
(51, 44, 'chk', 1, 23.00, 23.00, 'cash', 'Paid', 'vdfg', 'geafitness06@gmail.com', '639614765538', '0085,Tagjaguimit,City of Naga, Cebu', '2024-10-23 05:33:20', 0),
(52, 44, 'chk', 1, 23.00, 23.00, 'E-Wallet', 'Paid', 'vdfg', 'geafitness06@gmail.com', '639614765538', '0085,Tagjaguimit,City of Naga, Cebu', '2024-10-23 05:33:49', 23),
(53, 44, 'HerbalLifeNutrition', 1, 200.00, 200.00, 'cash', 'Paid', 'vdfg', 'geafitness06@gmail.com', '639614765538', '0085,Tagjaguimit,City of Naga, Cebu', '2024-10-23 06:53:31', 0),
(54, 44, 'MassFuel', 1, 100.00, 100.00, 'E-Wallet', 'Paid', 'vdfg', 'geafitness06@gmail.com', '639614765538', '0085,Tagjaguimit,City of Naga, Cebu', '2024-10-23 06:54:14', 7),
(55, 44, 'chk', 1, 23.00, 23.00, 'cash', 'pending', 'vdfg', 'geafitness06@gmail.com', '639614765538', '0085,Tagjaguimit,City of Naga, Cebu', '2024-10-23 07:18:03', 0),
(56, 47, 'MassFuel', 1, 100.00, 100.00, 'E-Wallet', 'Paid', 'Jefferson Tana', 'jenellejoycetana@gmail.com', '639876543111', 'Unit C, Realfa Bldg, San Isidro Road, Talisay, 6045 Cebu', '2024-10-26 01:25:10', 7);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `unit` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `quantity`, `price`, `image`, `unit`) VALUES
(7, 'MassFuel', 95, 100.00, 'dfd3f6db0ead07ec259b573261e673b7.jpg', ''),
(24, 'maxman', 12, 12.00, 'dfd3f6db0ead07ec259b573261e673b7.jpg', '12gram');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `settings_id` int(11) NOT NULL,
  `site_title` varchar(50) NOT NULL,
  `site_about` varchar(500) NOT NULL,
  `shutdown` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`settings_id`, `site_title`, `site_about`, `shutdown`) VALUES
(1, 'Gea Fitness Gym', 'The gym is a place where physical strength is forged and mental toughness is built.', 0);

-- --------------------------------------------------------

--
-- Table structure for table `specialty`
--

CREATE TABLE `specialty` (
  `specialty_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `specialty`
--

INSERT INTO `specialty` (`specialty_id`, `name`, `description`) VALUES
(38, 'Group Classes', 'We offer classes for yoga, spin, Zumba, Circuit Training, MX4 and more.'),
(39, 'Personal Trainers', 'Maximize your workout with the help of our personal trainers.'),
(40, '24/7 Access', 'Workout when the time is right for you with 24/7 access.'),
(41, 'Strength Training', 'Free Weights: Dumbbells, barbells, kettlebells\r\nResistance Machines: Leg press, chest press, lat pulldown'),
(42, 'Cardio Equipment', 'Machines: Treadmills, ellipticals, stationary bikes, rowing machines'),
(43, 'Wellness Services', 'Nutrition Counseling: Personalized meal plans and dietary advice\r\nPhysical Therapy: Rehabilitation services and injury prevention'),
(44, 'Recovery Services', 'Massage Therapy: To help with muscle recovery and relaxation\r\nSauna/Steam Room: For relaxation and muscle recovery');

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `plan` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `interval` varchar(20) NOT NULL,
  `description` text NOT NULL,
  `payment_id` varchar(255) NOT NULL,
  `payment_status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `user_id`, `name`, `email`, `plan`, `price`, `interval`, `description`, `payment_id`, `payment_status`, `created_at`, `end_date`) VALUES
(48, 0, 'geafitnessgym', 'minglanillageafitnessgym@gmail.com', 'day', 35.00, 'none', '0', 'WALK-IN', 'complete', '2024-10-10 16:00:00', '2024-10-12'),
(49, 35, 'juan', 'minglanillageagym@gmail.com', 'Weekly', 245.00, 'week', 'Unlock a week of unlimited access to our gym, with additional perks like diet plans and group sessions.', '670f0de547e92', 'pending', '2024-10-16 00:50:46', '2024-10-23'),
(50, 34, 'JEFF', 'marckasayan0008@gmail.com', 'Weekly', 245.00, 'week', 'Unlock a week of unlimited access to our gym, with additional perks like diet plans and group sessions.', '670f0e3f4178d', 'pending', '2024-10-16 00:52:15', '2024-10-23'),
(52, 32, 'jefferson', 'Kenshin2@gmail.com', 'Weekly', 245.00, 'week', 'Unlock a week of unlimited access to our gym, with additional perks like diet plans and group sessions.', '6710b3b5ec700', 'pending', '2024-10-17 06:50:31', '2024-10-24'),
(112, 49, 'Jefferson', 'jeffersontana071@gmail.com', 'Weekly', 245.00, 'week', 'Unlock a week of unlimited access to our gym, with additional perks like diet plans and group sessions.', '671f9a960c310', 'pending', '2024-10-28 14:07:19', '2024-11-04'),
(113, 51, 'Jefferson', 'minglanillageafitnessgym@gmail.com', 'Weekly', 245.00, 'week', 'Unlock a week of unlimited access to our gym, with additional perks like diet plans and group sessions.', '67204c5c57ba8', 'success', '2024-10-29 02:45:49', '2024-11-05');

-- --------------------------------------------------------

--
-- Table structure for table `team_details`
--

CREATE TABLE `team_details` (
  `team_id` int(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `picture` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `team_details`
--

INSERT INTO `team_details` (`team_id`, `name`, `picture`) VALUES
(25, 'juan', 'IMG_11354.webp'),
(26, 'jean', 'IMG_27053.webp'),
(27, 'jefferson', 'IMG_63389.webp');

-- --------------------------------------------------------

--
-- Table structure for table `trainor`
--

CREATE TABLE `trainor` (
  `trainor_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `info` varchar(350) NOT NULL,
  `price` int(50) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `removed` int(11) NOT NULL DEFAULT 0,
  `address` varchar(255) DEFAULT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainor`
--

INSERT INTO `trainor` (`trainor_id`, `name`, `info`, `price`, `status`, `removed`, `address`, `contact_no`, `email`) VALUES
(59, 'Ana Imenel Taña', 'tgdfxvdfv', 0, 1, 1, NULL, NULL, NULL),
(61, 'MassFuel', 'qsSsdadvxbc vdszv', 0, 1, 0, 'Unit C, Realfa Bldg, San Isidro Road, Talisay, 6045 Cebu', '639507628230', 'geafitnessg@gmail.com'),
(62, 'Jefferson Tana', 'best trainor of all time', 0, 1, 0, '0085,Tagjaguimit,City of Naga, Cebu', '09603496968', 'geafitnessg@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `trainor_images`
--

CREATE TABLE `trainor_images` (
  `sr_no` int(11) NOT NULL,
  `trainor_id` int(11) NOT NULL,
  `image` varchar(150) NOT NULL,
  `thumb` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainor_images`
--

INSERT INTO `trainor_images` (`sr_no`, `trainor_id`, `image`, `thumb`) VALUES
(171, 59, 'IMG_55793.jpeg', 1),
(180, 61, 'IMG_79629.jpg', 1),
(188, 62, 'IMG_13321.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `trainor_specialty`
--

CREATE TABLE `trainor_specialty` (
  `ds_id` int(11) NOT NULL,
  `doc_id` int(11) NOT NULL,
  `specialty_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainor_specialty`
--

INSERT INTO `trainor_specialty` (`ds_id`, `doc_id`, `specialty_id`) VALUES
(619, 62, 39),
(620, 62, 40),
(621, 62, 41),
(622, 62, 42),
(623, 62, 43),
(624, 62, 44),
(625, 61, 38),
(626, 61, 40);

-- --------------------------------------------------------

--
-- Table structure for table `user_cred`
--

CREATE TABLE `user_cred` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `dob` date NOT NULL,
  `phonenum` varchar(50) NOT NULL,
  `address` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL,
  `profile` varchar(100) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `datentime` datetime NOT NULL DEFAULT current_timestamp(),
  `appointment_status` varchar(20) NOT NULL DEFAULT 'available',
  `qr_code` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_cred`
--

INSERT INTO `user_cred` (`user_id`, `name`, `email`, `dob`, `phonenum`, `address`, `password`, `profile`, `status`, `datentime`, `appointment_status`, `qr_code`) VALUES
(53, 'Jefferson tana', 'kenshin2@gmail.com', '0002-02-02', '639551351741', 'Naga,Cebu', '$2y$10$5R9hrmOSwWPLyblaDDCz4e945qVJlwKxuu6PcMmPJnP3LmL2CZUIS', 'IMG_73556.jpeg', 1, '2024-10-30 12:32:00', 'pending', ''),
(54, 'Jefferson tana', 'jeffersonjpoy09551351741@gmail.com', '2002-02-01', '639551351701', 'Naga,Cebu', '$2y$10$uS8OMfIeog.bq22ZQ.UtcOstlcdkRAH/1GpNuB90m9FpRVooS4ORG', 'IMG_42951.jpeg', 1, '2024-10-30 12:35:03', 'pending', ''),
(55, 'Jefferson tana', 'jeffersontana071@gmail.com', '2002-02-02', '639654321111', 'Naga,Cebu', '$2y$10$ItnUd7s1wTsu.meBLy6KjuET/Aq5vkDKCm6is9ui1AvJgpMVkNldy', 'IMG_88975.jpeg', 1, '2024-10-30 12:49:57', 'pending', ''),
(56, 'Juan', 'jeffersontana71@gmail.com', '2002-02-02', '639543212112', 'Naga,Cebu', '$2y$10$JLjR7nT0ebzunuI6F2Bp5.JGNHAt6zSoVpakkP8aflBpSLTQdWvzK', 'IMG_43399.jpeg', 1, '2024-10-30 13:26:18', 'pending', ''),
(57, 'Jefferson tana', 'juandelacruz05@gmail.com', '2002-02-02', '639876543215', 'Naga,Cebu', '$2y$10$zZh5CRG.syUefgcITGfWKe/AK98VlWRAzVEhOqnPdIiaN0WPTewf2', 'IMG_91218.jpeg', 1, '2024-10-30 13:40:21', 'available', ''),
(58, 'Jefferson', 'geafitnessg@gmail.com', '2002-02-22', '639987654333', 'Unit C, Realfa Bldg, San Isidro Road, Talisay, 6045 Cebu', '$2y$10$ZruyvCZ5Z1qllRhcrJvOpes6IzutquD8CklCOWoK3QeKzsk/C6CiK', '434023109_742951687604492_885505134420779832_n.jpg', 1, '2024-10-31 07:39:02', 'pending', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_cred`
--
ALTER TABLE `admin_cred`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `carousel`
--
ALTER TABLE `carousel`
  ADD PRIMARY KEY (`carousel_id`);

--
-- Indexes for table `contact_details`
--
ALTER TABLE `contact_details`
  ADD PRIMARY KEY (`contact_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`settings_id`);

--
-- Indexes for table `specialty`
--
ALTER TABLE `specialty`
  ADD PRIMARY KEY (`specialty_id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `team_details`
--
ALTER TABLE `team_details`
  ADD PRIMARY KEY (`team_id`);

--
-- Indexes for table `trainor`
--
ALTER TABLE `trainor`
  ADD PRIMARY KEY (`trainor_id`);

--
-- Indexes for table `trainor_images`
--
ALTER TABLE `trainor_images`
  ADD PRIMARY KEY (`sr_no`),
  ADD KEY `trainor_id` (`trainor_id`);

--
-- Indexes for table `trainor_specialty`
--
ALTER TABLE `trainor_specialty`
  ADD PRIMARY KEY (`ds_id`),
  ADD KEY `specialty_id` (`specialty_id`),
  ADD KEY `doc_id` (`doc_id`);

--
-- Indexes for table `user_cred`
--
ALTER TABLE `user_cred`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_cred`
--
ALTER TABLE `admin_cred`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=260;

--
-- AUTO_INCREMENT for table `carousel`
--
ALTER TABLE `carousel`
  MODIFY `carousel_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `contact_details`
--
ALTER TABLE `contact_details`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `settings_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `specialty`
--
ALTER TABLE `specialty`
  MODIFY `specialty_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `team_details`
--
ALTER TABLE `team_details`
  MODIFY `team_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `trainor`
--
ALTER TABLE `trainor`
  MODIFY `trainor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `trainor_images`
--
ALTER TABLE `trainor_images`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=189;

--
-- AUTO_INCREMENT for table `trainor_specialty`
--
ALTER TABLE `trainor_specialty`
  MODIFY `ds_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=627;

--
-- AUTO_INCREMENT for table `user_cred`
--
ALTER TABLE `user_cred`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `trainor_images`
--
ALTER TABLE `trainor_images`
  ADD CONSTRAINT `trainor_images_ibfk_1` FOREIGN KEY (`trainor_id`) REFERENCES `trainor` (`trainor_id`);

--
-- Constraints for table `trainor_specialty`
--
ALTER TABLE `trainor_specialty`
  ADD CONSTRAINT `doc_id` FOREIGN KEY (`doc_id`) REFERENCES `trainor` (`trainor_id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `specialty_id` FOREIGN KEY (`specialty_id`) REFERENCES `specialty` (`specialty_id`) ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
