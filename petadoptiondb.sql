-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 08, 2026 at 10:12 AM
-- Server version: 8.0.42
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `petadoptiondb`
--

-- --------------------------------------------------------

--
-- Table structure for table `adoption`
--

CREATE TABLE `adoption` (
  `adoptionid` int NOT NULL,
  `userid` int NOT NULL,
  `petid` int NOT NULL,
  `reviewed_by` int NOT NULL,
  `application_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `reason` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `appointmentid` int NOT NULL,
  `userid` int NOT NULL,
  `petid` int NOT NULL,
  `adoptionid` int NOT NULL,
  `fosterid` int NOT NULL,
  `appointment_datetime` datetime DEFAULT NULL,
  `purpose` varchar(250) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `foster`
--

CREATE TABLE `foster` (
  `fosterid` int NOT NULL,
  `userid` int NOT NULL,
  `petid` int NOT NULL,
  `reviewed_by` int NOT NULL,
  `application_date` date DEFAULT NULL,
  `planned_start_date` date DEFAULT NULL,
  `planned_end_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pet`
--

CREATE TABLE `pet` (
  `petid` int NOT NULL,
  `created_by` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `species` varchar(50) NOT NULL,
  `breed` varchar(50) NOT NULL,
  `age` int NOT NULL,
  `sex` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `photo_url` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `placement`
--

CREATE TABLE `placement` (
  `placementid` int NOT NULL,
  `userid` int NOT NULL,
  `petid` int NOT NULL,
  `adoptionid` int NOT NULL,
  `fosterid` int NOT NULL,
  `recorded_by` int NOT NULL,
  `placementtype` varchar(50) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userid` int NOT NULL,
  `full_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `role` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userid`, `full_name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Maverick Moran', 'test@gmail.com', '$2y$10$99vT0LA9b9o.HPd/doiNTO.w0tUDxLqr/n3O/.Nfd2PmEFdC1DnxW', 'user', '2026-10-31 05:13:05'),
(2, 'Renon palbog', 'palbog@gmail.com', '$2y$10$bMn4BGioSZ5LnWWhOhCVfu1faPu1GuTV9mle6PYq2SX.uhS6Mnedi', 'user', '2026-10-31 05:17:43'),
(3, 'Burat renon', 'burat@gmail.com', '$2y$10$MunpnBaNS58WwJOkbgjnrem/nWEpd30FeFqVIIhTnF801SzVI0DNi', 'user', '2026-10-31 05:20:18'),
(4, 'Maverick Moran', 'test_1@gmail.com', '$2y$10$c86wdxSBszHOZ2OxNn8KBu6588qMbkbkquQEzV8ZiV5ISOAWGxRXO', 'user', '2026-10-31 05:27:39'),
(5, 'Maverick Moran', 'maverickmoran3@gmail.com', '$2y$10$k0ei.pQKa3N1DLF3gxH8NOkoQnc5/mUAGvg.MDI2GrDoVq6ahHGhu', 'role', '2026-11-03 12:59:48'),
(6, 'Jumong Malbog', 'malbog@gmail.com', '$2y$10$.P7cVHVUf.rarx9BRa.gNeSZ2VYCA5Lw5iVk7GNI.bisQwDsZoTPO', 'user', '2026-11-03 13:01:00'),
(7, 'test2', 'test2@gmail.com', '$2y$10$GsVyA1lTHtZ47Uvq1btfveu/VsLlKMNxjCYv4hDmcNfOwBmtrL.FC', 'user', '2026-11-05 09:52:21'),
(8, 'test3', 'testmo@gmail.com', '$2y$10$Iv6WJu5orKhKWsk7hQ0unuVg5IeJoc2VSeUX8MU47r1z5CC6xPl5e', 'user', '2026-11-05 23:56:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adoption`
--
ALTER TABLE `adoption`
  ADD PRIMARY KEY (`adoptionid`,`userid`,`petid`,`reviewed_by`),
  ADD KEY `userid` (`userid`),
  ADD KEY `petid` (`petid`),
  ADD KEY `reviewed_by` (`reviewed_by`);

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`appointmentid`,`userid`,`petid`,`adoptionid`,`fosterid`),
  ADD KEY `userid` (`userid`),
  ADD KEY `petid` (`petid`),
  ADD KEY `adoptionid` (`adoptionid`),
  ADD KEY `fosterid` (`fosterid`);

--
-- Indexes for table `foster`
--
ALTER TABLE `foster`
  ADD PRIMARY KEY (`fosterid`,`userid`,`petid`,`reviewed_by`),
  ADD KEY `userid` (`userid`),
  ADD KEY `petid` (`petid`),
  ADD KEY `reviewed_by` (`reviewed_by`);

--
-- Indexes for table `pet`
--
ALTER TABLE `pet`
  ADD PRIMARY KEY (`petid`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `placement`
--
ALTER TABLE `placement`
  ADD PRIMARY KEY (`placementid`,`userid`,`petid`,`adoptionid`,`fosterid`,`recorded_by`),
  ADD KEY `userid` (`userid`),
  ADD KEY `petid` (`petid`),
  ADD KEY `adoptionid` (`adoptionid`),
  ADD KEY `fosterid` (`fosterid`),
  ADD KEY `recorded_by` (`recorded_by`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userid`),
  ADD UNIQUE KEY `Email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adoption`
--
ALTER TABLE `adoption`
  MODIFY `adoptionid` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `appointmentid` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `foster`
--
ALTER TABLE `foster`
  MODIFY `fosterid` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pet`
--
ALTER TABLE `pet`
  MODIFY `petid` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `placement`
--
ALTER TABLE `placement`
  MODIFY `placementid` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adoption`
--
ALTER TABLE `adoption`
  ADD CONSTRAINT `adoption_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`userid`),
  ADD CONSTRAINT `adoption_ibfk_2` FOREIGN KEY (`petid`) REFERENCES `pet` (`petid`),
  ADD CONSTRAINT `adoption_ibfk_3` FOREIGN KEY (`reviewed_by`) REFERENCES `user` (`userid`);

--
-- Constraints for table `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `appointment_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`userid`),
  ADD CONSTRAINT `appointment_ibfk_2` FOREIGN KEY (`petid`) REFERENCES `pet` (`petid`),
  ADD CONSTRAINT `appointment_ibfk_3` FOREIGN KEY (`adoptionid`) REFERENCES `adoption` (`adoptionid`),
  ADD CONSTRAINT `appointment_ibfk_4` FOREIGN KEY (`fosterid`) REFERENCES `foster` (`fosterid`);

--
-- Constraints for table `foster`
--
ALTER TABLE `foster`
  ADD CONSTRAINT `foster_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`userid`),
  ADD CONSTRAINT `foster_ibfk_2` FOREIGN KEY (`petid`) REFERENCES `pet` (`petid`),
  ADD CONSTRAINT `foster_ibfk_3` FOREIGN KEY (`reviewed_by`) REFERENCES `user` (`userid`);

--
-- Constraints for table `pet`
--
ALTER TABLE `pet`
  ADD CONSTRAINT `fk_pet_created_by` FOREIGN KEY (`created_by`) REFERENCES `user` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `placement`
--
ALTER TABLE `placement`
  ADD CONSTRAINT `placement_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`userid`),
  ADD CONSTRAINT `placement_ibfk_2` FOREIGN KEY (`petid`) REFERENCES `pet` (`petid`),
  ADD CONSTRAINT `placement_ibfk_3` FOREIGN KEY (`adoptionid`) REFERENCES `adoption` (`adoptionid`),
  ADD CONSTRAINT `placement_ibfk_4` FOREIGN KEY (`fosterid`) REFERENCES `foster` (`fosterid`),
  ADD CONSTRAINT `placement_ibfk_5` FOREIGN KEY (`recorded_by`) REFERENCES `user` (`userid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
