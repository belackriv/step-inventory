-- MySQL dump 10.16  Distrib 10.1.14-MariaDB, for osx10.10 (x86_64)
--
-- Host: localhost    Database: step-inventory
-- ------------------------------------------------------
-- Server version	10.1.14-MariaDB

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
-- Table structure for table `acl_classes`
--

DROP TABLE IF EXISTS `acl_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_classes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `class_type` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_69DD750638A36066` (`class_type`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acl_classes`
--

LOCK TABLES `acl_classes` WRITE;
/*!40000 ALTER TABLE `acl_classes` DISABLE KEYS */;
INSERT INTO `acl_classes` VALUES (7,'AppBundle\\Entity\\Department'),(6,'AppBundle\\Entity\\MenuLink');
/*!40000 ALTER TABLE `acl_classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acl_entries`
--

DROP TABLE IF EXISTS `acl_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_entries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `class_id` int(10) unsigned NOT NULL,
  `object_identity_id` int(10) unsigned DEFAULT NULL,
  `security_identity_id` int(10) unsigned NOT NULL,
  `field_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ace_order` smallint(5) unsigned NOT NULL,
  `mask` int(11) NOT NULL,
  `granting` tinyint(1) NOT NULL,
  `granting_strategy` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `audit_success` tinyint(1) NOT NULL,
  `audit_failure` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_46C8B806EA000B103D9AB4A64DEF17BCE4289BF4` (`class_id`,`object_identity_id`,`field_name`,`ace_order`),
  KEY `IDX_46C8B806EA000B103D9AB4A6DF9183C9` (`class_id`,`object_identity_id`,`security_identity_id`),
  KEY `IDX_46C8B806EA000B10` (`class_id`),
  KEY `IDX_46C8B8063D9AB4A6` (`object_identity_id`),
  KEY `IDX_46C8B806DF9183C9` (`security_identity_id`),
  CONSTRAINT `FK_46C8B8063D9AB4A6` FOREIGN KEY (`object_identity_id`) REFERENCES `acl_object_identities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_46C8B806DF9183C9` FOREIGN KEY (`security_identity_id`) REFERENCES `acl_security_identities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_46C8B806EA000B10` FOREIGN KEY (`class_id`) REFERENCES `acl_classes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acl_entries`
--

LOCK TABLES `acl_entries` WRITE;
/*!40000 ALTER TABLE `acl_entries` DISABLE KEYS */;
INSERT INTO `acl_entries` VALUES (45,6,23,13,NULL,0,32,1,'all',0,0),(46,6,23,14,NULL,1,1,1,'all',0,0),(47,6,24,13,NULL,0,32,1,'all',0,0),(48,6,24,15,NULL,1,1,1,'all',0,0),(49,6,25,13,NULL,0,32,1,'all',0,0),(50,6,25,16,NULL,1,1,1,'all',0,0),(51,6,26,13,NULL,0,32,1,'all',0,0),(52,6,26,14,NULL,1,1,1,'all',0,0),(53,7,27,13,NULL,0,32,1,'all',0,0),(54,7,27,14,NULL,1,1,1,'all',0,0),(55,7,28,13,NULL,0,32,1,'all',0,0),(56,7,28,15,NULL,1,1,1,'all',0,0),(57,7,29,13,NULL,0,32,1,'all',0,0),(58,7,29,16,NULL,1,1,1,'all',0,0),(59,7,30,13,NULL,0,32,1,'all',0,0),(60,7,30,14,NULL,1,1,1,'all',0,0),(61,7,31,13,NULL,0,32,1,'all',0,0),(62,7,31,14,NULL,1,1,1,'all',0,0),(65,6,35,13,NULL,0,32,1,'all',0,0),(66,6,35,15,NULL,1,1,1,'all',0,0),(67,6,36,13,NULL,0,32,1,'all',0,0),(68,6,36,14,NULL,1,1,1,'all',0,0),(69,6,37,13,NULL,0,32,1,'all',0,0),(70,6,37,16,NULL,1,1,1,'all',0,0);
/*!40000 ALTER TABLE `acl_entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acl_object_identities`
--

DROP TABLE IF EXISTS `acl_object_identities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_object_identities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_object_identity_id` int(10) unsigned DEFAULT NULL,
  `class_id` int(10) unsigned NOT NULL,
  `object_identifier` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `entries_inheriting` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_9407E5494B12AD6EA000B10` (`object_identifier`,`class_id`),
  KEY `IDX_9407E54977FA751A` (`parent_object_identity_id`),
  CONSTRAINT `FK_9407E54977FA751A` FOREIGN KEY (`parent_object_identity_id`) REFERENCES `acl_object_identities` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acl_object_identities`
--

LOCK TABLES `acl_object_identities` WRITE;
/*!40000 ALTER TABLE `acl_object_identities` DISABLE KEYS */;
INSERT INTO `acl_object_identities` VALUES (23,NULL,6,'13',1),(24,NULL,6,'14',1),(25,NULL,6,'15',1),(26,NULL,6,'16',1),(27,NULL,7,'11',1),(28,NULL,7,'12',1),(29,NULL,7,'13',1),(30,NULL,7,'14',1),(31,NULL,7,'15',1),(35,NULL,6,'21',1),(36,NULL,6,'22',1),(37,NULL,6,'23',1);
/*!40000 ALTER TABLE `acl_object_identities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acl_object_identity_ancestors`
--

DROP TABLE IF EXISTS `acl_object_identity_ancestors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_object_identity_ancestors` (
  `object_identity_id` int(10) unsigned NOT NULL,
  `ancestor_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`object_identity_id`,`ancestor_id`),
  KEY `IDX_825DE2993D9AB4A6` (`object_identity_id`),
  KEY `IDX_825DE299C671CEA1` (`ancestor_id`),
  CONSTRAINT `FK_825DE2993D9AB4A6` FOREIGN KEY (`object_identity_id`) REFERENCES `acl_object_identities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_825DE299C671CEA1` FOREIGN KEY (`ancestor_id`) REFERENCES `acl_object_identities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acl_object_identity_ancestors`
--

LOCK TABLES `acl_object_identity_ancestors` WRITE;
/*!40000 ALTER TABLE `acl_object_identity_ancestors` DISABLE KEYS */;
INSERT INTO `acl_object_identity_ancestors` VALUES (23,23),(24,24),(25,25),(26,26),(27,27),(28,28),(29,29),(30,30),(31,31),(35,35),(36,36),(37,37);
/*!40000 ALTER TABLE `acl_object_identity_ancestors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acl_security_identities`
--

DROP TABLE IF EXISTS `acl_security_identities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_security_identities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `identifier` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `username` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8835EE78772E836AF85E0677` (`identifier`,`username`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acl_security_identities`
--

LOCK TABLES `acl_security_identities` WRITE;
/*!40000 ALTER TABLE `acl_security_identities` DISABLE KEYS */;
INSERT INTO `acl_security_identities` VALUES (15,'ROLE_ADMIN',0),(13,'ROLE_DEV',0),(16,'ROLE_LEAD',0),(14,'ROLE_USER',0);
/*!40000 ALTER TABLE `acl_security_identities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bin`
--

DROP TABLE IF EXISTS `bin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `part_category_id` int(11) DEFAULT NULL,
  `bin_type_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_AA275AED8E7AEECE` (`part_category_id`),
  KEY `IDX_AA275AEDE4A591E` (`bin_type_id`),
  KEY `IDX_AA275AED727ACA70` (`parent_id`),
  CONSTRAINT `FK_AA275AED727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `bin` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_AA275AED8E7AEECE` FOREIGN KEY (`part_category_id`) REFERENCES `part_category` (`id`),
  CONSTRAINT `FK_AA275AEDE4A591E` FOREIGN KEY (`bin_type_id`) REFERENCES `bin_type` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bin`
--

LOCK TABLES `bin` WRITE;
/*!40000 ALTER TABLE `bin` DISABLE KEYS */;
INSERT INTO `bin` VALUES (1,NULL,1,NULL,'Bin One','Bin One Desc',1),(2,2,1,1,'Bin Two','Bin Two Desc goes on and on and on and on and on and on and on and on and on and on and on and on and on and on and on and on and on and on and on and on and on and on and',1);
/*!40000 ALTER TABLE `bin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bin_part_count`
--

DROP TABLE IF EXISTS `bin_part_count`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bin_part_count` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bin_id` int(11) NOT NULL,
  `part_id` int(11) NOT NULL,
  `count` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bin_part_count_unique` (`bin_id`,`part_id`),
  KEY `IDX_EA6B0FF6222586DC` (`bin_id`),
  KEY `IDX_EA6B0FF64CE34BEC` (`part_id`),
  CONSTRAINT `FK_EA6B0FF6222586DC` FOREIGN KEY (`bin_id`) REFERENCES `bin` (`id`),
  CONSTRAINT `FK_EA6B0FF64CE34BEC` FOREIGN KEY (`part_id`) REFERENCES `part` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bin_part_count`
--

LOCK TABLES `bin_part_count` WRITE;
/*!40000 ALTER TABLE `bin_part_count` DISABLE KEYS */;
INSERT INTO `bin_part_count` VALUES (1,2,1,5),(2,1,1,9);
/*!40000 ALTER TABLE `bin_part_count` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bin_type`
--

DROP TABLE IF EXISTS `bin_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bin_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `behavoirs` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:simple_array)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bin_type`
--

LOCK TABLES `bin_type` WRITE;
/*!40000 ALTER TABLE `bin_type` DISABLE KEYS */;
INSERT INTO `bin_type` VALUES (1,'Bin Type One','test',1,'cannotHaveParent');
/*!40000 ALTER TABLE `bin_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `department`
--

DROP TABLE IF EXISTS `department`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `department` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `office_id` int(11) DEFAULT NULL,
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CD1DE18AFFA0C224` (`office_id`),
  CONSTRAINT `FK_CD1DE18AFFA0C224` FOREIGN KEY (`office_id`) REFERENCES `office` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `department`
--

LOCK TABLES `department` WRITE;
/*!40000 ALTER TABLE `department` DISABLE KEYS */;
INSERT INTO `department` VALUES (11,7,'DFW-Check-In'),(12,7,'DFW-Processing'),(13,7,'DFW-Shipping'),(14,8,'AUS-Check-In'),(15,8,'AUS-Shipping');
/*!40000 ALTER TABLE `department` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_audit`
--

DROP TABLE IF EXISTS `inventory_audit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `by_user_id` int(11) NOT NULL,
  `for_bin_id` int(11) NOT NULL,
  `started_at` datetime NOT NULL,
  `ended_at` datetime DEFAULT NULL,
  `total_deviations` smallint(6) DEFAULT NULL,
  `serial_count_deviations` smallint(6) DEFAULT NULL,
  `serial_match_deviations` smallint(6) DEFAULT NULL,
  `part_count_deviations` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4E845479DC9C2434` (`by_user_id`),
  KEY `IDX_4E84547914A67AF8` (`for_bin_id`),
  CONSTRAINT `FK_4E84547914A67AF8` FOREIGN KEY (`for_bin_id`) REFERENCES `bin` (`id`),
  CONSTRAINT `FK_4E845479DC9C2434` FOREIGN KEY (`by_user_id`) REFERENCES `stepthrough_user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_audit`
--

LOCK TABLES `inventory_audit` WRITE;
/*!40000 ALTER TABLE `inventory_audit` DISABLE KEYS */;
INSERT INTO `inventory_audit` VALUES (1,1,1,'2016-08-02 18:35:27','2016-08-03 01:18:00',6,NULL,NULL,6),(2,1,2,'2016-08-02 20:24:50','2016-08-03 01:25:19',0,NULL,NULL,0),(3,1,1,'2016-08-02 20:37:26','2016-08-03 01:42:15',3,NULL,NULL,3);
/*!40000 ALTER TABLE `inventory_audit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_movement_rule`
--

DROP TABLE IF EXISTS `inventory_movement_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_movement_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) DEFAULT NULL,
  `bin_type_id` int(11) DEFAULT NULL,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `restrictions` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:simple_array)',
  `is_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_FDFB0022D60322AC` (`role_id`),
  KEY `IDX_FDFB0022E4A591E` (`bin_type_id`),
  CONSTRAINT `FK_FDFB0022D60322AC` FOREIGN KEY (`role_id`) REFERENCES `stepthrough_role` (`id`),
  CONSTRAINT `FK_FDFB0022E4A591E` FOREIGN KEY (`bin_type_id`) REFERENCES `bin_type` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_movement_rule`
--

LOCK TABLES `inventory_movement_rule` WRITE;
/*!40000 ALTER TABLE `inventory_movement_rule` DISABLE KEYS */;
INSERT INTO `inventory_movement_rule` VALUES (1,11,1,'test imr','test imr',NULL,0);
/*!40000 ALTER TABLE `inventory_movement_rule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_part_adjustment`
--

DROP TABLE IF EXISTS `inventory_part_adjustment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_part_adjustment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `part_id` int(11) NOT NULL,
  `by_user_id` int(11) NOT NULL,
  `for_bin_id` int(11) NOT NULL,
  `old_count` smallint(6) DEFAULT NULL,
  `new_count` smallint(6) NOT NULL,
  `performed_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CE31296C4CE34BEC` (`part_id`),
  KEY `IDX_CE31296CDC9C2434` (`by_user_id`),
  KEY `IDX_CE31296C14A67AF8` (`for_bin_id`),
  CONSTRAINT `FK_CE31296C14A67AF8` FOREIGN KEY (`for_bin_id`) REFERENCES `bin` (`id`),
  CONSTRAINT `FK_CE31296C4CE34BEC` FOREIGN KEY (`part_id`) REFERENCES `part` (`id`),
  CONSTRAINT `FK_CE31296CDC9C2434` FOREIGN KEY (`by_user_id`) REFERENCES `stepthrough_user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_part_adjustment`
--

LOCK TABLES `inventory_part_adjustment` WRITE;
/*!40000 ALTER TABLE `inventory_part_adjustment` DISABLE KEYS */;
INSERT INTO `inventory_part_adjustment` VALUES (1,1,1,2,0,3,'2016-07-31 22:25:07'),(2,1,1,2,3,2,'2016-07-31 22:25:58'),(3,1,1,2,2,4,'2016-08-01 20:55:34'),(4,1,1,2,4,5,'2016-08-01 20:58:23'),(5,1,1,2,5,2,'2016-08-01 20:59:28'),(6,1,1,2,2,5,'2016-08-01 21:24:52'),(7,1,1,1,NULL,3,'2016-08-01 21:52:48'),(8,1,1,1,3,5,'2016-08-01 21:52:55'),(9,1,2,1,3,7,'2016-08-02 15:39:45');
/*!40000 ALTER TABLE `inventory_part_adjustment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_part_audit`
--

DROP TABLE IF EXISTS `inventory_part_audit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_part_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `part_id` int(11) NOT NULL,
  `user_count` smallint(6) NOT NULL,
  `system_count` smallint(6) NOT NULL,
  `inventory_audit_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4285FD194CE34BEC` (`part_id`),
  KEY `IDX_4285FD1996349C8` (`inventory_audit_id`),
  CONSTRAINT `FK_4285FD194CE34BEC` FOREIGN KEY (`part_id`) REFERENCES `part` (`id`),
  CONSTRAINT `FK_4285FD1996349C8` FOREIGN KEY (`inventory_audit_id`) REFERENCES `inventory_audit` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_part_audit`
--

LOCK TABLES `inventory_part_audit` WRITE;
/*!40000 ALTER TABLE `inventory_part_audit` DISABLE KEYS */;
INSERT INTO `inventory_part_audit` VALUES (3,2,3,0,1),(4,1,6,9,1),(6,1,5,5,2),(7,1,6,9,3);
/*!40000 ALTER TABLE `inventory_part_audit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_part_movement`
--

DROP TABLE IF EXISTS `inventory_part_movement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_part_movement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `part_id` int(11) NOT NULL,
  `by_user_id` int(11) NOT NULL,
  `from_bin_id` int(11) NOT NULL,
  `to_bin_id` int(11) NOT NULL,
  `moved_at` datetime NOT NULL,
  `count` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_82D37C4CE34BEC` (`part_id`),
  KEY `IDX_82D37CDC9C2434` (`by_user_id`),
  KEY `IDX_82D37CAF14FD0C` (`from_bin_id`),
  KEY `IDX_82D37C4F78A41` (`to_bin_id`),
  CONSTRAINT `FK_82D37C4CE34BEC` FOREIGN KEY (`part_id`) REFERENCES `part` (`id`),
  CONSTRAINT `FK_82D37C4F78A41` FOREIGN KEY (`to_bin_id`) REFERENCES `bin` (`id`),
  CONSTRAINT `FK_82D37CAF14FD0C` FOREIGN KEY (`from_bin_id`) REFERENCES `bin` (`id`),
  CONSTRAINT `FK_82D37CDC9C2434` FOREIGN KEY (`by_user_id`) REFERENCES `stepthrough_user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_part_movement`
--

LOCK TABLES `inventory_part_movement` WRITE;
/*!40000 ALTER TABLE `inventory_part_movement` DISABLE KEYS */;
INSERT INTO `inventory_part_movement` VALUES (1,1,1,2,1,'2016-08-01 21:57:31',4),(2,1,1,1,2,'2016-08-01 21:57:46',3),(3,1,1,2,1,'2016-08-01 21:57:56',2),(4,1,1,1,2,'2016-08-01 21:58:18',3),(5,1,1,1,2,'2016-08-01 22:00:30',2),(6,1,2,2,1,'2016-08-02 15:40:08',2);
/*!40000 ALTER TABLE `inventory_part_movement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_item`
--

DROP TABLE IF EXISTS `menu_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_link_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D754D550257F1FCF` (`menu_link_id`),
  KEY `IDX_D754D550AE80F5DF` (`department_id`),
  KEY `IDX_D754D550727ACA70` (`parent_id`),
  CONSTRAINT `FK_D754D550257F1FCF` FOREIGN KEY (`menu_link_id`) REFERENCES `menu_link` (`id`),
  CONSTRAINT `FK_D754D550727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `menu_item` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_D754D550AE80F5DF` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_item`
--

LOCK TABLES `menu_item` WRITE;
/*!40000 ALTER TABLE `menu_item` DISABLE KEYS */;
INSERT INTO `menu_item` VALUES (41,13,11,NULL,1,1),(42,14,11,NULL,1,2),(43,15,NULL,41,1,3),(44,16,NULL,41,1,4),(45,13,12,NULL,1,1),(46,14,12,NULL,1,2),(47,15,NULL,45,1,3),(48,16,NULL,45,1,4),(49,13,13,NULL,1,1),(50,14,13,NULL,1,2),(51,15,NULL,49,1,3),(52,16,NULL,49,1,4),(53,13,14,NULL,1,1),(54,14,14,NULL,1,2),(55,15,NULL,53,1,3),(56,16,NULL,53,1,4),(57,13,15,NULL,1,1),(58,14,15,NULL,1,2),(59,15,NULL,57,1,3),(60,16,NULL,57,1,4),(61,21,NULL,42,1,1),(62,22,NULL,41,1,1),(63,23,NULL,41,1,2);
/*!40000 ALTER TABLE `menu_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_link`
--

DROP TABLE IF EXISTS `menu_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `route_matches` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:simple_array)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_link`
--

LOCK TABLES `menu_link` WRITE;
/*!40000 ALTER TABLE `menu_link` DISABLE KEYS */;
INSERT INTO `menu_link` VALUES (13,'Main',NULL,NULL),(14,'Admin Options','/admin','user,menu_item'),(15,'For ROLE_LEAD','/role_lead',NULL),(16,'For ROLE_USER','/role_user',NULL),(21,'Admin Inventory','/admin_inventory','part,part_category,part_group,bin,bin_type,inventory_movement_rule'),(22,'Inventory','/inventory','bin_part_count,inventory_part_adjustment,inventory_part_movement'),(23,'Inventory Audit','/inventory_audit',NULL);
/*!40000 ALTER TABLE `menu_link` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `office`
--

DROP TABLE IF EXISTS `office`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `office` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `office`
--

LOCK TABLES `office` WRITE;
/*!40000 ALTER TABLE `office` DISABLE KEYS */;
INSERT INTO `office` VALUES (7,'Coppell'),(8,'Austin');
/*!40000 ALTER TABLE `office` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `part`
--

DROP TABLE IF EXISTS `part`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `part` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `part_category_id` int(11) DEFAULT NULL,
  `part_group_id` int(11) DEFAULT NULL,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `part_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `part_alt_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `is_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_490F70C68E7AEECE` (`part_category_id`),
  KEY `IDX_490F70C669065C38` (`part_group_id`),
  CONSTRAINT `FK_490F70C669065C38` FOREIGN KEY (`part_group_id`) REFERENCES `part_group` (`id`),
  CONSTRAINT `FK_490F70C68E7AEECE` FOREIGN KEY (`part_category_id`) REFERENCES `part_category` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `part`
--

LOCK TABLES `part` WRITE;
/*!40000 ALTER TABLE `part` DISABLE KEYS */;
INSERT INTO `part` VALUES (1,2,2,'Part One','Part One Id','Part One Alt Id','This is a test part.  It goes on and on and on. This should be cut off at some point.  I hope it is soon, I am running out of things to say.',1),(2,1,1,'Part Two','parttwo','alt_part_Two','two',1);
/*!40000 ALTER TABLE `part` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `part_category`
--

DROP TABLE IF EXISTS `part_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `part_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `part_category`
--

LOCK TABLES `part_category` WRITE;
/*!40000 ALTER TABLE `part_category` DISABLE KEYS */;
INSERT INTO `part_category` VALUES (1,'Category One',1),(2,'Category Two',1);
/*!40000 ALTER TABLE `part_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `part_group`
--

DROP TABLE IF EXISTS `part_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `part_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `part_group`
--

LOCK TABLES `part_group` WRITE;
/*!40000 ALTER TABLE `part_group` DISABLE KEYS */;
INSERT INTO `part_group` VALUES (1,'Group One',1),(2,'Group Two',1);
/*!40000 ALTER TABLE `part_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_role`
--

DROP TABLE IF EXISTS `role_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_source_id` int(11) DEFAULT NULL,
  `role_target_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E9D6F8FEC4C8CFBD` (`role_source_id`),
  KEY `IDX_E9D6F8FE447AD8BA` (`role_target_id`),
  CONSTRAINT `FK_E9D6F8FE447AD8BA` FOREIGN KEY (`role_target_id`) REFERENCES `stepthrough_role` (`id`),
  CONSTRAINT `FK_E9D6F8FEC4C8CFBD` FOREIGN KEY (`role_source_id`) REFERENCES `stepthrough_role` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_role`
--

LOCK TABLES `role_role` WRITE;
/*!40000 ALTER TABLE `role_role` DISABLE KEYS */;
INSERT INTO `role_role` VALUES (7,10,9),(8,11,10),(9,12,11);
/*!40000 ALTER TABLE `role_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stepthrough_role`
--

DROP TABLE IF EXISTS `stepthrough_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stepthrough_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `role` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `is_allowed_to_switch` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1AEAD55357698A6A` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stepthrough_role`
--

LOCK TABLES `stepthrough_role` WRITE;
/*!40000 ALTER TABLE `stepthrough_role` DISABLE KEYS */;
INSERT INTO `stepthrough_role` VALUES (9,'User','ROLE_USER',0),(10,'Lead','ROLE_LEAD',0),(11,'Admin','ROLE_ADMIN',0),(12,'Dev','ROLE_DEV',1);
/*!40000 ALTER TABLE `stepthrough_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stepthrough_user`
--

DROP TABLE IF EXISTS `stepthrough_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stepthrough_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `default_department_id` int(11) DEFAULT NULL,
  `username` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_C0108970F85E0677` (`username`),
  UNIQUE KEY `UNIQ_C0108970E7927C74` (`email`),
  KEY `IDX_C010897098A169CC` (`default_department_id`),
  CONSTRAINT `FK_C010897098A169CC` FOREIGN KEY (`default_department_id`) REFERENCES `department` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stepthrough_user`
--

LOCK TABLES `stepthrough_user` WRITE;
/*!40000 ALTER TABLE `stepthrough_user` DISABLE KEYS */;
INSERT INTO `stepthrough_user` VALUES (1,11,'belac','$2y$13$G61R1emqU0dGNGPU3RGx/OqnhUubm62IaLr9AxGRyVVwybROfUXue','belackriv@gmail.com','Belac','Kriv',1),(2,11,'usertest','$2y$13$D64vfbqVcFpPtO9OzscoBeki38X6KvLMBMm8gLjW6b1r03p6SumQy','user@none','User','Test',1),(3,12,'leadtest','$2y$13$4L1ym1qghNHtmsD.dJbAoOx7YwSrq0/Zceg/wfYudcGB74Gy0DG0G','lead@none','Lead','Test',1),(4,14,'admintest','$2y$13$q3JcBimdPVslykShOq8nhu2v1gPZ4ENMHY.CcqGu1dNOTuZvMKLWm','admin@none','Admin','TestLastName',1),(5,13,'User2','$2y$13$CI0Puv0WSR9V/sIc/qOu0.ReE7JNgfdpzg5nqooBmtj9N5wzmQwf.','user2@test.com','User','Two',1);
/*!40000 ALTER TABLE `stepthrough_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `traveler_id`
--

DROP TABLE IF EXISTS `traveler_id`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `traveler_id` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `traveler_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `traveler_id`
--

LOCK TABLES `traveler_id` WRITE;
/*!40000 ALTER TABLE `traveler_id` DISABLE KEYS */;
/*!40000 ALTER TABLE `traveler_id` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_role`
--

DROP TABLE IF EXISTS `user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2DE8C6A3A76ED395` (`user_id`),
  KEY `IDX_2DE8C6A3D60322AC` (`role_id`),
  CONSTRAINT `FK_2DE8C6A3A76ED395` FOREIGN KEY (`user_id`) REFERENCES `stepthrough_user` (`id`),
  CONSTRAINT `FK_2DE8C6A3D60322AC` FOREIGN KEY (`role_id`) REFERENCES `stepthrough_role` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_role`
--

LOCK TABLES `user_role` WRITE;
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
INSERT INTO `user_role` VALUES (1,1,12),(2,2,9),(3,3,10),(4,4,11),(5,NULL,9),(6,NULL,9),(7,NULL,9),(8,NULL,9),(9,NULL,9),(10,NULL,9),(11,NULL,9),(12,NULL,9),(13,NULL,9),(15,5,9);
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-08-05 18:56:10
