-- MySQL dump 10.13  Distrib 8.0.44, for Linux (x86_64)
--
-- Host: localhost    Database: project_platform
-- ------------------------------------------------------
-- Server version	8.0.44-0ubuntu0.22.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `parent_id` int DEFAULT NULL,
  PRIMARY KEY (`category_id`),
  KEY `fk_category_parent` (`parent_id`),
  CONSTRAINT `fk_category_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=216 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (129,'EOA0 - Projectes d\'obra civil',129),(130,'EOA1 - Projectes d\'obra civil (Sobrestants)',129),(131,'EOA2 - Projectes d\'obra civil (ús d\'aplicacions SIG)',129),(132,'EOB0 - Projectes d\'edificació',129),(133,'EOB1 - Projectes d\'edificació (rehabilitació i restauració)',129),(134,'EOC0 - Organització i control d\'obres de construcció',129),(136,'FMA0 - Construccions metàl·liques',136),(137,'FMB0 - Programació de la producció en fabricació mecànica',136),(138,'FMB1 - Programació de la producció en fabricació mecànica (motlles i matrius)',136),(139,'FMC0 - Disseny en fabricació mecànica',136),(140,'FMC1 - Disseny en fabricació mecànica (desenvolupament virtual de l\'automòbil)',136),(141,'FMD0 - Programació de la producció en emmotllament de metalls i polímers',136),(143,'FSA0 - Disseny i moblament',143),(144,'FSA1 - Disseny i moblament (construccions efímeres i decorats)',143),(146,'HTA0 - Agències de viatges i gestió d\'esdeveniments',146),(147,'HTB0 - Gestió d\'allotjaments turístics',146),(148,'HTD0 - Direcció de cuina',146),(149,'HTE0 - Direcció de serveis en restauració',146),(150,'HTF0 - Guia, informació i assistència turístiques',146),(151,'HTF1 - Guia, informació i assistència turístiques (animació turística)',146),(153,'IAA0 - Vitivinicultura',153),(154,'IAB0 - Processos i qualitat en la indústria alimentària',153),(156,'ICA0 - Administració de sistemes informàtics en xarxa',156),(157,'ICA1 - Administració de sistemes informàtics en xarxa (ciberseguretat)',156),(158,'ICB0 - Desenvolupament d\'aplicacions multiplataforma',156),(159,'ICB1 - Desenvolupament d\'aplicacions multiplataforma (informàtica aplicada a la logística)',156),(160,'ICB2 - Desenvolupament d\'aplicacions multiplataforma (videojocs i oci digital)',156),(161,'ICC0 - Desenvolupament d\'aplicacions web',156),(162,'ICC1 - Desenvolupament d\'aplicacions web (bioinformàtica)',156),(164,'IMA0 - Manteniment d\'instal·lacions tèrmiques i de fluids',164),(165,'IMB0 - Desenvolupament de projectes d\'instal·lacions tèrmiques i de fluids',164),(166,'IMC0 - Mecatrònica industrial',164),(167,'IMC1 - Mecatrònica industrial (fabricació de productes ceràmics)',164),(169,'IPA0 - Assessoria d\'imatge personal i corporativa',169),(170,'IPB0 - Estètica integral i benestar',169),(171,'IPC0 - Estilisme i direcció de perruqueria',169),(172,'IPD0 - Caracterització i maquillatge professional',169),(174,'ISA0 - Realització de projectes d\'audiovisuals i espectacles',174),(175,'ISB0 - Il·luminació, captació i tractament d\'imatge',174),(176,'ISC0 - So per a audiovisuals i espectacles',174),(177,'ISD0 - Producció d\'audiovisuals i espectacles',174),(178,'ISE0 - Animacions 3D, jocs i entorns interactius',174),(179,'ISE1 - Animacions 3D, jocs i entorns interactius (móns virtuals, realitat augmentada i gamificació)',174),(181,'MPA0 - Transport marítim i pesca d\'altura',181),(182,'MPB0 - Aqüicultura',181),(183,'MPC0 - Organització del manteniment de la maquinària de vaixells i embarcacions',181),(185,'QUA0 - Química industrial',185),(186,'QUB0 - Fabricació de productes farmacèutics, biotecnològics i afins',185),(187,'QUD0 - Laboratori d\'anàlisi i control de qualitat',185),(189,'SAA0 - Pròtesis dentals',189),(190,'SAB0 - Ortopròtesi i productes de suport',189),(191,'SAC0 - Anatomia patològica i citodiagnòstic',189),(192,'SAD0 - Documentació i administració sanitària',189),(193,'SAD1 - Documentació i administració sanitàries (gestió de dades)',189),(194,'SAE0 - Laboratori clínic i biomèdic',189),(195,'SAE1 - Laboratori clínic i biomèdic (recerca)',189),(196,'SAF0 - Radioteràpia i dosimetria',189),(197,'SAG0 - Audiologia protètica',189),(198,'SAH0 - Higiene bucodental',189),(199,'SAI0 - Imatge per al diagnòstic i medicina nuclear',189),(201,'SCA0 - Animació sociocultural i turística',201),(202,'SCB0 - Educació Infantil',201),(203,'SCC0 - Integració social',201),(204,'SCD0 - Promoció d\'igualtat de gènere',201),(205,'SCE0 - Mediació comunicativa',201),(207,'SMA0 - Educació i control ambiental',207),(208,'SMB0 - Coordinació d\'emergències i protecció civil',207),(210,'TMA0 - Automoció',210),(211,'TMA1 - Automoció (Vehicles industrials)',210),(213,'TXA0 - Disseny tècnic en tèxtil i pell',213),(214,'TXB0 - Vestuari a mida i d\'espectacles',213),(215,'TXE0 - Patronatge i moda',213);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `likes`
--

DROP TABLE IF EXISTS `likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `likes` (
  `user_id` int NOT NULL,
  `project_id` int NOT NULL,
  PRIMARY KEY (`user_id`,`project_id`),
  KEY `fk_likes_project` (`project_id`),
  CONSTRAINT `fk_likes_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_likes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `likes`
--

LOCK TABLES `likes` WRITE;
/*!40000 ALTER TABLE `likes` DISABLE KEYS */;
INSERT INTO `likes` VALUES (8,1),(1,2),(3,2),(10,2),(1,4),(1,5),(6,5),(6,6);
/*!40000 ALTER TABLE `likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `message` (
  `message_id` int NOT NULL AUTO_INCREMENT,
  `remitent_id` int NOT NULL,
  `destination_id` int NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`message_id`),
  KEY `fk_message_remitent` (`remitent_id`),
  KEY `fk_message_destination` (`destination_id`),
  CONSTRAINT `fk_message_destination` FOREIGN KEY (`destination_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_message_remitent` FOREIGN KEY (`remitent_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message`
--

LOCK TABLES `message` WRITE;
/*!40000 ALTER TABLE `message` DISABLE KEYS */;
/*!40000 ALTER TABLE `message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project`
--

DROP TABLE IF EXISTS `project`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project` (
  `project_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` varchar(120) NOT NULL,
  `description` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `video_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`project_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `project_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project`
--

LOCK TABLES `project` WRITE;
/*!40000 ALTER TABLE `project` DISABLE KEYS */;
INSERT INTO `project` VALUES (1,1,'Projecte de Robòtica Industrial','Els alumnes de Mecatrònica presenten el seu projecte final de robòtica industrial.','/uploads/logo1.png','/uploads/video1.mp4'),(2,2,'Desenvolupament d\'App Mòbil','Projecte final de DAM: una aplicació mòbil per a la gestió d\'esdeveniments.','/uploads/logo2.png','/uploads/video2.mp4'),(4,4,'Taller de Cuina Mediterrània','Els alumnes de Cuina preparen un menú complet de cuina mediterrània.','/uploads/logo4.png','/uploads/video4.mp4'),(5,5,'Disseny Gràfic Editorial','Projecte de disseny d\'una revista digital amb contingut cultural.','/uploads/logo5.png','/uploads/video5.mp4'),(6,6,'Manteniment d\'Automòbils','Pràctica de taller on es realitza el manteniment complet d\'un vehicle.','/uploads/logo6.png','/uploads/video6.mp4');
/*!40000 ALTER TABLE `project` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_category`
--

DROP TABLE IF EXISTS `project_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_category` (
  `project_id` int NOT NULL,
  `category_id` int NOT NULL,
  PRIMARY KEY (`project_id`,`category_id`),
  KEY `fk_pc_category` (`category_id`),
  CONSTRAINT `fk_pc_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pc_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_category`
--

LOCK TABLES `project_category` WRITE;
/*!40000 ALTER TABLE `project_category` DISABLE KEYS */;
INSERT INTO `project_category` VALUES (6,129),(6,131),(4,132),(2,134),(1,136),(2,136),(5,136),(5,138),(5,140),(2,146),(4,147),(6,147),(1,149),(5,149),(6,151);
/*!40000 ALTER TABLE `project_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_category`
--

DROP TABLE IF EXISTS `user_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_category` (
  `user_id` int NOT NULL,
  `category_id` int NOT NULL,
  PRIMARY KEY (`user_id`,`category_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `user_category_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `user_category_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_category`
--

LOCK TABLES `user_category` WRITE;
/*!40000 ALTER TABLE `user_category` DISABLE KEYS */;
INSERT INTO `user_category` VALUES (14,129),(16,129),(27,129),(29,129),(32,129),(18,130),(31,130),(33,130),(39,130),(14,131),(24,131),(9,132),(21,132),(32,132),(13,133),(24,133),(10,134),(12,136),(36,136),(22,137),(34,137),(1,138),(18,138),(34,138),(1,139),(3,139),(9,139),(11,139),(13,139),(26,139),(37,139),(38,139),(40,139),(23,140),(27,140),(30,140),(38,140),(40,140),(2,141),(7,141),(10,141),(12,141),(34,141),(2,143),(3,143),(4,143),(6,143),(21,143),(38,143),(40,143),(16,144),(26,144),(29,144),(38,144),(40,144),(7,146),(20,146),(28,146),(33,146),(11,147),(15,148),(20,148),(25,148),(28,148),(35,148),(4,149),(35,149),(37,149),(5,150),(8,150),(17,150),(19,150),(22,150),(31,150),(39,150),(5,151),(6,151),(8,151),(15,151),(17,151),(19,151),(23,151),(25,151),(30,151),(36,151),(41,164),(41,172),(41,181);
/*!40000 ALTER TABLE `user_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `user_name` varchar(100) NOT NULL,
  `user_surname` varchar(100) NOT NULL,
  `description` text,
  `email` varchar(150) NOT NULL,
  `password` char(64) NOT NULL,
  `phone_number` bigint DEFAULT NULL,
  `user_type` enum('center','company') NOT NULL,
  `entity_name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `validation_hash` varchar(64) DEFAULT NULL,
  `validation_expires` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'IES','Centre','Institut públic amb una àmplia oferta de cicles formatius.','info@iesterradas.cat','c0c71b5b6afc2ffeded5652d5b61a586e305491723b0ed4069e7a7ac0592148f',934567890,'center','IES Esteve Terradas i Illa',0,NULL,NULL),(2,'Institut','Centre','Centre de referència en formació professional.','secretaria@ferreria.cat','1b55d9102835a5083de19c3814af4072d68bf9320fea275356d731b0c0983724',934567891,'center','Institut La Ferreria',0,NULL,NULL),(3,'Institut','Centre','Especialitzat en cicles de informàtica i disseny.','contacte@inspoblenou.cat','788acf36fb5e5388302f5d3aa49452adce885aeaa75c199aeb9baac7960aee77',934567892,'center','Institut Poblenou',0,NULL,NULL),(4,'IES','Centre','Centre amb tradició en arts gràfiques.','administracio@iesjoanbrossa.cat','3fd10714c4c9bc382657e6d9a6e0d797a7a5a9416ad5c821aae6646c890d2a16',934567893,'center','IES Joan Brossa',0,NULL,NULL),(5,'Institut','Centre','Formació professional en sanitat i serveis.','info@ieshl.cat','1727361e5a8ab0decbc91972fdc38cb66791f116342d29207b7a03269d206fc6',934567894,'center','Institut L\'Hospitalet',0,NULL,NULL),(6,'IES','Centre','Referent en cicles agraris i medi ambient.','garrotxa@iesgarrotxa.cat','ba5898e86dc4fb93b52551677a5e96df266e9287a8d648294975fe70db382897',934567895,'center','IES La Garrotxa',0,NULL,NULL),(7,'Institut','Centre','Especialitzat en electricitat i automoció.','torrevicens@iestorrevicens.cat','75f2a2f530d6137d1bc2ed2d3e0bcda42cbad3cd89d4b5e607603a087c571734',934567896,'center','Institut Torre Vicens',0,NULL,NULL),(8,'IES','Centre','Formació en hosteleria i turisme.','montserrat@iesmontserrat.cat','5eb503f6e047d6c9a29b6d5cab310a18741fae9dff6253c50df904b50758af99',934567897,'center','IES Montserrat',0,NULL,NULL),(9,'Institut','Centre','Centre tecnològic amb cicles d\'informàtica.','provencana@iesprovencana.cat','57a0ee97f9224724c0296efdf2b9a8f089cc59364ee1911a9c24a2b0b2483ccd',934567898,'center','Institut Provençana',0,NULL,NULL),(10,'IES','Centre','Especialitzat en imatge personal.','dali@iessalvadordali.cat','eca1336d2105e42befd74e909e40bfeccc17bb3251cd2f6bc0d6e35d8e36d3d3',934567899,'center','IES Salvador Dalí',0,NULL,NULL),(11,'Institut','Centre','Centre històric amb cicles de grau superior.','bonanova@iesbonanova.cat','1a58c073beb0c41a8f9bc2322fd40530a5acb1b17bc7039d4cb15577595d7c81',934567800,'center','Institut Bonanova',0,NULL,NULL),(12,'IES','Centre','Referent en comerç i màrqueting.','consellcent@iesconsellcent.cat','3ae978431be22ec461bd87fa5a27f5a709be4709382b2cf7946e6b75c6bcc7a6',934567801,'center','IES Consell de Cent',0,NULL,NULL),(13,'Institut','Centre','Tecnològic amb cicles industrials.','valles@iesvalles.cat','5e685d4e08b7f673da637722dc54ae80ce2139af8e71bc8a4f12cdfc33e7c115',934567802,'center','Institut Vallès',0,NULL,NULL),(14,'IES','Centre','Centre integral de formació professional.','guineueta@iesguineueta.cat','53bbe35302b2a4291b28ee981b00c5a4b37fd71078b0f76bb2f9ef5f6d5a4b54',934567803,'center','IES La Guineueta',0,NULL,NULL),(15,'Institut','Centre','Especialitzat en construcció.','besos@iesbarribesos.cat','629b738fab966d2fab18024d3ce6aa7c7ff3ea3e1456029ec2c92f8e9cf7dcf1',934567804,'center','Institut Barri Besòs',0,NULL,NULL),(16,'IES','Centre','Centre de referència en serveis socioculturals.','lamina@ieslamina.cat','8883b2e3b650ae2cf7621c6ba8f8e2a044a9b635ad347d4632677b6f317c8a58',934567805,'center','IES La Mina',0,NULL,NULL),(17,'Institut','Centre','Formació en química i indústries alimentàries.','canpeixauet@iescanpeixauet.cat','40b245ba55e1cf5ef6d404890c0b1a18f53b4833b795ac8d6e2259b53fcc4fe0',934567806,'center','Institut Can Peixauet',0,NULL,NULL),(18,'IES','Centre','Centre tecnològic al districte 22@.','icaria@iesicaria.cat','ac79d435d838fbb2cbeb02fa521ab10fcc83ce42208551d861c7941b42f5f126',934567807,'center','IES Icària',0,NULL,NULL),(19,'Institut','Centre','Formació professional amb vistes al mar.','montjuic@iesmontjuic.cat','6a8e0f344a9bca6a0e9de3231f6502c9f7e87d385746391cb4f5ac363b430adc',934567808,'center','Institut Montjuïc',0,NULL,NULL),(20,'IES','Centre','Centre especialitzat en marítim-pesquera.','marines@ieslesmarines.cat','59f4b3c0fb4664dde69489254ade49cc8ed467bec8213b289fa7db00a47c466f',934567809,'center','IES Les Marines',0,NULL,NULL),(21,'TechSolutions','Empresa','Empresa tecnològica especialitzada en desenvolupament de software.','rrhh@techsolutions.cat','4b0e27ca51d536eb87c843ebfdc2fdfc18496ec10bf9bb46bde9f91db67e5911',936543210,'company','TechSolutions Barcelona',0,NULL,NULL),(22,'Gràfiques','Empresa','Impressió digital i arts gràfiques des de 1995.','info@grafiquesmodernes.cat','78fdb75ac5b4892826eab5943c54fe3238afe62fd5a81e0d7bb233ed4d86c64f',936543211,'company','Gràfiques Modernes SL',0,NULL,NULL),(23,'Restaurant','Empresa','Restaurant gourmet amb estrella Michelin.','angles@restaurant.cat','8b3f98473b65580562c2951eea987c09ebf8f7cb9b16319aefc4965e4674630c',936543212,'company','Restaurant Els Àngels',0,NULL,NULL),(24,'Clínica','Empresa','Clínica dental especialitzada en ortodoncia.','smile@clinicadental.cat','c484c423187a913b9b440dc2b3d35f797117feb48b37d3590b3fbb6385c33343',936543213,'company','Clínica Dental Smile',0,NULL,NULL),(25,'AutoMecànica','Empresa','Taller mecànic amb 20 anys d\'experiència.','taller@automecanica.cat','2c42aa8e458e365373cfeda02c5e98f6e4cc7bed9089b793744aedcad7c58e19',936543214,'company','AutoMecànica Ràpid',0,NULL,NULL),(26,'Disseny','Empresa','Agència de disseny web i màrqueting digital.','hola@dissenyweb.cat','ec0ef0c111dbdf44f0680f00d7b83b6aa61122f7a844f41d0fb66eb35eda1ed1',936543215,'company','Disseny Web Creatiu',0,NULL,NULL),(27,'Farmacèutics','Empresa','Xarxa de farmàcies amb servei integral.','associats@farmacia.cat','aa4c3974045e31cfb8612075f8ec725ae7485221b11f5cc0ee046aa92a460814',936543216,'company','Farmacèutics Associats',0,NULL,NULL),(28,'Constructora','Empresa','Empresa de construcció i rehabilitació.','info@constructora.cat','94d5f5688194fb0e758299fe8e585c44d6a11dc368f2c6d33694b5c3e4db3ec1',936543217,'company','Constructora Mediterrània',0,NULL,NULL),(29,'Perruqueria','Empresa','Perruqueria i centre d\'estètica unisex.','style@perruqueria.cat','c185de5fb0ffef3adde71d1f0015ddfda0326d0053c17b03b60bd2a257e3e81a',936543218,'company','Perruqueria Style',0,NULL,NULL),(30,'Electricitat','Empresa','Instal·lacions elèctriques i domòtica.','electric@electricitat.cat','8c1b0236ae7fe89267cbebcb850b605c97e57b81599f90693149c347f4a08b14',936543219,'company','Electricitat BCN',0,NULL,NULL),(31,'Hotel','Empresa','Hotel 4 estrelles a primera línia de platja.','reserves@hotelmarina.cat','dc208212611c2d5a7b7ed0f0245c7f3d9bc7cac8acbbf2085b5aee7e49579cd7',936543220,'company','Hotel Marina Beach',0,NULL,NULL),(32,'Laboratoris','Empresa','Laboratori d\'anàlisis clíniques i biomèdiques.','lab@sanit.cat','45dfa359f82af4717b6fb4c76fb59060ac695705df83d46a8befb38804810925',936543221,'company','Laboratoris Sanit',0,NULL,NULL),(33,'Mecanitzats','Empresa','Empresa de mecanització CNC i prototipat.','precisio@mecanitzats.cat','39411f49d41db4face000513e81c6e95ec66b07bee98c21d5db84841276c101b',936543222,'company','Mecanitzats Precisió',0,NULL,NULL),(34,'Agricultura','Empresa','Producció i venda de productes ecològics.','eco@agricultura.cat','65f207ce667df30c4b74915578d914e05efbaa153f633a6395fd67fc0f1b9b0d',936543223,'company','Agricultura Ecològica',0,NULL,NULL),(35,'Transportes','Empresa','Empresa de transport i logística.','transport@rapid.cat','ed64504cdc08ebca4bb252610423d71c6b2d2d019391b0229a816f7dc6bdade4',936543224,'company','Transportes Ràpid',0,NULL,NULL),(36,'Estètica','Empresa','Centre d\'estètica i benestar integral.','bella@estetica.cat','e15b67f09b0dfe1a0b1ca98f2f563f8dbe453ed90b831258ec3aac4211f4b2f8',936543225,'company','Estètica Bella',0,NULL,NULL),(37,'Informàtica','Empresa','Serveis informàtics per a empreses.','soporte@informaticapro.cat','3c38ccd3c5d259bb32188d9c46bfa1b6f4a36e82cb7d7fd10e232904b4835fcb',936543226,'company','Informàtica Pro',0,NULL,NULL),(38,'Cuines','Empresa','Disseny i instal·lació de cuines a mida.','cuines@innovadores.cat','1f6411ea9792916172dbba6a2be314525bc56bff15daf353aeaa434a428c18a5',936543227,'company','Cuines Innovadores',0,NULL,NULL),(39,'Nàutica','Empresa','Manteniment i reparació d\'embarcacions.','nautica@costabrava.cat','0b3b319a2a9e20bdbaf2136bd2f9e6c9b03d72624e3bde8e11a742efecbe1406',936543228,'company','Nàutica Costa Brava',0,NULL,NULL),(40,'Moda','Empresa','Disseny i confecció de moda juvenil.','moda@jove.cat','49eec563eff010b60cb0ae4443fd36a6c5fa89629601061dab8f166ede9ff50b',936543229,'company','Moda Jove',0,NULL,NULL),(41,'Plural','Informatica',NULL,'pluralinf@gmail.com','61d58cbe5bad3042630f503dbea9fa15177233a930129b826c6b33b808b81323',699789087,'company','nose',0,'ba88b78b5d3f87c67f6e8507bf6f4604','2026-01-24 13:26:40'),(42,'hector','cardizales',NULL,'loopsis753@gmail.com','95f5ea5e5b714ffc9591619406068f9c2a2eeffc3d937b86505cdcc85aa29783',NULL,'company','hector.sa',0,'eb70474619e71d5e8737bff21e2b8d64','2026-01-24 15:23:03'),(47,'gerard2','sanchez',NULL,'gelexart90@gmail.com','95f5ea5e5b714ffc9591619406068f9c2a2eeffc3d937b86505cdcc85aa29783',NULL,'company','pepe',0,'db498170183ff07d2d25abc4f6572820','2026-01-28 15:49:26');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-26 16:04:06
