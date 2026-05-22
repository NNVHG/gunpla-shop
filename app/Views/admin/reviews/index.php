<?php
/**
 * @var array $reviews
 */
?>

<div class="admin-header">
    <h1 class="admin-title">Quản lý Đánh giá (Reviews)</h1>
</div>

<div class="admin-table-wrap">
    <table>
        <thead>
            <tr>
                <th>Khách hàng</th>
                <th>Sản phẩm</th>
                <th>Số sao (Rating)</th>
                <th>Nội dung</th>
                <th>Trạng thái</th>
                <th style="text-align: right;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reviews ?? [] as $r): ?>
            <tr>
                <td style="font-weight: 500; color: var(--gold);"><?= htmlspecialchars($r['user_name']) ?></td>
                <td style="color: var(--text-2); font-size:14px;"><?= htmlspecialchars($r['product_name']) ?></td>
                <td style="font-size:12px;"><?= str_repeat('⭐', $r['rating']) ?></td>
                <td style="max-width: 250px; white-space: normal; color: var(--text-1); font-size:14px;">
                    <?= htmlspecialchars($r['comment']) ?>
                </td>
                <td>
                    <?php if($r['status'] === 'approved'): ?>
                        <span class="badge badge-delivered">Đã duyệt</span>
                    <?php elseif($r['status'] === 'rejected'): ?>
                        <span class="badge badge-cancelled">Đã ẩn</span>
                    <?php else: ?>
                        <span class="badge badge-pending">Chờ duyệt</span>
                    <?php endif; ?>
                </td>
                <td style="text-align: right;">
                    <form method="POST" action="<?= BASE_URL ?>/admin/reviews" style="display:flex; gap:6px; justify-content: flex-end;">
                        <input type="hidden" name="review_id" value="<?= $r['id'] ?>">
                        <?php if ($r['status'] !== 'approved'): ?>
                            <button type="submit" name="status" value="approved" class="btn btn-sm" style="color: var(--green); border-color: var(--green);">Duyệt</button>
                        <?php endif; ?>
                        <?php if ($r['status'] !== 'rejected'): ?>
                            <button type="submit" name="status" value="rejected" class="btn btn-sm btn-danger">Ẩn</button>
                        <?php endif; ?>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($reviews)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-3); font-family: var(--font-m);">Chưa có đánh giá nào.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>