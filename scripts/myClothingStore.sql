-- Pastimes ClothingStore Database
-- Student: Vutivi & Karabo
-- Date: April 2026

-- Drop and create database
DROP DATABASE IF EXISTS `ClothingStore`;
CREATE DATABASE `ClothingStore`;
USE `ClothingStore`;

-- ============================================
-- TABLE: tblUser
-- ============================================
DROP TABLE IF EXISTS `tblUser`;
CREATE TABLE `tblUser` (
    `user_id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `surname` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `delivery_address` TEXT DEFAULT NULL,
    `is_verified` TINYINT(1) DEFAULT 0,
    `is_seller_verified` TINYINT(1) DEFAULT 0,
    `role` ENUM('user', 'admin') DEFAULT 'user',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL,
    PRIMARY KEY (`user_id`),
    INDEX idx_email (`email`),
    INDEX idx_username (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: tblAdmin
-- ============================================
DROP TABLE IF EXISTS `tblAdmin`;
CREATE TABLE `tblAdmin` (
    `admin_id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `permissions` TEXT DEFAULT NULL,
    `last_action` TIMESTAMP NULL,
    PRIMARY KEY (`admin_id`),
    FOREIGN KEY (`user_id`) REFERENCES `tblUser`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLE: tblClothes
-- ============================================
DROP TABLE IF EXISTS `tblClothes`;
CREATE TABLE `tblClothes` (
    `product_id` INT(11) NOT NULL AUTO_INCREMENT,
    `seller_id` INT(11) NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `brand` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `condition` ENUM('New', 'Like New', 'Good', 'Fair') DEFAULT 'Good',
    `category` VARCHAR(50) DEFAULT NULL,
    `size` VARCHAR(20) DEFAULT NULL,
    `colour` VARCHAR(30) DEFAULT NULL,
    `image_url` VARCHAR(500) DEFAULT NULL,
    `status` ENUM('pending', 'approved', 'rejected', 'sold') DEFAULT 'pending',
    `views` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `sold_date` TIMESTAMP NULL,
    PRIMARY KEY (`product_id`),
    FOREIGN KEY (`seller_id`) REFERENCES `tblUser`(`user_id`),
    INDEX idx_status (`status`),
    INDEX idx_brand (`brand`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLE: tblAorder (Orders)
-- ============================================
DROP TABLE IF EXISTS `tblAorder`;
CREATE TABLE `tblAorder` (
    `order_id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `order_number` VARCHAR(50) NOT NULL UNIQUE,
    `total_amount` DECIMAL(10,2) NOT NULL,
    `delivery_address` TEXT NOT NULL,
    `status` ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    `payment_method` VARCHAR(50) DEFAULT NULL,
    `payment_status` ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    `tracking_number` VARCHAR(100) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`order_id`),
    FOREIGN KEY (`user_id`) REFERENCES `tblUser`(`user_id`),
    INDEX idx_order_number (`order_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLE: Order Items
-- ============================================
DROP TABLE IF EXISTS `tblOrderItems`;
CREATE TABLE `tblOrderItems` (
    `item_id` INT(11) NOT NULL AUTO_INCREMENT,
    `order_id` INT(11) NOT NULL,
    `product_id` INT(11) NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `price` DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (`item_id`),
    FOREIGN KEY (`order_id`) REFERENCES `tblAorder`(`order_id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `tblClothes`(`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLE: Shopping Cart
-- ============================================
DROP TABLE IF EXISTS `tblCart`;
CREATE TABLE `tblCart` (
    `cart_id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `product_id` INT(11) NOT NULL,
    `quantity` INT DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`cart_id`),
    FOREIGN KEY (`user_id`) REFERENCES `tblUser`(`user_id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `tblClothes`(`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLE: Messages
-- ============================================
DROP TABLE IF EXISTS `tblMessages`;
CREATE TABLE `tblMessages` (
    `message_id` INT(11) NOT NULL AUTO_INCREMENT,
    `sender_id` INT(11) NOT NULL,
    `receiver_id` INT(11) NOT NULL,
    `product_id` INT(11) DEFAULT NULL,
    `message_text` TEXT NOT NULL,
    `is_read` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`message_id`),
    FOREIGN KEY (`sender_id`) REFERENCES `tblUser`(`user_id`),
    FOREIGN KEY (`receiver_id`) REFERENCES `tblUser`(`user_id`),
    FOREIGN KEY (`product_id`) REFERENCES `tblClothes`(`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Insert Admin User
-- ============================================
INSERT INTO `tblUser` (`name`, `surname`, `email`, `username`, `password_hash`, `is_verified`, `is_seller_verified`, `role`) VALUES
('Admin', 'User', 'admin@pastimes.com', 'admin', MD5('admin123'), 1, 1, 'admin');

INSERT INTO `tblAdmin` (`user_id`, `permissions`) VALUES (1, 'full_access');

-- ============================================
-- Insert Sample Users (20 records)
-- ============================================
INSERT INTO `tblUser` (`name`, `surname`, `email`, `username`, `password_hash`, `is_verified`, `is_seller_verified`, `role`) VALUES
('John', 'Smith', 'john.smith@email.com', 'johnsmith', MD5('password123'), 1, 1, 'user'),
('Sarah', 'Johnson', 'sarah.j@email.com', 'sarahj', MD5('password123'), 1, 1, 'user'),
('Michael', 'Brown', 'michael.b@email.com', 'mbrown', MD5('password123'), 1, 0, 'user'),
('Emily', 'Davis', 'emily.d@email.com', 'emilyd', MD5('password123'), 1, 1, 'user'),
('David', 'Wilson', 'david.w@email.com', 'dwilson', MD5('password123'), 1, 0, 'user'),
('Lisa', 'Martinez', 'lisa.m@email.com', 'lisam', MD5('password123'), 1, 1, 'user'),
('James', 'Taylor', 'james.t@email.com', 'jamest', MD5('password123'), 1, 0, 'user'),
('Maria', 'Anderson', 'maria.a@email.com', 'mariaa', MD5('password123'), 1, 1, 'user'),
('Robert', 'Thomas', 'robert.t@email.com', 'robertt', MD5('password123'), 1, 0, 'user'),
('Jennifer', 'Jackson', 'jennifer.j@email.com', 'jenj', MD5('password123'), 1, 1, 'user');

-- ============================================
-- Insert Sample Products (30 records)
-- ============================================
INSERT INTO `tblClothes` (`seller_id`, `title`, `brand`, `description`, `price`, `condition`, `category`, `size`, `colour`, `status`) VALUES
(2, 'Vintage Levi\'s 501 Jeans', 'Levi\'s', 'Classic vintage jeans in excellent condition. Original button fly.', 450.00, 'Like New', 'Jeans', '32', 'Blue', 'approved'),
(3, 'Nike Air Max 90', 'Nike', 'Limited edition Air Max trainers, barely worn, original box included.', 850.00, 'Like New', 'Shoes', '9', 'White/Red', 'approved'),
(4, 'Zara Wool Blend Coat', 'Zara', 'Elegant winter coat, perfect for formal occasions.', 650.00, 'Like New', 'Coats', 'M', 'Beige', 'approved'),
(5, 'Adidas Originals Hoodie', 'Adidas', 'Classic trefoil hoodie, warm and comfortable.', 380.00, 'Good', 'Hoodies', 'L', 'Black', 'approved'),
(6, 'H&M Designer Dress', 'H&M', 'Limited edition designer collaboration dress.', 420.00, 'Like New', 'Dresses', 'S', 'Green', 'approved'),
(7, 'Ray-Ban Wayfarer', 'Ray-Ban', 'Authentic Ray-Ban Wayfarer sunglasses with case.', 890.00, 'Good', 'Accessories', 'One Size', 'Black', 'approved'),
(2, 'Calvin Klein T-Shirt', 'Calvin Klein', 'Classic white t-shirt, never worn with tags.', 250.00, 'New', 'T-Shirts', 'M', 'White', 'approved'),
(4, 'Tommy Hilfiger Polo', 'Tommy Hilfiger', 'Preppy style polo shirt, great condition.', 320.00, 'Like New', 'Polos', 'L', 'Navy', 'approved'),
(6, 'Guess Leather Jacket', 'Guess', 'Genuine leather biker jacket, timeless style.', 1200.00, 'Good', 'Jackets', 'M', 'Brown', 'approved'),
(8, 'Puma Training Pants', 'Puma', 'Comfortable training pants for gym or casual wear.', 280.00, 'Like New', 'Pants', 'L', 'Grey', 'approved'),
(3, 'Forever 21 Summer Dress', 'Forever 21', 'Beautiful floral summer dress, worn twice.', 180.00, 'Good', 'Dresses', 'M', 'Floral', 'approved'),
(5, 'Converse Chuck Taylor', 'Converse', 'Classic high-top sneakers, iconic style.', 390.00, 'Good', 'Shoes', '7', 'Black', 'approved'),
(7, 'North Face Puffer', 'North Face', 'Warm winter puffer jacket, perfect for cold weather.', 950.00, 'Like New', 'Jackets', 'XL', 'Black', 'approved'),
(9, 'Vans Old Skool', 'Vans', 'Classic skate shoes, barely used.', 450.00, 'Like New', 'Shoes', '8', 'Black/White', 'approved'),
(2, 'Superdry Windbreaker', 'Superdry', 'Stylish windbreaker jacket with hood.', 550.00, 'Good', 'Jackets', 'L', 'Red', 'approved'),
(4, 'Ralph Lauren Sweater', 'Ralph Lauren', 'Cashmere blend sweater, luxury feel.', 680.00, 'Like New', 'Sweaters', 'M', 'Navy', 'approved'),
(6, 'Under Armour Gym Bag', 'Under Armour', 'Durable gym backpack, multiple compartments.', 320.00, 'Good', 'Bags', 'One Size', 'Black', 'approved'),
(8, 'GAP Khaki Pants', 'GAP', 'Classic fit chinos, perfect for office.', 290.00, 'Like New', 'Pants', '34', 'Khaki', 'approved'),
(3, 'New Era Cap', 'New Era', 'Baseball cap, adjustable fit.', 180.00, 'New', 'Accessories', 'One Size', 'Black', 'approved'),
(5, 'Levi\'s Denim Jacket', 'Levi\'s', 'Classic trucker jacket, vintage wash.', 780.00, 'Good', 'Jackets', 'M', 'Blue', 'approved'),
(7, 'ASOS Design Shirt', 'ASOS', 'Casual button-down shirt, slim fit.', 220.00, 'Like New', 'Shirts', 'M', 'Pink', 'approved'),
(9, 'BOSS Suit Jacket', 'BOSS', 'Formal suit jacket, excellent condition.', 1500.00, 'Like New', 'Suits', '50', 'Charcoal', 'approved'),
(2, 'Lacoste Polo', 'Lacoste', 'Classic crocodile logo polo shirt.', 450.00, 'Good', 'Polos', 'L', 'White', 'approved'),
(4, 'Fila Disruptors', 'Fila', 'Chunky sneakers, trendy style.', 550.00, 'Like New', 'Shoes', '9', 'White', 'approved'),
(6, 'Jack & Jones Jeans', 'Jack & Jones', 'Slim fit jeans, stretch comfort.', 340.00, 'Good', 'Jeans', '32', 'Black', 'approved'),
(8, 'Esprit Blouse', 'Esprit', 'Elegant work blouse, professional look.', 260.00, 'Like New', 'Blouses', 'S', 'White', 'approved'),
(3, 'Columbia Fleece', 'Columbia', 'Warm fleece jacket, great for layering.', 420.00, 'Good', 'Jackets', 'XL', 'Blue', 'approved'),
(5, 'Mango Trousers', 'Mango', 'Tailored trousers, office ready.', 310.00, 'Like New', 'Pants', '8', 'Black', 'approved'),
(7, 'Skechers Slip-ons', 'Skechers', 'Comfort walking shoes, memory foam.', 480.00, 'Good', 'Shoes', '10', 'Black', 'approved');

-- ============================================
-- Insert Pending Products
-- ============================================
INSERT INTO `tblClothes` (`seller_id`, `title`, `brand`, `description`, `price`, `condition`, `category`, `size`, `colour`, `status`) VALUES
(4, 'River Island Jumpsuit', 'River Island', 'Summer jumpsuit, worn twice.', 350.00, 'Good', 'Jumpsuits', 'M', 'Floral', 'pending'),
(6, 'Bershka Cargo Pants', 'Bershka', 'Trendy cargo pants with pockets.', 290.00, 'Like New', 'Pants', 'S', 'Olive', 'pending'),
(8, 'Stradivarius Top', 'Stradivarius', 'Lace detail top, party ready.', 150.00, 'Good', 'Tops', 'XS', 'White', 'pending');

echo "Database created successfully with all tables and sample data!";