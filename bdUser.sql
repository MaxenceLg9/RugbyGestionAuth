-- Active: 1741785382839@@mysql-gestionequiperugby.alwaysdata.net@3306
-- Base de données :  `gestionequiperugby_bd`

-- Structure de la table `user`
DROP TABLE IF EXISTS `User`;

CREATE TABLE `User` (
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- insérer des données dans la table `user`
INSERT INTO `User` (`email`, `password`) VALUES
('maxenceL9@gmail.com', '$2y$10$PsJZvmmnvbGwIYDBeEGtC.Z1Bkulg1W1eyhOtPsjPSL4DJ72D97xy'),
('virgilskoh@gmail.com', '$2y$10$cefdlQN3gWXEo7AseLD4q.GoF3LH73CaD.b5nhmeIGf8aYNMJ8Mje');
