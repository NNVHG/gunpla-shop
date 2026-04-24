<?php
/**
 * app/views/admin/inventory/index.php
 * Biến: $products, $hasLogTable
 */
?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
  <div>
    <div style="font-family:var(--font-m);font-size:10px;color:var(--text-2)">
      Quản lý tồn kho — click vào số lượng để điều chỉnh nhanh
    </div>
  </div>
  <a href="/admin/products/create" class="btn btn-gold">+ Thêm sản phẩm mới</a>
</div>

<div class="admin-table-wrap">
  <div class="admin-table-head">
    <span class="admin-table-title">Tồn kho toàn bộ sản phẩm</span>
    <div style="display:flex;align-items:center;gap:8px">
      <input type="text" id="stockSearch" placeholder="Tìm sản phẩm..."
             style="padding:5px 10px;font-size:11px;width:200px" oninput="filterTable(this.value)">
    </div>
  </div>
  <table id="inventoryTable">
    <thead>
      <tr>
        <th>Sản phẩm</th>
        <th>Grade</th>
        <th>Scale</th>
        <th>Giá bán</th>
        <th style="width:200px">Tồn kho</th>
        <th>Điều chỉnh nhanh</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $p): ?>
        <?php
          $pct   = min(100, $p['stock'] > 0 ? ($p['stock'] / 30) * 100 : 0);
          $color = $p['stock'] === 0 ? 'var(--red)' : ($p['stock'] <= 5 ? 'var(--amber)' : 'var(--green)');
        ?>
        <tr id="inv-row-<?= $p['id'] ?>" data-name="<?= strtolower(htmlspecialchars($p['name'])) ?>">
          <td>
            <div style="font-size:12px;font-weight:500;max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
              <?= htmlspecialchars($p['name']) ?>
            </div>
            <div style="font-family:var(--font-m);font-size:9px;color:var(--text-3)"><?= htmlspecialchars($p['series'] ?? '') ?></div>
          </td>
          <td><span class="badge badge-confirmed" style="font-size:9px"><?= htmlspecialchars($p['grade']) ?></span></td>
          <td style="font-family:var(--font-m);font-size:11px;color:var(--text-2)"><?= htmlspecialchars($p['scale'] ?? '—') ?></td>
          <td style="font-family:var(--font-d);font-size:15px;color:var(--gold)"><?= number_format($p['price'],0,',','.') ?>đ</td>
          <td>
            <div class="stock-bar-wrap">
              <div class="stock-bar" style="height:6px">
                <div class="stock-bar-fill" id="bar-<?= $p['id'] ?>" style="width:<?= $pct ?>%;background:<?= $color ?>"></div>
              </div>
              <span class="stock-num" id="stock-num-<?= $p['id'] ?>" style="color:<?= $color ?>;font-family:var(--font-m);font-size:12px">
                <?= $p['stock'] === 0 ? 'Hết' : $p['stock'] ?>
              </span>
            </div>
          </td>
          <td>
            <div style="display:flex;align-items:center;gap:6px">
              <!-- Trừ kho -->
              <button class="btn btn-sm btn-danger" title="Xuất kho"
                      onclick="adjustStock(<?= $p['id'] ?>, -1, this)">−1</button>
              <!-- Nhập kho nhanh -->
              <?php foreach ([10, 20, 50] as $qty): ?>
                <button class="btn btn-sm" style="color:var(--green);border-color:rgba(58,158,106,0.3)"
                        onclick="adjustStock(<?= $p['id'] ?>, <?= $qty ?>, this)" title="Nhập thêm <?= $qty ?>">
                  +<?= $qty ?>
                </button>
              <?php endforeach; ?>
              <!-- Nhập số tùy chọn -->
              <button class="btn btn-sm btn-gold" onclick="openAdjustModal(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['name'])) ?>', <?= $p['stock'] ?>)">
                ±N
              </button>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Modal điều chỉnh kho tùy chọn -->
<div id="adjustModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:200;align-items:center;justify-content:center">
  <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:8px;padding:28px;width:380px">
    <h3 style="font-family:var(--font-d);font-size:20px;letter-spacing:0.08em;margin-bottom:6px" id="modalTitle"></h3>
    <div style="font-family:var(--font-m);font-size:10px;color:var(--text-2);margin-bottom:20px">
      Tồn kho hiện tại: <span id="modalCurrentStock" style="color:var(--gold)"></span>
    </div>
    <div style="margin-bottom:14px">
      <label>Số lượng (+ nhập kho / − xuất kho)</label>
      <input type="number" id="modalDelta" placeholder="Ví dụ: 20 hoặc -5" style="margin-top:6px">
    </div>
    <div style="margin-bottom:20px">
      <label>Lý do (tùy chọn)</label>
      <input type="text" id="modalReason" placeholder="Nhập hàng từ nhà cung cấp..." style="margin-top:6px">
    </div>
    <div style="display:flex;gap:10px;justify-content:flex-end">
      <button class="btn" onclick="closeModal()">Hủy</button>
      <button class="btn btn-gold" onclick="submitAdjust()">Xác nhận</button>
    </div>
  </div>
</div>

<script>
let modalProductId = null;

function filterTable(q) {
  document.querySelectorAll('#inventoryTable tbody tr').forEach(tr => {
    tr.style.display = tr.dataset.name.includes(q.toLowerCase()) ? '' : 'none';
  });
}

async function adjustStock(productId, delta, btn) {
  btn.disabled = true;
  const res  = await fetch('/admin/inventory/adjust', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: `product_id=${productId}&delta=${delta}&reason=Điều chỉnh nhanh`
  });
  const data = await res.json();
  btn.disabled = false;

  if (data.success) {
    updateStockUI(productId, data.new_stock);
    showToast(data.message);
  } else {
    showToast(data.message, true);
  }
}

function updateStockUI(productId, newStock) {
  const pct   = Math.min(100, newStock > 0 ? (newStock / 30) * 100 : 0);
  const color = newStock === 0 ? 'var(--red)' : (newStock <= 5 ? 'var(--amber)' : 'var(--green)');
  const bar   = document.getElementById('bar-' + productId);
  const num   = document.getElementById('stock-num-' + productId);
  if (bar) { bar.style.width = pct + '%'; bar.style.background = color; }
  if (num) { num.textContent = newStock === 0 ? 'Hết' : newStock; num.style.color = color; }
}

function openAdjustModal(id, name, stock) {
  modalProductId = id;
  document.getElementById('modalTitle').textContent = name;
  document.getElementById('modalCurrentStock').textContent = stock;
  document.getElementById('modalDelta').value = '';
  document.getElementById('modalReason').value = '';
  document.getElementById('adjustModal').style.display = 'flex';
  document.getElementById('modalDelta').focus();
}

function closeModal() {
  document.getElementById('adjustModal').style.display = 'none';
}

async function submitAdjust() {
  const delta  = parseInt(document.getElementById('modalDelta').value);
  const reason = document.getElementById('modalReason').value;
  if (!delta || isNaN(delta)) { alert('Vui lòng nhập số lượng'); return; }

  const res  = await fetch('/admin/inventory/adjust', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: `product_id=${modalProductId}&delta=${delta}&reason=${encodeURIComponent(reason)}`
  });
  const data = await res.json();
  closeModal();
  if (data.success) {
    updateStockUI(modalProductId, data.new_stock);
    showToast(data.message);
  } else {
    showToast(data.message, true);
  }
}

// Toast thông báo nhỏ ở góc dưới phải
function showToast(msg, isError = false) {
  const t = document.createElement('div');
  t.style.cssText = `position:fixed;bottom:24px;right:24px;z-index:999;padding:10px 18px;border-radius:5px;
    font-family:var(--font-m);font-size:11px;letter-spacing:0.08em;animation:fadeIn .2s ease;
    background:${isError ? 'rgba(200,64,64,0.15)' : 'rgba(58,158,106,0.15)'};
    border:1px solid ${isError ? 'rgba(200,64,64,0.4)' : 'rgba(58,158,106,0.4)'};
    color:${isError ? '#e07070' : '#5cba88'}`;
  t.textContent = msg;
  document.body.appendChild(t);
  setTimeout(() => t.remove(), 2800);
}

// Đóng modal khi click bên ngoài
document.getElementById('adjustModal').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});
</script>
