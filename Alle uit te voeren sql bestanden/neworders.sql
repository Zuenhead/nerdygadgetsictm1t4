use nerdygadgets;

-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 22 dec 2021 om 19:30
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
-- Tabelstructuur voor tabel `neworders`
--
drop table if exists `neworders`;
CREATE TABLE `neworders` (
  `OrderID` int(11) NOT NULL,
  `PersonID` int(11) DEFAULT NULL,
  `OrderDate` date NOT NULL,
  `SubTotaal` decimal(18,2) NOT NULL,
  `Verzendkosten` decimal(18,2) NOT NULL,
  `Korting` decimal(18,2) NOT NULL,
  `Totaal` decimal(18,2) NOT NULL,
  `DeliveryAddress` varchar(100) NOT NULL,
  `DeliveryCityID` int(11) NOT NULL,
  `DeliveryPostalCode` varchar(11) NOT NULL,
  `PhoneNumber` varchar(20) NOT NULL,
  `UserName` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `neworders`
--

INSERT INTO `neworders` (`OrderID`, `PersonID`, `OrderDate`, `SubTotaal`, `Verzendkosten`, `Korting`, `Totaal`, `DeliveryAddress`, `DeliveryCityID`, `DeliveryPostalCode`, `PhoneNumber`, `UserName`) VALUES
(1, 4001, '2021-12-22', '248.12', '0.00', '0.00', '248.12', 'JanPietStraat 123', 773, '6114 HC', '0800 - 0432', 'Ligma Bols'),
(2, 0, '2021-12-22', '73.97', '0.00', '0.00', '73.97', 'RamsayStraat 42', 38212, '6114 HC', '0800 - 0432', 'Gordon Ramsay');

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `neworders`
--
ALTER TABLE `neworders`
  ADD PRIMARY KEY (`OrderID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
