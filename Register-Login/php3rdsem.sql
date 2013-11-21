-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 03, 2013 at 02:54 PM
-- Server version: 5.5.32
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `php3rdsem`
--
CREATE DATABASE IF NOT EXISTS `php3rdsem` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `php3rdsem`;

-- --------------------------------------------------------

--
-- Table structure for table `forum_thread`
--

CREATE TABLE IF NOT EXISTS `forum_thread` (
  `thread_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `created_by` varchar(70) DEFAULT NULL,
  PRIMARY KEY (`thread_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `email` varchar(70) NOT NULL,
  `name` text NOT NULL,
  `password` text NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`email`, `name`, `password`) VALUES
('', 'dhj', '35139ef894b28b73bea022755166a23933c7d9cb'),
('drg@a.com', 'roxana', 'ff39796487e85a7066e18d814bcb63856de6cfff'),
('e@jknn', 'dgdg', '949e336090e6a14ff74d407b6df6b19833142ade'),
('fgd@jhjbh', 'gdgh', '3b47beb9c2f06bbf6aba12bd6cde3473d542169e'),
('r@r.com', 'RRR', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220'),
('roxana@a.com', 'roxana', 'e145a0498bd2dc00702f4720dc014e74e1b5e3e8'),
('thbh@kea.dk', 'Thomas', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3'),
('timmy@t.com', 'timmy', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220'),
('user@user.com', 'user', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
