<?php
class FavoriteController
{
    private Favorite $favoriteModel;

    public function __construct() {
        $this->favoriteModel = new Favorite(getDB()); // Chuẩn hóa getDB()
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

    public function index(): void {
        if (!isset($_SESSION['user']['id'])) {
            header("Location: " . BASE_URL . "/user/login");
            exit;
        }

        $userId = (int) $_SESSION['user']['id'];
        $favorites = $this->favoriteModel->getUserFavorites($userId);

        // Sử dụng view layouts/main (Giống với ProductController)
        $data = ['title' => 'Sản phẩm yêu thích — GUNPLA SHOP', 'favorites' => $favorites];
        
        extract($data);
        ob_start();
        include APP_PATH . '/views/user/favorites.php'; // Bạn cần tạo view này nếu chưa có
        $content = ob_get_clean();
        include APP_PATH . '/views/layouts/main.php';
    }
}