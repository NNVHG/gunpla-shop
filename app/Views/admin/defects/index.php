<?php
/**
 * @var string $title
 * @var array $items
 * @var int $total
 * @var int $pages
 * @var int $page
 */
?>
<div class="admin-header">
  <div>
    <div style="font-family:var(--font-m); font-size:12px; color:var(--text-3); letter-spacing:.15em; text-transform:uppercase; margin-bottom:4px;">
      HỆ THỐNG KIỂM TRA LỖI SẢN PHẨM
    </div>
    <h2 class="admin-title">
      DANH SÁCH BÁO CÁO LỖI
    </h2>
  </div>
</div>

<div class="admin-table-wrap">
  <div class="admin-table-head">
    <div class="admin-table-title">// TẤT CẢ BÁO CÁO LỖI (<?= $total ?> BẢN GHI)</div>
  </div>
  <div class="admin-table-scroll">
  <table>
    <thead>
      <tr>
        <th>Mã BC</th>
        <th>Khách hàng</th>
        <th>Sản phẩm</th>
        <th>Mã đơn</th>
        <th>Ngày gửi</th>
        <th>Trạng thái</th>
        <th style="text-align:right">Thao tác</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($items)): ?>
        <tr>
          <td colspan="7" style="text-align:center; padding:32px; color:var(--text-2); font-family:var(--font-m);">
            Không có báo cáo lỗi nào được tìm thấy.
          </td>
        </tr>
      <?php else: ?>
        <?php foreach ($items as $item): ?>
          <tr>
            <td style="font-family:var(--font-m); font-weight:bold; color:var(--gold);">#<?= $item['id'] ?></td>
            <td>
              <div style="font-weight:500;"><?= htmlspecialchars($item['user_name'] ?? 'N/A') ?></div>
              <div style="font-size:12px; color:var(--text-2); font-family:var(--font-m);"><?= htmlspecialchars($item['user_email'] ?? '') ?></div>
            </td>
            <td>
              <div style="max-width:240px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?= htmlspecialchars($item['product_name'] ?? 'N/A') ?>">
                <?= htmlspecialchars($item['product_name'] ?? 'N/A') ?>
              </div>
            </td>
            <td style="font-family:var(--font-m);">#<?= $item['order_id'] ?></td>
            <td><?= date('d/m/Y H:i', strtotime($item['created_at'])) ?></td>
            <td>
              <?php if ($item['status'] === 'pending'): ?>
                <span class="badge badge-pending">Chờ duyệt</span>
              <?php elseif ($item['status'] === 'approved'): ?>
                <span class="badge badge-delivered">Đã duyệt</span>
              <?php else: ?>
                <span class="badge badge-cancelled">Từ chối</span>
              <?php endif; ?>
            </td>
            <td style="text-align:right">
              <a href="<?= BASE_URL ?>/admin/defectDetail/<?= $item['id'] ?>" class="btn btn-sm btn-gold" style="text-decoration:none;">Xem chi tiết</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
  </div>

  <?php if ($pages > 1): ?>
    <div class="pagination">
      <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>" class="page-btn">&larr; TRƯỚC</a>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $pages; $i++): ?>
        <a href="?page=<?= $i ?>" class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
      <?php endfor; ?>

      <?php if ($page < $pages): ?>
        <a href="?page=<?= $page + 1 ?>" class="page-btn">SAU &rarr;</a>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>
