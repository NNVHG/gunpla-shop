<?php
/**
 * app/views/admin/orders/index.php
 * Biến: $orders, $statusCounts, $currentStatus, $pages, $page
 */

$statusLabels = [
    'pending'   => 'Chờ xác nhận',
    'confirmed' => 'Đã xác nhận',
    'shipping'  => 'Đang giao',
    'delivered' => 'Đã giao',
    'cancelled' => 'Đã hủy',
];
$nextStatus = [
    'pending'   => 'confirmed',
    'confirmed' => 'shipping',
    'shipping'  => 'delivered',
];
$nextLabel = [
    'pending'   => 'Xác nhận',
    'confirmed' => 'Giao hàng',
    'shipping'  => 'Đã nhận',
];
?>

<!-- Filter tabs -->
<div style="display:flex;gap:0;border:1px solid var(--border);border-radius:6px;overflow:hidden;margin-bottom:20px;width:fit-content">
  <a href="/admin/orders" class="tab <?= $currentStatus==='' ? 'active' : '' ?>">
    Tất cả <span class="tab-count"><?= array_sum($statusCounts) ?></span>
  </a>
  <?php foreach ($statusLabels as $key => $label): ?>
    <a href="/admin/orders?status=<?= $key ?>" class="tab <?= $currentStatus===$key ? 'active' : '' ?>">
      <?= $label ?> <span class="tab-count"><?= $statusCounts[$key] ?? 0 ?></span>
    </a>
  <?php endforeach; ?>
</div>

<div class="admin-table-wrap">
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Khách hàng</th>
        <th>Địa chỉ</th>
        <th>Tạm tính</th>
        <th>Ship</th>
        <th>Tổng</th>
        <th>Trạng thái</th>
        <th>Ngày đặt</th>
        <th>Thao tác</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($orders as $order): ?>
        <tr id="row-<?= $order['id'] ?>">
          <td style="font-family:var(--font-m);color:var(--text-2)">#<?= $order['id'] ?></td>
          <td>
            <div style="font-weight:500"><?= htmlspecialchars($order['full_name']) ?></div>
            <div style="font-family:var(--font-m);font-size:10px;color:var(--text-2)"><?= htmlspecialchars($order['phone']) ?></div>
          </td>
          <td style="font-size:11px;color:var(--text-2);max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
            <?= htmlspecialchars($order['address']) ?>, <?= htmlspecialchars($order['province']) ?>
          </td>
          <td style="font-family:var(--font-m);font-size:11px;color:var(--text-2)"><?= number_format($order['subtotal'],0,',','.') ?>đ</td>
          <td style="font-family:var(--font-m);font-size:11px;color:var(--text-2)"><?= number_format($order['shipping_fee'],0,',','.') ?>đ</td>
          <td style="font-family:var(--font-d);font-size:16px;color:var(--gold)"><?= number_format($order['total'],0,',','.') ?>đ</td>
          <td>
            <span class="badge badge-<?= $order['status'] ?>" id="badge-<?= $order['id'] ?>">
              <?= $statusLabels[$order['status']] ?? $order['status'] ?>
            </span>
          </td>
          <td style="font-family:var(--font-m);font-size:10px;color:var(--text-2);white-space:nowrap">
            <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
          </td>
          <td>
            <div style="display:flex;gap:6px;flex-wrap:nowrap">
              <?php if (isset($nextStatus[$order['status']])): ?>
                <button class="btn btn-gold btn-sm"
                  onclick="updateStatus(<?= $order['id'] ?>, '<?= $nextStatus[$order['status']] ?>', this)">
                  <?= $nextLabel[$order['status']] ?>
                </button>
              <?php endif; ?>
              <?php if ($order['status'] !== 'cancelled' && $order['status'] !== 'delivered'): ?>
                <button class="btn btn-danger btn-sm"
                  onclick="updateStatus(<?= $order['id'] ?>, 'cancelled', this)">
                  Hủy
                </button>
              <?php endif; ?>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($orders)): ?>
        <tr><td colspan="9" style="text-align:center;padding:40px;color:var(--text-3);font-family:var(--font-m);font-size:11px">Không có đơn hàng nào</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- Phân trang -->
  <?php if ($pages > 1): ?>
    <div class="pagination">
      <?php for ($i = 1; $i <= $pages; $i++): ?>
        <a href="?status=<?= $currentStatus ?>&page=<?= $i ?>" class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  <?php endif; ?>
</div>


