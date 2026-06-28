CREATE DATABASE  IF NOT EXISTS `cucms_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `cucms_db`;
-- MySQL dump 10.13  Distrib 8.0.46, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: cucms_db
-- ------------------------------------------------------
-- Server version	8.0.45

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `application`
--

DROP TABLE IF EXISTS `application`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `application` (
  `ApplicationID` int NOT NULL AUTO_INCREMENT,
  `StudentID` int NOT NULL,
  `CourseID` int NOT NULL,
  `CertificateNumber` varchar(50) NOT NULL,
  `ApplicationDate` date NOT NULL,
  `ApprovedBy` int DEFAULT NULL,
  PRIMARY KEY (`ApplicationID`),
  UNIQUE KEY `CertificateNumber` (`CertificateNumber`),
  KEY `StudentID` (`StudentID`),
  KEY `CourseID` (`CourseID`),
  CONSTRAINT `application_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `student` (`StudentID`),
  CONSTRAINT `application_ibfk_2` FOREIGN KEY (`CourseID`) REFERENCES `course` (`CourseID`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `application`
--

LOCK TABLES `application` WRITE;
/*!40000 ALTER TABLE `application` DISABLE KEYS */;
INSERT INTO `application` VALUES (1,1,1,'ANU-2022-08-15-CS-1','2022-08-15',NULL),(2,2,2,'CUEA-2021-08-20-IT-2','2021-08-20',NULL),(3,3,3,'SU-2023-01-10-BA-3','2023-01-10',NULL),(4,4,4,'KU-2020-08-25-ME-4','2020-08-25',NULL),(5,5,5,'UON-2022-08-30-DS-5','2022-08-30',NULL),(6,6,6,'JKUAT-2021-05-05-NU-6','2021-05-05',NULL),(7,7,7,'DU-2019-08-18-LW-7','2019-08-18',NULL),(8,8,8,'MKU-2022-08-22-ED-8','2022-08-22',NULL),(9,9,9,'USIU-2023-02-28-CY-9','2023-02-28',NULL),(10,10,10,'EU-2020-08-12-AE-10','2020-08-12',NULL),(11,12,1,'MKU-CS-1062100-2025','2025-10-20',NULL),(12,13,1,'UON-CS-6639-2024','2024-12-03',NULL),(13,14,1,'JKUAT-CS-1000-2024','2024-10-11',NULL),(14,15,1,'ANU-CS-2000-2025','2025-08-15',NULL),(15,16,6,'JKUAT-2026-08-15-CS-15','2023-11-30',16),(16,17,3,'SU20251127BA16','2025-11-27',16),(17,11,1,'KU20251105CS17','2025-11-05',16),(18,18,18,'NUK20241214ACC18','2024-12-14',16),(19,19,18,'NUK20251120ACC19','2025-11-20',16);
/*!40000 ALTER TABLE `application` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course`
--

DROP TABLE IF EXISTS `course`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `course` (
  `CourseID` int NOT NULL AUTO_INCREMENT,
  `CourseName` varchar(100) NOT NULL,
  `CourseCode` varchar(10) NOT NULL,
  `LevelOfProgram` enum('Certificate','Diploma','Bachelor','Master','PhD') NOT NULL,
  `UniversityID` int NOT NULL,
  `Duration` int DEFAULT NULL,
  PRIMARY KEY (`CourseID`),
  UNIQUE KEY `CourseCode` (`CourseCode`),
  KEY `UniversityID` (`UniversityID`),
  CONSTRAINT `course_ibfk_1` FOREIGN KEY (`UniversityID`) REFERENCES `university` (`UniversityID`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course`
--

LOCK TABLES `course` WRITE;
/*!40000 ALTER TABLE `course` DISABLE KEYS */;
INSERT INTO `course` VALUES (1,'Computer Science','CS','Bachelor',1,4),(2,'Information Technology','IT','Bachelor',2,4),(3,'Business Administration','BA','Diploma',3,2),(4,'Mechanical Engineering','ME','Bachelor',4,5),(5,'Data Science','DS','Master',5,2),(6,'Nursing','NU','Diploma',6,3),(7,'Law','LW','Bachelor',7,5),(8,'Education','ED','Bachelor',8,4),(9,'Cybersecurity','CY','Certificate',9,1),(10,'Agricultural Economics','AE','PhD',10,3),(11,'Physics','Phy','Bachelor',3,4),(12,'Chemistry','Chem','Diploma',1,3),(13,'Agriculture','Agri','Master',6,2),(15,'Computer Science','CompSci','Bachelor',4,4),(16,'Law','Law','Master',8,2),(17,'Economics','Econ','Bachelor',5,4),(18,'Accounting','Acc','Diploma',23,3);
/*!40000 ALTER TABLE `course` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student` (
  `StudentID` int NOT NULL AUTO_INCREMENT,
  `RegistrationNumber` varchar(20) NOT NULL,
  `StudentName` varchar(100) NOT NULL,
  `StudentEmail` varchar(100) DEFAULT NULL,
  `DateOfAdmission` date NOT NULL,
  `DateOfGraduation` date DEFAULT NULL,
  `CourseName` varchar(100) NOT NULL,
  `LevelOfProgram` enum('Certificate','Diploma','Bachelor','Master','PhD') NOT NULL,
  `UniversityID` int NOT NULL,
  PRIMARY KEY (`StudentID`),
  UNIQUE KEY `RegistrationNumber` (`RegistrationNumber`),
  UNIQUE KEY `StudentEmail` (`StudentEmail`),
  KEY `UniversityID` (`UniversityID`),
  CONSTRAINT `student_ibfk_1` FOREIGN KEY (`UniversityID`) REFERENCES `university` (`UniversityID`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student`
--

LOCK TABLES `student` WRITE;
/*!40000 ALTER TABLE `student` DISABLE KEYS */;
INSERT INTO `student` VALUES (1,'REG2026001','Alice Wanjiku','alice.wanjiku@example.com','2022-09-01','2026-06-30','Computer Science','Bachelor',1),(2,'REG2026002','Brian Otieno','brian.otieno@example.com','2021-09-01','2025-06-30','Information Technology','Bachelor',2),(3,'REG2026003','Cynthia Mwangi','cynthia.mwangi@example.com','2023-01-15','2025-10-15','Business Administration','Diploma',3),(4,'REG2026004','David Kamau','david.kamau@example.com','2020-09-01','2025-06-30','Mechanical Engineering','Bachelor',4),(5,'REG2026005','Esther Njeri','esther.njeri@example.com','2022-09-01','2024-12-15','Data Science','Master',5),(6,'REG2026006','Felix Oloo','felix.oloo@example.com','2021-05-10','2024-05-10','Nursing','Diploma',6),(7,'REG2026007','Grace Achieng','grace.achieng@example.com','2019-09-01','2024-06-30','Law','Bachelor',7),(8,'REG2026008','Henry Mutua','henry.mutua@example.com','2022-09-01','2026-12-01','Education','Bachelor',8),(9,'REG2026009','Irene Chebet','irene.chebet@example.com','2023-03-01','2024-03-01','Cybersecurity','Certificate',9),(10,'REG2026010','James Kiptoo','james.kiptoo@example.com','2020-09-01','2023-09-01','Agricultural Economics','PhD',10),(11,'1062093','Lucy Wanjiru','l@wanjiru.com','2024-01-01','2025-11-05','Computer Science','Bachelor',4),(12,'1062100','Josy Wambui','j@wambui.com','2025-01-01','2025-10-20','Law','Master',8),(13,'6639','Lewis Maina','l@maina.com','2020-01-07','2024-12-03','Economics','Bachelor',5),(14,'1000','James Njoroge','j@njoro.com','2022-01-03','2024-10-11','Agriculture','Master',6),(15,'2000','Wilson Wambua','w@wambua.com','2023-01-10','2025-08-15','Chemistry','Diploma',1),(16,'3000','Margaret Nyambura','m@nyambu.com','2021-01-04','2023-11-30','Nursing','Diploma',6),(17,'4000','Joy Wangechi','j@wangechi.com','2023-01-10','2025-11-27','Business Administration','Diploma',3),(18,'5000','New Student','new@student.com','2021-01-14','2024-12-14','Accounting','Diploma',23),(19,'6000','Steve Jobs','s@jobs.com','2022-02-01','2025-11-20','Accounting','Diploma',23);
/*!40000 ALTER TABLE `student` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transcript`
--

DROP TABLE IF EXISTS `transcript`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transcript` (
  `TranscriptID` int NOT NULL AUTO_INCREMENT,
  `StudentID` int NOT NULL,
  `FilePath` varchar(255) NOT NULL,
  `UploadDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`TranscriptID`),
  KEY `StudentID` (`StudentID`),
  CONSTRAINT `transcript_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `student` (`StudentID`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transcript`
--

LOCK TABLES `transcript` WRITE;
/*!40000 ALTER TABLE `transcript` DISABLE KEYS */;
INSERT INTO `transcript` VALUES (1,1,'/transcripts/student1_cs.pdf','2024-07-01 07:15:00'),(2,2,'/transcripts/student2_it.pdf','2024-07-02 08:20:00'),(3,3,'/transcripts/student3_ba.pdf','2024-07-03 06:45:00'),(4,4,'/transcripts/student4_me.pdf','2024-07-04 11:30:00'),(5,5,'/transcripts/student5_ds.pdf','2024-07-05 13:10:00'),(6,6,'/transcripts/student6_nu.pdf','2024-07-06 10:05:00'),(7,7,'/transcripts/student7_lw.pdf','2024-07-07 05:55:00'),(8,8,'/transcripts/student8_ed.pdf','2024-07-08 09:40:00'),(9,9,'/transcripts/student9_cy.pdf','2024-07-09 12:25:00'),(10,10,'/transcripts/student10_ae.pdf','2024-07-10 14:50:00'),(12,11,'uploads/1776788315_Exposure+Triangle.pdf','2026-04-21 16:18:35'),(13,12,'uploads/1062100_1776789499.pdf','2026-04-21 16:38:19'),(14,13,'uploads/6639_1777016466.pdf','2026-04-24 07:41:06'),(15,14,'uploads/1000_1777017755.pdf','2026-04-24 08:02:35'),(16,15,'uploads/2000_1777028609.pdf','2026-04-24 11:03:29'),(17,16,'uploads/3000_1777031614.pdf','2026-04-24 11:53:34'),(18,17,'uploads/4000_1777040737.pdf','2026-04-24 14:25:37'),(19,18,'uploads/5000_1781427669.pdf','2026-06-14 09:01:09'),(20,19,'uploads/6000_1781511287.pdf','2026-06-15 08:14:47');
/*!40000 ALTER TABLE `transcript` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `university`
--

DROP TABLE IF EXISTS `university`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `university` (
  `UniversityID` int NOT NULL AUTO_INCREMENT,
  `UniversityName` varchar(100) NOT NULL,
  `UniversityCode` varchar(15) NOT NULL,
  PRIMARY KEY (`UniversityID`),
  UNIQUE KEY `UniversityName` (`UniversityName`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `university`
--

LOCK TABLES `university` WRITE;
/*!40000 ALTER TABLE `university` DISABLE KEYS */;
INSERT INTO `university` VALUES (1,'Africa Nazarene University','ANU'),(2,'Catholic University of Eastern Africa','CUEA'),(3,'Strathmore University','SU'),(4,'Kenyatta University','KU'),(5,'University of Nairobi','UON'),(6,'Jomo Kenyatta University of Agriculture and Technology','JKUAT'),(7,'Daystar University','DU'),(8,'Mount Kenya University','MKU'),(9,'United States International University Africa','USIU'),(10,'Egerton University','EU'),(22,'Kibabi University','KiU'),(23,'New University of Kenya','NUK');
/*!40000 ALTER TABLE `university` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `UserID` int NOT NULL AUTO_INCREMENT,
  `RealName` varchar(100) NOT NULL,
  `UserPhone` varchar(15) DEFAULT NULL,
  `UserEmail` varchar(100) NOT NULL,
  `UserName` varchar(50) NOT NULL,
  `UserPassword` varchar(255) NOT NULL,
  `UserRole` enum('Admin','UniversityStaff','Regulator') NOT NULL,
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `UserEmail` (`UserEmail`),
  UNIQUE KEY `UserName` (`UserName`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Mercy Wambui','+254711222333','m.wambui@edu.ke','mercy_admin','p@ssword123','Admin','2026-04-23 06:57:43'),(2,'Kelvin Onyango','+254722333444','k.onyango@univ.ac.ke','kevin_staff','staffPass456','UniversityStaff','2026-04-23 06:57:43'),(3,'Catherine Mwende','+254733444555','c.mwende@regulator.go.ke','cate_reg','regGuard789','Regulator','2026-04-23 06:57:43'),(4,'Joseph Omondi','+254700111222','joseph.o@univ.ac.ke','jomondi_staff','secure_pass1','UniversityStaff','2026-04-23 06:57:43'),(5,'Emma Wanjiru','+254755666777','e.wanjiru@admin.com','ewanjiru_adm','admin_vault2','Admin','2026-04-23 06:57:43'),(6,'Fred Kiprop','+254788999000','f.kiprop@regulator.go.ke','fkiprop_reg','safe_access3','Regulator','2026-04-23 06:57:43'),(7,'Zoe Muthoni','+254712345678','z.muthoni@univ.ac.ke','zmuthoni_staff','uni_staff_pw4','UniversityStaff','2026-04-23 06:57:43'),(8,'Hassan Juma','+254799888777','h.juma@univ.ac.ke','hjuma_staff','pass_word5','UniversityStaff','2026-04-23 06:57:43'),(9,'Ivy Cherop','+254766555444','i.cherop@regulator.go.ke','icherop_reg','reg_secure6','Regulator','2026-04-23 06:57:43'),(10,'John Koech','+254744333222','j.koech@admin.com','jkoech_adm','master_key7','Admin','2026-04-23 06:57:43'),(11,'Jeremiah Muguro','0703580060','jeremiahmuguro@gmail.com','jmuguro','$2y$12$XvqBbXIFC20yJEPxEVatxuU5etdk1aVUhsggQ.Kjwu3cPi8pPgRJG','Admin','2026-04-23 06:57:43'),(12,'John Mwangi','0712345678','jmwangi@gmail.com','jmwangi','1234','UniversityStaff','2026-04-23 06:57:43'),(13,'Alex Johnson','123456789','j@lex.com','aj','1234','Admin','2026-04-23 06:57:43'),(15,'Chris Martins','789456123','c@martin.com','cm','1234','UniversityStaff','2026-04-23 06:57:43'),(16,'Peter Adams','789456123','p@adams.com','pa','$2y$12$bdyYPF5gQrnC4sLNlHc5FeRU4NBrR1oa7KrLG/D0bGc11Fe4rCm6e','Regulator','2026-04-23 06:57:43'),(17,'Jack Smith','035800608','j@smith.com','js','$2y$12$8bBpn0Z/Q26LLX4tFs3Zbe66ncZ15UCKQqm7sFMEHY6B4l1NJG9oG','Admin','2026-04-23 06:57:43'),(18,'Henry William','789456123','h@will.com','hw','$2y$12$6qdriL38XBruZLZQ488XqOYJ0vzmtQe.ekckUDYG4xGSs2C/Ddmm2','UniversityStaff','2026-04-23 06:57:43');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visitors`
--

DROP TABLE IF EXISTS `visitors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `visitors` (
  `VisitorID` int NOT NULL AUTO_INCREMENT,
  `VisitorName` varchar(150) NOT NULL,
  `CertificateNumber` varchar(50) NOT NULL,
  `VisitDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`VisitorID`),
  KEY `CertificateNumber` (`CertificateNumber`),
  CONSTRAINT `visitors_ibfk_1` FOREIGN KEY (`CertificateNumber`) REFERENCES `application` (`CertificateNumber`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visitors`
--

LOCK TABLES `visitors` WRITE;
/*!40000 ALTER TABLE `visitors` DISABLE KEYS */;
INSERT INTO `visitors` VALUES (1,'Jojo','SU20251127BA16','2026-06-01 16:18:12'),(2,'Koko','NUK20241214ACC18','2026-06-14 09:39:46'),(3,'Koko','NUK20241214ACC18','2026-06-14 09:40:13'),(4,'Jojo','NUK20241214ACC18','2026-06-14 10:03:04'),(5,'CUEA','NUK20251120ACC19','2026-06-15 08:25:03');
/*!40000 ALTER TABLE `visitors` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-28 21:08:46
