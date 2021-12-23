use nerdygadgets;
-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 22 dec 2021 om 19:29
-- Serverversie: 10.4.21-MariaDB
-- PHP-versie: 8.0.10

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
-- Tabelstructuur voor tabel `neworderlines`
--

CREATE TABLE `neworderlines` (
  `OrderLineID` int(11) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `StockItemID` int(11) NOT NULL,
  `Description` varchar(50) DEFAULT NULL,
  `Quantity` int(11) NOT NULL,
  `UnitPrice` decimal(18,2) NOT NULL,
  `TaxRate` decimal(18,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `neworderlines`
--

INSERT INTO `neworderlines` (`OrderLineID`, `OrderID`, `StockItemID`, `Description`, `Quantity`, `UnitPrice`, `TaxRate`) VALUES
(1, 1, 16, 'DBA joke mug - mind if I join you? (White)', 1, '13.00', '15.000'),
(2, 1, 222, 'Chocolate beetles 250g', 4, '8.55', '10.000'),
(3, 1, 2, 'USB rocket launcher (Gray)', 4, '25.00', '15.000'),
(4, 2, 20, 'DBA joke mug - you might be a DBA if (White)', 1, '13.00', '15.000'),
(5, 2, 138, 'Furry animal socks (Pink) S', 6, '5.00', '15.000');

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `neworderlines`
--
ALTER TABLE `neworderlines`
  ADD PRIMARY KEY (`OrderLineID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
