<?php
/**
 * database/update_db_defects_notifications.php
 * Script khởi tạo bảng defect_reports và notifications trong CSDL.
 */

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH',  BASE_PATH . '/app');

require_once BASE_PATH . '/vendor/autoload.php';

// Tải cấu hình .env
if (file_exists(BASE_PATH . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
    $dotenv->load();
}

require_once BASE_PATH . '/config/database.php';

try {
    $db = getDB();
    echo "Kết nối cơ sở dữ liệu thành công.\n";

    // 1. Tạo bảng notifications
    $db->exec("
        CREATE TABLE IF NOT EXISTS notifications (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            title VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            link VARCHAR(255) NULL,
            is_read TINYINT(1) NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            KEY idx_notifications_user (user_id),
            KEY idx_notifications_unread (user_id, is_read)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Đã tạo bảng `notifications` (hoặc bảng đã tồn tại).\n";

    // 2. Tạo bảng defect_reports
    $db->exec("
        CREATE TABLE IF NOT EXISTS defect_reports (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            product_id INT UNSIGNED NOT NULL,
            order_id INT UNSIGNED NOT NULL,
            description TEXT NOT NULL,
            image_proof VARCHAR(255) NOT NULL,
            video_proof VARCHAR(255) NOT NULL,
            status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
            admin_comment TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            KEY idx_defect_reports_user (user_id),
            KEY idx_defect_reports_product (product_id),
            KEY idx_defect_reports_order (order_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Đã tạo bảng `defect_reports` (hoặc bảng đã tồn tại).\n";

    echo "Khởi tạo cơ sở dữ liệu HOÀN TẤT.\n";

} catch (Exception $e) {
    die("Lỗi thực thi: " . $e->getMessage() . "\n");
}
