-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 26, 2025 at 03:06 AM
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
-- Database: `event_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `date` date NOT NULL,
  `time` time DEFAULT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `name`, `description`, `category`, `date`, `time`, `venue`, `created_by`) VALUES
(1, 'Music Festival', 'A grand outdoor concert with multiple artists.', 'Concert', '2025-03-20', '18:00:00', NULL, NULL),
(2, 'Tech Expo', 'Showcasing the latest technology and gadgets.', 'Exhibition', '2025-04-10', '10:00:00', NULL, NULL),
(3, 'Art Fair', 'An exhibition of modern and classic artworks.', 'Arts', '2025-05-22', '09:30:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `seat_number` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `event_id`, `user_id`, `category`, `seat_number`, `price`) VALUES
(1, 1, 1, 'VIP', 1, 500.00),
(2, 1, 2, 'VIP', 5, 500.00),
(3, 1, 3, 'Regular', 6, 250.00),
(4, 1, 3, 'Regular', 16, 250.00),
(5, 1, 3, 'VIP', 2, 500.00),
(6, 1, 3, 'VIP', 3, 500.00),
(7, 1, 3, 'VIP', 4, 500.00),
(8, 1, 3, 'VIP', 7, 500.00),
(9, 1, 3, 'VIP', 8, 500.00),
(10, 1, 3, 'VIP', 9, 500.00),
(11, 2, 3, 'VIP', 1, 500.00),
(12, 2, 3, 'VIP', 2, 500.00),
(13, 1, 3, 'VIP', 10, 500.00),
(14, 1, 3, 'VIP', 28, 500.00),
(15, 1, 3, 'VIP', 11, 500.00),
(16, 1, 3, 'VIP', 12, 500.00),
(17, 1, 3, 'VIP', 13, 500.00),
(18, 1, 3, 'VIP', 14, 500.00),
(19, 1, 3, 'VIP', 15, 500.00),
(20, 1, 3, 'VIP', 17, 500.00),
(21, 1, 3, 'VIP', 18, 500.00),
(22, 1, 3, 'VIP', 19, 500.00),
(26, 1, 3, 'Regular', 23, 250.00),
(27, 1, 3, 'VIP', 24, 500.00),
(28, 1, 3, 'VIP', 25, 500.00),
(29, 1, 3, 'VIP', 26, 500.00),
(30, 1, 3, 'VIP', 27, 500.00),
(31, 1, 3, 'VIP', 29, 500.00),
(32, 1, 3, 'VIP', 30, 500.00),
(33, 1, 3, 'VIP', 31, 500.00),
(34, 2, 3, 'VIP', 3, 500.00),
(35, 2, 3, 'VIP', 4, 500.00),
(36, 1, 3, 'VIP', 32, 500.00),
(37, 2, 3, 'Regular', 5, 250.00),
(38, 1, 3, 'VIP', 33, 500.00),
(39, 1, 3, 'VIP', 34, 500.00),
(40, 1, 3, 'VIP', 35, 500.00),
(41, 1, 3, 'VIP', 36, 500.00),
(42, 1, 3, 'VIP', 37, 500.00),
(43, 1, 3, 'VIP', 38, 500.00),
(44, 1, 3, 'VIP', 39, 500.00),
(45, 3, 3, 'VIP', 1, 500.00),
(46, 1, 3, 'VIP', 40, 500.00),
(47, 1, 3, 'VIP', 41, 500.00),
(48, 1, 3, 'VIP', 42, 500.00),
(49, 1, 3, 'VIP', 43, 500.00),
(50, 1, 3, 'VIP', 44, 500.00),
(51, 1, 3, 'VIP', 45, 500.00),
(52, 1, 3, 'VIP', 46, 500.00),
(53, 1, 3, 'VIP', 47, 500.00),
(54, 1, 3, 'VIP', 48, 500.00),
(55, 1, 3, 'VIP', 49, 500.00),
(56, 1, 3, 'VIP', 50, 500.00),
(57, 2, 3, 'VIP', 6, 500.00),
(58, 2, 3, 'VIP', 7, 500.00),
(59, 1, 3, 'VIP', 21, 500.00),
(60, 2, 3, 'VIP', 8, 500.00),
(61, 1, 3, 'VIP', 20, 500.00),
(62, 2, 3, 'VIP', 9, 500.00),
(63, 1, 3, 'VIP', 22, 500.00),
(64, 3, 3, 'VIP', 2, 500.00),
(65, 2, 3, 'VIP', 10, 500.00),
(66, 2, 3, 'VIP', 26, 500.00),
(67, 2, 3, 'VIP', 11, 500.00),
(68, 2, 3, 'VIP', 12, 500.00),
(69, 2, 3, 'VIP', 13, 500.00),
(70, 2, 3, 'VIP', 14, 500.00),
(71, 2, 3, 'VIP', 15, 500.00),
(72, 2, 3, 'VIP', 16, 500.00),
(73, 2, 3, 'VIP', 17, 500.00),
(74, 2, 3, 'VIP', 18, 500.00),
(75, 2, 3, 'VIP', 19, 500.00),
(76, 2, 3, 'VIP', 20, 500.00),
(77, 2, 3, 'VIP', 21, 500.00),
(78, 2, 3, 'VIP', 22, 500.00),
(79, 3, 3, 'VIP', 3, 500.00),
(80, 3, 3, 'VIP', 4, 500.00),
(81, 2, 3, 'VIP', 23, 500.00),
(83, 2, 3, 'VIP', 24, 500.00),
(84, 3, 3, 'VIP', 5, 500.00),
(85, 2, 3, 'VIP', 25, 500.00),
(86, 3, 3, 'VIP', 6, 500.00),
(87, 2, 3, 'VIP', 27, 500.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `failed_attempts` int(11) DEFAULT 0,
  `lockout_time` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `failed_attempts`, `lockout_time`) VALUES
(1, 'Joshua', 'joshuag2@gmail.com', '$2y$10$iUW0S3gs7HHl3gmzDn2D0.7YxagSWN4Dma731AwsWR6zptkwq5Eda', 'admin', '2025-02-21 14:45:02', 0, NULL),
(2, 'Ash', 'ash22@gmail.com', '$2y$10$7r/BrldlNn9a90CsedhCnOlef4ww/90MI25c78v/WIDEZvRO0ZP76', 'user', '2025-02-21 15:48:13', 0, NULL),
(3, 'Thea', 'theanery3@gmail.com', '$2y$10$mh2h3zXZZBSw6bylYMTUjOH1kauJh9OETIvAaxgpd4BHmNosNErM2', 'user', '2025-02-23 04:42:42', 1, NULL),
(12, 'Thea', 'theane*ry3@gmail.com', '$2y$10$SpeXVy13jeYLs08n/idA3OjKFrZ7jc16x0bzTFJIZuGumW6Hq/EsC', 'user', '2025-02-25 07:54:56', 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
