<?php
/** Trang cảm ơn sau khi đặt hàng. Biến: $order */
$statusLabels=['pending'=>'Chờ xác nhận','confirmed'=>'Đã xác nhận','shipping'=>'Đang giao','delivered'=>'Đã giao','cancelled'=>'Đã hủy'];
?>
<div class="success-wrap">
  <div class="success-icon">✅</div>
  <h1 class="success-title">ĐẶT HÀNG<br>THÀNH CÔNG</h1>
  <p class="success-sub">Mã đơn: #<?=$order['id']?> — Chúng tôi sẽ xác nhận sớm nhất</p>

  <div class="order-summary-box">
    <div style="font-family:var(--font-mono);font-size:10px;color:var(--gold);letter-spacing:.15em;text-transform:uppercase;margin-bottom:16px">// Thông tin đơn hàng</div>
    <table style="width:100%;font-size:13px">
      <tr><td style="color:var(--text-hint);padding:4px 0;width:120px">Người nhận</td><td><?=htmlspecialchars($order['full_name'])?></td></tr>
      <tr><td style="color:var(--text-hint);padding:4px 0">Điện thoại</td><td><?=htmlspecialchars($order['phone'])?></td></tr>
      <tr><td style="color:var(--text-hint);padding:4px 0">Địa chỉ</td><td><?=htmlspecialchars($order['address'])?>, <?=htmlspecialchars($order['province'])?></td></tr>
      <tr><td style="color:var(--text-hint);padding:4px 0">Trạng thái</td><td><span style="color:var(--gold)"><?=$statusLabels[$order['status']]??$order['status']?></span></td></tr>
    </table>
    <div style="border-top:1px solid var(--border);margin-top:14px;padding-top:14px">
      <?php foreach($order['items']??[] as $item): ?>
        <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:6px">
          <span style="color:var(--text-secondary)"><?=htmlspecialchars($item['product_name'])?> × <?=$item['quantity']?></span>
          <span style="font-family:var(--font-mono)"><?=number_format($item['price_at_order']*$item['quantity'],0,',','.')?>đ</span>
        </div>
      <?php endforeach; ?>
      <div style="display:flex;justify-content:space-between;padding-top:10px;border-top:1px solid var(--border);margin-top:6px">
        <span style="font-family:var(--font-mono);font-size:11px;color:var(--text-secondary)">Phí ship: <?=number_format($order['shipping_fee'],0,',','.')?>đ</span>
        <span style="font-family:var(--font-display);font-size:22px;color:var(--gold)"><?=number_format($order['total'],0,',','.')?>đ</span>
      </div>
    </div>
  </div>
  <a href="/" class="btn-hero" style="display:inline-block">VỀ TRANG CHỦ</a>
</div>
