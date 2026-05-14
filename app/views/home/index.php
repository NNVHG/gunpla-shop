<?php

/** * Trang chủ - GUNPLA SHOP 
 * Biến truyền từ Controller: $featured, $categories, $newArrivals, $favoriteIds
 */
?>

<section class="hero">
  <div class="hero-grid"></div>
  <div class="container">
    <div class="hero-content">
      <div class="hero-tag">Bộ sưu tập mới — <?= date('m/Y') ?></div>
      <h1 class="hero-title">BUILD YOUR<span class="accent-line">LEGEND</span></h1>
      <p class="hero-sub">// Mô Hình Lắp Ráp Chính Hãng Bandai</p>
      <p class="hero-desc">Khám phá hàng trăm mô hình Gunpla chính hãng — từ HG 1/144 đến PG Unleashed cho collector đẳng cấp.</p>
      <div class="hero-actions">
        <a href="<?= BASE_URL ?>/products" class="btn-hero">Xem sản phẩm</a>
        <a href="<?= BASE_URL ?>/products?grade=PG" class="btn-ghost">Perfect Grade</a>
      </div>
    </div>
  </div>

  <div class="hero-stats">
    <div class="container">
      <div class="hero-stats-inner">
        <?php foreach (['HG' => 'High Grade / 1/144', 'MG' => 'Master Grade / 1/100', 'RG' => 'Real Grade / 1/144', 'PG' => 'Perfect Grade / 1/60'] as $g => $lbl): ?>
          <a href="<?= BASE_URL ?>/products?grade=<?= $g ?>" class="stat-item" style="text-decoration:none">
            <div class="stat-num"><?= $g ?></div>
            <div class="stat-label"><?= $lbl ?></div>
          </a>
        <?php endforeach; ?>
        <div class="stat-item">
          <div class="stat-num"><?= count($newArrivals ?? []) ?>+</div>
          <div class="stat-label">Sản phẩm<br>mới</div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php 
$activeCategories = array_filter($categories ?? [], fn($c) => ($c['product_count'] ?? 0) > 0);
usort($activeCategories, fn($a, $b) => $b['product_count'] <=> $a['product_count']);
if (!empty($activeCategories)): 
?>
  <section class="categories-section">
    <div class="container">
      <div class="section-head">
        <h2 class="section-title">Danh mục sản phẩm</h2>
        <a href="<?= BASE_URL ?>/products" class="section-link">Xem tất cả &rarr;</a>
      </div>
      <div class="category-grid">
        <?php foreach (array_slice($activeCategories, 0, 5) as $c): ?>
          <a href="<?= BASE_URL ?>/products?category_id=<?= $c['id'] ?>" class="cat-card">
            <div class="cat-icon">📦</div>
            <div class="cat-name"><?= htmlspecialchars($c['name']) ?></div>
            <div class="cat-count"><?= (int)($c['product_count'] ?? 0) ?> sản phẩm</div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
<?php endif; ?>

<?php if (!empty($newArrivals)): ?>
  <section class="products-section">
    <div class="container">
      <div class="section-head">
        <h2 class="section-title">Hàng mới về</h2>
        <a href="<?= BASE_URL ?>/products" class="section-link">Khám phá thêm &rarr;</a>
      </div>
      <div class="product-grid">
        <?php
        foreach ($newArrivals as $i => $p):
          include APP_PATH . '/Views/products/_card.php';
        endforeach;
        ?>
      </div>
    </div>
  </section>
<?php endif; ?>

<?php if (!empty($featured)): ?>
  <section class="products-section" style="background: var(--bg-surface); padding: 60px 0;">
    <div class="container">
      <div class="section-head">
        <h2 class="section-title">Gợi ý cho bạn</h2>
      </div>
      <div class="product-grid">
        <?php
        foreach ($featured as $i => $p):
          include APP_PATH . '/Views/products/_card.php';
        endforeach;
        ?>
      </div>
    </div>
  </section>
<?php endif; ?>