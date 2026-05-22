<?php
/**
 * database/update_db_comparison.php
 * Script nâng cấp cấu trúc CSDL và khởi tạo dữ liệu mẫu cho tính năng So sánh sản phẩm.
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

    // 1. Kiểm tra và thêm cột parts_count
    $checkParts = $db->query("SHOW COLUMNS FROM products LIKE 'parts_count'");
    if ($checkParts->rowCount() === 0) {
        $db->exec("ALTER TABLE products ADD COLUMN parts_count INT UNSIGNED NULL DEFAULT NULL AFTER weight_gram");
        echo "Đã thêm cột `parts_count` vào bảng `products`.\n";
    } else {
        echo "Cột `parts_count` đã tồn tại.\n";
    }

    // 2. Kiểm tra và thêm cột difficulty
    $checkDiff = $db->query("SHOW COLUMNS FROM products LIKE 'difficulty'");
    if ($checkDiff->rowCount() === 0) {
        $db->exec("ALTER TABLE products ADD COLUMN difficulty VARCHAR(50) NULL DEFAULT NULL AFTER parts_count");
        echo "Đã thêm cột `difficulty` vào bảng `products`.\n";
    } else {
        echo "Cột `difficulty` đã tồn tại.\n";
    }

    // 3. Cập nhật dữ liệu mẫu cho các sản phẩm hiện có dựa vào Grade
    $stmt = $db->query("SELECT id, grade, category_id, name FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $updateStmt = $db->prepare("UPDATE products SET parts_count = :parts_count, difficulty = :difficulty WHERE id = :id");

    $updatedCount = 0;
    foreach ($products as $product) {
        $id = (int)$product['id'];
        $grade = $product['grade'] ? strtoupper(trim($product['grade'])) : '';
        
        $parts = null;
        $difficulty = null;

        // Nếu sản phẩm thuộc dòng Gunpla (có Grade hoặc Scale, hoặc dựa vào tên chứa từ khóa)
        if (!empty($grade)) {
            switch ($grade) {
                case 'SD':
                case 'EG':
                case 'ENTRY GRADE':
                case 'SUPER DEFORMED':
                    $parts = rand(40, 80);
                    $difficulty = 'Dễ';
                    break;
                case 'HG':
                case 'HIGH GRADE':
                    $parts = rand(120, 220);
                    $difficulty = 'Trung bình';
                    break;
                case 'RG':
                case 'REAL GRADE':
                    $parts = rand(250, 350);
                    $difficulty = 'Khó';
                    break;
                case 'MG':
                case 'MASTER GRADE':
                    $parts = rand(280, 450);
                    $difficulty = 'Khó';
                    break;
                case 'PG':
                case 'PERFECT GRADE':
                    $parts = rand(600, 1100);
                    $difficulty = 'Rất khó';
                    break;
                default:
                    $parts = rand(100, 200);
                    $difficulty = 'Trung bình';
                    break;
            }
        } else {
            // Thử đoán theo tên nếu không có grade rõ ràng nhưng là robot/gundam
            $nameLower = strtolower($product['name']);
            if (strpos($nameLower, 'hg') !== false) {
                $parts = rand(120, 220);
                $difficulty = 'Trung bình';
            } elseif (strpos($nameLower, 'rg') !== false || strpos($nameLower, 'mg') !== false) {
                $parts = rand(250, 450);
                $difficulty = 'Khó';
            } elseif (strpos($nameLower, 'pg') !== false) {
                $parts = rand(600, 1100);
                $difficulty = 'Rất khó';
            } elseif (strpos($nameLower, 'sd') !== false || strpos($nameLower, 'eg') !== false) {
                $parts = rand(40, 80);
                $difficulty = 'Dễ';
            } else {
                // Các sản phẩm không phải mô hình (dụng cụ, sơn, decal...)
                $parts = 0;
                $difficulty = 'Dễ';
            }
        }

        // Thực hiện cập nhật
        $updateStmt->execute([
            ':parts_count' => $parts,
            ':difficulty'  => $difficulty,
            ':id'          => $id
        ]);
        $updatedCount++;
    }

    echo "Đã cập nhật dữ liệu mẫu thành công cho $updatedCount sản phẩm.\n";
    echo "Nâng cấp cơ sở dữ liệu HOÀN TẤT.\n";

} catch (Exception $e) {
    die("Lỗi thực thi: " . $e->getMessage() . "\n");
}
