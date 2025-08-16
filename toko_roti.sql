-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 16, 2025 at 06:38 AM
-- Server version: 8.0.42
-- PHP Version: 8.3.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `toko_roti`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `icon_class` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `image_url`, `icon_class`) VALUES
(1, 'Roti Tawar', 'roti-tawar', NULL, NULL),
(2, 'Kue Kering', 'kue-kering', NULL, NULL),
(3, 'Kue Ulang Tahun', 'kue-ulang-tahun', NULL, NULL),
(9, 'Kue Lebaran', 'kue-lebaran', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Menunggu Pembayaran',
  `shipping_address` text,
  `shipping_method` varchar(100) DEFAULT NULL,
  `payment_method` varchar(100) DEFAULT NULL,
  `shipping_cost` decimal(10,2) DEFAULT NULL,
  `shipped_at` datetime DEFAULT NULL,
  `voucher_code` varchar(50) DEFAULT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `shipping_address`, `shipping_method`, `payment_method`, `shipping_cost`, `shipped_at`, `voucher_code`, `discount_amount`, `created_at`) VALUES
(1, 3, '24000.00', 'Selesai', 'Toko Riyan, Jalan Mangaan 2 Link XII Lorong Wisnu Ujung, Mabar, Medan Deli MEDAN DELI, KOTA MEDAN, SUMATERA UTARA, ID, 20242', 'Gosend Instant', 'Transfer Bank/E-Wallet', '18000.00', '2025-08-06 12:47:22', '0', '9000.00', '2025-07-30 07:42:11'),
(2, 3, '12000.00', 'Diproses', 'Toko Riyan, Jalan Mangaan 2 Link XII Lorong Wisnu Ujung, Mabar, Medan Deli MEDAN DELI, KOTA MEDAN, SUMATERA UTARA, ID, 20242', 'Ambil di Toko', 'Bayar di Toko', '0.00', '2025-08-06 13:08:03', NULL, '0.00', '2025-08-06 06:00:18'),
(3, 3, '95000.00', 'Diproses', 'Toko Riyan, Jalan Mangaan 2 Link XII Lorong Wisnu Ujung, Mabar, Medan Deli MEDAN DELI, KOTA MEDAN, SUMATERA UTARA, ID, 20242', 'Ambil di Toko', 'Transfer Bank/E-Wallet', '0.00', NULL, NULL, '0.00', '2025-08-15 08:13:48');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `customization_details` text,
  `has_reviewed` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=belum review, 1=sudah'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `customization_details`, `has_reviewed`) VALUES
(1, 1, 1, 1, '15000.00', '', 0),
(2, 2, 2, 1, '12000.00', '', 0),
(3, 3, 4, 1, '95000.00', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `payment_confirmations`
--

CREATE TABLE `payment_confirmations` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `account_holder` varchar(150) NOT NULL,
  `transfer_amount` decimal(10,2) NOT NULL,
  `transfer_date` date NOT NULL,
  `proof_image_url` varchar(255) NOT NULL,
  `status` enum('pending','verified') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int NOT NULL,
  `method_name` varchar(100) NOT NULL,
  `account_details` text NOT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `qris_image_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `method_name`, `account_details`, `logo_url`, `qris_image_url`, `is_active`) VALUES
(3, 'DANA', '081260426431 - Riyan Wardhana', 'logo-1752746187-logo.png', NULL, 1),
(5, 'QRIS', 'Silmarils', 'logo-1752740447-logo.png', 'qris-1752740447-qris.jpg', 1),
(6, 'BRI', '8162-1231-3155-78 | Riyan Wardhana', 'logo-1752741505-logo.png', NULL, 1),
(7, 'BNI', '8162-1231-3155-78 | Riyan Wardhana', 'logo-1752745429-logo.png', NULL, 1),
(8, 'Gopay', '081260426431 - Riyan Wardhana', 'logo-1752745834-gopay-seeklogo.png', NULL, 1),
(9, 'Bayar di Toko', 'Silakan lakukan pembayaran tunai saat Anda mengambil pesanan di toko kami.', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `category_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_customizable` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `stock`, `image_url`, `created_at`, `is_customizable`) VALUES
(1, 1, 'Roti Tawar Gandum', 'Roti tawar sehat dibuat dari gandum pilihan.', '15000.00', 36, 'roti-gandum.jpg', '2025-06-23 05:37:37', 0),
(2, 1, 'Roti Tawar Susu', 'Roti tawar lembut dengan rasa susu yang nikmat.', '12000.00', 37, 'roti-susu.jpg', '2025-06-23 05:37:37', 0),
(3, 2, 'Nastar Keju Premium', 'Kue nastar dengan isian nanas asli dan taburan keju cheddar.', '85000.00', 29, 'nastar.jpg', '2025-06-23 05:37:37', 0),
(4, 2, 'Kastengel Keju Edam', 'Kue keju renyah menggunakan keju edam asli.', '95000.00', 22, 'kastengel.jpg', '2025-06-23 05:37:37', 0),
(5, 3, 'Black Forest Cake', 'Kue ulang tahun klasik dengan cokelat melimpah dan buah ceri.', '250000.00', 10, 'black-forest.jpg', '2025-06-23 05:37:37', 0),
(6, 3, 'Red Velvet Cake', 'Kue red velvet dengan cream cheese frosting yang lezat.', '275000.00', 15, '6859692813f1d-red-velvet.jpg', '2025-06-23 05:37:37', 0),
(7, 1, 'Roti Sobek Coklat', 'Roti sobek isi coklat lumer, cocok untuk keluarga.', '25000.00', 33, 'roti-sobek.jpg', '2025-06-23 05:37:37', 0),
(8, 3, 'Kue Ulang Tahun', 'Kue Tahun Dijamin Enak mantap dan bergizi', '150000.00', 20, '685a3e3b673c2-kue ultah.png', '2025-06-24 05:57:15', 1);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `user_id` int NOT NULL,
  `order_id` int NOT NULL,
  `rating` tinyint(1) NOT NULL COMMENT 'Rating dari 1 sampai 5',
  `comment` text COLLATE utf8mb4_general_ci COMMENT 'Komentar ulasan dari pelanggan',
  `is_approved` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 = Menunggu Persetujuan, 1 = Disetujui',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'about_us_content', 'Kami adalah toko kue yang hadir dari rasa cinta terhadap aroma manis dan kehangatan momen bersama keluarga. Setiap kue yang kami buat bukan hanya tentang rasa, tetapi tentang cerita – cerita manis yang diciptakan dari bahan-bahan berkualitas, resep pilihan, dan sentuhan cinta dari tangan-tangan ahli. Mulai dari kue ulang tahun, roti harian, hingga pastry istimewa, semua dibuat dengan sepenuh hati untuk membawa kebahagiaan dalam setiap gigitannya.\r\n\r\nKami percaya bahwa kue bukan sekadar makanan, melainkan bagian dari momen berharga dalam hidup Anda. Itulah mengapa kami selalu berinovasi dan menjaga kualitas untuk memastikan setiap pelanggan merasakan pengalaman yang tak terlupakan. Yuk, datang dan ciptakan momen manis Anda bersama kami – karena hidup ini terlalu singkat untuk tidak menikmati kue yang lezat!\r\n\r\nNomor Wa: 081260426431'),
(4, 'website_title', 'Silmarils Cookies Dessert'),
(5, 'website_favicon', 'site-website_favicon-1751296505.png'),
(6, 'navbar_brand_type', 'logo'),
(7, 'navbar_brand_text', 'Toko Roti'),
(8, 'navbar_brand_logo', 'site-navbar_brand_logo-1751296505.png'),
(62, 'contact_address', 'Jl. Amal No.19d, Sunggal, Kec. Medan Sunggal, Kota Medan, Sumatera Utara 20127'),
(63, 'contact_phone', '0823-6319-8655'),
(64, 'contact_email', ''),
(65, 'social_facebook', ''),
(66, 'social_instagram', 'https://www.instagram.com/silmarils_shop/'),
(67, 'social_whatsapp', 'https://wa.me/082363198655');

-- --------------------------------------------------------

--
-- Table structure for table `sliders`
--

CREATE TABLE `sliders` (
  `id` int NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `button_text` varchar(100) DEFAULT NULL,
  `button_link` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `display_order` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sliders`
--

INSERT INTO `sliders` (`id`, `image_url`, `button_text`, `button_link`, `is_active`, `display_order`) VALUES
(3, '6862a8aaf2eaf-banner 1.png', 'Cek Sekarang', 'http://localhost/toko-roti/produk', 1, 0),
(6, '6879fd8abdfa4-Coklat dan Putih Ilustrasi Roti Promo Banner.png', 'Cek Sekarang', 'http://localhost/toko-roti/produk', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `role` enum('customer','admin','pegawai') NOT NULL DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `role`, `created_at`) VALUES
(3, 'Riyan Wardhana', 'riyanwardhana2@gmail.com', '$2y$10$dbxPZ2y1Sea6UM5uSwvReuQbesvz.mG1YUnmfCAZNw/saSk6eQWRK', '081260426431', 'Toko Riyan, Jalan Mangaan 2 Link XII Lorong Wisnu Ujung, Mabar, Medan Deli MEDAN DELI, KOTA MEDAN, SUMATERA UTARA, ID, 20242', 'admin', '2025-06-23 08:31:15'),
(7, 'Riyan Wkwk', 'riyanwardhana55@gmail.com', '$2y$10$4XkuCk1l5bdd4SzCwy39XOBrlzkE7SeIjg2oyiiv87bSG.BANn.le', '081260426431', 'Jl. Mangaan III Ps. II 167, M A B A R, Kec. Medan Deli, Kota Medan, Sumatera Utara 20241', 'pegawai', '2025-08-06 07:30:51'),
(8, 'Silmar', 'silmarils2025@gmail.com', '$2y$10$MXUvMdmZG2UnIu9sWj8YVeOIs3bZPUq62Uj2iSXIv6e6O.q4YRx1u', '081260426431', 'Jalan Kenangan', 'customer', '2025-08-15 08:16:49');

-- --------------------------------------------------------

--
-- Table structure for table `vouchers`
--

CREATE TABLE `vouchers` (
  `id` int NOT NULL,
  `code` varchar(50) NOT NULL,
  `type` enum('percentage','fixed') NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `min_purchase` decimal(10,2) DEFAULT '0.00',
  `usage_limit` int DEFAULT '1',
  `usage_count` int DEFAULT '0',
  `expires_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `payment_confirmations`
--
ALTER TABLE `payment_confirmations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `sliders`
--
ALTER TABLE `sliders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payment_confirmations`
--
ALTER TABLE `payment_confirmations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `sliders`
--
ALTER TABLE `sliders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `payment_confirmations`
--
ALTER TABLE `payment_confirmations`
  ADD CONSTRAINT `payment_confirmations_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
