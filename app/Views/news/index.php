<?php
// Kiểm tra biến truyền từ Controller
$newsList = $newsList ?? [];
?>

<div class="news-page-wrapper" style="max-width: 1200px; margin: 0 auto; padding: 40px 20px; font-family: var(--font-m, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif);">
    
    <div class="news-header" style="text-align: center; margin-bottom: 40px; border-bottom: 2px solid var(--border); padding-bottom: 20px;">
        <h1 style="font-size:34px; color: var(--text-1); text-transform: uppercase; margin: 0 0 10px 0; letter-spacing: 1px; font-family: var(--font-b, inherit);">
            Tin tức & Sự kiện
        </h1>
        <p style="color: var(--text-2); font-size:17px; margin: 0;">
            Cập nhật những thông tin mới nhất về mô hình Gunpla và các chương trình khuyến mãi
        </p>
    </div>

    <?php if (!empty($newsList) && is_array($newsList)): ?>
        <div class="news-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px;">
            
            <?php foreach ($newsList as $item): ?>
                <article class="news-card" style="background: var(--bg-2, var(--bg-content, transparent)); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); display: flex; flex-direction: column; transition: transform 0.2s ease, box-shadow 0.2s ease;">
                    
                    <div class="card-thumbnail" style="position: relative; width: 100%; height: 220px; background: var(--border); overflow: hidden;">
                        <?php if (!empty($item['thumbnail']) && file_exists(__DIR__ . '/../../../public/' . $item['thumbnail'])): ?>
                            <img src="<?= BASE_URL . '/' . htmlspecialchars($item['thumbnail']) ?>" 
                                 alt="<?= htmlspecialchars($item['title']) ?>" 
                                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
                        <?php else: ?>
                            <div style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--text-3); background: var(--bg-2);">
                                <span style="font-size:42px; margin-bottom: 8px;">📰</span>
                                <span style="font-size:14px;">Không có hình ảnh</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="card-body" style="padding: 24px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div class="card-meta" style="font-size:14px; color: var(--text-3); margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                                <span>📅</span>
                                <span><?= isset($item['created_at']) ? date('d/m/Y', strtotime($item['created_at'])) : 'Chưa rõ ngày' ?></span>
                            </div>

                            <h2 class="card-title" style="font-size:20px; line-height: 1.4; margin: 0 0 12px 0; font-family: var(--font-b, inherit);">
                                <a href="<?= BASE_URL . '/news/' . htmlspecialchars($item['slug'] ?? '') ?>" 
                                   style="color: var(--text-1); text-decoration: none; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?= htmlspecialchars($item['title'] ?? 'Tiêu đề trống') ?>
                                </a>
                            </h2>

                            <p class="card-summary" style="font-size:16px; color: var(--text-2); line-height: 1.6; margin: 0 0 20px 0; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= htmlspecialchars($item['summary'] ?? 'Chưa có tóm tắt cho bài viết này.') ?>
                            </p>
                        </div>

                        <div class="card-footer" style="margin-top: auto; padding-top: 15px; border-top: 1px solid var(--border);">
                            <a href="<?= BASE_URL . '/news/' . htmlspecialchars($item['slug'] ?? '') ?>" 
                               style="display: inline-flex; align-items: center; color: var(--gold, #d69e2e); text-decoration: none; font-weight: 600; font-size:16px; gap: 5px;">
                                Đọc bài viết <span style="font-size:18px;">→</span>
                            </a>
                        </div>
                    </div>

                </article>
            <?php endforeach; ?>

        </div>
    <?php else: ?>
        <div class="no-news" style="text-align: center; padding: 60px 20px; background: var(--bg-2, transparent); border-radius: 8px; border: 1px dashed var(--border);">
            <span style="font-size:50px; display: block; margin-bottom: 16px;">📂</span>
            <h3 style="color: var(--text-1); margin: 0 0 8px 0; font-size:20px;">Hiện chưa có bài viết nào</h3>
            <p style="color: var(--text-3); margin: 0; font-size:16px;">Vui lòng quay lại sau hoặc bổ sung bài viết mới từ trang quản trị hệ thống.</p>
        </div>
    <?php endif; ?>

</div>