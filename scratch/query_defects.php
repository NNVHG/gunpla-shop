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
$stmt = $db->query("SELECT * FROM defect_reports ORDER BY id DESC LIMIT 50");
$defects = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Defect Reports:\n";
foreach ($defects as $d) {
    echo "ID: {$d['id']}, UserID: {$d['user_id']}, ProductID: {$d['product_id']}, OrderID: {$d['order_id']}, Status: {$d['status']}\n";
}
