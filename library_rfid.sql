-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 05, 2025 at 01:17 PM
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
-- Database: `library_rfid`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `id` int(255) NOT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `date_created` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_admin`
--

INSERT INTO `tbl_admin` (`id`, `firstname`, `lastname`, `email`, `username`, `password`, `date_created`) VALUES
(1, 'jordan', 'zapantaa', 'jordan@gmail.com', 'jordan', '123', '2025-09-04'),
(2, 'asd', 'asd', 'asd@gmail.com', 'asd', 'asd', '2025-09-05');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_books`
--

CREATE TABLE `tbl_books` (
  `id` int(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `date_created` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_books`
--

INSERT INTO `tbl_books` (`id`, `title`, `description`, `status`, `date_created`) VALUES
(1, 'programming 1', 'para lang sa ewan', 'unavailable', '2025-09-04'),
(2, 'javascipt book', 'sdad', 'unavailable', '2025-09-04'),
(3, 'python book', 'hello', 'available', '2025-09-04'),
(4, 'test', 'test', 'available', '2025-09-04'),
(5, 'asd', 'asd', 'unavailable', '2025-09-04'),
(6, 'qwe', 'asd', 'available', '2025-09-04'),
(7, 'qwe12123asdadzx', 'q3213zxczxc', 'available', '2025-09-04'),
(8, 'adasro;iqp heflkjashdflkjashdflkjhwlkej,r', 'asdfasdf', 'available', '2025-09-04');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_rfid_auth`
--

CREATE TABLE `tbl_rfid_auth` (
  `id` int(255) NOT NULL,
  `uid` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `inuse` int(10) DEFAULT 0,
  `date_created` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_rfid_auth`
--

INSERT INTO `tbl_rfid_auth` (`id`, `uid`, `status`, `inuse`, `date_created`) VALUES
(4, '2643783428', 'valid', 1, '2025-09-04'),
(5, '0335859972', 'valid', 0, '2025-09-04'),
(6, '2643783940', 'valid', 0, '2025-09-04');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_rfid_loan`
--

CREATE TABLE `tbl_rfid_loan` (
  `id` int(255) NOT NULL,
  `uid` varchar(255) DEFAULT NULL,
  `student_id` int(255) DEFAULT NULL,
  `book_id` int(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `borrow_date` timestamp NULL DEFAULT NULL,
  `return_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_rfid_loan`
--

INSERT INTO `tbl_rfid_loan` (`id`, `uid`, `student_id`, `book_id`, `status`, `borrow_date`, `return_date`) VALUES
(9, '2643783428', 4, 2, 'borrowed', '2025-09-04 13:00:26', NULL),
(10, '2643783428', 4, 1, 'borrowed', '2025-09-04 13:00:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_students`
--

CREATE TABLE `tbl_students` (
  `id` int(255) NOT NULL,
  `uid` varchar(255) DEFAULT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  `eligible_status` varchar(20) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `date_created` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_students`
--

INSERT INTO `tbl_students` (`id`, `uid`, `firstname`, `lastname`, `email`, `address`, `eligible_status`, `image_path`, `date_created`) VALUES
(4, '2643783428', 'jordan', 'Zapanta', 'SantaRita@gmail.com', 'hello12323', '0', NULL, '2025-09-04');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_superadmin`
--

CREATE TABLE `tbl_superadmin` (
  `id` int(10) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_superadmin`
--

INSERT INTO `tbl_superadmin` (`id`, `name`, `username`, `password`) VALUES
(1, 'Super Admin', 'superadmin', 'superadmin');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `id` int(255) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `user_type` varchar(10) DEFAULT NULL,
  `date_created` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id`, `email`, `username`, `password`, `user_type`, `date_created`) VALUES
(1, 'jordan@gmail.com', 'jordan', '123', 'admin', '2025-09-04'),
(2, 'asd@gmail.com', 'asd', 'asd', 'admin', '2025-09-05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_books`
--
ALTER TABLE `tbl_books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_rfid_auth`
--
ALTER TABLE `tbl_rfid_auth`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_rfid_loan`
--
ALTER TABLE `tbl_rfid_loan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_students`
--
ALTER TABLE `tbl_students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_superadmin`
--
ALTER TABLE `tbl_superadmin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_books`
--
ALTER TABLE `tbl_books`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_rfid_auth`
--
ALTER TABLE `tbl_rfid_auth`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_rfid_loan`
--
ALTER TABLE `tbl_rfid_loan`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tbl_students`
--
ALTER TABLE `tbl_students`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_superadmin`
--
ALTER TABLE `tbl_superadmin`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
