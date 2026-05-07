<?php
/**
 * index.php — Front Controller (Nằm trong thư mục public)
 */
declare(strict_types=1);

// 1. ĐỊNH NGHĨA ĐƯỜNG DẪN (dirname(__DIR__) để lùi ra thư mục gunpla-shop)
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH',  BASE_PATH . '/app');
define('BASE_URL', '/gunpla-shop');

// 2. NẠP AUTOLOAD CỦA COMPOSER (BẮT BUỘC PHẢI NẰM Ở ĐÂY, TRÊN DOTENV)
require_once BASE_PATH . '/vendor/autoload.php';

// 3. KÍCH HOẠT DOTENV 
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

// 4. NẠP CẤU HÌNH HỆ THỐNG
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/config/app.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Parse URL ──────────────────────────────────────────────────────
$rawUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = rtrim(BASE_URL, '/');
if ($base !== '' && str_starts_with($rawUri, $base)) {
    $rawUri = substr($rawUri, strlen($base));
}
$uri   = trim($rawUri, '/');
$parts = $uri !== '' ? explode('/', $uri) : [];

$controllerName = !empty($parts[0]) ? strtolower($parts[0]) : '';
$action         = !empty($parts[1]) ? strtolower($parts[1]) : 'index';
$param          = $parts[2] ?? null;

if ($controllerName === '' || $controllerName === 'index.php') {
    $controllerName = 'products';
    $action         = 'home';
}

// ── Routing ────────────────────────────────────────────────────────
$routes = [
    'products' => \App\Controllers\ProductController::class,
    'cart'     => \App\Controllers\CartController::class,
    'orders'   => \App\Controllers\OrderController::class,    // Quản lý Checkout (thanh toán)
    'admin'    => \App\Controllers\AdminController::class,
    'user'     => \App\Controllers\UserController::class,     // Quản lý Profile (hồ sơ)
    'favorite' => \App\Controllers\FavoriteController::class, // Quản lý Yêu thích
];

if (!isset($routes[$controllerName])) {
    http_response_code(404);
    $content = '<div class="error-wrap"><div class="error-code">404</div><div class="error-msg">Không tìm thấy trang</div><a href="' . BASE_URL . '/" class="btn-hero">VỀ TRANG CHỦ</a></div>';
    @include APP_PATH . '/Views/layouts/main.php';
    exit;
}

$controller = new $routes[$controllerName]();

if (!method_exists($controller, $action)) {
    http_response_code(404);
    $content = '<div class="error-wrap"><div class="error-code">404</div><div class="error-msg">Không tìm thấy trang</div><a href="' . BASE_URL . '/" class="btn-hero">VỀ TRANG CHỦ</a></div>';
    @include APP_PATH . '/Views/layouts/main.php';
    exit;
}

$controller->$action($param);