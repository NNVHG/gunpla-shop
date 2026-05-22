<?php
declare(strict_types=1);

define('BASE_PATH', 'e:/XAMPP/htdocs/gunpla-shop');
define('APP_PATH',  BASE_PATH . '/app');
define('BASE_URL', '/gunpla-shop');

require_once BASE_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

require_once BASE_PATH . '/config/database.php';

try {
    $db = getDB();
    echo "Connected to database.\n";

    // Select all notifications
    $stmt = $db->query("SELECT id, message, link FROM notifications");
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Scanning " . count($notifications) . " notifications...\n";

    foreach ($notifications as $n) {
        // Extract product name and order id from message:
        // Pattern: Báo cáo lỗi sản phẩm "..." (Đơn hàng #...)
        if (preg_match('/Báo cáo lỗi sản phẩm "([^"]+)" \(Đơn hàng #(\d+)\)/u', $n['message'], $matches)) {
            $productName = $matches[1];
            $orderId = (int)$matches[2];

            echo "Found matching pattern: Product = '{$productName}', Order = {$orderId}\n";

            // Find the defect report matching the order and product name
            $stmtReport = $db->prepare("
                SELECT dr.id FROM defect_reports dr
                JOIN products p ON dr.product_id = p.id
                WHERE dr.order_id = ? AND p.name = ?
            ");
            $stmtReport->execute([$orderId, $productName]);
            $reportId = $stmtReport->fetchColumn();

            if ($reportId) {
                $newLink = '/orders/detail/' . $orderId . '#defect-report-' . $reportId;
                if ($n['link'] !== $newLink) {
                    $stmtUpdate = $db->prepare("UPDATE notifications SET link = ? WHERE id = ?");
                    $stmtUpdate->execute([$newLink, $n['id']]);
                    echo "Updated notification ID {$n['id']} link from '{$n['link']}' to '{$newLink}'\n";
                } else {
                    echo "Notification ID {$n['id']} already has correct link: {$newLink}\n";
                }
            } else {
                echo "Warning: No matching defect report found for order {$orderId} and product '{$productName}'\n";
            }
        }
    }
    echo "Scan complete.\n";
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
