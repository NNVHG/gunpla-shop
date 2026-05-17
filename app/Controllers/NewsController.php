<?php

/**
 * app/Controllers/NewsController.php
 * Xử lý request liên quan đến tin tức (Giao diện Frontend của Khách hàng)
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Models\News;
use App\Models\Category;

class NewsController
{
    private News $newsModel;
    private Category $categoryModel;

    public function __construct()
    {
        $this->newsModel = new News();
        $this->categoryModel = new Category();
    }

    public function index()
    {
        $newsList = $this->newsModel->getAllActive();

        $this->render('news/index', [
            'title'    => 'Tin tức & Hoạt động — Gunpla Shop',
            'newsList' => $newsList
        ]);
    }

    /**
     * Hiển thị trang chi tiết bài viết tin tức dựa trên chuỗi Slug
     * @param string $slug
     */
    public function detail(string $slug)
    {
        $article = $this->newsModel->findBySlug($slug);

        if (!$article) {
            if (file_exists(__DIR__ . '/../Views/errors/404.php')) {
                $this->render('errors/404', [
                    'title' => 'Bài viết không tồn tại — Gunpla Shop'
                ]);
            } else {
                header("HTTP/1.0 404 Not Found");
                echo "Bài viết này không tồn tại hoặc đã bị gỡ bỏ khỏi hệ thống.";
            }
            exit();
        }

        $this->render('news/detail', [
            'title'   => htmlspecialchars($article['title']) . ' — Gunpla Shop',
            'article' => $article
        ]);
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);
        $viewFile = APP_PATH . '/Views/' . $view . '.php';

        ob_start();
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "<p>View không tìm thấy: $viewFile</p>";
        }
        $content = ob_get_clean();

        include APP_PATH . '/Views/layouts/main.php';
    }

    private function redirect(string $url): void
    {
        header("Location: " . BASE_URL . $url);
        exit;
    }
    
}