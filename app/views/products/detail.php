<?php
/**
 * Chi tiết sản phẩm
 * Biến: $product, $related
 */
$p      = $product;
$images = $p['images'] ?? [];
$stock  = (int) $p['stock'];
$stockClass = $stock===0?'out':($stock<=5?'low':'ok');
$stockText  = $stock===0?'Hết hàng':($stock<=5?"Còn $stock sản phẩm":"Còn hàng");
?>
<div class="container">
  <div class="breadcrumb">
    <a href="<?= BASE_URL ?>/">Trang chủ</a><span>/</span>
    <a href="<?= BASE_URL ?>/products">Sản phẩm</a><span>/</span>
    <?=htmlspecialchars($p['name'])?>
  </div>
  <div class="product-detail-grid">
    <div class="detail-images">
      <div class="main-img" id="mainImgWrap">
        <?php if(!empty($images)): ?>
          <img src="<?=htmlspecialchars($images[0]['image_path'])?>" alt="<?=htmlspecialchars($p['name'])?>" id="mainImg">
        <?php else: ?>
          <div class="img-placeholder"><?=htmlspecialchars($p['grade']??'?')?></div>
        <?php endif; ?>
      </div>
      <?php if(count($images)>1): ?>
        <div class="thumb-strip">
          <?php foreach($images as $i=>$img): ?>
            <div class="thumb-item<?=$i===0?' active':''?>" onclick="switchImg('<?=htmlspecialchars($img['image_path'])?>', this)">
              <img src="<?=htmlspecialchars($img['image_path'])?>" alt="">
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    <div class="detail-info">
      <div class="detail-series"><?=htmlspecialchars($p['series']??'')?></div>
      <h1 class="detail-name"><?=htmlspecialchars($p['name'])?></h1>
      <div class="detail-price"><?=number_format($p['price'],0,',','.')?>đ</div>
      <div class="detail-tags">
        <?php if($p['grade']): ?><span class="detail-tag"><?=htmlspecialchars($p['grade'])?></span><?php endif; ?>
        <?php if($p['scale']): ?><span class="detail-tag"><?=htmlspecialchars($p['scale'])?></span><?php endif; ?>
        <?php if($p['category_name']??''): ?><span class="detail-tag"><?=htmlspecialchars($p['category_name'])?></span><?php endif; ?>
      </div>
      <div class="detail-stock <?=$stockClass?>"><?=$stockText?></div>
      <?php if($stock>0): ?>
        <div class="qty-row">
          <div class="qty-control">
            <button onclick="changeQty(-1, <?=$stock?>)">−</button>
            <span id="qtyDisplay">1</span>
            <button onclick="changeQty(1, <?=$stock?>)">+</button>
          </div>
          <button class="btn-add-large" onclick="addToCartDetail(<?=$p['id']?>)">+ THÊM VÀO GIỎ</button>
        </div>
      <?php else: ?>
        <button class="btn-add-large" disabled style="opacity:.4;cursor:not-allowed">HẾT HÀNG</button>
      <?php endif; ?>
      <?php if(!empty($p['description'])): ?>
        <div class="detail-desc"><?=nl2br(htmlspecialchars($p['description']))?></div>
      <?php endif; ?>
    </div>
  </div>

  <?php if(!empty($related)): ?>
    <div style="padding-bottom:48px">
      <div class="section-head"><h2 class="section-title">Liên quan</h2></div>
      <div class="product-grid">
        <?php foreach($related as $r): ?>
          <div class="product-card" onclick="window.location='<?= BASE_URL ?>/products/detail/<?=$r['id']?>'">
            <div class="product-img-wrap">
              <?php if(!empty($r['thumbnail_path'])): ?>
                <img src="<?=htmlspecialchars($r['thumbnail_path'])?>" alt="<?=htmlspecialchars($r['name'])?>" loading="lazy">
              <?php else: ?>
                <div class="img-placeholder"><?=htmlspecialchars($r['grade']??'?')?></div>
              <?php endif; ?>
              <span class="grade-badge"><?=htmlspecialchars($r['grade']??'')?></span>
            </div>
            <div class="product-info">
              <div class="product-series"><?=htmlspecialchars($r['series']??'')?></div>
              <div class="product-name"><?=htmlspecialchars($r['name'])?></div>
              <div class="product-price-row"><span class="product-price"><?=number_format($r['price'],0,',','.')?>đ</span></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
</div>