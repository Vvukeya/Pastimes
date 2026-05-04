-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 02, 2026 at 07:15 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ClothingStore`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblAdmin`
--

CREATE TABLE `tblAdmin` (
  `admin_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `permissions` text DEFAULT NULL,
  `last_action` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblAdmin`
--

INSERT INTO `tblAdmin` (`admin_id`, `user_id`, `permissions`, `last_action`) VALUES
(1, 1, 'full_access', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblAorder`
--

CREATE TABLE `tblAorder` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `delivery_address` text NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `tracking_number` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblAorder`
--

INSERT INTO `tblAorder` (`order_id`, `user_id`, `order_number`, `total_amount`, `delivery_address`, `status`, `payment_method`, `payment_status`, `tracking_number`, `created_at`) VALUES
(1, 12, 'ORD-20260415-B82733', 850.00, '2558 Mikosi Parklane street\r\nChiawelo\r\n1818', 'pending', 'cash_on_delivery', 'pending', NULL, '2026-04-15 14:04:11');

-- --------------------------------------------------------

--
-- Table structure for table `tblCart`
--

CREATE TABLE `tblCart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblCart`
--

INSERT INTO `tblCart` (`cart_id`, `user_id`, `product_id`, `quantity`, `created_at`) VALUES
(3, 12, 1, 1, '2026-04-16 08:09:16'),
(4, 1, 34, 1, '2026-05-01 21:09:21');

-- --------------------------------------------------------

--
-- Table structure for table `tblClothes`
--

CREATE TABLE `tblClothes` (
  `product_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `condition` enum('New','Like New','Good','Fair') DEFAULT 'Good',
  `category` varchar(50) DEFAULT NULL,
  `size` varchar(20) DEFAULT NULL,
  `colour` varchar(30) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `status` enum('pending','approved','rejected','sold') DEFAULT 'pending',
  `views` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sold_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblClothes`
--

INSERT INTO `tblClothes` (`product_id`, `seller_id`, `title`, `brand`, `description`, `price`, `condition`, `category`, `size`, `colour`, `image_url`, `status`, `views`, `created_at`, `sold_date`) VALUES
(1, 2, 'Vintage Levi\'s 501 Jeans', 'Levi\'s', 'Classic vintage jeans in excellent condition. Original button fly.', 450.00, 'Like New', 'Jeans', '32', 'Blue', 'images/products/levis-jeans.jpg', 'approved', 6, '2026-04-15 11:09:43', NULL),
(2, 3, 'Nike Air Max 90', 'Nike', 'Limited edition Air Max trainers, barely worn, original box included.', 850.00, 'Like New', 'Shoes', '9', 'White/Red', 'images/products/nike-airmax.jpg', 'approved', 7, '2026-04-15 11:09:43', NULL),
(3, 4, 'Zara Wool Blend Coat', 'Zara', 'Elegant winter coat, perfect for formal occasions.', 650.00, 'Like New', 'Coats', 'M', 'Beige', 'images/products/zara-coat.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(4, 5, 'Adidas Originals Hoodie', 'Adidas', 'Classic trefoil hoodie, warm and comfortable.', 380.00, 'Good', 'Hoodies', 'L', 'Black', 'images/products/adidas-hoodie.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(5, 6, 'H&M Designer Dress', 'H&M', 'Limited edition designer collaboration dress.', 450.00, 'New', 'Dresses', 'S', 'Green', 'images/products/hm-dress.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(6, 7, 'Ray-Ban Wayfarer', 'Ray-Ban', 'Authentic Ray-Ban Wayfarer sunglasses with case.', 890.00, 'Good', 'Accessories', 'One Size', 'Black', 'images/products/rayban.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(7, 2, 'Calvin Klein T-Shirt', 'Calvin Klein', 'Classic white t-shirt, never worn with tags.', 250.00, 'New', 'T-Shirts', 'M', 'White', 'images/products/ck-tshirt.jpg', 'approved', 3, '2026-04-15 11:09:43', NULL),
(8, 4, 'Tommy Hilfiger Polo', 'Tommy Hilfiger', 'Preppy style polo shirt, great condition.', 320.00, 'Like New', 'Polos', 'L', 'Navy', 'images/products/tommy-polo.jpg', 'approved', 0, '2026-04-15 11:09:43', NULL),
(9, 6, 'Guess Leather Jacket', 'Guess', 'Genuine leather biker jacket, timeless style.', 1200.00, 'Good', 'Jackets', 'M', 'Brown', 'images/products/guess-jacket.jpg', 'approved', 0, '2026-04-15 11:09:43', NULL),
(10, 8, 'Puma Training Pants', 'Puma', 'Comfortable training pants for gym or casual wear.', 1500.00, 'New', 'Pants', 'L', 'Grey', 'images/products/puma-pants.jpg', 'pending', 0, '2026-04-15 11:09:43', NULL),
(11, 3, 'Forever 21 Summer Dress', 'Forever 21', 'Beautiful floral summer dress, worn twice.', 180.00, 'Good', 'Dresses', 'M', 'Floral', 'images/products/f21-dress.jpg', 'approved', 2, '2026-04-15 11:09:43', NULL),
(12, 5, 'Converse Chuck Taylor', 'Converse', 'Classic high-top sneakers, iconic style.', 390.00, 'Good', 'Shoes', '7', 'Black', 'images/products/converse.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(13, 7, 'North Face Puffer', 'North Face', 'Warm winter puffer jacket, perfect for cold weather.', 950.00, 'Like New', 'Jackets', 'XL', 'Black', 'images/products/northface.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(14, 9, 'Vans Old Skool', 'Vans', 'Classic skate shoes, barely used.', 450.00, 'Like New', 'Shoes', '8', 'Black/White', 'images/products/vans.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(15, 2, 'Superdry Windbreaker', 'Superdry', 'Stylish windbreaker jacket with hood.', 550.00, 'Good', 'Jackets', 'L', 'Red', 'images/products/superdry.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(16, 4, 'Ralph Lauren Sweater', 'Ralph Lauren', 'Cashmere blend sweater, luxury feel.', 680.00, 'Like New', 'Sweaters', 'M', 'Navy', 'images/products/ralph-lauren.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(17, 6, 'Under Armour Gym Bag', 'Under Armour', 'Durable gym backpack, multiple compartments.', 320.00, 'Good', 'Bags', 'One Size', 'Black', 'images/products/underarmour-bag.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(18, 8, 'GAP Khaki Pants', 'GAP', 'Classic fit chinos, perfect for office.', 290.00, 'Like New', 'Pants', '34', 'Khaki', 'images/products/gap-khaki.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(19, 3, 'New Era Cap', 'New Era', 'Baseball cap, adjustable fit.', 180.00, 'New', 'Accessories', 'One Size', 'Black', 'images/products/newera-cap.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(20, 5, 'Levi\'s Denim Jacket', 'Levi\'s', 'Classic trucker jacket, vintage wash.', 780.00, 'Good', 'Jackets', 'M', 'Blue', 'images/products/levis-jacket.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(21, 7, 'ASOS Design Shirt', 'ASOS', 'Casual button-down shirt, slim fit.', 220.00, 'Like New', 'Shirts', 'M', 'Pink', 'images/products/asos-shirt.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(22, 9, 'BOSS Suit Jacket', 'BOSS', 'Formal suit jacket, excellent condition.', 1500.00, 'Like New', 'Suits', '50', 'Charcoal', 'images/products/boss-jacket.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(23, 2, 'Lacoste Polo', 'Lacoste', 'Classic crocodile logo polo shirt.', 450.00, 'Good', 'Polos', 'L', 'White', 'images/products/lacoste-polo.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(24, 4, 'Fila Disruptors', 'Fila', 'Chunky sneakers, trendy style.', 550.00, 'Like New', 'Shoes', '9', 'White', 'images/products/fila.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(25, 6, 'Jack & Jones Jeans', 'Jack & Jones', 'Slim fit jeans, stretch comfort.', 340.00, 'Good', 'Jeans', '32', 'Black', 'images/products/jackjones-jeans.jpg', 'approved', 2, '2026-04-15 11:09:43', NULL),
(26, 8, 'Esprit Blouse', 'Esprit', 'Elegant work blouse, professional look.', 260.00, 'Like New', 'Blouses', 'S', 'White', 'images/products/esprit-blouse.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(27, 3, 'Columbia Fleece', 'Columbia', 'Warm fleece jacket, great for layering.', 420.00, 'Good', 'Jackets', 'XL', 'Blue', 'images/products/columbia-fleece.jpg', 'approved', 2, '2026-04-15 11:09:43', NULL),
(28, 5, 'Mango Trousers', 'Mango', 'Tailored trousers, office ready.', 310.00, 'Like New', 'Pants', '8', 'Black', 'images/products/mango-trousers.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(29, 7, 'Skechers Slip-ons', 'Skechers', 'Comfort walking shoes, memory foam.', 480.00, 'Good', 'Shoes', '10', 'Black', 'images/products/skechers.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(30, 4, 'River Island Jumpsuit', 'River Island', 'Summer jumpsuit, worn twice.', 350.00, 'Good', 'Jumpsuits', 'M', 'Floral', NULL, 'pending', 0, '2026-04-15 11:09:43', NULL),
(31, 6, 'Bershka Cargo Pants', 'Bershka', 'Trendy cargo pants with pockets.', 290.00, 'Like New', 'Pants', 'S', 'Olive', NULL, 'pending', 0, '2026-04-15 11:09:43', NULL),
(32, 8, 'Stradivarius Top', 'Stradivarius', 'Lace detail top, party ready.', 150.00, 'Good', 'Tops', 'XS', 'White', NULL, 'pending', 0, '2026-04-15 11:09:43', NULL),
(34, 12, 'Nike Air Force 1 Sneakers', 'Nike', 'Gently used white Nike Air Force 1 sneakers. Still in very good condition with minimal creasing. Comfortable and stylish for everyday wear.', 1800.00, 'Like New', 'Shoes', '7', 'White and Red', 'uploads/1776298205_69e028dd68a7c.jpg', 'approved', 8, '2026-04-16 00:10:05', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblMessages`
--

CREATE TABLE `tblMessages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `message_text` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblMessages`
--

INSERT INTO `tblMessages` (`message_id`, `sender_id`, `receiver_id`, `product_id`, `message_text`, `is_read`, `created_at`) VALUES
(1, 12, 3, 2, 'do you mind showing  me more pictures of this product', 0, '2026-04-15 14:00:29'),
(2, 12, 2, 1, 'hi', 0, '2026-04-15 14:05:58'),
(3, 3, 12, 2, 'ok no problem', 0, '2026-04-15 15:28:29');

-- --------------------------------------------------------

--
-- Table structure for table `tblOrderItems`
--

CREATE TABLE `tblOrderItems` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblOrderItems`
--

INSERT INTO `tblOrderItems` (`item_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 2, 1, 850.00);

-- --------------------------------------------------------

--
-- Table structure for table `tblUser`
--

CREATE TABLE `tblUser` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `delivery_address` text DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `is_seller_verified` tinyint(1) DEFAULT 0,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tblUser`
--

INSERT INTO `tblUser` (`user_id`, `name`, `surname`, `email`, `username`, `password_hash`, `phone`, `delivery_address`, `is_verified`, `is_seller_verified`, `role`, `created_at`, `last_login`) VALUES
(1, 'Admin', 'User', 'admin@pastimes.com', 'admin', '0192023a7bbd73250516f069df18b500', NULL, NULL, 1, 1, 'admin', '2026-04-15 11:09:43', '2026-05-01 21:08:29'),
(2, 'John', 'Smith', 'john.smith@email.com', 'johnsmith', '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, 1, 1, 'user', '2026-04-15 11:09:43', '2026-04-15 14:21:45'),
(3, 'Sarah', 'Johnson', 'sarah.j@email.com', 'sarahj', '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, 1, 1, 'user', '2026-04-15 11:09:43', '2026-04-15 15:27:56'),
(4, 'Michael', 'Brown', 'michael.b@email.com', 'mbrown', '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, 1, 0, 'user', '2026-04-15 11:09:43', NULL),
(5, 'Emily', 'Davis', 'emily.d@email.com', 'emilyd', '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, 1, 1, 'user', '2026-04-15 11:09:43', NULL),
(6, 'David', 'Wilson', 'david.w@email.com', 'dwilson', '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, 1, 0, 'user', '2026-04-15 11:09:43', NULL),
(7, 'Lisa', 'Martinez', 'lisa.m@email.com', 'lisam', '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, 1, 1, 'user', '2026-04-15 11:09:43', NULL),
(8, 'James', 'Taylor', 'james.t@email.com', 'jamest', '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, 1, 0, 'user', '2026-04-15 11:09:43', NULL),
(9, 'Maria', 'Anderson', 'maria.a@email.com', 'mariaa', '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, 1, 1, 'user', '2026-04-15 11:09:43', NULL),
(10, 'Robert', 'Thomas', 'robert.t@email.com', 'robertt', '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, 1, 0, 'user', '2026-04-15 11:09:43', NULL),
(11, 'Jennifer', 'Jackson', 'jennifer.j@email.com', 'jenj', '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, 1, 1, 'user', '2026-04-15 11:09:43', NULL),
(12, 'Vutivi', 'Vukeya', 'vutivivukeya544@gmail.com', 'vvukeya', 'f5a0652d89ceb53b00b222fbd2a3b286', '0677183135', '2558 Mikosi parklane street\r\nChiawelo\r\n1818', 1, 1, 'user', '2026-04-15 11:15:42', '2026-05-01 13:12:36'),
(13, 'sophie', 'vukeya', 'ST10445789@rcconnect.edu.za', 'sophie62', 'c17add984c219479d46ebb66154e326c', '0838131081', NULL, 0, 0, 'user', '2026-04-17 22:37:12', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblAdmin`
--
ALTER TABLE `tblAdmin`
  ADD PRIMARY KEY (`admin_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tblAorder`
--
ALTER TABLE `tblAorder`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_order_number` (`order_number`);

--
-- Indexes for table `tblCart`
--
ALTER TABLE `tblCart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `tblClothes`
--
ALTER TABLE `tblClothes`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_brand` (`brand`);

--
-- Indexes for table `tblMessages`
--
ALTER TABLE `tblMessages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `tblOrderItems`
--
ALTER TABLE `tblOrderItems`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `tblUser`
--
ALTER TABLE `tblUser`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblAdmin`
--
ALTER TABLE `tblAdmin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblAorder`
--
ALTER TABLE `tblAorder`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblCart`
--
ALTER TABLE `tblCart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tblClothes`
--
ALTER TABLE `tblClothes`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `tblMessages`
--
ALTER TABLE `tblMessages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tblOrderItems`
--
ALTER TABLE `tblOrderItems`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblUser`
--
ALTER TABLE `tblUser`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblAdmin`
--
ALTER TABLE `tblAdmin`
  ADD CONSTRAINT `tbladmin_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tblUser` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `tblAorder`
--
ALTER TABLE `tblAorder`
  ADD CONSTRAINT `tblaorder_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tblUser` (`user_id`);

--
-- Constraints for table `tblCart`
--
ALTER TABLE `tblCart`
  ADD CONSTRAINT `tblcart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tblUser` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tblcart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `tblClothes` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `tblClothes`
--
ALTER TABLE `tblClothes`
  ADD CONSTRAINT `tblclothes_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `tblUser` (`user_id`);

--
-- Constraints for table `tblMessages`
--
ALTER TABLE `tblMessages`
  ADD CONSTRAINT `tblmessages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `tblUser` (`user_id`),
  ADD CONSTRAINT `tblmessages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `tblUser` (`user_id`),
  ADD CONSTRAINT `tblmessages_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `tblClothes` (`product_id`);

--
-- Constraints for table `tblOrderItems`
--
ALTER TABLE `tblOrderItems`
  ADD CONSTRAINT `tblorderitems_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `tblAorder` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tblorderitems_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `tblClothes` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
