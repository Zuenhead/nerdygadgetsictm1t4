-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 08, 2022 at 09:22 PM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 8.0.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nerdygadgets`
--

-- --------------------------------------------------------

--
-- Table structure for table `useraccounts`
--

CREATE TABLE `useraccounts` (
  `PersonID` int(11) NOT NULL,
  `UserName` varchar(50) NOT NULL,
  `EmailAddress` varchar(50) NOT NULL,
  `HashedPassword` varchar(256) NOT NULL,
  `PhoneNumber` varchar(20) NOT NULL,
  `DeliveryCityID` int(11) NOT NULL,
  `DeliveryPostalCode` varchar(11) NOT NULL,
  `DeliveryAddress` varchar(100) NOT NULL,
  `Nieuwsbrief` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `useraccounts`
--

INSERT INTO `useraccounts` (`PersonID`, `UserName`, `EmailAddress`, `HashedPassword`, `PhoneNumber`, `DeliveryCityID`, `DeliveryPostalCode`, `DeliveryAddress`, `Nieuwsbrief`) VALUES
(4001, 'Sven Test', 'demo@gmail.com', '$2y$10$mkT6xWUHW1hCeQbRyQaEgeWw9iTpT0NT8Cza/aMybkqnw/G7XyiNK', ' 08000432', 38186, '6969 AR', 'teststraat 69', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `useraccounts`
--
ALTER TABLE `useraccounts`
  ADD PRIMARY KEY (`PersonID`),
  ADD UNIQUE KEY `EmailAddress` (`EmailAddress`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
