CREATE DATABASE  IF NOT EXISTS `counters_db` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `counters_db`;
-- MySQL dump 10.13  Distrib 5.7.17, for macos10.12 (x86_64)
--
-- Host: 127.0.0.1    Database: counters_db
-- ------------------------------------------------------
-- Server version	5.5.5-10.2.8-MariaDB-10.2.8+maria~jessie

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `event_counters`
--

DROP TABLE IF EXISTS `event_counters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_counters` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `event_title` varchar(255) NOT NULL DEFAULT '',
  `country` char(2) NOT NULL DEFAULT '',
  `counter` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `date_title_country` (`date`,`event_title`,`country`),
  KEY `country_index` (`country`),
  KEY `counter_index` (`counter`),
  KEY `country_date` (`country`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_counters`
--

LOCK TABLES `event_counters` WRITE;
/*!40000 ALTER TABLE `event_counters` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_counters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_queue`
--

DROP TABLE IF EXISTS `event_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_queue` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `event_title` varchar(255) NOT NULL DEFAULT '',
  `country` char(2) NOT NULL DEFAULT '',
  `counter` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `date_title_country` (`date`,`event_title`,`country`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_queue`
--

LOCK TABLES `event_queue` WRITE;
/*!40000 ALTER TABLE `event_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'counters_db'
--
/*!50106 SET @save_time_zone= @@TIME_ZONE */ ;
/*!50106 DROP EVENT IF EXISTS `collect_counters` */;
DELIMITER ;;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;;
/*!50003 SET character_set_client  = utf8 */ ;;
/*!50003 SET character_set_results = utf8 */ ;;
/*!50003 SET collation_connection  = utf8_general_ci */ ;;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;;
/*!50003 SET @saved_time_zone      = @@time_zone */ ;;
/*!50003 SET time_zone             = 'SYSTEM' */ ;;
/*!50106 CREATE*/ /*!50117 DEFINER=`root`@`%`*/ /*!50106 EVENT `collect_counters` ON SCHEDULE EVERY 10 SECOND STARTS '2017-09-23 19:49:28' ON COMPLETION NOT PRESERVE ENABLE DO call counters_db.process_counters_queue() */ ;;
/*!50003 SET time_zone             = @saved_time_zone */ ;;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;;
/*!50003 SET character_set_client  = @saved_cs_client */ ;;
/*!50003 SET character_set_results = @saved_cs_results */ ;;
/*!50003 SET collation_connection  = @saved_col_connection */ ;;
DELIMITER ;
/*!50106 SET TIME_ZONE= @save_time_zone */ ;

--
-- Dumping routines for database 'counters_db'
--
/*!50003 DROP PROCEDURE IF EXISTS `process_counters_queue` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `process_counters_queue`()
BEGIN
  DROP TABLE IF EXISTS `event_queue_buffer`;
  RENAME TABLE `event_queue` TO `event_queue_buffer`;
    CREATE TABLE `event_queue` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `date` date DEFAULT NULL,
    `event_title` varchar(255) NOT NULL DEFAULT '',
    `country` char(2) NOT NULL DEFAULT '',
    `counter` tinyint(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    KEY `date_title_country` (`date`,`event_title`,`country`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    INSERT INTO event_counters (`date`, `event_title`, `country`, `counter`) 
  SELECT t1.date, t1.event_title, t1.country, count(t1.id) as counter FROM event_queue_buffer as t1 GROUP BY `date`, `event_title`, `country`
ON DUPLICATE KEY UPDATE event_counters.counter = event_counters.counter + VALUES(counter);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-09-23 22:55:36
