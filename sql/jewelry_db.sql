-- Xóa các bảng cũ nếu tồn tại
DROP TABLE IF EXISTS `order_details`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `cart`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;

-- Tạo bảng users
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tạo bảng categories
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tạo bảng products
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL, -- Sửa thành DECIMAL(10,2) để đồng nhất
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `detail_image` varchar(5000) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tạo bảng cart
CREATE TABLE `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tạo bảng orders
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `payment_method` enum('credit_card','e_wallet','bank_transfer') DEFAULT 'credit_card',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_message` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tạo bảng order_details
CREATE TABLE `order_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Chèn dữ liệu
INSERT INTO `categories` VALUES 
(1,'Men\'s Fashion','Thời trang nam'),
(2,'Women\'s Fashion','Thời trang nữ'),
(3,'Accessories','Phụ kiện');

INSERT INTO `users` VALUES 
(1,'vinh','$2y$10$8Gd.eq2.v5CZCsjmQiaDOem0N78hU6K6uTasA9auNkPyb/pYd6vOW','Vinh@gmail.com','vinh','st','09999999','user','2025-03-26 07:05:59','avatar/user/67e3b196b1b09_test_avata.png'),
(2,'vinh2','$2y$10$BqH46O39G.NcM8pR.6VO4.Yf8sjTt78OWK9wbzQ5x8bBJI3faowli','Vinh2@gmail.com','vinh2','','1','user','2025-03-26 07:11:08','avatar/user/67e3b2f1c102a_test_avata.png'),
(3,'admin','$2y$10$Rbd4Za9NHuKIMeW81WWqsu6Z5aGj5Yg.0.O4qpnQZ6FtKKt89fVOi','admin@gmail.com','Vinh','q','1','admin','2025-03-26 07:12:47','avatar/user/67e425cee009b_17430050987259137499480395813608.jpg');

INSERT INTO `products` VALUES 
(2,1,'Clear Crystal Square Pendant Necklace',6000000.00,'67e3dc470d893_18ct_Gold_Vermeil___Clear_Crystal_Square_Pendant_Necklace_925_Naledi_Jewellery_Plain-1.jpg','Clear Crystal Square Pendant Necklace',5,'uploads/67e3dc470d96b_detail1.png,uploads/67e3dc470d9bd_detail2.png,uploads/67e3dc470da02_detail3.png,uploads/67e3dc470da42_detail4.png'),
(3,1,'Cuban Chain',1200000.00,'67e3dc6d79529_cubanChain.jpg','Cuban Chain',3,'uploads/67e3dc6d795e4_detail1.png,uploads/67e3dc6d79610_detail2.png,uploads/67e3dc6d7962c_detail3.png,uploads/67e3dc6d79644_detail4.png'),
(5,2,'Figaro Chain Cross Necklace',4000000.00,'67e3dcb3494eb_FigaroChainCrossNecklace.jpg','Figaro Chain Cross Necklace',4,'uploads/67e3dcb34962a_detail1.png,uploads/67e3dcb349672_detail2.png,uploads/67e3dcb349693_detail3.png,uploads/67e3dcb3496b1_detail4.png'),
(6,1,'Pandora Sparkling Collier Round & Square Pendant Necklace',9000000.00,'67e3dcd82c421_pandora-sparkling-collier-round-square-pendant-necklace.jpg','Pandora Sparkling Collier Round & Square Pendant Necklace',8,'uploads/67e3dcd82c574_detail1.png,uploads/67e3dcd82c5c3_detail2.png,uploads/67e3dcd82c606_detail3.png,uploads/67e3dcd82c645_detail4.png'),
(7,1,'Peeckaboo square concrete necklace',2600000.00,'67e3dd12751b7_PeekaBooConcreteNecklaceinSilver.jpg','Peeckaboo square concrete necklace',15,'uploads/67e3dd1275273_detail1.png,uploads/67e3dd127529c_detail2.png,uploads/67e3dd12752b7_detail3.png,uploads/67e3dd12752d1_detail4.png'),
(8,1,'Puzzle Necklace',999999.00,'67e3dd388e92f_puzzle_necklace.jpg','Puzzle Necklace',22,'uploads/67e3dd388e9ee_detail1.png,uploads/67e3dd388ea2a_detail2.png,uploads/67e3dd388ea8d_detail3.png,uploads/67e3dd388eab0_detail4.png'),
(9,1,'Square Diamond Cluster pendant necklace',4500000.00,'67e3dd6421b5f_square-diamond-cluster-pendant-necklace.jpg','Square Diamond Cluster pendant necklace',14,'uploads/67e3dd6421c29_detail1.png,uploads/67e3dd6421c7f_detail2.png,uploads/67e3dd6421cca_detail3.png,uploads/67e3dd6421d11_detail4.png'),
(10,2,'Swarovski Millenia Bracelet',1230000.00,'67e3dd89d18a1_SWAROVSKI Millenia bracelet.jpg','Swarovski Millenia Bracelet',24,'uploads/67e3dd89d1a17_detail1.png,uploads/67e3dd89d1a8e_detail2.png,uploads/67e3dd89d1ae6_detail3.png,uploads/67e3dd89d1b3c_detail4.png'),
(12,3,'Princess Cut Square Cubic Zirconia Stud Earring',24000000.00,'67e3df37a138c_Princess Cut Square Cubic Zirconia Stud Earring.png','Princess Cut Square Cubic Zirconia Stud Earring',7,'uploads/67e3df37a14cd_detail1.png,uploads/67e3df37a1547_detail2.png,uploads/67e3df37a15ad_detail3.png,uploads/67e3df37a1603_detail4.png');

INSERT INTO `orders` VALUES 
(4,3,13200000.00,'pending','credit_card','2025-03-26 17:24:24',NULL),
(5,3,24000000.00,'pending','bank_transfer','2025-03-26 17:24:34',NULL),
(6,3,4500000.00,'pending','credit_card','2025-03-26 17:24:42',NULL);

INSERT INTO `order_details` VALUES 
(5,4,2,1,6000000.00),
(6,4,2,1,6000000.00),
(7,4,3,1,1200000.00),
(8,5,12,1,24000000.00),
(9,6,9,1,4500000.00);