-- phpMyAdmin SQL Dump
-- version 3.5.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 13, 2016 at 01:49 PM
-- Server version: 5.5.50-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.19

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `CWC`
--

-- --------------------------------------------------------

--
-- Table structure for table `Collector`
--

DROP TABLE IF EXISTS `Collector`;
CREATE TABLE IF NOT EXISTS `Collector` (
  `cid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lat` float NOT NULL,
  `lon` float NOT NULL,
  `tdate` date NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=114 ;

--
-- Dumping data for table `Collector`
--

-- --------------------------------------------------------

--
-- Table structure for table `Places`
--

DROP TABLE IF EXISTS `Places`;
CREATE TABLE IF NOT EXISTS `Places` (
  `pid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `lat` float NOT NULL,
  `lon` float NOT NULL,
  PRIMARY KEY (`pid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=45 ;

--
-- Dumping data for table `Places`
--

-- --------------------------------------------------------

--
-- Table structure for table `tally`
--

DROP TABLE IF EXISTS `tally`;
CREATE TABLE IF NOT EXISTS `tally` (
  `cid` int(10) unsigned NOT NULL,
  `iid` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  KEY `cid` (`cid`,`iid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tally`
--

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
