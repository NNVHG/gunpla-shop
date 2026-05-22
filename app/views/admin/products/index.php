
<?php
/**
 * Danh sách sản phẩm — Admin
 * @var array $products
 * @var int $total
 * @var int $pages
 * @var int $page
 * @var string $search
 */
?>
<div class="admin-action-bar">
  <div class="admin-action-bar-left">
  <form method="GET" action="<?= BASE_URL ?>/admin/products" style="display:flex;gap:8px;align-items:center">
    <input type="text" name="search" value="<?=htmlspecialchars($search??'')?>"
           placeholder="Tìm sản phẩm..." style="padding:6px 12px;font-size:13px;width:240px">
    <button type="submit" class="btn btn-sm">Tìm</button>
    <?php if($search): ?><a href="<?= BASE_URL ?>/admin/products" class="btn btn-sm">Xóa lọc</a><?php endif; ?>
  </form>
  </div>
  <a href="<?= BASE_URL ?>/admin/products/create" class="btn btn-gold">+ Thêm sản phẩm</a>
</div>

<div class="admin-table-wrap">
  <div class="admin-table-head">
    <span class="admin-table-title">
      Tất cả sản phẩm
      <span style="color:var(--t2);font-weight:normal;margin-left:8px">(<?=$total?>)</span>
    </span>
  </div>
  <div class="admin-table-scroll">
  <table>
<thead>
    <tr>
        <th style="width: 60px; text-align: center;">ID</th>
        <th style="width: 80px; text-align: center;">Hình ảnh</th> <th>Tên sản phẩm</th>
        <th>Danh mục</th>
        <th>Giá bán</th>
        <th>Số lượng</th>
        <th style="text-align: center;">Thao tác</th>
    </tr>
</thead>
    <tbody>
        <?php foreach ($products as $item): ?>
        <tr>
          <td style="text-align: center;"><?= $item['id'] ?></td>
            
            <td style="text-align: center; vertical-align: middle;">
                <?php 
                if (!empty($item['image'])): 
                ?>
                    <img src="<?= BASE_URL . '/' . htmlspecialchars($item['image']) ?>" 
                         alt="Product Image" 
                         style="width: 55px; height: 55px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border, #e2e8f0); display: block; margin: 0 auto;">
                
                <?php 
                elseif (!empty($item['image_path'])): 
                ?>
                    <img src="<?= BASE_URL . '/' . htmlspecialchars($item['image_path']) ?>" 
                         alt="Product Image" 
                         style="width: 55px; height: 55px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border, #e2e8f0); display: block; margin: 0 auto;">
                
                <?php else: ?>
                    <span style="color: var(--text-3, #999); font-size:13px; font-style: italic; display: block; text-align: center;">
                        Chưa có hình
                    </span>
                <?php endif; ?>
            </td>
          <td><?= htmlspecialchars($item['name'] ?? $item['title'] ?? '') ?></td>
          <td style="font-family:var(--font-m);color:var(--text-2);font-size:13px"><?=$item['id']?></td>
          <td>
            <div style="font-size:14px;font-weight:500;max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?=htmlspecialchars($item['name'])?></div>
            <div style="font-family:var(--font-m);font-size:11px;color:var(--text-3)"><?=htmlspecialchars($item['series']??'')?></div>
          </td>
          <td>
            <?php if($item['grade']): ?><span class="badge badge-confirmed" style="font-size:11px"><?=htmlspecialchars($item['grade'])?></span><?php endif; ?>
            <div style="font-family:var(--font-m);font-size:12px;color:var(--text-2);margin-top:3px"><?=htmlspecialchars($item['scale']??'')?></div>
          </td>
          <td style="font-family:var(--font-d);font-size:18px;color:var(--gold)"><?=number_format($item['price'],0,',','.')?>đ</td>
          <td>
            <?php
              $s=(int)$item['stock'];
              $c=$s===0?'var(--red)':($s<=5?'var(--amber)':'var(--green)');
            ?>
            <span style="font-family:var(--font-m);font-size:14px;color:<?=$c?>">
              <?=$s===0?'Hết hàng':$s.' cái'?>
            </span>
          </td>
          <td>
            <?php if($item['is_active']): ?>
              <span class="badge badge-delivered">Đang bán</span>
            <?php else: ?>
              <span class="badge badge-cancelled">Đã ẩn</span>
            <?php endif; ?>
          </td>
          <td>
            <div style="display:flex;gap:6px">
              <a href="/products/detail/<?=$item['id']?>" target="_blank" class="btn btn-sm" title="Xem trên shop">↗</a>
              <a href="<?= BASE_URL ?>/admin/products/edit/<?=$item['id']?>" class="btn btn-sm">Sửa</a>
              <form method="POST" action="<?= BASE_URL ?>/admin/products/delete/<?=$item['id']?>" style="display:inline"
                    onsubmit="return confirm('Ẩn sản phẩm này?')">
                <button type="submit" class="btn btn-sm btn-danger">Ẩn</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if(empty($products)): ?>
        <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-3);font-family:var(--font-m);font-size:13px">
          <?=$search?'Không tìm thấy sản phẩm nào với từ khóa "'.htmlspecialchars($search).'"':'Chưa có sản phẩm nào'?>
        </td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  </div>
</div>

<?php if (isset($totalPages) && $totalPages > 1): $curr = $currentPage ?? 1; ?>
<div class="pagination">
    <?php if ($curr > 1): ?>
        <a href="<?= BASE_URL ?>/admin/products?page=<?= $curr - 1 ?>" class="page-link">&laquo; Trước</a>
    <?php endif; ?>

    <?php
    $maxVisible = 10;
    
    if ($totalPages <= $maxVisible) {
        for ($i = 1; $i <= $totalPages; $i++) {
            $active = ($curr === $i) ? 'active' : '';
            echo '<a href="' . BASE_URL . '/admin/products?page=' . $i . '" class="page-link ' . $active . '">' . $i . '</a>';
        }
    } else {
                $active = ($curr === 1) ? 'active' : '';
        echo '<a href="' . BASE_URL . '/admin/products?page=1" class="page-link ' . $active . '">1</a>';

        $start = max(2, $curr - 2);
        $end = min($totalPages - 1, $curr + 2);

        if ($curr <= 4) {
            $end = 7;
        }
        if ($curr >= $totalPages - 3) {
            $start = $totalPages - 6;
        }

        if ($start > 2) {
            echo '<span class="page-ellipsis">...</span>';
        }

        for ($i = $start; $i <= $end; $i++) {
            $active = ($curr === $i) ? 'active' : '';
            echo '<a href="' . BASE_URL . '/admin/products?page=' . $i . '" class="page-link ' . $active . '">' . $i . '</a>';
        }

        if ($end < $totalPages - 1) {
            echo '<span class="page-ellipsis">...</span>';
        }

        $active = ($curr === $totalPages) ? 'active' : '';
        echo '<a href="' . BASE_URL . '/admin/products?page=' . $totalPages . '" class="page-link ' . $active . '">' . $totalPages . '</a>';
    }
    ?>

    <?php if ($curr < $totalPages): ?>
        <a href="<?= BASE_URL ?>/admin/products?page=<?= $curr + 1 ?>" class="page-link">Sau &raquo;</a>
    <?php endif; ?>
</div>
<?php endif; ?>
