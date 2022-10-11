-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 04 oct. 2022 à 18:34
-- Version du serveur : 5.7.36
-- Version de PHP : 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `calendrier`
--

-- --------------------------------------------------------

--
-- Structure de la table `conge`
--

DROP TABLE IF EXISTS `conge`;
CREATE TABLE IF NOT EXISTS `conge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `datedebut` date NOT NULL,
  `datefin` date NOT NULL,
  `state` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `typeconge` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2ED89348A76ED395` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `conge`
--

INSERT INTO `conge` (`id`, `user_id`, `datedebut`, `datefin`, `state`, `typeconge`) VALUES
(3, 4, '2018-01-04', '2018-01-05', 'valide', 'maternité'),
(4, 4, '2019-01-01', '2019-01-02', 'no check', 'sans solde'),
(5, 6, '2018-01-03', '2017-01-01', 'invalide', 'sans solde'),
(7, 6, '2017-01-03', '2017-01-06', 'no check', 'annuel'),
(9, 4, '2017-01-01', '2017-01-01', 'no check', 'sans solde');

-- --------------------------------------------------------

--
-- Structure de la table `contrat`
--

DROP TABLE IF EXISTS `contrat`;
CREATE TABLE IF NOT EXISTS `contrat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `datedebut` date NOT NULL,
  `datefin` date NOT NULL,
  `datearret` date DEFAULT NULL,
  `typedecontrat` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quotaparmoisaccorde` double NOT NULL,
  `quotarestant` int(11) DEFAULT NULL,
  `statut` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_60349993A76ED395` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `contrat`
--

INSERT INTO `contrat` (`id`, `user_id`, `datedebut`, `datefin`, `datearret`, `typedecontrat`, `quotaparmoisaccorde`, `quotarestant`, `statut`) VALUES
(2, 4, '2017-01-04', '2027-01-01', NULL, 'CDI', 2.5, 165, 'en cours'),
(5, 6, '2017-01-01', '2021-01-01', NULL, 'CDI', 2.5, 165, 'en cours');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20220624121731', '2022-06-24 12:17:38', 7280),
('DoctrineMigrations\\Version20220624134532', '2022-06-24 13:45:53', 1969),
('DoctrineMigrations\\Version20220624140858', '2022-06-24 14:09:20', 825),
('DoctrineMigrations\\Version20220627091352', '2022-06-27 09:14:08', 280),
('DoctrineMigrations\\Version20220627134719', '2022-06-27 15:13:31', 1224);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `suivi_conge`
--

DROP TABLE IF EXISTS `suivi_conge`;
CREATE TABLE IF NOT EXISTS `suivi_conge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contrat_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  `mois` int(11) NOT NULL,
  `quota` double NOT NULL,
  `nbjourpris` int(11) DEFAULT NULL,
  `nbjourrestant` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8DD4B36B1823061F` (`contrat_id`),
  KEY `IDX_8DD4B36BA76ED395` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `suivi_conge`
--

INSERT INTO `suivi_conge` (`id`, `contrat_id`, `user_id`, `annee`, `mois`, `quota`, `nbjourpris`, `nbjourrestant`) VALUES
(26, 2, 4, 2017, 1, 2.5, 0, 2),
(27, 2, 4, 2017, 2, 2.5, 0, 4),
(28, 2, 4, 2017, 3, 2.5, 0, 6),
(29, 2, 4, 2017, 4, 2.5, 0, 8),
(30, 2, 4, 2017, 5, 2.5, 0, 10),
(31, 2, 4, 2017, 6, 2.5, 0, 12),
(32, 2, 4, 2017, 7, 2.5, 0, 14),
(33, 2, 4, 2017, 8, 2.5, 0, 16),
(34, 2, 4, 2017, 9, 2.5, 0, 18),
(35, 2, 4, 2017, 10, 2.5, 0, 20),
(36, 2, 4, 2017, 11, 2.5, 0, 22),
(37, 2, 4, 2017, 12, 2.5, 0, 24),
(38, 2, 4, 2018, 1, 2.5, 1, 25),
(39, 2, 4, 2018, 2, 2.5, 0, 27),
(40, 2, 4, 2018, 3, 2.5, 0, 29),
(41, 2, 4, 2018, 4, 2.5, 0, 31),
(42, 2, 4, 2018, 5, 2.5, 0, 33),
(43, 2, 4, 2018, 6, 2.5, 0, 35),
(44, 2, 4, 2018, 7, 2.5, 0, 37),
(45, 2, 4, 2018, 8, 2.5, 0, 39),
(46, 2, 4, 2018, 9, 2.5, 0, 41),
(47, 2, 4, 2018, 10, 2.5, 0, 43),
(48, 2, 4, 2018, 11, 2.5, 0, 45),
(49, 2, 4, 2018, 12, 2.5, 0, 47),
(50, 2, 4, 2019, 12, 2.5, 0, 47),
(51, 2, 4, 2017, 1, 2.5, 0, 2),
(52, 2, 4, 2017, 1, 2.5, 0, 2),
(53, 2, 4, 2017, 1, 2.5, 0, 2),
(54, 2, 4, 2017, 1, 2.5, 0, 2),
(55, 2, 4, 2000, 3, 2.5, 0, 2),
(56, 2, 4, 2020, 12, 2.5, 0, 2),
(57, 2, 4, 2021, 12, 2.5, 0, 2),
(58, 2, 4, 2022, 12, 2.5, 0, 2),
(59, 5, 6, 2017, 1, 2.5, 0, 2),
(60, 5, 6, 2017, 2, 2.5, 0, 4),
(61, 5, 6, 2017, 3, 2.5, 0, 6),
(62, 5, 6, 2017, 4, 2.5, 0, 8),
(63, 5, 6, 2017, 5, 2.5, 0, 10),
(64, 5, 6, 2017, 6, 2.5, 0, 12),
(65, 5, 6, 2017, 7, 2.5, 0, 14),
(66, 5, 6, 2017, 8, 2.5, 0, 16),
(67, 5, 6, 2017, 9, 2.5, 0, 18),
(68, 5, 6, 2017, 10, 2.5, 0, 20),
(69, 5, 6, 2017, 11, 2.5, 0, 22),
(70, 5, 6, 2017, 12, 2.5, 0, 24),
(71, 5, 6, 2018, 1, 2.5, 0, 26),
(72, 5, 6, 2018, 2, 2.5, 0, 28),
(73, 5, 6, 2018, 3, 2.5, 0, 30),
(74, 5, 6, 2018, 4, 2.5, 0, 32),
(75, 5, 6, 2018, 5, 2.5, 0, 34),
(76, 5, 6, 2018, 6, 2.5, 0, 36),
(77, 5, 6, 2018, 7, 2.5, 0, 38),
(78, 5, 6, 2018, 8, 2.5, 0, 40),
(79, 5, 6, 2018, 9, 2.5, 0, 42),
(80, 5, 6, 2018, 10, 2.5, 0, 44),
(81, 5, 6, 2018, 11, 2.5, 0, 46),
(82, 5, 6, 2018, 12, 2.5, 0, 48),
(83, 5, 6, 2019, 1, 2.5, 0, 50),
(84, 5, 6, 2019, 2, 2.5, 0, 52),
(85, 5, 6, 2019, 3, 2.5, 0, 54),
(86, 5, 6, 2019, 4, 2.5, 0, 56),
(87, 5, 6, 2019, 5, 2.5, 0, 58),
(88, 5, 6, 2019, 6, 2.5, 0, 60),
(89, 5, 6, 2019, 7, 2.5, 0, 62),
(90, 5, 6, 2019, 8, 2.5, 0, 64),
(91, 5, 6, 2019, 9, 2.5, 0, 66),
(92, 5, 6, 2019, 10, 2.5, 0, 68),
(93, 5, 6, 2019, 11, 2.5, 0, 70),
(94, 5, 6, 2019, 12, 2.5, 0, 72),
(95, 5, 6, 2020, 1, 2.5, 0, 74),
(96, 5, 6, 2020, 2, 2.5, 0, 76),
(97, 5, 6, 2020, 3, 2.5, 0, 78),
(98, 5, 6, 2020, 4, 2.5, 0, 80),
(99, 5, 6, 2020, 5, 2.5, 0, 82),
(100, 5, 6, 2020, 6, 2.5, 0, 84),
(101, 5, 6, 2020, 7, 2.5, 0, 86),
(102, 5, 6, 2020, 8, 2.5, 0, 88),
(103, 5, 6, 2020, 9, 2.5, 0, 90),
(104, 5, 6, 2020, 10, 2.5, 0, 92),
(105, 5, 6, 2020, 11, 2.5, 0, 94),
(106, 5, 6, 2020, 12, 2.5, 0, 96);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomutilisateur` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quota` int(11) DEFAULT NULL,
  `salaire` double DEFAULT NULL,
  `nbjourpris` int(11) DEFAULT NULL,
  `administrateur_id` int(11) DEFAULT NULL,
  `is_leaving` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D6497F1813BC` (`nomutilisateur`),
  KEY `IDX_8D93D6497EE5403C` (`administrateur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `nomutilisateur`, `roles`, `password`, `nom`, `prenom`, `quota`, `salaire`, `nbjourpris`, `administrateur_id`, `is_leaving`) VALUES
(4, 'aziz', '[\"ROLE_USER\"]', '$2y$13$EIlPiErI/h79iIgFPi4HI.HDGzinNP23NCIO3F7uKhLtD6e7QwWnC', 'aziz', 'abes', 165, 300, 0, 6, 0),
(5, 'abes', '[\"ROLE_ADMIN\"]', '$2y$13$.ayo4ak78XMeRXrjRm5tv.SbXK8.ztDrTAIj09RzFMbz3RPbUqRg.', 'abes', 'kouki', 165, 200, NULL, NULL, 0),
(6, 'aziz234', '[\"ROLE_ADMIN\"]', '$2y$13$NO99hmhrfhKtStc8Y9xpmu/IembpfLkzF7.P/JeqD/1uLhFvL74HG', 'aziz234', 'kouki', 165, 200, 3, NULL, 0);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `conge`
--
ALTER TABLE `conge`
  ADD CONSTRAINT `FK_2ED89348A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `contrat`
--
ALTER TABLE `contrat`
  ADD CONSTRAINT `FK_60349993A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `suivi_conge`
--
ALTER TABLE `suivi_conge`
  ADD CONSTRAINT `FK_8DD4B36B1823061F` FOREIGN KEY (`contrat_id`) REFERENCES `contrat` (`id`),
  ADD CONSTRAINT `FK_8DD4B36BA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_8D93D6497EE5403C` FOREIGN KEY (`administrateur_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
