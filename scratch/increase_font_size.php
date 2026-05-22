<?php
/**
 * scratch/increase_font_size.php
 * Script to increase all px-based font-sizes in CSS and PHP view files by 1px.
 */

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

// We will scan public/css/ and app/Views/
$directories = [
    BASE_PATH . '/public/css',
    BASE_PATH . '/app/Views'
];

function getFiles(string $dir, array &$results = []): array {
    $files = scandir($dir);
    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            if (in_array($ext, ['css', 'php'])) {
                $results[] = $path;
            }
        } else if ($value != "." && $value != "..") {
            getFiles($path, $results);
        }
    }
    return $results;
}

$allFiles = [];
foreach ($directories as $dir) {
    if (is_dir($dir)) {
        getFiles($dir, $allFiles);
    }
}

echo "Found " . count($allFiles) . " files to process.\n";

$totalReplacements = 0;
foreach ($allFiles as $file) {
    $content = file_get_contents($file);
    
    $count = 0;
    $newContent = preg_replace_callback('/font-size\s*:\s*(\d+)px/i', function($matches) use (&$count) {
        $count++;
        $newSize = ((int)$matches[1]) + 1;
        return 'font-size:' . $newSize . 'px';
    }, $content);

    if ($count > 0) {
        file_put_contents($file, $newContent);
        echo "Updated file: " . str_replace(BASE_PATH, '', $file) . " (increased $count font sizes)\n";
        $totalReplacements += $count;
    }
}

echo "Completed. Total font-size declarations increased: $totalReplacements\n";
