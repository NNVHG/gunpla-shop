<?php

/**
 * @var array $order
 */
$statusLabels = [
    'pending'   => 'Chờ xác nhận',
    'confirmed' => 'Đã xác nhận',
    'shipping'  => 'Đang giao hàng',
    'delivered' => 'Đã giao thành công',
    'cancelled' => 'Đã hủy'
];

$statusColors = [
    'pending'   => 'var(--amber)',
    'confirmed' => 'var(--blue)',
    'shipping'  => '#aa70e0', // Tông màu tím nhẹ trong dark mode
    'delivered' => 'var(--green)',
    'cancelled' => 'var(--red)'
];
$paymentStatusLabels = [
    'unpaid' => 'Chưa thanh toán',
    'paid'   => 'Đã thanh toán',
    'failed' => 'Thanh toán thất bại'
];
$paymentStatusColors = [
    'unpaid' => 'var(--amber)',
    'paid'   => 'var(--green)',
    'failed' => 'var(--red)'
];
?>
<div class="admin-header">
    <h1 class="admin-title">Chi tiết Đơn hàng #<?= $order['id'] ?></h1>
    <a href="<?= BASE_URL ?>/admin/orders" class="btn" style="color:var(--text-2)">Quay lại</a>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
    <div class="admin-table-wrap" style="padding: 20px;">
        <h3 style="color:var(--gold); font-family:var(--font-d); letter-spacing: 0.08em; font-size: 18px; margin-top:0; border-bottom:1px solid var(--border); padding-bottom:10px">Khách hàng</h3>
        <p style="margin-top: 12px; margin-bottom: 8px;"><strong>Người nhận:</strong> <span style="color:var(--text-1)"><?= htmlspecialchars($order['full_name']) ?></span></p>
        <p style="margin-bottom: 8px;"><strong>Số điện thoại:</strong> <span style="color:var(--text-1)"><?= htmlspecialchars($order['phone']) ?></span></p>
        <p style="margin-bottom: 8px;"><strong>Tỉnh/Thành phố:</strong> <span style="color:var(--text-1)"><?= htmlspecialchars($order['province']) ?></span></p>
        <p style="margin-bottom: 8px;"><strong>Địa chỉ chi tiết:</strong> <span style="color:var(--text-1)"><?= htmlspecialchars($order['address']) ?></span></p>
        <p style="margin-bottom: 8px;"><strong>Ghi chú:</strong> <span style="color:var(--text-1)"><?= htmlspecialchars($order['note'] ?? 'Không có') ?></span></p>
        <p style="margin-bottom: 0;"><strong>Ngày đặt:</strong> <span style="color:var(--text-1)"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span></p>
    </div>

    <div class="admin-table-wrap" style="padding: 20px;">
        <h3 style="color:var(--gold); font-family:var(--font-d); letter-spacing: 0.08em; font-size: 18px; margin-top:0; border-bottom:1px solid var(--border); padding-bottom:10px">Trạng thái</h3>

        <p style="margin-top: 12px; margin-bottom: 8px;"><strong>Phương thức:</strong> <span style="color:var(--text-1)"><?= strtoupper($order['payment_method']) ?></span></p>
        <p style="margin-bottom: 8px;">
            <strong>Thanh toán:</strong>
            <span style="color: <?= $paymentStatusColors[$order['payment_status']] ?>; font-weight: bold;">
                <?= $paymentStatusLabels[$order['payment_status']] ?? $order['payment_status'] ?>
            </span>
        </p>
        <?php if ($order['transaction_id']): ?>
            <p style="margin-bottom: 8px;"><strong>Mã GD (VNPAY):</strong> <span style="color:var(--text-1)"><?= htmlspecialchars($order['transaction_id']) ?></span></p>
        <?php endif; ?>
        
        <?php if ($order['payment_method'] === 'cod' && $order['payment_status'] !== 'paid'): ?>
            <button class="btn" id="btnMarkPaid" data-id="<?= $order['id'] ?>" style="margin-top:8px; padding:6px 12px; font-size:11px; background:var(--bg-panel); border-color:var(--green); color:var(--green)">
                ✓ Xác nhận đã thu tiền
            </button>
        <?php endif; ?>

        <div style="margin-top:20px; border-top:1px solid var(--border); padding-top:16px">
            <p style="margin-bottom: 12px;">
                <strong>Giao hàng:</strong>
                <span class="badge badge-<?= $order['status'] ?>" style="font-size: 11px;">
                    <?= $statusLabels[$order['status']] ?? $order['status'] ?>
                </span>
            </p>

            <div>
                <p style="margin-bottom: 8px; color:var(--text-3); font-family: var(--font-m); font-size: 10px;">CẬP NHẬT TRẠNG THÁI:</p>
                <div style="display:flex; gap:8px; flex-wrap:wrap">
                    <button class="btn status-btn" data-id="<?= $order['id'] ?>" data-status="confirmed" style="color:var(--blue); border-color:var(--blue)">Xác nhận</button>
                    <button class="btn status-btn" data-id="<?= $order['id'] ?>" data-status="shipping" style="color:#aa70e0; border-color:#aa70e0">Giao hàng</button>
                    <button class="btn status-btn" data-id="<?= $order['id'] ?>" data-status="delivered" style="color:var(--green); border-color:var(--green)">Đã giao</button>
                    <button class="btn status-btn" data-id="<?= $order['id'] ?>" data-status="cancelled" style="color:var(--red); border-color:var(--red)">Hủy đơn</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="admin-table-wrap">
    <div class="admin-table-head"><span class="admin-table-title">SẢN PHẨM ĐÃ ĐẶT</span></div>
    <table style="margin-bottom: 0;">
        <thead>
            <tr>
                <th>Ảnh</th>
                <th>Sản phẩm</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th style="text-align: right;">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($order['items'] as $item): ?>
                <tr>
                    <td>
                        <?php if (!empty($item['thumbnail_path'])): ?>
                            <img src="<?= BASE_URL . '/' . $item['thumbnail_path'] ?>" width="40" height="40" style="object-fit:cover; border-radius:4px; border:1px solid var(--border)">
                        <?php else: ?>
                            <div style="width:40px;height:40px;background:var(--bg-panel);border-radius:4px; border:1px solid var(--border)"></div>
                        <?php endif; ?>
                    </td>
                    <td style="font-weight:500; color:var(--text-1)"><?= htmlspecialchars($item['product_name']) ?></td>
                    <td style="font-family: var(--font-m); color:var(--text-2)"><?= $item['quantity'] ?></td>
                    <td style="font-family: var(--font-m); color:var(--text-2)"><?= number_format((float)$item['price_at_order'], 0, ',', '.') ?>đ</td>
                    <td style="font-weight:bold; color:var(--gold); text-align: right; font-family: var(--font-d); font-size: 16px; letter-spacing: 0.05em;"><?= number_format((float)($item['price_at_order'] * $item['quantity']), 0, ',', '.') ?>đ</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot style="background: var(--bg-panel);">
            <tr>
                <td colspan="4" style="text-align:right; padding:12px 16px; color: var(--text-2); font-size: 12px;">Tạm tính:</td>
                <td style="font-weight:bold; padding:12px 16px; text-align: right; color: var(--text-1); font-family: var(--font-d); font-size: 16px;"><?= number_format((float)$order['subtotal'], 0, ',', '.') ?>đ</td>
            </tr>
            <tr>
                <td colspan="4" style="text-align:right; padding:12px 16px; color: var(--text-2); font-size: 12px;">Phí vận chuyển:</td>
                <td style="font-weight:bold; padding:12px 16px; text-align: right; color: var(--text-1); font-family: var(--font-d); font-size: 16px;"><?= number_format((float)$order['shipping_fee'], 0, ',', '.') ?>đ</td>
            </tr>
            <tr>
                <td colspan="4" style="text-align:right; color:var(--gold); font-size:16px; font-weight:bold; padding:16px;">TỔNG CỘNG:</td>
                <td style="font-weight:bold; color:var(--gold); font-size:22px; padding:16px; text-align: right; font-family: var(--font-d); letter-spacing: 0.05em;"><?= number_format((float)$order['total'], 0, ',', '.') ?>đ</td>
            </tr>
        </tfoot>
    </table>
</div>

<script>
    document.querySelectorAll('.status-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.dataset.id;
            const status = this.dataset.status;
            if (!confirm('Chuyển trạng thái đơn hàng này?')) return;

            fetch('<?= BASE_URL ?>/admin/orderstatus', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `order_id=${orderId}&status=${status}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Cập nhật trạng thái thành công!');
                        location.reload();
                    } else {
                        alert('Có lỗi xảy ra.');
                    }
                });
        });
    });

    const btnMarkPaid = document.getElementById('btnMarkPaid');
    if (btnMarkPaid) {
        btnMarkPaid.addEventListener('click', function() {
            if (!confirm('Xác nhận đã nhận tiền (COD) cho đơn hàng này?')) return;
            fetch('<?= BASE_URL ?>/admin/markpaid', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `order_id=${this.dataset.id}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Đã cập nhật trạng thái thanh toán!');
                        location.reload();
                    } else {
                        alert('Có lỗi xảy ra.');
                    }
                });
        });
    }
</script>