<?php
declare(strict_types=1);

define('BASE_PATH', 'e:/XAMPP/htdocs/gunpla-shop');
define('APP_PATH',  BASE_PATH . '/app');
define('BASE_URL', '/gunpla-shop');

require_once BASE_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

require_once BASE_PATH . '/config/database.php';

$db = getDB();
$stmt = $db->query("SELECT * FROM orders WHERE id = 27");
$order = $stmt->fetch(PDO::FETCH_ASSOC);

echo "Order 27:\n";
print_r($order);
