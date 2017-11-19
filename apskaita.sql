-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Darbinė stotis: localhost
-- Atlikimo laikas: 2017 m. Lap 19 d. 20:53
-- Serverio versija: 5.5.24-log
-- PHP versija: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Duomenų bazė: `apskaita`
--

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `configs`
--

CREATE TABLE IF NOT EXISTS `configs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config_name` varchar(255) NOT NULL,
  `config_value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `config_name` (`config_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Sukurta duomenų kopija lentelei `configs`
--

INSERT INTO `configs` (`id`, `config_name`, `config_value`) VALUES
(1, 'web_title', 'Sporto klubo apskaita'),
(2, 'web_location', 'http://localhost/apskaita/'),
(3, 'user_log', 'true'),
(4, 'web_domain', 'localhost/apskaita'),
(5, 'pvm', '21'),
(6, 'company_code', '148202087'),
(7, 'company_address', 'Kranto g. 36, Panevėžys'),
(8, 'company_name', 'Panevėžio sporto klubas "Voras"');

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `members`
--

CREATE TABLE IF NOT EXISTS `members` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) CHARACTER SET utf8 NOT NULL,
  `lastname` varchar(45) CHARACTER SET utf8 NOT NULL,
  `telephone` varchar(15) NOT NULL,
  `birthday` varchar(10) NOT NULL,
  `address` varchar(255) CHARACTER SET utf8 NOT NULL,
  `otherInfo` text CHARACTER SET utf8 NOT NULL,
  `status` tinyint(1) NOT NULL,
  `createTime` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=71 ;

--
-- Sukurta duomenų kopija lentelei `members`
--

INSERT INTO `members` (`id`, `name`, `lastname`, `telephone`, `birthday`, `address`, `otherInfo`, `status`, `createTime`) VALUES
(45, 'Linas', 'Mikalauskas', '865672147', '1992-10-30', '', '', 1, 1400886884),
(46, 'Tomas', 'Tomaitis', '864747447', '1987-08-25', '', '', 1, 1400886920),
(47, 'Vytautas', 'Adomas', '865487145', '1990-09-19', '', '', 1, 1400928547),
(48, 'Gintas', 'Blažys', '865417417', '1984-06-16', '', '', 1, 1400928582),
(49, 'Arnoldas', 'Balsys', '865471475', '1995-02-20', '', '', 1, 1400928620),
(50, 'Tadas', 'Jonulis', '865471474', '1995-03-24', '', '', 1, 1400928665),
(51, 'Gediminas', 'Jonkus', '865478474', '1987-06-15', '', '', 1, 1400928691),
(52, 'Ernestas', 'Jokūbauskas', '865474714', '1985-06-19', '', '', 1, 1400928721),
(53, 'Agnė', 'Liuvinaitė', '865474741', '1994-01-07', '', '', 1, 1400928785),
(54, 'Tomas', 'Lapė', '865471868', '1985-02-26', '', '', 1, 1400928822),
(55, 'Haroldas', 'Narušis', '865479889', '1990-06-23', '', '', 1, 1400928861),
(56, 'Andrius', 'Kaminskas', '865474187', '1986-09-11', '', '', 1, 1400928903),
(57, 'Aldas', 'Šapoka', '865487841', '2001-12-24', '', '', 1, 1400929008),
(58, 'Lukas', 'Talaišis', '865989812', '1992-03-26', '', '', 1, 1400929055),
(59, 'Rolandas', 'Butvilas', '864871453', '1988-06-12', '', '', 1, 1400929151),
(60, 'Dominykas', 'Sapatka', '864874712', '2004-09-30', '', '', 1, 1400929187),
(61, 'Tadas', 'Jokutis', '895484155', '1994-06-24', '', '', 1, 1400929320),
(62, 'Vaidas', 'Urba', '865921247', '1987-05-18', '', '', 1, 1400929708),
(63, 'Benas', 'Rakauskas', '865478912', '1989-06-24', '', '', 1, 1400930012),
(64, 'Simas', 'Adomaitis', '869932147', '1994-05-28', '', '', 1, 1400930054),
(65, 'Tomas', 'Banys', '865231289', '1999-05-30', '', '', 1, 1400930099),
(66, 'Lukas', 'Sakalas', '869531451', '1985-04-23', '', '', 1, 1400931214),
(67, 'Aivaras', 'Mažeika', '865414791', '1994-02-25', '', '', 1, 1400931543),
(68, 'Tomas', 'Tomaitis', '864871245', '1989-12-20', '', '', 0, 1401006874),
(69, 'tomas', 'tomaitis', '865457471', '1985-09-24', '', '', 1, 1401102454),
(70, 'gedimas', 'gediminaitis', '86574747', '2010-12-31', '', '', 0, 1401354694);

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `session_id` char(32) NOT NULL,
  `previous_id` char(32) NOT NULL,
  `ip_address` char(8) NOT NULL,
  `user_agent` varchar(120) NOT NULL,
  `joined` int(10) NOT NULL,
  `last_activity` int(10) NOT NULL,
  `user_data` text NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `taxes`
--

CREATE TABLE IF NOT EXISTS `taxes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `memberId` int(10) NOT NULL,
  `year` int(4) NOT NULL,
  `month` int(2) NOT NULL,
  `taxes` int(10) NOT NULL,
  `createTime` int(10) NOT NULL,
  `updateTime` int(10) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=106 ;

--
-- Sukurta duomenų kopija lentelei `taxes`
--

INSERT INTO `taxes` (`id`, `memberId`, `year`, `month`, `taxes`, `createTime`, `updateTime`, `status`) VALUES
(38, 46, 2014, 5, 80, 1400887023, 1400887048, 0),
(39, 46, 2014, 4, 80, 1400887059, 0, 0),
(40, 45, 2014, 4, 80, 1400887075, 1400930783, 0),
(41, 45, 2014, 5, 80, 1400887085, 0, 0),
(42, 46, 2014, 3, 80, 1400887307, 0, 0),
(43, 46, 2014, 2, 80, 1400887313, 1400930951, 0),
(44, 46, 2014, 1, 80, 1400887323, 1400930955, 0),
(45, 63, 2014, 1, 80, 1400930758, 0, 0),
(46, 63, 2014, 2, 80, 1400930762, 0, 0),
(47, 63, 2014, 3, 80, 1400930766, 0, 0),
(48, 63, 2014, 4, 80, 1400930769, 0, 0),
(49, 63, 2014, 5, 80, 1400930772, 0, 0),
(50, 45, 2014, 3, 80, 1400930788, 0, 0),
(51, 45, 2014, 2, 80, 1400930791, 0, 0),
(52, 45, 2014, 1, 80, 1400930797, 0, 0),
(53, 45, 2013, 12, 80, 1400930805, 0, 0),
(54, 45, 2013, 11, 80, 1400930814, 0, 0),
(55, 45, 2013, 10, 80, 1400930823, 0, 0),
(56, 45, 2013, 9, 80, 1400930832, 0, 0),
(57, 45, 2013, 8, 80, 1400930840, 0, 0),
(58, 45, 2013, 7, 80, 1400930846, 0, 0),
(59, 45, 2013, 6, 80, 1400930852, 0, 0),
(60, 45, 2013, 5, 80, 1400930860, 0, 0),
(61, 45, 2013, 4, 80, 1400930865, 0, 0),
(62, 45, 2013, 3, 80, 1400930871, 0, 0),
(63, 45, 2013, 2, 80, 1400930875, 0, 0),
(64, 45, 2013, 1, 80, 1400930880, 0, 0),
(65, 47, 2014, 4, 80, 1400930970, 0, 0),
(66, 47, 2014, 3, 80, 1400930975, 0, 0),
(67, 47, 2014, 2, 80, 1400930984, 0, 0),
(68, 48, 2014, 5, 80, 1400930995, 0, 0),
(69, 48, 2014, 4, 80, 1400931000, 0, 0),
(70, 48, 2014, 3, 80, 1400931004, 0, 0),
(71, 49, 2014, 5, 80, 1400931009, 0, 0),
(72, 50, 2014, 4, 80, 1400931041, 0, 0),
(73, 50, 2014, 3, 80, 1400931045, 1400931049, 0),
(74, 50, 2014, 5, 80, 1400931052, 0, 0),
(75, 50, 2014, 2, 80, 1400931056, 0, 0),
(76, 51, 2014, 1, 80, 1400931067, 0, 0),
(77, 51, 2013, 12, 80, 1400931073, 0, 0),
(78, 51, 2013, 11, 80, 1400931080, 0, 0),
(79, 59, 2014, 5, 80, 1400931097, 0, 0),
(80, 60, 2014, 5, 80, 1400931103, 0, 0),
(81, 55, 2014, 5, 80, 1400931114, 0, 0),
(82, 55, 2014, 4, 80, 1400931118, 0, 0),
(83, 55, 2014, 3, 80, 1400931123, 0, 0),
(84, 55, 2014, 2, 80, 1400931128, 0, 0),
(85, 55, 2014, 1, 80, 1400931134, 0, 0),
(86, 64, 2014, 5, 80, 1400931567, 0, 0),
(87, 65, 2014, 5, 80, 1400931571, 0, 0),
(88, 53, 2014, 5, 80, 1400931593, 0, 0),
(89, 53, 2014, 4, 80, 1400931602, 0, 0),
(90, 53, 2014, 3, 80, 1400931608, 0, 0),
(91, 53, 2014, 2, 80, 1400931614, 0, 0),
(92, 53, 2014, 1, 80, 1400931619, 0, 0),
(93, 52, 2014, 5, 80, 1400931624, 0, 0),
(94, 66, 2014, 3, 80, 1400931671, 0, 0),
(95, 67, 2014, 4, 80, 1400931756, 0, 0),
(96, 67, 2014, 3, 80, 1400931760, 0, 0),
(97, 67, 2014, 2, 80, 1400931764, 0, 0),
(98, 68, 2014, 5, 80, 1401006897, 0, 0),
(99, 57, 2014, 5, 80, 1401041750, 0, 0),
(100, 69, 2014, 5, 80, 1401102530, 1401102571, 0),
(101, 69, 2014, 4, 80, 1401125650, 0, 0),
(102, 58, 2014, 5, 80, 1401276140, 0, 0),
(103, 67, 2014, 5, 80, 1401291903, 0, 0),
(104, 66, 2014, 5, 80, 1401354801, 0, 0),
(105, 69, 2015, 5, 80, 1432745908, 0, 0);

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(45) NOT NULL,
  `password` char(32) NOT NULL,
  `permissions` int(32) NOT NULL,
  `createTime` int(11) NOT NULL,
  `lastLogin` int(11) NOT NULL,
  `lastIp` char(8) NOT NULL,
  `lastChange` int(11) NOT NULL,
  `adminChangeId` int(11) NOT NULL,
  `adminChangeTime` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `login` (`login`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Sukurta duomenų kopija lentelei `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `permissions`, `createTime`, `lastLogin`, `lastIp`, `lastChange`, `adminChangeId`, `adminChangeTime`, `status`) VALUES
(1, 'linas', 'qRfXDm5kzqPvW+CO884uBRgMIa0=', 255, 1400586249, 1432745796, '7f000001', 0, 0, 0, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
