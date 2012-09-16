-- phpMyAdmin SQL Dump
-- version 3.3.7deb7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 17. September 2012 um 01:51
-- Server Version: 5.1.63
-- PHP-Version: 5.3.3-7+squeeze14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Datenbank: `nr2d`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_area`
--

CREATE TABLE IF NOT EXISTS `tbl_area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `name` varchar(255) NOT NULL,
  `accessCode` varchar(255) NOT NULL,
  `level` smallint(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_entity`
--

CREATE TABLE IF NOT EXISTS `tbl_entity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `roomId` int(11) NOT NULL,
  `type` enum('default','bouncer','user','fragment','worker','codebit') NOT NULL,
  `created` datetime NOT NULL,
  `attack` int(11) NOT NULL,
  `defend` int(11) NOT NULL,
  `stealth` int(11) NOT NULL,
  `detect` int(11) NOT NULL,
  `eeg` int(11) NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `credits` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`,`roomId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=65 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_migration`
--

CREATE TABLE IF NOT EXISTS `tbl_migration` (
  `version` varchar(255) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_profiles`
--

CREATE TABLE IF NOT EXISTS `tbl_profiles` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `location` int(10) NOT NULL DEFAULT '1',
  `homenode` int(10) NOT NULL DEFAULT '1',
  `speed` int(10) NOT NULL DEFAULT '128',
  `credits` int(10) NOT NULL DEFAULT '1000',
  `secrating` int(10) NOT NULL DEFAULT '0',
  `stealth` int(10) NOT NULL DEFAULT '1',
  `detect` int(10) NOT NULL DEFAULT '1',
  `attack` int(10) NOT NULL DEFAULT '1',
  `defend` int(10) NOT NULL DEFAULT '1',
  `coding` int(10) NOT NULL DEFAULT '1',
  `eeg` int(10) NOT NULL DEFAULT '100',
  `willpower` int(10) NOT NULL DEFAULT '100',
  `snippets` int(10) NOT NULL DEFAULT '100',
  `decking` int(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_profiles_fields`
--

CREATE TABLE IF NOT EXISTS `tbl_profiles_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `varname` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `field_type` varchar(50) NOT NULL DEFAULT '',
  `field_size` int(3) NOT NULL DEFAULT '0',
  `field_size_min` int(3) NOT NULL DEFAULT '0',
  `required` int(1) NOT NULL DEFAULT '0',
  `match` varchar(255) NOT NULL DEFAULT '',
  `range` varchar(255) NOT NULL DEFAULT '',
  `error_message` varchar(255) NOT NULL DEFAULT '',
  `other_validator` text,
  `default` varchar(255) NOT NULL DEFAULT '',
  `widget` varchar(255) NOT NULL DEFAULT '',
  `widgetparams` text,
  `position` int(3) NOT NULL DEFAULT '0',
  `visible` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_program`
--

CREATE TABLE IF NOT EXISTS `tbl_program` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coderId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `type` enum('stealth','attack','antivirus','detect','defend','eegbooster','scanner') NOT NULL,
  `created` datetime NOT NULL,
  `rating` smallint(2) NOT NULL,
  `condition` int(4) NOT NULL,
  `maxUpgrades` smallint(2) NOT NULL,
  `upgrades` smallint(2) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `coderId` (`coderId`,`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_room`
--

CREATE TABLE IF NOT EXISTS `tbl_room` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `areaId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `type` enum('default','io','firewall','database','terminal','coproc','coding') NOT NULL,
  `level` smallint(2) NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `areaId` (`areaId`,`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=33 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_users`
--

CREATE TABLE IF NOT EXISTS `tbl_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL DEFAULT '',
  `password` varchar(128) NOT NULL DEFAULT '',
  `email` varchar(128) NOT NULL DEFAULT '',
  `activkey` varchar(128) NOT NULL DEFAULT '',
  `superuser` int(1) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT '0',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastvisit_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_username` (`username`),
  UNIQUE KEY `user_email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `tbl_profiles`
--
ALTER TABLE `tbl_profiles`
  ADD CONSTRAINT `user_profile_id` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`id`) ON DELETE CASCADE;
