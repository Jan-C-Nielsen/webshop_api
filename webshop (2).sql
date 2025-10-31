-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Vært: localhost:3306
-- Genereringstid: 31. 10 2025 kl. 10:55:45
-- Serverversion: 5.7.24
-- PHP-version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webshop`
--

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` varchar(100) NOT NULL,
  `phone` varchar(14) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Data dump for tabellen `customers`
--

INSERT INTO `customers` (`id`, `name`, `address`, `phone`) VALUES
(1, 'Jan Nielsen', 'Pulsen 8', '5555555'),
(2, 'test3', 'bistrup 44', '666'),
(3, 'Fanny', 'Gadevænget', '6666'),
(6, 'Jenny', 'Gadevænget 8', '7777');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `media`
--

CREATE TABLE `media` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `filetype` varchar(10) NOT NULL,
  `url` varchar(2000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Data dump for tabellen `media`
--

INSERT INTO `media` (`id`, `name`, `filetype`, `url`) VALUES
(1, 'Klejner', '', 'https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEhyGnGY53bbwWqqzMIztt2dtAzqzskxh_edVcWGEPcsbuNSj05JaOl_4Y9j9dRuv81YcqgtqlT8U4ssnXzzvIK88G9GOzar9WUmKt2nJiMJMfke7LgwFu__dwqwaxOgHmOJfH6PYFwOWms/s1600/IMG_1890-001.JPG'),
(2, 'test', 'test', 'http://test.com'),
(3, 'test4', 'jpg', 'http::/test2'),
(13, 'Biscotti', 'jpg', 'https://madenimitliv.dk/wp-content/uploads/2022/10/Pebernoedder-2-scaled-2.jpg'),
(14, 'vanilikrans', 'jpg', 'https://mummum.dk/wp-content/uploads/2023/01/4Y7A4130-min.jpg'),
(15, 'Finskbrød', 'jpg', 'https://www.valdemarsro.dk/wp-content/2016/12/finskbroed-1.jpg'),
(16, 'Finskbrød', 'jpg', 'https://madensverden.dk/wp-content/uploads/2016/03/finskbroed-finsk-broed.jpg'),
(17, 'Fedtebrød', 'jpg', 'https://mummum.dk/wp-content/uploads/2024/10/fedtebroed-med-citronglasur-min.jpg'),
(18, 'Fedtebrød', 'youtube', 'https://www.google.com/url?sa=t&rct=j&q=&esrc=s&source=video&cd=&cad=rja&uact=8&ved=2ahUKEwiM5OGj9s2QAxUvSvEDHSSRLeMQtwJ6BAgXEAI&url=https%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3DqlFUk4KxGVU&usg=AOvVaw0PwrKaQ722oFkWGpxjL8ao&opi=89978449'),
(19, 'Jødekager', 'jpg', 'https://www.amo.dk/siteassets/2.-opskrifter/jule/gammeldags-jodekager.jpg?width=1440'),
(20, 'Kokostoppe', 'jpg', 'https://spisbedre-production-app.imgix.net/images/recipes/kokostoppe-med-marcipan_10951.jpg?auto=format&ar=655%3A500&fit=crop&crop=focalpoint&fp-x=0.5054954574052445&fp-y=0.4943500582015676&fp-z=1.0114290303089815'),
(21, 'Havregrynskager', 'jpg', 'https://xhgnv2ng.photoncache.com/wp-content/uploads/2021/09/DSC_1501.jpg'),
(22, 'Chokoladecookies', 'jpg', 'https://mummum.dk/wp-content/uploads/2021/09/IMG_7090-min-1536x1255.jpg'),
(23, 'Smørkage', 'jpg', 'https://madenimitliv.dk/wp-content/uploads/2022/10/Smoerkage-2-scaled-2.jpg'),
(24, 'Kanelkager', 'jpg', 'https://xhgnv2ng.photoncache.com/wp-content/uploads/2012/06/DSC_1546.jpg'),
(25, 'Mandelkage', 'jpg', 'https://xhgnv2ng.photoncache.com/wp-content/uploads/2024/11/DSC_1122.JPG'),
(26, 'Små citronmåner ', 'jpg', 'https://madensverden.dk/wp-content/uploads/2015/12/citronmacc8ane-muffins.jpg'),
(27, 'Sirupskager', 'jpg', 'https://xhgnv2ng.photoncache.com/wp-content/uploads/2014/06/DSC_6350.jpg'),
(28, 'Nøddesmåkage', 'jpg', 'https://mad.winther.nu/image/7c601280-1636-4d13-a150-697ad69328a4.jpg'),
(29, 'Kaffekager', 'jpg', 'https://juliemarieeats.com/wp-content/uploads/2022/08/Chocolate-Chip-Coffee-Cookies-7-scaled.jpg'),
(30, 'Marengskys', 'jpg', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRztoLFTr7UbeQbOxb2AxrpGm8vgrL4Wv2Jfg&s');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `customer_id` int(11) NOT NULL,
  `delivered` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Data dump for tabellen `orders`
--

INSERT INTO `orders` (`id`, `date`, `customer_id`, `delivered`) VALUES
(2, '2025-10-27 14:32:54', 2, '2025-10-30 15:50:11'),
(3, '2025-10-27 15:31:51', 1, NULL),
(5, '2025-10-30 12:30:48', 2, NULL),
(6, '2025-10-31 10:45:06', 3, NULL),
(7, '2025-10-31 10:56:02', 1, NULL);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `orders_products`
--

CREATE TABLE `orders_products` (
  `orders_id` int(11) NOT NULL,
  `products_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Data dump for tabellen `orders_products`
--

INSERT INTO `orders_products` (`orders_id`, `products_id`) VALUES
(2, 1),
(3, 3),
(3, 3),
(3, 5),
(3, 17);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(6,2) NOT NULL,
  `weight_in_grams` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Data dump for tabellen `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `weight_in_grams`) VALUES
(1, 'klejner', 'småkage', '10.00', 200),
(2, 'brun kager', 'småkage', '8.00', 200),
(3, 'peber-nødder', 'småkage', '6.00', 200),
(4, 'vanillie kranse', 'småkage', '6.00', 200),
(5, 'Finskbrød', 'Klassiske finskbrød', '10.50', 300),
(7, 'test4', 'test småkage', '0.00', 0),
(8, 'test4', 'test småkage', '0.00', 0),
(9, 'test4', 'test småkage', '0.00', 0),
(10, 'test4', 'test småkage', '0.00', 0),
(17, 'Fedtebrød', 'Gammeldags småkage', '8.50', 200),
(18, 'Jødekager', '', '9.00', 250),
(19, 'Kokostoppe', 'Saftige', '12.00', 400),
(20, 'Havregrynskager', 'Sprøde', '14.00', 150),
(21, 'Chokoladecookies', 'Med chokolade', '20.00', 200),
(22, 'Biscotti', 'italiensk mandelsmåkage', '24.00', 400),
(23, 'Smørkager', 'Med ægte smør', '17.00', 200),
(24, 'Kanelkager', 'Med kanel', '10.00', 250),
(25, 'Mandelkager', 'Med rigtige mandler', '20.00', 300),
(26, 'Citronmåner', 'små udgaver som småkager', '25.00', 500),
(27, 'Sirupssmåkager', 'Ahorn sirup', '28.00', 300),
(28, 'Nøddesmåkager', 'med hasselnødder eller valnødder', '20.00', 250),
(29, 'Sprøde kaffekager', '', '8.50', 250),
(30, 'Lakridssmåkager', 'Lyder ulækkert', '12.00', 200),
(31, 'Marengskys', 'Meget søde', '8.50', 300),
(32, 'Chokolade småkager', '', '10.50', 200);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `products_media`
--

CREATE TABLE `products_media` (
  `products_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Data dump for tabellen `products_media`
--

INSERT INTO `products_media` (`products_id`, `media_id`) VALUES
(1, 1),
(1, 2),
(1, 2),
(4, 14),
(5, 15),
(5, 16),
(17, 17),
(17, 18),
(18, 19),
(19, 20),
(20, 21),
(21, 22),
(22, 13),
(23, 23),
(24, 24),
(25, 25),
(26, 26),
(27, 27),
(28, 28),
(31, 30);

--
-- Begrænsninger for dumpede tabeller
--

--
-- Indeks for tabel `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_customer` (`customer_id`);

--
-- Indeks for tabel `orders_products`
--
ALTER TABLE `orders_products`
  ADD KEY `fk_orders` (`orders_id`),
  ADD KEY `fk_products` (`products_id`);

--
-- Indeks for tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `products_media`
--
ALTER TABLE `products_media`
  ADD KEY `fk_media_id` (`media_id`),
  ADD KEY `fk_products_id` (`products_id`);

--
-- Brug ikke AUTO_INCREMENT for slettede tabeller
--

--
-- Tilføj AUTO_INCREMENT i tabel `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tilføj AUTO_INCREMENT i tabel `media`
--
ALTER TABLE `media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Tilføj AUTO_INCREMENT i tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tilføj AUTO_INCREMENT i tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Begrænsninger for dumpede tabeller
--

--
-- Begrænsninger for tabel `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Begrænsninger for tabel `orders_products`
--
ALTER TABLE `orders_products`
  ADD CONSTRAINT `fk_orders` FOREIGN KEY (`orders_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `fk_products` FOREIGN KEY (`products_id`) REFERENCES `products` (`id`);

--
-- Begrænsninger for tabel `products_media`
--
ALTER TABLE `products_media`
  ADD CONSTRAINT `fk_media_id` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`),
  ADD CONSTRAINT `fk_products_id` FOREIGN KEY (`products_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
