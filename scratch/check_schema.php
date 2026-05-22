<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';

$db = getDB();
echo "--- PRODUCTS SAMPLES ---\n";
$prods = $db->query("SELECT id, name, thumbnail FROM products LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
foreach ($prods as $p) {
    echo "ID: {$p['id']} | Name: {$p['name']} | Thumbnail: {$p['thumbnail']}\n";
}

echo "\n--- PRODUCT_IMAGES SAMPLES ---\n";
$imgs = $db->query("SELECT id, product_id, image_path, is_primary FROM product_images LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
foreach ($imgs as $i) {
    echo "ID: {$i['id']} | Product ID: {$i['product_id']} | Path: {$i['image_path']} | Primary: {$i['is_primary']}\n";
}
