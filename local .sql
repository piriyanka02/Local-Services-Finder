-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 25, 2025 at 06:10 PM
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
-- Database: `local`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `email_alerts` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `email`, `password`, `profile_pic`, `email_alerts`) VALUES
(1, 'Piriyanka', 'piriyanka@gmail.com', '$2y$10$zyyYlI1g6iGiPiXc.omYsOwh4.qeywy2jq0LUZgCeBc1hh0GVInPS', 'uploads/admin_1753370667.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `service_type` varchar(255) DEFAULT NULL,
  `date` date NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `preferred_date` date NOT NULL,
  `preferred_time` time NOT NULL,
  `message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `provider_id`, `service_type`, `date`, `status`, `preferred_date`, `preferred_time`, `message`) VALUES
(1, 1, 1, 'Beautician', '0000-00-00', 'Approved', '2025-06-19', '10:00:00', 'Hello,  \r\nI would like to book a facial. Please let me know if you have a slot available.'),
(3, 2, 3, 'Talior', '0000-00-00', 'Approved', '2025-06-13', '09:00:00', 'Hello,\r\nI’m coming to get a shirt stitched. Will it be ready in 2 days?'),
(4, 1, 3, 'Talior', '0000-00-00', 'Rejected', '2025-06-14', '08:30:00', 'Hello,\r\nI’m coming to get a shirt stitched. Will it be ready in 5 days?'),
(5, 2, 3, 'Talior', '0000-00-00', 'Approved', '2025-06-13', '07:00:00', 'Hello,  \r\nI have a small work. Can you do it for me?');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `image`) VALUES
(1, 'Electrician', '\"Professional electrical services including wiring, lighting, repairs, and installation of electrical systems. Safe and reliable solutions for homes and businesses.\"', 'Electricion.png'),
(2, 'Mechanic', '\"Skilled vehicle repair and maintenance services, including engine checks, brake repairs, oil changes, and diagnostics to keep your car running smoothly.\"', 'Mechanic.png'),
(3, 'Plumber', '\"Expert plumbing services including installation, repair, and maintenance of pipes, faucets, toilets, and drainage systems. Reliable solutions for leaks, blockages, and water heater issues to keep your home or business running smoothly.\"', 'plumber.png'),
(10, 'Home Tutor', '\"Personalized one-on-one tutoring for students in various subjects, helping improve understanding, performance, and confidence at home.\"', 'Home tutor.png'),
(11, 'Carpenter', '\"Expert in woodwork, offering furniture making, repairs, and custom fittings for homes and offices with quality craftsmanship.\"', 'Carpenter.png'),
(12, 'Talior', '\"Custom tailoring services including stitching, alterations, and fitting of clothes for all occasions with attention to detail and style.\"', 'talior.png'),
(13, 'Beautician', '\"Professional beauty services including skincare, makeup, hair styling, and grooming to enhance your look for any occasion.\"', 'beautician.png'),
(14, 'Painter', '\"Quality painting services for homes and buildings, including interior and exterior work, touch-ups, and decorative finishes with a clean, professional touch.\"', 'painter.png');

-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

CREATE TABLE `districts` (
  `id` int(11) NOT NULL,
  `district_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `districts`
--

INSERT INTO `districts` (`id`, `district_name`) VALUES
(2, 'Ampara'),
(3, 'Anuradhapura'),
(4, 'Badulla'),
(5, 'Batticaloa'),
(6, 'Colombo'),
(1, 'District'),
(7, 'Galle'),
(8, 'Gampaha'),
(9, 'Hambantota'),
(10, 'Jaffna'),
(11, 'Kalutara'),
(12, 'Kandy'),
(13, 'Kegalle'),
(14, 'Kilinochchi'),
(15, 'Kurunegala'),
(16, 'Mannar'),
(17, 'Matale'),
(18, 'Matara'),
(19, 'Monaragala'),
(20, 'Mullaitivu'),
(21, 'Nuwara Eliya'),
(22, 'Polonnaruwa'),
(23, 'Puttalam'),
(24, 'Ratnapura'),
(25, 'Trincomalee'),
(26, 'Vavuniya');

-- --------------------------------------------------------

--
-- Table structure for table `districts_gn_divisions`
--

CREATE TABLE `districts_gn_divisions` (
  `district` varchar(100) DEFAULT NULL,
  `gn_division` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `districts_gn_divisions`
--

INSERT INTO `districts_gn_divisions` (`district`, `gn_division`) VALUES
('District', 'GN Division'),
('Ampara', 'Akkaraipattu'),
('Ampara', 'Kalmunai'),
('Ampara', 'Sammanthurai'),
('Ampara', 'Addalachchenai'),
('Ampara', 'Pottuvil'),
('Anuradhapura', 'Kekirawa'),
('Anuradhapura', 'Medawachchiya'),
('Anuradhapura', 'Galenbindunuwewa'),
('Badulla', 'Badulla'),
('Badulla', 'Bandarawela'),
('Badulla', 'Welimada'),
('Batticaloa', 'Eravur'),
('Batticaloa', 'Chenkalady'),
('Batticaloa', 'Kattankudy'),
('Colombo', 'Kollupitiya'),
('Colombo', 'Wellawatte'),
('Colombo', 'Dematagoda'),
('Colombo', 'Borella'),
('Colombo', 'Kirulapone'),
('Galle', 'Galle Fort'),
('Galle', 'Unawatuna'),
('Galle', 'Hikkaduwa'),
('Galle', 'Ambalangoda'),
('Galle', 'Karapitiya'),
('Gampaha', 'Negombo'),
('Gampaha', 'Wattala'),
('Gampaha', 'Ja-Ela'),
('Hambantota', 'Tangalle'),
('Hambantota', 'Ambalantota'),
('Hambantota', 'Tissamaharama'),
('Jaffna', 'Karainagar'),
('Jaffna', 'Nallur'),
('Jaffna', 'Chankanai'),
('Jaffna', 'Point Pedro'),
('Jaffna', 'Velanai'),
('Kalutara', 'Panadura'),
('Kalutara', 'Beruwala'),
('Kalutara', 'Horana'),
('Kandy', 'Gampola'),
('Kandy', 'Peradeniya'),
('Kandy', 'Katugastota'),
('Kandy', 'Akurana'),
('Kandy', 'Kadugannawa'),
('Kegalle', 'Kegalle'),
('Kegalle', 'Mawanella'),
('Kegalle', 'Warakapola'),
('Kilinochchi', 'Pallai'),
('Kilinochchi', 'Paranthan'),
('Kilinochchi', 'Kilinochchi'),
('Kurunegala', 'Kurunegala'),
('Kurunegala', 'Kuliyapitiya'),
('Kurunegala', 'Nikaweratiya'),
('Mannar', 'Mannar'),
('Mannar', 'Pesalai'),
('Mannar', 'Murunkan'),
('Matale', 'Matale'),
('Matale', 'Dambulla'),
('Matale', 'Ukuwela'),
('Matara', 'Matara'),
('Matara', 'Akuressa'),
('Matara', 'Hakmana'),
('Monaragala', 'Monaragala'),
('Monaragala', 'Bibile'),
('Monaragala', 'Wellawaya'),
('Mullaitivu', 'Mullaitivu'),
('Mullaitivu', 'Puthukudiyiruppu'),
('Mullaitivu', 'Oddusuddan'),
('Nuwara Eliya', 'Nuwara Eliya'),
('Nuwara Eliya', 'Hatton'),
('Nuwara Eliya', 'Ragala'),
('Polonnaruwa', 'Polonnaruwa'),
('Polonnaruwa', 'Medirigiriya'),
('Polonnaruwa', 'Hingurakgoda'),
('Puttalam', 'Puttalam'),
('Puttalam', 'Chilaw'),
('Puttalam', 'Wennappuwa'),
('Ratnapura', 'Ratnapura'),
('Ratnapura', 'Balangoda'),
('Ratnapura', 'Pelmadulla'),
('Trincomalee', 'Trincomalee'),
('Trincomalee', 'Kinniya'),
('Trincomalee', 'Kantalai'),
('Vavuniya', 'Vavuniya'),
('Vavuniya', 'Nedunkeni'),
('Vavuniya', 'Cheddikulam');

-- --------------------------------------------------------

--
-- Table structure for table `gn_divisions`
--

CREATE TABLE `gn_divisions` (
  `id` int(11) NOT NULL,
  `district_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gn_divisions`
--

INSERT INTO `gn_divisions` (`id`, `district_id`, `name`) VALUES
(1, 1, 'GN Division'),
(2, 2, 'Akkaraipattu'),
(3, 2, 'Kalmunai'),
(4, 2, 'Sammanthurai'),
(5, 2, 'Addalachchenai'),
(6, 2, 'Pottuvil'),
(7, 3, 'Kekirawa'),
(8, 3, 'Medawachchiya'),
(9, 3, 'Galenbindunuwewa'),
(10, 4, 'Badulla'),
(11, 4, 'Bandarawela'),
(12, 4, 'Welimada'),
(13, 5, 'Eravur'),
(14, 5, 'Chenkalady'),
(15, 5, 'Kattankudy'),
(16, 6, 'Kollupitiya'),
(17, 6, 'Wellawatte'),
(18, 6, 'Dematagoda'),
(19, 6, 'Borella'),
(20, 6, 'Kirulapone'),
(21, 7, 'Galle Fort'),
(22, 7, 'Unawatuna'),
(23, 7, 'Hikkaduwa'),
(24, 7, 'Ambalangoda'),
(25, 7, 'Karapitiya'),
(26, 8, 'Negombo'),
(27, 8, 'Wattala'),
(28, 8, 'Ja-Ela'),
(29, 9, 'Tangalle'),
(30, 9, 'Ambalantota'),
(31, 9, 'Tissamaharama'),
(32, 10, 'Karainagar'),
(33, 10, 'Nallur'),
(34, 10, 'Chankanai'),
(35, 10, 'Point Pedro'),
(36, 10, 'Velanai'),
(37, 11, 'Panadura'),
(38, 11, 'Beruwala'),
(39, 11, 'Horana'),
(40, 12, 'Gampola'),
(41, 12, 'Peradeniya'),
(42, 12, 'Katugastota'),
(43, 12, 'Akurana'),
(44, 12, 'Kadugannawa'),
(45, 13, 'Kegalle'),
(46, 13, 'Mawanella'),
(47, 13, 'Warakapola'),
(48, 14, 'Pallai'),
(49, 14, 'Paranthan'),
(50, 14, 'Kilinochchi'),
(51, 15, 'Kurunegala'),
(52, 15, 'Kuliyapitiya'),
(53, 15, 'Nikaweratiya'),
(54, 16, 'Mannar'),
(55, 16, 'Pesalai'),
(56, 16, 'Murunkan'),
(57, 17, 'Matale'),
(58, 17, 'Dambulla'),
(59, 17, 'Ukuwela'),
(60, 18, 'Matara'),
(61, 18, 'Akuressa'),
(62, 18, 'Hakmana'),
(63, 19, 'Monaragala'),
(64, 19, 'Bibile'),
(65, 19, 'Wellawaya'),
(66, 20, 'Mullaitivu'),
(67, 20, 'Puthukudiyiruppu'),
(68, 20, 'Oddusuddan'),
(69, 21, 'Nuwara Eliya'),
(70, 21, 'Hatton'),
(71, 21, 'Ragala'),
(72, 22, 'Polonnaruwa'),
(73, 22, 'Medirigiriya'),
(74, 22, 'Hingurakgoda'),
(75, 23, 'Puttalam'),
(76, 23, 'Chilaw'),
(77, 23, 'Wennappuwa'),
(78, 24, 'Ratnapura'),
(79, 24, 'Balangoda'),
(80, 24, 'Pelmadulla'),
(81, 25, 'Trincomalee'),
(82, 25, 'Kinniya'),
(83, 25, 'Kantalai'),
(84, 26, 'Vavuniya'),
(85, 26, 'Nedunkeni'),
(86, 26, 'Cheddikulam');

-- --------------------------------------------------------

--
-- Table structure for table `service_providers`
--

CREATE TABLE `service_providers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `service_type` varchar(100) NOT NULL,
  `experience` text NOT NULL,
  `district` varchar(100) NOT NULL,
  `gn_division` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `availability` enum('available','unavailable') NOT NULL DEFAULT 'unavailable',
  `image` varchar(255) DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_providers`
--

INSERT INTO `service_providers` (`id`, `name`, `email`, `service_type`, `experience`, `district`, `gn_division`, `phone`, `password`, `created_at`, `status`, `availability`, `image`) VALUES
(1, 'Piriyadharshini', 'piriyadharshini@gmail.com', 'Beautician', '3', 'Jaffna', 'Karainagar', '0775644231', '$2y$10$Gllt/Z/gb73b8oaoI53iZeOr6zEM.L6QT8PHBVPUGSdSu9v3zfjcG', '2025-06-11 18:44:48', 'approved', 'unavailable', 'default.jpg'),
(3, 'Kugarani', 'kugarani@gmail.com', 'Talior', '10', 'Jaffna', 'Karainagar', '0763416234', '$2y$10$Gllt/Z/gb73b8oaoI53iZeOr6zEM.L6QT8PHBVPUGSdSu9v3zfjcG', '2025-06-11 23:22:26', 'approved', 'available', 'provider_3_1753452504.jpg'),
(4, 'Arun Kumar', 'arunkumar@gmail.com', 'Electrician', '', '', '', '0774589321', '$2y$10$l63VH81Ph1BNCkDDOuLjPe5bhMUaJxgui41zbMqWs7w3omtAM5HMe', '2025-06-12 10:00:26', 'approved', 'unavailable', 'default.jpg'),
(5, 'Karthik Raj', 'karthik@gmail.com', 'Mechanic', '', '', '', '0748569213', '$2y$10$hCU9OcdqhTIMweeYm0IxXu7Ykhy0GjlJiC3ziH397ppXf50RT7RoG', '2025-06-12 10:01:56', 'pending', 'unavailable', 'default.jpg'),
(6, 'Vikraman Selvan', 'vikraman@gmail.com', 'Plumber', '', '', '', '0754698123', '$2y$10$hLi1ntVqoiz7lku3cDnX..atXolgRd69owWz2rzwwWG5rJiAaDB5.', '2025-06-12 10:03:18', 'approved', 'unavailable', 'default.jpg'),
(7, 'Divya Subramanian', 'divya@gmail.com', 'Home Tutor', '', '', '', '0775896412', '$2y$10$LRVmg498moD9xmuTBHlznOzFmk80UxudBJKbMx99md9iaVbF0wHU6', '2025-06-12 10:05:07', 'approved', 'unavailable', 'default.jpg'),
(8, 'Siddharth Murali', 'murali@gmail.com', 'Carpenter', '', '', '', '0764589213', '$2y$10$179l.qixJcrxnSpv7S1YHeCLkUhwTHiJic1l5wkov1DZRH2d36aF6', '2025-06-12 10:06:22', 'approved', 'unavailable', 'default.jpg'),
(9, 'Raghavan Tharun', 'raghavan@gmail.com', 'Painter', '', '', '', '0785423695', '$2y$10$szdxE98X4fV0LXyW6essiu.ZWN3Eurz/8x46hD7hrfFNJ2lnqvpHG', '2025-06-12 10:07:47', 'approved', 'unavailable', 'default.jpg'),
(10, 'Kannan', 'kannan@gmail.com', 'Mechanic', '1', 'colombo', 'Wellawatte', '0765460948', '$2y$10$W.Rh4I.Yd4jFSCvcKj9vTOGsdijir5lj9I200rS2jjXEG2UwqtzIC', '2025-07-24 12:56:25', 'approved', 'unavailable', 'default.jpg'),
(12, 'Kayaththiri', 'kaya@gmail.com', 'Beauticians', '3', 'Jaffna', 'Karainagar', '0754128963', '$2y$10$gc/BmTi87D9zrAUc6XLUleRf9KVhV3mP.w4GA0STDqIukV1QE4hZ2', '2025-07-25 02:12:14', 'approved', 'unavailable', 'default.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `phone`) VALUES
(1, 'Kajanan', 'kajanan@gmail.com', '$2y$10$ueSCCcL3XtTvoaEfGHmqn.yIkbXoLPp19MQkPpoh0XPZxysyLawo.', '2025-06-11 19:13:00', '0765460942'),
(2, 'Mainthan', 'mainthan@gmail.com', '$2y$10$xssgxThhZ5D4Tl.1NYPpruXb1imaZ5BoDbM3D1gdFQEIwE2Fws7mi', '2025-06-12 06:50:20', '0764123588');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `provider_id` (`provider_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`district_name`);

--
-- Indexes for table `gn_divisions`
--
ALTER TABLE `gn_divisions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `district_id` (`district_id`);

--
-- Indexes for table `service_providers`
--
ALTER TABLE `service_providers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

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
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `districts`
--
ALTER TABLE `districts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `gn_divisions`
--
ALTER TABLE `gn_divisions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `service_providers`
--
ALTER TABLE `service_providers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`provider_id`) REFERENCES `service_providers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gn_divisions`
--
ALTER TABLE `gn_divisions`
  ADD CONSTRAINT `gn_divisions_ibfk_1` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
