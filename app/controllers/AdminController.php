<?php

/**
 * app/controllers/AdminController.php
 *
 * Routes:
 *   GET  /admin                      → dashboard()
 *   GET  /admin/products             → products()
 *   GET  /admin/products/create      → productCreate()
 *   POST /admin/products/store       → productStore()
 *   GET  /admin/products/edit/{id}   → productEdit($id)
 *   POST /admin/products/update/{id} → productUpdate($id)
 *   POST /admin/products/delete/{id} → productDelete($id)
 *   GET  /admin/orders               → orders()
 *   POST /admin/orders/status        → orderStatus()
 *   GET  /admin/inventory            → inventory()
 *   POST /admin/inventory/adjust     → inventoryAdjust()
 *   GET  /admin/login                → loginForm()
 *   POST /admin/login                → loginSubmit()
 *   GET  /admin/logout               → logout()
 */

declare(strict_types=1);

namespace App\Controllers; // Thêm dòng này

use App\Models\Product;    // Gọi Model Product
use App\Models\Category;  // Gọi Model Category
use App\Models\User;      // Gọi Model User
use App\Models\Order;     // Gọi Model Order

class AdminController
{
    private Product  $productModel;
    private Order    $orderModel;
    private Category $categoryModel;
    private User     $userModel;

    public function __construct()
    {
        $this->productModel  = new Product();
        $this->orderModel    = new Order();
        $this->categoryModel = new Category();
        $this->userModel     = new User();
    }

    // ────────────────────────────────────────────
    //  ĐIỀU HƯỚNG MẶC ĐỊNH KHI VÀO /ADMIN
    // ────────────────────────────────────────────
    public function index(): void
    {
        // Tự động chuyển hướng vào trang dashboard
        $this->redirect('/admin/dashboard');
    }

    // ────────────────────────────────────────────
    //  AUTH — Đăng nhập / Đăng xuất Admin
    // ────────────────────────────────────────────

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->loginSubmit();
        } else {
            $this->loginForm();
        }
    }

    public function loginForm(): void
    {
        if ($this->isAdmin()) {
            $this->redirect('/admin');
            return;
        }
        $this->renderAdmin('admin/login', ['title' => 'Admin Login — GUNPLA SHOP'], false);
    }

    public function loginSubmit(): void
    {
        $this->requirePost();
        $email    = trim($_POST['email']    ?? '');
        $password = trim($_POST['password'] ?? '');

        $user = $this->userModel->findByEmail($email);

        if ($user && $user['role'] === 'admin' && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id'    => $user['id'],
                'name'  => $user['full_name'],
                'email' => $user['email'],
                'role'  => $user['role'],
            ];
            $this->redirect('/admin');
        } else {
            $_SESSION['login_error'] = 'Email hoặc mật khẩu không đúng';
            $this->redirect('/admin/login');
        }
    }

    public function logout(): void
    {
        unset($_SESSION['user']);
        $this->redirect('/admin/login');
    }

    // ────────────────────────────────────────────
    //  DASHBOARD — Thống kê tổng quan
    // ────────────────────────────────────────────

    public function dashboard(): void
    {
        $this->requireAdmin();
        $db = getDB();

        // Thống kê nhanh
        $stats = [
            'total_products'  => (int) $db->query("SELECT COUNT(*) FROM products WHERE is_active=1")->fetchColumn(),
            'total_orders'    => (int) $db->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
            'pending_orders'  => (int) $db->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn(),
            'revenue_today'   => (int) $db->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE DATE(created_at)=CURDATE() AND status!='cancelled'")->fetchColumn(),
            'revenue_month'   => (int) $db->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW()) AND status!='cancelled'")->fetchColumn(),
            'low_stock'       => (int) $db->query("SELECT COUNT(*) FROM products WHERE stock<=5 AND is_active=1")->fetchColumn(),
        ];

        // Doanh thu 7 ngày gần nhất (cho chart)
        $revenueChart = $db->query("
            SELECT DATE(created_at) AS date, SUM(total) AS revenue
            FROM orders
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND status != 'cancelled'
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ")->fetchAll();

        // Đơn hàng mới nhất
        $latestOrders = $this->orderModel->getAll('', 1, 8)['items'];

        // Sản phẩm sắp hết hàng
        $lowStockProducts = $db->query("
            SELECT id, name, grade, scale, stock
            FROM products WHERE stock <= 5 AND is_active = 1 ORDER BY stock ASC LIMIT 8
        ")->fetchAll();

        $this->renderAdmin('admin/dashboard', compact(
            'stats',
            'revenueChart',
            'latestOrders',
            'lowStockProducts'
        ));
    }

    // ────────────────────────────────────────────
    //  QUẢN LÝ SẢN PHẨM
    // ────────────────────────────────────────────

    public function products(): void
    {
        $this->requireAdmin();
        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $search  = htmlspecialchars($_GET['search'] ?? '');
        $filters = $search ? ['search' => $search] : [];
        $result  = $this->productModel->getAll($filters, 'newest', $page, 15);

        $this->renderAdmin('admin/products/index', [
            'title'    => 'Quản lý sản phẩm',
            'products' => $result['items'],
            'total'    => $result['total'],
            'pages'    => $result['pages'],
            'page'     => $result['page'],
            'search'   => $search,
        ]);
    }

    public function productCreate(): void
    {
        $this->requireAdmin();
        $this->renderAdmin('admin/products/form', [
            'title'       => 'Thêm sản phẩm mới',
            'categories'  => $this->categoryModel->getTree(), // Dùng getTree để hiển thị danh mục phân cấp
            'groupedCats' => $this->categoryModel->getGroupedByType(), // Dữ liệu cho các dropdown khác
            'product'     => null,
        ]);
    }

    public function productStore(): void
    {
        $this->requireAdmin();
        $this->requirePost();

        $errors = $this->validateProductForm($_POST);
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data']   = $_POST;
            $this->redirect('/admin/products/create');
            return;
        }

        $productId = $this->productModel->create([
            'name'        => trim($_POST['name']),
            'price'       => (int) $_POST['price'],
            'stock'       => (int) ($_POST['stock'] ?? 0),
            'category_id' => (int) $_POST['category_id'],
            'scale'       => trim($_POST['scale']   ?? ''),
            'grade'       => trim($_POST['grade']   ?? ''),
            'series'      => trim($_POST['series']  ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'weight_gram' => !empty($_POST['weight_gram']) ? (int) $_POST['weight_gram'] : null,
            'is_active'   => isset($_POST['is_active']) ? 1 : 0,
        ]);

        // Upload ảnh nếu có
        if (!empty($_FILES['thumbnail']['name'])) {
            $this->productModel->uploadImage($productId, $_FILES['thumbnail'], true);
        }

        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Đã thêm sản phẩm thành công!'];
        $this->redirect('/admin/products');
    }

    public function productEdit(?string $id): void
    {
        $this->requireAdmin();
        $product = $this->productModel->getById((int) $id);
        if (!$product) {
            $this->redirect('/admin/products');
            return;
        }

        $this->renderAdmin('admin/products/form', [
            'title'       => 'Chỉnh sửa sản phẩm',
            'categories'  => $this->categoryModel->getTree(),
            'groupedCats' => $this->categoryModel->getGroupedByType(),
            'product'     => $product,
        ]);
    }

    public function productUpdate(?string $id): void
    {
        $this->requireAdmin();
        $this->requirePost();
        $productId = (int) $id;

        $errors = $this->validateProductForm($_POST);
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $this->redirect("/admin/products/edit/$productId");
            return;
        }

        $this->productModel->update($productId, [
            'name'        => trim($_POST['name']),
            'price'       => (int) $_POST['price'],
            'stock'       => (int) ($_POST['stock'] ?? 0),
            'category_id' => (int) $_POST['category_id'],
            'scale'       => trim($_POST['scale']   ?? ''),
            'grade'       => trim($_POST['grade']   ?? ''),
            'series'      => trim($_POST['series']  ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'weight_gram' => !empty($_POST['weight_gram']) ? (int) $_POST['weight_gram'] : null,
            'is_active'   => isset($_POST['is_active']) ? 1 : 0,
        ]);

        if (!empty($_FILES['thumbnail']['name'])) {
            $this->productModel->uploadImage($productId, $_FILES['thumbnail'], true);
        }

        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Đã cập nhật sản phẩm!'];
        $this->redirect('/admin/products');
    }

    public function productDelete(?string $id): void
    {
        $this->requireAdmin();
        $this->requirePost();
        $this->productModel->softDelete((int) $id);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Đã ẩn sản phẩm khỏi cửa hàng.'];
        $this->redirect('/admin/products');
    }

    // ────────────────────────────────────────────
    //  QUẢN LÝ DANH MỤC (CATEGORY)
    // ────────────────────────────────────────────

    public function categories(): void
    {
        $this->requireAdmin();
        $db = getDB();
        $categories = $db->query("SELECT c.*, p.name AS parent_name FROM categories c LEFT JOIN categories p ON c.parent_id = p.id ORDER BY c.type ASC, c.name ASC")->fetchAll();

        $this->renderAdmin('admin/categories/index', [
            'title'      => 'Quản lý danh mục',
            'categories' => $categories
        ]);
    }

    public function categoryCreate(): void
    {
        $this->requireAdmin();
        $db = getDB();
        $parents = $db->query("SELECT id, name, type FROM categories WHERE parent_id IS NULL ORDER BY name ASC")->fetchAll();

        $this->renderAdmin('admin/categories/form', [
            'title'    => 'Thêm danh mục mới',
            'category' => null,
            'parents'  => $parents
        ]);
    }

    public function categoryStore(): void
    {
        $this->requireAdmin();
        $this->requirePost();

        $name = trim($_POST['name'] ?? '');
        $type = trim($_POST['type'] ?? '');
        $parentId = !empty($_POST['parent_id']) ? (int) $_POST['parent_id'] : null;

        if (!$name || !$type) {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Vui lòng nhập tên và chọn loại danh mục.'];
            $this->redirect('/admin/categories/create');
            return;
        }

        $this->categoryModel->create([
            'name'      => $name,
            'type'      => $type,
            'parent_id' => $parentId
        ]);

        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Thêm danh mục thành công!'];
        $this->redirect('/admin/categories');
    }

    public function categoryEdit(?string $id): void
    {
        $this->requireAdmin();
        $db = getDB();
        $catId = (int) $id;
        $category = $db->query("SELECT * FROM categories WHERE id = $catId")->fetch();

        if (!$category) {
            $this->redirect('/admin/categories');
            return;
        }

        $parents = $db->query("SELECT id, name, type FROM categories WHERE parent_id IS NULL AND id != $catId ORDER BY name ASC")->fetchAll();

        $this->renderAdmin('admin/categories/form', [
            'title'    => 'Sửa danh mục',
            'category' => $category,
            'parents'  => $parents
        ]);
    }

    public function categoryUpdate(?string $id): void
    {
        $this->requireAdmin();
        $this->requirePost();
        $catId = (int) $id;

        $name = trim($_POST['name'] ?? '');
        $type = trim($_POST['type'] ?? '');
        $parentId = !empty($_POST['parent_id']) ? (int) $_POST['parent_id'] : null;

        if (!$name || !$type) {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Vui lòng nhập tên và chọn loại danh mục.'];
            $this->redirect("/admin/categories/edit/$catId");
            return;
        }

        $this->categoryModel->update($catId, [
            'name'      => $name,
            'type'      => $type,
            'parent_id' => $parentId
        ]);

        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Cập nhật danh mục thành công!'];
        $this->redirect('/admin/categories');
    }

    public function categoryDelete(?string $id): void
    {
        $this->requireAdmin();
        $this->requirePost();
        $catId = (int) $id;

        $this->categoryModel->delete($catId);

        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Đã xóa danh mục!'];
        $this->redirect('/admin/categories');
    }

    // ────────────────────────────────────────────
    //  QUẢN LÝ ĐƠN HÀNG
    // ────────────────────────────────────────────

    public function orders(): void
    {
        $this->requireAdmin();
        $status = htmlspecialchars($_GET['status'] ?? '');
        $page   = max(1, (int) ($_GET['page'] ?? 1));
        $result = $this->orderModel->getAll($status, $page, 20);

        // Đếm theo từng trạng thái (cho filter tabs)
        $db         = getDB();
        $statusCounts = [];
        foreach (['pending', 'confirmed', 'shipping', 'delivered', 'cancelled'] as $s) {
            $statusCounts[$s] = (int) $db->query("SELECT COUNT(*) FROM orders WHERE status='$s'")->fetchColumn();
        }

        $this->renderAdmin('admin/orders/index', [
            'title'        => 'Quản lý đơn hàng',
            'orders'       => $result['items'],
            'total'        => $result['total'],
            'pages'        => $result['pages'],
            'page'         => $page,
            'currentStatus' => $status,
            'statusCounts' => $statusCounts,
        ]);
    }

    public function orderDetail(?string $id): void
    {
        $this->requireAdmin();
        $orderId = (int) $id;
        $order = $this->orderModel->getById($orderId);

        if (!$order) {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Không tìm thấy đơn hàng.'];
            $this->redirect('/admin/orders');
            return;
        }

        $this->renderAdmin('admin/orders/detail', [
            'title' => 'Chi tiết Đơn hàng #' . $orderId,
            'order' => $order
        ]);
    }

    /**
     * POST /admin/orders/status
     * Body: { order_id: int, status: string }
     */
    public function orderStatus(): void
    {
        $this->requireAdmin();
        $this->requirePost();
        $orderId = (int) ($_POST['order_id'] ?? 0);
        $status  = $_POST['status'] ?? '';
        $success = $this->orderModel->updateStatus($orderId, $status);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => $success]);
        exit;
    }

    // ────────────────────────────────────────────
    //  QUẢN LÝ KHO (INVENTORY)
    // ────────────────────────────────────────────

    public function inventory(): void
    {
        $this->requireAdmin();
        $db = getDB();

        // Toàn bộ sản phẩm kèm tồn kho, sắp xếp theo stock tăng dần
        $products = $db->query("
            SELECT p.id, p.name, p.grade, p.scale, p.series, p.stock, p.price,
                   pi.image_path AS thumbnail_path
            FROM products p
            LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
            WHERE p.is_active = 1
            ORDER BY p.stock ASC
        ")->fetchAll();

        // Lịch sử điều chỉnh kho (nếu có bảng inventory_logs)
        $hasLogTable = false;
        try {
            $db->query("SELECT 1 FROM inventory_logs LIMIT 1");
            $hasLogTable = true;
        } catch (\PDOException $e) {
        }

        $this->renderAdmin('admin/inventory/index', [
            'title'       => 'Quản lý kho hàng',
            'products'    => $products,
            'hasLogTable' => $hasLogTable,
        ]);
    }

    /**
     * POST /admin/inventory/adjust
     * Body: { product_id: int, delta: int, reason: string }
     * delta dương = nhập thêm hàng, delta âm = xuất/điều chỉnh giảm
     */
    public function inventoryAdjust(): void
    {
        $this->requireAdmin();
        $this->requirePost();

        $productId = (int) ($_POST['product_id'] ?? 0);
        $delta     = (int) ($_POST['delta']      ?? 0);
        $reason    = htmlspecialchars(trim($_POST['reason'] ?? 'Điều chỉnh thủ công'));

        if (!$productId || $delta === 0) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            exit;
        }

        $success = $this->productModel->adjustStock($productId, $delta);

        if ($success) {
            // Lấy tồn kho mới
            $db       = getDB();
            $newStock = (int) $db->query("SELECT stock FROM products WHERE id=$productId")->fetchColumn();

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success'   => true,
                'new_stock' => $newStock,
                'message'   => ($delta > 0 ? "Nhập thêm $delta" : "Xuất $delta") . " — Còn lại: $newStock",
            ]);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Tồn kho không đủ để xuất']);
        }
        exit;
    }

    // ────────────────────────────────────────────
    //  VALIDATE FORM SẢN PHẨM
    // ────────────────────────────────────────────

    private function validateProductForm(array $post): array
    {
        $errors = [];
        if (empty(trim($post['name']        ?? ''))) $errors['name']        = 'Vui lòng nhập tên sản phẩm';
        if (empty($post['price']) || $post['price'] < 0) $errors['price']   = 'Giá không hợp lệ';
        if (empty($post['category_id']))               $errors['category_id'] = 'Vui lòng chọn danh mục';
        return $errors;
    }

    // ────────────────────────────────────────────
    //  MIDDLEWARE + HELPER
    // ────────────────────────────────────────────

    private function isAdmin(): bool
    {
        return ($_SESSION['user']['role'] ?? '') === 'admin';
    }

    private function requireAdmin(): void
    {
        if (!$this->isAdmin()) {
            $this->redirect('/admin/login');
        }
    }

    private function requirePost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method not allowed');
        }
    }

    private function redirect(string $url): void
    {
        header('Location: ' . BASE_URL . '/' . ltrim($url, '/'));
        exit;
    }

    /**
     * Render view trong layout Admin (khác layout shop)
     * @param bool $withLayout  false → render không có layout (dùng cho trang login)
     */
    private function renderAdmin(string $view, array $data = [], bool $withLayout = true): void
    {
        extract($data);
        $viewFile = APP_PATH . '/views/' . $view . '.php';

        ob_start();
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "<p style='color:red'>View không tìm thấy: $viewFile</p>";
        }
        $content = ob_get_clean();

        if ($withLayout) {
            include APP_PATH . '/views/layouts/admin.php';
        } else {
            echo $content;
        }
    }
}
