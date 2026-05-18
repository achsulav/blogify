/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-12.2.2-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: blogify
-- ------------------------------------------------------
-- Server version	12.2.2-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES
(1,'Technology','technology','2026-03-17 02:29:06'),
(2,'Programming','programming','2026-03-17 02:29:06'),
(3,'Lifestyle','lifestyle','2026-03-17 02:29:06'),
(4,'Business','business','2026-03-17 02:29:06'),
(5,'Education','education','2026-03-17 02:29:06'),
(9,'Health','health','2026-04-18 03:47:56'),
(10,'Travel','travel','2026-04-18 03:47:56');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES
(1,39,1,'nice post','2026-03-17 10:07:21'),
(2,39,1,'nice post','2026-03-17 10:08:02'),
(3,37,1,'nice post','2026-03-17 10:08:23'),
(4,25,6,'nice post','2026-03-17 10:09:08'),
(5,39,6,'nice post','2026-03-17 10:09:52'),
(6,39,1,'nice post','2026-03-17 10:16:33'),
(7,39,1,'nice post','2026-03-17 10:17:07'),
(24,41,6,'hi i am a new comment,say hello','2026-03-18 09:26:33'),
(26,31,1,'hello','2026-03-18 09:35:52'),
(27,44,1,'hello','2026-04-06 05:16:54'),
(29,98,1,'hellow','2026-04-08 02:46:49'),
(30,99,1,'hello','2026-04-08 04:25:42'),
(32,110,11,'hello','2026-04-11 03:23:00'),
(34,109,11,'hello i am dipesh','2026-04-11 08:57:56'),
(37,111,13,'hello','2026-04-29 07:31:15'),
(47,117,13,'hello','2026-05-12 05:33:27'),
(48,112,13,'helllo','2026-05-16 02:49:44');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(10,'m0000_create_users_table.php','2026-04-12 16:23:45'),
(11,'m0001_create_posts_table.php','2026-04-12 16:23:45'),
(12,'m0002_add_slug_to_posts.php','2026-04-12 16:23:45'),
(13,'m0003_create_categories_table.php','2026-04-12 16:23:45'),
(14,'m0003_update_posts_structure.php','2026-04-12 16:23:45'),
(15,'m0004_add_category_id_to_posts.php','2026-04-12 16:23:45'),
(16,'m0004_theme_column.php','2026-04-12 16:23:45'),
(17,'m0005_create_comments_table.php','2026-04-12 16:23:45'),
(18,'m0006_add_content_html_to_posts.php','2026-04-12 16:23:45'),
(19,'m0007_add_phone_to_users.php','2026-04-28 10:40:45'),
(20,'m0008_create_otps_table.php','2026-04-28 10:40:45');
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `otps`
--

DROP TABLE IF EXISTS `otps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `otps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `otp_code` varchar(10) NOT NULL,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otps`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `otps` WRITE;
/*!40000 ALTER TABLE `otps` DISABLE KEYS */;
INSERT INTO `otps` VALUES
(3,12,'600883','2026-04-28 05:11:07','2026-04-28 10:46:07'),
(5,14,'787567','2026-04-28 20:27:22','2026-04-29 02:02:22'),
(11,17,'412399','2026-04-29 11:10:03','2026-04-29 16:45:03'),
(12,18,'976581','2026-05-11 23:32:20','2026-05-12 05:07:20'),
(13,19,'428430','2026-05-15 21:12:38','2026-05-16 02:47:38');
/*!40000 ALTER TABLE `otps` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `content_html` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_slug` (`user_id`,`slug`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES
(112,16,4,'A Canvas for Identity','a-canvas-for-identity','2026-04-29 04:49:57','<p>The T-shirt is more <br>than just a staple of casual wear; it is a powerful medium for <br>self-expression, a historical artifact, and a symbol of cultural shifts.<br> Originally designed as an undergarment for the U.S. Navy in the early <br>20th century, the T-shirt evolved into a standalone fashion icon, <br>largely popularized by Hollywood figures like Marlon Brando and James <br>Dean.</p><img src=\"/uploads/posts/16/img_69f18ded8c4b42.10242517.jpg\"><p></p>');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `theme` varchar(100) DEFAULT 'default',
  `phone` varchar(20) DEFAULT NULL,
  `is_verified` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(12,'Sulav','sulav','sulav@email.com','$2y$12$Rz1o359lBOCw6QyK1R/hjuaURpqeoTGEDW5WlSN/jzbdFg8tSq3Wq','2026-04-12 16:25:19','default','9840043614',0),
(13,'Sulav Ach','sulavach1','sulavach@gmail.com','$2y$12$JxGzJS86g6DEKyhguzz34OG9vHbpT0O9AlD7XMYHS3dP2xoCXzJKC','2026-04-28 17:01:43','default','9840043614',1),
(14,'Termux','termux','termux@email.com','$2y$12$c1B9adSdLLXCiXwf02aq2eGlvCUHoSK33ER64j8N/BZIMsUdhG8Uu','2026-04-29 02:02:22','default','9840043614',0),
(15,'Name','username','email@email.comm','$2y$12$zyOksXxpk8NmarJfrCunOu3IPv5agwY3Kzi0L7qVEAimdhxPhuRAa','2026-04-29 02:12:08','default','9779840043614',1),
(16,'Arch','arch','arch@email.com','$2y$12$kUok2UDkZC7ZPulXFT555e..NYvLEYQbjB7NDFXYsS7/dmvDzP7jS','2026-04-29 02:32:13','default','9779840043614',1),
(17,'Blogify','blogify','blogify@email.com','$2y$12$T98CGGXUkYlZ4lgdWIoZS.AScOWVcjlzDi8UK0T7.z/AxL.TBWpAe','2026-04-29 16:43:09','default','9840043614',0),
(18,'Kanchan Aryal','kanchan','kanchan@email.com','$2y$12$ka6x7C7Fask8.tGa4uM2ueS0mGmybsXa5vt6dfBgWZEIuKPmWLIm2','2026-05-12 05:07:20','default','9779749714239',0),
(19,'deno','deno','deno@email.com','$2y$12$Pt.ODkarFV4RZ/2kW0l3eOV3gKU/1nilQo/MvdpmNU/GoRWS.Y7p6','2026-05-16 02:47:38','default','97712345678',0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-05-16 10:29:55
