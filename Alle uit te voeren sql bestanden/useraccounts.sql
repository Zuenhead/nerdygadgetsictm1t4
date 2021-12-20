-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 18 dec 2021 om 16:26
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
-- Tabelstructuur voor tabel `useraccounts`
--

CREATE TABLE `useraccounts` (
  `PersonID` int(11) NOT NULL,
  `UserName` varchar(50) NOT NULL,
  `EmailAddress` varchar(50) NOT NULL,
  `HashedPassword` varchar(256) NOT NULL,
  `PhoneNumber` varchar(20) NOT NULL,
  `DeliveryCityID` int(11) NOT NULL,
  `DeliveryPostalCode` varchar(11) NOT NULL,
  `DeliveryAddress` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `useraccounts`
--

INSERT INTO `useraccounts` (`PersonID`, `UserName`, `EmailAddress`, `HashedPassword`, `PhoneNumber`, `DeliveryCityID`, `DeliveryPostalCode`, `DeliveryAddress`) VALUES
(4001, 'Mike Oxmaul', 'demo@gmail.com', '$2y$10$LzqMMuYNwx2vR7O7O80jGuq1//8nWQSlWKKcXc.pJYfCvjOggryKK', '0800- 0432', 38212, '6114HC', 'De Wallen 69');

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `useraccounts`
--
ALTER TABLE `useraccounts`
  ADD PRIMARY KEY (`PersonID`),
  ADD UNIQUE KEY `EmailAddress` (`EmailAddress`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
