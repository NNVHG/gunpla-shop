<?php
/* app/views/products/_card.php
 * Biến yêu cầu: $p (mảng sản phẩm), $i (index, dùng cho animation delay)
 */
$stock      = (int) ($p['stock'] ?? 0);
$stockClass = $stock === 0 ? 'out-stock' : ($stock <= 5 ? 'low-stock' : 'in-stock');
$stockLabel = $stock === 0 ? 'HẾT HÀNG' : ($stock <= 5 ? "CÒN $stock" : 'CÒN HÀNG');
$thumb      = $p['thumbnail_path'] ?? null;
$delay      = ($i % 8) * 0.05;
?>
<div class="product-card fade-up" style="animation-delay:<?= $delay ?>s">
  <div class="product-img-wrap">
    <?php if ($thumb): ?>
      <img src="<?= htmlspecialchars($thumb) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy">
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