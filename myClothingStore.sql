-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 18, 2026 at 06:47 PM
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
-- Table structure for table `tblAdminReplies`
--

CREATE TABLE `tblAdminReplies` (
  `reply_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `reply_text` text DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblAdminReplies`
--

INSERT INTO `tblAdminReplies` (`reply_id`, `message_id`, `admin_id`, `reply_text`, `file_name`, `file_path`, `file_type`, `file_size`, `created_at`) VALUES
(1, 6, 1, 'hi guys', NULL, NULL, NULL, NULL, '2026-05-26 15:39:13'),
(2, 6, 1, 'hi vutivi', NULL, NULL, NULL, NULL, '2026-06-17 19:58:21'),
(3, 9, 1, 'please share on time', NULL, NULL, NULL, NULL, '2026-06-17 23:16:39'),
(4, 13, 1, 'hi user', NULL, NULL, NULL, NULL, '2026-06-18 14:52:01'),
(5, 16, 1, 'Hi guys', NULL, NULL, NULL, NULL, '2026-06-18 15:38:36');

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
  `coupon_id` int(11) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `points_used` int(11) DEFAULT 0,
  `points_earned` int(11) DEFAULT 0,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `tracking_number` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblAorder`
--

INSERT INTO `tblAorder` (`order_id`, `user_id`, `order_number`, `total_amount`, `delivery_address`, `coupon_id`, `discount_amount`, `points_used`, `points_earned`, `status`, `payment_method`, `payment_status`, `tracking_number`, `created_at`) VALUES
(1, 12, 'ORD-20260415-B82733', 850.00, '2558 Mikosi Parklane street\r\nChiawelo\r\n1818', NULL, 0.00, 0, 0, 'pending', 'cash_on_delivery', 'pending', NULL, '2026-04-15 14:04:11'),
(2, 12, 'ORD-20260503-15C004', 1100.00, '56 John street,\r\nsoweto', NULL, 0.00, 0, 0, 'delivered', 'cash_on_delivery', 'pending', NULL, '2026-05-03 01:57:21'),
(3, 12, 'ORD-20260504-F22BFC', 450.00, '2558 Mikosi street', NULL, 0.00, 0, 0, 'shipped', 'cash_on_delivery', 'pending', NULL, '2026-05-04 12:24:31'),
(4, 12, 'ORD-20260504-7C7BF5', 320.00, '56 John  street', NULL, 0.00, 0, 0, 'processing', 'cash_on_delivery', 'pending', NULL, '2026-05-04 12:42:31'),
(5, 12, 'ORD-20260504-6727B2', 850.00, '26 John street', NULL, 0.00, 0, 0, 'processing', 'cash_on_delivery', 'pending', NULL, '2026-05-04 14:56:54'),
(6, 12, 'ORD-20260618-B5AA8B', 1140.00, '2558 parklane', NULL, 0.00, 0, 0, 'shipped', 'card', 'pending', NULL, '2026-06-17 22:50:03'),
(7, 12, 'ORD-20260618-7E6D09', 1590.00, '56 Jorissen', NULL, 0.00, 0, 0, 'processing', 'bank_transfer', 'pending', NULL, '2026-06-17 23:01:11'),
(8, 16, 'ORD-20260618-0BAACD', 1800.00, '56 Jorissen str', NULL, 0.00, 0, 0, 'delivered', 'card', 'pending', NULL, '2026-06-17 23:12:16'),
(9, 16, 'ORD-20260618-4127C6', 260.00, 'John str', NULL, 0.00, 0, 0, 'delivered', 'cash_on_delivery', 'pending', NULL, '2026-06-18 00:12:04'),
(10, 16, 'ORD-20260618-C64B00', 630.00, 'Helen Joseph street', NULL, 0.00, 0, 0, 'shipped', 'cash_on_delivery', 'pending', NULL, '2026-06-18 02:05:32'),
(11, 19, 'ORD-20260618-F2BC2D', 2388.00, '56 Jorissen str', NULL, 0.00, 0, 0, 'processing', 'cash_on_delivery', 'pending', NULL, '2026-06-18 14:04:31'),
(12, 20, 'ORD-20260618-C68796', 1848.00, '56 Jorissen str, braam', NULL, 0.00, 0, 0, 'processing', 'cash_on_delivery', 'pending', NULL, '2026-06-18 15:27:24');

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
(9, 15, 34, 1, '2026-05-04 21:12:57'),
(10, 14, 34, 1, '2026-05-05 02:04:09'),
(26, 12, 29, 1, '2026-06-18 14:48:42');

-- --------------------------------------------------------

--
-- Table structure for table `tblChatConversations`
--

CREATE TABLE `tblChatConversations` (
  `conversation_id` int(11) NOT NULL,
  `user1_id` int(11) NOT NULL,
  `user2_id` int(11) NOT NULL,
  `last_message` text DEFAULT NULL,
  `last_message_time` timestamp NULL DEFAULT NULL,
  `is_admin_conversation` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `quantity` int(11) NOT NULL DEFAULT 1,
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

INSERT INTO `tblClothes` (`product_id`, `seller_id`, `title`, `brand`, `description`, `price`, `quantity`, `condition`, `category`, `size`, `colour`, `image_url`, `status`, `views`, `created_at`, `sold_date`) VALUES
(1, 2, 'Vintage Levi\'s 501 Jeans', 'Levi\'s', 'Classic vintage jeans in excellent condition. Original button fly.', 450.00, 1, 'Like New', 'Jeans', '32', 'Blue', 'images/products/levis-jeans.jpg', 'approved', 12, '2026-04-15 11:09:43', NULL),
(2, 3, 'Nike Air Max 90', 'Nike', 'Limited edition Air Max trainers, barely worn, original box included.', 850.00, 1, 'Like New', 'Shoes', '9', 'White/Red', 'images/products/nike-airmax.jpg', 'approved', 11, '2026-04-15 11:09:43', NULL),
(3, 4, 'Zara Wool Blend Coat', 'Zara', 'Elegant winter coat, perfect for formal occasions.', 650.00, 1, 'Like New', 'Coats', 'M', 'Beige', 'images/products/zara-coat.jpg', 'approved', 2, '2026-04-15 11:09:43', NULL),
(4, 5, 'Adidas Originals Hoodie', 'Adidas', 'Classic trefoil hoodie, warm and comfortable.', 380.00, 1, 'Good', 'Hoodies', 'L', 'Black', 'images/products/adidas-hoodie.jpg', 'approved', 3, '2026-04-15 11:09:43', NULL),
(5, 6, 'H&M Designer Dress', 'H&M', 'Limited edition designer collaboration dress.', 450.00, 1, 'New', 'Dresses', 'S', 'Green', 'images/products/hm-dress.jpg', 'approved', 2, '2026-04-15 11:09:43', NULL),
(6, 7, 'Ray-Ban Wayfarer', 'Ray-Ban', 'Authentic Ray-Ban Wayfarer sunglasses with case.', 890.00, 1, 'Good', 'Accessories', 'One Size', 'Black', 'images/products/rayban.jpg', 'approved', 3, '2026-04-15 11:09:43', NULL),
(7, 2, 'Calvin Klein T-Shirt', 'Calvin Klein', 'Classic white t-shirt, never worn with tags.', 250.00, 0, 'New', 'T-Shirts', 'M', 'White', 'images/products/ck-tshirt.jpg', 'sold', 5, '2026-04-15 11:09:43', '2026-06-18 15:27:24'),
(8, 4, 'Tommy Hilfiger Polo', 'Tommy Hilfiger', 'Preppy style polo shirt, great condition.', 320.00, 1, 'Like New', 'Polos', 'L', 'Navy', 'images/products/tommy-polo.jpg', 'approved', 0, '2026-04-15 11:09:43', NULL),
(9, 6, 'Guess Leather Jacket', 'Guess', 'Genuine leather biker jacket, timeless style.', 1200.00, 1, 'Good', 'Jackets', 'M', 'Brown', 'images/products/guess-jacket.jpg', 'approved', 0, '2026-04-15 11:09:43', NULL),
(10, 8, 'Puma Training Pants', 'Puma', 'Comfortable training pants for gym or casual wear.', 1500.00, 1, 'New', 'Pants', 'L', 'Grey', 'images/products/puma-pants.jpg', 'pending', 0, '2026-04-15 11:09:43', NULL),
(11, 3, 'Forever 21 Summer Dress', 'Forever 21', 'Beautiful floral summer dress, worn twice.', 180.00, 0, 'Good', 'Dresses', 'M', 'Floral', 'images/products/f21-dress.jpg', 'sold', 2, '2026-04-15 11:09:43', '2026-06-18 02:05:32'),
(12, 5, 'Converse Chuck Taylor', 'Converse', 'Classic high-top sneakers, iconic style.', 390.00, 0, 'Good', 'Shoes', '7', 'Black', 'images/products/converse.jpg', 'sold', 1, '2026-04-15 11:09:43', '2026-06-18 14:04:31'),
(13, 7, 'North Face Puffer', 'North Face', 'Warm winter puffer jacket, perfect for cold weather.', 950.00, 1, 'Like New', 'Jackets', 'XL', 'Black', 'images/products/northface.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(14, 9, 'Vans Old Skool', 'Vans', 'Classic skate shoes, barely used.', 450.00, 0, 'Like New', 'Shoes', '8', 'Black/White', 'images/products/vans.jpg', 'sold', 1, '2026-04-15 11:09:43', '2026-06-18 02:05:32'),
(15, 2, 'Superdry Windbreaker', 'Superdry', 'Stylish windbreaker jacket with hood.', 550.00, 1, 'Good', 'Jackets', 'L', 'Red', 'images/products/superdry.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(16, 4, 'Ralph Lauren Sweater', 'Ralph Lauren', 'Cashmere blend sweater, luxury feel.', 680.00, 1, 'Like New', 'Sweaters', 'M', 'Navy', 'images/products/ralph-lauren.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(17, 6, 'Under Armour Gym Bag', 'Under Armour', 'Durable gym backpack, multiple compartments.', 320.00, 1, 'Good', 'Bags', 'One Size', 'Black', 'images/products/underarmour-bag.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(18, 8, 'GAP Khaki Pants', 'GAP', 'Classic fit chinos, perfect for office.', 290.00, 1, 'Like New', 'Pants', '34', 'Khaki', 'images/products/gap-khaki.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(19, 3, 'New Era Cap', 'New Era', 'Baseball cap, adjustable fit.', 180.00, 1, 'New', 'Accessories', 'One Size', 'Black', 'images/products/newera-cap.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(20, 5, 'Levi\'s Denim Jacket', 'Levi\'s', 'Classic trucker jacket, vintage wash.', 780.00, 1, 'Good', 'Jackets', 'M', 'Blue', 'images/products/levis-jacket.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(21, 7, 'ASOS Design Shirt', 'ASOS', 'Casual button-down shirt, slim fit.', 220.00, 1, 'Like New', 'Shirts', 'M', 'Pink', 'images/products/asos-shirt.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(22, 9, 'BOSS Suit Jacket', 'BOSS', 'Formal suit jacket, excellent condition.', 1500.00, 1, 'Like New', 'Suits', '50', 'Charcoal', 'images/products/boss-jacket.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(23, 2, 'Lacoste Polo', 'Lacoste', 'Classic crocodile logo polo shirt.', 450.00, 1, 'Good', 'Polos', 'L', 'White', 'images/products/lacoste-polo.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(24, 4, 'Fila Disruptors', 'Fila', 'Chunky sneakers, trendy style.', 550.00, 1, 'Like New', 'Shoes', '9', 'White', 'images/products/fila.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(25, 6, 'Jack & Jones Jeans', 'Jack & Jones', 'Slim fit jeans, stretch comfort.', 340.00, 1, 'Good', 'Jeans', '32', 'Black', 'images/products/jackjones-jeans.jpg', 'approved', 2, '2026-04-15 11:09:43', NULL),
(26, 8, 'Esprit Blouse', 'Esprit', 'Elegant work blouse, professional look.', 260.00, 1, 'Like New', 'Blouses', 'S', 'White', 'images/products/esprit-blouse.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(27, 3, 'Columbia Fleece', 'Columbia', 'Warm fleece jacket, great for layering.', 420.00, 1, 'Good', 'Jackets', 'XL', 'Blue', 'images/products/columbia-fleece.jpg', 'approved', 2, '2026-04-15 11:09:43', NULL),
(28, 5, 'Mango Trousers', 'Mango', 'Tailored trousers, office ready.', 310.00, 1, 'Like New', 'Pants', '8', 'Black', 'images/products/mango-trousers.jpg', 'approved', 1, '2026-04-15 11:09:43', NULL),
(29, 7, 'Skechers Slip-ons', 'Skechers', 'Comfort walking shoes, memory foam.', 480.00, 1, 'Good', 'Shoes', '10', 'Black', 'images/products/skechers.jpg', 'approved', 2, '2026-04-15 11:09:43', NULL),
(30, 4, 'River Island Jumpsuit', 'River Island', 'Summer jumpsuit, worn twice.', 350.00, 1, 'Good', 'Jumpsuits', 'M', 'Floral', NULL, 'pending', 0, '2026-04-15 11:09:43', NULL),
(31, 6, 'Bershka Cargo Pants', 'Bershka', 'Trendy cargo pants with pockets.', 290.00, 1, 'Like New', 'Pants', 'S', 'Olive', NULL, 'pending', 0, '2026-04-15 11:09:43', NULL),
(32, 8, 'Stradivarius Top', 'Stradivarius', 'Lace detail top, party ready.', 150.00, 1, 'Good', 'Tops', 'XS', 'White', NULL, 'pending', 0, '2026-04-15 11:09:43', NULL),
(34, 12, 'Nike Air Force 1 Sneakers', 'Nike', 'Gently used white Nike Air Force 1 sneakers. Still in very good condition with minimal creasing. Comfortable and stylish for everyday wear.', 1800.00, 1, 'Like New', 'Shoes', '7', 'White and Red', 'uploads/1776298205_69e028dd68a7c.jpg', 'approved', 54, '2026-04-16 00:10:05', NULL),
(36, 17, 'Y2k Super Baggy Jean', 'cotton on', 'A pair of relaxed-rise jeans with an extra baggy leg and straight hem. Crafted from rigid denim with a gathered waistband and drawcord for adjustable comfort. Finished with classic five-pocket styling and a mid-wash grey tone.', 999.00, 0, 'New', 'Jeans', '30,32', 'black', 'uploads/1781784179_6a33de737ce8d.jpg', 'sold', 3, '2026-06-18 12:02:59', '2026-06-18 14:04:31'),
(38, 12, 'QQWERT', 'NNN', 'DFGHJK', 988.00, 1, 'New', 'Dresses', 'M', 'VBN', 'uploads/1781793031_6a34010724713.png', 'pending', 1, '2026-06-18 14:30:31', NULL),
(39, 12, 'Half Half Panelled Crew', 'Half Half', 'Relaxed fits, real attitude. The Half Half Panelled Crew brings soft brushed fleece with a loose, slouchy build that&#039;s built for repeat wear. Graphic panelling adds visual weight without the noise. Rib cuffs and hem band keep the shape locked in. The kind of piece you&#039;ll keep in regular rotation. Weekend plans: fleece on repeat. Jeans, shorts or a skirt you&#039;re sorted.', 799.00, 0, 'New', 'Jackets', 'M', 'hh mud/panel', 'uploads/1781794060_6a34050c7530d.jpg', 'sold', 3, '2026-06-18 14:47:40', '2026-06-18 15:27:24'),
(40, 12, 'Notion Zip Thru Hoodie', 'Notion', 'Ready to roll. Always. The Notion Zip Thru Hoodie brings that oversized frame and full-coverage hood with a soft, brushed fleece feel that keeps you comfortable all day. Zip through design with stretchy rib cuffs and hem for easy layering. Graphic print adds edge without trying too hard. Soft feel, strong energy. Layer it over a vintage tee or wear it with baggy denim.', 699.00, 1, 'New', 'Jackets', 'S', 'ntn washed black/ntn logo', 'uploads/1781796806_6a340fc63ddd8.jpg', 'approved', 3, '2026-06-18 15:33:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblComments`
--

CREATE TABLE `tblComments` (
  `comment_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `rating` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblCoupons`
--

CREATE TABLE `tblCoupons` (
  `coupon_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_type` enum('percentage','fixed') DEFAULT 'percentage',
  `discount_value` decimal(10,2) NOT NULL,
  `min_order` decimal(10,2) DEFAULT 0.00,
  `max_discount` decimal(10,2) DEFAULT NULL,
  `valid_from` date NOT NULL,
  `valid_to` date NOT NULL,
  `usage_limit` int(11) DEFAULT 1,
  `used_count` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblCoupons`
--

INSERT INTO `tblCoupons` (`coupon_id`, `code`, `discount_type`, `discount_value`, `min_order`, `max_discount`, `valid_from`, `valid_to`, `usage_limit`, `used_count`, `is_active`, `created_at`) VALUES
(1, 'SAVE10', 'percentage', 10.00, 500.00, NULL, '2026-05-05', '2026-06-04', 100, 0, 1, '2026-05-05 03:22:01'),
(2, 'SAVE50', 'fixed', 50.00, 300.00, NULL, '2026-05-05', '2026-06-04', 50, 0, 1, '2026-05-05 03:22:01'),
(3, 'FIRST20', 'percentage', 20.00, 0.00, NULL, '2026-05-05', '2026-06-04', 1, 0, 1, '2026-05-05 03:22:01');

-- --------------------------------------------------------

--
-- Table structure for table `tblFlashSales`
--

CREATE TABLE `tblFlashSales` (
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `discount_percent` int(11) NOT NULL,
  `sale_price` decimal(10,2) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblFollows`
--

CREATE TABLE `tblFollows` (
  `follow_id` int(11) NOT NULL,
  `follower_id` int(11) NOT NULL,
  `following_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblFollows`
--

INSERT INTO `tblFollows` (`follow_id`, `follower_id`, `following_id`, `created_at`) VALUES
(1, 14, 2, '2026-05-20 21:59:30'),
(2, 2, 12, '2026-05-20 22:16:43'),
(3, 14, 12, '2026-05-20 22:17:25'),
(4, 12, 2, '2026-05-21 14:53:30'),
(5, 16, 12, '2026-06-17 23:11:13'),
(6, 19, 12, '2026-06-18 14:06:45'),
(7, 20, 12, '2026-06-18 15:29:06');

--
-- Triggers `tblFollows`
--
DELIMITER $$
CREATE TRIGGER `update_followers_count_delete` AFTER DELETE ON `tblFollows` FOR EACH ROW BEGIN
    UPDATE tblUser SET followers_count = followers_count - 1 WHERE user_id = OLD.following_id;
    UPDATE tblUser SET following_count = following_count - 1 WHERE user_id = OLD.follower_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_followers_count_insert` AFTER INSERT ON `tblFollows` FOR EACH ROW BEGIN
    UPDATE tblUser SET followers_count = followers_count + 1 WHERE user_id = NEW.following_id;
    UPDATE tblUser SET following_count = following_count + 1 WHERE user_id = NEW.follower_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tblInvoices`
--

CREATE TABLE `tblInvoices` (
  `invoice_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `invoice_data` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `is_admin_reply` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblMessages`
--

INSERT INTO `tblMessages` (`message_id`, `sender_id`, `receiver_id`, `product_id`, `message_text`, `is_read`, `created_at`, `file_name`, `file_path`, `file_type`, `file_size`, `is_admin_reply`) VALUES
(1, 12, 3, 2, 'do you mind showing  me more pictures of this product', 1, '2026-04-15 14:00:29', NULL, NULL, NULL, NULL, 0),
(2, 12, 2, 1, 'hi', 1, '2026-04-15 14:05:58', NULL, NULL, NULL, NULL, 0),
(3, 3, 12, 2, 'ok no problem', 1, '2026-04-15 15:28:29', NULL, NULL, NULL, NULL, 0),
(4, 14, 2, 7, 'Hi, Mr John I can see your Item but I will like to ask something about this item if you do not have any problem', 1, '2026-05-03 21:09:37', NULL, NULL, NULL, NULL, 0),
(5, 2, 14, 7, 'Ok no problem feel free to ask', 1, '2026-05-03 21:22:17', NULL, NULL, NULL, NULL, 0),
(6, 12, 3, 2, 'vutivi 1', 1, '2026-05-04 12:38:39', NULL, NULL, NULL, NULL, 0),
(7, 1, 12, 2, 'hi vutivi', 1, '2026-06-17 19:58:21', NULL, NULL, NULL, NULL, 1),
(8, 16, 12, 34, 'Hi can you share other pictures for this item', 1, '2026-06-17 23:11:00', NULL, NULL, NULL, NULL, 0),
(9, 12, 16, 34, 'Okay no problem I will share more soon just wait', 1, '2026-06-17 23:13:36', NULL, NULL, NULL, NULL, 0),
(10, 1, 12, 34, 'please share on time', 0, '2026-06-17 23:16:39', NULL, NULL, NULL, NULL, 1),
(11, 16, 12, 34, 'thanks I will be waiting', 0, '2026-06-18 04:09:20', NULL, NULL, NULL, NULL, 0),
(12, 19, 12, 34, 'Hi can I get more information about this', 1, '2026-06-18 14:08:42', NULL, NULL, NULL, NULL, 0),
(13, 12, 19, 34, 'what do you want', 1, '2026-06-18 14:11:42', NULL, NULL, NULL, NULL, 0),
(14, 1, 12, 34, 'hi user', 0, '2026-06-18 14:52:01', NULL, NULL, NULL, NULL, 1),
(15, 20, 12, 34, 'hi please tell me more about this product', 1, '2026-06-18 15:30:04', NULL, NULL, NULL, NULL, 0),
(16, 12, 20, 34, 'what do you want to know', 1, '2026-06-18 15:31:38', NULL, NULL, NULL, NULL, 0),
(17, 1, 12, 34, 'Hi guys', 0, '2026-06-18 15:38:36', NULL, NULL, NULL, NULL, 1);

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
(1, 1, 2, 1, 850.00),
(2, 2, 1, 1, 450.00),
(3, 2, 3, 1, 650.00),
(4, 3, 5, 1, 450.00),
(5, 4, 8, 1, 320.00),
(6, 5, 2, 1, 850.00),
(7, 6, 2, 1, 850.00),
(8, 6, 18, 1, 290.00),
(9, 7, 12, 1, 390.00),
(10, 7, 24, 1, 550.00),
(11, 7, 3, 1, 650.00),
(12, 8, 34, 1, 1800.00),
(13, 9, 26, 1, 260.00),
(14, 10, 11, 1, 180.00),
(15, 10, 14, 1, 450.00),
(16, 11, 36, 2, 999.00),
(17, 11, 12, 1, 390.00),
(18, 12, 39, 2, 799.00),
(19, 12, 7, 1, 250.00);

-- --------------------------------------------------------

--
-- Table structure for table `tblSavedFilters`
--

CREATE TABLE `tblSavedFilters` (
  `filter_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `filter_name` varchar(100) NOT NULL,
  `filter_data` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `points` int(11) DEFAULT 0,
  `first_purchase_discount_used` tinyint(1) DEFAULT 0,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `order_count` int(11) DEFAULT 0,
  `loyalty_discount_used` tinyint(1) DEFAULT 0,
  `followers_count` int(11) DEFAULT 0,
  `following_count` int(11) DEFAULT 0,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tblUser`
--

INSERT INTO `tblUser` (`user_id`, `name`, `surname`, `email`, `username`, `password_hash`, `phone`, `delivery_address`, `is_verified`, `is_seller_verified`, `points`, `first_purchase_discount_used`, `role`, `created_at`, `last_login`, `order_count`, `loyalty_discount_used`, `followers_count`, `following_count`, `profile_picture`) VALUES
(1, 'Admin', 'User', 'admin@pastimes.com', 'admin', '0192023a7bbd73250516f069df18b500', NULL, NULL, 1, 1, 0, 0, 'admin', '2026-04-15 11:09:43', '2026-06-18 15:34:26', 0, 0, 0, 0, NULL),
(2, 'John', 'Smith', 'john.smith@email.com', 'johnsmith', '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, 1, 1, 0, 0, 'user', '2026-04-15 11:09:43', '2026-05-20 22:16:32', 0, 0, 2, 1, NULL),
(3, 'Sarah', 'Johnson', 'sarah.j@email.com', 'sarahj', '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, 1, 1, 0, 0, 'user', '2026-04-15 11:09:43', '2026-05-26 15:41:39', 0, 0, 0, 0, NULL),
(4, 'Michael', 'Brown', 'michael.b@email.com', 'mbrown', '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, 1, 0, 0, 0, 'user', '2026-04-15 11:09:43', NULL, 0, 0, 0, 0, NULL),
(5, 'Emily', 'Davis', 'emily.d@email.com', 'emilyd', '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, 1, 1, 0, 0, 'user', '2026-04-15 11:09:43', NULL, 0, 0, 0, 0, NULL),
(6, 'David', 'Wilson', 'david.w@email.com', 'dwilson', '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, 1, 0, 0, 0, 'user', '2026-04-15 11:09:43', NULL, 0, 0, 0, 0, NULL),
(7, 'Lisa', 'Martinez', 'lisa.m@email.com', 'lisam', '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, 1, 1, 0, 0, 'user', '2026-04-15 11:09:43', NULL, 0, 0, 0, 0, NULL),
(8, 'James', 'Taylor', 'james.t@email.com', 'jamest', '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, 1, 0, 0, 0, 'user', '2026-04-15 11:09:43', NULL, 0, 0, 0, 0, NULL),
(9, 'Maria', 'Anderson', 'maria.a@email.com', 'mariaa', '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, 1, 1, 0, 0, 'user', '2026-04-15 11:09:43', NULL, 0, 0, 0, 0, NULL),
(10, 'Robert', 'Thomas', 'robert.t@email.com', 'robertt', '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, 1, 0, 0, 0, 'user', '2026-04-15 11:09:43', NULL, 0, 0, 0, 0, NULL),
(12, 'Vutivi', 'Vukeya', 'vutivivukeya544@gmail.com', 'vvukeya', 'f5a0652d89ceb53b00b222fbd2a3b286', '0677183135', '2558 Mikosi parklane street\r\nChiawelo\r\n1818', 1, 1, 0, 0, 'user', '2026-04-15 11:15:42', '2026-06-18 15:39:04', 0, 0, 5, 1, NULL),
(13, 'sophie', 'vukeya', 'ST10445789@rcconnect.edu.za', 'sophie62', 'c17add984c219479d46ebb66154e326c', '0838131081', NULL, 0, 0, 0, 0, 'user', '2026-04-17 22:37:12', NULL, 0, 0, 0, 0, NULL),
(14, 'Tonny', 'Vukeya', 'tonny@gmail.com', 'tvukeya', 'f3a303d8c5f2230131180339bda2fa92', '', '', 1, 0, 0, 0, 'user', '2026-05-03 18:01:09', '2026-05-20 22:17:09', 0, 0, 0, 2, NULL),
(15, 'jack', 'vukeya', 'jack@gmail.com', 'jack01', 'f5a0652d89ceb53b00b222fbd2a3b286', '0731453906', NULL, 1, 0, 0, 0, 'user', '2026-05-04 18:06:23', '2026-05-04 18:07:10', 0, 0, 0, 0, NULL),
(16, 'Talent', 'Vukeya', 'Talent@gmail.com', 'TalentTT', '482c811da5d5b4bc6d497ffa98491e38', '0736792956', NULL, 1, 0, 0, 0, 'user', '2026-06-17 23:09:07', '2026-06-18 02:10:34', 0, 0, 0, 1, NULL),
(17, 'Karabo', 'Makoro', 'karabomakoro@gmail.com', 'kmakoro', '482c811da5d5b4bc6d497ffa98491e38', '0783214534', NULL, 1, 1, 0, 0, 'user', '2026-06-18 11:52:27', '2026-06-18 12:19:20', 0, 0, 0, 0, NULL),
(18, 'Tivasi', 'Ntimbane', 'tivasi@gmail.com', 'tivasi', '482c811da5d5b4bc6d497ffa98491e38', NULL, NULL, 1, 0, 0, 0, 'user', '2026-06-18 12:21:45', NULL, 0, 0, 0, 0, NULL),
(19, 'Tirhanii', 'Vukeya', 'ttvukeya@gmail.com', 'Tvuks', '482c811da5d5b4bc6d497ffa98491e38', '0784182900', NULL, 1, 1, 0, 0, 'user', '2026-06-18 14:00:12', '2026-06-18 14:53:02', 0, 0, 0, 1, NULL),
(20, 'Simphiwe', 'Baloyi', 'Sbaloyi@gmail.com', 'Sbaloyi', '482c811da5d5b4bc6d497ffa98491e38', '0873251347', NULL, 1, 1, 0, 0, 'user', '2026-06-18 15:23:28', '2026-06-18 15:24:49', 0, 0, 0, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblWishlist`
--

CREATE TABLE `tblWishlist` (
  `wishlist_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblWishlist`
--

INSERT INTO `tblWishlist` (`wishlist_id`, `user_id`, `product_id`, `created_at`) VALUES
(1, 12, 34, '2026-05-05 03:46:18');

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
-- Indexes for table `tblAdminReplies`
--
ALTER TABLE `tblAdminReplies`
  ADD PRIMARY KEY (`reply_id`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `admin_id` (`admin_id`);

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
-- Indexes for table `tblChatConversations`
--
ALTER TABLE `tblChatConversations`
  ADD PRIMARY KEY (`conversation_id`),
  ADD UNIQUE KEY `unique_conversation` (`user1_id`,`user2_id`),
  ADD KEY `user2_id` (`user2_id`);

--
-- Indexes for table `tblClothes`
--
ALTER TABLE `tblClothes`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_brand` (`brand`);

--
-- Indexes for table `tblComments`
--
ALTER TABLE `tblComments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tblCoupons`
--
ALTER TABLE `tblCoupons`
  ADD PRIMARY KEY (`coupon_id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `tblFlashSales`
--
ALTER TABLE `tblFlashSales`
  ADD PRIMARY KEY (`sale_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `tblFollows`
--
ALTER TABLE `tblFollows`
  ADD PRIMARY KEY (`follow_id`),
  ADD UNIQUE KEY `unique_follow` (`follower_id`,`following_id`),
  ADD KEY `following_id` (`following_id`);

--
-- Indexes for table `tblInvoices`
--
ALTER TABLE `tblInvoices`
  ADD PRIMARY KEY (`invoice_id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `order_id` (`order_id`);

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
-- Indexes for table `tblSavedFilters`
--
ALTER TABLE `tblSavedFilters`
  ADD PRIMARY KEY (`filter_id`),
  ADD KEY `user_id` (`user_id`);

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
-- Indexes for table `tblWishlist`
--
ALTER TABLE `tblWishlist`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD UNIQUE KEY `unique_wishlist` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblAdmin`
--
ALTER TABLE `tblAdmin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblAdminReplies`
--
ALTER TABLE `tblAdminReplies`
  MODIFY `reply_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tblAorder`
--
ALTER TABLE `tblAorder`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tblCart`
--
ALTER TABLE `tblCart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `tblChatConversations`
--
ALTER TABLE `tblChatConversations`
  MODIFY `conversation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblClothes`
--
ALTER TABLE `tblClothes`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `tblComments`
--
ALTER TABLE `tblComments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblCoupons`
--
ALTER TABLE `tblCoupons`
  MODIFY `coupon_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tblFlashSales`
--
ALTER TABLE `tblFlashSales`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblFollows`
--
ALTER TABLE `tblFollows`
  MODIFY `follow_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tblInvoices`
--
ALTER TABLE `tblInvoices`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblMessages`
--
ALTER TABLE `tblMessages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tblOrderItems`
--
ALTER TABLE `tblOrderItems`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `tblSavedFilters`
--
ALTER TABLE `tblSavedFilters`
  MODIFY `filter_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblUser`
--
ALTER TABLE `tblUser`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tblWishlist`
--
ALTER TABLE `tblWishlist`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblAdmin`
--
ALTER TABLE `tblAdmin`
  ADD CONSTRAINT `tbladmin_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tblUser` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `tblAdminReplies`
--
ALTER TABLE `tblAdminReplies`
  ADD CONSTRAINT `tbladminreplies_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `tblMessages` (`message_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbladminreplies_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `tblUser` (`user_id`) ON DELETE CASCADE;

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
-- Constraints for table `tblChatConversations`
--
ALTER TABLE `tblChatConversations`
  ADD CONSTRAINT `tblchatconversations_ibfk_1` FOREIGN KEY (`user1_id`) REFERENCES `tblUser` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tblchatconversations_ibfk_2` FOREIGN KEY (`user2_id`) REFERENCES `tblUser` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `tblClothes`
--
ALTER TABLE `tblClothes`
  ADD CONSTRAINT `tblclothes_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `tblUser` (`user_id`);

--
-- Constraints for table `tblComments`
--
ALTER TABLE `tblComments`
  ADD CONSTRAINT `tblcomments_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `tblClothes` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tblcomments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `tblUser` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `tblFlashSales`
--
ALTER TABLE `tblFlashSales`
  ADD CONSTRAINT `tblflashsales_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `tblClothes` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tblflashsales_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `tblUser` (`user_id`);

--
-- Constraints for table `tblFollows`
--
ALTER TABLE `tblFollows`
  ADD CONSTRAINT `tblfollows_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `tblUser` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tblfollows_ibfk_2` FOREIGN KEY (`following_id`) REFERENCES `tblUser` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `tblInvoices`
--
ALTER TABLE `tblInvoices`
  ADD CONSTRAINT `tblinvoices_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `tblAorder` (`order_id`) ON DELETE CASCADE;

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

--
-- Constraints for table `tblSavedFilters`
--
ALTER TABLE `tblSavedFilters`
  ADD CONSTRAINT `tblsavedfilters_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tblUser` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `tblWishlist`
--
ALTER TABLE `tblWishlist`
  ADD CONSTRAINT `tblwishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tblUser` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tblwishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `tblClothes` (`product_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
