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

namespace App\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Order;

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

    public function index(): void
    {
        $this->redirect('/admin/dashboard');
    }

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

    public function dashboard(): void
    {
        $this->requireAdmin();
        $db = getDB();

        $stats = [
            'total_products'  => (int) $db->query("SELECT COUNT(*) FROM products WHERE is_active=1")->fetchColumn(),
            'total_orders'    => (int) $db->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
            'pending_orders'  => (int) $db->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn(),
            'revenue_today'   => (int) $db->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE DATE(created_at)=CURDATE() AND status!='cancelled'")->fetchColumn(),
            'revenue_month'   => (int) $db->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW()) AND status!='cancelled'")->fetchColumn(),
            'low_stock'       => (int) $db->query("SELECT COUNT(*) FROM products WHERE stock<=5 AND is_active=1")->fetchColumn(),
        ];

        $revenueChart = $db->query("
            SELECT DATE(created_at) AS date, SUM(total) AS revenue
            FROM orders
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND status != 'cancelled'
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ")->fetchAll();

        $latestOrders = $this->orderModel->getAll('', 1, 8)['items'];

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

    public function products(): void
    {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $perPage = 10;
        
        $filters = [];
        if ($search !== '') {
            $filters['search'] = $search;
        }

        $data = $this->productModel->getAll($filters, 'newest', $page, $perPage);
        $categories = $this->categoryModel->getAll();

        $this->renderAdmin('admin/products/index', [
            'title'       => 'Quản lý Sản phẩm',
            'products'    => $data['items'],
            'categories'  => $categories,
            'total'       => $data['total'] ?? 0,
            'totalPages'  => $data['pages'] ?? 1,
            'currentPage' => $page,
            'search'      => $search
        ]);
    }

    public function productCreate(): void
    {
        $this->requireAdmin();
        $this->renderAdmin('admin/products/form', [
            'title'       => 'Thêm sản phẩm mới',
            'categories'  => $this->categoryModel->getTree(),
            'groupedCats' => $this->categoryModel->getGroupedByType(),
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

    public function orders(): void
    {
        $this->requireAdmin();
        $status = htmlspecialchars($_GET['status'] ?? '');
        $page   = max(1, (int) ($_GET['page'] ?? 1));
        $result = $this->orderModel->getAll($status, $page, 20);

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

    public function orderDetail(?string $id = null): void
    {
        if (!$id) {
            header('Location: ' . BASE_URL . '/admin/orders');
            exit;
        }

        $order = $this->orderModel->getById((int)$id);
        if (!$order) {
            die('Không tìm thấy đơn hàng');
        }

        $this->renderAdmin('admin/orders/detail', [
            'title' => 'Chi tiết đơn hàng #' . $id,
            'order' => $order
        ]);
    }

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

    public function markPaid(): void
    {
        $this->requireAdmin();
        $this->requirePost();
        
        $orderId = (int) ($_POST['order_id'] ?? 0);
        
        if ($orderId > 0) {
            // Gọi model để cập nhật trạng thái thanh toán thành 'paid'
            $success = $this->orderModel->updatePaymentStatus($orderId, 'paid');
        } else {
            $success = false;
        }

        // Trả về JSON cho fetch API ở frontend
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => $success]);
        exit;
    }

    public function inventory(): void
    {
        $this->requireAdmin();
        $db = getDB();

        $products = $db->query("
            SELECT p.id, p.name, p.grade, p.scale, p.series, p.stock, p.price,
                   pi.image_path AS thumbnail_path
            FROM products p
            LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
            WHERE p.is_active = 1
            ORDER BY p.stock ASC
        ")->fetchAll();

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

    private function validateProductForm(array $post): array
    {
        $errors = [];
        if (empty(trim($post['name']        ?? ''))) $errors['name']        = 'Vui lòng nhập tên sản phẩm';
        if (empty($post['price']) || $post['price'] < 0) $errors['price']   = 'Giá không hợp lệ';
        if (empty($post['category_id']))               $errors['category_id'] = 'Vui lòng chọn danh mục';
        return $errors;
    }

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

    public function users(): void
    {
        $this->requireAdmin();
        $this->renderAdmin('admin/users/index', [
            'title' => 'Quản lý Khách hàng',
            'users' => $this->userModel->getAllUsers(),
        ]);
    }

    public function changeUserRole(?string $id): void
    {
        $this->requireAdmin();
        $this->requirePost();

        $userId      = (int) $id;
        $currentRole = $_POST['current_role'] ?? 'customer';
        $newRole     = ($currentRole === 'admin') ? 'customer' : 'admin';

        if ($userId === (int) ($_SESSION['user']['id'] ?? 0)) {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Bạn không thể thay đổi quyền của chính mình!'];
            $this->redirect('/admin/users');
            return;
        }

        $this->userModel->updateUserRole($userId, $newRole);
        $_SESSION['flash'] = [
            'type' => 'success',
            'msg'  => "Đã đổi quyền tài khoản #{$userId} thành " . strtoupper($newRole),
        ];
        $this->redirect('/admin/users');
    }

    public function deleteUser(?string $id): void
    {
        $this->requireAdmin();
        $this->requirePost();

        $userId = (int) $id;

        if ($userId === (int) ($_SESSION['user']['id'] ?? 0)) {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Bạn không thể xóa tài khoản của chính mình!'];
            $this->redirect('/admin/users');
            return;
        }

        $this->userModel->deleteUser($userId);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => "Đã xóa tài khoản #{$userId}."];
        $this->redirect('/admin/users');
    }

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

    public function reviews(): void
    {
        $reviewModel = new \App\Models\Review();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['review_id'] ?? 0);
            $status = $_POST['status'] ?? 'pending';
            if ($id > 0) {
                $reviewModel->updateStatus($id, $status);
            }
            header('Location: ' . BASE_URL . '/admin/reviews');
            exit;
        }

        $reviews = $reviewModel->getAllForAdmin();
        $this->renderAdmin('admin/reviews/index', [
            'title' => 'Quản lý Đánh giá',
            'reviews' => $reviews
        ]);
    }
}
