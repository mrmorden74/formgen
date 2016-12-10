-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 10. Dez 2016 um 16:23
-- Server-Version: 5.7.14
-- PHP-Version: 7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `formgen`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dblist`
--

CREATE TABLE `dblist` (
  `id` int(11) NOT NULL,
  `srvlist_id` int(11) NOT NULL,
  `dbname` varchar(255) NOT NULL,
  `username` varchar(55) DEFAULT NULL,
  `password` varchar(55) DEFAULT NULL,
  `projectname` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `privilegies`
--

CREATE TABLE `privilegies` (
  `idprivilegies` int(11) NOT NULL,
  `idsrv` int(11) DEFAULT NULL,
  `iddb` int(11) DEFAULT NULL,
  `idtbl` int(11) DEFAULT NULL,
  `idusr` int(11) DEFAULT NULL,
  `allow` tinyint(1) DEFAULT NULL,
  `deny` tinyint(1) DEFAULT NULL,
  `srvlist_id` int(11) DEFAULT NULL,
  `dblist_id` int(11) DEFAULT NULL,
  `tablelist_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `privilegies`
--

INSERT INTO `privilegies` (`idprivilegies`, `idsrv`, `iddb`, `idtbl`, `idusr`, `allow`, `deny`, `srvlist_id`, `dblist_id`, `tablelist_id`, `user_id`) VALUES
(1, NULL, NULL, NULL, 2, 1, NULL, NULL, NULL, NULL, 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `srvlist`
--

CREATE TABLE `srvlist` (
  `id` int(11) NOT NULL,
  `server` varchar(255) NOT NULL,
  `srvtype` varchar(55) NOT NULL,
  `username` varchar(55) NOT NULL,
  `password` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tablelist`
--

CREATE TABLE `tablelist` (
  `id` int(11) NOT NULL,
  `dbid` int(11) NOT NULL,
  `tablename` varchar(255) NOT NULL,
  `showdb` tinyint(1) NOT NULL,
  `formname` varchar(255) DEFAULT NULL,
  `formexist` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(45) CHARACTER SET utf8mb4 NOT NULL,
  `password` varchar(95) CHARACTER SET utf8mb4 NOT NULL,
  `type` varchar(5) CHARACTER SET utf8mb4 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `type`) VALUES
(1, 'user', '$2y$10$/DhkaxjPcDMbhHLP.7jIn.fpzwHVLoR7BMRMm22H5InKEAHrZbqG.', 'user'),
(2, 'admin', '$2y$10$jhtPYpAIZKvQ4yIM3wqsjeR5nabE8aYE61G3FGuG8afo4ZSjpnC52', 'admin');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `dblist`
--
ALTER TABLE `dblist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `fk_dblist_srvlist1_idx` (`srvlist_id`);

--
-- Indizes für die Tabelle `privilegies`
--
ALTER TABLE `privilegies`
  ADD PRIMARY KEY (`idprivilegies`),
  ADD UNIQUE KEY `idprivilegies_UNIQUE` (`idprivilegies`),
  ADD UNIQUE KEY `kombi` (`idsrv`,`iddb`,`idtbl`,`idusr`),
  ADD KEY `fk_privilegies_srvlist1_idx` (`srvlist_id`),
  ADD KEY `fk_privilegies_dblist1_idx` (`dblist_id`),
  ADD KEY `fk_privilegies_tablelist1_idx` (`tablelist_id`),
  ADD KEY `fk_privilegies_user1_idx` (`user_id`);

--
-- Indizes für die Tabelle `srvlist`
--
ALTER TABLE `srvlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indizes für die Tabelle `tablelist`
--
ALTER TABLE `tablelist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tablename` (`tablename`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `dbid` (`dbid`);

--
-- Indizes für die Tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `dblist`
--
ALTER TABLE `dblist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT für Tabelle `privilegies`
--
ALTER TABLE `privilegies`
  MODIFY `idprivilegies` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT für Tabelle `srvlist`
--
ALTER TABLE `srvlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT für Tabelle `tablelist`
--
ALTER TABLE `tablelist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `dblist`
--
ALTER TABLE `dblist`
  ADD CONSTRAINT `fk_dblist_srvlist1` FOREIGN KEY (`srvlist_id`) REFERENCES `srvlist` (`id`);

--
-- Constraints der Tabelle `privilegies`
--
ALTER TABLE `privilegies`
  ADD CONSTRAINT `fk_privilegies_dblist1` FOREIGN KEY (`dblist_id`) REFERENCES `dblist` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_privilegies_srvlist1` FOREIGN KEY (`srvlist_id`) REFERENCES `srvlist` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_privilegies_tablelist1` FOREIGN KEY (`tablelist_id`) REFERENCES `tablelist` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_privilegies_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `tablelist`
--
ALTER TABLE `tablelist`
  ADD CONSTRAINT `tablelist_ibfk_1` FOREIGN KEY (`dbid`) REFERENCES `dblist` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
