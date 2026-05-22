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
$stmt = $db->query("SELECT * FROM notifications ORDER BY id DESC LIMIT 50");
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Notifications:\n";
foreach ($notifications as $n) {
    echo "ID: {$n['id']}, Title: {$n['title']}, Message: {$n['message']}, Link: {$n['link']}, IsRead: {$n['is_read']}\n";
}
