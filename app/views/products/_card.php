<?php

/** app/views/products/_card.php
 * @var array $p
 * @var int $i
 */
$stock      = (int) ($p['stock'] ?? 0);
$stockClass = $stock === 0 ? 'out-stock' : ($stock <= 5 ? 'low-stock' : 'in-stock');
$stockLabel = $stock === 0 ? 'HẾT HÀNG' : ($stock <= 5 ? "CÒN $stock" : 'CÒN HÀNG');

// Xử lý chuẩn hóa đường dẫn ảnh
$thumb      = $p['thumbnail_path'] ?? null;
if ($thumb && strpos($thumb, '/public/') === 0) {
  $thumb = substr($thumb, 8);
}
$thumbUrl   = $thumb ? BASE_URL . '/' . ltrim($thumb, '/') : null;

$delay      = ($i % 8) * 0.05;
?>
<div class="product-card fade-up" style="animation-delay:<?= $delay ?>s">
  <div class="product-img-wrap">
    <?php if ($thumbUrl): ?>
      <img src="<?= htmlspecialchars($thumbUrl) ?>" alt="Thumbnail"
        style="width:100%;border-radius:4px;border:1px solid var(--border);object-fit:cover;max-height:160px">
    <?php else: ?>
      <div class="img-placeholder"><?= htmlspecialchars($p['grade'] ?? '?') ?></div>
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