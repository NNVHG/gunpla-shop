<?php
declare(strict_types=1);

define('BASE_PATH', 'e:/XAMPP/htdocs/gunpla-shop');
define('APP_PATH',  BASE_PATH . '/app');
define('BASE_URL', '/gunpla-shop');

require_once BASE_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/config/app.php';

session_start();
// Mock user login
$_SESSION['user'] = [
    'id' => 6,
    'name' => 'Test User',
    'role' => 'user'
];

ob_start();
$controller = new \App\Controllers\OrderController();
$controller->detail('27');
$output = ob_get_clean();

echo "Response length: " . strlen($output) . "\n";
if (strpos($output, 'LỊCH SỬ BÁO CÁO THIẾU ĐỒ') !== false) {
    echo "Found defect reports section!\n";
} else {
    echo "Defect reports section NOT found!\n";
    echo "Output snippet:\n" . substr($output, 0, 1000) . "\n";
}
