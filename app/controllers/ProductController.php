<?php

/**
 * app/controllers/ProductController.php
 * Xử lý tất cả request liên quan đến sản phẩm
 *
 * Routes:
 * GET /                          → home()
 * GET /products                  → index()
 * GET /products/detail/{id}      → detail($id)
 * GET /products/search           → search()
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\Review;

class ProductController
{
    private Product  $productModel;
    private Category $categoryModel;
    private Review   $reviewModel;

    public function __construct()
    {
        $this->productModel  = new Product();
        $this->categoryModel = new Category();
        $this->reviewModel   = new Review();
    }

    public function home(): void
    {
        $data = [
            'title'            => 'GUNPLA SHOP — Mô Hình Lắp Ráp Chính Hãng',
            'featured'         => $this->productModel->getFeatured(8),
            'categories'       => $this->categoryModel->getTopLevel(),
            'newArrivals'      => $this->productModel->getAll([], 'newest', 1, 8)['items'],
            'beginnerChoices'  => $this->productModel->getAll(['grade' => ['SD', 'EG'], 'stock_status' => 'in_stock'], 'newest', 1, 4)['items'],
            'hgBestSellers'    => $this->productModel->getAll(['grade' => 'HG', 'stock_status' => 'in_stock'], 'bestseller', 1, 4)['items'],
            'essentialTools'   => $this->productModel->getAll(['group' => 'tools', 'stock_status' => 'in_stock'], 'newest', 1, 4)['items'],
            'favoriteIds'      => $this->getFavoriteIds(),
        ];
        $this->render('home/index', $data);
    }

    public function index(): void
    {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 12;

        // Nhận diện nhóm sản phẩm và các tham số lọc bổ sung (Scale, Series)
        $group      = $_GET['group'] ?? 'all'; 
        $grade      = $_GET['grade'] ?? null;
        $scale      = $_GET['scale'] ?? null;
        $series     = $_GET['series'] ?? null;
        $categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
        $search     = isset($_GET['search']) ? trim($_GET['search']) : null;

        // Gọi hàm phân tách danh mục động 
        $categories = $this->categoryModel->getByGroup($group);

        $minPrice    = $_GET['min_price'] ?? null;
        $maxPrice    = $_GET['max_price'] ?? null;
        $stockStatus = $_GET['stock_status'] ?? null;

        // Đóng gói mảng tham số lọc ĐẦY ĐỦ để truyền vào Model dữ liệu
        $filters = [
            'group'        => $group,
            'grade'        => $grade,
            'scale'        => $scale,
            'series'       => $series,
            'category_id'  => $categoryId,
            'min_price'    => $minPrice,
            'max_price'    => $maxPrice,
            'stock_status' => $stockStatus,
            'search'       => $search,
        ];

        $sort = in_array($_GET['sort'] ?? '', ['newest', 'price_asc', 'price_desc', 'bestseller'])
            ? $_GET['sort'] : 'newest';

        $data = $this->productModel->getFilteredProducts($filters, $sort, $page, $perPage);

        // Thiết lập tiêu đề động tương ứng cho từng trang điều hướng chuyên nghiệp
        $title = 'Tất Cả Sản Phẩm';
        if ($group === 'gunpla') $title = 'Mô Hình Gunpla Lắp Ráp Chính Hãng';
        if ($group === 'tools')  $title = 'Dụng Cụ & Hóa Chất Phụ Trợ';

        $this->render('products/index', [
            'title'        => $title,
            'products'     => $data['items'],
            'total'        => $data['total'],
            'pages'        => $data['pages'],
            'page'         => $page,
            'filters'      => $filters,
            'sort'         => $sort,
            'categories'   => $categories,
            'currentGroup' => $group,
            'currentGrade' => $grade,
            'currentCat'   => $categoryId,
            'favoriteIds'  => $this->getFavoriteIds(),
        ]);
    }

    public function detail(?string $param): void
    {
        if (!$param) {
            $this->redirect('/products');
            return;
        }

        $product = is_numeric($param)
            ? $this->productModel->getById((int) $param)
            : $this->productModel->getBySlug($param);

        if (!$product) {
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Không tìm thấy sản phẩm']);
            return;
        }

        $related = $this->productModel->getAll(
            ['grade' => $product['grade']],
            'newest',
            1,
            4
        )['items'];
        $related = array_filter($related, fn($p) => $p['id'] !== $product['id']);

        $reviews    = $this->reviewModel->getByProduct($product['id']);
        $ratingInfo = $this->reviewModel->getAvgRating($product['id']);
        $hasReviewed = !empty($_SESSION['user']['id'])
            && $this->reviewModel->hasReviewed($product['id'], (int) $_SESSION['user']['id']);

        $data = [
            'title'        => $product['name'] . ' — GUNPLA SHOP',
            'product'      => $product,
            'related'      => array_values($related),
            'reviews'      => $reviews,
            'avgRating'    => $ratingInfo['avg'],
            'totalReviews' => $ratingInfo['total'],
            'hasReviewed'  => $hasReviewed,
        ];

        $this->render('products/detail', $data);
    }

    public function compare(): void
    {
        $idsStr = $_GET['ids'] ?? '';
        $ids = array_filter(array_map('intval', explode(',', $idsStr)));
        // Giới hạn tối đa 3 sản phẩm để so sánh
        $ids = array_slice($ids, 0, 3);

        $products = [];
        foreach ($ids as $id) {
            $product = $this->productModel->getById($id);
            if ($product) {
                // Lấy đánh giá trung bình
                $ratingInfo = $this->reviewModel->getAvgRating($product['id']);
                $product['avg_rating'] = $ratingInfo['avg'];
                $product['total_reviews'] = $ratingInfo['total'];
                $products[] = $product;
            }
        }

        $this->render('products/compare', [
            'title' => 'So Sánh Sản Phẩm Gunpla',
            'products' => $products,
        ]);
    }

    public function submitReview(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/products');
            return;
        }

        if (empty($_SESSION['user']['id'])) {
            $this->redirect(BASE_URL . '/user/login');
            return;
        }

        $productId = (int) ($_POST['product_id'] ?? 0);
        $rating    = (int) ($_POST['rating']     ?? 0);
        $comment   = trim($_POST['comment']      ?? '');
        $userId    = (int) $_SESSION['user']['id'];

        $errors = [];
        if ($productId <= 0)               $errors[] = 'Sản phẩm không hợp lệ.';
        if ($rating < 1 || $rating > 5)    $errors[] = 'Vui lòng chọn số sao (1–5).';
        if (strlen($comment) < 10)         $errors[] = 'Nội dung đánh giá phải từ 10 ký tự.';
        if ($this->reviewModel->hasReviewed($productId, $userId)) {
            $errors[] = 'Bạn đã đánh giá sản phẩm này rồi.';
        }

        $redirectUrl = BASE_URL . '/products/detail/' . $productId;

        if ($errors) {
            $_SESSION['review_errors'] = $errors;
            $this->redirect($redirectUrl . '#reviews');
            return;
        }

        $this->reviewModel->create($productId, $userId, $rating, $comment);
        $_SESSION['review_success'] = 'Cảm ơn bạn đã đánh giá sản phẩm!';
        $this->redirect($redirectUrl . '#reviews');
    }

    public function reportdefect(): void
    {
        if (empty($_SESSION['user']['id'])) {
            $this->redirect(BASE_URL . '/user/login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/products');
            return;
        }

        $userId = (int) $_SESSION['user']['id'];
        $productId = (int) ($_POST['product_id'] ?? 0);
        $orderId = (int) ($_POST['order_id'] ?? 0);
        $description = trim($_POST['description'] ?? '');

        $errors = [];
        if ($productId <= 0) {
            $errors[] = 'Sản phẩm không hợp lệ.';
        }
        if ($orderId <= 0) {
            $errors[] = 'Đơn hàng không hợp lệ.';
        }
        if (strlen($description) < 15) {
            $errors[] = 'Mô tả chi tiết lỗi phải từ 15 ký tự trở lên.';
        }

        // Kiểm tra đơn hàng hợp lệ của user và chứa sản phẩm này
        $db = getDB();
        $stmt = $db->prepare("
            SELECT 1 FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            WHERE o.id = ? AND o.user_id = ? AND oi.product_id = ? AND o.status = 'delivered'
        ");
        $stmt->execute([$orderId, $userId, $productId]);
        if (!$stmt->fetchColumn()) {
            $errors[] = 'Đơn hàng này không hợp lệ hoặc chưa được giao thành công.';
        }

        // Kiểm tra xem đã báo cáo lỗi cho đơn hàng/sản phẩm này chưa
        $stmt = $db->prepare("SELECT 1 FROM defect_reports WHERE order_id = ? AND product_id = ?");
        $stmt->execute([$orderId, $productId]);
        if ($stmt->fetchColumn()) {
            $errors[] = 'Bạn đã gửi báo cáo lỗi cho sản phẩm này trong đơn hàng được chọn rồi.';
        }

        $imageProofPath = '';
        $videoProofPath = '';

        // Xử lý upload hình ảnh minh chứng
        if (!isset($_FILES['image_proof']) || $_FILES['image_proof']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Vui lòng tải lên hình ảnh minh chứng lỗi.';
        } else {
            $imgName = $_FILES['image_proof']['name'];
            $imgExt = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
            if (!in_array($imgExt, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                $errors[] = 'Hình ảnh minh chứng phải là định dạng JPG, JPEG, PNG, WEBP hoặc GIF.';
            } else {
                $uploadDir = BASE_PATH . '/public/uploads/defects/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $newImgName = 'img_' . time() . '_' . uniqid() . '.' . $imgExt;
                if (move_uploaded_file($_FILES['image_proof']['tmp_name'], $uploadDir . $newImgName)) {
                    $imageProofPath = 'public/uploads/defects/' . $newImgName;
                } else {
                    $errors[] = 'Không thể tải lên hình ảnh minh chứng.';
                }
            }
        }

        // Xử lý upload video minh chứng
        if (!isset($_FILES['video_proof']) || $_FILES['video_proof']['error'] !== UPLOAD_ERR_OK) {
            if (isset($_FILES['video_proof']) && $_FILES['video_proof']['error'] === UPLOAD_ERR_INI_SIZE) {
                $errors[] = 'Video minh chứng vượt quá kích thước tối đa cho phép của máy chủ (dưới 50MB).';
            } else {
                $errors[] = 'Vui lòng tải lên video minh chứng không cắt ghép.';
            }
        } else {
            $vidName = $_FILES['video_proof']['name'];
            $vidExt = strtolower(pathinfo($vidName, PATHINFO_EXTENSION));
            if (!in_array($vidExt, ['mp4', 'mov', 'avi', 'mkv', 'webm'])) {
                $errors[] = 'Video minh chứng phải là định dạng MP4, MOV, AVI, MKV hoặc WEBM.';
            } else {
                $uploadDir = BASE_PATH . '/public/uploads/defects/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $newVidName = 'vid_' . time() . '_' . uniqid() . '.' . $vidExt;
                if (move_uploaded_file($_FILES['video_proof']['tmp_name'], $uploadDir . $newVidName)) {
                    $videoProofPath = 'public/uploads/defects/' . $newVidName;
                } else {
                    $errors[] = 'Không thể tải lên video minh chứng.';
                }
            }
        }

        $redirectUrl = BASE_URL . '/orders/detail/' . $orderId;

        if (!empty($errors)) {
            // Xóa file đã upload nếu có lỗi
            if ($imageProofPath && file_exists(BASE_PATH . '/' . $imageProofPath)) {
                @unlink(BASE_PATH . '/' . $imageProofPath);
            }
            if ($videoProofPath && file_exists(BASE_PATH . '/' . $videoProofPath)) {
                @unlink(BASE_PATH . '/' . $videoProofPath);
            }

            $_SESSION['defect_errors'] = $errors;
            $this->redirect($redirectUrl);
            return;
        }

        // Lưu vào CSDL
        require_once APP_PATH . '/Models/DefectReport.php';
        $reportModel = new \App\Models\DefectReport();
        $reportModel->create($userId, $productId, $orderId, $description, $imageProofPath, $videoProofPath);

        $_SESSION['defect_success'] = 'Báo cáo thiếu đồ / lỗi sản phẩm đã được gửi thành công! Đội ngũ Admin sẽ xem xét và phản hồi sớm.';
        $this->redirect($redirectUrl);
    }

    public function search(): void
    {
        $query  = htmlspecialchars(trim($_GET['q'] ?? ''));
        $result = $this->productModel->getAll(['search' => $query], 'newest', 1, 20);

        require_once APP_PATH . '/Models/News.php';
        $newsModel = new \App\Models\News();
        $newsResult = $newsModel->search($query, 3);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'query'   => $query,
            'count'   => $result['total'],
            'results' => $result['items'],
            'news'    => $newsResult,
        ]);
        exit;
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);
        $viewFile = APP_PATH . '/views/' . $view . '.php';

        ob_start();
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "<p>View không tìm thấy: $viewFile</p>";
        }
        $content = ob_get_clean();

        include APP_PATH . '/views/layouts/main.php';
    }

    private function isAjax(): bool
    {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
    }

    private function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    private function getFavoriteIds(): array
    {
        if (isset($_SESSION['user']['id'])) {
            require_once APP_PATH . '/models/Favorite.php';
            $favModel = new Favorite(getDB());
            return $favModel->getUserFavoriteIds($_SESSION['user']['id']);
        }
        return [];
    }
}