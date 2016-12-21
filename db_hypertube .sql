-- phpMyAdmin SQL Dump
-- version 4.6.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 21, 2016 at 08:55 AM
-- Server version: 5.6.32
-- PHP Version: 5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_hypertube`
--

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `movie_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`movie_id`, `name`, `user_id`, `created_at`, `updated_at`) VALUES
(6044, 'ISRA 88', 18, '2016-12-21 07:45:38', '2016-12-21 07:45:38'),
(6045, 'Friend Request', 18, '2016-12-21 08:17:32', '2016-12-21 08:17:32'),
(388, 'Batman', 18, '2016-12-21 08:29:46', '2016-12-21 08:29:46'),
(5682, '10 Cloverfield Lane', 18, '2016-12-21 08:46:10', '2016-12-21 08:46:10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `email` text NOT NULL,
  `first_name` text NOT NULL,
  `last_name` text NOT NULL,
  `password` text NOT NULL,
  `profilePicture` text NOT NULL,
  `verification` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `first_name`, `last_name`, `password`, `profilePicture`, `verification`, `created_at`, `updated_at`) VALUES
(7, 'userone', 'user1@adotube.co.za', 'user', 'one', '$2y$10$MB41Jjmd.iyjyTb.ylIsTe6XX8tH7JI0PG.mS.EQdzckwIk481hjm', 'joker-profile.png', '', '2016-12-08 22:46:30', '2016-12-08 22:46:30'),
(8, 'usertwo', 'user2@adotube.co.za', 'user', 'two', '$2y$10$fXy5i3UFcqSBsZuS0Rza0.n/22ksmFU1dWNpi.z4zDmpd94Hq.Hey', 'joker-profile.png', '', '2016-12-08 22:46:52', '2016-12-08 22:46:52'),
(9, 'userthree', 'user3@adotube.co.za', 'user', 'three', '$2y$10$mCvC6XZAjMgPrEZ.6UzalerZTYpVC1aEUnPAEWnpzI9y74bQqox3u', 'joker-profile.png', '', '2016-12-08 22:47:11', '2016-12-08 22:47:11'),
(10, 'userfour', 'user4@adotube.co.za', 'user', 'four', '$2y$10$CSf0OSw2IbZBEdarMVRQYuR62DjOJ79nelJZtwkX0v7qeUxw.cfOe', 'joker-profile.png', '', '2016-12-08 22:47:32', '2016-12-08 22:47:32'),
(18, 'amarquez', 'amarquez@student.42.fr', 'Andre', 'Marquez', 'N/A', 'profile-584aa4b8b1ec4.jpg', '', '2016-12-09 12:34:00', '2016-12-09 02:34:00'),
(19, 'Andre-101870428491245845761', 'andreantoniomarques19@gmail.com', 'Andre', 'Marques', 'N/A', 'joker-profile.png', '', '2016-12-09 01:46:55', '2016-12-09 01:46:55'),
(20, 'mmodise', 'mmodise@student.42.fr', 'Moagi', 'Modise', 'N/A', 'profile-585a7aa7ab8e9.jpg', '', '2016-12-21 12:50:47', '2016-12-21 02:50:47'),
(22, 'thmkhwan', 'thmkhwan@student.42.fr', 'Thandeka', 'Mkhwanazi', 'N/A', 'joker-profile.png', '', '2016-12-21 02:55:22', '2016-12-21 02:55:22'),
(23, 'ktshikot', 'ktshikot@student.42.fr', 'Khuthadzo', 'Tshikotshi', 'N/A', 'joker-profile.png', '', '2016-12-21 03:03:16', '2016-12-21 03:03:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
