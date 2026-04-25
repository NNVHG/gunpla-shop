// public/js/shop.js
// BASE_URL được inject từ PHP trong main.php: window.BASE_URL = '/gunpla-shop'
const B = window.BASE_URL || '';

let cart = window.__CART__ || [];
function fmt(n){ return Number(n).toLocaleString('vi-VN')+'đ'; }

function renderCart(){
  const body=document.getElementById('cartBody');
  const badge=document.getElementById('cartBadge');
  const total=document.getElementById('cartTotal');
  if(!body)return;
  const qty=cart.reduce((s,i)=>s+i.qty,0);
  const amt=cart.reduce((s,i)=>s+i.price*i.qty,0);
  if(badge)badge.textContent=qty||'';
  if(total)total.textContent=fmt(amt);
  if(!cart.length){
    body.innerHTML='<div class="cart-empty"><div style="font-size:32px;margin-bottom:12px;opacity:.3">&#9635;</div>Giỏ hàng đang trống</div>';
    return;
  }
  body.innerHTML=cart.map(i=>`
    <div class="cart-item">
      <div class="cart-item-thumb">${i.grade||'?'}</div>
      <div style="flex:1;min-width:0">
        <div style="font-size:12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:3px">${i.name}</div>
        <div style="font-family:var(--font-mono);font-size:10px;color:var(--text-hint)">${i.scale||''}</div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:8px">
          <div style="display:flex;align-items:center;gap:8px">
            <button class="qty-btn" onclick="cartUpdate(${i.id},${i.qty-1})">−</button>
            <span style="font-family:var(--font-mono);font-size:12px;min-width:18px;text-align:center">${i.qty}</span>
            <button class="qty-btn" onclick="cartUpdate(${i.id},${i.qty+1})">+</button>
          </div>
          <span style="font-family:var(--font-display);font-size:16px;color:var(--gold)">${fmt(i.price*i.qty)}</span>
        </div>
      </div>
    </div>`).join('');
}

async function addToCart(pid,qty=1){
  try {
    const res=await fetch(B+'/cart/add',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`product_id=${pid}&qty=${qty}`});
    const d=await res.json();
    if(d.success){
      const existing=cart.find(x=>x.id==pid);
      const newQty=(existing?.qty||0)+qty;
      const r2=await fetch(B+'/cart/update',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`product_id=${pid}&qty=${newQty}`});
      const d2=await r2.json();
      if(d2.items)cart=d2.items;
      renderCart(); openCart(); showToast('Đã thêm vào giỏ hàng');
    }else{showToast(d.message||'Lỗi',true);}
  } catch(e){ showToast('Lỗi kết nối',true); }
}

async function cartUpdate(pid,qty){
  try {
    const res=await fetch(B+'/cart/update',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`product_id=${pid}&qty=${qty}`});
    const d=await res.json();
    if(d.success){cart=d.items||[];renderCart();}
  } catch(e){}
}

function openCart(){document.getElementById('cartSidebar')?.classList.add('open');document.getElementById('cartOverlay')?.classList.add('open');}
function closeCart(){document.getElementById('cartSidebar')?.classList.remove('open');document.getElementById('cartOverlay')?.classList.remove('open');}

document.getElementById('cartBtn')?.addEventListener('click',()=>{renderCart();openCart();});
document.getElementById('cartClose')?.addEventListener('click',closeCart);
document.getElementById('cartOverlay')?.addEventListener('click',closeCart);
renderCart();

// Search autocomplete
const si=document.getElementById('globalSearch');
const sd=document.getElementById('searchDropdown');
let st=null;
si?.addEventListener('input',function(){
  clearTimeout(st);
  const q=this.value.trim();
  if(q.length<2){if(sd)sd.style.display='none';return;}
  st=setTimeout(async()=>{
    try {
      const r=await fetch(B+'/products/search?q='+encodeURIComponent(q));
      const d=await r.json();
      if(!d.results?.length){if(sd)sd.style.display='none';return;}
      if(sd){
        sd.innerHTML=d.results.slice(0,5).map(p=>`
          <a href="${B}/products/detail/${p.id}" class="search-item">
            <div class="search-item-grade">${p.grade||'?'}</div>
            <div style="flex:1;min-width:0">
              <div style="font-size:12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${p.name}</div>
              <div style="font-family:var(--font-mono);font-size:10px;color:var(--text-hint)">${p.series||''}</div>
            </div>
            <div style="font-family:var(--font-display);font-size:15px;color:var(--gold);flex-shrink:0;margin-left:8px">${Number(p.price).toLocaleString('vi-VN')}đ</div>
          </a>`).join('')
          +`<a href="${B}/products?search=${encodeURIComponent(q)}" class="search-item" style="justify-content:center;color:var(--text-hint);font-family:var(--font-mono);font-size:11px">Xem tất cả &rarr;</a>`;
        sd.style.display='block';
      }
    } catch(e){}
  },280);
});
document.addEventListener('click',e=>{if(!si?.contains(e.target)&&!sd?.contains(e.target)&&sd)sd.style.display='none';});
si?.addEventListener('keydown',e=>{
  if(e.key==='Enter'){if(sd)sd.style.display='none';window.location.href=B+'/products?search='+encodeURIComponent(si.value.trim());}
  if(e.key==='Escape'&&sd)sd.style.display='none';
});

// Toast
function showToast(msg,err=false){
  const t=document.createElement('div');
  t.className='toast'+(err?' toast-error':'');
  t.textContent=msg;
  document.body.appendChild(t);
  requestAnimationFrame(()=>t.classList.add('show'));
  setTimeout(()=>{t.classList.remove('show');setTimeout(()=>t.remove(),300);},2500);
}

// Filter chips
document.querySelectorAll('.filter-chip').forEach(c=>{
  c.addEventListener('click',function(){
    const g=this.dataset.group;
    if(g)document.querySelectorAll(`.filter-chip[data-group="${g}"]`).forEach(x=>x.classList.remove('active'));
    else document.querySelectorAll('.filter-chip:not([data-group])').forEach(x=>x.classList.remove('active'));
    this.classList.add('active');
    const p=new URLSearchParams(window.location.search);
    const k=this.dataset.filterKey, v=this.dataset.filterVal;
    if(k&&v&&v!=='all')p.set(k,v); else if(k)p.delete(k);
    p.delete('page');
    window.location.href=B+'/products?'+p.toString();
  });
});
document.querySelector('.sort-select')?.addEventListener('change',function(){
  const p=new URLSearchParams(window.location.search);
  p.set('sort',this.value);p.delete('page');
  window.location.href=B+'/products?'+p.toString();
});