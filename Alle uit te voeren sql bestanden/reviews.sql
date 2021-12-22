-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 22, 2021 at 03:08 PM
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
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `ReviewID` int(11) NOT NULL,
  `PersonID` int(11) NOT NULL,
  `StockItemID` int(11) NOT NULL,
  `Rating` tinyint(1) NOT NULL,
  `Title` varchar(100) NOT NULL,
  `Comment` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`ReviewID`, `PersonID`, `StockItemID`, `Rating`, `Title`, `Comment`) VALUES
(3, 4001, 138, 5, 'mooi man', 'kei goeie product'),
(4, 4001, 138, 4, 'titel', 'beschrijving'),
(5, 4001, 138, 1, 'matig', 'boeie'),
(6, 4001, 138, 3, 'gemiddeld', 'kan beter'),
(7, 4001, 138, 5, 'titel', 'beschrijving'),
(8, 4001, 138, 5, 'titel', 'beschrijving'),
(9, 4001, 138, 2, 'titel', 'beschrijving'),
(10, 4001, 138, 2, 'titel', 'beschrijving'),
(11, 4001, 138, 1, 'titel', 'beschrijving'),
(12, 4001, 138, 4, 'titel', 'beschrijving'),
(13, 4001, 138, 5, 'titel', 'beschrijving');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`ReviewID`),
  ADD KEY `FK_Reviews_StockItemID_StockItems` (`StockItemID`),
  ADD KEY `FK_Reviews_PersonID_UserAccounts` (`PersonID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `ReviewID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `FK_Reviews_CustomerID_Customers` FOREIGN KEY (`PersonID`) REFERENCES `customers` (`CustomerID`),
  ADD CONSTRAINT `FK_Reviews_PersonID_UserAccounts` FOREIGN KEY (`PersonID`) REFERENCES `useraccounts` (`PersonID`),
  ADD CONSTRAINT `FK_Reviews_StockItemID_StockItems` FOREIGN KEY (`StockItemID`) REFERENCES `stockitems` (`StockItemID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
