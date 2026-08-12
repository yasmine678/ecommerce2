-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 03 août 2026 à 18:19
-- Version du serveur : 8.4.7
-- Version de PHP : 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `paecomdb`
--

-- --------------------------------------------------------

--
-- Structure de la table `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE IF NOT EXISTS `cart` (
  `cId` int NOT NULL AUTO_INCREMENT,
  `usId` int NOT NULL,
  `cquantity` int NOT NULL DEFAULT '1',
  `prodId` int DEFAULT NULL,
  `servId` int DEFAULT NULL,
  PRIMARY KEY (`cId`),
  KEY `usId` (`usId`),
  KEY `fk_cart_product` (`prodId`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cart`
--

INSERT INTO `cart` (`cId`, `usId`, `cquantity`, `prodId`, `servId`) VALUES
(28, 8, 1, 9, NULL),
(27, 8, 1, 10, NULL),
(26, 8, 2, 5, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `catId` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `catImage` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`catId`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `category`
--

INSERT INTO `category` (`catId`, `name`, `description`, `catImage`) VALUES
(4, 'Textiles', 'monocouleur, multicouleur', 'prod_6a6a0a3b1546d4.64751599.jpg'),
(8, 'Brevage', 'Jus, boissons lactées, sans alcool et avec alcool', 'prod_6a6a0cb0e566e2.26549649.jpg'),
(6, 'Viande', 'rouge, blanc', 'prod_6a6a0d2eee20c6.47123225.jpg'),
(7, 'cereales', 'jardin comme champ', 'prod_6a6a114d1ecb09.12869792.jpg'),
(9, 'Legumes', 'jardin comme ferme', 'prod_6a69c16c8029d4.06738812.jpg'),
(10, 'Fruits', 'sucré, acide, amer, juteux sec', 'prod_6a69c149078342.05656723.jpg'),
(11, 'Accessoirs de vêtements', 'complet', 'prod_6a6a1165ec14f4.10165118.jpg'),
(12, 'Produits ménagés', 'produits de nettoyage, outils de nettoyage', 'prod_6a6a0e678cd091.45900908.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `oId` int NOT NULL AUTO_INCREMENT,
  `proId` int DEFAULT NULL,
  `servId` int DEFAULT NULL,
  `cId` int NOT NULL,
  `status` enum('en_attente','en_cours','annulee','livee','expediee') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `quantity` int NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `usId` int NOT NULL,
  PRIMARY KEY (`oId`),
  KEY `proId` (`proId`),
  KEY `cId` (`cId`),
  KEY `fk_users_usId` (`usId`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `orders`
--

INSERT INTO `orders` (`oId`, `proId`, `servId`, `cId`, `status`, `quantity`, `note`, `usId`) VALUES
(2, 6, NULL, 0, 'en_attente', 2, NULL, 0),
(3, 6, NULL, 0, 'en_cours', 2, NULL, 2),
(4, 5, NULL, 0, 'en_cours', 6, NULL, 2),
(5, 5, NULL, 0, 'en_cours', 1, NULL, 2),
(6, 5, NULL, 0, 'en_attente', 1, NULL, 2),
(7, 6, NULL, 0, 'en_attente', 1, NULL, 2),
(8, 5, NULL, 0, 'en_attente', 2, NULL, 2),
(9, 5, NULL, 0, 'annulee', 1, NULL, 8),
(10, 5, NULL, 0, 'livee', 1, NULL, 9),
(11, 13, NULL, 0, 'livee', 1, NULL, 9),
(12, 12, NULL, 0, 'livee', 1, NULL, 9),
(13, 5, NULL, 0, 'livee', 1, NULL, 8),
(14, 5, NULL, 0, 'annulee', 1, NULL, 8),
(15, 6, NULL, 0, 'livee', 2, NULL, 8),
(16, 11, NULL, 0, 'annulee', 2, NULL, 8),
(17, 5, NULL, 0, 'livee', 2, NULL, 8),
(18, 8, NULL, 0, 'livee', 1, NULL, 8),
(19, 13, NULL, 0, 'livee', 1, NULL, 8),
(20, 10, NULL, 0, 'livee', 1, NULL, 8),
(21, 6, NULL, 0, 'livee', 1, NULL, 8),
(22, 5, NULL, 0, 'annulee', 1, 'Merci de livrer avant 18h, portail bleu au fond de la cour.', 8),
(23, 6, NULL, 0, 'livee', 2, 'mais sec, rouge, 10kg, Adresse(segbe)', 8);

-- --------------------------------------------------------

--
-- Structure de la table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `proId` int NOT NULL AUTO_INCREMENT,
  `proName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `prodescription` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `catId` int NOT NULL,
  PRIMARY KEY (`proId`),
  KEY `catId` (`catId`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `product`
--

INSERT INTO `product` (`proId`, `proName`, `price`, `prodescription`, `image`, `catId`) VALUES
(5, 'tissus', 3500, 'coton, soie', 'prod_6a623adb21d2c9.07788147.jpg', 4),
(6, 'Maïs', 500, 'Blanc, jaune, sec, moue', 'prod_6a623c32d62752.80858009.jpg', 7),
(4, 'choux', 300, 'verte, rouge, blanche, violette', 'prod_6a623be2d5cca4.93603492.jpg', 9),
(7, 'Poulet', 1500, 'entier, cuisse, ....', 'prod_6a624bb357ceb4.43724100.jpg', 6),
(8, 'viande de boeuf', 2800, 'le kilo', 'prod_6a67348696a142.55223815.jpg', 6),
(9, 'Jus d\'orange', 1000, 'la bouteille', 'prod_6a6734fb6d6982.72372501.jpg', 8),
(10, 'Pomme rouge', 2000, 'le kilo', 'prod_6a6735a47df502.57860914.jpg', 10),
(11, 'Farine de riz', 1200, 'le kilo', 'prod_6a67372ccf4493.19780812.jpg', 7),
(12, 'Montre', 15000, 'Pour homme', 'prod_6a673ae6271cf2.66802444.jpg', 11),
(13, 'Bague', 2000, 'Pour femme', 'prod_6a673b0f7ea666.64972376.jpg', 11),
(14, 'Detergeant', 1000, 'en poudre(le kilo)', 'prod_6a673cc752c742.45621639.jpg', 12);

-- --------------------------------------------------------

--
-- Structure de la table `service`
--

DROP TABLE IF EXISTS `service`;
CREATE TABLE IF NOT EXISTS `service` (
  `servId` int NOT NULL AUTO_INCREMENT,
  `servName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `priceHours` decimal(10,0) NOT NULL,
  `serImage` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `available` tinyint(1) NOT NULL DEFAULT '1',
  `provider` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catId` int NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`servId`),
  KEY `catId` (`catId`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `usId` int NOT NULL AUTO_INCREMENT,
  `lastName` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `firstName` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('client','manager') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'client',
  `profil` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastLogin` datetime DEFAULT NULL,
  PRIMARY KEY (`usId`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`usId`, `lastName`, `firstName`, `email`, `password`, `role`, `profil`, `lastLogin`) VALUES
(9, 'ZOMAYI', 'Samira', 'yue@gmail.com', '$2y$10$wHHIha4Ptl.OL1qqtWiReubKfkXy/ay.uzFaAoxclRZwdT0k5RCgS', 'client', NULL, NULL),
(7, 'ZOMAYI', 'Yasmine Yayra', 'yas@gmail.com', '$2y$10$wHHIha4Ptl.OL1qqtWiReubKfkXy/ay.uzFaAoxclRZwdT0k5RCgS', 'manager', NULL, NULL),
(8, 'ALASSANI', 'Samira', 'sam@gmail.com', '$2y$10$wHHIha4Ptl.OL1qqtWiReubKfkXy/ay.uzFaAoxclRZwdT0k5RCgS', 'client', NULL, NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
