-- phpMyAdmin SQL Dump
-- version 3.3.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 17, 2013 at 08:17 PM
-- Server version: 5.1.63
-- PHP Version: 5.3.5-1ubuntu7.11

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `litecoin`
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
  `threshold` varchar(4) DEFAULT '0',
  `donated` varchar(40) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `userId` (`userId`),
  KEY `b_userId` (`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3221 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3517208 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=159316 ;

-- --------------------------------------------------------

--
-- Table structure for table `DGM_blocks`
--

CREATE TABLE IF NOT EXISTS `DGM_blocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `block` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `DGM_config`
--

CREATE TABLE IF NOT EXISTS `DGM_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `value` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Table structure for table `DGM_payments`
--

CREATE TABLE IF NOT EXISTS `DGM_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `block` int(11) NOT NULL,
  `payment` double NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7132 ;

-- --------------------------------------------------------

--
-- Table structure for table `DGM_scores`
--

CREATE TABLE IF NOT EXISTS `DGM_scores` (
  `id_user` int(11) NOT NULL,
  `score` double NOT NULL,
  `time_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `shares` bigint(20) NOT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dumped_shares`
--

CREATE TABLE IF NOT EXISTS `dumped_shares` (
  `id` bigint(30) NOT NULL DEFAULT '0',
  `counted` int(1) NOT NULL COMMENT 'BOOLEAN) Tells server if it used these shares for counting',
  `blockNumber` int(255) NOT NULL,
  `rem_host` varchar(255) NOT NULL,
  `username` varchar(120) NOT NULL,
  `our_result` enum('Y','N') NOT NULL,
  `upstream_result` enum('Y','N') DEFAULT NULL,
  `reason` varchar(50) DEFAULT NULL,
  `solution` varchar(257) NOT NULL,
  `time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `score` double(23,2) DEFAULT NULL,
  `userId` int(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `locks`
--

CREATE TABLE IF NOT EXISTS `locks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`),
  KEY `name` (`name`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Triggers `locks`
--
DROP TRIGGER IF EXISTS `lock_update`;
DELIMITER //
CREATE TRIGGER `lock_update` BEFORE UPDATE ON `locks`
 FOR EACH ROW SET NEW.`timestamp` = CURRENT_TIMESTAMP()
//
DELIMITER ;

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
  `amount` decimal(16,8) NOT NULL DEFAULT '0.00000000',
  PRIMARY KEY (`id`),
  KEY `blockNumber_index` (`blockNumber`),
  KEY `confirms_index` (`confirms`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27507 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

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
  `monitor` enum('yes','no') NOT NULL DEFAULT 'no',
  `hashes` int(11) NOT NULL,
  `disabled` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`),
  KEY `p_username` (`username`),
  KEY `username_userid` (`username`,`associatedUserId`),
  KEY `userid` (`associatedUserId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7520 ;

-- --------------------------------------------------------

--
-- Table structure for table `rounddetails`
--

CREATE TABLE IF NOT EXISTS `rounddetails` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `userId` int(255) NOT NULL,
  `blockNumber` int(255) NOT NULL,
  `amount` varchar(40) NOT NULL,
  `rewarded` enum('N','Y') NOT NULL DEFAULT 'N',
  `shares` int(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `rounddetails_blockNumber` (`blockNumber`),
  KEY `rounddetails_rewarded` (`rewarded`),
  KEY `rounddetails_userId` (`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3083965 ;

-- --------------------------------------------------------

--
-- Table structure for table `server_stats`
--

CREATE TABLE IF NOT EXISTS `server_stats` (
  `key` varchar(20) NOT NULL,
  `value` tinytext NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1093569814 ;

-- --------------------------------------------------------

--
-- Table structure for table `shares_counted`
--

CREATE TABLE IF NOT EXISTS `shares_counted` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blockNumber` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `invalid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=748536 ;

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
  KEY `time` (`time`,`username`),
  KEY `fk_sh_webUsers` (`userId`),
  KEY `sh_upstream_result_blockNumber` (`upstream_result`,`blockNumber`),
  KEY `sh_our_result_blockNumber` (`our_result`,`blockNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `shares_inno`
--

CREATE TABLE IF NOT EXISTS `shares_inno` (
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `unconfirmed_rewards`
--

CREATE TABLE IF NOT EXISTS `unconfirmed_rewards` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `userId` int(255) NOT NULL,
  `blockNumber` int(255) NOT NULL,
  `amount` varchar(40) NOT NULL,
  `rewarded` enum('N','Y') NOT NULL DEFAULT 'N',
  `shares` int(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `unconfirmed_rewards_blockNumber` (`blockNumber`),
  KEY `unconfirmed_rewards_rewarded` (`rewarded`),
  KEY `unconfirmed_rewards_userId` (`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2594306 ;

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
  KEY `userId_timestamp` (`userId`,`timestamp`),
  KEY `idxUHashTS` (`timestamp`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6435618 ;

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
  `joindate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `emailAuthPin` varchar(32) NOT NULL COMMENT 'The pin required to authorize that email address',
  `secret` varchar(10) NOT NULL,
  `loggedIp` varchar(255) NOT NULL,
  `sessionTimeoutStamp` int(255) NOT NULL,
  `accountLocked` int(255) NOT NULL COMMENT 'This is the timestamp when the account will be unlocked(usually used to lock accounts that are trying to be bruteforced)',
  `accountFailedAttempts` int(2) NOT NULL COMMENT 'This counts the number of failed attempts for web login',
  `ad` enum('yes','no') NOT NULL DEFAULT 'no',
  `pin` varchar(255) NOT NULL COMMENT 'four digit pin to allow account changes',
  `share_count` int(32) NOT NULL DEFAULT '0',
  `stale_share_count` int(32) NOT NULL DEFAULT '0',
  `shares_this_round` int(32) NOT NULL DEFAULT '0',
  `api_key` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `sendemail` enum('yes','no') DEFAULT 'no',
  `hashrate` int(11) DEFAULT '0',
  `donate_percent` varchar(11) DEFAULT '0',
  `round_estimate` varchar(40) DEFAULT '0',
  `donoragree` enum('yes','no') NOT NULL DEFAULT 'no',
  `donorend` enum('yes','no') NOT NULL DEFAULT 'no',
  `donorselect` int(4) NOT NULL DEFAULT '0',
  `recivemail` enum('yes','no') NOT NULL DEFAULT 'yes',
  `deadworker` enum('yes','no') NOT NULL DEFAULT 'no',
  `iwarn` varchar(255) NOT NULL,
  `header` varchar(255) NOT NULL DEFAULT '1.2.3.4',
  `nickname` varchar(10) NOT NULL,
  `ipcheck` enum('yes','no') NOT NULL DEFAULT 'yes',
  `livestats` enum('yes','no') NOT NULL DEFAULT 'yes',
  `update_interval` int(10) NOT NULL DEFAULT '60',
  `tax_exempt` int(10) NOT NULL,
  `passreset` int(2) NOT NULL DEFAULT '0',
  `lastseen` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `disabled` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`),
  KEY `webusers_disabled` (`disabled`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3221 ;

-- --------------------------------------------------------

--
-- Table structure for table `winning_shares`
--

CREATE TABLE IF NOT EXISTS `winning_shares` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blockNumber` int(11) NOT NULL,
  `username` varchar(11) NOT NULL,
  `share_id` int(255) NOT NULL DEFAULT '0',
  `rewarded` enum('N','Y') NOT NULL DEFAULT 'N',
  `amount` varchar(40) NOT NULL DEFAULT '0',
  `confirms` smallint(6) NOT NULL DEFAULT '0',
  `txid` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `scored` enum('N','Y') NOT NULL DEFAULT 'N',
  `timestamp` int(255) NOT NULL,
  `shares` int(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `winning_shares_scored` (`scored`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14829 ;

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
-- Constraints for table `shares_history`
--
ALTER TABLE `shares_history`
  ADD CONSTRAINT `fk_sh_webUsers` FOREIGN KEY (`userId`) REFERENCES `webUsers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `shares_inno`
--
ALTER TABLE `shares_inno`
  ADD CONSTRAINT `fk_webUsers` FOREIGN KEY (`userId`) REFERENCES `webUsers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
