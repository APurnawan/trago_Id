-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 28, 2025 at 12:52 PM
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
-- Database: `employee_monitoring`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('Field Staff','Manager') NOT NULL,
  `parent_comment_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `log_id`, `user_id`, `role`, `parent_comment_id`, `content`, `created_at`) VALUES
(10, 8, 2, 'Field Staff', NULL, 'Test command ke manager', '2025-06-26 05:00:02'),
(11, 8, 1, 'Manager', 10, 'Test pesan dari manager Lisa', '2025-06-26 05:03:18');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `employee_code` varchar(20) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `employee_code`, `name`, `date_of_birth`, `gender`, `address`, `phone_number`, `position`, `join_date`, `photo`) VALUES
(1, 'PP001', 'Lisa', '2025-06-21', 'Female', 'Jl. Pahlawan 1 Surabaya', '0811111111', 'Managar', '2025-06-21', 'Lisa.jpg'),
(2, 'PP002', 'Dany Pratama', '2018-06-07', 'Male', 'JL. Arjuna Malang', '086444444', NULL, '2025-05-07', 'Dani_Pratama.JPG'),
(4, 'PP004', 'Jaya', '2025-06-25', 'Male', 'Jl. Merdeka - Malang', '08566666', NULL, '2025-06-25', 'Dani_Pratama.JPG'),
(5, 'PP005', 'Dewi', '2025-06-05', 'Female', 'Jl. Mawar, Malang', '08764444355', NULL, '2025-06-26', 'DEWI.JPG');

-- --------------------------------------------------------

--
-- Table structure for table `field_logs`
--

CREATE TABLE `field_logs` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `status` enum('Onsite','Offsite','Leave') DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `log_date` date DEFAULT NULL,
  `log_time` time DEFAULT NULL,
  `latitude` decimal(9,6) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL,
  `photo_url` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `field_logs`
--

INSERT INTO `field_logs` (`id`, `employee_id`, `status`, `project_id`, `log_date`, `log_time`, `latitude`, `longitude`, `photo_url`, `notes`, `created_at`) VALUES
(8, 2, 'Onsite', 3, '2025-06-26', '14:00:00', -4.622939, 55.460354, 'assets/uploads/1750913986_istockphoto-1442849073-612x612.jpg', 'Test', '2025-06-26 04:59:46'),
(9, 4, 'Onsite', 1, '2025-06-26', '12:12:00', -4.622939, 55.460354, 'assets/uploads/1750924573_Capture1JPG.JPG', 'Inspeksi Menara Pemancar di Dudun Londo - Waru, Sidoarjo', '2025-06-26 07:56:13'),
(10, 5, 'Offsite', 4, '2025-06-26', '12:12:00', -4.622939, 55.460354, 'assets/uploads/1750925667_Capture1JPG.JPG', 'Office General', '2025-06-26 08:14:27'),
(11, 4, 'Leave', 5, '2025-06-26', '12:12:00', -4.622939, 55.460354, 'assets/uploads/1750927617_Utm-zones.jpg', '', '2025-06-26 08:46:57');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `no_project` varchar(50) DEFAULT NULL,
  `descripsi_project` text DEFAULT NULL,
  `location_project` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `no_project`, `descripsi_project`, `location_project`) VALUES
(1, 'R001', 'Inspeksi Menara BTS dan Kabel', 'Kenjeran, Surabaya'),
(3, 'R002', 'Perbaikan Listrik ', 'Surabaya'),
(4, 'Ofiice', 'Office Job', 'Surabaya'),
(5, 'Leave', 'Leave', 'Home');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('Field Staff','Office Staff','Manager') DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `employee_id`) VALUES
(1, 'lisa@gmail.com', '$2y$10$/rkohepu/mTuSnSeuksqXe/6Hj0MNP28keXTu/kMdyiwWloUKo85m', 'Manager', 1),
(2, 'danipratama@gmail.com', '$2y$10$uOJLktSxdYzppiZVOq08VeMFYIbZ7GL8A7COb2gr.v7iHvItvWi/u', 'Field Staff', 2),
(4, 'jaya@gmail.com', '$2y$10$GsxQ.wjTs7QFdvRJLU/AtekQMtFyewOrKq6QQX1m31K1xU3Pcrpd2', 'Field Staff', 4),
(5, 'dewi@gmail.com', '$2y$10$WD.Hm7aHrM3b/y9GrIMceO41jHOHYDEY9LvNcY7Vdgj2DYtszXmWu', 'Office Staff', 5);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `log_id` (`log_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_comment_id` (`parent_comment_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_code` (`employee_code`);

--
-- Indexes for table `field_logs`
--
ALTER TABLE `field_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `employee_id` (`employee_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `field_logs`
--
ALTER TABLE `field_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`log_id`) REFERENCES `field_logs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`parent_comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `field_logs`
--
ALTER TABLE `field_logs`
  ADD CONSTRAINT `field_logs_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `field_logs_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
