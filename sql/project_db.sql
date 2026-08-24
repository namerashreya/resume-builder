-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 13, 2025 at 06:11 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project`
--

-- --------------------------------------------------------

--
-- Table structure for table `resumes`
--

CREATE TABLE `resumes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `data` text NOT NULL,
  `template_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `resumes`
--

INSERT INTO `resumes` (`id`, `user_id`, `data`, `template_id`, `created_at`, `updated_at`) VALUES
(1, 1, '{\"personal\":{\"name\":\"Gungun\",\"email\":\"gun@gmail.com\",\"phone\":\"123456789\",\"address\":\"fnvjjkvfjndjvsdbbdshfcb\"},\"summary\":\"love to eat food all the time\",\"education\":{\"school\":\"atmiya\",\"degree\":\"bca\",\"year\":\"2023\"},\"experience\":{\"company\":\"abc\",\"role\":\"q n a\",\"duration\":\"1 years\",\"description\":\"knjbjhvjgvhg h\"},\"skills\":\"php\",\"projects\":{\"title\":\"123\",\"desc\":\"hbvyfytftrrcdtrt\",\"link\":\"www.google.com\"}}', 1, '2025-07-30 05:03:32', '2025-07-30 05:03:32'),
(2, 1, '{\"personal\":{\"name\":\"x.c nxzn\",\"email\":\"gun@gmail.com\",\"phone\":\"12346789\",\"address\":\"bjbjjjkkj\"},\"summary\":\"heyyy\",\"education\":{\"school\":\"atmiya\",\"degree\":\"bca\",\"year\":\"2025\"},\"experience\":{\"company\":\"it\",\"role\":\"no\",\"duration\":\"no\",\"description\":\"ejfjerfkr\"},\"skills\":\"php\",\"projects\":{\"title\":\"web\",\"desc\":\"njdncjdnic\",\"link\":\"www.google.com\"}}', 1, '2025-07-30 05:25:26', '2025-07-30 05:25:26'),
(4, 1, '{\"personal\":{\"name\":\"shreee\",\"email\":\"shreya@gmail.com\",\"phone\":\"789456123\",\"address\":\"kdcbsdgcblsjg\"},\"summary\":\"hello world!\",\"education\":{\"school\":\"modi\",\"degree\":\"12th passed out\",\"year\":\"2023\"},\"experience\":{\"company\":\"no\",\"role\":\"no\",\"duration\":\"no\",\"description\":\"No\"},\"skills\":\"cjkjcsnc,sleep,maggie eating\",\"projects\":{\"title\":\"nothing to show\",\"desc\":\"cjjvcnh\",\"link\":\"\"}}', 9, '2025-09-12 05:12:28', '2025-09-18 02:50:51');

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

CREATE TABLE `templates` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `preview_image` varchar(255) DEFAULT NULL,
  `html_structure` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'gun', 'gun@gmail.com', '$2y$10$cfhNDuoz258k8ZbU4e82..4RY3JdK2nEJ5L.idV9a2qDwQPW/0GFK', 'user', '2025-07-29 14:51:34'),
(2, 'Shreya N', 'shreya@gmail.com', '$2y$10$oCSFZcB14pnSacMoMKP2q.S0FbDsykiFbIMbW/tQ542aLcf6dWfB6', 'user', '2025-08-20 04:37:30'),
(3, 'Forummm', 'rum@gmail.com', '$2y$10$KtqI1qjvnnwAjI7cyogDlu8A.gjfil/eqvarFYk6D8OXzgM3n1T6m', 'user', '2025-09-03 06:57:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `resumes`
--
ALTER TABLE `resumes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `resumes`
--
ALTER TABLE `resumes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `templates`
--
ALTER TABLE `templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `resumes`
--
ALTER TABLE `resumes`
  ADD CONSTRAINT `resumes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
