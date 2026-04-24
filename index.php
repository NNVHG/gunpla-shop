<?php
/**
 * index.php — Front Controller
 * Chạy trong subfolder XAMPP: http://localhost/gunpla-shop/
 */

declare(strict_types=1);

define('BASE_PATH', __DIR__);
define('APP_PATH',  BASE_PATH . '/app');

// Tên subfolder — đổi nếu thư mục của bạn khác tên
define('BASE_URL', '/gunpla-shop');

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/config/app.php';
require_once APP_PATH  . '/models/Category.php';
require_once APP_PATH  . '/models/Product.php';
require_once APP_PATH  . '/models/Order.php';
require_once APP_PATH  . '/models/User.php';
require_once APP_PATH  . '/controllers/ProductController.php';
require_once APP_PATH  . '/controllers/CartController.php';
require_once APP_PATH  . '/controllers/OrderController.php';
require_once APP_PATH  . '/controllers/AdminController.php';
require_once APP_PATH  . '/controllers/UserController.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Parse URL ── loại bỏ prefix subfolder ──────────────────────────
$rawUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Xóa /gunpla-shop khỏi đầu URI
$base = rtrim(BASE_URL, '/');
if ($base !== '' && str_starts_with($rawUri, $base)) {
    $rawUri = substr($rawUri, strlen($base));
}

$uri   = trim($rawUri, '/');
$parts = $uri !== '' ? explode('/', $uri) : [];

$controllerName = !empty($parts[0]) ? strtolower($parts[0]) : '';
$action         = !empty($parts[1]) ? strtolower($parts[1]) : 'index';
$param          = $parts[2] ?? null;

// Trang chủ
if ($controllerName === '' || $controllerName === 'index.php') {
    $controllerName = 'products';
    $action         = 'home';
}

// ── Routing ────────────────────────────────────────────────────────
$routes = [
    'products' => ProductController::class,
    'cart'     => CartController::class,
    'orders'   => OrderController::class,
    'admin'    => AdminController::class,
    'user'     => UserController::class,
];

if (!isset($routes[$controllerName])) {
    http_response_code(404);
    $content = '<div class="error-wrap"><div class="error-code">404</div><div class="error-msg">Không tìm thấy trang</div><a href="' . BASE_URL . '/" class="btn-hero">VỀ TRANG CHỦ</a></div>';
    @include APP_PATH . '/views/layouts/main.php';
    exit;
}

$controller = new $routes[$controllerName]();

if (!method_exists($controller, $action)) {
    http_response_code(404);
    $content = '<div class="error-wrap"><div class="error-code">404</div><div class="error-msg">Không tìm thấy trang</div><a href="' . BASE_URL . '/" class="btn-hero">VỀ TRANG CHỦ</a></div>';
    @include APP_PATH . '/views/layouts/main.php';
    exit;
}

$controller->$action($param);