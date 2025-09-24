-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 06, 2025 at 04:49 AM
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
  `genre_id` int(100) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `date_created` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_books`
--

INSERT INTO `tbl_books` (`id`, `title`, `description`, `genre_id`, `status`, `date_created`) VALUES
(9, 'programming', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 1, 'available', '2025-09-06'),
(10, 'A course module for ethics', 'An ethics course module provides an overview of the study of human conduct and moral principles, examining the nature of right and wrong, good and bad, and the justification of moral beliefs.\r\n It typically explores foundational concepts such as morality, ethical standards, and the distinction between moral and non-moral issues.\r\n', 2, 'available', '2025-09-06'),
(11, 'Developing your Personality', ' A book on developing your personality is that it serves as a practical guide to cultivating and expressing one\'s unique personality in daily life, focusing on self-awareness, self-confidence, communication skills, and personal growth through exercises and real-life examples.\r\n', 3, 'available', '2025-09-06'),
(12, 'Sariling Wika at Pilosopiyang Filipino ', 'Centers on the critical role of the native language, particularly Filipino, in the development of a distinct and authentic Filipino philosophy. It emphasizes that the use of the national language is not merely a linguistic choice but a vital mechanism for intellectual and cultural empowerment, enabling the expression of complex ideas, truth, and reality in a way that is deeply rooted in the Filipino experience.', 4, 'available', '2025-09-06'),
(13, 'test', 'test', 7, 'unavailable', '2025-09-06');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_genre`
--

CREATE TABLE `tbl_genre` (
  `id` int(11) NOT NULL,
  `genre_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_genre`
--

INSERT INTO `tbl_genre` (`id`, `genre_name`) VALUES
(1, 'BSIS COLLEGE'),
(2, 'COLLEGE ENTREP'),
(3, 'COLLEGE FOREIGN'),
(5, 'FILIPINIANA COLLEGE'),
(7, 'FILIPINIANA HIGH SCHOOL'),
(6, 'FILIPINO-TAGALOG HIGH SCHOOL'),
(8, 'FORIEGN HIGH SCHOOL'),
(9, 'KAPAMPANGAN SHS'),
(10, 'SHS FOREIGN'),
(4, 'TAGALOG COLLEGE');

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
(10, '2643783428', 4, 1, 'borrowed', '2025-09-04 13:00:26', NULL),
(11, '2643783428', 5, 13, 'borrowed', '2025-09-06 02:44:04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_students`
--

CREATE TABLE `tbl_students` (
  `id` int(255) NOT NULL,
  `uid` varchar(255) DEFAULT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `year` varchar(50) DEFAULT NULL,
  `section` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  `eligible_status` varchar(20) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `date_created` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_students`
--

INSERT INTO `tbl_students` (`id`, `uid`, `firstname`, `lastname`, `year`, `section`, `email`, `address`, `eligible_status`, `image_path`, `date_created`) VALUES
(5, '2643783428', 'test1', 'test1', '1st year', 'BSIS A', 'test@gmail.com', 'sa malapit lang', '0', '../uploads/1757122893_prank2.jpg', '2025-09-06');

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
-- Indexes for table `tbl_genre`
--
ALTER TABLE `tbl_genre`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `genre_name` (`genre_name`);

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
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tbl_genre`
--
ALTER TABLE `tbl_genre`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tbl_rfid_auth`
--
ALTER TABLE `tbl_rfid_auth`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_rfid_loan`
--
ALTER TABLE `tbl_rfid_loan`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tbl_students`
--
ALTER TABLE `tbl_students`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
