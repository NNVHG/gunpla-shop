<?php
// Đảm bảo dữ liệu bài viết tồn tại trước khi render tránh lỗi hệ thống
$article = $article ?? null;
?>

<?php if ($article): ?>
<div class="news-detail-container" style="max-width: 800px; margin: 0 auto; padding: 40px 20px; font-family: var(--font-m, 'Segoe UI', sans-serif); color: var(--text-1); background: transparent;">
    
    <div style="margin-bottom: 24px;">
        <a href="<?= BASE_URL ?>/news" style="color: var(--gold, #d69e2e); text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
            ← Quay lại danh sách tin tức
        </a>
    </div>

    <header class="article-header" style="margin-bottom: 30px; border-bottom: 1px solid var(--border); padding-bottom: 20px;">
        <h1 style="font-size: 30px; line-height: 1.3; margin: 0 0 12px 0; font-family: var(--font-b, inherit); color: var(--text-1);">
            <?= htmlspecialchars($article['title']) ?>
        </h1>
        
        <div class="article-meta" style="font-size: 13px; color: var(--text-3); display: flex; align-items: center; gap: 6px;">
            <span>📅</span>
            <span>Đăng ngày: <?= date('d/m/Y', strtotime($article['created_at'])) ?></span>
        </div>
    </header>

    <?php if (!empty($article['thumbnail']) && file_exists(__DIR__ . '/../../../public/' . $article['thumbnail'])): ?>
        <div class="article-thumbnail" style="width: 100%; max-height: 450px; border-radius: 12px; overflow: hidden; margin-bottom: 35px; border: 1px solid var(--border);">
            <img src="<?= BASE_URL . '/' . htmlspecialchars($article['thumbnail']) ?>" 
                 alt="<?= htmlspecialchars($article['title']) ?>" 
                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
        </div>
    <?php endif; ?>

    <?php if (!empty($article['summary'])): ?>
        <div class="article-summary" style="font-size: 16px; font-style: italic; color: var(--text-2); line-height: 1.6; padding-left: 16px; border-left: 4px solid var(--gold, #d69e2e); margin-bottom: 30px;">
            <?= htmlspecialchars($article['summary']) ?>
        </div>
    <?php endif; ?>

    <div class="article-content" style="font-size: 15px; color: var(--text-1); line-height: 1.8; letter-spacing: 0.3px; word-wrap: break-word;">
        <?= $article['content'] ?>
    </div>

</div>
<?php endif; ?>