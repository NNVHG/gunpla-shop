<?php
/**
 * config/database.php
 * Kết nối MySQL bằng PDO (PHP Data Objects)
 *
 * Thay đổi DB_USER và DB_PASS theo cấu hình XAMPP/Laragon của bạn.
 */

define('DB_HOST',    'localhost');
define('DB_NAME',    'gunpla_shop');
define('DB_USER',    'root');
define('DB_PASS',    '');          // XAMPP mặc định để trống; Laragon mặc định là 'root'
define('DB_CHARSET', 'utf8mb4');

/**
 * Trả về kết nối PDO — chỉ tạo 1 lần (Singleton pattern)
 */
function getDB(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST, DB_NAME, DB_CHARSET
        );
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // Ném exception khi lỗi SQL
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // Trả về array kết hợp
            PDO::ATTR_EMULATE_PREPARES   => false,                     // Dùng prepared statements thật
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Hiển thị lỗi thân thiện thay vì lộ thông tin server
            if (defined('APP_DEBUG') && APP_DEBUG) {
                die('<pre style="color:red;padding:20px">Lỗi kết nối DB: ' . $e->getMessage() . '</pre>');
            }
            die('<p style="color:red;padding:20px;font-family:sans-serif">Không thể kết nối cơ sở dữ liệu. Vui lòng thử lại sau.</p>');
        }
    }

    return $pdo;
}
