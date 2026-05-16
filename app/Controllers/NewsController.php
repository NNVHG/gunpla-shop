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

    public function index(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $result = $this->newsModel->getAll($page, 10);
        
        $categories = $this->categoryModel->getTopLevel();

        $data = [
            'title'      => 'Tin tức — GUNPLA SHOP',
            'news'       => $result['items'] ?? [],
            'total'      => $result['total'] ?? 0,
            'pages'      => $result['pages'] ?? 1,
            'page'       => $result['page'] ?? $page,
            'categories' => $categories
        ];

        $this->render('news/index', $data);
    }

    public function detail(?string $param): void
    {
        if (!$param) {
            $this->redirect('/news');
            return;
        }

        $newsItem = is_numeric($param) 
            ? $this->newsModel->findById((int) $param)
            : $this->newsModel->findBySlug($param);

        if (!$newsItem) {
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Không tìm thấy tin tức']);
            return;
        }

        $latestNews = $this->newsModel->getLatest(5);
        $categories = $this->categoryModel->getTopLevel();

        $data = [
            'title'      => $newsItem['title'] . ' — GUNPLA SHOP',
            'newsItem'   => $newsItem,
            'latestNews' => $latestNews,
            'categories' => $categories
        ];

        $this->render('news/detail', $data);
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