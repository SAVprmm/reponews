SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Структура таблицы `test_news`
--

CREATE TABLE `test_news` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

--
-- Дамп данных таблицы `test_news`
--

INSERT INTO `test_news` (`id`, `name`, `text`, `created_at`, `deleted_at`) VALUES
(10000003, 'test112', 'test112', '2022-11-26 13:12:24', '2022-11-26 13:12:24'),
(10000002, 'name last off', 'text last off', '2022-11-26 10:19:49', '2022-11-26 11:00:00'),
(10000001, 'test111', 'test111', '2022-11-24 18:50:58', NULL),
(6710169, 'name 615', 'text 5743', '2022-11-24 19:00:12', NULL),
(8871660, 'name 978', 'text 5901', '2022-11-24 12:58:24', NULL),
(5226271, 'name 171', 'text 5953', '2022-11-24 08:56:33', NULL),
(5958350, 'name 781', 'text 1299', '2022-11-24 11:56:26', NULL),
(1220910, 'name 242', 'text 9375', '2022-11-23 03:56:25', NULL),
(4210669, 'name 911', 'text 3047', '2022-11-23 08:54:15', NULL),
(5470489, 'name 520', 'text 7952', '2022-11-23 20:54:06', NULL),
(2217254, 'name 664', 'text 2107', '2022-11-23 09:53:32', NULL),
(6608935, 'name 950', 'text 5899', '2022-11-22 22:46:47', '2020-09-11 13:32:31'),
(6447354, 'name 863', 'text 6883', '2022-11-22 18:46:18', NULL),
(9507165, 'name 804', 'text 4697', '2022-11-22 14:45:11', NULL),
(3862851, 'name 463', 'text 3985', '2022-11-22 08:45:09', NULL),
(5442443, 'name 873', 'text 5721', '2022-11-22 07:42:57', NULL),
(8720495, 'name 771', 'text 8219', '2022-11-22 20:42:35', NULL),
(9307228, 'name 343', 'text 6053', '2022-11-21 21:42:01', NULL),
(9622784, 'name 308', 'text 5124', '2022-11-21 08:38:49', NULL),
(6351064, 'name 971', 'text 6422', '2022-11-21 17:38:25', NULL),
(3700009, 'name 785', 'text 2568', '2022-11-21 01:37:01', '2019-05-09 13:42:32'),
(6817591, 'name 916', 'text 5955', '2022-11-20 14:36:45', NULL),
(360339, 'name 433', 'text 8968', '2022-11-20 11:35:53', NULL),
(3848865, 'name 127', 'text 1008', '2022-11-20 01:35:13', NULL),
(5905224, 'name 683', 'text 7722', '2022-11-20 15:34:15', NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `test_news`
--
ALTER TABLE `test_news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dates_at` (`created_at`,`deleted_at`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `test_news`
--
ALTER TABLE `test_news`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10000004;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
