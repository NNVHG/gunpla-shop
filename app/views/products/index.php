<?php
/**
 * Danh sách sản phẩm — có filter, sort, phân trang
 * Biến: $products, $total, $pages, $page, $filters, $sort, $categories
 */
function stockBadge(int $s): string {
    if($s===0) return '<span class="stock-badge out-stock">HẾT</span>';
    if($s<=5)  return "<span class='stock-badge low-stock'>CÒN $s</span>";
    return '<span class="stock-badge in-stock">CÒN HÀNG</span>';
}
$currentGrade = $filters['grade']  ?? '';
$currentScale = $filters['scale']  ?? '';
$currentSort  = $sort ?? 'newest';
?>
<div class="container" style="padding-top:32px;padding-bottom:48px">
  <div class="breadcrumb"><a href="/">Trang chủ</a><span>/</span>Sản phẩm</div>
  <div style="display:grid;grid-template-columns:220px 1fr;gap:32px;margin-top:28px">
    <!-- SIDEBAR -->
    <aside>
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:7px;padding:20px">
        <div style="font-family:var(--font-mono);font-size:10px;color:var(--gold);letter-spacing:.15em;text-transform:uppercase;margin-bottom:14px">// Lọc theo dòng</div>
        <?php foreach([''=>'Tất cả','HG'=>'High Grade','MG'=>'Master Grade','RG'=>'Real Grade','PG'=>'Perfect Grade'] as $v=>$l): ?>
          <a href="/products?grade=<?=$v?>&sort=<?=$currentSort?>"
             style="display:block;padding:7px 10px;font-family:var(--font-mono);font-size:11px;border-radius:4px;margin-bottom:3px;letter-spacing:.06em;transition:all .15s;<?=$currentGrade===$v?'background:rgba(200,168,90,.1);color:var(--gold);':'color:var(--text-secondary);' ?>">
            <?=$l?>
          </a>
        <?php endforeach; ?>
        <div style="font-family:var(--font-mono);font-size:10px;color:var(--gold);letter-spacing:.15em;text-transform:uppercase;margin:18px 0 14px">// Tỷ lệ</div>
        <?php foreach([''=>'Tất cả','1/144'=>'1/144 Scale','1/100'=>'1/100 Scale','1/60'=>'1/60 Scale'] as $v=>$l): ?>
          <a href="/products?<?=http_build_query(array_merge($filters??[],['scale'=>$v,'page'=>1]))?>"
             style="display:block;padding:7px 10px;font-family:var(--font-mono);font-size:11px;border-radius:4px;margin-bottom:3px;letter-spacing:.06em;transition:all .15s;<?=$currentScale===$v?'background:rgba(200,168,90,.1);color:var(--gold);':'color:var(--text-secondary);' ?>">
            <?=$l?>
          </a>
        <?php endforeach; ?>
      </div>
    </aside>
    <!-- GRID -->
    <div>
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
        <div style="font-family:var(--font-mono);font-size:11px;color:var(--text-hint)">
          <?=$total?> sản phẩm<?=$currentGrade?" · Grade: $currentGrade":''?>
        </div>
        <select class="sort-select" onchange="window.location='/products?<?=http_build_query(array_merge($filters??[]))?>&sort='+this.value">
          <?php foreach(['newest'=>'Mới nhất','price_asc'=>'Giá tăng dần','price_desc'=>'Giá giảm dần','bestseller'=>'Bán chạy'] as $v=>$l): ?>
            <option value="<?=$v?>"<?=$currentSort===$v?' selected':''?>><?=$l?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php if(empty($products)): ?>
        <div style="text-align:center;padding:80px 0;color:var(--text-hint);font-family:var(--font-mono);font-size:12px">
          Không tìm thấy sản phẩm nào
        </div>
      <?php else: ?>
        <div class="product-grid">
          <?php foreach($products as $p): ?>
            <div class="product-card" onclick="window.location='/products/detail/<?=$p['id']?>'">
              <div class="product-img-wrap">
                <?php if(!empty($p['thumbnail_path'])): ?>
                  <img src="<?=htmlspecialchars($p['thumbnail_path'])?>" alt="<?=htmlspecialchars($p['name'])?>" loading="lazy">
                <?php else: ?>
                  <div class="img-placeholder"><?=htmlspecialchars($p['grade']??'?')?></div>
                <?php endif; ?>
                <?=stockBadge((int)$p['stock'])?>
                <span class="grade-badge"><?=htmlspecialchars($p['grade']??'')?></span>
                <div class="quick-add">
                  <button class="btn-add" onclick="event.stopPropagation();addToCart(<?=$p['id']?>)"<?=$p['stock']==0?' disabled':''?>>
                    <?=$p['stock']==0?'HẾT HÀNG':'+ GIỎ HÀNG'?>
                  </button>
                  <button class="btn-wish" onclick="event.stopPropagation()">♡</button>
                </div>
              </div>
              <div class="product-info">
                <div class="product-series"><?=htmlspecialchars($p['series']??'')?></div>
                <div class="product-name"><?=htmlspecialchars($p['name'])?></div>
                <div style="margin-bottom:6px"><span class="scale-tag"><?=htmlspecialchars($p['scale']??'')?></span></div>
                <div class="product-price-row">
                  <span class="product-price"><?=number_format($p['price'],0,',','.')?> đ</span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <!-- Phân trang -->
        <?php if($pages>1): ?>
          <div class="pagination">
            <?php if($page>1): ?><a href="?<?=http_build_query(array_merge($filters??[],['sort'=>$sort,'page'=>$page-1]))?>" class="page-btn">&laquo;</a><?php endif; ?>
            <?php for($i=max(1,$page-2);$i<=min($pages,$page+2);$i++): ?>
              <a href="?<?=http_build_query(array_merge($filters??[],['sort'=>$sort,'page'=>$i]))?>" class="page-btn<?=$i===$page?' active':''?>"><?=$i?></a>
            <?php endfor; ?>
            <?php if($page<$pages): ?><a href="?<?=http_build_query(array_merge($filters??[],['sort'=>$sort,'page'=>$page+1]))?>" class="page-btn">&raquo;</a><?php endif; ?>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
