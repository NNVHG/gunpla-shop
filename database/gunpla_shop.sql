-- ============================================================
-- gunpla_shop.sql
-- Cơ sở dữ liệu cho đồ án Quản lý & Mua bán Mô hình Lắp ráp
-- Sinh viên: Nguyễn Ngô Vũ Hoàng Gia
-- Trường   : Đại học Thủ Dầu Một — Khoa CNTT
-- ============================================================

CREATE DATABASE IF NOT EXISTS gunpla_shop
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE gunpla_shop;

-- ── 1. CATEGORIES (phân cấp tự tham chiếu) ────────────────────────
-- Tầng 1 (parent_id IS NULL) : Tỷ lệ  → 1/144, 1/100, 1/60
-- Tầng 2 (parent_id = ID T1) : Dòng   → HG, MG, RG, PG
-- ─────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS categories (
    id         INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100)     NOT NULL,
    slug       VARCHAR(100)     NOT NULL UNIQUE,
    parent_id  INT UNSIGNED     NULL DEFAULT NULL,
    type       ENUM('scale','grade','tool','accessory') NOT NULL DEFAULT 'scale',
    sort_order TINYINT UNSIGNED NOT NULL DEFAULT 0,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 2. PRODUCTS ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS products (
    id           INT UNSIGNED   AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(255)   NOT NULL,
    slug         VARCHAR(255)   NOT NULL UNIQUE,
    price        DECIMAL(10,0)  NOT NULL,
    stock        SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    category_id  INT UNSIGNED   NOT NULL,
    scale        VARCHAR(20)    NULL COMMENT '1/144, 1/100, 1/60...',
    grade        VARCHAR(30)    NULL COMMENT 'HG, MG, RG, PG...',
    series       VARCHAR(100)   NULL COMMENT 'Mobile Suit Gundam, SEED...',
    description  TEXT           NULL,
    thumbnail    VARCHAR(255)   NULL,
    weight_gram  SMALLINT UNSIGNED NULL DEFAULT 200 COMMENT 'Dùng để tính phí ship',
    is_active    TINYINT(1)     NOT NULL DEFAULT 1,
    created_at   TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 3. PRODUCT_IMAGES (gallery ảnh) ──────────────────────────────
CREATE TABLE IF NOT EXISTS product_images (
    id           INT UNSIGNED   AUTO_INCREMENT PRIMARY KEY,
    product_id   INT UNSIGNED   NOT NULL,
    image_path   VARCHAR(255)   NOT NULL,
    is_primary   TINYINT(1)     NOT NULL DEFAULT 0,
    sort_order   TINYINT UNSIGNED NOT NULL DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 4. USERS ──────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id          INT UNSIGNED   AUTO_INCREMENT PRIMARY KEY,
    full_name   VARCHAR(100)   NOT NULL,
    email       VARCHAR(150)   NOT NULL UNIQUE,
    password    VARCHAR(255)   NOT NULL COMMENT 'Lưu bằng password_hash()',
    phone       VARCHAR(15)    NULL,
    address     TEXT           NULL,
    role        ENUM('customer','admin') NOT NULL DEFAULT 'customer',
    created_at  TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 5. ORDERS ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS orders (
    id            INT UNSIGNED   AUTO_INCREMENT PRIMARY KEY,
    user_id       INT UNSIGNED   NULL COMMENT 'NULL = đặt không cần tài khoản',
    full_name     VARCHAR(100)   NOT NULL,
    phone         VARCHAR(15)    NOT NULL,
    province      VARCHAR(100)   NOT NULL,
    address       TEXT           NOT NULL,
    note          TEXT           NULL,
    subtotal      DECIMAL(12,0)  NOT NULL,
    shipping_fee  DECIMAL(8,0)   NOT NULL DEFAULT 0,
    total         DECIMAL(12,0)  NOT NULL,
    status        ENUM('pending','confirmed','shipping','delivered','cancelled')
                  NOT NULL DEFAULT 'pending',
    created_at    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 6. ORDER_ITEMS ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS order_items (
    id               INT UNSIGNED   AUTO_INCREMENT PRIMARY KEY,
    order_id         INT UNSIGNED   NOT NULL,
    product_id       INT UNSIGNED   NULL,
    product_name     VARCHAR(255)   NOT NULL COMMENT 'Snapshot tên lúc đặt',
    quantity         SMALLINT UNSIGNED NOT NULL,
    price_at_order   DECIMAL(10,0)  NOT NULL COMMENT 'Snapshot giá lúc đặt',
    FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- DỮ LIỆU MẪU (SEED DATA)
-- ============================================================

-- Tầng 1: Tỷ lệ
INSERT INTO categories (name, slug, parent_id, type, sort_order) VALUES
('1/144 Scale', '1-144', NULL, 'scale', 1),
('1/100 Scale', '1-100', NULL, 'scale', 2),
('1/60 Scale',  '1-60',  NULL, 'scale', 3),
('Dụng cụ',     'tools', NULL, 'tool',  4),
('Phụ kiện',    'accessories', NULL, 'accessory', 5);

-- Tầng 2: Dòng (grade) — con của tỷ lệ
INSERT INTO categories (name, slug, parent_id, type, sort_order) VALUES
('HG (High Grade)',     'hg',            1, 'grade', 1),
('RG (Real Grade)',     'rg',            1, 'grade', 2),
('Entry Grade',         'entry-grade',   1, 'grade', 3),
('MG (Master Grade)',   'mg',            2, 'grade', 1),
('MG Ver.Ka',           'mg-verka',      2, 'grade', 2),
('PG (Perfect Grade)',  'pg',            3, 'grade', 1),
('PG Unleashed',        'pg-unleashed',  3, 'grade', 2);

-- Sản phẩm mẫu (10 sản phẩm)
INSERT INTO products (name, slug, price, stock, category_id, scale, grade, series, description, weight_gram) VALUES
('HG 1/144 RX-78-2 Gundam (Revive Edition)',
 'hg-rx78-2-revive', 250000, 15, 6, '1/144', 'HG',
 'Mobile Suit Gundam',
 'Phiên bản Revive 2015 với khuôn đúc mới, chi tiết sắc nét hơn bản gốc. Phù hợp cho người mới bắt đầu chơi Gunpla.', 180),

('RG 1/144 Unicorn Gundam',
 'rg-unicorn', 420000, 8, 7, '1/144', 'RG',
 'Mobile Suit Gundam Unicorn',
 'Real Grade với khung nội thất Inner Frame chi tiết, phần Psychoframe màu vàng nổi bật. Có thể chuyển đổi giữa Unicorn Mode và Destroy Mode.', 210),

('MG 1/100 Freedom Gundam Ver.2.0',
 'mg-freedom-v2', 680000, 5, 9, '1/100', 'MG',
 'Mobile Suit Gundam SEED',
 'Phiên bản Ver.2.0 cải tiến toàn diện từ khớp vai đến cánh tên lửa METEOR. Khớp cánh có thể mở hoàn toàn 180 độ.', 420),

('HG 1/144 Zaku II MS-06F',
 'hg-zaku-ii', 220000, 20, 6, '1/144', 'HG',
 'Mobile Suit Gundam',
 'Biểu tượng Zeon với ống nhiệt trên vai, súng trường Zaku Machine Gun và Shield tặng kèm.', 160),

('MG 1/100 Wing Gundam Zero EW',
 'mg-wing-zero-ew', 720000, 3, 9, '1/100', 'MG',
 'Mobile Suit Gundam Wing: Endless Waltz',
 'Thiết kế cánh thiên thần biểu tượng của dòng Wing. Phiên bản EW (Endless Waltz) với cánh kiếm đôi.', 480),

('RG 1/144 Gundam Exia',
 'rg-exia', 390000, 0, 7, '1/144', 'RG',
 'Mobile Suit Gundam 00',
 'Real Grade Exia với GN Drive phát sáng xanh, 7 thanh kiếm GN Sword kèm theo. Hiện tạm hết hàng.', 200),

('HG 1/144 Gundam Barbatos Lupus Rex',
 'hg-barbatos-lupus-rex', 280000, 12, 6, '1/144', 'HG',
 'Mobile Suit Gundam: Iron-Blooded Orphans',
 'Hình dạng hung hãn với Tail Blade khổng lồ và cặp búa mace. Thiết kế phi đối xứng độc đáo.', 195),

('MG Ver.Ka 1/100 Sinanju',
 'mg-verka-sinanju', 1480000, 2, 10, '1/100', 'MG Ver.Ka',
 'Mobile Suit Gundam Unicorn',
 'Thiết kế đỏ thắm huyền thoại của Neo Zeon. Ver.Ka với các chi tiết khắc laser trên toàn thân và decal kim loại cao cấp.', 620),

('PG 1/60 Strike Freedom Gundam',
 'pg-strike-freedom', 3200000, 2, 11, '1/60', 'PG',
 'Mobile Suit Gundam SEED Destiny',
 'Perfect Grade với khung nội thất đầy đủ, cánh tên lửa METEOR có thể mở 180 độ. Gồm LED unit cho mắt và phần Dragoon phát sáng.', 1200),

('HG 1/144 Gundam Aerial',
 'hg-aerial', 310000, 18, 6, '1/144', 'HG',
 'Mobile Suit Gundam: The Witch from Mercury',
 'Gundam thế hệ mới từ series Witch from Mercury 2022. Thiết kế mảnh mai, cánh Permet Scale độc đáo.', 175);

-- Tài khoản Admin mặc định
-- Email: admin@gunplashop.vn
-- Password: Admin@123  (đã hash bằng password_hash với BCRYPT)
INSERT INTO users (full_name, email, password, role) VALUES
('Quản trị viên',
 'admin@gunplashop.vn',
 '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'admin');

-- Đơn hàng mẫu để xem Admin Dashboard
INSERT INTO orders (user_id, full_name, phone, province, address, subtotal, shipping_fee, total, status) VALUES
(NULL, 'Nguyễn Văn An', '0901234567', 'Bình Dương', '15 Đường CMT8, Phú Cường, Thủ Dầu Một', 670000, 15000, 685000, 'delivered'),
(NULL, 'Trần Thị Bình', '0912345678', 'TP. Hồ Chí Minh', '42 Lê Văn Việt, Thủ Đức', 420000, 20000, 440000, 'shipping'),
(NULL, 'Lê Minh Châu', '0923456789', 'Hà Nội', '88 Nguyễn Trãi, Thanh Xuân', 3200000, 35000, 3235000, 'pending');

INSERT INTO order_items (order_id, product_id, product_name, quantity, price_at_order) VALUES
(1, 3, 'MG 1/100 Freedom Gundam Ver.2.0', 1, 680000),
(2, 2, 'RG 1/144 Unicorn Gundam', 1, 420000),
(3, 9, 'PG 1/60 Strike Freedom Gundam', 1, 3200000);
