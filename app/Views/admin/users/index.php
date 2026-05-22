<?php

/**
 * app/Views/admin/users/index.php — Quản lý Khách hàng
 * @var array $users   List of users: id, full_name, email, phone, role, created_at
 */
?>
<div class="admin-header">
    <h1 class="admin-title">Danh sách Khách hàng</h1>
</div>

<div class="admin-table-wrap">
    <div class="admin-table-scroll">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên Khách Hàng</th>
                <th>Thông tin liên lạc</th>
                <th>Quyền</th>
                <th>Ngày tạo</th>
                <th style="text-align: right;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td style="font-family: var(--font-m); color: var(--text-2); font-size:13px;">#<?= $u['id'] ?></td>
                    <td style="font-weight: 500; color: var(--gold);"><?= htmlspecialchars($u['full_name']) ?></td>
                    <td>
                        <div style="color: var(--text-1); font-size:14px; margin-bottom: 2px;"><?= htmlspecialchars($u['email']) ?></div>
                        <div style="font-family: var(--font-m); font-size:12px; color: var(--text-3);"><?= htmlspecialchars($u['phone'] ?? 'Chưa cập nhật') ?></div>
                    </td>
                    <td>
                        <?php if ($u['role'] === 'admin'): ?>
                            <span class="badge badge-delivered">Admin</span>
                        <?php else: ?>
                            <span class="badge badge-pending">Customer</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-family: var(--font-m); font-size:13px; color: var(--text-2);">
                        <?= date('d/m/Y', strtotime($u['created_at'])) ?>
                    </td>
                    <td style="text-align: right;">
                        <form method="POST" action="<?= BASE_URL ?>/admin/changeUserRole/<?= $u['id'] ?>" style="display: inline-block;">
                            <input type="hidden" name="current_role" value="<?= $u['role'] ?>">
                            <button type="submit" class="btn btn-sm" onclick="return confirm('Thay đổi quyền tài khoản này?')" style="margin-right: 6px;">Đổi Quyền</button>
                        </form>
                        <form method="POST" action="<?= BASE_URL ?>/admin/deleteUser/<?= $u['id'] ?>" style="display: inline-block;">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Xóa vĩnh viễn người dùng này?')">Xóa</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>