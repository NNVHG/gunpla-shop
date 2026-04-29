<?php
class Favorite {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    // Toggle yêu thích: Nếu đã có thì xóa, chưa có thì thêm
    public function toggle($userId, $productId) {
        // Kiểm tra xem đã yêu thích chưa
        $stmt = $this->db->prepare("SELECT 1 FROM favorites WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        $exists = $stmt->fetchColumn();

        if ($exists) {
            // Đã có -> Xóa (Bỏ yêu thích)
            $stmt = $this->db->prepare("DELETE FROM favorites WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$userId, $productId]);
            return ['status' => 'removed'];
        } else {
            // Chưa có -> Thêm (Yêu thích)
            $stmt = $this->db->prepare("INSERT INTO favorites (user_id, product_id) VALUES (?, ?)");
            $stmt->execute([$userId, $productId]);
            return ['status' => 'added'];
        }
    }

    // Lấy danh sách ID sản phẩm user đã yêu thích (để hiển thị nút tim đỏ)
    public function getUserFavoriteIds($userId) {
        $stmt = $this->db->prepare("SELECT product_id FROM favorites WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Lấy toàn bộ thông tin sản phẩm yêu thích của user
    public function getUserFavorites($userId) {
        $sql = "SELECT p.* FROM products p 
                INNER JOIN favorites f ON p.id = f.product_id 
                WHERE f.user_id = ? 
                ORDER BY f.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}