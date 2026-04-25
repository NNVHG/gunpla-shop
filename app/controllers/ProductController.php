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

class ProductController
{
    private Product  $productModel;
    private Category $categoryModel;

    public function __construct()
    {
        $this->productModel  = new Product();
        $this->categoryModel = new Category();
    }

    // ─────────────────────────────────────────────
    //  TRANG CHỦ
    // ─────────────────────────────────────────────

    public function home(): void
    {
        $data = [
            'title'      => 'GUNPLA SHOP — Mô Hình Lắp Ráp Chính Hãng',
            'featured'   => $this->productModel->getFeatured(8),
            'categories' => $this->categoryModel->getTopLevel(),
            'newArrivals'=> $this->productModel->getAll([], 'newest', 1, 8)['items'],
        ];
        $this->render('home/index', $data);
    }

    // ─────────────────────────────────────────────
    //  DANH SÁCH SẢN PHẨM + LỌC
    // ─────────────────────────────────────────────

    public function index(): void
    {
        // Lấy tham số lọc từ URL
        $filters = [];
        if (!empty($_GET['grade']))       $filters['grade']       = htmlspecialchars($_GET['grade']);
        if (!empty($_GET['scale']))       $filters['scale']       = htmlspecialchars($_GET['scale']);
        if (!empty($_GET['series']))      $filters['series']      = htmlspecialchars($_GET['series']);
        if (!empty($_GET['category_id'])) $filters['category_id'] = (int) $_GET['category_id'];
        if (!empty($_GET['search']))      $filters['search']      = htmlspecialchars($_GET['search']);
        
        $sort = in_array($_GET['sort'] ?? '', ['newest','price_asc','price_desc','bestseller']) ? $_GET['sort'] : 'newest';
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $result  = $this->productModel->getAll($filters, $sort, $page, 12);
        
        $categories = $this->categoryModel->getTopLevel();
        $groupedCategories = $this->categoryModel->getGroupedByType(); // MỚI THÊM: Lấy danh mục nhóm theo type

        $data = [
            'title'      => 'Danh sách sản phẩm — GUNPLA SHOP',
            'products'   => $result['items'],
            'total'      => $result['total'],
            'pages'      => $result['pages'],
            'page'       => $result['page'],
            'filters'    => $filters,
            'sort'       => $sort,
            'categories' => $categories,
            'groupedCategories' => $groupedCategories, // MỚI THÊM: Đẩy sang View
        ];

        if ($this->isAjax()) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($result);
            exit;
        }

        $this->render('products/index', $data);
    }

    // ─────────────────────────────────────────────
    //  CHI TIẾT SẢN PHẨM
    // ─────────────────────────────────────────────

    /**
     * @param string|null $param  Có thể là ID (số) hoặc slug
     */
    public function detail(?string $param): void
    {
        if (!$param) {
            $this->redirect('/products');
            return;
        }

        // Nếu là số → lấy theo ID, ngược lại → lấy theo slug
        $product = is_numeric($param)
            ? $this->productModel->getById((int) $param)
            : $this->productModel->getBySlug($param);

        if (!$product) {
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Không tìm thấy sản phẩm']);
            return;
        }

        // Sản phẩm liên quan (cùng grade)
        $related = $this->productModel->getAll(
            ['grade' => $product['grade']],
            'newest', 1, 4
        )['items'];
        // Loại bỏ sản phẩm hiện tại khỏi danh sách liên quan
        $related = array_filter($related, fn($p) => $p['id'] !== $product['id']);

        $data = [
            'title'   => $product['name'] . ' — GUNPLA SHOP',
            'product' => $product,
            'related' => array_values($related),
        ];

        $this->render('products/detail', $data);
    }

    // ─────────────────────────────────────────────
    //  TÌM KIẾM (AJAX)
    // ─────────────────────────────────────────────

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

    // ─────────────────────────────────────────────
    //  HELPER
    // ─────────────────────────────────────────────

    /**
     * Render view — nạp layout + nội dung trang
     *
     * @param string $view  Đường dẫn view tương đối, ví dụ: 'products/index'
     * @param array  $data  Biến truyền vào view
     */
    private function render(string $view, array $data = []): void
    {
        // Giải nén mảng thành biến (extract) để dùng trong view như $title, $products...
        extract($data);
        $viewFile = APP_PATH . '/views/' . $view . '.php';

        ob_start();
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "<p>View không tìm thấy: $viewFile</p>";
        }
        $content = ob_get_clean();

        // Nhúng vào layout chính
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
}
