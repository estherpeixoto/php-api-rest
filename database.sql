CREATE DATABASE IF NOT EXISTS `rest_api`;

USE `rest_api`;

DROP TABLE IF EXISTS `empresas`;

CREATE TABLE `empresas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cnpj` varchar(14) NOT NULL,
  `razao_social` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
