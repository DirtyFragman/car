-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 01, 2024 at 06:54 AM
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
-- Database: `car_dealership`
--

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `car_id` int(11) NOT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `model` varchar(50) NOT NULL,
  `year` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `mileage` int(11) NOT NULL,
  `fuel_type` enum('Petrol','Diesel','Electric') NOT NULL,
  `transmission` enum('Manual','Automatic') NOT NULL,
  `drive_mode` varchar(50) NOT NULL,
  `engine_size` decimal(3,1) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`car_id`, `brand_id`, `model`, `year`, `price`, `mileage`, `fuel_type`, `transmission`, `drive_mode`, `engine_size`, `image_url`, `description`) VALUES
(1, 1, 'Vantage', 2022, 149900.00, 5000, 'Petrol', 'Automatic', 'Sport', 4.0, 'vantage.png', 'Aston Martin Vantage with a 4.0L V8 engine'),
(2, 1, 'DBX', 2023, 193500.00, 1000, 'Petrol', 'Manual', 'All-Wheel Drive', 4.0, 'dbx.png', 'Aston Martin DBX with a 4.0L V8 engine'),
(3, 2, '911 Carrera', 2021, 113300.00, 8000, 'Petrol', 'Automatic', 'Sport', 3.0, '911_carrera.png', 'Porsche 911 Carrera with a 3.0L engine'),
(4, 2, 'Taycan', 2022, 82400.00, 3000, 'Electric', 'Automatic', 'Sport', 0.0, 'taycan.png', 'Porsche Taycan electric car'),
(5, 3, 'Huayra', 2020, 3400000.00, 1200, 'Petrol', 'Manual', 'Sport', 6.0, 'huayra.png', 'Pagani Huayra with a 6.0L V12 engine'),
(6, 3, 'Zonda', 2019, 2800000.00, 3000, 'Petrol', 'Manual', 'Sport', 7.3, 'zonda.png', 'Pagani Zonda with a 7.3L V12 engine'),
(7, 4, 'X5', 2023, 61000.00, 1500, 'Diesel', 'Automatic', 'All-Wheel Drive', 3.0, 'x5.png', 'BMW X5 with a 3.0L diesel engine'),
(8, 4, 'M4', 2023, 71800.00, 2500, 'Petrol', 'Manual', 'Sport', 3.0, 'm4.png', 'BMW M4 with a 3.0L petrol engine'),
(9, 5, 'S-Class', 2023, 111000.00, 2000, 'Petrol', 'Automatic', 'Luxury', 3.0, 's_class.png', 'Mercedes S-Class with a 3.0L engine'),
(10, 5, 'EQS', 2023, 103000.00, 500, 'Electric', 'Automatic', 'Luxury', 0.0, 'eqs.png', 'Mercedes EQS electric car'),
(11, 6, 'Ghibli', 2022, 76500.00, 7000, 'Petrol', 'Automatic', 'Sport', 3.0, 'ghibli.png', 'Maserati Ghibli with a 3.0L petrol engine'),
(12, 6, 'Levante', 2023, 83000.00, 2000, 'Diesel', 'Automatic', 'All-Wheel Drive', 3.0, 'levante.png', 'Maserati Levante with a 3.0L diesel engine');

-- --------------------------------------------------------

--
-- Table structure for table `car_brands`
--

CREATE TABLE `car_brands` (
  `brand_id` int(11) NOT NULL,
  `brand_name` varchar(50) NOT NULL,
  `logo_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car_brands`
--

INSERT INTO `car_brands` (`brand_id`, `brand_name`, `logo_url`) VALUES
(1, 'Aston Martin', 'aston_martin_logo.png'),
(2, 'Porsche', 'porsche_logo.png'),
(3, 'Pagani', 'pagani_logo.png'),
(4, 'BMW', 'bmw_logo.png'),
(5, 'Mercedes', 'mercedes_logo.png'),
(6, 'Maserati', 'maserati_logo.png');

-- --------------------------------------------------------

--
-- Table structure for table `garage`
--

CREATE TABLE `garage` (
  `garage_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `car_id` int(11) DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `garage`
--

INSERT INTO `garage` (`garage_id`, `user_id`, `car_id`, `added_at`) VALUES
(19, 31, 8, '2024-11-01 05:25:13'),
(20, 31, 5, '2024-11-01 05:25:27'),
(21, 31, 11, '2024-11-01 05:40:05'),
(22, 31, 4, '2024-11-01 05:43:01'),
(23, 31, 12, '2024-11-01 05:43:11');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `email`, `created_at`) VALUES
(1, 'ian', '123', 'ian@gmail.com', '2024-10-29 18:45:05'),
(2, '', '$2y$10$.3rQr5mBvMIdPDquzhgIv.cCd4UyK5qDXIEuFCRXRom0jZUTjeaQy', 'jamie@gmail.com', '2024-10-30 18:05:59'),
(31, 'user', '$2y$10$u38j6OuXD6atEpfXx7ANj.MLApQJrT3Icogs4FCacGWYJ6pLww7Ya', 'weq@gmail.com', '2024-11-01 05:18:10');

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `after_user_update` AFTER UPDATE ON `users` FOR EACH ROW BEGIN
    IF OLD.email != NEW.email THEN
        INSERT INTO user_activity (user_id, activity_type, description)
        VALUES (NEW.user_id, 'profile_update', 'Updated email address');
    END IF;
    IF (OLD.password_hash != NEW.password_hash) THEN
        INSERT INTO user_activity (user_id, activity_type, description)
        VALUES (NEW.user_id, 'security', 'Changed password');
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_activity`
--

CREATE TABLE `user_activity` (
  `activity_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `activity_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_activity`
--

INSERT INTO `user_activity` (`activity_id`, `user_id`, `activity_type`, `description`, `activity_date`) VALUES
(1, 2, 'account_creation', 'Account created', '2024-10-31 09:36:50'),
(2, 1, 'account_creation', 'Account created', '2024-10-31 09:36:50'),
(3, 2, 'security', 'Changed password', '2024-11-01 02:27:25'),
(4, 2, 'garage_add', 'Added car to garage', '2024-11-01 03:04:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`car_id`),
  ADD KEY `brand_id` (`brand_id`);

--
-- Indexes for table `car_brands`
--
ALTER TABLE `car_brands`
  ADD PRIMARY KEY (`brand_id`),
  ADD UNIQUE KEY `brand_name` (`brand_name`);

--
-- Indexes for table `garage`
--
ALTER TABLE `garage`
  ADD PRIMARY KEY (`garage_id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`car_id`),
  ADD KEY `car_id` (`car_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_activity`
--
ALTER TABLE `user_activity`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `idx_user_activity_user_id` (`user_id`),
  ADD KEY `idx_user_activity_date` (`activity_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `car_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `car_brands`
--
ALTER TABLE `car_brands`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `garage`
--
ALTER TABLE `garage`
  MODIFY `garage_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `user_activity`
--
ALTER TABLE `user_activity`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cars`
--
ALTER TABLE `cars`
  ADD CONSTRAINT `cars_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `car_brands` (`brand_id`) ON DELETE CASCADE;

--
-- Constraints for table `garage`
--
ALTER TABLE `garage`
  ADD CONSTRAINT `garage_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `garage_ibfk_2` FOREIGN KEY (`car_id`) REFERENCES `cars` (`car_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_activity`
--
ALTER TABLE `user_activity`
  ADD CONSTRAINT `user_activity_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
