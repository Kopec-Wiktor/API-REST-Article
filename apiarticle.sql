-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 31 mars 2023 à 22:21
-- Version du serveur : 10.4.27-MariaDB
-- Version de PHP : 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `apiarticle`
--

-- --------------------------------------------------------

--
-- Structure de la table `article`
--

CREATE TABLE `article` (
  `id_article` int(11) NOT NULL,
  `date_publication` datetime DEFAULT NULL,
  `contenu` varchar(300) DEFAULT NULL,
  `pseudo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `article`
--

INSERT INTO `article` (`id_article`, `date_publication`, `contenu`, `pseudo`) VALUES
(2, '2023-03-16 00:28:31', 'Dzien dobry swiat', 'wikpak'),
(3, '2023-03-16 00:28:52', 'Bonjour le monde!', 'amk'),
(4, '2023-03-16 00:29:10', 'buenos dias el mundo', 'wikpak'),
(5, '2023-03-16 00:28:31', '1234', 'amk'),
(6, '2023-03-16 00:28:31', '1234', 'wikpak'),
(7, '2023-03-16 00:28:31', '12345678', 'amk'),
(8, '2023-03-16 00:28:31', '12345688888888', 'amk');

-- --------------------------------------------------------

--
-- Structure de la table `liker`
--

CREATE TABLE `liker` (
  `pseudo` varchar(50) NOT NULL,
  `id_article` int(11) NOT NULL,
  `liker` tinyint(1) DEFAULT NULL,
  `disliker` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `liker`
--

INSERT INTO `liker` (`pseudo`, `id_article`, `liker`, `disliker`) VALUES
('amk', 5, NULL, 1),
('amk', 6, 1, NULL),
('amk', 7, NULL, 1),
('amk', 8, 1, NULL),
('wikpak', 6, 1, NULL),
('wikpak', 7, 1, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `pseudo` varchar(50) NOT NULL,
  `mdp` varchar(255) DEFAULT NULL,
  `role1` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`pseudo`, `mdp`, `role1`) VALUES
('amk', '$2y$10$.oJYBhMbn7eU/qu0yLMYDePdTKzR5NtJK9sEIOUAFNWRSyCcxTlFi', 'publisher'),
('wikpak', '$2y$10$2ZPAb71LgKSydYIkoWFK5.JsXwkeS55Hx4F0J1ZkQZh0sgxEYZixq', 'moderator');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`id_article`),
  ADD KEY `pseudo` (`pseudo`);

--
-- Index pour la table `liker`
--
ALTER TABLE `liker`
  ADD PRIMARY KEY (`pseudo`,`id_article`),
  ADD KEY `id_article` (`id_article`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`pseudo`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `article`
--
ALTER TABLE `article`
  MODIFY `id_article` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `article`
--
ALTER TABLE `article`
  ADD CONSTRAINT `article_ibfk_1` FOREIGN KEY (`pseudo`) REFERENCES `utilisateur` (`pseudo`);

--
-- Contraintes pour la table `liker`
--
ALTER TABLE `liker`
  ADD CONSTRAINT `liker_ibfk_1` FOREIGN KEY (`pseudo`) REFERENCES `utilisateur` (`pseudo`),
  ADD CONSTRAINT `liker_ibfk_2` FOREIGN KEY (`id_article`) REFERENCES `article` (`id_article`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
