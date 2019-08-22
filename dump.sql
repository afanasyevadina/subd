-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Авг 22 2019 г., 08:47
-- Версия сервера: 5.6.37
-- Версия PHP: 7.0.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `dbmanager`
--
CREATE DATABASE IF NOT EXISTS `dbmanager` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `dbmanager`;

-- --------------------------------------------------------

--
-- Структура таблицы `calculate`
--

CREATE TABLE `calculate` (
  `db_name` varchar(255) NOT NULL,
  `table_name` varchar(255) NOT NULL,
  `col_name` varchar(255) NOT NULL,
  `formula` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `calculate`
--

INSERT INTO `calculate` (`db_name`, `table_name`, `col_name`, `formula`) VALUES
('it', 'employee', 'real_salary', 'salary*coef'),
('it', 'Экзамен', 'Итого', 'Математика+Физика+Русскийязык+История'),
('test_db', 'clothes', 'itogo', 'count*price*(100-skidka)/100'),
('test_db', 'Постояльцы', 'КоличествоДней', 'ДатаОтъезда-ДатаПриезда'),
('test_db', 'тестТаблица', 'Скоростьсвета', '299792458'),
('test_db', 'тестТаблица', 'Энергия', 'Масса*Скоростьсвета*Скоростьсвета');

-- --------------------------------------------------------

--
-- Структура таблицы `dbs`
--

CREATE TABLE `dbs` (
  `db_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `dbs`
--

INSERT INTO `dbs` (`db_name`) VALUES
('it'),
('stayingAlive'),
('test_db');

-- --------------------------------------------------------

--
-- Структура таблицы `grants`
--

CREATE TABLE `grants` (
  `grant_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `db_name` varchar(255) NOT NULL,
  `select_data` int(11) DEFAULT NULL,
  `update_data` int(11) DEFAULT NULL,
  `create_table` int(11) DEFAULT NULL,
  `drop_table` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `grants`
--

INSERT INTO `grants` (`grant_id`, `user_name`, `db_name`, `select_data`, `update_data`, `create_table`, `drop_table`) VALUES
(33, 'dmitry', 'it', 1, 1, 1, 1),
(34, 'a', 'it', 1, 1, NULL, NULL),
(36, 'dmitry', 'test_db', 1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `tables`
--

CREATE TABLE `tables` (
  `table_name` varchar(255) NOT NULL DEFAULT '',
  `last_update` datetime DEFAULT NULL,
  `last_user` varchar(255) DEFAULT NULL,
  `creator` varchar(255) DEFAULT NULL,
  `db_name` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `tables`
--

INSERT INTO `tables` (`table_name`, `last_update`, `last_user`, `creator`, `db_name`, `created_at`) VALUES
('clothes', '2019-08-13 15:08:36', 'root', 'root', 'test_db', '2019-08-12 16:25:59'),
('dontWorry', '2019-08-21 09:06:56', 'root', 'root', 'stayingAlive', '2019-08-14 09:41:25'),
('employee', '2019-08-16 10:52:33', 'dmitry', 'dmitry', 'it', '2019-08-09 16:19:02'),
('normal', '2019-08-08 16:58:12', 'root', 'root', 'again', '2019-08-07 09:39:25'),
('someTable', '2019-08-09 12:34:32', 'root', 'root', 'again', '2019-08-07 17:14:52'),
('wskModules', '2019-08-21 10:29:01', 'dmitry', 'dmitry', 'it', '2019-08-21 10:26:21'),
('Постояльцы', '2019-08-16 09:49:00', 'root', 'root', 'test_db', '2019-08-09 17:08:49'),
('тестТаблица', '2019-08-16 09:48:05', 'root', 'root', 'test_db', '2019-08-12 09:57:58'),
('Экзамен', '2019-08-16 11:02:47', 'dmitry', 'dmitry', 'it', '2019-08-13 14:13:30');

-- --------------------------------------------------------

--
-- Структура таблицы `types`
--

CREATE TABLE `types` (
  `type_id` int(11) NOT NULL,
  `type_name` varchar(255) DEFAULT NULL,
  `real_name` varchar(255) DEFAULT NULL,
  `ordered` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `types`
--

INSERT INTO `types` (`type_id`, `type_name`, `real_name`, `ordered`) VALUES
(1, 'Целое число', 'int(11)', 1),
(2, 'Короткий текст', 'varchar(255)', 4),
(3, 'Дата', 'date', 6),
(4, 'Дробное число', 'float', 2),
(5, 'Длинный текст', 'longtext', 5),
(6, 'Символ', 'char', 3),
(7, 'Список значений', 'enum', 8),
(8, 'Логический', 'tinyint(1)', 7);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `user_name` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `hash` varchar(500) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `last_ping` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`user_name`, `password`, `hash`, `role`, `last_ping`) VALUES
('a', 'b', '110c729e5ca14c5778aaf4221af48e68', NULL, '2019-08-09 16:40:17'),
('dmitry', 'hidden', '955509eec89d32d5e65db2f77b99f554', NULL, '2019-08-22 11:44:40'),
('root', '', '390A818897178125A703D277145FF681', 'admin', '2019-08-21 10:23:36');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `calculate`
--
ALTER TABLE `calculate`
  ADD PRIMARY KEY (`db_name`,`table_name`,`col_name`);

--
-- Индексы таблицы `dbs`
--
ALTER TABLE `dbs`
  ADD PRIMARY KEY (`db_name`),
  ADD UNIQUE KEY `db_name` (`db_name`);

--
-- Индексы таблицы `grants`
--
ALTER TABLE `grants`
  ADD PRIMARY KEY (`grant_id`),
  ADD UNIQUE KEY `user_id` (`user_name`,`db_name`);

--
-- Индексы таблицы `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`table_name`);

--
-- Индексы таблицы `types`
--
ALTER TABLE `types`
  ADD PRIMARY KEY (`type_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_name`),
  ADD UNIQUE KEY `user_name` (`user_name`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `grants`
--
ALTER TABLE `grants`
  MODIFY `grant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
--
-- AUTO_INCREMENT для таблицы `types`
--
ALTER TABLE `types`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;--
-- База данных: `it`
--
CREATE DATABASE IF NOT EXISTS `it` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `it`;

-- --------------------------------------------------------

--
-- Структура таблицы `employee`
--

CREATE TABLE `employee` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `position` enum('programmer','designer','tester','manager','administrator') NOT NULL,
  `salary` float NOT NULL,
  `born_date` date NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `employment_date` date NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `coef` float NOT NULL DEFAULT '1',
  `real_salary` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `employee`
--

INSERT INTO `employee` (`id`, `name`, `position`, `salary`, `born_date`, `email`, `employment_date`, `gender`, `coef`, `real_salary`) VALUES
(1, 'Dmitry', 'programmer', 75000, '1999-06-20', 'kovalevsky@gmail.com', '2019-08-08', 'male', 1.2, 90000),
(2, 'Dina', 'programmer', 76000, '2000-03-20', 'afanasyeva@gmail.com', '2019-07-17', 'female', 1.45, 110200),
(3, 'Alex', 'tester', 72000, '2000-01-15', '', '2019-07-31', 'male', 1.25, 90000),
(4, 'Vitalina', 'designer', 78000, '2000-05-23', '', '2019-07-30', 'female', 1.05, 81900),
(5, 'Jane', 'manager', 70000, '2001-07-28', '', '2019-07-29', 'female', 1.05, 73500),
(6, 'Timur', 'administrator', 65000, '2001-09-19', '', '2019-07-12', 'male', 1.3, 84500),
(7, 'Aidar', 'designer', 78000, '1999-12-01', '', '2019-06-14', 'male', 1, 78000);

-- --------------------------------------------------------

--
-- Структура таблицы `wskModules`
--

CREATE TABLE `wskModules` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `hours` int(11) NOT NULL,
  `ready` tinyint(1) DEFAULT NULL,
  `description` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `wskModules`
--

INSERT INTO `wskModules` (`id`, `name`, `hours`, `ready`, `description`) VALUES
(1, 'PHP and JS', 6, 0, 'Create a RESTful API and frontend using it'),
(2, 'Design and Frontend', 6, 0, 'Online presentation editor with show functionality'),
(3, 'CMS and Layout', 6, 0, 'Create a site on WordPress, create plugins and themes');

-- --------------------------------------------------------

--
-- Структура таблицы `Экзамен`
--

CREATE TABLE `Экзамен` (
  `Студент` varchar(255) NOT NULL,
  `Математика` int(11) DEFAULT NULL,
  `Физика` int(11) DEFAULT NULL,
  `Русскийязык` int(11) DEFAULT NULL,
  `История` int(11) DEFAULT NULL,
  `Итого` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Экзамен`
--

INSERT INTO `Экзамен` (`Студент`, `Математика`, `Физика`, `Русскийязык`, `История`, `Итого`) VALUES
('Алиса', 10, 10, 10, 9, 39),
('Ангелина', 10, 9, 10, 10, 39),
('Вася', 10, 10, 10, 10, 40),
('Петя', 9, 7, 10, 6, 32);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `wskModules`
--
ALTER TABLE `wskModules`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Экзамен`
--
ALTER TABLE `Экзамен`
  ADD PRIMARY KEY (`Студент`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `employee`
--
ALTER TABLE `employee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT для таблицы `wskModules`
--
ALTER TABLE `wskModules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;--
-- База данных: `stayingAlive`
--
CREATE DATABASE IF NOT EXISTS `stayingAlive` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `stayingAlive`;

-- --------------------------------------------------------

--
-- Структура таблицы `dontWorry`
--

CREATE TABLE `dontWorry` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `dateBorn` date NOT NULL,
  `isMember` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `dontWorry`
--

INSERT INTO `dontWorry` (`id`, `name`, `dateBorn`, `isMember`) VALUES
(1, 'Alissa', '1994-10-20', 1),
(2, 'Arkady', '1993-01-01', 0),
(3, 'Renata', '2003-04-16', 1);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `dontWorry`
--
ALTER TABLE `dontWorry`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `dontWorry`
--
ALTER TABLE `dontWorry`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;--
-- База данных: `test_db`
--
CREATE DATABASE IF NOT EXISTS `test_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `test_db`;

-- --------------------------------------------------------

--
-- Структура таблицы `clothes`
--

CREATE TABLE `clothes` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` float NOT NULL,
  `count` int(11) NOT NULL,
  `skidka` float NOT NULL,
  `itogo` float NOT NULL,
  `category` enum('Clothes','Footwear','Accessoires','Other') NOT NULL DEFAULT 'Other'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `clothes`
--

INSERT INTO `clothes` (`id`, `name`, `price`, `count`, `skidka`, `itogo`, `category`) VALUES
(7, 'Black shoes', 4990, 2, 20, 7984, 'Footwear'),
(8, 'Jeans', 5490, 3, 5, 15646.5, 'Clothes'),
(9, 'T-shirt', 2490, 5, 15, 10582.5, 'Clothes'),
(10, 'Sweater', 5490, 1, 0, 5490, 'Clothes'),
(11, 'Shirt', 3990, 10, 20, 31920, 'Clothes'),
(12, 'Blouse', 2490, 2, 5, 4731, 'Clothes'),
(13, 'Trousers', 7990, 4, 0, 31960, 'Clothes'),
(14, 'Shorts', 4490, 4, 0, 17960, 'Clothes'),
(15, 'Skirt', 5490, 1, 0, 5490, 'Clothes'),
(16, 'Coat', 14990, 1, 0, 14990, 'Clothes'),
(17, 'Jacket', 11990, 2, 0, 23980, 'Clothes'),
(18, 'Bomber jacket', 9990, 2, 0, 19980, 'Clothes'),
(19, 'Belt', 990, 1, 0, 990, 'Accessoires'),
(20, 'Cap', 1490, 7, 0, 10430, 'Accessoires'),
(21, 'Tie', 1990, 8, 0, 15920, 'Accessoires'),
(22, 'Boots', 8990, 1, 0, 8990, 'Footwear'),
(23, 'Trainers', 12990, 3, 0, 38970, 'Footwear'),
(24, 'Slippers', 7990, 7, 0, 55930, 'Footwear'),
(25, 'Sneaker', 4490, 5, 0, 22450, 'Footwear'),
(26, 'Wedge', 11490, 1, 0, 11490, 'Footwear'),
(27, 'Sandals', 1990, 2, 0, 3980, 'Other');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(15) NOT NULL,
  `password` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `password`) VALUES
(1, 'Talga', '123');

-- --------------------------------------------------------

--
-- Структура таблицы `Постояльцы`
--

CREATE TABLE `Постояльцы` (
  `id` int(11) NOT NULL,
  `Имя` varchar(255) NOT NULL,
  `ДатаПриезда` date NOT NULL,
  `ДатаОтъезда` date NOT NULL,
  `КоличествоДней` int(11) NOT NULL,
  `Мигрант` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `Постояльцы`
--

INSERT INTO `Постояльцы` (`id`, `Имя`, `ДатаПриезда`, `ДатаОтъезда`, `КоличествоДней`, `Мигрант`) VALUES
(1, 'Вася', '2019-08-05', '2019-08-10', 5, 1),
(2, 'Петя', '2019-08-05', '2019-08-08', 3, 0),
(3, 'Саша', '2019-08-07', '2019-08-10', 3, 1),
(4, 'Андрей', '2019-08-07', '2019-08-10', 3, 0),
(5, 'Марина', '2019-08-07', '2019-08-13', 6, 1),
(6, 'Дания', '2019-08-07', '2019-08-14', 7, 0),
(7, 'Арсения', '2019-08-07', '2019-08-08', 1, 1),
(8, 'Игорь', '2019-08-07', '2019-08-12', 5, 0),
(9, 'Джамшут', '2019-08-07', '2019-08-14', 7, 1),
(10, 'Владислава', '2019-08-12', '2019-08-16', 4, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `тестТаблица`
--

CREATE TABLE `тестТаблица` (
  `Код` int(11) NOT NULL,
  `Название` varchar(255) NOT NULL DEFAULT '',
  `Масса` float NOT NULL,
  `Скоростьсвета` float NOT NULL,
  `Энергия` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `тестТаблица`
--

INSERT INTO `тестТаблица` (`Код`, `Название`, `Масса`, `Скоростьсвета`, `Энергия`) VALUES
(1, 'Спаситеhfgh\';', 1, 299792000, 8.98755e16),
(2, 'Спаси\'; -- те', 5, 299792000, 4.49378e17),
(3, 'Спаситеgfjgh', 3, 299792000, 2.69627e17),
(4, 'Спасите', 1.89, 299792000, 1.69865e17),
(5, 'gfhfgh\';--ghjg', 5646, 299792000, 5.07437e20),
(6, 'ghj', 6, 299792000, 5.39253e17);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `clothes`
--
ALTER TABLE `clothes`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Постояльцы`
--
ALTER TABLE `Постояльцы`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `тестТаблица`
--
ALTER TABLE `тестТаблица`
  ADD PRIMARY KEY (`Код`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `clothes`
--
ALTER TABLE `clothes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `Постояльцы`
--
ALTER TABLE `Постояльцы`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT для таблицы `тестТаблица`
--
ALTER TABLE `тестТаблица`
  MODIFY `Код` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
