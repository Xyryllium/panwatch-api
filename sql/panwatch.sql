-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 21, 2020 at 10:42 AM
-- Server version: 10.1.32-MariaDB
-- PHP Version: 7.2.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `panwatch`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_records`
--

CREATE TABLE `contact_records` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `dateContacted` datetime NOT NULL,
  `timeContacted` datetime NOT NULL,
  `typeId` int(11) NOT NULL,
  `duration` time NOT NULL,
  `contactInfo` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `hasFacemask` tinyint(1) NOT NULL,
  `hasFaceshield` tinyint(1) NOT NULL,
  `hasSocialDistancing` tinyint(1) NOT NULL DEFAULT '0',
  `hasTemperatureCheck` tinyint(1) NOT NULL DEFAULT '0',
  `attendees` varchar(191) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `contact_records`
--

INSERT INTO `contact_records` (`id`, `userId`, `name`, `location`, `dateContacted`, `timeContacted`, `typeId`, `duration`, `contactInfo`, `address`, `hasFacemask`, `hasFaceshield`, `hasSocialDistancing`, `hasTemperatureCheck`, `attendees`) VALUES
(1, 1, 'Pedro Penduko', 'Faith Tanuan Batangas', '2020-08-17 00:00:00', '2020-08-17 12:09:57', 1, '00:50:00', '09999102903', 'Tanauan Batngas', 1, 0, 0, 0, NULL),
(2, 1, 'Pedro Penduko', 'Faith Tanuan Batangas', '2020-08-17 00:00:00', '2020-08-17 12:09:57', 2, '00:50:00', '09999102903', '', 1, 0, 0, 1, NULL),
(3, 1, 'Pedro Penduko', 'Faith Tanuan Batangas', '2020-08-17 00:00:00', '2020-08-17 12:09:57', 3, '00:50:00', '09999102903', '', 1, 0, 0, 1, 'John Kenneth Furog'),
(4, 1, 'Pedro Penduko', 'Faith Tanuan Batangas', '2020-08-17 00:00:00', '2020-08-17 12:09:57', 3, '00:50:00', '09999102903', '', 1, 0, 0, 1, 'Juan dela Cruz');

-- --------------------------------------------------------

--
-- Table structure for table `contact_type`
--

CREATE TABLE `contact_type` (
  `id` int(11) NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `contact_type`
--

INSERT INTO `contact_type` (`id`, `type`) VALUES
(1, 'person'),
(2, 'establishment'),
(3, 'event');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `mobileNumber` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `avatar` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `name`, `mobileNumber`, `address`, `avatar`, `created_at`) VALUES
(1, 'jd@email.com', '123', 'JD', '09826471012', 'San Pablo, Laguna', 'https://res.cloudinary.com/xrlrnz/image/upload/v1597997944/giwnhn27n4nl8sjbnsoa.png', '2020-08-21 08:38:20'),
(2, 'jdc@email.com', 'i2VMaMkx', 'juan dela cruz', '09992010192', 'barangay Aplaya Santa Rosa Laguna', '', '2020-08-21 08:39:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_records`
--
ALTER TABLE `contact_records`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_type`
--
ALTER TABLE `contact_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_records`
--
ALTER TABLE `contact_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `contact_type`
--
ALTER TABLE `contact_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
