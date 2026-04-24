<?php
/**
 * Form thêm / sửa sản phẩm
 * Biến: $product (null = tạo mới), $categories
 * Nếu sửa: $product là array đầy đủ từ Product::getById()
 */
$isEdit  = !empty($product);
$action  = $isEdit ? "/admin/products/update/{$product['id']}" : '/admin/products/store';
$errors  = $_SESSION['form_errors'] ?? [];
$saved   = $_SESSION['form_data']   ?? $product ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_data']);

function fval(array $d, string $k, string $default=''): string {
    return htmlspecialchars($d[$k] ?? $default);
}
function ferr(array $e, string $k): string {
    return isset($e[$k]) ? "<div class='form-error'>".htmlspecialchars($e[$k])."</div>" : '';
}
?>

<form method="POST" action="<?=$action?>" enctype="multipart/form-data">

  <div style="display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start">

    <!-- CỘT TRÁI -->
    <div>

      <!-- Thông tin cơ bản -->
      <div class="admin-table-wrap" style="margin-bottom:20px">
        <div class="admin-table-head"><span class="admin-table-title">// Thông tin cơ bản</span></div>
        <div style="padding:20px;display:flex;flex-direction:column;gap:14px">
          <div class="form-group full">
            <label>Tên sản phẩm *</label>
            <input type="text" name="name" value="<?=fval($saved,'name')?>"
                   placeholder="HG 1/144 RX-78-2 Gundam (Revive)" required>
            <?=ferr($errors,'name')?>
          </div>
          <div class="form-grid">
            <div class="form-group">
              <label>Giá bán (VNĐ) *</label>
              <input type="number" name="price" value="<?=fval($saved,'price')?>" min="0" required placeholder="250000">
              <?=ferr($errors,'price')?>
            </div>
            <div class="form-group">
              <label>Tồn kho (số lượng)</label>
              <input type="number" name="stock" value="<?=fval($saved,'stock','0')?>" min="0">
            </div>
            <div class="form-group">
              <label>Grade (dòng)</label>
              <select name="grade">
                <option value="">-- Chọn --</option>
                <?php foreach(['HG','RG','MG','MG Ver.Ka','PG','PG Unleashed','SD','Entry Grade'] as $g): ?>
                  <option value="<?=$g?>"<?=($saved['grade']??'')===$g?' selected':''?>><?=$g?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Tỷ lệ (scale)</label>
              <select name="scale">
                <option value="">-- Chọn --</option>
                <?php foreach(['1/144','1/100','1/60','1/48','Non Scale'] as $s): ?>
                  <option value="<?=$s?>"<?=($saved['scale']??'')===$s?' selected':''?>><?=$s?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Series (dòng phim)</label>
              <input type="text" name="series" value="<?=fval($saved,'series')?>" placeholder="Mobile Suit Gundam">
            </div>
            <div class="form-group">
              <label>Trọng lượng (gram) — tính phí ship</label>
              <input type="number" name="weight_gram" value="<?=fval($saved,'weight_gram')?>" min="0" placeholder="200">
            </div>
          </div>
          <div class="form-group">
            <label>Mô tả sản phẩm</label>
            <textarea name="description" rows="4" placeholder="Mô tả chi tiết về sản phẩm..."><?=fval($saved,'description')?></textarea>
          </div>
        </div>
      </div>

      <!-- Danh mục -->
      <div class="admin-table-wrap" style="margin-bottom:20px">
        <div class="admin-table-head"><span class="admin-table-title">// Danh mục</span></div>
        <div style="padding:20px">
          <div class="form-group">
            <label>Danh mục *</label>
            <select name="category_id" required>
              <option value="">-- Chọn danh mục --</option>
              <?php foreach($categories as $cat): ?>
                <option value="<?=$cat['id']?>"
                  <?=(($saved['category_id']??''))==$cat['id']?' selected':''?>>
                  <?=($cat['parent_name']??null) ? htmlspecialchars($cat['parent_name']).' → ' : ''?><?=htmlspecialchars($cat['name'])?>
                </option>
              <?php endforeach; ?>
            </select>
            <?=ferr($errors,'category_id')?>
          </div>
        </div>
      </div>

    </div>

    <!-- CỘT PHẢI -->
    <div>

      <!-- Trạng thái -->
      <div class="admin-table-wrap" style="margin-bottom:16px">
        <div class="admin-table-head"><span class="admin-table-title">// Trạng thái</span></div>
        <div style="padding:16px">
          <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
            <input type="checkbox" name="is_active" value="1"
              <?=($saved['is_active']??1)?'checked':''?> style="width:16px;height:16px;accent-color:var(--gold)">
            <span style="color:var(--t1);font-size:12px;letter-spacing:.04em">Hiển thị trên cửa hàng</span>
          </label>
        </div>
      </div>

      <!-- Ảnh đại diện -->
      <div class="admin-table-wrap" style="margin-bottom:16px">
        <div class="admin-table-head"><span class="admin-table-title">// Ảnh đại diện</span></div>
        <div style="padding:16px">
          <?php if(!empty($product['thumbnail_path'])): ?>
            <div style="margin-bottom:12px">
              <img src="<?=htmlspecialchars($product['thumbnail_path'])?>" alt=""
                   style="width:100%;border-radius:4px;border:1px solid var(--border);object-fit:cover;max-height:160px">
              <div style="font-size:10px;color:var(--text-3);margin-top:4px;font-family:var(--font-m)">Ảnh hiện tại</div>
            </div>
          <?php endif; ?>
          <label style="margin-bottom:6px">Tải ảnh lên (jpg, png, webp — tối đa 5MB)</label>
          <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp"
                 style="font-size:11px;padding:6px 0;background:none;border:none">
          <div style="font-size:10px;color:var(--text-3);margin-top:6px;font-family:var(--font-m)">
            Ảnh mới sẽ thay thế ảnh cũ
          </div>
        </div>
      </div>

      <!-- Nút hành động -->
      <button type="submit" class="btn btn-gold" style="width:100%;padding:13px;font-size:18px;border-radius:5px">
        <?=$isEdit?'CẬP NHẬT SẢN PHẨM':'THÊM SẢN PHẨM'?>
      </button>
      <a href="/admin/products" style="display:block;text-align:center;margin-top:10px;font-family:var(--font-m);font-size:10px;color:var(--text-3);letter-spacing:.08em">
        &larr; Quay lại danh sách
      </a>

      <!-- Thông tin bổ sung khi edit -->
      <?php if($isEdit): ?>
        <div style="margin-top:16px;background:var(--bg-panel);border:1px solid var(--border);border-radius:6px;padding:14px">
          <div style="font-family:var(--font-m);font-size:9px;color:var(--gold);letter-spacing:.14em;text-transform:uppercase;margin-bottom:10px">// Thông tin hệ thống</div>
          <div style="font-family:var(--font-m);font-size:10px;color:var(--text-3);line-height:1.8">
            ID: <?=$product['id']?><br>
            Slug: <?=htmlspecialchars($product['slug']??'')?><br>
            Tạo lúc: <?=date('d/m/Y H:i', strtotime($product['created_at']??'now'))?>
          </div>
          <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--border)">
            <form method="POST" action="/admin/products/delete/<?=$product['id']?>"
                  onsubmit="return confirm('Ẩn sản phẩm này khỏi cửa hàng?')">
              <button type="submit" class="btn btn-danger btn-sm" style="width:100%">Ẩn sản phẩm này</button>
            </form>
          </div>
        </div>
      <?php endif; ?>

    </div>
  </div>
</form>
