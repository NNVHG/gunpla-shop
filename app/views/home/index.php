<?php
/** Trang chủ. Biến: $featured, $categories, $newArrivals */
function pCard(array $p): string {
    $t=!empty($p['thumbnail_path'])?"<img src='".htmlspecialchars($p['thumbnail_path'])."' alt='".htmlspecialchars($p['name'])."' loading='lazy'>":"<div class='img-placeholder'>".htmlspecialchars($p['grade']??'?')."</div>";
    $s=(int)$p['stock'];$d=$s===0?'disabled':'';$l=$s===0?'HẾT HÀNG':'+ GIỎ HÀNG';
    $sb=$s===0?"<span class='stock-badge out-stock'>HẾT</span>":($s<=5?"<span class='stock-badge low-stock'>CÒN $s</span>":"<span class='stock-badge in-stock'>CÒN HÀNG</span>");
    return "<div class='product-card'><div class='product-img-wrap'>$t$sb<span class='grade-badge'>".htmlspecialchars($p['grade']??'')." · ".htmlspecialchars($p['scale']??'')."</span><div class='quick-add'><button class='btn-add' onclick='addToCart({$p['id']})' $d>$l</button><button class='btn-wish'>♡</button></div></div><div class='product-info'><div class='product-series'>".htmlspecialchars($p['series']??'')."</div><div class='product-name'>".htmlspecialchars($p['name'])."</div><div class='product-price-row'><span class='product-price'>".number_format($p['price'],0,',','.')."đ</span></div></div></div>";
}
?>
<section class="hero"><div class="hero-grid"></div>
  <div class="container"><div class="hero-content">
    <div class="hero-tag">Bộ sưu tập mới — <?=date('m/Y')?></div>
    <h1 class="hero-title">BUILD YOUR<span class="accent-line">LEGEND</span></h1>
    <p class="hero-sub">// Mô Hình Lắp Ráp Chính Hãng Bandai</p>
    <p class="hero-desc">Khám phá hàng trăm mô hình Gunpla chính hãng — từ HG 1/144 đến PG Unleashed cho collector đẳng cấp.</p>
    <div class="hero-actions"><a href="/products" class="btn-hero">Xem sản phẩm</a><a href="/products?grade=PG" class="btn-ghost">Perfect Grade</a></div>
  </div></div>
  <div class="hero-stats"><div class="container"><div class="hero-stats-inner">
    <?php foreach(['HG'=>'High Grade / 1/144','MG'=>'Master Grade / 1/100','RG'=>'Real Grade / 1/144','PG'=>'Perfect Grade / 1/60'] as $g=>$lbl): ?>
      <a href="/products?grade=<?=$g?>" class="stat-item" style="text-decoration:none"><div class="stat-num"><?=$g?></div><div class="stat-label"><?=$lbl?></div></a>
    <?php endforeach; ?>
    <div class="stat-item"><div class="stat-num"><?=count($newArrivals??[])?>+</div><div class="stat-label">Sản phẩm<br>mới</div></div>
  </div></div></div>
</section>

<?php if(!empty($categories)): ?>
<section class="categories-section"><div class="container">
  <div class="section-head"><h2 class="section-title">Danh mục</h2><a href="/products" class="section-link">Xem tất cả &rarr;</a></div>
  <div class="category-grid">
    <?php foreach(array_slice($categories,0,5) as $c): ?>
      <a href="/products?category_id=<?=$c['id']?>" class="cat-card">
        <div class="cat-icon"><?=htmlspecialchars($c['name'])?></div>
        <div class="cat-name"><?=htmlspecialchars($c['name'])?></div>
        <div class="cat-count"><?=(int)($c['product_count']??0)?> sản phẩm</div>
      </a>
    <?php endforeach; ?>
  </div>
</div></section>
<?php endif; ?>

<?php if(!empty($newArrivals)): ?>
<section class="products-section"><div class="container">
  <div class="section-head"><h2 class="section-title">Mới nhất</h2><a href="/products" class="section-link">Xem tất cả &rarr;</a></div>
  <div class="product-grid"><?php foreach($newArrivals as $p): echo pCard($p); endforeach; ?></div>
</div></section>
<?php endif; ?>
