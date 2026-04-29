<?php
class FavoriteController extends BaseController { // Thay BaseController bằng class cha của bạn nếu có
    private $favoriteModel;

    public function __construct() {
        global $pdo; // Thay đổi tùy theo cách gọi DB của bạn
        $this->favoriteModel = new Favorite($pdo);
    }

    // Xử lý AJAX thêm/bỏ yêu thích
    public function toggle() {
        header('Content-Type: application/json');
        
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'unauthorized', 'message' => 'Vui lòng đăng nhập để sử dụng tính năng này.']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

        if ($productId > 0) {
            $result = $this->favoriteModel->toggle($userId, $productId);
            echo json_encode($result);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không hợp lệ.']);
        }
    }

    // Trang hiển thị danh sách yêu thích
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "/login");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $favorites = $this->favoriteModel->getUserFavorites($userId);

        $this->render('users/favorites', [
            'title' => 'Sản phẩm yêu thích — GUNPLA SHOP',
            'favorites' => $favorites
        ]);
    }
}