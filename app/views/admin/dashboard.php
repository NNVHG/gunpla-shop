<?php

/**
 * app/views/admin/dashboard.php
 * @var array $stats
 * @var array $revenueChart
 * @var array $latestOrders
 * @var array $lowStockProducts
 */

$statusLabels = [
  'pending'   => 'Chờ xác nhận',
  'confirmed' => 'Đã xác nhận',
  'shipping'  => 'Đang giao',
  'delivered' => 'Đã giao',
  'cancelled' => 'Đã hủy',
];
?>

<div class="stats-grid">
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
    <div class="stat-card-val c-green" style="font-size:22px"><?= number_format($stats['revenue_today'], 0, ',', '.') ?>đ</div>
    <div class="stat-card-sub"><?= date('d/m/Y') ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-card-label">Doanh thu tháng</div>
    <div class="stat-card-val c-green" style="font-size:22px"><?= number_format($stats['revenue_month'], 0, ',', '.') ?>đ</div>
    <div class="stat-card-sub">Tháng <?= date('m/Y') ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-card-label">Sắp hết kho</div>
    <div class="stat-card-val <?= $stats['low_stock'] > 0 ? 'c-red' : 'c-green' ?>"><?= $stats['low_stock'] ?></div>
    <div class="stat-card-sub">Tồn kho ≤ 5</div>
  </div>
</div>

<div class="dashboard-layout">

  <div class="admin-table-wrap">
    <div class="admin-table-head" style="display:flex;justify-content:space-between;align-items:center;">
      <span class="admin-table-title" id="revenueChartTitle">Doanh thu cửa hàng</span>
      <div style="display:flex;align-items:center;gap:8px;font-family:'Share Tech Mono',var(--font-m);font-size:13px;flex-wrap:wrap;">
        <label for="startDateInput" style="color:var(--text-secondary)">Từ</label>
        <input type="date" id="startDateInput" value="<?= date('Y-m-d', strtotime('-29 days')) ?>" style="background:var(--bg-card);color:var(--gold);border:1px solid var(--border);padding:4px 8px;border-radius:4px;outline:none;cursor:pointer;font-family:inherit;">
        
        <label for="endDateInput" style="color:var(--text-secondary)">đến</label>
        <input type="date" id="endDateInput" value="<?= date('Y-m-d') ?>" style="background:var(--bg-card);color:var(--gold);border:1px solid var(--border);padding:4px 8px;border-radius:4px;outline:none;cursor:pointer;font-family:inherit;">
      </div>
    </div>
    <div style="padding:20px;height:240px;position:relative;">
      <canvas id="revenueChart"></canvas>
    </div>
  </div>

  <div class="admin-table-wrap">
    <div class="admin-table-head">
      <span class="admin-table-title">Cảnh báo tồn kho</span>
      <a href="<?= BASE_URL ?>/admin/inventory" class="btn btn-sm">Xem kho</a>
    </div>
    <div style="padding:8px 0">
      <?php foreach ($lowStockProducts as $p): ?>
        <?php
        $pct   = min(100, $p['stock'] > 0 ? ($p['stock'] / 20) * 100 : 0);
        $color = $p['stock'] === 0 ? 'var(--red)' : ($p['stock'] <= 3 ? 'var(--amber)' : 'var(--gold)');
        ?>
        <div style="padding:8px 18px;border-bottom:1px solid var(--border)">
          <div style="display:flex;justify-content:space-between;margin-bottom:5px">
            <div style="font-size:13px;color:var(--text-1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px">
              <?= htmlspecialchars($p['name']) ?>
            </div>
            <div style="font-family:var(--font-m);font-size:12px;color:<?= $color ?>;flex-shrink:0;margin-left:8px">
              <?= $p['stock'] === 0 ? 'HẾT' : $p['stock'] . ' cái' ?>
            </div>
          </div>
          <div class="stock-bar">
            <div class="stock-bar-fill" style="width:<?= $pct ?>%;background:<?= $color ?>"></div>
          </div>
        </div>
      <?php endforeach; ?>
      <?php if (empty($lowStockProducts)): ?>
        <div style="padding:24px;text-align:center;font-family:var(--font-m);font-size:13px;color:var(--text-3)">
          Tất cả sản phẩm còn đủ hàng ✓
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="admin-table-wrap">
  <div class="admin-table-head">
    <span class="admin-table-title">Đơn hàng gần nhất</span>
    <a href="<?= BASE_URL ?>/admin/orders" class="btn btn-sm">Xem tất cả</a>
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
            <div style="font-size:14px"><?= htmlspecialchars($order['full_name']) ?></div>
            <div style="font-family:var(--font-m);font-size:12px;color:var(--text-2)"><?= htmlspecialchars($order['phone']) ?></div>
          </td>
          <td style="color:var(--text-2)"><?= htmlspecialchars($order['province']) ?></td>
          <td style="font-family:var(--font-d);font-size:18px;color:var(--gold)">
            <?= number_format($order['total'], 0, ',', '.') ?>đ
          </td>
          <td><span class="badge badge-<?= $order['status'] ?>"><?= $statusLabels[$order['status']] ?? $order['status'] ?></span></td>
          <td style="font-family:var(--font-m);font-size:12px;color:var(--text-2)"><?= date('d/m H:i', strtotime($order['created_at'])) ?></td>
          <td>
            <a href="<?= BASE_URL ?>/admin/orders/detail/<?= $order['id'] ?>" class="btn btn-sm">Chi tiết</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
  let chartInstance = null;

  function renderRevenueChart(labels, revenue, orders) {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    if (chartInstance) {
      chartInstance.destroy();
    }

    const gradient = ctx.createLinearGradient(0, 0, 0, ctx.canvas.clientHeight || 200);
    gradient.addColorStop(0, 'rgba(200, 168, 90, 0.45)');
    gradient.addColorStop(1, 'rgba(200, 168, 90, 0.01)');

    chartInstance = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Doanh thu',
          data: revenue,
          borderColor: '#c8a85a',
          borderWidth: 2,
          fill: true,
          backgroundColor: gradient,
          tension: 0.35,
          pointBackgroundColor: '#c8a85a',
          pointBorderColor: 'var(--bg-card)',
          pointBorderWidth: 1,
          pointRadius: 4,
          pointHoverRadius: 6,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: 'rgba(20, 20, 20, 0.95)',
            titleColor: '#c8a85a',
            bodyColor: '#e0e0e0',
            borderColor: 'rgba(200, 168, 90, 0.3)',
            borderWidth: 1,
            padding: 12,
            displayColors: false,
            callbacks: {
              label: function(context) {
                const idx = context.dataIndex;
                const rev = context.raw;
                const ord = orders[idx] || 0;
                return [
                  'Doanh thu: ' + rev.toLocaleString('vi-VN') + 'đ',
                  'Số đơn hàng: ' + ord + ' đơn'
                ];
              }
            }
          }
        },
        scales: {
          x: {
            grid: {
              color: 'rgba(255,255,255,0.03)'
            },
            ticks: {
              color: '#7a7874',
              font: {
                family: 'Share Tech Mono',
                size: 11
              }
            }
          },
          y: {
            grid: {
              color: 'rgba(255,255,255,0.03)'
            },
            ticks: {
              color: '#7a7874',
              font: {
                family: 'Share Tech Mono',
                size: 11
              },
              callback: v => v >= 1000000 ? (v / 1000000).toFixed(1) + 'M' : (v >= 1000 ? (v / 1000).toFixed(0) + 'K' : v)
            }
          }
        }
      }
    });
  }

  async function loadChartData() {
    const start = document.getElementById('startDateInput').value;
    const end = document.getElementById('endDateInput').value;
    try {
      const res = await fetch(BASE_URL + '/admin/revenueData?start_date=' + start + '&end_date=' + end);
      const data = await res.json();
      renderRevenueChart(data.labels, data.revenue, data.orders);
    } catch(e) {
      console.error('Error fetching chart data:', e);
    }
  }

  document.getElementById('startDateInput').addEventListener('change', loadChartData);
  document.getElementById('endDateInput').addEventListener('change', loadChartData);

  loadChartData();
</script>