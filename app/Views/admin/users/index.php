<?php
/**
 * app/Views/admin/users/index.php — Quản lý Khách hàng
 * @var array $users   List of users: id, full_name, email, phone, role, created_at
 */
?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
  <h2 style="font-family:var(--font-d);font-size:22px;letter-spacing:.08em;">
    DANH SÁCH KHÁCH HÀNG
    <span style="font-family:var(--font-m);font-size:12px;color:var(--text-2);font-weight:normal;margin-left:10px">
      (<?= count($users) ?> tài khoản)
    </span>
  </h2>
</div>

<div class="admin-table-wrap">
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Họ &amp; Tên</th>
        <th>Email</th>
        <th>Số điện thoại</th>
        <th>Vai trò</th>
        <th>Ngày tạo</th>
        <th>Thao tác</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($users)): ?>
        <tr>
          <td colspan="7" style="text-align:center;padding:40px;color:var(--text-2);font-family:var(--font-m);font-size:11px;">
            Chưa có tài khoản nào.
          </td>
        </tr>
      <?php else: foreach ($users as $u): ?>
        <tr>
          <td style="font-family:var(--font-m);color:var(--text-2);font-size:11px;"><?= $u['id'] ?></td>

          <td style="font-weight:500;"><?= htmlspecialchars($u['full_name']) ?></td>

          <td style="font-family:var(--font-m);font-size:11px;color:var(--text-2);">
            <?= htmlspecialchars($u['email']) ?>
          </td>

          <td style="font-family:var(--font-m);font-size:11px;color:var(--text-2);">
            <?= htmlspecialchars($u['phone'] ?? '—') ?>
          </td>

          <td>
            <?php if ($u['role'] === 'admin'): ?>
              <span class="badge badge-confirmed">ADMIN</span>
            <?php else: ?>
              <span class="badge badge-pending">CUSTOMER</span>
            <?php endif; ?>
          </td>

          <td style="font-family:var(--font-m);font-size:10px;color:var(--text-2);white-space:nowrap;">
            <?= date('d/m/Y H:i', strtotime($u['created_at'])) ?>
          </td>

          <td>
            <div style="display:flex;gap:6px;flex-wrap:nowrap;">

              <!-- Nút đổi quyền -->
              <form method="POST"
                    action="<?= BASE_URL ?>/admin/changeuserrole/<?= $u['id'] ?>"
                    onsubmit="return confirm('Chuyển quyền tài khoản #<?= $u['id'] ?> thành <?= $u['role'] === 'admin' ? 'CUSTOMER' : 'ADMIN' ?>?')">
                <input type="hidden" name="current_role" value="<?= htmlspecialchars($u['role']) ?>">
                <button type="submit"
                        class="btn btn-sm <?= $u['role'] === 'admin' ? '' : 'btn-gold' ?>"
                        title="<?= $u['role'] === 'admin' ? 'Hạ xuống Customer' : 'Nâng lên Admin' ?>">
                  <?= $u['role'] === 'admin' ? '↓ Customer' : '↑ Admin' ?>
                </button>
              </form>

              <!-- Nút xóa (chặn tự xóa mình) -->
              <?php if ((int)$u['id'] !== (int)($_SESSION['user']['id'] ?? 0)): ?>
                <form method="POST"
                      action="<?= BASE_URL ?>/admin/deleteuser/<?= $u['id'] ?>"
                      onsubmit="return confirm('Bạn có chắc chắn muốn XÓA tài khoản #<?= $u['id'] ?> (<?= htmlspecialchars(addslashes($u['email'])) ?>)?\n\nHành động này KHÔNG thể hoàn tác!')">
                  <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                </form>
              <?php else: ?>
                <span style="font-family:var(--font-m);font-size:9px;color:var(--text-3);padding:4px 6px;">
                  (bạn)
                </span>
              <?php endif; ?>

            </div>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
