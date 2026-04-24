<?php
/**
 * app/views/admin/dashboard.php
 * Biến từ AdminController::dashboard():
 *   $stats, $revenueChart, $latestOrders, $lowStockProducts
 */

$statusLabels = [
    'pending'   => 'Chờ xác nhận',
    'confirmed' => 'Đã xác nhận',
    'shipping'  => 'Đang giao',
    'delivered' => 'Đã giao',
    'cancelled' => 'Đã hủy',
];
?>

<!-- Stats row -->
<div class="stats-grid" style="grid-template-columns:repeat(6,1fr)">
  <div class="stat-card">
    <div class="stat-card-label">Sản phẩm</div>
    <div class="stat-card-val c-gold"><?= number_format($stats['total_products']) ?></div>
    <div class="stat-card-sub">Đang bán</div>
  </div>
  <div class="stat-card">
    <div class="stat-card-label">Tổng đơn</div>
    <div class="stat-card-val c-blue"><?= number_format($stats['total_orders']) ?></div>
    <div class="stat-card-sub">Tất cả trạng thái</div>
  </div>
  <div class="stat-card">
    <div class="stat-card-label">Chờ duyệt</div>
    <div class="stat-card-val c-amber"><?= number_format($stats['pending_orders']) ?></div>
    <div class="stat-card-sub">Cần xử lý</div>
  </div>
  <div class="stat-card">
    <div class="stat-card-label">Doanh thu hôm nay</div>
    <div class="stat-card-val c-green" style="font-size:20px"><?= number_format($stats['revenue_today'], 0, ',', '.') ?>đ</div>
    <div class="stat-card-sub"><?= date('d/m/Y') ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-card-label">Doanh thu tháng</div>
    <div class="stat-card-val c-green" style="font-size:20px"><?= number_format($stats['revenue_month'], 0, ',', '.') ?>đ</div>
    <div class="stat-card-sub">Tháng <?= date('m/Y') ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-card-label">Sắp hết kho</div>
    <div class="stat-card-val <?= $stats['low_stock'] > 0 ? 'c-red' : 'c-green' ?>"><?= $stats['low_stock'] ?></div>
    <div class="stat-card-sub">Tồn kho ≤ 5</div>
  </div>
</div>

<!-- Chart + Low stock side by side -->
<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;margin-bottom:24px">

  <!-- Doanh thu 7 ngày -->
  <div class="admin-table-wrap">
    <div class="admin-table-head">
      <span class="admin-table-title">Doanh thu 7 ngày gần nhất</span>
    </div>
    <div style="padding:20px">
      <canvas id="revenueChart" height="180"></canvas>
    </div>
  </div>

  <!-- Sản phẩm sắp hết hàng -->
  <div class="admin-table-wrap">
    <div class="admin-table-head">
      <span class="admin-table-title">Cảnh báo tồn kho</span>
      <a href="/admin/inventory" class="btn btn-sm">Xem kho</a>
    </div>
    <div style="padding:8px 0">
      <?php foreach ($lowStockProducts as $p): ?>
        <?php
          $pct   = min(100, $p['stock'] > 0 ? ($p['stock'] / 20) * 100 : 0);
          $color = $p['stock'] === 0 ? 'var(--red)' : ($p['stock'] <= 3 ? 'var(--amber)' : 'var(--gold)');
        ?>
        <div style="padding:8px 18px;border-bottom:1px solid var(--border)">
          <div style="display:flex;justify-content:space-between;margin-bottom:5px">
            <div style="font-size:11px;color:var(--text-1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px">
              <?= htmlspecialchars($p['name']) ?>
            </div>
            <div style="font-family:var(--font-m);font-size:10px;color:<?= $color ?>;flex-shrink:0;margin-left:8px">
              <?= $p['stock'] === 0 ? 'HẾT' : $p['stock'] . ' cái' ?>
            </div>
          </div>
          <div class="stock-bar">
            <div class="stock-bar-fill" style="width:<?= $pct ?>%;background:<?= $color ?>"></div>
          </div>
        </div>
      <?php endforeach; ?>
      <?php if (empty($lowStockProducts)): ?>
        <div style="padding:24px;text-align:center;font-family:var(--font-m);font-size:11px;color:var(--text-3)">
          Tất cả sản phẩm còn đủ hàng ✓
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Đơn hàng mới nhất -->
<div class="admin-table-wrap">
  <div class="admin-table-head">
    <span class="admin-table-title">Đơn hàng gần nhất</span>
    <a href="/admin/orders" class="btn btn-sm">Xem tất cả</a>
  </div>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Khách hàng</th>
        <th>Tỉnh/Thành</th>
        <th>Tổng tiền</th>
        <th>Trạng thái</th>
        <th>Ngày đặt</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($latestOrders as $order): ?>
        <tr>
          <td style="font-family:var(--font-m);color:var(--text-2)">#<?= $order['id'] ?></td>
          <td>
            <div style="font-size:12px"><?= htmlspecialchars($order['full_name']) ?></div>
            <div style="font-family:var(--font-m);font-size:10px;color:var(--text-2)"><?= htmlspecialchars($order['phone']) ?></div>
          </td>
          <td style="color:var(--text-2)"><?= htmlspecialchars($order['province']) ?></td>
          <td style="font-family:var(--font-d);font-size:16px;color:var(--gold)">
            <?= number_format($order['total'], 0, ',', '.') ?>đ
          </td>
          <td><span class="badge badge-<?= $order['status'] ?>"><?= $statusLabels[$order['status']] ?? $order['status'] ?></span></td>
          <td style="font-family:var(--font-m);font-size:10px;color:var(--text-2)"><?= date('d/m H:i', strtotime($order['created_at'])) ?></td>
          <td>
            <a href="/admin/orders?highlight=<?= $order['id'] ?>" class="btn btn-sm">Chi tiết</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const chartData = <?= json_encode($revenueChart) ?>;

// Tạo mảng 7 ngày gần nhất
const days = [];
const revenues = [];
for (let i = 6; i >= 0; i--) {
  const d = new Date(); d.setDate(d.getDate() - i);
  const key = d.toISOString().slice(0, 10);
  days.push(d.toLocaleDateString('vi-VN', {day:'2-digit',month:'2-digit'}));
  const found = chartData.find(r => r.date === key);
  revenues.push(found ? parseInt(found.revenue) : 0);
}

new Chart(document.getElementById('revenueChart'), {
  type: 'bar',
  data: {
    labels: days,
    datasets: [{
      data: revenues,
      backgroundColor: 'rgba(200,168,90,0.25)',
      borderColor: '#c8a85a',
      borderWidth: 1,
      borderRadius: 3,
      hoverBackgroundColor: 'rgba(200,168,90,0.45)',
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#7a7874', font: { family: 'Share Tech Mono', size: 10 } } },
      y: {
        grid: { color: 'rgba(255,255,255,0.04)' },
        ticks: {
          color: '#7a7874',
          font: { family: 'Share Tech Mono', size: 10 },
          callback: v => v >= 1000000 ? (v/1000000).toFixed(1) + 'M' : (v >= 1000 ? (v/1000).toFixed(0) + 'K' : v)
        }
      }
    }
  }
});
</script>
