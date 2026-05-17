<?php
/**
 * index.php — Front Controller (Nằm trong thư mục public)
 */
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH',  BASE_PATH . '/app');
define('BASE_URL', '/gunpla-shop');

require_once BASE_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/config/app.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$rawUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = rtrim(BASE_URL, '/');
if ($base !== '' && str_starts_with($rawUri, $base)) {
    $rawUri = substr($rawUri, strlen($base));
}
$uri   = trim($rawUri, '/');

if (preg_match('/^news\/([a-zA-Z0-9-]+)$/', $uri, $matches)) {
    // Trích xuất chuỗi slug từ URL
    $slug = $matches[1];
    $controller = new \App\Controllers\NewsController();
    // Gọi đến phương thức hiển thị chi tiết bài viết
    $controller->detail($slug);
    exit;
}

$parts = $uri !== '' ? explode('/', $uri) : [];

$controllerName = !empty($parts[0]) ? strtolower($parts[0]) : '';
$param          = null;

if ($controllerName === '' || $controllerName === 'index.php') {
    $controllerName = 'products';
    $action         = 'home';
} else {

    $seg1 = !empty($parts[1]) ? strtolower($parts[1]) : 'index';
    $seg2 = $parts[2] ?? null;
    $seg3 = $parts[3] ?? null;

    if ($seg2 !== null && !is_numeric($seg2)) {
            $singleEntity = rtrim($seg1, 's');
            
            if ($seg1 === 'categories') {
                $singleEntity = 'category';
            } elseif ($seg1 === 'news') {
                $singleEntity = 'news';
            }
            
            $action = $singleEntity . ucfirst($seg2);
            $param = $seg3; 
        } else {
            $action = $seg1;
            $param = $seg2;
        }
}

$routes = [
    'products' => \App\Controllers\ProductController::class,
    'cart'     => \App\Controllers\CartController::class,
    'orders'   => \App\Controllers\OrderController::class,
    'admin'    => \App\Controllers\AdminController::class,
    'user'     => \App\Controllers\UserController::class,
    'favorite' => \App\Controllers\FavoriteController::class,
    'news'     => \App\Controllers\NewsController::class,
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
