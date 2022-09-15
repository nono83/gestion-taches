-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  jeu. 15 sep. 2022 à 09:11
-- Version du serveur :  10.4.10-MariaDB
-- Version de PHP :  7.4.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `gestion_taches`
--

-- --------------------------------------------------------

--
-- Structure de la table `listes`
--

DROP TABLE IF EXISTS `listes`;
CREATE TABLE IF NOT EXISTS `listes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `listes`
--

INSERT INTO `listes` (`id`, `nom`) VALUES
(1, 'liste'),
(6, 'liste4'),
(7, 'liste13'),
(8, 'liste5');

-- --------------------------------------------------------

--
-- Structure de la table `taches`
--

DROP TABLE IF EXISTS `taches`;
CREATE TABLE IF NOT EXISTS `taches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `statut` varchar(50) NOT NULL DEFAULT 'non validée',
  `liste_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `liste_id` (`liste_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `taches`
--

INSERT INTO `taches` (`id`, `nom`, `statut`, `liste_id`) VALUES
(4, 'test15', 'non validée', 1),
(7, 'test3', 'non validée', 6),
(8, 'test4', 'validée', 1),
(9, 'test7', 'validée', 1),
(13, 'test14', 'validée', 6);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `taches`
--
ALTER TABLE `taches`
  ADD CONSTRAINT `fk_liste_id` FOREIGN KEY (`liste_id`) REFERENCES `listes` (`id`),
  ADD CONSTRAINT `taches_ibfk_1` FOREIGN KEY (`liste_id`) REFERENCES `listes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
