-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 24, 2024 at 10:38 AM
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
-- Database: `todo`
--

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `task` varchar(255) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('incomplete','pending','complete') NOT NULL DEFAULT 'incomplete'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `task`, `file_name`, `file_type`, `created_at`, `status`) VALUES
(30, 'TASK1', NULL, NULL, '2024-04-24 05:34:01', 'complete'),
(31, 'finish this', NULL, NULL, '2024-04-24 05:34:20', 'complete'),
(32, 'download and watch this', 'Screenshot 2024-04-24 001937.png', 'image/png', '2024-04-24 05:34:49', 'incomplete'),
(33, 'finish this module', NULL, NULL, '2024-04-24 05:35:14', 'complete'),
(34, 'answer download and anwers this', 'Screenshot 2024-04-24 001937.png', 'image/png', '2024-04-24 06:07:24', 'incomplete'),
(35, 'ads', 'Recording 2024-04-24 002136.mp4', 'video/mp4', '2024-04-24 06:29:34', 'complete'),
(36, 'Your default text here', '', '', '2024-04-24 07:52:03', 'pending'),
(37, 'TITLE', 'Screenshot 2024-04-24 025517.png', 'image/png', '2024-04-24 08:09:59', 'pending');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
