-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 25, 2011 at 02:27 AM
-- Server version: 5.1.41
-- PHP Version: 5.3.2-1ubuntu4.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `pushpool`
--

-- --------------------------------------------------------

--
-- Table structure for table `accountBalance`
--

CREATE TABLE IF NOT EXISTS `accountBalance` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `userId` int(255) NOT NULL,
  `balance` varchar(40) DEFAULT NULL,
  `sendAddress` varchar(255) DEFAULT '',
  `paid` varchar(40) DEFAULT '0',
  `threshold` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `userId` (`userId`),
  KEY `b_userId` (`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=464 ;

--
-- Triggers `accountBalance`
--
DROP TRIGGER IF EXISTS `accountBalance_update`;
DELIMITER //
CREATE TRIGGER `accountBalance_update` BEFORE UPDATE ON `accountBalance`
 FOR EACH ROW INSERT INTO accountBalanceHistory (userId, balance, sendAddress, paid, threshold) VALUES (OLD.userId, OLD.balance, OLD.sendAddress, OLD.paid, OLD.threshold)
//
DELIMITER ;
DROP TRIGGER IF EXISTS `accountBalance_delete`;
DELIMITER //
CREATE TRIGGER `accountBalance_delete` BEFORE DELETE ON `accountBalance`
 FOR EACH ROW INSERT INTO accountBalanceHistory (userId, balance, sendAddress, paid, threshold) VALUES (OLD.userId, OLD.balance, OLD.sendAddress, OLD.paid, OLD.threshold)
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `accountBalanceHistory`
--

CREATE TABLE IF NOT EXISTS `accountBalanceHistory` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(255) NOT NULL,
  `balance` varchar(45) DEFAULT NULL,
  `sendAddress` varchar(255) DEFAULT NULL,
  `paid` varchar(45) DEFAULT NULL,
  `threshold` tinyint(4) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `accountBalanceHistory_id1` (`userId`),
  KEY `userId_timestamp` (`userId`,`timestamp`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=860 ;

-- --------------------------------------------------------

--
-- Table structure for table `accountHistory`
--

CREATE TABLE IF NOT EXISTS `accountHistory` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(255) NOT NULL,
  `balanceDelta` varchar(45) NOT NULL,
  `blockNumber` int(11) NOT NULL,
  `userShares` int(11) NOT NULL,
  `totalShares` int(11) NOT NULL,
  `sitePercent` int(11) NOT NULL,
  `donatePercent` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `accountHistory_userId` (`userId`),
  KEY `accountHistory_userId_timestamp` (`userId`,`timestamp`),
  KEY `accountHistory_blockNumber` (`blockNumber`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=760 ;

-- --------------------------------------------------------

--
-- Table structure for table `locks`
--

CREATE TABLE IF NOT EXISTS `locks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`),
  KEY `name` (`name`)
) ENGINE=MEMORY  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Table structure for table `networkBlocks`
--

CREATE TABLE IF NOT EXISTS `networkBlocks` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `blockNumber` int(255) NOT NULL,
  `timestamp` int(255) NOT NULL,
  `accountAddress` varchar(255) NOT NULL,
  `confirms` int(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `blockNumber_index` (`blockNumber`),
  KEY `confirms_index` (`confirms`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1391 ;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `timestamp` int(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `pool_worker`
--

CREATE TABLE IF NOT EXISTS `pool_worker` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `associatedUserId` int(255) NOT NULL,
  `username` char(50) DEFAULT NULL,
  `password` char(255) DEFAULT NULL,
  `active` tinyint(4) DEFAULT '0',
  `hashrate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `p_username` (`username`),
  KEY `username_userid` (`username`,`associatedUserId`),
  KEY `userid` (`associatedUserId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=949 ;

--
-- Triggers `pool_worker`
--
DROP TRIGGER IF EXISTS `workerHashrates_pw_update`;
DELIMITER //
CREATE TRIGGER `workerHashrates_pw_update` BEFORE UPDATE ON `pool_worker`
 FOR EACH ROW BEGIN
    IF NEW.hashrate IS NOT NULL AND NEW.hashrate != OLD.hashrate THEN
        INSERT INTO workerHashrates (hashrate, userId, username) VALUES (NEW.hashrate, NEW.associatedUserId, NEW.username);
    END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `roundDetails`
--

CREATE TABLE IF NOT EXISTS `roundDetails` (
  `roundId` int(10) unsigned NOT NULL,
  `userId` int(255) NOT NULL,
  `shares` int(10) unsigned NOT NULL,
  `estimate` varchar(45) NOT NULL,
  PRIMARY KEY (`roundId`,`userId`),
  UNIQUE KEY `UNIQUE` (`roundId`,`userId`),
  KEY `fk_roundDetails_round1` (`roundId`),
  KEY `fk_roundDetails_webUsers1` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rounds`
--

CREATE TABLE IF NOT EXISTS `rounds` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `blockNumber` int(255) NOT NULL,
  `shares` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blockNumber_UNIQUE` (`blockNumber`),
  KEY `round_blockNumber` (`blockNumber`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=198 ;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `setting` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`setting`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `shares`
--

CREATE TABLE IF NOT EXISTS `shares` (
  `id` bigint(30) NOT NULL AUTO_INCREMENT,
  `rem_host` varchar(255) NOT NULL,
  `username` varchar(120) NOT NULL,
  `our_result` enum('Y','N') NOT NULL,
  `upstream_result` enum('Y','N') DEFAULT NULL,
  `reason` varchar(50) DEFAULT NULL,
  `solution` varchar(257) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userId` int(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `fk_webUsers` (`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7677488 ;

-- --------------------------------------------------------

--
-- Table structure for table `shares_history`
--

CREATE TABLE IF NOT EXISTS `shares_history` (
  `id` bigint(30) NOT NULL AUTO_INCREMENT,
  `counted` int(1) NOT NULL COMMENT 'BOOLEAN) Tells server if it used these shares for counting',
  `blockNumber` int(255) NOT NULL,
  `rem_host` varchar(255) NOT NULL,
  `username` varchar(120) NOT NULL,
  `our_result` enum('Y','N') NOT NULL,
  `upstream_result` enum('Y','N') DEFAULT NULL,
  `reason` varchar(50) DEFAULT NULL,
  `solution` varchar(257) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `score` double(23,2) DEFAULT NULL,
  `userId` int(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sh_blocknumber` (`blockNumber`),
  KEY `sh_counted` (`counted`),
  KEY `time` (`time`,`username`),
  KEY `upstream_result` (`upstream_result`),
  KEY `our_result_username` (`our_result`,`username`),
  KEY `counted_our_result_username_blocknumber` (`counted`,`our_result`,`username`,`blockNumber`),
  KEY `blockNumber_username` (`blockNumber`,`username`),
  KEY `fk_sh_webUsers` (`userId`),
  KEY `sh_counted_ourresult_userId_blockNumber` (`counted`,`our_result`,`userId`,`blockNumber`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13743078 ;

-- --------------------------------------------------------

--
-- Table structure for table `userHashrates`
--

CREATE TABLE IF NOT EXISTS `userHashrates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userId` int(255) NOT NULL,
  `hashrate` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `timestamp` (`timestamp`),
  KEY `userHashrates_id1` (`userId`),
  KEY `userId_timestamp` (`userId`,`timestamp`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=602993 ;

-- --------------------------------------------------------

--
-- Table structure for table `webUsers`
--

CREATE TABLE IF NOT EXISTS `webUsers` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `admin` int(1) NOT NULL,
  `username` varchar(40) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL COMMENT 'Assocaited email: used for validating users, and re-setting passwords',
  `emailAuthPin` varchar(10) NOT NULL COMMENT 'The pin required to authorize that email address',
  `secret` varchar(10) NOT NULL,
  `loggedIp` varchar(255) NOT NULL,
  `sessionTimeoutStamp` int(255) NOT NULL,
  `accountLocked` int(255) NOT NULL COMMENT 'This is the timestamp when the account will be unlocked(usually used to lock accounts that are trying to be bruteforced)',
  `accountFailedAttempts` int(2) NOT NULL COMMENT 'This counts the number of failed attempts for web login',
  `pin` varchar(255) NOT NULL COMMENT 'four digit pin to allow account changes',
  `share_count` int(32) NOT NULL DEFAULT '0',
  `stale_share_count` int(32) NOT NULL DEFAULT '0',
  `shares_this_round` int(32) NOT NULL DEFAULT '0',
  `api_key` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `activeEmail` int(1) DEFAULT NULL,
  `hashrate` int(11) DEFAULT NULL,
  `donate_percent` varchar(11) DEFAULT '0',
  `round_estimate` varchar(40) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=431 ;

--
-- Triggers `webUsers`
--
DROP TRIGGER IF EXISTS `userHashrates_wu_update`;
DELIMITER //
CREATE TRIGGER `userHashrates_wu_update` BEFORE UPDATE ON `webUsers`
 FOR EACH ROW INSERT INTO userHashrates (userId, hashrate) VALUES (NEW.id, NEW.hashrate)
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `workerHashrates`
--

CREATE TABLE IF NOT EXISTS `workerHashrates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hashrate` int(11) NOT NULL DEFAULT '0',
  `userId` int(255) NOT NULL,
  `username` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `fk_workerHashrates_userId` (`userId`),
  KEY `userId_timestamp` (`userId`,`timestamp`),
  KEY `timestamp` (`timestamp`),
  KEY `userId_username_timestamp` (`userId`,`username`,`timestamp`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=77030 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accountBalanceHistory`
--
ALTER TABLE `accountBalanceHistory`
  ADD CONSTRAINT `accountBalanceHistory_id1` FOREIGN KEY (`userId`) REFERENCES `webUsers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `accountHistory`
--
ALTER TABLE `accountHistory`
  ADD CONSTRAINT `accountHistory_userId` FOREIGN KEY (`userId`) REFERENCES `webUsers` (`id`);

--
-- Constraints for table `roundDetails`
--
ALTER TABLE `roundDetails`
  ADD CONSTRAINT `fk_roundDetails_round1` FOREIGN KEY (`roundId`) REFERENCES `rounds` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_roundDetails_webUsers1` FOREIGN KEY (`userId`) REFERENCES `webUsers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `shares`
--
ALTER TABLE `shares`
  ADD CONSTRAINT `fk_webUsers` FOREIGN KEY (`userId`) REFERENCES `webUsers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `shares_history`
--
ALTER TABLE `shares_history`
  ADD CONSTRAINT `fk_sh_webUsers` FOREIGN KEY (`userId`) REFERENCES `webUsers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `userHashrates`
--
ALTER TABLE `userHashrates`
  ADD CONSTRAINT `userHashrates_id1` FOREIGN KEY (`userId`) REFERENCES `webUsers` (`id`);

--
-- Constraints for table `workerHashrates`
--
ALTER TABLE `workerHashrates`
  ADD CONSTRAINT `fk_workerHashrates_userId` FOREIGN KEY (`userId`) REFERENCES `webUsers` (`id`);
