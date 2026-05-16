<?php
/**
 * @var array $reviews
 */
?>

<div class="admin-header">
    <h2>Quản lý Đánh giá (Reviews)</h2>
</div>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Khách hàng</th>
                <th>Sản phẩm</th>
                <th>Số sao (Rating)</th>
                <th>Nội dung</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reviews ?? [] as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['user_name']) ?></td>
                <td><?= htmlspecialchars($r['product_name']) ?></td>
                <td><?= str_repeat('⭐', $r['rating']) ?></td>
                <td style="max-width: 300px; white-space: normal;"><?= htmlspecialchars($r['comment']) ?></td>
                <td>
                    <span class="badge <?= $r['status'] === 'approved' ? 'bg-success' : ($r['status'] === 'rejected' ? 'bg-danger' : 'bg-warning') ?>">
                        <?= strtoupper($r['status']) ?>
                    </span>
                </td>
                <td>
                    <form method="POST" action="<?= BASE_URL ?>/admin/reviews" style="display:inline-flex; gap:5px;">
                        <input type="hidden" name="review_id" value="<?= $r['id'] ?>">
                        <?php if ($r['status'] !== 'approved'): ?>
                            <button type="submit" name="status" value="approved" class="btn btn-sm btn-success">Duyệt</button>
                        <?php endif; ?>
                        <?php if ($r['status'] !== 'rejected'): ?>
                            <button type="submit" name="status" value="rejected" class="btn btn-sm btn-danger">Ẩn</button>
                        <?php endif; ?>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>