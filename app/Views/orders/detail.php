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
$methodLabels  = ['cod' => 'Thanh toán khi nhận hàng (COD)', 'vnpay' => 'VNPAY — Chuyển khoản'];
?>
<div class="container" style="padding: 40px 24px; min-height: 70vh;">

    <div class="breadcrumb" style="margin-bottom: 24px;">
        <a href="<?= BASE_URL ?>/">Trang chủ</a>
        <span>/</span>
        <a href="<?= BASE_URL ?>/user/profile?tab=orders">Lịch sử đơn hàng</a>
        <span>/</span>
        Đơn hàng #<?= $order['id'] ?>
    </div>

    <!-- Thông báo lỗi hoặc thành công -->
    <?php if (!empty($_SESSION['defect_success'])): ?>
      <div style="background:rgba(58,158,106,.14);border:1px solid rgba(58,158,106,.35);color:#5cba88;padding:12px 16px;border-radius:6px;font-family:var(--font-mono);font-size:14px;margin-bottom:20px;">
        ✓ <?= htmlspecialchars($_SESSION['defect_success']) ?>
      </div>
      <?php unset($_SESSION['defect_success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['defect_errors'])): ?>
      <div style="background:rgba(200,64,64,.12);border:1px solid rgba(200,64,64,.3);color:#e07070;padding:12px 16px;border-radius:6px;font-family:var(--font-mono);font-size:14px;margin-bottom:20px;">
        <?php foreach ($_SESSION['defect_errors'] as $err): ?>
          <div>✕ <?= htmlspecialchars($err) ?></div>
        <?php endforeach;
        unset($_SESSION['defect_errors']); ?>
      </div>
    <?php endif; ?>

    <div class="order-detail-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; padding-bottom: 16px; border-bottom: 1px solid var(--border);">
        <div>
            <h1 style="font-family: var(--font-display); font-size:34px; letter-spacing: .08em; color: var(--gold); margin-bottom: 4px;">
                ĐƠN HÀNG #<?= $order['id'] ?>
            </h1>
            <div style="font-family: var(--font-mono); font-size:13px; color: var(--text-hint);">
                Đặt lúc: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
            </div>
        </div>
        <span class="badge <?= $statusBadge[$order['status']] ?? 'badge-pending' ?>" style="font-size:14px; padding: 6px 14px;">
            <?= $statusLabels[$order['status']] ?? $order['status'] ?>
        </span>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 24px;">

        <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 8px; padding: 24px;">
            <h3 style="font-family: var(--font-mono); font-size:13px; color: var(--gold); letter-spacing: .14em; text-transform: uppercase; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                // THÔNG TIN NHẬN HÀNG
            </h3>
            <div style="display: flex; flex-direction: column; gap: 10px; font-size:16px;">
                <div style="display: flex; gap: 12px;">
                    <span style="font-family: var(--font-mono); font-size:13px; color: var(--text-hint); min-width: 100px; padding-top: 2px;">Người nhận</span>
                    <span style="color: var(--text-primary); font-weight: 500;"><?= htmlspecialchars($order['full_name']) ?></span>
                </div>
                <div style="display: flex; gap: 12px;">
                    <span style="font-family: var(--font-mono); font-size:13px; color: var(--text-hint); min-width: 100px; padding-top: 2px;">Số điện thoại</span>
                    <span style="color: var(--text-primary);"><?= htmlspecialchars($order['phone']) ?></span>
                </div>
                <div style="display: flex; gap: 12px;">
                    <span style="font-family: var(--font-mono); font-size:13px; color: var(--text-hint); min-width: 100px; padding-top: 2px;">Tỉnh / TP</span>
                    <span style="color: var(--text-primary);"><?= htmlspecialchars($order['province']) ?></span>
                </div>
                <div style="display: flex; gap: 12px;">
                    <span style="font-family: var(--font-mono); font-size:13px; color: var(--text-hint); min-width: 100px; padding-top: 2px;">Địa chỉ</span>
                    <span style="color: var(--text-primary);"><?= htmlspecialchars($order['address']) ?></span>
                </div>
                <?php if (!empty($order['note'])): ?>
                    <div style="display: flex; gap: 12px;">
                        <span style="font-family: var(--font-mono); font-size:13px; color: var(--text-hint); min-width: 100px; padding-top: 2px;">Ghi chú</span>
                        <span style="color: var(--text-secondary); font-style: italic;"><?= htmlspecialchars($order['note']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 8px; padding: 24px;">
            <h3 style="font-family: var(--font-mono); font-size:13px; color: var(--gold); letter-spacing: .14em; text-transform: uppercase; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                // THANH TOÁN & GIAO HÀNG
            </h3>
            <div style="display: flex; flex-direction: column; gap: 10px; font-size:16px;">
                <div style="display: flex; gap: 12px;">
                    <span style="font-family: var(--font-mono); font-size:13px; color: var(--text-hint); min-width: 110px; padding-top: 2px;">Phương thức</span>
                    <span style="color: var(--text-primary);"><?= $methodLabels[$order['payment_method']] ?? strtoupper($order['payment_method']) ?></span>
                </div>
                <div style="display: flex; gap: 12px; align-items: center;">
                    <span style="font-family: var(--font-mono); font-size:13px; color: var(--text-hint); min-width: 110px;">Trạng thái TT</span>
                    <span style="color: <?= $paymentColors[$order['payment_status']] ?? 'var(--text-primary)' ?>; font-weight: 600;">
                        <?= $paymentLabels[$order['payment_status']] ?? $order['payment_status'] ?>
                    </span>
                </div>
                <?php if (!empty($order['transaction_id'])): ?>
                    <div style="display: flex; gap: 12px;">
                        <span style="font-family: var(--font-mono); font-size:13px; color: var(--text-hint); min-width: 110px; padding-top: 2px;">Mã GD VNPAY</span>
                        <span style="font-family: var(--font-mono); font-size:14px; color: var(--text-secondary);"><?= htmlspecialchars($order['transaction_id']) ?></span>
                    </div>
                <?php endif; ?>
                <div style="margin-top: 14px; padding-top: 14px; border-top: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size:15px; color: var(--text-secondary);">
                        <span>Tạm tính</span>
                        <span><?= number_format((float)$order['subtotal'], 0, ',', '.') ?>đ</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 14px; font-size:15px; color: var(--text-secondary);">
                        <span>Phí vận chuyển</span>
                        <span><?= number_format((float)$order['shipping_fee'], 0, ',', '.') ?>đ</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: baseline; padding-top: 10px; border-top: 1px solid var(--border);">
                        <span style="font-family: var(--font-mono); font-size:13px; color: var(--text-hint); letter-spacing: .1em; text-transform: uppercase;">Tổng cộng</span>
                        <span style="font-family: var(--font-display); font-size:30px; color: var(--gold); letter-spacing: .04em;">
                            <?= number_format((float)$order['total'], 0, ',', '.') ?>đ
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 8px; overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border);">
            <h3 style="font-family: var(--font-mono); font-size:13px; color: var(--gold); letter-spacing: .14em; text-transform: uppercase;">
                // SẢN PHẨM ĐÃ ĐẶT (<?= count($order['items']) ?> sản phẩm)
            </h3>
        </div>
        <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;">
        <table style="width: 100%; border-collapse: collapse; min-width: 540px;">
            <thead>
                <tr style="background: var(--bg-surface);">
                    <th style="padding: 12px 20px; text-align: left; font-family: var(--font-mono); font-size:12px; color: var(--text-hint); letter-spacing: .1em; text-transform: uppercase; font-weight: normal;">Sản phẩm</th>
                    <th style="padding: 12px 20px; text-align: right; font-family: var(--font-mono); font-size:12px; color: var(--text-hint); letter-spacing: .1em; text-transform: uppercase; font-weight: normal;">Đơn giá</th>
                    <th style="padding: 12px 20px; text-align: center; font-family: var(--font-mono); font-size:12px; color: var(--text-hint); letter-spacing: .1em; text-transform: uppercase; font-weight: normal;">SL</th>
                    <th style="padding: 12px 20px; text-align: right; font-family: var(--font-mono); font-size:12px; color: var(--text-hint); letter-spacing: .1em; text-transform: uppercase; font-weight: normal;">Thành tiền</th>
                    <?php if ($order['status'] === 'delivered'): ?>
                        <th style="padding: 12px 20px; text-align: center; font-family: var(--font-mono); font-size:12px; color: var(--text-hint); letter-spacing: .1em; text-transform: uppercase; font-weight: normal; width: 220px;">Thao tác</th>
                    <?php endif; ?>
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
                                    <div style="width: 52px; height: 52px; background: var(--bg-surface); border: 1px solid var(--border); border-radius: 4px; display: flex; align-items: center; justify-content: center; font-family: var(--font-display); font-size:16px; color: var(--gold-dim); flex-shrink: 0;">KIT</div>
                                <?php endif; ?>
                                <div>
                                    <div style="font-weight: 500; color: var(--text-primary); margin-bottom: 2px;"><?= htmlspecialchars($item['product_name']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 14px 20px; text-align: right; font-family: var(--font-mono); font-size:15px; color: var(--text-secondary);">
                            <?= number_format((float)$item['price_at_order'], 0, ',', '.') ?>đ
                        </td>
                        <td style="padding: 14px 20px; text-align: center; font-family: var(--font-mono); font-size:15px; color: var(--text-secondary);">
                            ×<?= $item['quantity'] ?>
                        </td>
                        <td style="padding: 14px 20px; text-align: right; font-family: var(--font-mono); font-size:16px; font-weight: 600; color: var(--gold);">
                            <?= number_format((float)$item['price_at_order'] * $item['quantity'], 0, ',', '.') ?>đ
                        </td>
                        <?php if ($order['status'] === 'delivered'): ?>
                            <td style="padding: 14px 20px; text-align: center;">
                                <?php
                                $productId = (int)$item['product_id'];
                                if (isset($reportsByProduct[$productId])):
                                    $rep = $reportsByProduct[$productId];
                                    if ($rep['status'] === 'pending'): ?>
                                        <span class="badge badge-pending" style="font-size:11px; padding: 4px 8px;">Chờ duyệt</span>
                                    <?php elseif ($rep['status'] === 'approved'): ?>
                                        <span class="badge badge-delivered" style="font-size:11px; padding: 4px 8px; background: rgba(58,158,106,0.15); color: #5cba88; border: 1px solid rgba(58,158,106,0.35);">Đã duyệt (Gửi bù)</span>
                                    <?php else: ?>
                                        <span class="badge badge-cancelled" style="font-size:11px; padding: 4px 8px;">Bị từ chối</span>
                                    <?php endif; ?>
                                    <a href="#defect-report-<?= $rep['id'] ?>" style="display: block; font-size:13px; color: var(--gold); margin-top: 4px; text-decoration: underline;">Xem báo cáo</a>
                                <?php else: ?>
                                    <button class="btn-ghost" style="padding: 6px 12px; font-size:13px; cursor: pointer;" onclick="openDefectReportModal(<?= $productId ?>, '<?= htmlspecialchars($item['product_name'], ENT_QUOTES) ?>')">
                                        Báo thiếu đồ / lỗi
                                    </button>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>

    <!-- LỊCH SỬ BÁO CÁO THIẾU ĐỒ / LỖI ĐƠN HÀNG -->
    <?php
    $orderReports = [];
    if (!empty($reportsByProduct)) {
        $orderReports = array_filter($reportsByProduct);
    }
    if (!empty($orderReports)): ?>
        <div style="margin-top: 40px; margin-bottom: 24px;" id="order-defect-reports">
            <h3 style="font-family: var(--font-mono); font-size:13px; color: var(--gold); letter-spacing: .15em; text-transform: uppercase; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                // LỊCH SỬ BÁO CÁO THIẾU ĐỒ / LỖI ĐƠN HÀNG
            </h3>
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <?php foreach ($orderReports as $rep): ?>
                    <div id="defect-report-<?= $rep['id'] ?>" style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 8px; padding: 24px;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                            <div>
                                <span style="font-weight: 600; color: var(--text-primary); font-size:17px;">
                                    <?= htmlspecialchars($rep['product_name'] ?? 'Sản phẩm') ?>
                                </span>
                                <div style="font-family: var(--font-mono); font-size:11px; color: var(--text-hint); margin-top: 4px;">
                                    Gửi ngày: <?= date('d/m/Y H:i', strtotime($rep['created_at'])) ?>
                                </div>
                            </div>
                            <div>
                                <?php if ($rep['status'] === 'pending'): ?>
                                    <span class="badge badge-pending">ĐANG CHỜ DUYỆT</span>
                                <?php elseif ($rep['status'] === 'approved'): ?>
                                    <span class="badge badge-delivered" style="background: rgba(58,158,106,0.15); color: #5cba88; border: 1px solid rgba(58,158,106,0.4); padding: 4px 10px; border-radius: 4px; font-size:12px; font-family: var(--font-mono);">XÁC NHẬN GỬI HÀNG THAY THẾ</span>
                                <?php else: ?>
                                    <span class="badge badge-cancelled" style="background: rgba(200,64,64,0.15); color: #e07070; border: 1px solid rgba(200,64,64,0.4); padding: 4px 10px; border-radius: 4px; font-size:12px; font-family: var(--font-mono);">BỊ TỪ CHỐI</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <p style="color: var(--text-secondary); font-size:15px; line-height: 1.6; margin: 0 0 16px 0;">
                            <strong>Mô tả chi tiết:</strong> <?= nl2br(htmlspecialchars($rep['description'])) ?>
                        </p>
                        <div class="defect-proof-row" style="display: flex; gap: 20px; margin-bottom: 16px; flex-wrap: wrap;">
                            <div>
                                <div style="font-size:11px; color: var(--text-hint); font-family: var(--font-mono); margin-bottom: 6px;">ẢNH MINH CHỨNG</div>
                                <a href="<?= BASE_URL . '/' . htmlspecialchars($rep['image_proof']) ?>" target="_blank">
                                    <img src="<?= BASE_URL . '/' . htmlspecialchars($rep['image_proof']) ?>" style="width: 100px; height: 100px; object-fit: cover; border: 1px solid var(--border); border-radius: 6px; transition: 0.2s;" onmouseover="this.style.borderColor='var(--gold)'" onmouseout="this.style.borderColor='var(--border)'">
                                </a>
                            </div>
                            <div>
                                <div style="font-size:11px; color: var(--text-hint); font-family: var(--font-mono); margin-bottom: 6px;">VIDEO MINH CHỨNG</div>
                                <a href="<?= BASE_URL . '/' . htmlspecialchars($rep['video_proof']) ?>" target="_blank" style="display: flex; align-items: center; justify-content: center; width: 100px; height: 100px; border: 1px solid var(--border); background: #111; color: var(--gold); border-radius: 6px; text-decoration: none; font-size:26px; transition: 0.2s;" onmouseover="this.style.borderColor='var(--gold)'" onmouseout="this.style.borderColor='var(--border)'">
                                    ▶
                                </a>
                            </div>
                        </div>
                        <?php if (!empty($rep['admin_comment'])): ?>
                            <div style="background: rgba(255,255,255,0.03); border-left: 3px solid var(--gold); padding: 12px 16px; border-radius: 0 6px 6px 0; margin-top: 14px;">
                                <div style="font-size:12px; color: var(--gold); font-family: var(--font-mono); margin-bottom: 6px;">PHẢN HỒI TỪ ADMIN:</div>
                                <div style="font-size:15px; color: var(--text-secondary); line-height: 1.5;"><?= nl2br(htmlspecialchars($rep['admin_comment'])) ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div style="margin-top: 24px; display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="<?= BASE_URL ?>/user/profile?tab=orders" class="btn-ghost" style="padding: 10px 24px;">
            &larr; Lịch sử đơn hàng
        </a>
        <a href="<?= BASE_URL ?>/" class="btn-primary" style="padding: 10px 24px;">
            Tiếp tục mua sắm
        </a>
    </div>

</div>

<!-- MODAL BÁO CÁO LỖI -->
<div id="defectReportModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">// BÁO CÁO THIẾU ĐỒ / LỖI SẢN PHẨM</h3>
            <button class="modal-close" onclick="closeDefectReportModal()">&times;</button>
        </div>
        <form method="POST" action="<?= BASE_URL ?>/products/reportdefect" enctype="multipart/form-data" class="modal-body" style="margin:0;">
            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
            <input type="hidden" name="product_id" id="modalProductId" value="">
            
            <div style="margin-bottom: 20px;">
                <label style="font-family: var(--font-mono); font-size:12px; color: var(--text-hint); letter-spacing: .12em; text-transform: uppercase; display: block; margin-bottom: 8px;">
                    Sản phẩm báo cáo
                </label>
                <input type="text" id="modalProductName" readonly style="width: 100%; padding: 10px 14px; background: rgba(255,255,255,0.05); border: 1px solid var(--border); border-radius: 6px; color: var(--text-secondary); font-size:16px; outline: none;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="font-family: var(--font-mono); font-size:12px; color: var(--text-hint); letter-spacing: .12em; text-transform: uppercase; display: block; margin-bottom: 8px;">
                    Mô tả chi tiết lỗi / thiếu đồ * <span style="color: var(--text-hint); font-size:11px;">(tối thiểu 15 ký tự)</span>
                </label>
                <textarea name="description" rows="4" required minlength="15" placeholder="Ví dụ: Khi mở hộp nhận hàng thấy thiếu vỉ nhựa A, hoặc chi tiết số 12 bị gãy..."
                  style="width: 100%; padding: 12px 16px; background: var(--bg-surface); border: 1px solid var(--border); border-radius: 6px; color: var(--text-primary); font-family: var(--font-body); font-size:16px; line-height: 1.6; outline: none; resize: vertical;"></textarea>
            </div>

            <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 24px;">
                <div style="flex: 1; min-width: 200px;">
                    <label style="font-family: var(--font-mono); font-size:12px; color: var(--text-hint); letter-spacing: .12em; text-transform: uppercase; display: block; margin-bottom: 8px;">
                        Hình ảnh minh chứng * <span style="color: var(--text-hint); font-size:11px;">(JPG, PNG, WEBP)</span>
                    </label>
                    <input type="file" name="image_proof" accept="image/*" required style="width: 100%; color: var(--text-secondary); font-size:15px; font-family: var(--font-mono);">
                </div>
                
                <div style="flex: 1; min-width: 200px;">
                    <label style="font-family: var(--font-mono); font-size:12px; color: var(--text-hint); letter-spacing: .12em; text-transform: uppercase; display: block; margin-bottom: 8px;">
                        Video minh chứng * <span style="color: var(--text-hint); font-size:11px;">(Không cắt ghép, dưới 50MB)</span>
                    </label>
                    <input type="file" name="video_proof" accept="video/*" required style="width: 100%; color: var(--text-secondary); font-size:15px; font-family: var(--font-mono);">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" class="btn-ghost" style="padding: 10px 20px; font-size:15px; cursor: pointer;" onclick="closeDefectReportModal()">Hủy</button>
                <button type="submit" class="btn-primary" style="padding: 10px 24px; font-size:15px; cursor: pointer;">Gửi báo cáo</button>
            </div>
        </form>
    </div>
</div>

<script>
function openDefectReportModal(productId, productName) {
    document.getElementById('modalProductId').value = productId;
    document.getElementById('modalProductName').value = productName;
    document.getElementById('defectReportModal').style.display = 'flex';
}

function closeDefectReportModal() {
    document.getElementById('defectReportModal').style.display = 'none';
}

// Đóng modal khi nhấn ra ngoài modal-content
window.onclick = function(event) {
    var modal = document.getElementById('defectReportModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>

<style>
    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 4px;
        font-family: var(--font-mono);
        font-size:12px;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .badge-pending {
        background: rgba(200, 138, 58, .15);
        color: var(--amber, #c88a3a);
        border: 1px solid rgba(200, 138, 58, .3);
    }

    .badge-confirmed {
        background: rgba(58, 122, 200, .15);
        color: #3a7ac8;
        border: 1px solid rgba(58, 122, 200, .3);
    }

    .badge-shipping {
        background: rgba(150, 80, 200, .15);
        color: #aa70e0;
        border: 1px solid rgba(150, 80, 200, .3);
    }

    .badge-delivered {
        background: rgba(58, 158, 106, .15);
        color: #3a9e6a;
        border: 1px solid rgba(58, 158, 106, .3);
    }

    .badge-cancelled {
        background: rgba(80, 80, 80, .15);
        color: var(--text-secondary);
        border: 1px solid var(--border);
    }

    /* Modal styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.75);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 20px;
    }

    .modal-content {
        background: var(--bg-card, #141618);
        border: 1px solid var(--border, #2a2d31);
        border-radius: 12px;
        width: 100%;
        max-width: 600px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5), 0 0 40px rgba(200, 168, 90, 0.05);
        animation: modalFadeIn 0.3s ease;
        overflow: hidden;
    }

    @keyframes modalFadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .modal-header {
        padding: 18px 24px;
        border-bottom: 1px solid var(--border, #2a2d31);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-title {
        font-family: var(--font-mono);
        font-size:15px;
        color: var(--gold);
        letter-spacing: .08em;
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        color: var(--text-hint);
        font-size:26px;
        cursor: pointer;
        transition: color 0.15s;
    }

    .modal-close:hover {
        color: var(--gold);
    }

    .modal-body {
        padding: 24px;
        text-align: left;
    }
</style>