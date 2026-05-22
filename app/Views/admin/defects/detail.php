<?php
/**
 * @var string $title
 * @var array $report
 */
?>
<div style="margin-bottom:20px;">
  <a href="<?= BASE_URL ?>/admin/defects" style="font-family:var(--font-mono); font-size:13px; color:var(--gold); text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
    &larr; QUAY LẠI DANH SÁCH BÁO CÁO LỖI
  </a>
</div>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
  <div>
    <div style="font-family:var(--font-m); font-size:12px; color:var(--text-3); letter-spacing:.15em; text-transform:uppercase; margin-bottom:4px;">
      CHI TIẾT PHÊ DUYỆT LỖI SẢN PHẨM
    </div>
    <h2 style="font-family:var(--font-d); font-size:30px; letter-spacing:.05em; margin:0;">
      BÁO CÁO LỖI #<?= $report['id'] ?>
    </h2>
  </div>
  <div>
    <?php if ($report['status'] === 'pending'): ?>
      <span class="badge badge-pending" style="font-size:13px; padding:6px 12px;">ĐÃ TIẾP NHẬN</span>
    <?php elseif ($report['status'] === 'checking'): ?>
      <span class="badge badge-pending" style="font-size:13px; padding:6px 12px; background:rgba(200,138,58,0.15); color:var(--amber);">ĐANG KIỂM TRA</span>
    <?php elseif ($report['status'] === 'approved'): ?>
      <span class="badge badge-delivered" style="font-size:13px; padding:6px 12px; background:rgba(58,158,106,0.15); color:#5cba88;">ĐÃ DUYỆT (CHUẨN BỊ GỬI)</span>
    <?php elseif ($report['status'] === 'shipped'): ?>
      <span class="badge badge-delivered" style="font-size:13px; padding:6px 12px; background:var(--green); color:#fff; border:1px solid var(--green);">ĐÃ GỬI PART</span>
    <?php else: ?>
      <span class="badge badge-cancelled" style="font-size:13px; padding:6px 12px;">BỊ TỪ CHỐI</span>
    <?php endif; ?>
  </div>
</div>

<div class="form-grid" style="display:grid; grid-template-columns:3fr 2fr; gap:24px; align-items:start;">
  <!-- Thông tin báo cáo -->
  <div style="background:var(--bg-card); border:1px solid var(--border); border-radius:8px; padding:24px;">
    <h3 style="font-family:var(--font-mono); font-size:14px; color:var(--gold); letter-spacing:.12em; text-transform:uppercase; margin-bottom:20px; border-bottom:1px solid var(--border); padding-bottom:8px;">
      // NỘI DUNG MINH CHỨNG TỪ KHÁCH HÀNG
    </h3>

    <div style="margin-bottom:20px;">
      <label style="font-size:12px; color:var(--text-3); font-family:var(--font-mono);">Mô tả chi tiết lỗi từ khách hàng</label>
      <div style="background:var(--bg-panel); border:1px solid var(--border); border-radius:6px; padding:16px; font-size:15px; line-height:1.6; color:var(--text-1); margin-top:6px; white-space:pre-wrap;">
        <?= htmlspecialchars($report['description']) ?>
      </div>
    </div>

    <!-- Video minh chứng -->
    <div style="margin-bottom:20px;">
      <label style="font-size:12px; color:var(--text-3); font-family:var(--font-mono); display:block; margin-bottom:8px;">
        Video minh chứng (Bắt buộc quay liền mạch, không cắt ghép)
      </label>
      <div style="background:#000; border:1px solid var(--border); border-radius:6px; overflow:hidden; display:flex; justify-content:center;">
        <video src="<?= BASE_URL . '/' . htmlspecialchars($report['video_proof']) ?>" controls style="max-width:100%; max-height:400px; display:block;"></video>
      </div>
    </div>

    <!-- Hình ảnh minh chứng -->
    <div>
      <label style="font-size:12px; color:var(--text-3); font-family:var(--font-mono); display:block; margin-bottom:8px;">
        Hình ảnh minh chứng lỗi
      </label>
      <div style="display:inline-block; border:1px solid var(--border); border-radius:6px; overflow:hidden; background:var(--bg-panel);">
        <a href="<?= BASE_URL . '/' . htmlspecialchars($report['image_proof']) ?>" target="_blank" title="Nhấp vào để xem ảnh gốc">
          <img src="<?= BASE_URL . '/' . htmlspecialchars($report['image_proof']) ?>" style="max-width:100%; max-height:300px; display:block; transition:opacity .15s;" onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
        </a>
      </div>
    </div>
  </div>

  <!-- Thông tin đặt hàng & Form duyệt -->
  <div style="display:flex; flex-direction:column; gap:24px;">
    <!-- Box thông tin đơn hàng -->
    <div style="background:var(--bg-card); border:1px solid var(--border); border-radius:8px; padding:24px;">
      <h3 style="font-family:var(--font-mono); font-size:14px; color:var(--gold); letter-spacing:.12em; text-transform:uppercase; margin-bottom:16px; border-bottom:1px solid var(--border); padding-bottom:8px;">
        // THÔNG TIN LIÊN QUAN
      </h3>
      
      <div style="display:flex; flex-direction:column; gap:12px; font-size:15px;">
        <div>
          <span style="color:var(--text-2);">Khách hàng:</span>
          <strong style="display:block; color:var(--text-1); margin-top:2px;">
            <?= htmlspecialchars($report['user_name'] ?? 'N/A') ?>
          </strong>
          <span style="font-size:13px; color:var(--text-3); font-family:var(--font-mono);"><?= htmlspecialchars($report['user_email'] ?? '') ?></span>
        </div>

        <div>
          <span style="color:var(--text-2);">Sản phẩm báo lỗi:</span>
          <strong style="display:block; color:var(--text-1); margin-top:2px;">
            <?= htmlspecialchars($report['product_name'] ?? 'N/A') ?>
          </strong>
        </div>

        <div>
          <span style="color:var(--text-2);">Đơn hàng:</span>
          <a href="<?= BASE_URL ?>/admin/orders/detail/<?= $report['order_id'] ?>" target="_blank" style="display:block; color:var(--gold); text-decoration:underline; font-family:var(--font-mono); font-weight:bold; margin-top:2px;">
            #<?= $report['order_id'] ?> (Xem chi tiết đơn &rarr;)
          </a>
        </div>

        <div>
          <span style="color:var(--text-2);">Ngày báo cáo:</span>
          <div style="color:var(--text-1); font-family:var(--font-mono); margin-top:2px;">
            <?= date('d/m/Y H:i:s', strtotime($report['created_at'])) ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Box Phê duyệt -->
    <div style="background:var(--bg-card); border:1px solid var(--border); border-radius:8px; padding:24px;">
      <h3 style="font-family:var(--font-mono); font-size:14px; color:var(--gold); letter-spacing:.12em; text-transform:uppercase; margin-bottom:16px; border-bottom:1px solid var(--border); padding-bottom:8px;">
        // XỬ LÝ PHÊ DUYỆT
      </h3>

      <form method="POST" action="<?= BASE_URL ?>/admin/confirmDefectReshipment">
        <input type="hidden" name="id" value="<?= $report['id'] ?>">

        <div style="margin-bottom:20px;">
          <label style="font-family:var(--font-mono); font-size:12px; color:var(--text-hint); letter-spacing:.12em; text-transform:uppercase; display:block; margin-bottom:8px;">
            Trạng thái xử lý
          </label>
          <select name="status" style="width:100%; padding:10px 12px; background:var(--bg-panel); border:1px solid var(--border); border-radius:6px; color:var(--text-1); font-size:15px; outline:none; font-family:var(--font-b);">
            <option value="pending" <?= $report['status'] === 'pending' ? 'selected' : '' ?>>Đã tiếp nhận (Chờ duyệt)</option>
            <option value="checking" <?= $report['status'] === 'checking' ? 'selected' : '' ?>>Đang kiểm tra</option>
            <option value="approved" <?= $report['status'] === 'approved' ? 'selected' : '' ?>>Đã duyệt (Chuẩn bị gửi part)</option>
            <option value="shipped" <?= $report['status'] === 'shipped' ? 'selected' : '' ?>>Đã gửi part thay thế</option>
            <option value="rejected" <?= $report['status'] === 'rejected' ? 'selected' : '' ?>>Từ chối báo cáo lỗi</option>
          </select>
        </div>

        <div style="margin-bottom:20px;">
          <label style="font-family:var(--font-mono); font-size:12px; color:var(--text-hint); letter-spacing:.12em; text-transform:uppercase; display:block; margin-bottom:8px;">
            Nhận xét / Phản hồi cho khách hàng
          </label>
          <textarea name="admin_comment" rows="4" placeholder="Ví dụ: Đã kiểm duyệt video, sản phẩm thay thế sẽ được đóng gói gửi đi trong ngày..."
            style="width:100%; padding:10px 12px; background:var(--bg-panel); border:1px solid var(--border); border-radius:6px; color:var(--text-1); font-size:15px; line-height:1.5; outline:none; resize:vertical;"><?= htmlspecialchars($report['admin_comment'] ?? '') ?></textarea>
        </div>

        <div>
          <button type="submit" class="btn btn-gold" style="width:100%; padding:12px; font-family:var(--font-mono); font-size:14px; font-weight:bold; cursor:pointer;">
            CẬP NHẬT TIẾN TRÌNH XỬ LÝ
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
