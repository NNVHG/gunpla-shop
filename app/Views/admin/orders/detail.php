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
    'pending'   => 'var(--gold)',
    'confirmed' => '#17a2b8',
    'shipping'  => '#fd7e14',
    'delivered' => '#28a745',
    'cancelled' => '#dc3545'
];

$paymentStatusLabels = [
    'unpaid' => 'Chưa thanh toán',
    'paid'   => 'Đã thanh toán',
    'failed' => 'Thanh toán thất bại'
];
$paymentStatusColors = [
    'unpaid' => '#ffc107',
    'paid'   => '#28a745',
    'failed' => '#dc3545'
];
?>
<div class="admin-header">
    <h1 class="admin-title">Chi tiết Đơn hàng #<?= $order['id'] ?></h1>
    <a href="<?= BASE_URL ?>/admin/orders" class="btn-gold" style="background:transparent; border-color:var(--text-hint); color:var(--text-hint)">
        Quay lại
    </a>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
    <!-- Thông tin khách hàng -->
    <div class="admin-table-wrap" style="padding: 20px;">
        <h3 style="color:var(--gold); margin-top:0; border-bottom:1px solid var(--border); padding-bottom:10px">Thông tin Khách hàng</h3>
        <p style="margin-bottom: 8px;"><strong>Người nhận:</strong> <?= htmlspecialchars($order['full_name']) ?></p>
        <p style="margin-bottom: 8px;"><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['phone']) ?></p>
        <p style="margin-bottom: 8px;"><strong>Tỉnh/Thành phố:</strong> <?= htmlspecialchars($order['province']) ?></p>
        <p style="margin-bottom: 8px;"><strong>Địa chỉ chi tiết:</strong> <?= htmlspecialchars($order['address']) ?></p>
        <p style="margin-bottom: 8px;"><strong>Ghi chú:</strong> <?= htmlspecialchars($order['note'] ?? 'Không có') ?></p>
        <p style="margin-bottom: 0;"><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
    </div>

    <!-- Trạng thái Thanh toán & Giao hàng -->
    <div class="admin-table-wrap" style="padding: 20px;">
        <h3 style="color:var(--gold); margin-top:0; border-bottom:1px solid var(--border); padding-bottom:10px">Trạng thái</h3>
        
        <p style="margin-bottom: 8px;"><strong>Phương thức:</strong> <?= strtoupper($order['payment_method']) ?></p>
        <p style="margin-bottom: 8px;">
            <strong>Thanh toán:</strong> 
            <span style="color: <?= $paymentStatusColors[$order['payment_status']] ?>; font-weight: bold;">
                <?= $paymentStatusLabels[$order['payment_status']] ?? $order['payment_status'] ?>
            </span>
        </p>
        <?php if ($order['transaction_id']): ?>
            <p style="margin-bottom: 8px;"><strong>Mã giao dịch (VNPAY):</strong> <?= htmlspecialchars($order['transaction_id']) ?></p>
        <?php endif; ?>
        <?php if ($order['payment_method'] === 'cod' && $order['payment_status'] !== 'paid'): ?>
            <button class="btn-gold" id="btnMarkPaid" data-id="<?= $order['id'] ?>" style="margin-top:8px; padding:6px 12px; font-size:12px; background:#28a745; border-color:#28a745; color:#fff">
                ✓ Đã nhận tiền
            </button>
        <?php endif; ?>

        <div style="margin-top:20px; border-top:1px solid var(--border); padding-top:10px">
            <p style="margin-bottom: 12px;">
                <strong>Giao hàng:</strong> 
                <span style="color: <?= $statusColors[$order['status']] ?>; font-weight: bold;">
                    <?= $statusLabels[$order['status']] ?? $order['status'] ?>
                </span>
            </p>
            
            <div>
                <p style="margin-bottom: 8px; color:var(--text-hint); font-size: 13px;">Cập nhật trạng thái:</p>
                <div style="display:flex; gap:8px; flex-wrap:wrap">
                    <button class="btn-gold status-btn" data-id="<?= $order['id'] ?>" data-status="confirmed" style="padding:6px 12px; font-size:12px">Xác nhận</button>
                    <button class="btn-gold status-btn" data-id="<?= $order['id'] ?>" data-status="shipping" style="padding:6px 12px; font-size:12px; background:#fd7e14; border-color:#fd7e14">Giao hàng</button>
                    <button class="btn-gold status-btn" data-id="<?= $order['id'] ?>" data-status="delivered" style="padding:6px 12px; font-size:12px; background:#28a745; border-color:#28a745; color:#fff">Đã giao</button>
                    <button class="btn-gold status-btn" data-id="<?= $order['id'] ?>" data-status="cancelled" style="padding:6px 12px; font-size:12px; background:transparent; border-color:#dc3545; color:#dc3545">Hủy</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Danh sách sản phẩm -->
<div class="admin-table-wrap">
    <h3 style="color:var(--gold); margin: 20px 20px 10px 20px;">Sản phẩm đã đặt</h3>
    <table class="admin-table" style="margin-bottom: 0;">
        <thead>
            <tr>
                <th>Ảnh</th>
                <th>Sản phẩm</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($order['items'] as $item): ?>
                <tr>
                    <td>
                        <?php if(!empty($item['thumbnail_path'])): ?>
                            <img src="<?= BASE_URL . '/' . $item['thumbnail_path'] ?>" width="50" height="50" style="object-fit:cover; border-radius:4px; border:1px solid var(--border)">
                        <?php else: ?>
                            <div style="width:50px;height:50px;background:#333;border-radius:4px; border:1px solid var(--border)"></div>
                        <?php endif; ?>
                    </td>
                    <td style="font-weight:600; color:var(--text-primary)"><?= htmlspecialchars($item['product_name']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= number_format((float)$item['price_at_order'], 0, ',', '.') ?>đ</td>
                    <td style="font-weight:bold; color:var(--gold)"><?= number_format((float)($item['price_at_order'] * $item['quantity']), 0, ',', '.') ?>đ</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align:right; padding:12px 16px;">Tạm tính:</td>
                <td style="font-weight:bold; padding:12px 16px;"><?= number_format((float)$order['subtotal'], 0, ',', '.') ?>đ</td>
            </tr>
            <tr>
                <td colspan="4" style="text-align:right; padding:12px 16px;">Phí vận chuyển:</td>
                <td style="font-weight:bold; padding:12px 16px;"><?= number_format((float)$order['shipping_fee'], 0, ',', '.') ?>đ</td>
            </tr>
            <tr>
                <td colspan="4" style="text-align:right; color:var(--gold); font-size:16px; font-weight:bold; padding:16px;">Tổng cộng:</td>
                <td style="font-weight:bold; color:var(--gold); font-size:16px; padding:16px;"><?= number_format((float)$order['total'], 0, ',', '.') ?>đ</td>
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
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
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
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
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
