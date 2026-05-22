<?php
/**
 * @var array $categories
 */
?>
<div class="admin-header">
    <h1 class="admin-title">Quản lý Danh mục</h1>
    <a href="<?= BASE_URL ?>/admin/categories/create" class="btn-gold">
        + Thêm Danh Mục
    </a>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên danh mục</th>
                <th>Phân loại (Type)</th>
                <th>Danh mục cha</th>
                <th style="width:120px; text-align:right">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($categories)): ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding: 30px;">Chưa có danh mục nào.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td>#<?= $cat['id'] ?></td>
                        <td style="font-weight:600; color:var(--gold)"><?= htmlspecialchars($cat['name']) ?></td>
                        <td><span style="padding:4px 8px; background:var(--bg-card); border:1px solid var(--border); border-radius:4px; font-size:13px; text-transform:uppercase"><?= htmlspecialchars($cat['type']) ?></span></td>
                        <td><?= $cat['parent_name'] ? htmlspecialchars($cat['parent_name']) : '<span style="color:var(--text-hint)">--</span>' ?></td>
                        <td style="text-align:right">
                            <div style="display:flex; gap:8px; justify-content:flex-end">
                                <a href="<?= BASE_URL ?>/admin/categories/edit/<?= $cat['id'] ?>" class="btn-gold" style="padding:6px 12px; font-size:14px">Sửa</a>
                                <form method="POST" action="<?= BASE_URL ?>/admin/categories/delete/<?= $cat['id'] ?>" onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này? Các sản phẩm thuộc danh mục này có thể bị ảnh hưởng.');">
                                    <button type="submit" class="btn-gold" style="padding:6px 12px; font-size:14px; background:transparent; border-color:#ff4d4f; color:#ff4d4f">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
