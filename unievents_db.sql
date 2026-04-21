-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 20 avr. 2026 à 22:31
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `unievents_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `subject`, `message`, `is_read`, `sent_at`) VALUES
(1, 'John Smith', 'john.smith@example.com', 'Event Registration Issue', 'I had a problem registering for the Web Development workshop. Can you help?', 0, '2026-04-20 18:47:32'),
(2, 'Sarah Johnson', 'sarah.johnson@example.com', 'Question about AI Seminar', 'Will the AI seminar cover TensorFlow and PyTorch?', 1, '2026-04-20 18:47:32'),
(3, 'Mike Chen', 'mike.chen@example.com', 'Suggestion for New Event', 'I would love to see a cybersecurity workshop in the future.', 1, '2026-04-20 18:47:32'),
(4, 'Lisa Davis', 'lisa.davis@example.com', 'Thank you for the event', 'Great event last week! Looking forward to the next one.', 1, '2026-04-20 18:47:32');

-- --------------------------------------------------------

--
-- Structure de la table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `category` enum('Conférence','Workshop','Hackathon','Club','Séminaire') NOT NULL,
  `description` text NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `location` varchar(255) NOT NULL,
  `capacity` int(11) NOT NULL,
  `current_registrations` int(11) DEFAULT 0,
  `speaker` varchar(150) DEFAULT NULL,
  `status` enum('à_venir','en_cours','terminé','annulé') DEFAULT 'à_venir',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `events`
--

INSERT INTO `events` (`id`, `title`, `category`, `description`, `date`, `time`, `location`, `capacity`, `current_registrations`, `speaker`, `status`, `created_by`, `created_at`) VALUES
(1, 'IA et Éthique dans l\'Éducation', 'Conférence', 'Débat interactif sur l\'impact de l\'IA dans l\'enseignement supérieur', '2026-03-15', '14:00:00', 'Amphi 4 - Campus Principal', 50, 8, 'Pr. Mohamed Ben Ali', 'à_venir', NULL, '2026-04-19 14:49:20'),
(2, 'Développement Web - React.js', 'Workshop', 'Initiation pratique à React.js - Créez votre première application web moderne', '2026-03-20', '09:00:00', 'Labo Info 3 - Faculté Sciences', 30, 18, 'Club Dev Community', 'à_venir', NULL, '2026-04-19 14:49:20'),
(3, 'Smart Campus Hackathon', 'Hackathon', '48h pour innover et créer des solutions pour le campus de demain', '2026-04-05', '09:00:00', 'Bibliothèque Centrale', 20, 12, 'IEEE Student Branch', 'à_venir', NULL, '2026-04-19 14:49:20'),
(4, 'Web Development Workshop', 'Conférence', 'Learn modern web development with React and Node.js', '2026-04-25', '14:00:00', 'Room 101', 50, 25, NULL, 'à_venir', NULL, '2026-04-20 18:44:34'),
(5, 'AI & Machine Learning Seminar', 'Conférence', 'Explore the future of artificial intelligence', '2026-04-30', '10:00:00', 'Amphitheatre', 100, 45, NULL, 'à_venir', NULL, '2026-04-20 18:44:34');

-- --------------------------------------------------------

--
-- Structure de la table `organizer_requests`
--

CREATE TABLE `organizer_requests` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `club` varchar(150) NOT NULL,
  `role` varchar(100) DEFAULT NULL,
  `status` enum('en_attente','approuvé','refusé') DEFAULT 'en_attente',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `organizer_requests`
--

INSERT INTO `organizer_requests` (`id`, `name`, `email`, `club`, `role`, `status`, `requested_at`) VALUES
(1, 'Anis Miled', 'anis.miled@example.com', 'Club Robotique', 'President', 'en_attente', '2026-04-20 18:37:31'),
(2, 'Leila Hamdi', 'leila.hamdi@example.com', 'Club Art et Culture', 'Vice President', 'en_attente', '2026-04-20 18:37:31'),
(3, 'Karim Douaa', 'karim.douaa@example.com', 'Club Photography', 'Treasurer', 'approuvé', '2026-04-20 18:37:31'),
(4, 'Nadia Gharbi', 'nadia.gharbi@example.com', 'Club Music', 'Member', 'refusé', '2026-04-20 18:37:31');

-- --------------------------------------------------------

--
-- Structure de la table `registrations`
--

CREATE TABLE `registrations` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('inscrit','waitlist','annulé') DEFAULT 'inscrit',
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `registrations`
--

INSERT INTO `registrations` (`id`, `event_id`, `user_id`, `status`, `registered_at`) VALUES
(1, 1, 2, 'inscrit', '2026-04-20 18:44:34'),
(2, 1, 3, 'inscrit', '2026-04-20 18:44:34'),
(3, 1, 4, 'inscrit', '2026-04-20 18:44:34'),
(4, 1, 5, 'inscrit', '2026-04-20 18:44:34'),
(5, 1, 6, 'inscrit', '2026-04-20 18:44:34');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('etudiant','organisateur','admin') DEFAULT 'etudiant',
  `niveau` varchar(50) DEFAULT NULL,
  `specialite` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role`, `niveau`, `specialite`, `created_at`) VALUES
(1, 'Admin UniEvents', 'admin@unievents.tn', '$2y$12$wsSgHJ88mebd6KmrBzvZkuc9n5lMgl9zapJabUOZOtTuufAKxOAC.', NULL, 'admin', NULL, NULL, '2026-04-19 14:49:31'),
(2, 'Ahmed Ben Ali', 'ahmed.benali@example.com', '$2y$10$a7RfxO.BXTAG0ZYaWb.t7ePBVO8Sk8NFYWwCNsrnNlTZYfh1KUMC2', NULL, 'organisateur', NULL, NULL, '2026-04-20 18:37:49'),
(3, 'Fatima Tounsi', 'fatima.tounsi@example.com', '$2y$10$hU7tfujFXAxiKW6X8xEYWOJrSvvWApu6hHviyWH9QjU1frsM6/S1y', NULL, 'organisateur', NULL, NULL, '2026-04-20 18:37:49'),
(4, 'Mohamed Salah', 'mohamed.salah@example.com', '$2y$10$cK1Nz9u.A52li9Rc3.PFwutZaXHNd7yLv/MVkgzpneJ07Ml2kND7W', NULL, 'organisateur', NULL, NULL, '2026-04-20 18:37:49'),
(5, 'Mariam Karim', 'mariam.karim@uni.tn', '$2y$10$e8wOMj9cZw2Vw8t9kjqpj.b1OQ4s24ejMa94e9KIMz61.LvPHAmTa', NULL, 'organisateur', NULL, NULL, '2026-04-20 18:41:15'),
(6, 'Romain Dupont', 'romain.dupont@uni.tn', '$2y$10$wYbiqvuL3.PH.JYcWrtfq.mfdTNtQR5tpmVgHuTV8VYFgilgSIs/6', NULL, 'etudiant', NULL, NULL, '2026-04-20 18:41:15'),
(7, 'Sophia Martinez', 'sophia.martinez@uni.tn', '$2y$10$HGfiKuJC9TMwjUy3RcMfjukxKlj4AphCQXDzlVE76Rcy3Q/2CgrxW', NULL, 'organisateur', NULL, NULL, '2026-04-20 18:41:16'),
(8, 'Zain Al-Rashid', 'zain.rashid@uni.tn', '$2y$10$HpBKKJwCtfDd078JW0IVFe98SjMEcEw6pALop4ThRzDnGkT5EqjPK', NULL, 'organisateur', NULL, NULL, '2026-04-20 18:41:16'),
(9, 'Emma Wilson', 'emma.wilson@uni.tn', '$2y$10$rXP.abZLFZadSNYssfIW..uI55aRIk5i.86eELBh6xsfpYRZhsZ7O', NULL, 'organisateur', NULL, NULL, '2026-04-20 18:41:16');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Index pour la table `organizer_requests`
--
ALTER TABLE `organizer_requests`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_registration` (`event_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `organizer_requests`
--
ALTER TABLE `organizer_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `registrations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
