<?php

/** app/views/products/_card.php
 * @var array $p
 * @var int $i
 */
$stock      = (int) ($p['stock'] ?? 0);
$stockClass = $stock === 0 ? 'out-stock' : ($stock <= 5 ? 'low-stock' : 'in-stock');
$stockLabel = $stock === 0 ? 'HẾT HÀNG' : ($stock <= 5 ? "CÒN $stock" : 'CÒN HÀNG');

// SỬA LỖI 1: Lấy đúng biến image_path (dự phòng thêm thumbnail_path cho an toàn)
$thumb      = $p['image_path'] ?? $p['thumbnail_path'] ?? null;
if ($thumb && strpos($thumb, '/public/') === 0) {
  $thumb = substr($thumb, 8);
}
$thumbUrl   = $thumb ? BASE_URL . '/' . ltrim($thumb, '/') : null;

$delay      = ($i % 8) * 0.05;
?>
<div class="product-card fade-up" style="animation-delay:<?= $delay ?>s">
  
  <div class="product-img-wrap" style="position: relative; width: 100%; aspect-ratio: 1 / 1; overflow: hidden; border-radius: 4px; border: 1px solid var(--border); background-color: var(--bg-2, #f8f9fa);">
    
    <?php if ($thumbUrl): ?>
      <img src="<?= htmlspecialchars($thumbUrl) ?>" alt="Thumbnail"
        style="width: 100%; height: 100%; object-fit: cover; display: block;">
    <?php else: ?>
      <div class="img-placeholder" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: var(--text-3, #999); font-weight: bold;">
        <?= htmlspecialchars($p['grade'] ?? 'NO IMG') ?>
      </div>
    <?php endif; ?>
    
    <span class="stock-badge <?= $stockClass ?>"><?= $stockLabel ?></span>
    <span class="grade-badge"><?= htmlspecialchars($p['grade'] ?? '') ?> · <?= htmlspecialchars($p['scale'] ?? '') ?></span>
    <div class="quick-add">
      <button class="btn-add" <?= $stock === 0 ? 'disabled' : '' ?>
        onclick="addToCart(<?= $p['id'] ?>, 1, this)">
        <?= $stock === 0 ? 'HẾT HÀNG' : '+ THÊM VÀO GIỎ' ?>
      </button>
      <button class="btn-wish" title="Yêu thích">♡</button>
    </div>
  </div>
  
  <a href="<?= BASE_URL ?>/products/detail/<?= $p['id'] ?>" style="display:contents">
    <div class="product-info">
      <div class="product-series"><?= htmlspecialchars($p['series'] ?? '') ?></div>
      <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
      <div class="product-meta">
        <span class="scale-tag"><?= htmlspecialchars($p['scale'] ?? '') ?></span>
      </div>
      <div class="product-price-row">
        <span class="product-price"><?= number_format((int)$p['price'], 0, ',', '.') ?>đ</span>
        <span class="product-rating"><span class="stars">★★★★★</span></span>
      </div>
    </div>
  </a>
</div>