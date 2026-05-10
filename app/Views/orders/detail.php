<?php
/**
 * app/Views/orders/detail.php — Chi tiết đơn hàng (Client)
 * @var array $order  { id, full_name, phone, province, address, note,
 *                      payment_method, payment_status, status,
 *                      subtotal, shipping_fee, total, created_at,
 *                      items[] { product_name, price_at_order, quantity, thumbnail_path } }
 */

$statusLabels = [
    'pending'   => 'Chờ xác nhận',
    'confirmed' => 'Đã xác nhận',
    'shipping'  => 'Đang giao hàng',
    'delivered' => 'Đã giao thành công',
    'cancelled' => 'Đã hủy',
];
$statusBadge = [
    'pending'   => 'badge-pending',
    'confirmed' => 'badge-confirmed',
    'shipping'  => 'badge-shipping',
    'delivered' => 'badge-delivered',
    'cancelled' => 'badge-cancelled',
];
$paymentLabels = [
    'unpaid' => 'Chưa thanh toán',
    'paid'   => 'Đã thanh toán',
    'failed' => 'Thanh toán thất bại',
];
$paymentColors = ['unpaid' => 'var(--amber)', 'paid' => '#28a745', 'failed' => 'var(--red-accent)'];
$methodLabels  = ['cod' => 'Thanh toán khi nhận hàng (COD)', 'vnpay' => 'VNPAY — Chuyển khoản'];
?>

<div class="container" style="padding: 40px 24px; min-height: 70vh;">

    <!-- Breadcrumb -->
    <div class="breadcrumb" style="margin-bottom: 24px;">
        <a href="<?= BASE_URL ?>/">Trang chủ</a>
        <span>/</span>
        <a href="<?= BASE_URL ?>/user/profile?tab=orders">Lịch sử đơn hàng</a>
        <span>/</span>
        Đơn hàng #<?= $order['id'] ?>
    </div>

    <!-- Page Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; padding-bottom: 16px; border-bottom: 1px solid var(--border);">
        <div>
            <h1 style="font-family: var(--font-display); font-size: 32px; letter-spacing: .08em; color: var(--gold); margin-bottom: 4px;">
                ĐƠN HÀNG #<?= $order['id'] ?>
            </h1>
            <div style="font-family: var(--font-mono); font-size: 11px; color: var(--text-hint);">
                Đặt lúc: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
            </div>
        </div>
        <span class="badge <?= $statusBadge[$order['status']] ?? 'badge-pending' ?>" style="font-size: 12px; padding: 6px 14px;">
            <?= $statusLabels[$order['status']] ?? $order['status'] ?>
        </span>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">

        <!-- Thông tin nhận hàng -->
        <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 8px; padding: 24px;">
            <h3 style="font-family: var(--font-mono); font-size: 11px; color: var(--gold); letter-spacing: .14em; text-transform: uppercase; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                // THÔNG TIN NHẬN HÀNG
            </h3>
            <div style="display: flex; flex-direction: column; gap: 10px; font-size: 14px;">
                <div style="display: flex; gap: 12px;">
                    <span style="font-family: var(--font-mono); font-size: 11px; color: var(--text-hint); min-width: 100px; padding-top: 2px;">Người nhận</span>
                    <span style="color: var(--text-primary); font-weight: 500;"><?= htmlspecialchars($order['full_name']) ?></span>
                </div>
                <div style="display: flex; gap: 12px;">
                    <span style="font-family: var(--font-mono); font-size: 11px; color: var(--text-hint); min-width: 100px; padding-top: 2px;">Số điện thoại</span>
                    <span style="color: var(--text-primary);"><?= htmlspecialchars($order['phone']) ?></span>
                </div>
                <div style="display: flex; gap: 12px;">
                    <span style="font-family: var(--font-mono); font-size: 11px; color: var(--text-hint); min-width: 100px; padding-top: 2px;">Tỉnh / TP</span>
                    <span style="color: var(--text-primary);"><?= htmlspecialchars($order['province']) ?></span>
                </div>
                <div style="display: flex; gap: 12px;">
                    <span style="font-family: var(--font-mono); font-size: 11px; color: var(--text-hint); min-width: 100px; padding-top: 2px;">Địa chỉ</span>
                    <span style="color: var(--text-primary);"><?= htmlspecialchars($order['address']) ?></span>
                </div>
                <?php if (!empty($order['note'])): ?>
                <div style="display: flex; gap: 12px;">
                    <span style="font-family: var(--font-mono); font-size: 11px; color: var(--text-hint); min-width: 100px; padding-top: 2px;">Ghi chú</span>
                    <span style="color: var(--text-secondary); font-style: italic;"><?= htmlspecialchars($order['note']) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Thông tin thanh toán -->
        <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 8px; padding: 24px;">
            <h3 style="font-family: var(--font-mono); font-size: 11px; color: var(--gold); letter-spacing: .14em; text-transform: uppercase; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                // THANH TOÁN & GIAO HÀNG
            </h3>
            <div style="display: flex; flex-direction: column; gap: 10px; font-size: 14px;">
                <div style="display: flex; gap: 12px;">
                    <span style="font-family: var(--font-mono); font-size: 11px; color: var(--text-hint); min-width: 110px; padding-top: 2px;">Phương thức</span>
                    <span style="color: var(--text-primary);"><?= $methodLabels[$order['payment_method']] ?? strtoupper($order['payment_method']) ?></span>
                </div>
                <div style="display: flex; gap: 12px; align-items: center;">
                    <span style="font-family: var(--font-mono); font-size: 11px; color: var(--text-hint); min-width: 110px;">Trạng thái TT</span>
                    <span style="color: <?= $paymentColors[$order['payment_status']] ?? 'var(--text-primary)' ?>; font-weight: 600;">
                        <?= $paymentLabels[$order['payment_status']] ?? $order['payment_status'] ?>
                    </span>
                </div>
                <?php if (!empty($order['transaction_id'])): ?>
                <div style="display: flex; gap: 12px;">
                    <span style="font-family: var(--font-mono); font-size: 11px; color: var(--text-hint); min-width: 110px; padding-top: 2px;">Mã GD VNPAY</span>
                    <span style="font-family: var(--font-mono); font-size: 12px; color: var(--text-secondary);"><?= htmlspecialchars($order['transaction_id']) ?></span>
                </div>
                <?php endif; ?>
                <div style="margin-top: 14px; padding-top: 14px; border-top: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 13px; color: var(--text-secondary);">
                        <span>Tạm tính</span>
                        <span><?= number_format((float)$order['subtotal'], 0, ',', '.') ?>đ</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 14px; font-size: 13px; color: var(--text-secondary);">
                        <span>Phí vận chuyển</span>
                        <span><?= number_format((float)$order['shipping_fee'], 0, ',', '.') ?>đ</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: baseline; padding-top: 10px; border-top: 1px solid var(--border);">
                        <span style="font-family: var(--font-mono); font-size: 11px; color: var(--text-hint); letter-spacing: .1em; text-transform: uppercase;">Tổng cộng</span>
                        <span style="font-family: var(--font-display); font-size: 28px; color: var(--gold); letter-spacing: .04em;">
                            <?= number_format((float)$order['total'], 0, ',', '.') ?>đ
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách sản phẩm -->
    <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 8px; overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border);">
            <h3 style="font-family: var(--font-mono); font-size: 11px; color: var(--gold); letter-spacing: .14em; text-transform: uppercase;">
                // SẢN PHẨM ĐÃ ĐẶT (<?= count($order['items']) ?> sản phẩm)
            </h3>
        </div>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: var(--bg-surface);">
                    <th style="padding: 12px 20px; text-align: left; font-family: var(--font-mono); font-size: 10px; color: var(--text-hint); letter-spacing: .1em; text-transform: uppercase; font-weight: normal;">Sản phẩm</th>
                    <th style="padding: 12px 20px; text-align: right; font-family: var(--font-mono); font-size: 10px; color: var(--text-hint); letter-spacing: .1em; text-transform: uppercase; font-weight: normal;">Đơn giá</th>
                    <th style="padding: 12px 20px; text-align: center; font-family: var(--font-mono); font-size: 10px; color: var(--text-hint); letter-spacing: .1em; text-transform: uppercase; font-weight: normal;">SL</th>
                    <th style="padding: 12px 20px; text-align: right; font-family: var(--font-mono); font-size: 10px; color: var(--text-hint); letter-spacing: .1em; text-transform: uppercase; font-weight: normal;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order['items'] as $item): ?>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 14px 20px;">
                        <div style="display: flex; align-items: center; gap: 14px;">
                            <?php if (!empty($item['thumbnail_path'])): ?>
                                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($item['thumbnail_path']) ?>"
                                     alt=""
                                     style="width: 52px; height: 52px; object-fit: cover; border-radius: 4px; border: 1px solid var(--border); flex-shrink: 0;">
                            <?php else: ?>
                                <div style="width: 52px; height: 52px; background: var(--bg-surface); border: 1px solid var(--border); border-radius: 4px; display: flex; align-items: center; justify-content: center; font-family: var(--font-display); font-size: 14px; color: var(--gold-dim); flex-shrink: 0;">KIT</div>
                            <?php endif; ?>
                            <div>
                                <div style="font-weight: 500; color: var(--text-primary); margin-bottom: 2px;"><?= htmlspecialchars($item['product_name']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 14px 20px; text-align: right; font-family: var(--font-mono); font-size: 13px; color: var(--text-secondary);">
                        <?= number_format((float)$item['price_at_order'], 0, ',', '.') ?>đ
                    </td>
                    <td style="padding: 14px 20px; text-align: center; font-family: var(--font-mono); font-size: 13px; color: var(--text-secondary);">
                        ×<?= $item['quantity'] ?>
                    </td>
                    <td style="padding: 14px 20px; text-align: right; font-family: var(--font-mono); font-size: 14px; font-weight: 600; color: var(--gold);">
                        <?= number_format((float)$item['price_at_order'] * $item['quantity'], 0, ',', '.') ?>đ
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Nút quay lại -->
    <div style="margin-top: 24px; display: flex; gap: 12px;">
        <a href="<?= BASE_URL ?>/user/profile?tab=orders" class="btn-ghost" style="padding: 10px 24px;">
            &larr; Lịch sử đơn hàng
        </a>
        <a href="<?= BASE_URL ?>/" class="btn-primary" style="padding: 10px 24px;">
            Tiếp tục mua sắm
        </a>
    </div>

</div>

<style>
/* Badge styles for order status — compatible with shop.css variables */
.badge { display: inline-block; padding: 4px 10px; border-radius: 4px; font-family: var(--font-mono); font-size: 10px; letter-spacing: .1em; text-transform: uppercase; }
.badge-pending   { background: rgba(200,138,58,.15); color: var(--amber, #c88a3a); border: 1px solid rgba(200,138,58,.3); }
.badge-confirmed { background: rgba(58,122,200,.15); color: #3a7ac8; border: 1px solid rgba(58,122,200,.3); }
.badge-shipping  { background: rgba(150,80,200,.15); color: #aa70e0; border: 1px solid rgba(150,80,200,.3); }
.badge-delivered { background: rgba(58,158,106,.15); color: #3a9e6a; border: 1px solid rgba(58,158,106,.3); }
.badge-cancelled { background: rgba(80,80,80,.15); color: var(--text-secondary); border: 1px solid var(--border); }
</style>
