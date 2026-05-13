<?php

/**
 * app/controllers/ProductController.php
 * Xử lý tất cả request liên quan đến sản phẩm
 *
 * Routes:
 *   GET /                          → home()
 *   GET /products                  → index()
 *   GET /products/detail/{id}      → detail($id)
 *   GET /products/search           → search()
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
            'title'      => 'GUNPLA SHOP — Mô Hình Lắp Ráp Chính Hãng',
            'featured'   => $this->productModel->getFeatured(8),
            'categories' => $this->categoryModel->getTopLevel(),
            'newArrivals' => $this->productModel->getAll([], 'newest', 1, 8)['items'],
            'favoriteIds' => $this->getFavoriteIds(),
        ];
        $this->render('home/index', $data);
    }

    public function index(): void
    {

        $filters = [];
        if (!empty($_GET['grade']))       $filters['grade']       = htmlspecialchars($_GET['grade']);
        if (!empty($_GET['scale']))       $filters['scale']       = htmlspecialchars($_GET['scale']);
        if (!empty($_GET['series']))      $filters['series']      = htmlspecialchars($_GET['series']);
        if (!empty($_GET['category_id'])) $filters['category_id'] = (int) $_GET['category_id'];
        if (!empty($_GET['search']))      $filters['search']      = htmlspecialchars($_GET['search']);
        if (!empty($_GET['type']))        $filters['type']        = htmlspecialchars($_GET['type']);

        $sort    = in_array($_GET['sort'] ?? '', ['newest', 'price_asc', 'price_desc', 'bestseller'])
            ? $_GET['sort'] : 'newest';
        $page    = max(1, (int) ($_GET['page'] ?? 1));

        $result  = $this->productModel->getAll($filters, $sort, $page, 12);

        $categories = $this->categoryModel->getTopLevel();
        $groupedCategories = $this->categoryModel->getGroupedByType();

        $data = [
            'title'      => 'Danh sách sản phẩm — GUNPLA SHOP',
            'products'   => $result['items'],
            'total'      => $result['total'],
            'pages'      => $result['pages'],
            'page'       => $result['page'],
            'filters'    => $filters,
            'sort'       => $sort,
            'categories' => $categories,
            'groupedCategories' => $groupedCategories,
            'favoriteIds' => $this->getFavoriteIds(),
        ];

        if ($this->isAjax()) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($result);
            exit;
        }

        $this->render('products/index', $data);
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
            'title'       => $product['name'] . ' — GUNPLA SHOP',
            'product'     => $product,
            'related'     => array_values($related),
            'reviews'     => $reviews,
            'avgRating'   => $ratingInfo['avg'],
            'totalReviews' => $ratingInfo['total'],
            'hasReviewed' => $hasReviewed,
        ];

        $this->render('products/detail', $data);
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

    public function search(): void
    {
        $query  = htmlspecialchars(trim($_GET['q'] ?? ''));
        $result = $this->productModel->getAll(['search' => $query], 'newest', 1, 20);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'query'   => $query,
            'count'   => $result['total'],
            'results' => $result['items'],
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
