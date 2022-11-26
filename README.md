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

Entity
  ```
  \>src/Entity/TestNews.php
  ```
Repository
  ```
  \>src/Repository/TestNewsRepository.php
  ```
  
### Methods of Repository
1. find()
  ```
  ->find(int $id, $lockMode = null, $lockVersion = null)
  ```
found news by id

2. findFreshLastDays()
  ```
  ->findFreshLastDays(int $days = 3, array $orderBy = null)
  ```
finds fresh news for the last $days number of days.
by default 3 days.

3. Pagination methods
  ```
  ->findPagination(int $id, int $newsPerPage = 0);
  ->paginationMoveCurrent();
  ->paginationMovePrev(int $pageCount = 1);
  ->paginationMoveNext(int $pageCount = 1);
  ->PaginationResult();
  ```

  ```
  findPagination:         preparing extraction of news page
   $id:                   first news id on dispaible page
   $newsPerPage:          to override the default news count per page
  ```
  ```  
  paginationMoveCurrent:  display current page with $id
  ``` 
  ``` 
  paginationMovePrev:     retrieve the previous page moving away from the current one
   $pageCount:            step of moving
  ```
  ``` 
 paginationMoveNext:      retrieve the next page moving away from the current one
   $pageCount:            step of moving
  ```
  ```
  PaginationResult:       get result
  ```

##### Using
  ```
                      ->paginationMovePrev()
  ->findPagination()  ->paginationMoveCurrent()  ->PaginationResult();
                      ->paginationMoveNext()
  ```  
 
 ### Install
 1. PHP 7.2+
 2. Symfony 4.2 (composer.json include)
 3. Home controller routed to index and can be tested with
    symfony webserver
    ```
    php bin/console server:run
    ```
    
 ### Sql performance
 test table have 10 000 000 rows
 ```sh
 mysql> SELECT COUNT(*) FROM `test_news` WHERE `deleted_at` IS NULL;
+----------+
| COUNT(*) |
+----------+
|  8345516 |
+----------+
1 row in set (1.68 sec)

mysql> SELECT COUNT(*) FROM `test_news` WHERE `deleted_at` IS NOT NULL;
+----------+
| COUNT(*) |
+----------+
|  1654487 |
+----------+
1 row in set (1.46 sec)
 ```
 
 ##### Used query
 ```sh
 mysql> EXPLAIN SELECT t0_.id AS id_0, t0_.name AS name_1, t0_.text AS text_2, t0_.created_at AS created_at_3, t0_.deleted_at AS deleted_at_4 \
 FROM test_news t0_ \
 WHERE t0_.created_at >= '2022-11-23 00:00:00' AND t0_.deleted_at IS NULL \
 ORDER BY t0_.created_at ASC;
+----+-------------+-------+------------+-------+---------------+----------+---------+------+------+----------+-----------------------+
| id | select_type | table | partitions | type  | possible_keys | key      | key_len | ref  | rows | filtered | Extra                 |
+----+-------------+-------+------------+-------+---------------+----------+---------+------+------+----------+-----------------------+
|  1 | SIMPLE      | t0_   | NULL       | range | dates_at      | dates_at | 11      | NULL |  373 |    10.00 | Using index condition |
+----+-------------+-------+------------+-------+---------------+----------+---------+------+------+----------+-----------------------+
1 row in set, 1 warning (0.00 sec)
 ```
 
 ```sh
mysql> EXPLAIN SELECT t0_.id AS id_0, t0_.name AS name_1, t0_.text AS text_2, t0_.created_at AS created_at_3, t0_.deleted_at AS deleted_at_4 \
FROM test_news t0_ \
WHERE t0_.created_at < '2022-06-08 00:16:16' AND t0_.created_at >= '2022-05-31 00:16:16' AND t0_.deleted_at IS NULL \
ORDER BY t0_.created_at ASC;
+----+-------------+-------+------------+-------+---------------+----------+---------+------+------+----------+-----------------------+
| id | select_type | table | partitions | type  | possible_keys | key      | key_len | ref  | rows | filtered | Extra                 |
+----+-------------+-------+------------+-------+---------------+----------+---------+------+------+----------+-----------------------+
|  1 | SIMPLE      | t0_   | NULL       | range | dates_at      | dates_at | 11      | NULL | 7247 |    10.00 | Using index condition |
+----+-------------+-------+------------+-------+---------------+----------+---------+------+------+----------+-----------------------+
1 row in set, 1 warning (0.00 sec)

 ```
 
 ```sh
mysql> EXPLAIN SELECT t0_.id AS id_0, t0_.name AS name_1, t0_.text AS text_2, t0_.created_at AS created_at_3, t0_.deleted_at AS deleted_at_4 \
FROM test_news t0_ \
WHERE t0_.created_at >= '2022-06-15 00:16:16' AND t0_.deleted_at IS NULL \
ORDER BY t0_.created_at ASC \
LIMIT 41 OFFSET 40;
+----+-------------+-------+------------+-------+---------------+----------+---------+------+--------+----------+-----------------------+
| id | select_type | table | partitions | type  | possible_keys | key      | key_len | ref  | rows   | filtered | Extra                 |
+----+-------------+-------+------------+-------+---------------+----------+---------+------+--------+----------+-----------------------+
|  1 | SIMPLE      | t0_   | NULL       | range | dates_at      | dates_at | 11      | NULL | 285268 |    10.00 | Using index condition |
+----+-------------+-------+------------+-------+---------------+----------+---------+------+--------+----------+-----------------------+
1 row in set, 1 warning (0.00 sec)

 ```
 
 ```sh
mysql> EXPLAIN SELECT t0_.id AS id_0, t0_.name AS name_1, t0_.text AS text_2, t0_.created_at AS created_at_3, t0_.deleted_at AS deleted_at_4 \
FROM test_news t0_ \
WHERE t0_.created_at > '0000-00-00 00:00:00' AND t0_.deleted_at IS NULL \
ORDER BY t0_.created_at ASC \
LIMIT 1 OFFSET 0;
+----+-------------+-------+------------+-------+---------------+----------+---------+------+---------+----------+-----------------------+
| id | select_type | table | partitions | type  | possible_keys | key      | key_len | ref  | rows    | filtered | Extra                 |
+----+-------------+-------+------------+-------+---------------+----------+---------+------+---------+----------+-----------------------+
|  1 | SIMPLE      | t0_   | NULL       | range | dates_at      | dates_at | 5       | NULL | 4986899 |    10.00 | Using index condition |
+----+-------------+-------+------------+-------+---------------+----------+---------+------+---------+----------+-----------------------+
1 row in set, 1 warning (0.00 sec) 
 ```
 
 ```sh
mysql> EXPLAIN SELECT t0_.id AS id_0, t0_.name AS name_1, t0_.text AS text_2, t0_.created_at AS created_at_3, t0_.deleted_at AS deleted_at_4 \
FROM test_news t0_ \
WHERE t0_.created_at <= NOW() AND t0_.deleted_at IS NULL \
ORDER BY t0_.created_at DESC \
LIMIT 1 OFFSET 0;
+----+-------------+-------+------------+-------+---------------+----------+---------+------+---------+----------+--------------------------------------------+
| id | select_type | table | partitions | type  | possible_keys | key      | key_len | ref  | rows    | filtered | Extra                                      |
+----+-------------+-------+------------+-------+---------------+----------+---------+------+---------+----------+--------------------------------------------+
|  1 | SIMPLE      | t0_   | NULL       | range | dates_at      | dates_at | 11      | NULL | 4986899 |    10.00 | Using index condition; Backward index scan |
+----+-------------+-------+------------+-------+---------------+----------+---------+------+---------+----------+--------------------------------------------+
1 row in set, 1 warning (0.00 sec) 
 ```
 
### PHPunit

```
\>tests\Repository\TestNewsRepositoryTest.php
```
class for test repository, can be execite
php bin/phpunit tests/Repository/TestNewsRepositoryTest.php

```
\>tests\Repository\TestNewsRepositoryTest.php.html
```
Class \TestNewsRepositoryTest(); create simple <a href="https://github.com/SAVprmm/reponews/blob/main/tests/Repository/TestNewsRepositoryTest.php.html"><strong>html result »</strong></a> of call test function with explanation

```
\>tests\Repository\TestNewsRepositoryTest.php.jpg
```
image of executing test in console.

 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
