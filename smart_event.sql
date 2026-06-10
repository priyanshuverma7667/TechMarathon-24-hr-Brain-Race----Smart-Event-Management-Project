-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2026 at 08:00 AM
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
-- Database: `smart_event`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `admin_email` varchar(300) NOT NULL,
  `password` varchar(500) NOT NULL,
  `admin_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_email`, `password`, `admin_name`) VALUES
(1, 'utkarsh55@gmail.com', '5b60ec26b4ded32bdb04018f851654c3', 'utkarsh');

-- --------------------------------------------------------

--
-- Table structure for table `coordinator`
--

CREATE TABLE `coordinator` (
  `co_id` int(11) NOT NULL,
  `co_name` varchar(100) NOT NULL,
  `co_email` varchar(100) NOT NULL,
  `co_phone` varchar(15) DEFAULT NULL,
  `co_gender` varchar(10) DEFAULT NULL,
  `co_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `co_pass` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coordinator`
--

INSERT INTO `coordinator` (`co_id`, `co_name`, `co_email`, `co_phone`, `co_gender`, `co_address`, `created_at`, `co_pass`) VALUES
(1, 'priyanshu', 'priyanshu76670@gmail.com', '7667040788', 'M', NULL, '2026-04-25 05:54:56', 'c37bf859faf392800d739a41fe5af151'),
(2, 'shubhampriya', 'shubhampriya572@gmail.com', '7209976553', 'F', NULL, '2026-04-25 06:00:53', 'c37bf859faf392800d739a41fe5af151'),
(3, 'saurabh', 'saurabh123@gmail.com', '9988776655', 'M', NULL, '2026-04-25 08:09:31', 'c37bf859faf392800d739a41fe5af151'),
(5, 'vandana', 'vandana0202@gmail.com', '8877665544', 'Female', 'patna,bihar', '2026-04-25 20:17:18', 'c37bf859faf392800d739a41fe5af151');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `enroll_id` int(11) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `event_id` int(11) NOT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `user_email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `team_name` varchar(150) DEFAULT NULL,
  `total_members` int(11) DEFAULT 1,
  `participation_mode` enum('Individual','Team') DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` enum('Joined','Withdrawn','Completed','Cancelled') DEFAULT 'Joined',
  `joined_at` datetime DEFAULT current_timestamp(),
  `withdrawn_at` datetime DEFAULT NULL,
  `certificate_status` enum('Pending','Issued') DEFAULT 'Pending',
  `certificate_file` varchar(255) DEFAULT NULL,
  `team_leader_id` bigint(20) DEFAULT NULL,
  `is_winner` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`enroll_id`, `user_id`, `event_id`, `full_name`, `user_email`, `phone`, `team_name`, `total_members`, `participation_mode`, `remarks`, `status`, `joined_at`, `withdrawn_at`, `certificate_status`, `certificate_file`, `team_leader_id`, `is_winner`) VALUES
(14, 5, 9, 'shivam kumar', 'shiva123@gmail.com', '8877665544', '', 1, 'Individual', '', 'Withdrawn', '2026-04-26 04:35:33', '2026-04-26 04:35:46', 'Pending', NULL, NULL, 0),
(15, 5, 9, 'shivam kumar', 'shiva123@gmail.com', '8877665544', '', 1, 'Individual', 'present', 'Joined', '2026-04-26 04:46:50', NULL, 'Pending', NULL, NULL, 1),
(16, 5, 12, 'shivam kumar', 'shiva123@gmail.com', '6392858563', 'team32', 4, 'Team', '', 'Joined', '2026-04-26 07:19:07', NULL, 'Pending', NULL, 5, 0),
(17, 2, 12, 'mohan kumar', 'mohan42@gmail.com', '8877556677', 'team32', 4, 'Team', 'Added by Team Leader', 'Joined', '2026-04-26 07:19:07', NULL, 'Pending', NULL, 5, 0),
(18, 4, 12, 'suraj verma', 'sverma6655@gmail.com', '985671237', 'team32', 4, 'Team', 'Added by Team Leader', 'Joined', '2026-04-26 07:19:07', NULL, 'Pending', NULL, 5, 0),
(19, 3, 12, 'aman tiwari', 'aman420@gmail.com', '7766554433', 'team32', 4, 'Team', 'Added by Team Leader', 'Joined', '2026-04-26 07:19:07', NULL, 'Pending', NULL, 5, 0),
(20, 9, 13, 'piyush Sharma', 'priyanshuverma5374@gmail.com', '7494063499', '', 1, 'Individual', 'present', 'Joined', '2026-04-26 08:26:29', NULL, 'Pending', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `event_id` int(11) NOT NULL,
  `event_name` varchar(100) NOT NULL,
  `event_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `reg_open_date` date NOT NULL,
  `reg_close_date` date NOT NULL,
  `event_date` date NOT NULL,
  `event_time` time DEFAULT NULL,
  `event_location` varchar(150) DEFAULT NULL,
  `max_participants` int(11) DEFAULT 1,
  `min_participants` int(11) DEFAULT 1,
  `fees` decimal(10,2) DEFAULT 0.00,
  `status` enum('upcoming','open','closed','completed','running') DEFAULT 'upcoming',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `participation_mode` enum('Individual','Team') NOT NULL DEFAULT 'Individual',
  `allowed_gender` enum('Any','Male','Female','Other') DEFAULT 'Any',
  `required_gender_member` enum('None','Male','Female','Other') DEFAULT 'None'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`event_id`, `event_name`, `event_type`, `description`, `reg_open_date`, `reg_close_date`, `event_date`, `event_time`, `event_location`, `max_participants`, `min_participants`, `fees`, `status`, `created_at`, `participation_mode`, `allowed_gender`, `required_gender_member`) VALUES
(9, 'TECH MARATHON', 'TECH', '24hrs techno brain race', '2026-04-23', '2026-04-24', '2026-04-25', '08:22:00', 'SMS VARANASI', 1, 1, 0.00, 'closed', '2026-04-25 22:29:08', 'Individual', 'Any', 'Female'),
(10, 'ADHARSHILA', 'CULTURAL', 'annual function', '0000-00-00', '0000-00-00', '0000-00-00', NULL, NULL, 1, 1, 0.00, 'upcoming', '2026-04-25 23:07:50', 'Individual', 'Any', 'None'),
(11, 'SPORTS FEST', 'SPORTS', 'different types of sports played', '2026-04-24', '2026-04-26', '2026-04-27', '06:47:00', 'SMS VARANASI', 4, 1, 0.00, 'upcoming', '2026-04-25 23:08:16', 'Team', 'Female', 'Female'),
(12, 'WebDX', 'TECH', '8hr brain race', '2026-04-25', '2026-04-26', '2026-04-26', '12:02:00', 'SMS VARANASI', 4, 1, 2.00, 'upcoming', '2026-04-25 23:08:35', 'Team', 'Any', 'None'),
(13, 'SOCIAL MEDIA TRAINING', 'WORKSHOP', '10 days training', '2026-04-22', '2026-04-25', '2026-04-26', '08:22:00', 'SMS VARANASI', 1, 1, 1.00, 'running', '2026-04-25 23:09:15', 'Individual', 'Any', 'None');

-- --------------------------------------------------------

--
-- Table structure for table `event_coordinator_map`
--

CREATE TABLE `event_coordinator_map` (
  `map_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `event_co_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_coordinator_map`
--

INSERT INTO `event_coordinator_map` (`map_id`, `event_id`, `event_co_id`) VALUES
(20, 9, 2),
(21, 9, 5),
(22, 10, 3),
(23, 11, 2),
(24, 12, 1),
(30, 13, 5),
(31, 13, 2);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `payment_id` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(20) DEFAULT 'Success',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `user_id`, `event_id`, `payment_id`, `amount`, `status`, `created_at`) VALUES
(3, 5, 12, 'pay_Shwgnbt0BDrUX8', 2.00, 'Success', '2026-04-26 01:49:07'),
(4, 9, 13, 'pay_ShxpxlfoQIjpc6', 1.00, 'Success', '2026-04-26 02:56:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` bigint(20) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `full_name` varchar(200) GENERATED ALWAYS AS (concat(`first_name`,' ',coalesce(`last_name`,''))) STORED,
  `username` varchar(100) NOT NULL,
  `user_email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `user_pass` varchar(255) NOT NULL,
  `role` enum('participant','team_leader','admin') DEFAULT 'participant',
  `gender` enum('male','female','other') DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `organization_name` varchar(200) DEFAULT NULL,
  `department` varchar(150) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `total_events_joined` int(11) DEFAULT 0,
  `total_events_won` int(11) DEFAULT 0,
  `total_certificates` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `username`, `user_email`, `phone`, `user_pass`, `role`, `gender`, `date_of_birth`, `profile_image`, `organization_name`, `department`, `country`, `state`, `city`, `address`, `is_verified`, `is_active`, `total_events_joined`, `total_events_won`, `total_certificates`, `created_at`, `updated_at`, `last_login`) VALUES
(1, 'raman', 'tiwari', 'ramant21', 'ramantiwari21@gmal.com', '8899665544', '4e9fb040ab50b6221f4328aa95ff28c0', 'participant', 'male', '2016-04-14', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 0, 0, '2026-04-25 11:36:00', '2026-04-26 01:53:09', NULL),
(2, 'mohan', 'kumar', 'mohan42ku', 'mohan42@gmail.com', '8877556677', '05dc4be3550a5f2ec6bdb5e3a2fc5059', 'participant', 'male', '2017-04-12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 0, 0, '2026-04-25 13:31:10', '2026-04-26 01:53:09', NULL),
(3, 'aman', 'tiwari', 'aman420t', 'aman420@gmail.com', '7766554433', '9e1afa9f160e2403d5a6787e7e39c46f', 'participant', 'male', '2019-04-17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 0, 0, '2026-04-25 13:32:50', '2026-04-26 01:53:09', NULL),
(4, 'suraj', 'verma', 'sverma7667', 'sverma6655@gmail.com', '985671237', 'fb62579e990da4e2a8f15c3d1e123438', 'participant', 'male', '2016-04-05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 0, 0, '2026-04-25 13:34:18', '2026-04-26 01:53:09', NULL),
(5, 'shivam', 'kumar', 'shiva123', 'shiva123@gmail.com', '8877665544', 'e10adc3949ba59abbe56e057f20f883e', 'participant', 'male', '2008-06-12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 2, 0, 0, '2026-04-25 22:09:18', '2026-04-26 01:49:07', NULL),
(6, 'Manish', 'Singh', 'manish420', 'manish420@gmail.com', '798456235', '6531401f9a6807306651b87e44c05751', 'participant', 'male', '2002-08-11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 0, 0, '2026-04-26 01:59:40', '2026-04-26 01:59:40', NULL),
(7, 'Shyamu', 'Sharma', 'Shyamji123', 'syshrma456@gmail.com', '96857412', '6531401f9a6807306651b87e44c05751', 'participant', 'male', '2001-06-02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 0, 0, '2026-04-26 02:03:00', '2026-04-26 02:03:00', NULL),
(8, 'Amritanshu', 'Singh', 'amritanshu234', 'amritanshu234@gmail.com', '8745956623', '6531401f9a6807306651b87e44c05751', 'participant', 'male', '2000-07-28', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 0, 0, '2026-04-26 02:05:35', '2026-04-26 02:05:35', NULL),
(9, 'piyush', 'Sharma', 'piyush6655', 'priyanshuverma5374@gmail.com', '7494063499', 'c37bf859faf392800d739a41fe5af151', 'participant', 'male', '2010-02-04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0, '2026-04-26 02:55:25', '2026-04-26 02:56:29', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `coordinator`
--
ALTER TABLE `coordinator`
  ADD PRIMARY KEY (`co_id`),
  ADD UNIQUE KEY `co_email` (`co_email`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`enroll_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `event_coordinator_map`
--
ALTER TABLE `event_coordinator_map`
  ADD PRIMARY KEY (`map_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `event_co_id` (`event_co_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`user_email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `coordinator`
--
ALTER TABLE `coordinator`
  MODIFY `co_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enroll_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `event_coordinator_map`
--
ALTER TABLE `event_coordinator_map`
  MODIFY `map_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`) ON DELETE CASCADE;

--
-- Constraints for table `event_coordinator_map`
--
ALTER TABLE `event_coordinator_map`
  ADD CONSTRAINT `event_coordinator_map_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`),
  ADD CONSTRAINT `event_coordinator_map_ibfk_2` FOREIGN KEY (`event_co_id`) REFERENCES `coordinator` (`co_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
