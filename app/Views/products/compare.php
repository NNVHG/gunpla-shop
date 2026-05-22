<?php
/**
 * View so sánh sản phẩm Gunpla
 * @var array $products
 */

function compareStockBadge(int $s): string
{
    if ($s === 0) return '<span class="stock-badge out-stock" style="background:#e74c3c;color:#fff;padding:4px 8px;font-size:12px;border-radius:3px;font-weight:bold">HẾT HÀNG</span>';
    if ($s <= 5)  return "<span class='stock-badge low-stock' style='background:#f39c12;color:#fff;padding:4px 8px;font-size:12px;border-radius:3px;font-weight:bold'>CÒN ÍT ($s)</span>";
    return '<span class="stock-badge in-stock" style="background:#2ecc71;color:#fff;padding:4px 8px;font-size:12px;border-radius:3px;font-weight:bold">CÒN HÀNG</span>';
}

function getCompareImg($product) {
    if (!empty($product['images'])) {
        foreach ($product['images'] as $img) {
            if (!empty($img['is_primary'])) {
                $path = $img['image_path'];
                if (strpos($path, '/public/') === 0) {
                    $path = substr($path, 8);
                }
                return BASE_URL . '/' . ltrim($path, '/');
            }
        }
        $path = $product['images'][0]['image_path'];
        if (strpos($path, '/public/') === 0) {
            $path = substr($path, 8);
        }
        return BASE_URL . '/' . ltrim($path, '/');
    }
    return BASE_URL . '/public/uploads/img-gundam/default.png';
}
?>

<div class="container" style="padding-top:32px;padding-bottom:64px">
  <div class="breadcrumb">
    <a href="<?= BASE_URL ?>/">Trang chủ</a><span>/</span>
    <a href="<?= BASE_URL ?>/products">Sản phẩm</a><span>/</span>
    So sánh
  </div>

  <div style="margin-top:28px">
    <h1 style="font-family:var(--font-display);font-size:34px;color:var(--gold);letter-spacing:.05em;margin-bottom:10px">// BẢNG SO SÁNH SẢN PHẨM</h1>
    <p style="color:var(--text-secondary);font-size:16px;margin-bottom:30px">So sánh chi tiết thông số kỹ thuật, giá cả, độ khó của các mô hình Gunpla bạn quan tâm (tối đa 3 sản phẩm).</p>

    <?php if (empty($products)): ?>
      <div style="text-align:center;padding:60px 20px;background:var(--bg-card);border:1px solid var(--border);border-radius:8px">
        <div style="font-size:50px;margin-bottom:16px">🔍</div>
        <h3 style="color:var(--text-primary);font-size:20px;margin-bottom:12px">Chưa chọn sản phẩm nào để so sánh</h3>
        <p style="color:var(--text-secondary);font-size:16px;margin-bottom:20px">Hãy quay lại danh sách sản phẩm và tick chọn các sản phẩm cần so sánh.</p>
        <a href="<?= BASE_URL ?>/products" class="btn btn-gold" style="padding:10px 24px">QUAY LẠI CỬA HÀNG</a>
      </div>
    <?php else: ?>
      <div class="compare-table-wrapper" style="overflow-x:auto;background:var(--bg-card);border:1px solid var(--border);border-radius:8px">
        <table class="compare-table" style="width:100%;border-collapse:collapse;text-align:left;font-size:16px;min-width:700px">
          <thead>
            <tr style="border-bottom:1px solid var(--border)">
              <th style="width:20%;padding:20px;color:var(--gold);font-family:var(--font-mono);font-size:14px;text-transform:uppercase;letter-spacing:.1em;background:rgba(0,0,0,0.1)">Thông số</th>
              <?php foreach ($products as $p): ?>
                <th style="width:<?= 80 / count($products) ?>%;padding:20px;vertical-align:top;border-left:1px solid var(--border);position:relative">
                  <button class="remove-compare-btn" data-id="<?= $p['id'] ?>" style="position:absolute;top:10px;right:10px;background:none;border:none;color:var(--text-hint);cursor:pointer;font-size:18px" title="Xóa khỏi danh sách so sánh">✕</button>
                  <div style="text-align:center;margin-bottom:15px">
                    <img src="<?= getCompareImg($p) ?>" alt="<?= htmlspecialchars($p['name']) ?>" style="width:120px;height:120px;object-fit:contain;border-radius:4px;border:1px solid var(--border);background:#fff;padding:5px">
                  </div>
                  <div style="text-align:center">
                    <a href="<?= BASE_URL ?>/products/detail/<?= $p['id'] ?>" style="color:var(--text-primary);font-weight:bold;text-decoration:none;font-size:17px;display:block;margin-bottom:8px;line-height:1.4" class="hover-gold"><?= htmlspecialchars($p['name']) ?></a>
                    <div style="color:var(--gold);font-family:var(--font-display);font-size:20px;font-weight:bold;letter-spacing:.05em"><?= number_format((int)$p['price'], 0, ',', '.') ?>đ</div>
                  </div>
                </th>
              <?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <tr style="border-bottom:1px solid var(--border)">
              <td style="padding:15px 20px;font-weight:bold;color:var(--text-secondary);background:rgba(0,0,0,0.05)">Dòng (Grade)</td>
              <?php foreach ($products as $p): ?>
                <td style="padding:15px 20px;border-left:1px solid var(--border);color:var(--text-primary);font-weight:500"><?= htmlspecialchars($p['grade'] ?: 'N/A') ?></td>
              <?php endforeach; ?>
            </tr>
            <tr style="border-bottom:1px solid var(--border)">
              <td style="padding:15px 20px;font-weight:bold;color:var(--text-secondary);background:rgba(0,0,0,0.05)">Tỷ lệ (Scale)</td>
              <?php foreach ($products as $p): ?>
                <td style="padding:15px 20px;border-left:1px solid var(--border);color:var(--text-primary)"><?= htmlspecialchars($p['scale'] ?: 'N/A') ?></td>
              <?php endforeach; ?>
            </tr>
            <tr style="border-bottom:1px solid var(--border)">
              <td style="padding:15px 20px;font-weight:bold;color:var(--text-secondary);background:rgba(0,0,0,0.05)">Vũ trụ (Series)</td>
              <?php foreach ($products as $p): ?>
                <td style="padding:15px 20px;border-left:1px solid var(--border);color:var(--text-primary)"><?= htmlspecialchars($p['series'] ?: 'N/A') ?></td>
              <?php endforeach; ?>
            </tr>
            <tr style="border-bottom:1px solid var(--border)">
              <td style="padding:15px 20px;font-weight:bold;color:var(--text-secondary);background:rgba(0,0,0,0.05)">Số lượng Part (mảnh ghép)</td>
              <?php foreach ($products as $p): ?>
                <td style="padding:15px 20px;border-left:1px solid var(--border);color:var(--text-primary);font-family:var(--font-mono)">
                  <?= $p['parts_count'] !== null ? htmlspecialchars((string)$p['parts_count']) . ' pcs' : 'N/A' ?>
                </td>
              <?php endforeach; ?>
            </tr>
            <tr style="border-bottom:1px solid var(--border)">
              <td style="padding:15px 20px;font-weight:bold;color:var(--text-secondary);background:rgba(0,0,0,0.05)">Độ khó lắp ráp</td>
              <?php foreach ($products as $p): ?>
                <td style="padding:15px 20px;border-left:1px solid var(--border)">
                  <?php if (!empty($p['difficulty'])): ?>
                    <?php 
                      $diffColor = 'var(--text-secondary)';
                      if ($p['difficulty'] === 'Dễ') $diffColor = '#2ecc71';
                      elseif ($p['difficulty'] === 'Trung bình') $diffColor = '#3498db';
                      elseif ($p['difficulty'] === 'Khó') $diffColor = '#e67e22';
                      elseif ($p['difficulty'] === 'Rất khó') $diffColor = '#e74c3c';
                    ?>
                    <span style="color:<?= $diffColor ?>;font-weight:bold"><?= htmlspecialchars($p['difficulty']) ?></span>
                  <?php else: ?>
                    <span style="color:var(--text-hint)">N/A</span>
                  <?php endif; ?>
                </td>
              <?php endforeach; ?>
            </tr>
            <tr style="border-bottom:1px solid var(--border)">
              <td style="padding:15px 20px;font-weight:bold;color:var(--text-secondary);background:rgba(0,0,0,0.05)">Trọng lượng</td>
              <?php foreach ($products as $p): ?>
                <td style="padding:15px 20px;border-left:1px solid var(--border);color:var(--text-primary)">
                  <?= $p['weight_gram'] ? number_format((int)$p['weight_gram']) . ' gram' : 'N/A' ?>
                </td>
              <?php endforeach; ?>
            </tr>
            <tr style="border-bottom:1px solid var(--border)">
              <td style="padding:15px 20px;font-weight:bold;color:var(--text-secondary);background:rgba(0,0,0,0.05)">Đánh giá khách hàng</td>
              <?php foreach ($products as $p): ?>
                <td style="padding:15px 20px;border-left:1px solid var(--border)">
                  <?php if ($p['total_reviews'] > 0): ?>
                    <div style="color:#f1c40f;margin-bottom:4px">
                      <?php 
                        $stars = (int)round((float)$p['avg_rating']);
                        for ($i = 1; $i <= 5; $i++) {
                          echo $i <= $stars ? '★' : '☆';
                        }
                      ?>
                      <span style="color:var(--text-secondary);font-size:14px;margin-left:4px">(<?= number_format((float)$p['avg_rating'], 1) ?>)</span>
                    </div>
                    <div style="font-size:13px;color:var(--text-hint)"><?= $p['total_reviews'] ?> lượt đánh giá</div>
                  <?php else: ?>
                    <span style="color:var(--text-hint);font-size:14px">Chưa có đánh giá</span>
                  <?php endif; ?>
                </td>
              <?php endforeach; ?>
            </tr>
            <tr style="border-bottom:1px solid var(--border)">
              <td style="padding:15px 20px;font-weight:bold;color:var(--text-secondary);background:rgba(0,0,0,0.05)">Tình trạng kho</td>
              <?php foreach ($products as $p): ?>
                <td style="padding:15px 20px;border-left:1px solid var(--border)">
                  <?= compareStockBadge((int)$p['stock']) ?>
                </td>
              <?php endforeach; ?>
            </tr>
            <tr>
              <td style="padding:20px;background:rgba(0,0,0,0.05)"></td>
              <?php foreach ($products as $p): ?>
                <td style="padding:20px;border-left:1px solid var(--border);text-align:center">
                  <?php if ((int)$p['stock'] > 0): ?>
                    <button class="btn btn-gold btn-add-cart-quick" data-id="<?= $p['id'] ?>" data-name="<?= htmlspecialchars($p['name']) ?>" data-price="<?= $p['price'] ?>" data-image="<?= getCompareImg($p) ?>" style="width:100%;font-size:14px;padding:8px 12px">
                      THÊM VÀO GIỎ
                    </button>
                  <?php else: ?>
                    <button class="btn" style="width:100%;font-size:14px;padding:8px 12px;background:#333;color:#777;cursor:not-allowed;border:1px solid #444" disabled>
                      HẾT HÀNG
                    </button>
                  <?php endif; ?>
                </td>
              <?php endforeach; ?>
            </tr>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Đăng ký sự kiện click nút xóa sản phẩm so sánh
    document.querySelectorAll('.remove-compare-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            // Đọc local storage
            let compareIds = JSON.parse(localStorage.getItem('compare_products') || '[]');
            compareIds = compareIds.filter(item => item !== id && item !== parseInt(id));
            localStorage.setItem('compare_products', JSON.stringify(compareIds));
            
            // Reload trang mới không có sản phẩm vừa xóa
            if (compareIds.length > 0) {
                window.location.href = window.BASE_URL + '/products/compare?ids=' + compareIds.join(',');
            } else {
                window.location.href = window.BASE_URL + '/products/compare';
            }
        });
    });

    // Thêm vào giỏ nhanh
    document.querySelectorAll('.btn-add-cart-quick').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            
            if (typeof window.addToCart === 'function') {
                window.addToCart(id, 1);
            } else {
                // Dự phòng nếu script chưa load kịp
                alert('Đang tải giỏ hàng, vui lòng thử lại sau.');
            }
        });
    });
});
</script>
