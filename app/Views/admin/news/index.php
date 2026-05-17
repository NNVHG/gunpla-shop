<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <h1 class="admin-title">Quản lý Tin tức</h1>
    <a href="<?= BASE_URL ?>/admin/news/create" class="btn btn-gold">+ Thêm tin tức mới</a>
</div>

<div class="admin-table-wrap">
    <table>
        <thead>
            <tr>
                <th>Hình ảnh</th>
                <th>Tiêu đề bài viết</th>
                <th>Trạng thái</th>
                <th>Ngày đăng</th>
                <th style="text-align: right;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!isset($allNews) || empty($allNews)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-3); font-family: var(--font-m);">
                        Chưa có bài viết nào được cập nhật trong hệ thống.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($allNews as $item): ?>
                    <tr>
                        <td>
                           <?php if (!empty($item['thumbnail'])): ?>
                                <img src="<?= BASE_URL . '/' . $item['thumbnail'] ?>" style="width: 80px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid var(--border);">
                            <?php else: ?>
                                <div style="width: 80px; height: 50px; background: var(--bg-hover); display:flex; align-items:center; justify-content:center; font-size:11px; color:var(--text-3); border-radius: 4px; border: 1px solid var(--border);">No Img</div>
                            <?php endif; ?>
                        </td>
                        <td style="font-weight: 500; color: var(--text-1); max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <?= htmlspecialchars($item['title'] ?? '') ?>
                        </td>
                        <td style="text-align: center;">
                            <?php 
                            if (isset($item['is_active']) && $item['is_active'] == 1): 
                            ?>
                                <span class="badge badge-delivered">Hiển thị</span>
                            <?php else: ?>
                                <span class="badge badge-cancelled">Đang ẩn</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-family: var(--font-m); font-size: 11px; color: var(--text-2);">
                            <?php 
                            if (!empty($item['created_at'])) {
                                echo date('d/m/Y H:i', strtotime($item['created_at']));
                            } else {
                                echo 'Chưa cập nhật';
                            }
                            ?>
                        </td>
                        <td style="text-align: right;">
                            <div style="display:flex; gap:6px; justify-content: flex-end;">
                                <a href="<?= BASE_URL ?>/admin/news/edit/<?= $item['id'] ?>" class="btn btn-sm">Sửa</a>
                                <a href="<?= BASE_URL ?>/admin/news/delete/<?= $item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này không?')">Xóa</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>