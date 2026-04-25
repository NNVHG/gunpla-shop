// ==============================
// ADMIN JAVASCRIPT
// ==============================

// 1. CHỨC NĂNG KHO (INVENTORY)
let modalProductId = null;
function filterTable(q) {
  document.querySelectorAll('#inventoryTable tbody tr').forEach(tr => {
    tr.style.display = tr.dataset.name.includes(q.toLowerCase()) ? '' : 'none';
  });
}

async function adjustStock(productId, delta, btn) {
  btn.disabled = true;
  try {
    const res  = await fetch('/gunpla-shop/admin/inventory/adjust', {
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
  } catch(e) { btn.disabled = false; }
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

  try {
    const res  = await fetch('/gunpla-shop/admin/inventory/adjust', {
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
  } catch(e) {}
}

const adjustModal = document.getElementById('adjustModal');
if(adjustModal) {
    adjustModal.addEventListener('click', function(e) {
      if (e.target === this) closeModal();
    });
}

// 2. CHỨC NĂNG ĐƠN HÀNG (ORDERS)
const statusLabels = {
  pending:'Chờ xác nhận', confirmed:'Đã xác nhận',
  shipping:'Đang giao', delivered:'Đã giao', cancelled:'Đã hủy'
};
async function updateStatus(orderId, status, btn) {
  if (status === 'cancelled' && !confirm('Xác nhận hủy đơn hàng #' + orderId + '?')) return;
  btn.disabled = true; btn.textContent = '...';

  try {
    const res = await fetch('/gunpla-shop/admin/orders/status', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `order_id=${orderId}&status=${status}`
    });
    const data = await res.json();

    if (data.success) {
      const badge = document.getElementById('badge-' + orderId);
      badge.className = 'badge badge-' + status;
      badge.textContent = statusLabels[status];
      
      const row = document.getElementById('row-' + orderId);
      const cell = row.querySelector('td:last-child div');
      cell.innerHTML = '';
      if (status === 'confirmed') {
        cell.innerHTML = `<button class="btn btn-gold btn-sm" onclick="updateStatus(${orderId},'shipping',this)">Giao hàng</button>
          <button class="btn btn-danger btn-sm" onclick="updateStatus(${orderId},'cancelled',this)">Hủy</button>`;
      } else if (status === 'shipping') {
        cell.innerHTML = `<button class="btn btn-gold btn-sm" onclick="updateStatus(${orderId},'delivered',this)">Đã nhận</button>
          <button class="btn btn-danger btn-sm" onclick="updateStatus(${orderId},'cancelled',this)">Hủy</button>`;
      }
    } else {
      btn.disabled = false; btn.textContent = '!';
      alert('Cập nhật thất bại');
    }
  } catch(e) { btn.disabled = false; btn.textContent = '!'; }
}

// 3. Tiện ích Toast
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