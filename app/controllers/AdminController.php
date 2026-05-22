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
use App\Models\News;
use DateTime;
use PDO;

class AdminController
{
    private Product  $productModel;
    private Order    $orderModel;
    private Category $categoryModel;
    private User     $userModel;
    private News     $newsModel;

    public function __construct()
    {
        $this->productModel  = new Product();
        $this->orderModel    = new Order();
        $this->categoryModel = new Category();
        $this->userModel     = new User();
        $this->newsModel     = new News();
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
            'parts_count' => !empty($_POST['parts_count']) ? (int) $_POST['parts_count'] : null,
            'difficulty'  => !empty($_POST['difficulty']) ? trim($_POST['difficulty']) : null,
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
            'parts_count' => !empty($_POST['parts_count']) ? (int) $_POST['parts_count'] : null,
            'difficulty'  => !empty($_POST['difficulty']) ? trim($_POST['difficulty']) : null,
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

        if ($success) {
            $order = $this->orderModel->getById($orderId);
            if ($order && !empty($order['user_id'])) {
                require_once APP_PATH . '/Models/Notification.php';
                $notifyModel = new \App\Models\Notification();
                
                $statusLabels = [
                    'pending'   => 'Chờ xác nhận',
                    'confirmed' => 'Đã xác nhận',
                    'shipping'  => 'Đang giao hàng',
                    'delivered' => 'Đã giao hàng thành công',
                    'cancelled' => 'Đã hủy'
                ];
                $statusText = $statusLabels[$status] ?? $status;
                
                $title = 'Cập nhật trạng thái đơn hàng';
                $msg = "Đơn hàng #{$orderId} của bạn đã được chuyển sang trạng thái: {$statusText}.";
                $notifyModel->create((int)$order['user_id'], $title, $msg, '/orders/detail/' . $orderId);
            }
        }

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
        if (isset($post['parts_count']) && $post['parts_count'] !== '' && (!is_numeric($post['parts_count']) || (int)$post['parts_count'] < 0)) {
            $errors['parts_count'] = 'Số lượng mảnh ghép phải là số nguyên dương';
        }
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

    public function news(): void
    {
        $this->requireAdmin();
        $allNews = $this->newsModel->getAll();
        $this->renderAdmin('admin/news/index', ['allNews' => $allNews]);
    }

    public function newsCreate(): void
    {
        $this->requireAdmin();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title        = trim($_POST['title'] ?? '');
            $summary      = trim($_POST['summary'] ?? '');
            $content      = trim($_POST['content'] ?? '');
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

            $thumbnail = '';
            if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                if (in_array($_FILES['thumbnail']['type'], $allowedTypes)) {
                    $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
                    $filename = 'news_' . uniqid() . '.' . strtolower($ext);
                    
                    $dest = BASE_PATH . '/public/uploads/img/' . $filename;
                    if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $dest)) {
                        $thumbnail = 'uploads/img/' . $filename;
                    }
                }
            }

            if (empty($title)) {
                $errors[] = 'Tiêu đề tin tức không được để trống.';
            }

            if (empty($errors)) {
                $this->newsModel->create([
                    'title'        => $title,
                    'slug'         => $slug,
                    'summary'      => $summary,
                    'content'      => $content,
                    'thumbnail'    => $thumbnail,
                    'is_active'    => $is_active
                ]);
                $this->redirect('admin/news');
                return;
            }
        }

        $this->renderAdmin('admin/news/form', [
            'title'  => 'Thêm bài viết mới — Admin Panel',
            'errors' => $errors, 
            'isEdit' => false, 
            'news'   => []
        ]);
    }

    public function newsEdit(string $id)
    {
        $news = $this->newsModel->findById((int)$id);
        if (!$news) {
            header('Location: ' . BASE_URL . '/admin/news');
            exit();
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title     = trim($_POST['title'] ?? '');
            $summary   = trim($_POST['summary'] ?? '');
            $content   = trim($_POST['content'] ?? '');
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

            $thumbnail = $news['thumbnail'] ?? ''; 

            if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['image_path']['tmp_name'];
                $fileName    = $_FILES['image_path']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (in_array($fileExtension, $allowedExtensions)) {
                    $newFileName = time() . '_' . uniqid() . '.' . $fileExtension;
                    $uploadFileDir = __DIR__ . '/../../public/uploads/img/';
                    
                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }
                    
                    $destPath = $uploadFileDir . $newFileName;
                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $thumbnail = 'uploads/img/' . $newFileName;
                    }
                } else {
                    $errors[] = 'Định dạng tệp ảnh không hợp lệ. Hệ thống chỉ chấp nhận tệp JPG, JPEG, PNG, GIF, hoặc WEBP.';
                }
            }

            if (empty($title)) {
                $errors[] = 'Tiêu đề bài viết không được phép để trống.';
            }
            if (empty($content)) {
                $errors[] = 'Nội dung chi tiết bài viết không được phép để trống.';
            }

            if (empty($errors)) {
                $updateData = [
                    'title'     => $title,
                    'slug'      => $slug,
                    'summary'   => $summary,
                    'content'   => $content,
                    'thumbnail' => $thumbnail,
                    'is_active' => $is_active
                ];

                $isUpdated = $this->newsModel->update((int)$id, $updateData);
                $this->newsModel->update((int)$id, $updateData);

                if ($isUpdated) {
                    header('Location: ' . BASE_URL . '/admin/news');
                    exit();
                } else {
                    $errors[] = 'Hệ thống gặp sự cố. Không thể lưu dữ liệu vào cơ sở dữ liệu.';
                }
            }
        }

        $this->renderAdmin('admin/news/form', [
            'title'  => 'Cập nhật bài viết — Admin Panel',
            'errors' => $errors,
            'isEdit' => true,
            'news'   => $news
        ]);
    }

    protected function render(string $view, array $data = []): void
    {
        extract($data);
        require BASE_PATH . '/app/Views/' . $view . '.php';
    }

    public function newsDelete(string $id): void
    {
        $this->requireAdmin();
        $newsId = (int)$id;
        $this->newsModel->delete($newsId);
        $this->redirect('admin/news');
    }

    public function createNews(): void
    {
        $errors = [];
        $newsModel = new \App\Models\News();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title        = trim($_POST['title'] ?? '');
            $summary      = trim($_POST['summary'] ?? '');
            $content      = trim($_POST['content'] ?? '');
            $is_published = isset($_POST['is_published']) ? 1 : 0;
            
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

            if (empty($title)) $errors[] = "Tiêu đề bài viết không được để trống.";
            if (empty($content)) $errors[] = "Nội dung bài viết không được để trống.";

            $imagePath = '';
            if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = APP_PATH . '/../public/uploads/news/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileName = time() . '_' . basename($_FILES['image_path']['name']);
                if (move_uploaded_file($_FILES['image_path']['tmp_name'], $uploadDir . $fileName)) {
                    $imagePath = 'public/uploads/news/' . $fileName;
                }
            }

            if (empty($errors)) {
                $newsModel->create([
                    'title'        => $title,
                    'slug'         => $slug,
                    'summary'      => $summary,
                    'content'      => $content,
                    'image_path'   => $imagePath,
                    'is_published' => $is_published
                ]);

                header("Location: " . BASE_URL . "/admin/news");
                exit;
            }
        }

        $data = [
            'title'  => 'Thêm bài viết mới — Admin panel',
            'isEdit' => false,
            'news'   => [],
            'errors' => $errors
        ];

        $this->render('admin/news/form', $data);
    }

    public function editNews(?string $id): void
    {
        $newsId = (int)$id;
        $newsModel = new \App\Models\News();
        $newsItem = $newsModel->findById($newsId);

        if (!$newsItem) {
            header("Location: " . BASE_URL . "/admin/news");
            exit;
        }

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title        = trim($_POST['title'] ?? '');
            $summary      = trim($_POST['summary'] ?? '');
            $content      = trim($_POST['content'] ?? '');
            $is_published = isset($_POST['is_published']) ? 1 : 0;
            
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

            if (empty($title)) $errors[] = "Tiêu đề bài viết không được để trống.";
            if (empty($content)) $errors[] = "Nội dung bài viết không được để trống.";

            $imagePath = $newsItem['image_path'];
            
            if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = APP_PATH . '/../public/uploads/news/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileName = time() . '_' . basename($_FILES['image_path']['name']);
                if (move_uploaded_file($_FILES['image_path']['tmp_name'], $uploadDir . $fileName)) {
                    $imagePath = 'public/uploads/news/' . $fileName;
                    if (!empty($newsItem['image_path']) && file_exists(APP_PATH . '/../' . $newsItem['image_path'])) {
                        @unlink(APP_PATH . '/../' . $newsItem['image_path']);
                    }
                }
            }

            if (empty($errors)) {
                $newsModel->update($newsId, [
                    'title'        => $title,
                    'slug'         => $slug,
                    'summary'      => $summary,
                    'content'      => $content,
                    'image_path'   => $imagePath,
                    'is_published' => $is_published
                ]);

                header("Location: " . BASE_URL . "/admin/news");
                exit;
            }
        }

        $data = [
            'title'  => 'Cập nhật bài viết — Admin panel',
            'isEdit' => true,
            'news'   => $newsItem,
            'errors' => $errors
        ];

        $this->render('admin/news/form', $data);
    }

    public function settings(): void
    {
        $this->requireAdmin();
        $settingModel = new \App\Models\Setting();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $chatbotEnabled = isset($_POST['chatbot_enabled']) ? '1' : '0';
            $chatbotAiMode  = isset($_POST['chatbot_ai_mode']) ? '1' : '0';
            $chatbotGeminiKey = trim($_POST['chatbot_gemini_key'] ?? '');

            $settingModel->set('chatbot_enabled', $chatbotEnabled);
            $settingModel->set('chatbot_ai_mode', $chatbotAiMode);
            $settingModel->set('chatbot_gemini_key', $chatbotGeminiKey);

            $_SESSION['flash'] = [
                'type' => 'success',
                'msg'  => 'Cập nhật cấu hình AI & Chatbot thành công!'
            ];
            $this->redirect('/admin/settings');
        }

        $settings = $settingModel->getAll();

        $customKey = $settings['chatbot_gemini_key'] ?? '';
        $envKey = $_ENV['GEMINI_API_KEY'] ?? getenv('GEMINI_API_KEY') ?? '';
        $apiKey = !empty($customKey) ? $customKey : $envKey;

        $keyStatus = 'Chưa cấu hình';
        $keyStatusClass = 'status-warning';
        $keyStatusMsg = 'Vui lòng cấu hình API Key để kích hoạt AI Chatbot.';

        if (!empty($apiKey)) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $apiKey;
            $payload = [
                'contents' => [['parts' => [['text' => 'hi']]]],
                'generationConfig' => ['maxOutputTokens' => 1]
            ];
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_TIMEOUT, 4);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                $keyStatus = 'Hoạt động';
                $keyStatusClass = 'status-success';
                $keyStatusMsg = 'API Key hoạt động tốt. Hệ thống sẵn sàng chạy chế độ AI.';
            } else {
                $keyStatus = 'Lỗi kết nối / Hết hạn';
                $keyStatusClass = 'status-danger';
                $resData = json_decode($response, true);
                $keyStatusMsg = $resData['error']['message'] ?? 'Không thể kết nối đến Gemini API. Vui lòng kiểm tra lại khóa hoặc kết nối mạng.';
            }
        }

        $this->renderAdmin('admin/settings', [
            'title'    => 'Cấu hình AI & Chatbot',
            'settings' => $settings,
            'keyStatus' => $keyStatus,
            'keyStatusClass' => $keyStatusClass,
            'keyStatusMsg' => $keyStatusMsg
        ]);
    }

    public function defects(): void
    {
        $this->requireAdmin();
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 15;

        require_once APP_PATH . '/Models/DefectReport.php';
        $defectModel = new \App\Models\DefectReport();
        $data = $defectModel->getAllForAdmin($page, $perPage);

        $this->renderAdmin('admin/defects/index', [
            'title' => 'Quản lý Báo Cáo Lỗi Sản Phẩm',
            'items' => $data['items'],
            'total' => $data['total'],
            'pages' => $data['pages'],
            'page'  => $page
        ]);
    }

    public function defectDetail(?string $param): void
    {
        $this->requireAdmin();
        $id = (int)($param ?? 0);
        if ($id <= 0) {
            $this->redirect('/admin/defects');
            return;
        }

        require_once APP_PATH . '/Models/DefectReport.php';
        $defectModel = new \App\Models\DefectReport();
        $report = $defectModel->getById($id);

        if (!$report) {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Báo cáo lỗi không tồn tại hoặc đã bị xóa.'];
            $this->redirect('/admin/defects');
            return;
        }

        $this->renderAdmin('admin/defects/detail', [
            'title'  => 'Chi tiết Báo Cáo Lỗi #' . $id,
            'report' => $report
        ]);
    }

    public function confirmDefectReshipment(): void
    {
        $this->requireAdmin();
        $this->requirePost();

        $id = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $adminComment = trim($_POST['admin_comment'] ?? '');

        $allowed = ['pending', 'checking', 'approved', 'shipped', 'rejected'];
        if ($id <= 0 || !in_array($status, $allowed)) {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Dữ liệu yêu cầu không hợp lệ.'];
            $this->redirect('/admin/defects');
            return;
        }

        require_once APP_PATH . '/Models/DefectReport.php';
        $defectModel = new \App\Models\DefectReport();
        $report = $defectModel->getById($id);

        if (!$report) {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Báo cáo lỗi không tồn tại.'];
            $this->redirect('/admin/defects');
            return;
        }

        $success = $defectModel->updateStatus($id, $status, $adminComment);

        if ($success) {
            // Gửi thông báo đến tài khoản khách hàng
            require_once APP_PATH . '/Models/Notification.php';
            $notifyModel = new \App\Models\Notification();

            $statusLabels = [
                'pending' => 'Chờ tiếp nhận',
                'checking' => 'Đang kiểm tra',
                'approved' => 'Đã duyệt (chuẩn bị gửi part thay thế)',
                'shipped' => 'Đã gửi part thay thế',
                'rejected' => 'Bị từ chối'
            ];

            if ($status === 'checking') {
                $title = 'Báo cáo lỗi đang được kiểm tra';
                $msg = "Báo cáo lỗi sản phẩm \"{$report['product_name']}\" (Đơn hàng #{$report['order_id']}) của bạn đang được chúng tôi kiểm tra. Chúng tôi sẽ cập nhật tiến trình sớm nhất.";
            } elseif ($status === 'approved') {
                $title = 'Báo cáo lỗi sản phẩm được xác nhận';
                $msg = "Báo cáo lỗi sản phẩm \"{$report['product_name']}\" (Đơn hàng #{$report['order_id']}) của bạn đã được xác nhận. Chúng tôi đang chuẩn bị gửi sản phẩm mới thay thế.";
            } elseif ($status === 'shipped') {
                $title = 'Sản phẩm thay thế đã được gửi';
                $msg = "Sản phẩm thay thế cho báo cáo lỗi \"{$report['product_name']}\" (Đơn hàng #{$report['order_id']}) của bạn đã được gửi đi.";
            } elseif ($status === 'rejected') {
                $title = 'Báo cáo lỗi sản phẩm bị từ chối';
                $msg = "Báo cáo lỗi sản phẩm \"{$report['product_name']}\" (Đơn hàng #{$report['order_id']}) của bạn đã bị từ chối. Lý do: " . ($adminComment ?: 'Hình ảnh hoặc video không đáp ứng yêu cầu.');
            } else {
                $title = 'Báo cáo lỗi chuyển về chờ xử lý';
                $msg = "Báo cáo lỗi sản phẩm \"{$report['product_name']}\" (Đơn hàng #{$report['order_id']}) của bạn đã được chuyển về trạng thái Chờ xử lý.";
            }

        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Đã cập nhật trạng thái báo cáo lỗi sang: ' . $statusLabels[$status]];
            $notifyModel->create((int)$report['user_id'], $title, $msg, '/orders/detail/' . $report['order_id'] . '#defect-report-' . $report['id']);
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Không thể cập nhật trạng thái báo cáo lỗi.'];
        }

        $this->redirect('/admin/defects/detail/' . $id);
    }

    public function revenueData(): void
    {
        $this->requireAdmin();
        $db = getDB();

        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-29 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        // Parse dates
        try {
            $start = new DateTime($startDate);
            $end = new DateTime($endDate);
        } catch (\Exception $e) {
            $start = new DateTime(date('Y-m-d', strtotime('-29 days')));
            $end = new DateTime(date('Y-m-d'));
        }

        // Ensure start is before or equal to end
        if ($start > $end) {
            $temp = $start;
            $start = $end;
            $end = $temp;

            $tempStr = $startDate;
            $startDate = $endDate;
            $endDate = $tempStr;
        }

        $interval = $start->diff($end);
        $daysDiff = (int)$interval->format('%a');

        $labels = [];
        $revenueData = [];
        $ordersData = [];

        if ($daysDiff === 0) {
            // Single day, show hourly view
            for ($h = 0; $h < 24; $h++) {
                $labels[] = sprintf('%02dh', $h);
                $revenueData[$h] = 0;
                $ordersData[$h] = 0;
            }

            $stmt = $db->prepare("
                SELECT HOUR(created_at) AS hour_key, SUM(total) AS revenue, COUNT(id) AS orders_count
                FROM orders
                WHERE DATE(created_at) = :target_date AND status != 'cancelled'
                GROUP BY HOUR(created_at)
            ");
            $stmt->execute([':target_date' => $start->format('Y-m-d')]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $h = (int)$row['hour_key'];
                if (isset($revenueData[$h])) {
                    $revenueData[$h] = (int)$row['revenue'];
                    $ordersData[$h] = (int)$row['orders_count'];
                }
            }
            $revenueDataset = array_values($revenueData);
            $ordersDataset = array_values($ordersData);

        } elseif ($daysDiff <= 90) {
            // Under 90 days, show daily view
            $current = clone $start;
            while ($current <= $end) {
                $dateStr = $current->format('Y-m-d');
                $labels[] = $current->format('d/m');
                $revenueData[$dateStr] = 0;
                $ordersData[$dateStr] = 0;
                $current->modify('+1 day');
            }

            $stmt = $db->prepare("
                SELECT DATE(created_at) AS date_key, SUM(total) AS revenue, COUNT(id) AS orders_count
                FROM orders
                WHERE DATE(created_at) >= :start_date AND DATE(created_at) <= :end_date AND status != 'cancelled'
                GROUP BY DATE(created_at)
            ");
            $stmt->execute([
                ':start_date' => $start->format('Y-m-d'),
                ':end_date'   => $end->format('Y-m-d')
            ]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                if (isset($revenueData[$row['date_key']])) {
                    $revenueData[$row['date_key']] = (int)$row['revenue'];
                    $ordersData[$row['date_key']] = (int)$row['orders_count'];
                }
            }
            $revenueDataset = array_values($revenueData);
            $ordersDataset = array_values($ordersData);

        } else {
            // Over 90 days, show monthly view
            $current = clone $start;
            $current->modify('first day of this month');
            $endMonth = clone $end;
            $endMonth->modify('first day of this month');

            while ($current <= $endMonth) {
                $monthKey = $current->format('Y-m');
                $labels[] = $current->format('m/Y');
                $revenueData[$monthKey] = 0;
                $ordersData[$monthKey] = 0;
                $current->modify('+1 month');
            }

            $stmt = $db->prepare("
                SELECT DATE_FORMAT(created_at, '%Y-%m') AS month_key, SUM(total) AS revenue, COUNT(id) AS orders_count
                FROM orders
                WHERE DATE(created_at) >= :start_date AND DATE(created_at) <= :end_date AND status != 'cancelled'
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ");
            $stmt->execute([
                ':start_date' => $start->format('Y-m-d'),
                ':end_date'   => $end->format('Y-m-d')
            ]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                if (isset($revenueData[$row['month_key']])) {
                    $revenueData[$row['month_key']] = (int)$row['revenue'];
                    $ordersData[$row['month_key']] = (int)$row['orders_count'];
                }
            }
            $revenueDataset = array_values($revenueData);
            $ordersDataset = array_values($ordersData);
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'labels' => $labels,
            'revenue' => $revenueDataset,
            'orders' => $ordersDataset
        ]);
        exit;
    }
}

