<?php
function stockBadge(int $s): string
{
    if ($s === 0) return '<span class="stock-badge out-stock">HẾT</span>';
    if ($s <= 5)  return "<span class='stock-badge low-stock'>CÒN $s</span>";
    return '<span class="stock-badge in-stock">CÒN HÀNG</span>';
}

/**
 * @var array $products
 * @var int $total
 * @var int $pages
 * @var int $page
 * @var array $filters
 * @var string $sort
 * @var array $categories
 * @var array $favoriteIds
 * @var array $groupedCategories
 */
$currentGrade  = $filters['grade']  ?? '';
$currentScale  = $filters['scale']  ?? '';
$currentSeries = $filters['series'] ?? ''; // Thêm biến này cho đồng bộ mã bên dưới
$currentSort   = $sort ?? 'newest';
$currentGroup  = $filters['group']  ?? '';
$currentCat    = isset($filters['category_id']) && $filters['category_id'] !== '' ? (int)$filters['category_id'] : null;

// Xác định Tab hiện tại để hiển thị Sidebar chính xác
$isGunpla = ($currentGroup === 'gunpla');
$isTool   = ($currentGroup === 'tools' || $currentGroup === 'tool');
$isAll    = (!$isGunpla && !$isTool); // Nếu không phải Gunpla hay Tool thì là trang "Tất cả"

$buildUrl = function($newParams) use ($filters, $currentSort) {
    $merged = array_merge($filters ?? [], $newParams);
    if (!isset($newParams['sort'])) {
        $merged['sort'] = $currentSort;
    }
    // Xóa các tham số rỗng để URL nhìn sạch sẽ
    $merged = array_filter($merged, function($v) {
        return $v !== null && $v !== '';
    });
    return BASE_URL . '/products?' . http_build_query($merged);
};
?>
<!-- noUiSlider CSS & JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.js"></script>

<div class="container" style="padding-top:32px;padding-bottom:48px">
  <div class="breadcrumb"><a href="<?= BASE_URL ?>/">Trang chủ</a><span>/</span>Sản phẩm</div>
  <div class="catalog-layout">

    <aside>
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:7px;padding:20px">

        <?php if ($isAll || $isGunpla): ?>
            
            <div style="font-family:var(--font-mono);font-size:10px;color:var(--gold);letter-spacing:.15em;text-transform:uppercase;margin-bottom:10px">// Cấp độ (Grade)</div>
            <a href="<?= $buildUrl(['grade' => null]) ?>"
               class="filter-link <?= empty($currentGrade) ? 'active' : '' ?>">Tất cả Grade</a>
            <?php foreach (['SD' => 'SD (Super Deformed)', 'EG' => 'EG (Entry Grade)', 'HG' => 'HG (High Grade)', 'RG' => 'RG (Real Grade)', 'MG' => 'MG (Master Grade)', 'MGSD' => 'MGSD', 'PG' => 'PG (Perfect Grade)'] as $v => $l): ?>
                <a href="<?= $buildUrl(['grade' => $v]) ?>" 
                   class="filter-link <?= $currentGrade === $v ? 'active' : '' ?>"><?= $l ?></a>
            <?php endforeach; ?>

            <div style="font-family:var(--font-mono);font-size:10px;color:var(--gold);letter-spacing:.15em;text-transform:uppercase;margin:24px 0 10px">// Tỷ lệ (Scale)</div>
            <a href="<?= $buildUrl(['scale' => null]) ?>"
               class="filter-link <?= empty($currentScale) ? 'active' : '' ?>">Tất cả Tỷ lệ</a>
            <?php foreach (['1/144' => '1/144 Scale', '1/100' => '1/100 Scale', '1/60' => '1/60 Scale', '1/48' => '1/48 Scale', 'Non-scale' => 'Không tỷ lệ'] as $v => $l): ?>
                <a href="<?= $buildUrl(['scale' => $v]) ?>" 
                   class="filter-link <?= $currentScale === $v ? 'active' : '' ?>"><?= $l ?></a>
            <?php endforeach; ?>

            <div style="font-family:var(--font-mono);font-size:10px;color:var(--gold);letter-spacing:.15em;text-transform:uppercase;margin:24px 0 10px">// Vũ trụ phim (Series)</div>
            <a href="<?= $buildUrl(['series' => null]) ?>"
               class="filter-link <?= empty($currentSeries) ? 'active' : '' ?>">Tất cả Vũ trụ</a>
            <?php foreach (['Gundam' => 'Gundam Gốc (UC)', 'SEED' => 'Gundam SEED (CE)', '00' => 'Gundam 00 (AD)', 'Orphans' => 'Iron-Blooded (PD)', 'Mercury' => 'Witch from Mercury (AS)'] as $v => $l): ?>
                <a href="<?= $buildUrl(['series' => $v]) ?>" 
                   class="filter-link <?= $currentSeries === $v ? 'active' : '' ?>"><?= $l ?></a>
            <?php endforeach; ?>
            
        <?php endif; ?>


        <?php if ($isAll || $isTool): ?>
            
            <div style="font-family:var(--font-mono);font-size:10px;color:var(--gold);letter-spacing:.15em;text-transform:uppercase; <?= $isAll ? 'margin:24px 0 10px;' : 'margin-bottom:10px;' ?>">// Dụng cụ & Phụ kiện</div>
            <a href="<?= $buildUrl(['category_id' => null, 'group' => 'tools']) ?>" 
               class="filter-link <?= empty($currentCat) ? 'active' : '' ?>">Tất cả Dụng cụ</a>
               
            <?php 
            // Chỉ in ra các danh mục thuộc loại "Dụng cụ, hóa chất, phụ kiện"
            $toolTypes = ['tool', 'accessory', 'chemical', 'combo'];
            if (!empty($categories)) {
                foreach ($categories as $cat): 
                    if (in_array($cat['type'], $toolTypes)):
            ?>
                    <a href="<?= $buildUrl(['category_id' => $cat['id'], 'group' => 'tools']) ?>" 
                       class="filter-link <?= $currentCat === (int)$cat['id'] ? 'active' : '' ?>">
                       <?= htmlspecialchars($cat['name']) ?>
                    </a>
            <?php 
                    endif;
                endforeach; 
            }
            ?>
            
        <?php endif; ?>

        <!-- Bộ lọc tình trạng kho -->
        <div style="font-family:var(--font-mono);font-size:10px;color:var(--gold);letter-spacing:.15em;text-transform:uppercase;margin:24px 0 10px">// Tình trạng hàng</div>
        <a href="<?= $buildUrl(['stock_status' => null]) ?>"
           class="filter-link <?= empty($filters['stock_status']) ? 'active' : '' ?>">Tất cả tình trạng</a>
        <a href="<?= $buildUrl(['stock_status' => 'in_stock']) ?>"
           class="filter-link <?= ($filters['stock_status'] ?? '') === 'in_stock' ? 'active' : '' ?>">Còn hàng</a>
        <a href="<?= $buildUrl(['stock_status' => 'out_stock']) ?>"
           class="filter-link <?= ($filters['stock_status'] ?? '') === 'out_stock' ? 'active' : '' ?>">Hết hàng</a>

        <!-- Bộ lọc khoảng giá -->
        <div style="font-family:var(--font-mono);font-size:10px;color:var(--gold);letter-spacing:.15em;text-transform:uppercase;margin:24px 0 15px">// Khoảng giá (VNĐ)</div>
        <div style="padding:0 10px;margin-bottom:15px">
          <div id="price-slider" style="margin-bottom:20px;height:8px;border:none;background:var(--border);border-radius:4px"></div>
          <div style="display:flex;justify-content:space-between;align-items:center;font-family:var(--font-mono);font-size:10px;color:var(--text-hint);margin-bottom:15px">
            <span id="price-min-val">0đ</span>
            <span>-</span>
            <span id="price-max-val">5.000.000đ</span>
          </div>
          <button id="btn-apply-price" class="btn btn-gold" style="width:100%;padding:8px;font-size:11px;border-radius:4px;font-family:var(--font-mono);letter-spacing:0.05em">ÁP DỤNG</button>
        </div>

      </div>
    </aside>

    <div>
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
        <div style="font-family:var(--font-mono);font-size:11px;color:var(--text-hint)">
          <?= $total ?> sản phẩm<?= $currentGrade ? " · Grade: $currentGrade" : '' ?>
        </div>
        
        <?php
        // Xử lý URL cho nút select Sort an toàn hơn
        $sortFilters = array_filter($filters ?? [], fn($v) => $v !== null && $v !== '');
        $sortBaseUrl = BASE_URL . '/products?' . http_build_query($sortFilters);
        $sortConnector = empty($sortFilters) ? '' : '&';
        ?>
        <select class="sort-select" onchange="window.location='<?= $sortBaseUrl . $sortConnector ?>sort=' + this.value">
          <?php foreach (['newest' => 'Mới nhất', 'price_asc' => 'Giá tăng dần', 'price_desc' => 'Giá giảm dần', 'bestseller' => 'Bán chạy'] as $v => $l): ?>
            <option value="<?= $v ?>" <?= $currentSort === $v ? ' selected' : '' ?>><?= $l ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <?php if (empty($products)): ?>
        <div style="text-align:center;padding:80px 0;color:var(--text-hint);font-family:var(--font-mono);font-size:12px">
          Không tìm thấy sản phẩm nào
        </div>
      <?php else: ?>
        <div class="product-grid">
          <?php foreach ($products as $p): ?>
            <div class="product-card" onclick="window.location='<?= BASE_URL ?>/products/detail/<?= $p['id'] ?>'">
              <div class="product-img-wrap" style="position: relative; width: 100%; aspect-ratio: 1 / 1; overflow: hidden; background-color: var(--bg-2, #f8f9fa); border-radius: 4px; border: 1px solid var(--border);">
                
                <?php 
                // Xử lý lấy đúng tên biến và chuẩn hóa đường dẫn URL ảnh
                $thumb = $p['image_path'] ?? $p['thumbnail_path'] ?? null;
                if ($thumb && strpos($thumb, '/public/') === 0) {
                    $thumb = substr($thumb, 8);
                }
                $thumbUrl = $thumb ? BASE_URL . '/' . ltrim($thumb, '/') : null;
                ?>

                <!-- Compare Checkbox -->
                <label class="compare-checkbox-wrap" onclick="event.stopPropagation();" style="position:absolute;top:10px;left:10px;z-index:5;background:rgba(0,0,0,0.7);padding:4px 8px;border-radius:4px;display:flex;align-items:center;gap:6px;cursor:pointer;user-select:none;border:1px solid rgba(255,255,255,0.1)">
                  <input type="checkbox" class="compare-checkbox" data-id="<?= $p['id'] ?>" data-name="<?= htmlspecialchars($p['name']) ?>" data-image="<?= htmlspecialchars($thumbUrl ?? '') ?>" style="width:14px;height:14px;accent-color:var(--gold);cursor:pointer">
                  <span style="font-size:9px;color:#fff;font-family:var(--font-mono);font-weight:bold;letter-spacing:0.05em">SO SÁNH</span>
                </label>

                <?php if ($thumbUrl): ?>
                  <img src="<?= htmlspecialchars($thumbUrl) ?>" 
                       alt="<?= htmlspecialchars($p['name']) ?>" 
                       loading="lazy" 
                       style="width: 100%; height: 100%; object-fit: cover; display: block;">
                <?php else: ?>
                  <div class="img-placeholder" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: var(--text-3, #999); font-weight: bold; font-family: var(--font-mono, sans-serif);">
                      <?= htmlspecialchars($p['grade'] ?? 'NO IMG') ?>
                  </div>
                <?php endif; ?>

                <?= stockBadge((int)$p['stock']) ?>
                <span class="grade-badge"><?= htmlspecialchars($p['grade'] ?? '') ?></span>

                <div class="quick-add">
                  <button class="btn-add" onclick="event.stopPropagation();addToCart(<?= $p['id'] ?>)" <?= $p['stock'] == 0 ? ' disabled' : '' ?>>
                    <?= $p['stock'] == 0 ? 'HẾT HÀNG' : '+ GIỎ HÀNG' ?>
                  </button>

                  <?php $isFav = in_array($p['id'], $favoriteIds ?? []); ?>
                  <button class="btn-wish <?= $isFav ? 'active' : '' ?>"
                    onclick="event.stopPropagation(); toggleFavorite(<?= $p['id'] ?>, this)"
                    title="<?= $isFav ? 'Bỏ yêu thích' : 'Thêm vào yêu thích' ?>">
                    <?= $isFav ? '♥' : '♡' ?>
                  </button>
                </div>

              </div>
              <div class="product-info">
                <div class="product-series"><?= htmlspecialchars($p['series'] ?? '') ?></div>
                <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
                <div style="margin-bottom:6px"><span class="scale-tag"><?= htmlspecialchars($p['scale'] ?? '') ?></span></div>
                <div class="product-price-row">
                  <span class="product-price"><?= number_format($p['price'], 0, ',', '.') ?> đ</span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <?php if ($pages > 1): ?>
          <div class="pagination">
            <?php if ($page > 1): ?><a href="<?= $buildUrl(['page' => $page - 1]) ?>" class="page-btn">&laquo;</a><?php endif; ?>
            <?php for ($i = max(1, $page - 2); $i <= min($pages, $page + 2); $i++): ?>
              <a href="<?= $buildUrl(['page' => $i]) ?>" class="page-btn<?= $i === $page ? ' active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <?php if ($page < $pages): ?><a href="<?= $buildUrl(['page' => $page + 1]) ?>" class="page-btn">&raquo;</a><?php endif; ?>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Khởi tạo bộ lọc khoảng giá noUiSlider
    const slider = document.getElementById('price-slider');
    if (slider) {
        const minVal = parseInt('<?= $filters["min_price"] ?? 0 ?>') || 0;
        const maxVal = parseInt('<?= $filters["max_price"] ?? 5000000 ?>') || 5000000;
        
        noUiSlider.create(slider, {
            start: [minVal, maxVal],
            connect: true,
            range: {
                'min': 0,
                'max': 5000000
            },
            step: 50000,
            format: {
                to: function (value) {
                    return Math.round(value);
                },
                from: function (value) {
                    return Math.round(value);
                }
            }
        });

        const minSpan = document.getElementById('price-min-val');
        const maxSpan = document.getElementById('price-max-val');

        const formatVND = (val) => {
            return new Intl.NumberFormat('vi-VN').format(val) + 'đ';
        };

        slider.noUiSlider.on('update', function (values, handle) {
            if (handle === 0) {
                minSpan.textContent = formatVND(values[0]);
            } else {
                maxSpan.textContent = formatVND(values[1]);
            }
        });

        document.getElementById('btn-apply-price').addEventListener('click', () => {
            const values = slider.noUiSlider.get();
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('min_price', values[0]);
            currentUrl.searchParams.set('max_price', values[1]);
            // Reset page về 1 khi lọc
            currentUrl.searchParams.set('page', 1);
            window.location.href = currentUrl.toString();
        });
    }

    // 2. Đồng bộ hóa Compare checkboxes dựa trên localStorage
    const compareCheckboxes = document.querySelectorAll('.compare-checkbox');
    const updateCheckboxes = () => {
        const currentCompare = JSON.parse(localStorage.getItem('compare_products') || '[]');
        compareCheckboxes.forEach(cb => {
            const id = parseInt(cb.getAttribute('data-id'));
            cb.checked = currentCompare.includes(id);
        });
    };

    updateCheckboxes();

    compareCheckboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            const id = parseInt(cb.getAttribute('data-id'));
            const name = cb.getAttribute('data-name');
            const img = cb.getAttribute('data-image');
            
            let currentCompare = JSON.parse(localStorage.getItem('compare_products') || '[]');
            let currentCompareNames = JSON.parse(localStorage.getItem('compare_product_names') || '{}');
            let currentCompareImages = JSON.parse(localStorage.getItem('compare_product_images') || '{}');
            
            if (cb.checked) {
                if (currentCompare.length >= 3) {
                    alert('Bạn chỉ có thể so sánh tối đa 3 sản phẩm cùng lúc.');
                    cb.checked = false;
                    return;
                }
                if (!currentCompare.includes(id)) {
                    currentCompare.push(id);
                    currentCompareNames[id] = name;
                    currentCompareImages[id] = img;
                }
            } else {
                currentCompare = currentCompare.filter(item => item !== id);
                delete currentCompareNames[id];
                delete currentCompareImages[id];
            }
            
            localStorage.setItem('compare_products', JSON.stringify(currentCompare));
            localStorage.setItem('compare_product_names', JSON.stringify(currentCompareNames));
            localStorage.setItem('compare_product_images', JSON.stringify(currentCompareImages));
            
            // Phát ra sự kiện custom để cập nhật Floating Compare Bar
            window.dispatchEvent(new CustomEvent('compare_updated'));
        });
    });

    // Lắng nghe sự kiện compare_updated từ bên ngoài (e.g. nếu xóa từ Compare Bar)
    window.addEventListener('compare_updated', () => {
        updateCheckboxes();
    });
});
</script>