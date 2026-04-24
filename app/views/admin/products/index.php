<?php
/**
 * Danh sách sản phẩm — Admin
 * Biến: $products, $total, $pages, $page, $search
 */
?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
  <form method="GET" action="/admin/products" style="display:flex;gap:8px;align-items:center">
    <input type="text" name="search" value="<?=htmlspecialchars($search??'')?>"
           placeholder="Tìm sản phẩm..." style="padding:6px 12px;font-size:11px;width:240px">
    <button type="submit" class="btn btn-sm">Tìm</button>
    <?php if($search): ?><a href="/admin/products" class="btn btn-sm">Xóa lọc</a><?php endif; ?>
  </form>
  <a href="/admin/products/create" class="btn btn-gold">+ Thêm sản phẩm</a>
</div>

<div class="admin-table-wrap">
  <div class="admin-table-head">
    <span class="admin-table-title">
      Tất cả sản phẩm
      <span style="color:var(--t2);font-weight:normal;margin-left:8px">(<?=$total?>)</span>
    </span>
  </div>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Sản phẩm</th>
        <th>Grade / Scale</th>
        <th>Giá bán</th>
        <th>Tồn kho</th>
        <th>Trạng thái</th>
        <th>Thao tác</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($products as $p): ?>
        <tr>
          <td style="font-family:var(--font-m);color:var(--text-2);font-size:11px"><?=$p['id']?></td>
          <td>
            <div style="font-size:12px;font-weight:500;max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?=htmlspecialchars($p['name'])?></div>
            <div style="font-family:var(--font-m);font-size:9px;color:var(--text-3)"><?=htmlspecialchars($p['series']??'')?></div>
          </td>
          <td>
            <?php if($p['grade']): ?><span class="badge badge-confirmed" style="font-size:9px"><?=htmlspecialchars($p['grade'])?></span><?php endif; ?>
            <div style="font-family:var(--font-m);font-size:10px;color:var(--text-2);margin-top:3px"><?=htmlspecialchars($p['scale']??'')?></div>
          </td>
          <td style="font-family:var(--font-d);font-size:16px;color:var(--gold)"><?=number_format($p['price'],0,',','.')?>đ</td>
          <td>
            <?php
              $s=(int)$p['stock'];
              $c=$s===0?'var(--red)':($s<=5?'var(--amber)':'var(--green)');
            ?>
            <span style="font-family:var(--font-m);font-size:12px;color:<?=$c?>">
              <?=$s===0?'Hết hàng':$s.' cái'?>
            </span>
          </td>
          <td>
            <?php if($p['is_active']): ?>
              <span class="badge badge-delivered">Đang bán</span>
            <?php else: ?>
              <span class="badge badge-cancelled">Đã ẩn</span>
            <?php endif; ?>
          </td>
          <td>
            <div style="display:flex;gap:6px">
              <a href="/products/detail/<?=$p['id']?>" target="_blank" class="btn btn-sm" title="Xem trên shop">↗</a>
              <a href="/admin/products/edit/<?=$p['id']?>" class="btn btn-sm">Sửa</a>
              <form method="POST" action="/admin/products/delete/<?=$p['id']?>" style="display:inline"
                    onsubmit="return confirm('Ẩn sản phẩm này?')">
                <button type="submit" class="btn btn-sm btn-danger">Ẩn</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if(empty($products)): ?>
        <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-3);font-family:var(--font-m);font-size:11px">
          <?=$search?'Không tìm thấy sản phẩm nào với từ khóa "'.htmlspecialchars($search).'"':'Chưa có sản phẩm nào'?>
        </td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  <?php if($pages>1): ?>
    <div class="pagination" style="padding:14px 18px;justify-content:flex-start;border-top:1px solid var(--border)">
      <?php for($i=1;$i<=$pages;$i++): ?>
        <a href="/admin/products?search=<?=urlencode($search??'')?>&page=<?=$i?>"
           class="page-btn<?=$i===$page?' active':''?>"><?=$i?></a>
      <?php endfor; ?>
    </div>
  <?php endif; ?>
</div>
