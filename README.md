# reponews
simple repository of News

### Getting Started
table `test_news`

  ```
  CREATE TABLE `test_news` (
    `id` int(10) UNSIGNED NOT NULL,
    `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `dates_at` (`created_at`,`deleted_at`) USING BTREE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;
  ```
  file with sql dump `\>test_news.sql` with table struct and 25 rows of news with random insert of datetime
