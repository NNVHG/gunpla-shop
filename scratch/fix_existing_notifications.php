<?php
/**
 * scratch/fix_existing_notifications.php
 * Fixes links for existing defect report notifications in the database.
 */

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH',  BASE_PATH . '/app');

require_once BASE_PATH . '/vendor/autoload.php';

if (file_exists(BASE_PATH . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
    $dotenv->load();
}

require_once BASE_PATH . '/config/database.php';

try {
    $db = getDB();
    echo "Connected to database.\n";

    // Select notifications that have the old link format
    $stmt = $db->prepare("SELECT id, message, user_id FROM notifications WHERE link = '/user/profile?tab=notifications'");
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Found " . count($notifications) . " notifications to fix.\n";

    foreach ($notifications as $n) {
        // Extract order_id from message: (Đơn hàng #27)
        if (preg_match('/\(Đơn hàng #(\d+)\)/', $n['message'], $matches)) {
            $orderId = (int)$matches[1];
            
            // Try to find a matching defect report for this user and order
            // If multiple, get the latest one
            $stmtReport = $db->prepare("
                SELECT id FROM defect_reports 
                WHERE user_id = ? AND order_id = ? 
                ORDER BY id DESC LIMIT 1
            ");
            $stmtReport->execute([$n['user_id'], $orderId]);
            $reportId = $stmtReport->fetchColumn();

            if ($reportId) {
                $newLink = '/orders/detail/' . $orderId . '#defect-report-' . $reportId;
                $stmtUpdate = $db->prepare("UPDATE notifications SET link = ? WHERE id = ?");
                $stmtUpdate->execute([$newLink, $n['id']]);
                echo "Updated notification ID {$n['id']} with link: {$newLink}\n";
            } else {
                // Fallback to just order detail page
                $newLink = '/orders/detail/' . $orderId;
                $stmtUpdate = $db->prepare("UPDATE notifications SET link = ? WHERE id = ?");
                $stmtUpdate->execute([$newLink, $n['id']]);
                echo "Fallback: Updated notification ID {$n['id']} with link: {$newLink}\n";
            }
        }
    }
    echo "Done.\n";
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
