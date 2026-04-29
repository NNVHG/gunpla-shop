<?php
declare(strict_types=1);

class FavoriteController
{
    private Favorite $favoriteModel;

    public function __construct() {
        $this->favoriteModel = new Favorite(getDB());
    }

    public function toggle(): void {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user']['id'])) {
            echo json_encode(['status' => 'unauthorized', 'message' => 'Vui lòng đăng nhập để lưu sản phẩm.']);
            exit;
        }

        $userId = (int) $_SESSION['user']['id'];
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

        if ($productId > 0) {
            $result = $this->favoriteModel->toggle($userId, $productId);
            echo json_encode($result);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không hợp lệ.']);
        }
    }
}