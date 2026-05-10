-- ============================================================
--  GUNPLA SHOP — Bảng đánh giá sản phẩm
--  Chạy file này trong phpMyAdmin hoặc MySQL CLI:
--    SOURCE /path/to/reviews.sql;
-- ============================================================

CREATE TABLE IF NOT EXISTS `reviews` (
  `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `product_id`  INT UNSIGNED     NOT NULL,
  `user_id`     INT UNSIGNED     NOT NULL,
  `rating`      TINYINT UNSIGNED NOT NULL DEFAULT 5
                COMMENT 'Số sao: 1 - 5',
  `comment`     TEXT             NOT NULL,
  `status`      ENUM('pending','approved','rejected') NOT NULL DEFAULT 'approved'
                COMMENT 'pending = chờ duyệt, approved = hiển thị, rejected = ẩn',
  `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  CONSTRAINT `fk_reviews_product` FOREIGN KEY (`product_id`)
    REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_reviews_user`    FOREIGN KEY (`user_id`)
    REFERENCES `users`    (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_rating`         CHECK (`rating` BETWEEN 1 AND 5),

  INDEX `idx_product_status` (`product_id`, `status`),
  INDEX `idx_user`           (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
