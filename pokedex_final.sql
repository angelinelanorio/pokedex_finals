-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 14, 2026 at 01:32 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pokedex_final`
--

-- --------------------------------------------------------

--
-- Table structure for table `ai_trainers`
--

CREATE TABLE `ai_trainers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `region` varchar(255) NOT NULL DEFAULT 'Kanto',
  `level` int(11) NOT NULL DEFAULT 5,
  `description` text DEFAULT NULL,
  `avatar_color` varchar(255) NOT NULL DEFAULT '#3498db',
  `pokemon_count` int(11) NOT NULL DEFAULT 0,
  `trades_count` int(11) NOT NULL DEFAULT 0,
  `trade_preferences` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`trade_preferences`)),
  `personality` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`personality`)),
  `team_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`team_data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ai_trainers`
--

INSERT INTO `ai_trainers` (`id`, `name`, `region`, `level`, `description`, `avatar_color`, `pokemon_count`, `trades_count`, `trade_preferences`, `personality`, `team_data`, `created_at`, `updated_at`) VALUES
(101, 'Brock', 'Kanto', 15, 'Rock-type Gym Leader. Has strong Pokémon!', '#e74c3c', 8, 5, NULL, NULL, '[{\"pokemon_id\":15,\"level\":35},{\"pokemon_id\":18,\"level\":33},{\"pokemon_id\":19,\"level\":32}]', '2026-01-12 06:40:35', '2026-01-12 06:40:35'),
(102, 'Misty', 'Kanto', 14, 'Water-type Gym Leader. Good for mid-level trades!', '#3498db', 7, 3, NULL, NULL, '[{\"pokemon_id\":20,\"level\":28},{\"pokemon_id\":15,\"level\":30},{\"pokemon_id\":21,\"level\":25}]', '2026-01-12 06:40:35', '2026-01-12 06:40:35'),
(103, 'Ash', 'Kanto', 10, 'Aspiring Pokémon Master. Perfect for beginners!', '#f1c40f', 6, 2, NULL, NULL, '[{\"pokemon_id\":15,\"level\":18},{\"pokemon_id\":18,\"level\":16},{\"pokemon_id\":19,\"level\":14}]', '2026-01-12 06:40:35', '2026-01-12 06:40:35'),
(104, 'Gary', 'Kanto', 8, 'Beginner trainer with low-level Pokémon. Easy trades!', '#9b59b6', 4, 1, NULL, NULL, '[{\"pokemon_id\":15,\"level\":12},{\"pokemon_id\":18,\"level\":10},{\"pokemon_id\":19,\"level\":8}]', '2026-01-12 06:40:35', '2026-01-12 06:40:35'),
(105, 'Professor Oak', 'Kanto', 20, 'Pokémon Professor. Has balanced Pokémon for fair trades!', '#2ecc71', 10, 8, NULL, NULL, '[{\"pokemon_id\":20,\"level\":22},{\"pokemon_id\":21,\"level\":20},{\"pokemon_id\":15,\"level\":25}]', '2026-01-12 06:40:35', '2026-01-12 06:40:35'),
(106, 'Beginner Bot', 'Kanto', 5, 'Perfect for new trainers! Has Level 1-5 Pokémon only.', '#1abc9c', 3, 0, NULL, NULL, '[    {\"pokemon_id\": 15, \"level\": 5},    {\"pokemon_id\": 18, \"level\": 6},    {\"pokemon_id\": 19, \"level\": 1}]', '2026-01-12 06:40:35', '2026-01-12 06:40:35');

-- --------------------------------------------------------

--
-- Table structure for table `catch_history`
--

CREATE TABLE `catch_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `trainer_id` bigint(20) UNSIGNED NOT NULL,
  `pokemon_id` bigint(20) UNSIGNED NOT NULL,
  `success` tinyint(1) NOT NULL DEFAULT 1,
  `location` varchar(255) DEFAULT NULL,
  `method` varchar(255) NOT NULL DEFAULT 'pokeball',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `catch_history`
--

INSERT INTO `catch_history` (`id`, `trainer_id`, `pokemon_id`, `success`, `location`, `method`, `created_at`, `updated_at`) VALUES
(9, 1, 15, 1, 'Wild Area', 'pokeball', '2026-01-10 01:49:07', '2026-01-10 01:49:07'),
(10, 1, 21, 1, 'Wild Area', 'pokeball', '2026-01-12 06:58:27', '2026-01-12 06:58:27'),
(11, 1, 18, 1, 'Wild Area', 'pokeball', '2026-01-12 07:17:51', '2026-01-12 07:17:51'),
(12, 1, 19, 0, 'Wild Area', 'pokeball', '2026-01-13 05:10:25', '2026-01-13 05:10:25'),
(13, 1, 19, 1, 'Wild Area', 'pokeball', '2026-01-13 05:10:33', '2026-01-13 05:10:33'),
(14, 1, 20, 1, 'Wild Area', 'pokeball', '2026-01-13 08:05:00', '2026-01-13 08:05:00');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_01_09_095110_create_pokemons_table', 1),
(2, '2026_01_09_095209_create_trainers_table', 1),
(3, '2026_01_09_095221_create_teams_table', 1),
(4, '2026_01_09_095231_create_trades_table', 1),
(5, '2026_01_09_095241_create_catch_history_table', 1),
(6, '2026_01_09_104727_create_sessions_table', 2),
(7, '2026_01_12_074123_create_ai_trainers_table', 3),
(8, '2026_01_12_151127_update_trades_table_for_ai_trainers', 4),
(9, '2026_01_13_150919_create_training_histories_table', 5),
(10, '2026_01_13_163614_add_rarity_to_pokemons', 6),
(11, '2026_01_13_171712_add_daily_login_system_to_trainers', 7);

-- --------------------------------------------------------

--
-- Table structure for table `pokemons`
--

CREATE TABLE `pokemons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pokedex_number` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type1` varchar(20) NOT NULL,
  `type2` varchar(20) DEFAULT NULL,
  `rarity` varchar(255) NOT NULL DEFAULT 'common',
  `base_exp` int(11) NOT NULL DEFAULT 50,
  `height` decimal(5,2) NOT NULL DEFAULT 0.70,
  `weight` decimal(5,2) NOT NULL DEFAULT 6.90,
  `description` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `abilities` text DEFAULT NULL,
  `moves` text DEFAULT NULL,
  `hp` int(11) NOT NULL DEFAULT 50,
  `attack` int(11) NOT NULL DEFAULT 50,
  `defense` int(11) NOT NULL DEFAULT 50,
  `special_attack` int(11) NOT NULL DEFAULT 65,
  `special_defense` int(11) NOT NULL DEFAULT 65,
  `speed` int(11) NOT NULL DEFAULT 45,
  `evolution_stage` int(11) NOT NULL DEFAULT 1,
  `evolves_from` int(11) DEFAULT NULL,
  `evolves_to` int(11) DEFAULT NULL,
  `evolution_condition` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pokemons`
--

INSERT INTO `pokemons` (`id`, `pokedex_number`, `name`, `type1`, `type2`, `rarity`, `base_exp`, `height`, `weight`, `description`, `image_url`, `abilities`, `moves`, `hp`, `attack`, `defense`, `special_attack`, `special_defense`, `speed`, `evolution_stage`, `evolves_from`, `evolves_to`, `evolution_condition`, `created_at`, `updated_at`) VALUES
(15, 154, 'Pikachu', 'electric', 'electric', 'common', 50, 0.70, 6.90, 'Pure', 'https://www.pngmart.com/files/22/Pokemon-PNG-Isolated-File.png', '\"Custom Ability\"', '\"Tackle, Growl\"', 100, 60, 60, 65, 65, 45, 1, NULL, NULL, NULL, '2026-01-10 00:48:40', '2026-01-10 00:48:40'),
(18, 155, 'Bulbasaur', 'grass', 'poison', 'common', 50, 0.70, 6.90, 'A Seed Pokémon that uses the plant bulb on its back to absorb sunlight.', 'https://www.pngmart.com/files/11/Bulbasaur-Transparent-PNG.png', '\"Custom Ability\"', '\"Tackle, Growl\"', 45, 45, 49, 65, 65, 45, 1, NULL, NULL, NULL, '2026-01-12 05:40:38', '2026-01-12 05:40:38'),
(19, 156, 'Charmander', 'fire', NULL, 'common', 50, 0.60, 8.50, 'A Lizard Pokémon with a flame on its tail that shows its life force.', 'https://i.pinimg.com/736x/44/09/96/4409965bb4f6b13fa837bfc414abb647.jpg', '\"Custom Ability\"', '\"Tackle, Growl\"', 39, 52, 43, 60, 50, 55, 1, NULL, NULL, NULL, '2026-01-12 05:43:02', '2026-01-12 05:43:02'),
(20, 157, 'Squirtle', 'water', NULL, 'common', 50, 0.50, 0.90, 'A Tiny Turtle Pokémon that hides in its shell for protection.', 'https://www.pngarts.com/files/3/Squirtle-PNG-Image-Background.png', '\"Custom Ability\"', '\"Tackle, Growl\"', 50, 50, 50, 65, 65, 45, 1, NULL, NULL, NULL, '2026-01-12 05:45:39', '2026-01-12 05:45:39'),
(21, 158, 'Gengar', 'ghost', 'poison', 'common', 50, 1.50, 40.50, 'A Shadow Pokémon that hides in the darkness and scares its prey.', 'https://w7.pngwing.com/pngs/286/800/png-transparent-gengar-funny-monster.png', '\"Custom Ability\"', '\"Tackle, Growl\"', 60, 65, 60, 130, 75, 110, 1, NULL, NULL, NULL, '2026-01-12 05:48:31', '2026-01-12 05:48:31');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('VR38Edz98CtS5UUYpdc07dKCe59Itqf9LgNHYUU1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'YToxMjp7czo2OiJfdG9rZW4iO3M6NDA6ImdPc1duejluY3ZwUUY3OFBSU1dtZE5weXBNMUhXTmZyTWRvQ25oMUsiO3M6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjI2OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvdGVhbSI7czo1OiJyb3V0ZSI7czoxMDoidGVhbS5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MTA6InRyYWluZXJfaWQiO2k6MTtzOjE2OiJ0cmFpbmVyX3VzZXJuYW1lIjtzOjg6ImFuZ2VsaW5lIjtzOjEyOiJ0cmFpbmVyX25hbWUiO3M6ODoiQW5nZWxpbmUiO3M6MTM6InRyYWluZXJfZW1haWwiO3M6MjU6ImFuZ2VsaW5lbGFub3Jpb0BnbWFpbC5jb20iO3M6MTQ6InRyYWluZXJfcmVnaW9uIjtzOjU6IkthbnRvIjtzOjEzOiJ0cmFpbmVyX2xldmVsIjtpOjM7czoxNDoidHJhaW5lcl9zdHJlYWsiO2k6MjtzOjE0OiJ0cmFpbmVyX2F2YXRhciI7czo3ODoiaHR0cHM6Ly93d3cucG5nYWxsLmNvbS93cC1jb250ZW50L3VwbG9hZHMvMTIvQXZhdGFyLVByb2ZpbGUtVmVjdG9yLVBORy1QaWMucG5nIjtzOjk6ImxvZ2dlZF9pbiI7YjoxO30=', 1768350370);

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `trainer_id` bigint(20) UNSIGNED NOT NULL,
  `pokemon_id` bigint(20) UNSIGNED NOT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `level` int(11) NOT NULL DEFAULT 5,
  `experience` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `date_caught` date NOT NULL DEFAULT '2026-01-09',
  `caught_location` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`id`, `trainer_id`, `pokemon_id`, `nickname`, `level`, `experience`, `is_active`, `date_caught`, `caught_location`, `created_at`, `updated_at`) VALUES
(4, 1, 15, 'Pikachu', 5, 0, 1, '2026-01-13', 'Trade with AI Trainer', '2026-01-10 01:49:07', '2026-01-13 10:27:23'),
(5, 1, 21, NULL, 5, 0, 0, '2026-01-12', 'Wild Area', '2026-01-12 06:58:27', '2026-01-12 07:12:50'),
(7, 1, 18, 'Bulbasaur', 9, 690, 1, '2026-01-12', 'Trade with AI Trainer', '2026-01-12 07:17:51', '2026-01-12 18:55:32'),
(9, 1, 19, NULL, 6, 50, 1, '2026-01-13', 'Wild Area', '2026-01-13 05:10:33', '2026-01-13 07:39:49'),
(10, 1, 20, NULL, 5, 0, 0, '2026-01-13', 'Traded to AI Trainer', '2026-01-13 08:05:00', '2026-01-13 08:19:13');

-- --------------------------------------------------------

--
-- Table structure for table `trades`
--

CREATE TABLE `trades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `trainer1_id` bigint(20) UNSIGNED NOT NULL,
  `trainer2_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ai_trainer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `pokemon1_id` bigint(20) UNSIGNED NOT NULL,
  `pokemon2_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('pending','completed','rejected') NOT NULL DEFAULT 'completed',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `trades`
--

INSERT INTO `trades` (`id`, `trainer1_id`, `trainer2_id`, `ai_trainer_id`, `pokemon1_id`, `pokemon2_id`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(2, 1, 106, NULL, 21, 15, 'completed', 'Direct trade with AI Trainer', '2026-01-12 07:12:50', '2026-01-12 07:12:50'),
(3, 1, 106, NULL, 18, 15, 'completed', 'Direct trade with AI Trainer', '2026-01-12 08:09:54', '2026-01-12 08:09:54'),
(4, 1, 106, NULL, 15, 15, 'completed', 'Direct trade with AI Trainer', '2026-01-12 08:13:29', '2026-01-12 08:13:29'),
(5, 1, 106, NULL, 15, 18, 'completed', 'Direct trade with AI Trainer', '2026-01-12 08:14:33', '2026-01-12 08:14:33'),
(6, 1, 106, NULL, 18, 18, 'completed', 'Direct trade with AI Trainer', '2026-01-12 08:15:20', '2026-01-12 08:15:20'),
(7, 1, 106, NULL, 18, 18, 'completed', 'Direct trade with AI Trainer', '2026-01-12 08:22:12', '2026-01-12 08:22:12'),
(8, 1, 106, NULL, 20, 15, 'completed', 'Direct trade with AI Trainer', '2026-01-13 08:19:13', '2026-01-13 08:19:13'),
(9, 1, 106, NULL, 15, 15, 'completed', 'Trade with AI Trainer: Beginner Bot', '2026-01-13 10:27:23', '2026-01-13 10:27:23');

-- --------------------------------------------------------

--
-- Table structure for table `trainers`
--

CREATE TABLE `trainers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `region` varchar(255) NOT NULL DEFAULT 'Kanto',
  `level` int(11) NOT NULL DEFAULT 1,
  `avatar_url` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `pokemon_caught` int(11) NOT NULL DEFAULT 0,
  `trades_completed` int(11) NOT NULL DEFAULT 0,
  `badges_earned` int(11) NOT NULL DEFAULT 0,
  `daily_streak` int(11) NOT NULL DEFAULT 0,
  `last_login_date` date DEFAULT NULL,
  `total_logins` int(11) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `trainers`
--

INSERT INTO `trainers` (`id`, `username`, `email`, `password`, `first_name`, `last_name`, `region`, `level`, `avatar_url`, `bio`, `pokemon_caught`, `trades_completed`, `badges_earned`, `daily_streak`, `last_login_date`, `total_logins`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'angeline', 'angelinelanorio@gmail.com', '$2y$12$B1jXjbrZabl/ESkaWZSVuuJplN0fmUvbtLnCSMBwOD5IOZu8daVaa', 'Angeline', 'Lanorio', 'Kanto', 3, 'https://www.pngall.com/wp-content/uploads/12/Avatar-Profile-Vector-PNG-Pic.png', 'Kind', 3, 8, 0, 2, '2026-01-14', 2, NULL, '2026-01-09 21:37:47', '2026-01-13 16:21:11');

-- --------------------------------------------------------

--
-- Table structure for table `training_histories`
--

CREATE TABLE `training_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `trainer_id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `pokemon_id` bigint(20) UNSIGNED NOT NULL,
  `exp_gained` int(11) NOT NULL,
  `training_type` varchar(255) NOT NULL,
  `old_level` int(11) NOT NULL,
  `new_level` int(11) NOT NULL,
  `old_experience` int(11) NOT NULL,
  `new_experience` int(11) NOT NULL,
  `leveled_up` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `training_histories`
--

INSERT INTO `training_histories` (`id`, `trainer_id`, `team_id`, `pokemon_id`, `exp_gained`, `training_type`, `old_level`, `new_level`, `old_experience`, `new_experience`, `leveled_up`, `created_at`, `updated_at`) VALUES
(1, 1, 9, 19, 25, 'Easy Training', 5, 5, 350, 375, 0, '2026-01-13 07:39:02', '2026-01-13 07:39:02'),
(2, 1, 9, 19, 25, 'Easy Training', 5, 5, 375, 400, 0, '2026-01-13 07:39:17', '2026-01-13 07:39:17'),
(3, 1, 9, 19, 100, 'Hard Training', 5, 6, 400, 50, 1, '2026-01-13 07:39:49', '2026-01-13 07:39:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ai_trainers`
--
ALTER TABLE `ai_trainers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `catch_history`
--
ALTER TABLE `catch_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `catch_history_trainer_id_foreign` (`trainer_id`),
  ADD KEY `catch_history_pokemon_id_foreign` (`pokemon_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pokemons`
--
ALTER TABLE `pokemons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pokemons_pokedex_number_unique` (`pokedex_number`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teams_trainer_id_pokemon_id_unique` (`trainer_id`,`pokemon_id`),
  ADD KEY `teams_pokemon_id_foreign` (`pokemon_id`);

--
-- Indexes for table `trades`
--
ALTER TABLE `trades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trades_trainer1_id_foreign` (`trainer1_id`),
  ADD KEY `trades_pokemon1_id_foreign` (`pokemon1_id`),
  ADD KEY `trades_pokemon2_id_foreign` (`pokemon2_id`),
  ADD KEY `trades_ai_trainer_id_foreign` (`ai_trainer_id`),
  ADD KEY `trades_trainer2_id_ai_trainer_id_index` (`trainer2_id`,`ai_trainer_id`);

--
-- Indexes for table `trainers`
--
ALTER TABLE `trainers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `trainers_username_unique` (`username`),
  ADD UNIQUE KEY `trainers_email_unique` (`email`);

--
-- Indexes for table `training_histories`
--
ALTER TABLE `training_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `training_histories_trainer_id_foreign` (`trainer_id`),
  ADD KEY `training_histories_pokemon_id_foreign` (`pokemon_id`),
  ADD KEY `training_histories_team_id_created_at_index` (`team_id`,`created_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ai_trainers`
--
ALTER TABLE `ai_trainers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `catch_history`
--
ALTER TABLE `catch_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pokemons`
--
ALTER TABLE `pokemons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `trades`
--
ALTER TABLE `trades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `trainers`
--
ALTER TABLE `trainers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `training_histories`
--
ALTER TABLE `training_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `catch_history`
--
ALTER TABLE `catch_history`
  ADD CONSTRAINT `catch_history_pokemon_id_foreign` FOREIGN KEY (`pokemon_id`) REFERENCES `pokemons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `catch_history_trainer_id_foreign` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teams`
--
ALTER TABLE `teams`
  ADD CONSTRAINT `teams_pokemon_id_foreign` FOREIGN KEY (`pokemon_id`) REFERENCES `pokemons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teams_trainer_id_foreign` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trades`
--
ALTER TABLE `trades`
  ADD CONSTRAINT `trades_ai_trainer_id_foreign` FOREIGN KEY (`ai_trainer_id`) REFERENCES `ai_trainers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trades_pokemon1_id_foreign` FOREIGN KEY (`pokemon1_id`) REFERENCES `pokemons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trades_pokemon2_id_foreign` FOREIGN KEY (`pokemon2_id`) REFERENCES `pokemons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trades_trainer1_id_foreign` FOREIGN KEY (`trainer1_id`) REFERENCES `trainers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `training_histories`
--
ALTER TABLE `training_histories`
  ADD CONSTRAINT `training_histories_pokemon_id_foreign` FOREIGN KEY (`pokemon_id`) REFERENCES `pokemons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `training_histories_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `training_histories_trainer_id_foreign` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
