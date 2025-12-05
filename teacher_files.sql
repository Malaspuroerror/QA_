/*
SQLyog Ultimate - MySQL GUI v8.2 
MySQL - 5.5.5-10.1.25-MariaDB : Database - school
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`school` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `school`;

/*Table structure for table `teacher_files` */

DROP TABLE IF EXISTS `teacher_files`;

CREATE TABLE `teacher_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `teacher_name` varchar(765) DEFAULT NULL,
  `subject` varchar(765) DEFAULT NULL,
  `grade_section` varchar(150) DEFAULT NULL,
  `file_name` varchar(765) DEFAULT NULL,
  `file_path` varchar(765) DEFAULT NULL,
  `status` varchar(24) DEFAULT NULL,
  `submitted_date` datetime DEFAULT NULL,
  `approve_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

/*Data for the table `teacher_files` */

insert  into `teacher_files`(`id`,`teacher_name`,`subject`,`grade_section`,`file_name`,`file_path`,`status`,`submitted_date`,`approve_date`,`created_at`) values (6,'Aron Diolata','ESP','VI - Venus','ESP-VI-Venus.xlsx','uploads/teacher_files/1764305862_ESP-VI-Venus.xlsx','approved','2025-11-28 12:57:42','2025-11-28 13:06:30','2025-11-28 12:57:42'),(7,'Aron Diolata','ESP','VI - Venus','ESP-VI-Venus.xlsx','uploads/teacher_files/1764306412_ESP-VI-Venus.xlsx','pending','2025-11-28 13:06:52',NULL,'2025-11-28 13:06:52'),(8,'Khian Kervy Mamaril','ESP','VI - Venus','ESP-VI-Venus.xlsx','uploads/teacher_files/1764306926_ESP-VI-Venus.xlsx','pending','2025-11-28 13:15:26',NULL,'2025-11-28 13:15:26');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
