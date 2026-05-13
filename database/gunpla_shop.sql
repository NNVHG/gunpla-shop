
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `gunpla_shop` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `gunpla_shop`;

CREATE TABLE `users` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `password` VARCHAR(255) NOT NULL COMMENT 'Lưu bằng password_hash()',
  `phone` VARCHAR(15) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `role` ENUM('customer','admin') NOT NULL DEFAULT 'customer',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Thêm tài khoản Admin mặc định (Pass: Admin@123)
INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `phone`, `address`, `role`) VALUES
(1, 'Administrator', 'admin@gunplashop.vn', '$2y$10$Uo2aH6A2pS4A2tYv9GzD8eL8SXYiK9fM/bTf0oP0iUu1w4hT9BZyG', '0987654321', 'Bình Dương', 'admin');

CREATE TABLE `categories` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `parent_id` INT(10) UNSIGNED DEFAULT NULL,
  `type` ENUM('scale','grade','series','manufacturer','tool','accessory','chemical','combo') NOT NULL DEFAULT 'scale',
  `sort_order` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `categories` (`id`, `name`, `slug`, `parent_id`, `type`, `sort_order`) VALUES
(1, 'HG (High Grade)', 'grade-hg', NULL, 'grade', 1),
(2, 'RG (Real Grade)', 'grade-rg', NULL, 'grade', 2),
(3, 'MG (Master Grade)', 'grade-mg', NULL, 'grade', 3),
(4, 'PG (Perfect Grade)', 'grade-pg', NULL, 'grade', 4),
(5, 'Universal Century [UC]', 'series-uc', NULL, 'series', 1),
(6, 'Tỉ lệ 1/144', 'scale-1-144', NULL, 'scale', 1),
(7, 'Dụng cụ cắt', 'tool-cutting', NULL, 'tool', 1);

CREATE TABLE `products` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `price` DECIMAL(10,0) NOT NULL,
  `stock` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
  `category_id` INT(10) UNSIGNED NOT NULL,
  `scale` VARCHAR(20) DEFAULT NULL COMMENT '1/144, 1/100, 1/60...',
  `grade` VARCHAR(30) DEFAULT NULL COMMENT 'HG, MG, RG, PG...',
  `series` VARCHAR(100) DEFAULT NULL COMMENT 'Mobile Suit Gundam, SEED...',
  `description` TEXT DEFAULT NULL,
  `thumbnail` VARCHAR(255) DEFAULT NULL,
  `weight_gram` SMALLINT(5) UNSIGNED DEFAULT 200 COMMENT 'Dùng để tính phí ship',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `product_images` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT(10) UNSIGNED NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `reviews` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT(10) UNSIGNED NOT NULL,
  `user_id` INT(10) UNSIGNED NOT NULL,
  `rating` TINYINT(3) UNSIGNED NOT NULL DEFAULT 5 COMMENT 'Số sao: 1 - 5',
  `comment` TEXT NOT NULL,
  `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'approved' COMMENT 'pending = chờ duyệt, approved = hiển thị',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product_status` (`product_id`, `status`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `fk_reviews_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `favorites` (
  `user_id` INT(10) UNSIGNED NOT NULL,
  `product_id` INT(10) UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `product_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `orders` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) UNSIGNED DEFAULT NULL COMMENT 'NULL = đặt không cần tài khoản',
  `full_name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(15) NOT NULL,
  `province` VARCHAR(100) NOT NULL,
  `address` TEXT NOT NULL,
  `note` TEXT DEFAULT NULL,
  `subtotal` DECIMAL(12,0) NOT NULL,
  `shipping_fee` DECIMAL(8,0) NOT NULL DEFAULT 0,
  `total` DECIMAL(12,0) NOT NULL,
  `status` ENUM('pending','confirmed','shipping','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `payment_method` ENUM('cod','vnpay') NOT NULL DEFAULT 'cod',
  `payment_status` ENUM('unpaid','paid','failed') NOT NULL DEFAULT 'unpaid',
  `transaction_id` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `order_items` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` INT(10) UNSIGNED NOT NULL,
  `product_id` INT(10) UNSIGNED DEFAULT NULL,
  `product_name` VARCHAR(255) NOT NULL COMMENT 'Snapshot tên lúc đặt',
  `quantity` SMALLINT(5) UNSIGNED NOT NULL,
  `price_at_order` DECIMAL(10,0) NOT NULL COMMENT 'Snapshot giá lúc đặt',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;

INSERT INTO `categories` (`id`, `name`, `slug`, `parent_id`, `type`, `sort_order`) VALUES
(1, 'Tỉ lệ 1/144', 'scale-1-144', NULL, 'scale', 1),
(2, 'Tỉ lệ 1/100', 'scale-1-100', NULL, 'scale', 2),
(3, 'Tỉ lệ 1/60', 'scale-1-60', NULL, 'scale', 3),
(4, 'Tỉ lệ 1/48', 'scale-1-48', NULL, 'scale', 4),
(5, 'Không tỉ lệ (Non-scale)', 'scale-non-scale', NULL, 'scale', 5),
(6, 'HG (High Grade)', 'grade-hg', NULL, 'grade', 3),
(7, 'RG (Real Grade)', 'grade-rg', NULL, 'grade', 4),
(8, 'EG (Entry Grade)', 'grade-eg', NULL, 'grade', 2),
(9, 'MG (Master Grade)', 'grade-mg', NULL, 'grade', 5),
(10, 'MG Ver.Ka', 'grade-mg-verka', NULL, 'grade', 6),
(11, 'PG (Perfect Grade)', 'grade-pg', NULL, 'grade', 7),
(12, 'SD (Super Deformed)', 'grade-sd', NULL, 'grade', 1),
(13, 'MGSD', 'grade-mgsd', NULL, 'grade', 8),
(14, 'PG Unleashed', 'grade-pg-unleashed', NULL, 'grade', 9),
(15, 'Universal Century [UC]', 'series-uc', NULL, 'series', 1),
(16, 'Cosmic Era [CE]', 'series-ce', NULL, 'series', 2),
(17, 'Anno Domini [AD]', 'series-ad', NULL, 'series', 3),
(18, 'Post Disaster [PD]', 'series-pd', NULL, 'series', 4),
(19, 'Ad Stella [AS]', 'series-as', NULL, 'series', 5),
(20, 'Bandai Namco', 'brand-bandai', NULL, 'manufacturer', 1),
(21, 'Hãng thiết kế riêng (Third-party)', 'brand-third-party', NULL, 'manufacturer', 2),
(22, 'Hãng sao chép (Bootleg)', 'brand-bootleg', NULL, 'manufacturer', 3),
(40, 'Dụng cụ cơ bản', 'tool-basic', NULL, 'tool', 1),
(41, 'Kềm cắt chuyên dụng', 'tool-nipper', 40, 'tool', 1),
(42, 'Dao trổ (Hobby Knife)', 'tool-knife', 40, 'tool', 2),
(43, 'Nhíp gắp (Tweezers)', 'tool-tweezers', 40, 'tool', 3),
(44, 'Dụng cụ mài nhám', 'tool-sanding', 40, 'tool', 4),
(50, 'Dụng cụ làm chi tiết', 'tool-detailing', NULL, 'tool', 2),
(51, 'Bút kẻ viền (Panel Line)', 'tool-panel-line', 50, 'tool', 1),
(52, 'Bút sơn (Gundam Marker)', 'tool-marker', 50, 'tool', 2),
(53, 'Dụng cụ dán đề can', 'tool-decal-acc', 50, 'tool', 3),
(54, 'Dụng cụ tách mảnh', 'tool-separator', 50, 'tool', 4),
(60, 'Dụng cụ hoàn thiện', 'chem-finishing', NULL, 'chemical', 3),
(61, 'Sơn phủ bảo vệ (Topcoat)', 'chem-topcoat', 60, 'chemical', 1),
(62, 'Keo dán mô hình', 'chem-cement', 60, 'chemical', 2),
(70, 'Dụng cụ nâng cao', 'tool-advanced', NULL, 'tool', 4),
(71, 'Dao khắc (Scriber)', 'tool-scriber', 70, 'tool', 1),
(72, 'Máy mài cầm tay', 'tool-electric-sander', 70, 'tool', 2),
(73, 'Bộ máy phun sơn (Airbrush)', 'tool-airbrush', 70, 'tool', 3),
(80, 'Phụ kiện hỗ trợ', 'accessory-support', NULL, 'accessory', 5),
(81, 'Thảm cắt (Cutting Mat)', 'acc-cutting-mat', 80, 'accessory', 1),
(82, 'Giá đỡ (Action Base)', 'acc-action-base', 80, 'accessory', 2),
(83, 'Khay đựng linh kiện', 'acc-parts-tray', 80, 'accessory', 3),
(90, 'Combo khởi đầu (Starter Kit)', 'combo-starter', NULL, 'combo', 6);

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `quantity`, `price_at_order`) VALUES
(1, 1, 3, 'MG 1/100 Freedom Gundam Ver.2.0', 1, 680000),
(2, 2, 2, 'RG 1/144 Unicorn Gundam', 1, 420000),
(3, 3, 9, 'PG 1/60 Strike Freedom Gundam', 1, 3200000),
(4, 4, 2, 'RG 1/144 Unicorn Gundam', 1, 420000),
(5, 5, 2, 'RG 1/144 Unicorn Gundam', 1, 420000),
(6, 6, 2, 'RG 1/144 Unicorn Gundam', 1, 420000),
(7, 7, 2, 'RG 1/144 Unicorn Gundam', 1, 420000),
(8, 8, 2, 'RG 1/144 Unicorn Gundam', 1, 420000),
(9, 9, 2, 'RG 1/144 Unicorn Gundam', 1, 420000),
(10, 10, 7, 'HG 1/144 Gundam Barbatos Lupus Rex', 1, 280000),
(11, 11, 7, 'HG 1/144 Gundam Barbatos Lupus Rex', 1, 280000),
(12, 12, 3, 'MG 1/100 Freedom Gundam Ver.2.0', 1, 680000),
(13, 13, 3, 'MG 1/100 Freedom Gundam Ver.2.0', 1, 680000),
(14, 14, 3, 'MG 1/100 Freedom Gundam Ver.2.0', 1, 680000),
(15, 15, 3, 'MG 1/100 Freedom Gundam Ver.2.0', 1, 680000),
(16, 16, 3, 'MG 1/100 Freedom Gundam Ver.2.0', 1, 680000),
(17, 17, 1, 'HG 1/144 RX-78-2 Gundam (Revive Edition)', 1, 250000),
(18, 18, 8, 'MG Ver.Ka 1/100 Sinanju', 1, 1480000),
(19, 19, 8, 'MG Ver.Ka 1/100 Sinanju', 1, 1480000),
(20, 20, 7, 'HG 1/144 Gundam Barbatos Lupus Rex', 1, 280000),
(21, 21, 4, 'HG 1/144 Zaku II MS-06F', 1, 220000),
(22, 22, 4, 'HG 1/144 Zaku II MS-06F', 1, 220000);

INSERT INTO `products` (`id`, `name`, `slug`, `price`, `stock`, `category_id`, `scale`, `grade`, `series`, `description`, `thumbnail`, `weight_gram`, `is_active`, `created_at`) VALUES
(1, 'HG 1/144 RX-78-2 Gundam (Revive Edition)', 'hg-rx78-2-revive', 250000, 14, 6, '1/144', 'HG', 'Mobile Suit Gundam', 'Phiên bản Revive 2015 với khuôn đúc mới, chi tiết sắc nét hơn bản gốc. Phù hợp cho người mới bắt đầu chơi Gunpla.', NULL, 180, 1, '2026-04-25 13:47:38'),
(2, 'RG 1/144 Unicorn Gundam', 'rg-unicorn', 420000, 12, 7, '1/144', 'RG', 'Mobile Suit Gundam Unicorn', 'Real Grade với khung nội thất Inner Frame chi tiết, phần Psychoframe màu vàng nổi bật. Có thể chuyển đổi giữa Unicorn Mode và Destroy Mode.', NULL, 210, 1, '2026-04-25 13:47:38'),
(3, 'MG 1/100 Freedom Gundam Ver.2.0', 'mg-freedom-v2', 680000, 10, 9, '1/100', 'MG', 'Mobile Suit Gundam SEED', 'Phiên bản Ver.2.0 cải tiến toàn diện từ khớp vai đến cánh tên lửa METEOR. Khớp cánh có thể mở hoàn toàn 180 độ.', NULL, 420, 1, '2026-04-25 13:47:38'),
(4, 'HG 1/144 Zaku II MS-06F', 'hg-zaku-ii', 220000, 18, 6, '1/144', 'HG', 'Mobile Suit Gundam', 'Biểu tượng Zeon với ống nhiệt trên vai, súng trường Zaku Machine Gun và Shield tặng kèm.', NULL, 160, 1, '2026-04-25 13:47:38'),
(5, 'MG 1/100 Wing Gundam Zero EW', 'mg-wing-zero-ew', 720000, 7, 9, '1/100', 'MG', 'Mobile Suit Gundam Wing: Endless Waltz', 'Thiết kế cánh thiên thần biểu tượng của dòng Wing. Phiên bản EW (Endless Waltz) với cánh kiếm đôi.', NULL, 480, 1, '2026-04-25 13:47:38'),
(6, 'RG 1/144 Gundam Exia', 'rg-exia', 390000, 10, 7, '1/144', 'RG', 'Mobile Suit Gundam 00', 'Real Grade Exia với GN Drive phát sáng xanh, 7 thanh kiếm GN Sword kèm theo. Hiện tạm hết hàng.', NULL, 200, 1, '2026-04-25 13:47:38'),
(7, 'HG 1/144 Gundam Barbatos Lupus Rex', 'hg-barbatos-lupus-rex', 280000, 9, 6, '1/144', 'HG', 'Mobile Suit Gundam: Iron-Blooded Orphans', 'Hình dạng hung hãn với Tail Blade khổng lồ và cặp búa mace. Thiết kế phi đối xứng độc đáo.', NULL, 195, 1, '2026-04-25 13:47:38'),
(8, 'MG Ver.Ka 1/100 Sinanju', 'mg-verka-sinanju', 1480000, 10, 10, '1/100', 'MG Ver.Ka', 'Mobile Suit Gundam Unicorn', 'Thiết kế đỏ thắm huyền thoại của Neo Zeon. Ver.Ka với các chi tiết khắc laser trên toàn thân và decal kim loại cao cấp.', NULL, 620, 1, '2026-04-25 13:47:38'),
(9, 'PG 1/60 Strike Freedom Gundam', 'pg-strike-freedom', 3200000, 8, 11, '1/60', 'PG', 'Mobile Suit Gundam SEED Destiny', 'Perfect Grade với khung nội thất đầy đủ, cánh tên lửa METEOR có thể mở 180 độ. Gồm LED unit cho mắt và phần Dragoon phát sáng.', NULL, 1200, 1, '2026-04-25 13:47:38'),
(10, 'HG 1/144 Gundam Aerial', 'hg-aerial', 310000, 18, 6, '1/144', 'HG', 'Mobile Suit Gundam: The Witch from Mercury', 'Gundam thế hệ mới từ series Witch from Mercury 2022. Thiết kế mảnh mai, cánh Permet Scale độc đáo.', NULL, 175, 1, '2026-04-25 13:47:38');

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `phone`, `address`, `role`, `created_at`) VALUES
(1, 'Quản trị viên', 'admin@gunplashop.vn', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 'admin', '2026-04-25 13:47:38'),
(2, 'Nguyen Ngo Vu hoang gia', '2224802010628@student.tdmu.edu.vn', '$2y$10$tVWBq2y2IQX6z9wLAltTIONfWzYer7KndYO/hYeeFqnvLMsjOC8vC', '0363710624', NULL, 'customer', '2026-04-29 18:52:58'),
(3, 'admin', 'admin1@gunplashop.vn', '$2y$10$9q3WTRNmjjVTmi6JBH1vouROY9rP5Ab4JsVvoSH5kTlJnm/TvT.7C', '0363710624', NULL, 'admin', '2026-05-10 17:44:48'),
(4, 'Dương Dua', 'dua@gunplashop.vn', '$2y$10$1bdqWcoJELPSaVboZuYceeFCTkyFf3nAQrlSg/mAe0ISCoxuiXb0.', '0363710624', 'Huỳnh Văn Lũy', 'customer', '2026-05-10 18:24:38');