<?php
/**
 * app/Models/Setting.php
 * Quản lý cấu hình toàn cục lưu trữ trong Cơ sở dữ liệu
 */

declare(strict_types=1);

namespace App\Models;

use PDO;

class Setting
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
        $this->ensureTableExists();
    }

    /**
     * Tự động tạo bảng settings nếu chưa tồn tại và nạp các giá trị mặc định
     */
    private function ensureTableExists(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS settings (
            `key` VARCHAR(50) PRIMARY KEY,
            `value` TEXT NOT NULL,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        
        $this->db->exec($sql);

        // Khởi tạo các cấu hình mặc định nếu chưa có
        $defaults = [
            'chatbot_enabled' => '1',
            'chatbot_ai_mode' => '1',      // '1' = Gemini AI, '0' = Rule-based thủ công
            'chatbot_gemini_key' => ''     // Key tùy chỉnh, trống thì dùng trong file .env
        ];

        foreach ($defaults as $key => $val) {
            $stmt = $this->db->prepare("INSERT IGNORE INTO settings (`key`, `value`) VALUES (?, ?)");
            $stmt->execute([$key, $val]);
        }
    }

    /**
     * Lấy giá trị của một cấu hình theo key
     */
    public function get(string $key, string $default = ''): string
    {
        $stmt = $this->db->prepare("SELECT `value` FROM settings WHERE `key` = ?");
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
        return $val !== false ? (string)$val : $default;
    }

    /**
     * Lấy toàn bộ cấu hình dưới dạng mảng key-value
     */
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT `key`, `value` FROM settings");
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
    }

    /**
     * Lưu hoặc cập nhật một cấu hình
     */
    public function set(string $key, string $value): bool
    {
        $stmt = $this->db->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = ?");
        return $stmt->execute([$key, $value, $value]);
    }
}
