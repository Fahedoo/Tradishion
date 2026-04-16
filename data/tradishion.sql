-- phpMyAdmin SQL Dump
-- version 4.6.6deb4+deb9u2
-- https://www.phpmyadmin.net/
--
-- Client :  localhost:3306
-- Généré le :  Ven 03 Avril 2026 à 01:21
-- Version du serveur :  10.1.48-MariaDB-0+deb9u2
-- Version de PHP :  7.0.33-0+deb9u12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `tradishion`
--

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `id_comment` int(10) UNSIGNED NOT NULL,
  `id_post` int(10) UNSIGNED NOT NULL,
  `id_author` int(10) UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `comments`
--

INSERT INTO `comments` (`id_comment`, `id_post`, `id_author`, `content`, `created_at`) VALUES
(4, 10, 13, 'Incroyable cette soie ! Est-ce difficile de travailler cette matière sur une machine familiale ?', '2026-04-02 22:54:24'),
(5, 10, 14, 'C\'est une question de tension de fil, ma chère. Je posterai un tutoriel bientôt !', '2026-04-02 22:55:03'),
(8, 9, 14, 'Cette touche apporte véritablement un plus et je trouve que c\'est bien réussi ! Bravo !', '2026-04-02 22:57:43'),
(9, 10, 13, 'J\'ai hâte de voir ça, c\'est super merciiii', '2026-04-02 23:03:16'),
(10, 6, 13, 'C\'est passionnant de voir tant de tissu de différentes couleurs où on peut concevoir d\'innombrable tenue ! Il y a de quoi sourire :)', '2026-04-02 23:04:08'),
(11, 5, 13, 'J\'avoue que la tenue donne du caractère et un style vraiment élégant. C\'est inspirant merci du partage !', '2026-04-02 23:05:00'),
(12, 11, 13, 'C\'est important de rester passioné, l\'artisanat est un métier très enrichissant', '2026-04-02 23:06:22'),
(13, 12, 13, 'Le détail fait toute la différence :D', '2026-04-02 23:06:46'),
(14, 9, 15, 'Oh wouaw j\'aimerais bien avoir le même. Une culture qui se démarque de manière particulière, c\'est fascinant merci beaucoup', '2026-04-02 23:07:54'),
(15, 8, 15, 'Très stylé ! Des vêtements streetwear à porter pour aller au travail ou voir des amis tout en restant fashion. Cette touche berbère est magnifique <3', '2026-04-02 23:08:47'),
(16, 7, 15, 'On en voit peu de ce genre de robe, c\'est raffiné, les couleurs se marient très bien ensemble. Ca me rappelle une tenue que j\'avais confectionné pour ma soeur, elle avait beaucoup aimé !', '2026-04-02 23:09:44'),
(17, 12, 15, 'J\'aime bien, oui Inès c\'est incroyable comme tout est dans le détail.', '2026-04-02 23:10:44'),
(18, 10, 15, 'L\'atelier m\'inspire beaucoup, à mon avis les idées, la créativité doivent être au summum :D', '2026-04-02 23:11:39'),
(19, 10, 15, 'Je serai bien intéressée aussi de regarder ce tutoriel ! Merci beaucoup Giacomo', '2026-04-02 23:12:31'),
(20, 12, 14, 'Cela me réjouie de partager cela avec vous merci pour vos commentaires', '2026-04-02 23:16:40'),
(21, 11, 14, 'Merci ! Tout à fait', '2026-04-02 23:16:56'),
(22, 10, 14, 'La semaine prochaine je pense sortir le tutoriel. De rien Aminata et Inès', '2026-04-02 23:17:58'),
(23, 7, 14, 'Belle création', '2026-04-02 23:21:51'),
(24, 6, 14, 'Le choix du tissu fait toute la différence', '2026-04-02 23:22:11'),
(25, 1, 14, 'Hâte de voir cela :)', '2026-04-02 23:22:35'),
(26, 2, 14, 'Avec grand plaisir de découvrir votre tradition', '2026-04-02 23:23:25');

-- --------------------------------------------------------

--
-- Structure de la table `connections`
--

CREATE TABLE `connections` (
  `id_follower` int(10) UNSIGNED NOT NULL,
  `id_followed` int(10) UNSIGNED NOT NULL,
  `status` enum('pending','accepted','blocked') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `connections`
--

INSERT INTO `connections` (`id_follower`, `id_followed`, `status`, `created_at`, `updated_at`) VALUES
(1, 0, 'accepted', '2026-04-01 12:51:22', NULL),
(1, 4, 'accepted', '2026-04-01 13:15:28', NULL),
(2, 0, 'accepted', '2026-04-01 12:59:33', NULL),
(3, 4, 'accepted', '2026-04-01 13:04:21', NULL),
(4, 5, 'accepted', '2026-04-01 13:07:48', NULL),
(4, 6, 'accepted', '2026-04-02 12:36:21', '2026-04-02 17:18:46'),
(5, 6, 'accepted', '2026-04-02 17:20:39', '2026-04-02 17:20:55'),
(6, 3, 'pending', '2026-04-02 17:21:04', NULL),
(7, 3, 'pending', '2026-04-01 16:12:58', NULL),
(9, 7, 'accepted', '2026-04-02 11:28:25', '2026-04-02 20:45:49'),
(13, 1, 'pending', '2026-04-02 22:43:58', NULL),
(13, 2, 'pending', '2026-04-02 22:44:00', NULL),
(13, 3, 'pending', '2026-04-02 22:44:01', NULL),
(14, 13, 'pending', '2026-04-02 23:24:30', NULL),
(15, 1, 'pending', '2026-04-02 22:30:38', NULL),
(15, 2, 'pending', '2026-04-02 22:30:38', NULL),
(15, 3, 'pending', '2026-04-02 22:30:36', NULL),
(15, 4, 'accepted', '2026-04-02 22:30:58', '2026-04-03 01:13:43'),
(15, 5, 'pending', '2026-04-02 22:30:59', NULL),
(15, 6, 'accepted', '2026-04-02 22:31:00', '2026-04-02 23:16:01'),
(15, 7, 'pending', '2026-04-02 22:35:17', NULL),
(15, 9, 'pending', '2026-04-02 22:35:18', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `events`
--

CREATE TABLE `events` (
  `id_event` int(10) UNSIGNED NOT NULL,
  `id_organizer` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `visibility` enum('private','shared','public') NOT NULL DEFAULT 'shared',
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `meeting_url` varchar(512) DEFAULT NULL COMMENT 'Link to Jitsi / BBB / Meet / etc.',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `event_media`
--

CREATE TABLE `event_media` (
  `id_event` int(10) UNSIGNED NOT NULL,
  `id_media` int(10) UNSIGNED NOT NULL,
  `role` enum('cover','recording','gallery') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'gallery' COMMENT 'cover = event thumbnail; recording = saved session video',
  `sort_order` tinyint(3) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `event_media`
--

INSERT INTO `event_media` (`id_event`, `id_media`, `role`, `sort_order`) VALUES
(1, 8, 'cover', 0),
(3, 9, 'cover', 0),
(5, 10, 'cover', 0),
(6, 11, 'cover', 0);

-- --------------------------------------------------------

--
-- Structure de la table `event_participants`
--

CREATE TABLE `event_participants` (
  `id_event` int(10) UNSIGNED NOT NULL,
  `id_user` int(10) UNSIGNED NOT NULL,
  `invited_by` int(10) UNSIGNED DEFAULT NULL COMMENT 'NULL if self-added (public event)',
  `status` enum('invited','accepted','declined','tentative') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'invited',
  `responded_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `event_participants`
--

INSERT INTO `event_participants` (`id_event`, `id_user`, `invited_by`, `status`, `responded_at`) VALUES
(4, 5, NULL, 'invited', NULL),
(6, 5, NULL, 'declined', NULL),
(7, 5, NULL, 'accepted', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `hashtags`
--

CREATE TABLE `hashtags` (
  `id_hashtag` int(10) UNSIGNED NOT NULL,
  `id_category` int(10) UNSIGNED DEFAULT NULL,
  `slug` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Internal key: baking, photo_editing …'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `hashtags`
--

INSERT INTO `hashtags` (`id_hashtag`, `id_category`, `slug`) VALUES
(1, 6, 'costume_evolution'),
(2, 3, 'silver_necklaces'),
(3, 8, 'vintage_fashion'),
(4, 7, 'street_photography'),
(5, 5, 'pattern_making'),
(6, 5, 'embroidery'),
(7, 2, 'linen'),
(8, 2, 'silk'),
(9, 1, 'hanbok'),
(10, 1, 'qipao'),
(11, 1, 'culla'),
(12, 1, 'sari'),
(13, 1, 'kilt'),
(14, 1, 'ao_dai');

-- --------------------------------------------------------

--
-- Structure de la table `hashtag_categories`
--

CREATE TABLE `hashtag_categories` (
  `id_category` int(10) UNSIGNED NOT NULL,
  `slug` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Internal key: cooking, language_learning …'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `hashtag_categories`
--

INSERT INTO `hashtag_categories` (`id_category`, `slug`) VALUES
(4, 'headwear'),
(6, 'history_culture'),
(3, 'jewelry_accessories'),
(8, 'other'),
(7, 'photography_fashion'),
(5, 'tailoring_techniques'),
(2, 'textiles_fabrics'),
(1, 'traditional_costumes');

-- --------------------------------------------------------

--
-- Structure de la table `hashtag_category_i18n`
--

CREATE TABLE `hashtag_category_i18n` (
  `id_category` int(10) UNSIGNED NOT NULL,
  `id_lang` int(10) UNSIGNED NOT NULL,
  `label` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `hashtag_category_i18n`
--

INSERT INTO `hashtag_category_i18n` (`id_category`, `id_lang`, `label`) VALUES
(1, 1, 'Costumes traditionnels'),
(1, 2, 'Traditional Costumes'),
(1, 3, 'Trang phục truyền thống'),
(1, 4, 'Veshje tradicionale'),
(2, 1, 'Textiles et Tissus'),
(2, 2, 'Textiles & Fabrics'),
(2, 3, 'Dệt may & Vải vóc'),
(2, 4, 'Tekstilet dhe Pëlhurat'),
(3, 1, 'Bijoux et Accessoires'),
(3, 2, 'Jewelry & Accessories'),
(3, 3, 'Trang sức & Phụ kiện'),
(3, 4, 'Bijuteri dhe Aksesorë'),
(4, 1, 'Couvre-chefs'),
(4, 2, 'Headwear'),
(4, 3, 'Khăn & Mũ đội đầu'),
(4, 4, 'Mbulesat e kokës'),
(5, 1, 'Techniques de couture'),
(5, 2, 'Tailoring Techniques'),
(5, 3, 'Kỹ thuật may mặc'),
(5, 4, 'Teknikat e rrobaqepësisë'),
(6, 1, 'Histoire et Culture'),
(6, 2, 'History & Culture'),
(6, 3, 'Lịch sử & Văn hóa'),
(6, 4, 'Historia dhe Kultura'),
(7, 1, 'Photographie de mode'),
(7, 2, 'Fashion Photography'),
(7, 3, 'Nhiếp ảnh thời trang'),
(7, 4, 'Fotografi e modës'),
(8, 1, 'Autre'),
(8, 2, 'Other'),
(8, 3, 'Khác'),
(8, 4, 'Tjetër');

-- --------------------------------------------------------

--
-- Structure de la table `hashtag_i18n`
--

CREATE TABLE `hashtag_i18n` (
  `id_hashtag` int(10) UNSIGNED NOT NULL,
  `id_lang` int(10) UNSIGNED NOT NULL,
  `label` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `hashtag_i18n`
--

INSERT INTO `hashtag_i18n` (`id_hashtag`, `id_lang`, `label`) VALUES
(1, 1, 'Évolution du costume'),
(1, 2, 'Costume Evolution'),
(1, 3, 'Tiến hóa phục trang'),
(1, 4, 'Evolucioni i veshjeve'),
(2, 1, 'Colliers en argent'),
(2, 2, 'Silver Necklaces'),
(2, 3, 'Vòng cổ bạc'),
(2, 4, 'Varëse argjendi'),
(3, 1, 'Mode vintage'),
(3, 2, 'Vintage Fashion'),
(3, 3, 'Thời trang cổ điển'),
(3, 4, 'Modë antike'),
(4, 1, 'Photo de rue'),
(4, 2, 'Street Photography'),
(4, 3, 'Ảnh đường phố'),
(4, 4, 'Fotografi rruge'),
(5, 1, 'Patronnage'),
(5, 2, 'Pattern Making'),
(5, 3, 'Ra rập'),
(5, 4, 'Prerje modelesh'),
(6, 1, 'Broderie'),
(6, 2, 'Embroidery'),
(6, 3, 'Thêu thùa'),
(6, 4, 'Qëndisje'),
(7, 1, 'Lin'),
(7, 2, 'Linen'),
(7, 3, 'Lanh'),
(7, 4, 'Liri'),
(8, 1, 'Soie'),
(8, 2, 'Silk'),
(8, 3, 'Lụa'),
(8, 4, 'Mëndafsh'),
(9, 1, 'Hanbok'),
(9, 2, 'Hanbok'),
(9, 3, 'Hanbok'),
(9, 4, 'Hanbok'),
(10, 1, 'Qipao'),
(10, 2, 'Qipao'),
(10, 3, 'Sườn xám'),
(10, 4, 'Qipao'),
(11, 1, 'Culla'),
(11, 2, 'Culla'),
(11, 3, 'Culla'),
(11, 4, 'Culla'),
(12, 1, 'Sari'),
(12, 2, 'Sari'),
(12, 3, 'Sari'),
(12, 4, 'Sari'),
(13, 1, 'Kilt'),
(13, 2, 'Kilt'),
(13, 3, 'Váy Kilt'),
(13, 4, 'Kilt'),
(14, 1, 'Ao Dai'),
(14, 2, 'Ao Dai'),
(14, 3, 'Áo Dài'),
(14, 4, 'Ao Dai');

-- --------------------------------------------------------

--
-- Structure de la table `languages`
--

CREATE TABLE `languages` (
  `id_lang` int(10) UNSIGNED NOT NULL,
  `lang_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'BCP-47 code: fr, en, vi, sq …',
  `lang_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Native name: Français, English …'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `languages`
--

INSERT INTO `languages` (`id_lang`, `lang_code`, `lang_name`) VALUES
(1, 'fr', 'Français'),
(2, 'en', 'English'),
(3, 'vi', 'Tiếng Việt'),
(4, 'al', 'Shqip');

-- --------------------------------------------------------

--
-- Structure de la table `media`
--

CREATE TABLE `media` (
  `id_media` int(10) UNSIGNED NOT NULL,
  `id_uploader` int(10) UNSIGNED NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Stored filename (UUID-based, no collision)',
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Original name as uploaded by user',
  `file_path` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Relative path: media/uploads/YYYY/MM/uuid.ext',
  `media_type` enum('image','video') COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'e.g. image/jpeg, video/mp4',
  `file_size` int(10) UNSIGNED NOT NULL COMMENT 'File size in bytes',
  `duration_sec` smallint(5) UNSIGNED DEFAULT NULL COMMENT 'Duration in seconds (video only)',
  `width_px` smallint(5) UNSIGNED DEFAULT NULL COMMENT 'Width in pixels (image/video)',
  `height_px` smallint(5) UNSIGNED DEFAULT NULL COMMENT 'Height in pixels (image/video)',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Optional caption/title',
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Accessibility alt text (images)',
  `is_public` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'TRUE = visible without login (e.g. promo video assets)',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `media`
--

INSERT INTO `media` (`id_media`, `id_uploader`, `file_name`, `original_name`, `file_path`, `media_type`, `mime_type`, `file_size`, `duration_sec`, `width_px`, `height_px`, `title`, `alt_text`, `is_public`, `created_at`) VALUES
(1, 6, '69cd19ae22135.jpeg', 'jeune-chat-pexels-104827-900x598.jpeg', 'assets/images/69cd19ae22135.jpeg', 'image', 'image/jpeg', 48412, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-01 15:12:14'),
(2, 6, '69cd19d12ac8c.jpeg', 'jeune-chat-pexels-104827-900x598.jpeg', 'assets/images/69cd19d12ac8c.jpeg', 'image', 'image/jpeg', 48412, NULL, NULL, NULL, NULL, NULL, 0, '2026-04-01 15:12:49'),
(3, 6, '69cd1d8accf93.jpeg', 'jeune-chat-pexels-104827-900x598.jpeg', 'assets/images/69cd1d8accf93.jpeg', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-01 15:28:42'),
(4, 6, '69cd1f0ceabd9.jpeg', 'jeune-chat-pexels-104827-900x598.jpeg', 'assets/images/69cd1f0ceabd9.jpeg', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 0, '2026-04-01 15:35:08'),
(5, 6, '69cd1f9428ac0.jpg', 'British_Shorthair._Female._18_months_old.jpg', 'assets/images/69cd1f9428ac0.jpg', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-01 15:37:24'),
(6, 6, '69cd25fa24acf.jpg', 'British_Shorthair._Female._18_months_old.jpg', 'assets/images/69cd25fa24acf.jpg', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-01 16:04:42'),
(7, 7, '69cd2744bfb42.jpg', '팬텀크로_fevercell (@PT_CROW) on X.jpg', 'assets/images/69cd2744bfb42.jpg', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-01 16:10:12'),
(8, 6, '69cd3ae264b98.jpeg', 'jeune-chat-pexels-104827-900x598.jpeg', 'assets/images/69cd3ae264b98.jpeg', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-01 17:33:54'),
(9, 6, '69cd40a9b34bb.jpg', 'British_Shorthair._Female._18_months_old.jpg', 'assets/images/69cd40a9b34bb.jpg', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-01 17:58:33'),
(10, 6, '69ce7dea94973.jpg', 'British_Shorthair._Female._18_months_old.jpg', 'assets/images/69ce7dea94973.jpg', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-02 16:32:10'),
(11, 6, '69ce7eac43421.jpeg', 'jeune-chat-pexels-104827-900x598.jpeg', 'assets/images/69ce7eac43421.jpeg', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-02 16:35:24'),
(12, 6, '69ce7f6a7363a.jpeg', 'jeune-chat-pexels-104827-900x598.jpeg', 'assets/images/69ce7f6a7363a.jpeg', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-02 16:38:34'),
(13, 6, '69ce7f7262ce5.jpg', 'British_Shorthair._Female._18_months_old.jpg', 'assets/images/69ce7f7262ce5.jpg', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-02 16:38:42'),
(14, 7, '69ceb91fcd2c3.webp', 'mewgenics.webp', 'assets/images/69ceb91fcd2c3.webp', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-02 20:44:47'),
(15, 13, '69cecf7f7a0ea.jpg', 'ines.jpg', 'assets/images/69cecf7f7a0ea.jpg', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-02 22:20:15'),
(16, 13, '69cecf7f82a69.webp', 'algeria-3d-flag-banner-algerian-260nw-502219570.webp', 'assets/images/69cecf7f82a69.webp', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-02 22:20:15'),
(17, 14, '69ced0083d477.avif', 'homme-affaires-age-moyen-souriant-heureux-debout-ville_839833-16023.avif', 'assets/images/69ced0083d477.avif', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-02 22:22:32'),
(18, 14, '69ced044ed3c6.webp', 'banniere-costume-business.webp', 'assets/images/69ced044ed3c6.webp', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-02 22:23:32'),
(19, 15, '69ced143f0b76.jpg', 'aminata.jpg', 'assets/images/69ced143f0b76.jpg', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-02 22:27:47'),
(20, 15, '69ced144050c3.jpg', 'images.jpg', 'assets/images/69ced144050c3.jpg', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-02 22:27:48'),
(21, 15, '69ced1692c4bd.jpg', 'depositphotos_420549960-stock-illustration-african-wax-print-fabric-ethnic.jpg', 'assets/images/69ced1692c4bd.jpg', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-02 22:28:25'),
(22, 15, '69ced264bb2ed.webp', 'echarpe-en-bogolan-doublee-de-coton-noir-textile-africain-graphique-kaolack-creations-8490151.webp', 'assets/images/69ced264bb2ed.webp', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-02 22:32:36'),
(23, 15, '69ced2fcb35cb.webp', 'Afrikanische_Textilien.jpg.webp', 'assets/images/69ced2fcb35cb.webp', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-02 22:35:08'),
(24, 13, '69ced4ced7ead.jpg', 'gandoura-sans-manches-en-beige-avec-broderie-berbere.jpg', 'assets/images/69ced4ced7ead.jpg', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-02 22:42:54'),
(25, 13, '69ced5079bc85.webp', 'il_300x300.6771588794_dg6g.webp', 'assets/images/69ced5079bc85.webp', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-02 22:43:51'),
(26, 13, '69ced5af8ec17.jpg', 'coussin-berbere-blanc-rectangulaire-coussin-mazir-decoration-423.jpg', 'assets/images/69ced5af8ec17.jpg', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-02 22:46:39'),
(27, 14, '69ced664f3e2e.webp', 'homme-modeleur-silks-paris.webp', 'assets/images/69ced664f3e2e.webp', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-02 22:49:40'),
(28, 14, '69ced702d98a3.avif', 'homme-affaires-age-moyen-souriant-heureux-debout-ville_839833-16023.avif', 'assets/images/69ced702d98a3.avif', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-02 22:52:18'),
(29, 14, '69ced750bad44.jpg', 'iStock-1134288934-scaled-thegem-gallery-fullwidth.jpg', 'assets/images/69ced750bad44.jpg', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-02 22:53:36'),
(30, 4, '69cef79ea12c9.jpg', 'doffy.jpg', 'assets/images/69cef79ea12c9.jpg', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-03 01:11:26'),
(31, 4, '69cef81c7215f.png', 'emptySprite.png', 'assets/images/69cef81c7215f.png', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-03 01:13:32'),
(32, 4, '69cef821e19b1.png', 'emptySprite.png', 'assets/images/69cef821e19b1.png', 'image', '', 0, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-03 01:13:37');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id_message` int(10) UNSIGNED NOT NULL,
  `id_sender` int(10) UNSIGNED NOT NULL,
  `id_receiver` int(10) UNSIGNED NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sent_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `is_first_message` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'TRUE if sent before mutual connection; enforces §4.6 first-message rule',
  `deleted_by_sender` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_by_receiver` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `messages`
--

INSERT INTO `messages` (`id_message`, `id_sender`, `id_receiver`, `content`, `sent_at`, `is_read`, `is_first_message`, `deleted_by_sender`, `deleted_by_receiver`) VALUES
(1, 0, 1, 'salut', '2026-04-01 12:51:57', 0, 1, 0, 0),
(2, 4, 3, 'test', '2026-04-01 13:06:49', 0, 1, 0, 0),
(3, 5, 4, 'test', '2026-04-01 13:08:37', 0, 1, 0, 0),
(4, 4, 5, 'tg', '2026-04-01 13:08:45', 0, 1, 0, 0),
(5, 5, 4, 'ftg', '2026-04-01 13:09:01', 0, 0, 0, 0),
(6, 4, 6, 'tg', '2026-04-01 14:41:58', 0, 1, 0, 0),
(7, 6, 4, 't un fous', '2026-04-01 14:42:09', 0, 1, 0, 0),
(8, 6, 4, '', '2026-04-01 15:12:49', 0, 0, 0, 0),
(9, 6, 5, 'connard t\'es toujours en retard', '2026-04-01 15:34:59', 0, 1, 0, 0),
(10, 6, 5, '', '2026-04-01 15:35:08', 0, 0, 0, 0),
(11, 4, 3, 'lll', '2026-04-02 15:33:42', 0, 0, 0, 0),
(12, 4, 1, 'Donne ton snap sale pute', '2026-04-02 17:12:22', 0, 1, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `message_media`
--

CREATE TABLE `message_media` (
  `id_message` int(10) UNSIGNED NOT NULL,
  `id_media` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `message_media`
--

INSERT INTO `message_media` (`id_message`, `id_media`) VALUES
(8, 2),
(10, 4);

-- --------------------------------------------------------

--
-- Structure de la table `posts`
--

CREATE TABLE `posts` (
  `id_post` int(10) UNSIGNED NOT NULL,
  `id_author` int(10) UNSIGNED NOT NULL,
  `id_hashtag` int(10) UNSIGNED DEFAULT NULL COMMENT 'Primary skill/topic tag for the post',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci COMMENT 'Rich free-text description in author''s language',
  `visibility` enum('public','followers') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'followers',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `posts`
--

INSERT INTO `posts` (`id_post`, `id_author`, `id_hashtag`, `title`, `body`, `visibility`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'Ma collection vintage', 'Je vous partage ma dernière trouvaille des années 70.', 'public', '2026-03-27 16:01:38', NULL),
(2, 2, 14, 'Technique du Áo Dài', 'Découvrez comment coudre ce vêtement traditionnel étape par étape.', 'public', '2026-03-27 16:01:38', NULL),
(5, 15, NULL, '', 'Le Bogolan n\'est pas qu\'un tissu, c\'est une écriture. Chaque symbole raconte une protection, une force.', 'followers', '2026-04-02 22:32:36', NULL),
(6, 15, NULL, '', 'En direct de l\'atelier à Dakar. Derrière chaque vêtement, il y a des mains et des sourires.', 'followers', '2026-04-02 22:35:08', NULL),
(7, 13, NULL, '', 'Le projet de la semaine : redonner du pep\'s à cette magnifique tenue avec des motifs de chez moi. Qu\'en pensez-vous ?', 'followers', '2026-04-02 22:42:54', NULL),
(8, 13, NULL, '', 'Porter l\'histoire au quotidien. Merci mamie pour les conseils sur la coupe !', 'followers', '2026-04-02 22:43:51', NULL),
(9, 13, NULL, '', 'J\'ai voulu ajouter ma touche pour un intérieur qui représente ma culture berbère, c\'est plutôt réussi non ? Ca a transformé mon salon !', 'followers', '2026-04-02 22:46:39', NULL),
(10, 14, NULL, '', 'Le secret d\'un bon costume réside dans la patience et le respect du grain du tissu. Travail en cours sur une commande spéciale.', 'followers', '2026-04-02 22:49:40', NULL),
(11, 14, NULL, '', 'Là où tout a commencé. La mode passe, le style et l\'artisanat restent.', 'followers', '2026-04-02 22:52:18', NULL),
(12, 14, NULL, '', 'Chaque détail compte, chaque costume aussi. Essayage pour un grand évènement.', 'followers', '2026-04-02 22:53:36', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `post_likes`
--

CREATE TABLE `post_likes` (
  `id_post` int(10) UNSIGNED NOT NULL,
  `id_user` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `post_likes`
--

INSERT INTO `post_likes` (`id_post`, `id_user`) VALUES
(1, 14),
(2, 14),
(3, 4),
(4, 6),
(4, 7),
(5, 13),
(5, 14),
(6, 13),
(6, 14),
(7, 14),
(7, 15),
(8, 14),
(8, 15),
(9, 14),
(9, 15),
(10, 13),
(10, 15),
(11, 13),
(11, 15),
(12, 6),
(12, 13),
(12, 15);

-- --------------------------------------------------------

--
-- Structure de la table `post_media`
--

CREATE TABLE `post_media` (
  `id_post` int(10) UNSIGNED NOT NULL,
  `id_media` int(10) UNSIGNED NOT NULL,
  `sort_order` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Display order in gallery'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `post_media`
--

INSERT INTO `post_media` (`id_post`, `id_media`, `sort_order`) VALUES
(0, 3, 0),
(3, 6, 0),
(5, 22, 0),
(6, 23, 0),
(7, 24, 0),
(8, 25, 0),
(9, 26, 0),
(10, 27, 0),
(11, 28, 0),
(12, 29, 0);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id_user` int(10) UNSIGNED NOT NULL,
  `email` varchar(254) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birth_date` date NOT NULL,
  `location` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Human-readable city/country',
  `latitude` decimal(9,6) DEFAULT NULL COMMENT 'For map-based search',
  `longitude` decimal(9,6) DEFAULT NULL COMMENT 'For map-based search',
  `bio_free` text COLLATE utf8mb4_unicode_ci COMMENT 'Free-text skill/experience description',
  `avatar_url` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banner_url` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `skills` text COLLATE utf8mb4_unicode_ci COMMENT 'Comma separated skills',
  `interests` text COLLATE utf8mb4_unicode_ci COMMENT 'Comma separated interests',
  `ui_lang_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Preferred UI language',
  `terms_accepted` tinyint(1) NOT NULL DEFAULT '0',
  `terms_accepted_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL COMMENT 'GDPR soft-delete',
  `data_export_req_at` datetime DEFAULT NULL COMMENT 'GDPR data-export request timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id_user`, `email`, `password_hash`, `username`, `display_name`, `birth_date`, `location`, `latitude`, `longitude`, `bio_free`, `avatar_url`, `banner_url`, `contact_email`, `website`, `social_link`, `skills`, `interests`, `ui_lang_id`, `terms_accepted`, `terms_accepted_at`, `is_active`, `created_at`, `updated_at`, `deleted_at`, `data_export_req_at`) VALUES
(1, 'marie@france.fr', 'hash123', 'marie_fr', 'Marie Dupont', '1990-01-01', 'FR', NULL, NULL, 'Passionnée de mode vintage.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, '2026-03-27 16:01:38', NULL, NULL, NULL),
(2, 'nguyen@vietnam.vn', 'hash123', 'nguyen_vn', 'Nguyen Van A', '1985-05-05', 'VN', NULL, NULL, 'Tailleur traditionnel.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, '2026-03-27 16:01:38', NULL, NULL, NULL),
(3, 'raph@gmail.com', '$2y$10$bcJT4EMk2RihPn3XfBM3GeXulzpYdoxh0v6ASvSpjMlox/xgp2kXK', '', 'Raph', '0000-00-00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 1, '2026-04-01 13:03:46', NULL, NULL, NULL),
(4, 'test@gmail.com', '$2y$10$8OO/GJrxlEG9tVPhaSW9yujvZfOiewvjoDVC5cl4TK5bzPPJE/ZBi', '', 'Testeur', '0000-00-00', NULL, NULL, NULL, 'Testeur aguerri', 'assets/images/69cef81c7215f.png', 'assets/images/69cef821e19b1.png', '', '', '', '', '', NULL, 0, NULL, 1, '2026-04-01 13:04:09', '2026-04-03 01:13:37', NULL, NULL),
(5, 'fahed@gmail.com', '$2y$10$1zTGg6bvCALPKhKJYB5vauSVPp5EMaf0pelPTF79CfHirybdaVu12', '', 'Fahed', '0000-00-00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 1, '2026-04-01 13:07:41', NULL, NULL, NULL),
(6, 'maya@gmail.com', '$2y$10$Wtj2Qt/VES.dIh81liRvJeyO6DCetIPpgxDYU.Gz9FMlogwvUj4de', '', 'Maya', '0000-00-00', 'MA', NULL, NULL, '', 'assets/images/69ce7f6a7363a.jpeg', 'assets/images/69ce7f7262ce5.jpg', '', '', '', '', '', NULL, 0, NULL, 1, '2026-04-01 13:16:24', '2026-04-02 16:38:42', NULL, NULL),
(7, 'amauryfabre2007@gmail.com', '$2y$10$lLm4.SE7KLxpE0NbzbVwN.khu.q9G5RKT5oxpjYpH2JuGsGRJX4FW', '', 'Amaury', '0000-00-00', 'Paris', NULL, NULL, '', 'assets/images/69cd2744bfb42.jpg', 'assets/images/69ceb91fcd2c3.webp', '', '', '', '', '', NULL, 0, NULL, 1, '2026-04-01 16:10:04', '2026-04-02 20:44:47', NULL, NULL),
(8, 'test@tradishion.com', '$2y$10$f2Rs2W5McWs2/kahQ81BVeajCVQsZKJ7G69qwefnLS5rPbDTG6C0O', '', 'Test User', '1995-06-15', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 1, '2026-04-01 23:17:48', '2026-04-02 11:32:07', NULL, NULL),
(9, 'lucas@gmail.com', '$2y$10$fE1eqAHZz.GR6H52vV98P./urkXl7JNebPSORTyPWl7Ic35rZ5S/.', '', 'Lucas Randri', '2003-10-26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 1, '2026-04-02 11:26:48', NULL, NULL, NULL),
(10, 'jesuissteve@gmail.com', '$2y$10$CYoyQCZQ3Xk5Ol9WBXyUGe1EBOEJbDcav7raT8xAUGrKY3gnfglB6', '', 'Emmanuel Steve Mahougnon KPATINDE DOSSA', '0000-00-00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 1, '2026-04-02 17:48:54', NULL, NULL, NULL),
(11, 'slimani.kaina@gmail.com', '$2y$10$7l9Er5sKdvMROZup8rCIye4kX25/J11leDVHgAyWw/8dDfMH2AEvS', '', 'SLIMANI Kaïna', '1993-02-07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 1, '2026-04-02 18:11:56', NULL, NULL, NULL),
(12, 'bsy@gmail.com', '$2y$10$V3wLWYgvtTlhwwWeZziymeaZzZMWVp0QLlm.PKz4nIz6QSv1TUM5a', '', 'Boubakar Sy', '2007-12-28', NULL, NULL, NULL, 'J\'aime le mafé et les roadtrip en Europe pour découvrir des cultures ! ????', NULL, NULL, '', '', '', '', '', NULL, 0, NULL, 1, '2026-04-02 18:14:10', '2026-04-02 18:15:42', NULL, NULL),
(13, 'ines@gmail.com', '$2y$10$ebxdHHG8q1q8aA6aDgQOnOst4tzGMapSpSrFV6DPEuaxdHjD91YH2', '', 'BENSAID Inès', '2003-05-14', NULL, NULL, NULL, 'Étudiante en design à Paris. Passionnée par le mélange entre le streetwear moderne et les broderies traditionnelles berbères de ma grand-mère. Ici pour apprendre et m\'inspirer !', 'assets/images/69cecf7f7a0ea.jpg', 'assets/images/69cecf7f82a69.webp', '', '', 'inesdz', 'broderie', 'Design, streetwear', NULL, 0, NULL, 1, '2026-04-02 22:17:51', '2026-04-02 22:20:15', NULL, NULL),
(14, 'giacomo@gmail.com', '$2y$10$wLGXT.//DJtb9DWlgOctoeVgkZyHHqWd4KtGbgMWHgDDMBXyyoi9q', '', 'MORETTI Giacomo', '1958-11-03', NULL, NULL, NULL, 'Tailleur de père en fils depuis 40 ans. Spécialiste du costume sur mesure et du travail de la soie. Je souhaite transmettre les secrets de la coupe italienne à la nouvelle génération.', 'assets/images/69ced0083d477.avif', 'assets/images/69ced044ed3c6.webp', '', '', 'giacomo_couture', 'couture, tailleur sur mesure', 'Costume, soie', NULL, 0, NULL, 1, '2026-04-02 22:21:40', '2026-04-02 22:24:42', NULL, NULL),
(15, 'aminata@gmail.com', '$2y$10$1jkbxiSExoO4l3qThpHUX.pBGBpH/PMg9DKwJ5wzYxEEfOop16Tum', '', 'DIOP Aminata', '1990-08-22', NULL, NULL, NULL, 'Créatrice de mode indépendante. Je travaille exclusivement avec des artisans locaux au Sénégal pour promouvoir le véritable Wax et le Bogolan. La mode est un langage politique et culturel.', 'assets/images/69ced143f0b76.jpg', 'assets/images/69ced1692c4bd.jpg', '', 'www.aminata-diop.com', 'wax_aminata', 'Couture, sur mesure', 'Wax, Bogolan, local', NULL, 0, NULL, 1, '2026-04-02 22:26:33', '2026-04-02 22:30:09', NULL, NULL),
(16, 'dmel@gmail', '$2y$10$uQ6bz5paU3ZUvlrwnkgWseDOYW5yltt9IZ0xP18zVXxF8o2M/8j6q', '', 'dofus', '2006-07-02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 1, '2026-04-02 23:09:17', NULL, NULL, NULL),
(17, 'klai09@gmail.com', '$2y$10$oK3D9/hkRGQo//5tblWr.OE2VXyilM62hbUH7WsAKqUFUA0IF0.uK', '', 'Jonida', '2003-02-27', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 1, '2026-04-03 00:05:30', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `user_hashtags`
--

CREATE TABLE `user_hashtags` (
  `id_user` int(10) UNSIGNED NOT NULL,
  `id_hashtag` int(10) UNSIGNED NOT NULL,
  `role` enum('offer','seek') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'offer' COMMENT 'offer = sharing this skill; seek = wants to learn this'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user_languages`
--

CREATE TABLE `user_languages` (
  `id_user` int(10) UNSIGNED NOT NULL,
  `id_lang` int(10) UNSIGNED NOT NULL,
  `proficiency` enum('basic','conversational','fluent','native') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'conversational'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user_origins`
--

CREATE TABLE `user_origins` (
  `id_user` int(10) UNSIGNED NOT NULL,
  `country_code` char(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `user_origins`
--

INSERT INTO `user_origins` (`id_user`, `country_code`) VALUES
(1, 'FR'),
(2, 'VN'),
(6, 'DZ'),
(7, 'FR'),
(8, 'VN'),
(9, 'MG'),
(11, 'DZ'),
(12, 'ML'),
(12, 'SN'),
(13, 'DZ'),
(13, 'FR'),
(14, 'IT'),
(15, 'FR'),
(15, 'SN'),
(16, 'FR'),
(17, 'AL');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id_comment`);

--
-- Index pour la table `connections`
--
ALTER TABLE `connections`
  ADD PRIMARY KEY (`id_follower`,`id_followed`),
  ADD KEY `idx_conn_followed` (`id_followed`,`status`);

--
-- Index pour la table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id_event`),
  ADD KEY `idx_events_organizer` (`id_organizer`,`visibility`,`start_time`);

--
-- Index pour la table `event_media`
--
ALTER TABLE `event_media`
  ADD PRIMARY KEY (`id_event`,`id_media`),
  ADD KEY `fk_em_media` (`id_media`);

--
-- Index pour la table `event_participants`
--
ALTER TABLE `event_participants`
  ADD PRIMARY KEY (`id_event`,`id_user`),
  ADD KEY `fk_ep_invited_by` (`invited_by`),
  ADD KEY `idx_ep_user_status` (`id_user`,`status`);

--
-- Index pour la table `hashtags`
--
ALTER TABLE `hashtags`
  ADD PRIMARY KEY (`id_hashtag`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_hashtag_cat` (`id_category`);

--
-- Index pour la table `hashtag_categories`
--
ALTER TABLE `hashtag_categories`
  ADD PRIMARY KEY (`id_category`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Index pour la table `hashtag_category_i18n`
--
ALTER TABLE `hashtag_category_i18n`
  ADD PRIMARY KEY (`id_category`,`id_lang`),
  ADD KEY `fk_hcat_i18n_lang` (`id_lang`);

--
-- Index pour la table `hashtag_i18n`
--
ALTER TABLE `hashtag_i18n`
  ADD PRIMARY KEY (`id_hashtag`,`id_lang`),
  ADD KEY `fk_htag_i18n_lang` (`id_lang`);

--
-- Index pour la table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id_lang`),
  ADD UNIQUE KEY `lang_code` (`lang_code`);

--
-- Index pour la table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id_media`),
  ADD KEY `idx_media_uploader` (`id_uploader`,`media_type`,`created_at`),
  ADD KEY `idx_media_public` (`is_public`,`media_type`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id_message`),
  ADD KEY `fk_msg_sender` (`id_sender`),
  ADD KEY `idx_msg_receiver` (`id_receiver`,`is_read`,`sent_at`);

--
-- Index pour la table `message_media`
--
ALTER TABLE `message_media`
  ADD PRIMARY KEY (`id_message`,`id_media`),
  ADD KEY `fk_mm_media` (`id_media`);

--
-- Index pour la table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id_post`),
  ADD KEY `idx_posts_author` (`id_author`,`visibility`,`created_at`),
  ADD KEY `idx_posts_hashtag` (`id_hashtag`,`visibility`);

--
-- Index pour la table `post_likes`
--
ALTER TABLE `post_likes`
  ADD PRIMARY KEY (`id_post`,`id_user`);

--
-- Index pour la table `post_media`
--
ALTER TABLE `post_media`
  ADD PRIMARY KEY (`id_post`,`id_media`),
  ADD KEY `fk_pm_media` (`id_media`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- Index pour la table `user_hashtags`
--
ALTER TABLE `user_hashtags`
  ADD PRIMARY KEY (`id_user`,`id_hashtag`,`role`),
  ADD KEY `fk_uhtag_htag` (`id_hashtag`);

--
-- Index pour la table `user_languages`
--
ALTER TABLE `user_languages`
  ADD PRIMARY KEY (`id_user`,`id_lang`),
  ADD KEY `fk_ulang_lang` (`id_lang`);

--
-- Index pour la table `user_origins`
--
ALTER TABLE `user_origins`
  ADD PRIMARY KEY (`id_user`,`country_code`),
  ADD KEY `country_code` (`country_code`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `comments`
--
ALTER TABLE `comments`
  MODIFY `id_comment` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
--
-- AUTO_INCREMENT pour la table `events`
--
ALTER TABLE `events`
  MODIFY `id_event` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT pour la table `hashtags`
--
ALTER TABLE `hashtags`
  MODIFY `id_hashtag` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT pour la table `hashtag_categories`
--
ALTER TABLE `hashtag_categories`
  MODIFY `id_category` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT pour la table `media`
--
ALTER TABLE `media`
  MODIFY `id_media` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id_message` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT pour la table `posts`
--
ALTER TABLE `posts`
  MODIFY `id_post` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `user_origins`
--
ALTER TABLE `user_origins`
  ADD CONSTRAINT `user_origins_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
