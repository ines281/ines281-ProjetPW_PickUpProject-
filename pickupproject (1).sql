-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : sam. 05 juin 2021 à 00:07
-- Version du serveur :  8.0.21
-- Version de PHP : 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `pickupproject`
--

-- --------------------------------------------------------

--
-- Structure de la table `listeprojets`
--

DROP TABLE IF EXISTS `listeprojets`;
CREATE TABLE IF NOT EXISTS `listeprojets` (
  `idProjet` int NOT NULL AUTO_INCREMENT,
  `nomProjet` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idProjet`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `listeprojets`
--

INSERT INTO `listeprojets` (`idProjet`, `nomProjet`) VALUES
(20, 'projet1'),
(12, 'PickUpProject'),
(13, 'projetTest');

-- --------------------------------------------------------

--
-- Structure de la table `listesujets`
--

DROP TABLE IF EXISTS `listesujets`;
CREATE TABLE IF NOT EXISTS `listesujets` (
  `idSujet` int NOT NULL AUTO_INCREMENT,
  `idProjet` int NOT NULL,
  `nomSujet` varchar(255) DEFAULT NULL,
  `effMin` int NOT NULL DEFAULT '0',
  `effMax` int NOT NULL DEFAULT '0',
  `dateSoutenance` date DEFAULT NULL,
  `lieuSoutenance` varchar(255) DEFAULT NULL,
  `dateRendu` date DEFAULT NULL,
  `lienRendu` varchar(255) DEFAULT NULL,
  `dateLien` datetime DEFAULT NULL,
  `descriptif` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idSujet`),
  KEY `idProjet` (`idProjet`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `listesujets`
--

INSERT INTO `listesujets` (`idSujet`, `idProjet`, `nomSujet`, `effMin`, `effMax`, `dateSoutenance`, `lieuSoutenance`, `dateRendu`, `lienRendu`, `dateLien`, `descriptif`) VALUES
(12, 12, 'VueEleve', 3, 0, '0000-00-00', '/', '0000-00-00', NULL, NULL, NULL),
(10, 12, 'Authentification', 0, 3, '2021-06-10', 'Batiment', '2021-06-11', 'hgoishfeoiho', '2021-06-05 01:20:13', 'descriptifsSujets/ProjetL2pw_20202021.pdf'),
(11, 12, 'VueAdmin', 0, 5, '2021-06-13', '', '2021-06-09', NULL, NULL, NULL),
(13, 13, 'sujet1', 0, 2, '0000-00-00', '', '0000-00-00', NULL, NULL, NULL),
(14, 13, 'sujet2', 0, 6, '0000-00-00', '', '0000-00-00', NULL, NULL, NULL),
(21, 20, 'sujetPres1', 2, 4, '2021-06-09', 'Bat26', '2021-06-18', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `listeutilisateurs`
--

DROP TABLE IF EXISTS `listeutilisateurs`;
CREATE TABLE IF NOT EXISTS `listeutilisateurs` (
  `idUtilisateur` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) DEFAULT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `identifiant` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `nbGroupe` int DEFAULT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idUtilisateur`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `listeutilisateurs`
--

INSERT INTO `listeutilisateurs` (`idUtilisateur`, `nom`, `prenom`, `identifiant`, `password`, `nbGroupe`, `admin`, `email`) VALUES
(15, 'Simon', 'Antonin', 'asimon', '$2y$10$oQtlmIs307VRvzVlDyzwjOpmUSpFtGGHiKTFE48oc9HHkA5x0vuBi', 2, 0, 'tristan.le-saux@etudiant.univ-rennes1.fr'),
(26, 'Sans', 'Virginie', 'vsans', '$2y$10$QCjKuYUJpJoGNSChCUYc5.5SsmGuau6lHyKVVM1W4vmf25D6nTbhu', 1, 1, 'virgnie.sans@univ-rennes1.fr'),
(25, 'Khelil', 'Ines', 'ikhelil', '$2y$10$GqOifd/pfmZpSVmk0V664OYKIK/V5qCSCYnv9Sthgo.6tLi6tf5jS', 1, 1, 'ines.khelil@etudiant.univ-rennes1.fr'),
(16, 'Santini', 'Emma', 'ESantini', '$2y$10$mGjOzsZ71ZJxPG/vd14Cc.7qRcyMxcEbCD6m76DRJzj/I..FyjdSa', 3, 0, NULL),
(17, 'Bahuon', 'Yoann', 'YBahuon', '$2y$10$4CSdRq1443vZk7vR1JQs/OxjTjmLXp0L/AHydUzb1.Obfj1s3V51u', 1, 0, NULL),
(18, 'Raulin', 'Bastien', 'BRaulin', '$2y$10$02E2V3fmhb6Fe5F8fiRbDO12cuvHm8o58DAGyzgznrJBVuvKvEMHK', 5, 0, NULL),
(19, 'Gabaud', 'Julien', 'JGabaud', '$2y$10$ADvaB/iwsJGYIdPH.lHc8u6L4/9Iqv0rFAA872kTRodgp0HV6Jymi', 2, 0, NULL),
(20, 'Hardouin', 'Maelle', 'MHardouin', '$2y$10$oH2gWAi8/q.31CZtvBMSLOor6RXOap5irdwZLJ9ByFVt3nnk85CQ2', 4, 0, NULL),
(24, 'LeSaux', 'Tristan', 'tlesaux', '$2y$10$0U7mt/WMgQtmyhxlYd7TyuvPeBFjNXxz16eM3UJaiIcfJZTbI/XH2', 1, 1, 'tlesaux22@gmail.com'),
(33, 'Dupont', 'Henry', 'HDupont', '$2y$10$fnYTsJ9ooa/H1ULNEqKGAef.V.5pxpNhfTg30XkQ6Ys12zDB0RDIu', 5, 0, NULL),
(34, 'Dupont', 'Raphael', 'rdupont', '$2y$10$vmIXR5JDlP4tk2ao489pfuRLQuF/mkV6UdSIddyqy8lFkDTfbktOq', 4, 0, 'raphdupont@gmail.com');

-- --------------------------------------------------------

--
-- Structure de la table `matchprojet`
--

DROP TABLE IF EXISTS `matchprojet`;
CREATE TABLE IF NOT EXISTS `matchprojet` (
  `idProjet` int NOT NULL,
  `idUtilisateur` int NOT NULL,
  `idSujet` int DEFAULT NULL,
  KEY `idProjet` (`idProjet`),
  KEY `idUtilisateur` (`idUtilisateur`),
  KEY `idSujet` (`idSujet`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `matchprojet`
--

INSERT INTO `matchprojet` (`idProjet`, `idUtilisateur`, `idSujet`) VALUES
(12, 17, 11),
(12, 15, 10),
(12, 16, NULL),
(12, 18, 10),
(12, 19, 10),
(13, 18, 13),
(13, 17, 14),
(13, 15, NULL),
(13, 16, NULL),
(13, 19, 14),
(13, 20, NULL),
(12, 20, NULL),
(20, 33, NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
