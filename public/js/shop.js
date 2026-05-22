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

// Search autocomplete & Voice Search
const si=document.getElementById('globalSearch');
const sd=document.getElementById('searchDropdown');
const voiceSearchBtn=document.getElementById('voiceSearchBtn');
let st=null;

si?.addEventListener('input',function(){
  clearTimeout(st);
  const q=this.value.trim();
  if(q.length<2){if(sd)sd.style.display='none';return;}
  st=setTimeout(async()=>{
    try {
      const r=await fetch(B+'/products/search?q='+encodeURIComponent(q));
      const d=await r.json();
      if((!d.results?.length) && (!d.news?.length)){if(sd)sd.style.display='none';return;}
      if(sd){
        let html = '';
        if (d.results && d.results.length) {
          html += `<div class="search-dropdown-section-title">SẢN PHẨM</div>`;
          html += d.results.slice(0,5).map(p=>{
            const imgPath = p.image_path || p.thumbnail_path || '';
            const cleanImgPath = imgPath ? imgPath.replace(/^\/?public\//, '') : '';
            const imgSrc = cleanImgPath ? B + '/' + cleanImgPath : '';
            return `
              <a href="${B}/products/detail/${p.id}" class="search-item">
                <div class="search-item-thumb">
                  ${imgSrc ? `<img src="${imgSrc}" alt="${p.name}" style="width:100%;height:100%;object-fit:cover;border-radius:4px;">` : `<div class="search-item-grade" style="width:100%;height:100%;font-size:10px">${p.grade||'?'}</div>`}
                </div>
                <div style="flex:1;min-width:0">
                  <div style="font-size:12.5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:var(--text-primary);font-weight:500;">${p.name}</div>
                  <div style="font-family:var(--font-mono);font-size:10.5px;color:var(--text-hint)">${p.grade ? p.grade + ' · ' : ''}${p.series||''}</div>
                </div>
                <div style="font-family:var(--font-display);font-size:14px;color:var(--gold);flex-shrink:0;margin-left:8px">${Number(p.price).toLocaleString('vi-VN')}đ</div>
              </a>`;
          }).join('');
        }
        
        if (d.news && d.news.length) {
          html += `<div class="search-dropdown-section-title">TIN TỨC & BÀI VIẾT</div>`;
          html += d.news.slice(0,3).map(n=>`
            <a href="${B}/news/${n.slug}" class="search-item news-search-item">
              <span style="font-size:14px;margin-right:6px">📰</span>
              <div style="flex:1;min-width:0">
                <div style="font-size:12.5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:var(--text-primary);">${n.title}</div>
                <div style="font-family:var(--font-mono);font-size:10px;color:var(--text-hint)">${new Date(n.created_at).toLocaleDateString('vi-VN')}</div>
              </div>
            </a>`).join('');
        }

        if (d.results && d.results.length) {
          html += `<a href="${B}/products?search=${encodeURIComponent(q)}" class="search-item" style="justify-content:center;color:var(--gold);font-family:var(--font-mono);font-size:11px;font-weight:bold;letter-spacing:0.05em;border-top:1px solid var(--border)">XEM TẤT CẢ KẾT QUẢ &rarr;</a>`;
        }
        sd.innerHTML = html;
        sd.style.display = 'block';
      }
    } catch(e){}
  },280);
});
document.addEventListener('click',e=>{if(!si?.contains(e.target)&&!sd?.contains(e.target)&&sd)sd.style.display='none';});
si?.addEventListener('keydown',e=>{
  if(e.key==='Enter'){if(sd)sd.style.display='none';window.location.href=B+'/products?search='+encodeURIComponent(si.value.trim());}
  if(e.key==='Escape'&&sd)sd.style.display='none';
});

// Voice Search Implementation
voiceSearchBtn?.addEventListener('click', function() {
  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
  if (!SpeechRecognition) {
    showToast('Trình duyệt không hỗ trợ tìm kiếm bằng giọng nói', true);
    return;
  }

  const recognition = new SpeechRecognition();
  recognition.lang = 'vi-VN';
  recognition.interimResults = true;
  recognition.maxAlternatives = 1;

  // Create overlay modal
  const overlay = document.createElement('div');
  overlay.className = 'voice-search-overlay';
  overlay.innerHTML = `
    <div class="voice-search-container">
      <button class="voice-search-close" type="button">&times;</button>
      <div class="voice-search-pulse-circle">
        <div class="pulse-ring"></div>
        <div class="pulse-ring2"></div>
        <div class="voice-mic-icon">🎙️</div>
      </div>
      <h3 class="voice-search-title">Đang lắng nghe...</h3>
      <p class="voice-search-status">Hãy nói tên sản phẩm bạn muốn tìm kiếm</p>
      <div class="voice-search-transcript">...</div>
      <p class="voice-search-hint">Ví dụ: "Freedom Gundam", "Kìm cắt", "HG Aerial"</p>
    </div>
  `;
  document.body.appendChild(overlay);

  // Trigger browser visual layout
  setTimeout(() => overlay.classList.add('show'), 50);

  let finalTranscript = '';
  recognition.start();

  recognition.onresult = function(event) {
    let interimTranscript = '';
    for (let i = event.resultIndex; i < event.results.length; ++i) {
      if (event.results[i].isFinal) {
        finalTranscript += event.results[i][0].transcript;
      } else {
        interimTranscript += event.results[i][0].transcript;
      }
    }
    const currentText = finalTranscript || interimTranscript;
    const transcriptEl = overlay.querySelector('.voice-search-transcript');
    if (transcriptEl && currentText) {
      transcriptEl.textContent = currentText;
      transcriptEl.style.fontStyle = 'normal';
    }
  };

  recognition.onend = function() {
    const finalVal = finalTranscript.trim();
    if (finalVal) {
      overlay.querySelector('.voice-search-title').textContent = "Đang tìm kiếm...";
      overlay.querySelector('.voice-search-status').textContent = `Đang chuyển hướng tìm kiếm cho "${finalVal}"`;
      setTimeout(() => {
        overlay.classList.remove('show');
        setTimeout(() => overlay.remove(), 300);
        if (si) {
          si.value = finalVal;
          if (sd) sd.style.display = 'none';
          window.location.href = B + '/products?search=' + encodeURIComponent(finalVal);
        }
      }, 1000);
    } else {
      overlay.querySelector('.voice-search-title').textContent = "Không nghe rõ...";
      overlay.querySelector('.voice-search-status').textContent = "Hãy thử nói lại hoặc nói to hơn";
      setTimeout(() => {
        overlay.classList.remove('show');
        setTimeout(() => overlay.remove(), 300);
      }, 2000);
    }
  };

  recognition.onerror = function(event) {
    console.error('Speech recognition error', event);
    overlay.querySelector('.voice-search-title').textContent = "Lỗi nhận dạng";
    overlay.querySelector('.voice-search-status').textContent = "Vui lòng kiểm tra micro của bạn";
    setTimeout(() => {
      overlay.classList.remove('show');
      setTimeout(() => overlay.remove(), 300);
    }, 2000);
  };

  const closeFunc = () => {
    recognition.abort();
    overlay.classList.remove('show');
    setTimeout(() => overlay.remove(), 300);
  };

  overlay.querySelector('.voice-search-close').addEventListener('click', closeFunc);
  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) {
      closeFunc();
    }
  });
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

// ==============================
// PRODUCT DETAIL
// ==============================
let detailQty = 1;
function changeQty(d, maxStock){
  detailQty = Math.max(1, Math.min(maxStock, detailQty + d));
  const display = document.getElementById('qtyDisplay');
  if(display) display.textContent = detailQty;
}
function addToCartDetail(id){ addToCart(id, detailQty); }
function switchImg(src, el){
  const mainImg = document.getElementById('mainImg');
  if(mainImg) mainImg.src = src;
  document.querySelectorAll('.thumb-item').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
}

// ==============================
// CHECKOUT
// ==============================
async function updateShipping() {
  const province = document.getElementById('provinceSelect').value;
  if (!province) return;
  try {
    const res  = await fetch(B + '/cart/shipping?province=' + encodeURIComponent(province));
    const data = await res.json();
    document.getElementById('shippingFeeDisplay').textContent = data.shipping_fee.toLocaleString('vi-VN') + 'đ';
    document.getElementById('totalDisplay').textContent = data.total.toLocaleString('vi-VN') + 'đ';
  } catch(e) {}
}

const checkoutForm = document.getElementById('checkoutForm');
if(checkoutForm){
  checkoutForm.addEventListener('submit', function(e) {
    const prov = document.getElementById('provinceSelect');
    if (!prov.value) {
      e.preventDefault();
      prov.classList.add('error');
      prov.focus();
    }
  });
}
// ==============================
// FAVORITE (WISHLIST) SYSTEM
// ==============================
async function toggleFavorite(productId, buttonElement) {
    try {
        const formData = new FormData();
        formData.append('product_id', productId);

        const res = await fetch(BASE_URL + '/favorite/toggle', {
            method: 'POST',
            body: formData
        });

        const data = await res.json();

        if (data.status === 'unauthorized') {
            alert(data.message); // Có thể thay bằng Modal/Toast UI của bạn
            window.location.href = BASE_URL + '/login';
        } else if (data.status === 'added') {
            buttonElement.classList.add('active');
            buttonElement.innerHTML = '♥'; // Tim đặc
        } else if (data.status === 'removed') {
            buttonElement.classList.remove('active');
            buttonElement.innerHTML = '♡'; // Tim rỗng
        }
    } catch (error) {
        console.error("Lỗi khi xử lý yêu thích: ", error);
    }
    
}

// ==============================
// PRODUCT COMPARISON (FLOATING COMPARE BAR)
// ==============================
document.addEventListener('DOMContentLoaded', () => {
  const compareBar = document.getElementById('compareBar');
  const compareBarItems = document.getElementById('compareBarItems');
  const compareCount = document.getElementById('compareCount');
  const btnClearCompare = document.getElementById('btnClearCompare');
  const btnCompareNow = document.getElementById('btnCompareNow');

  function updateCompareBar() {
    const ids = JSON.parse(localStorage.getItem('compare_products') || '[]');
    const names = JSON.parse(localStorage.getItem('compare_product_names') || '{}');
    const images = JSON.parse(localStorage.getItem('compare_product_images') || '{}');

    if (!compareBar) return;

    if (ids.length === 0) {
      compareBar.style.display = 'none';
      document.body.classList.remove('compare-bar-active');
      return;
    }

    compareBar.style.display = 'block';
    document.body.classList.add('compare-bar-active');
    if (compareCount) {
      compareCount.textContent = `(${ids.length}/3)`;
    }

    if (compareBarItems) {
      compareBarItems.innerHTML = ids.map(id => {
        const img = images[id] || '';
        const name = names[id] || '';
        return `
          <div class="compare-item-thumb" title="${escapeHTML(name)}">
            <img src="${escapeHTML(img)}" alt="${escapeHTML(name)}">
            <button class="compare-item-remove" data-id="${id}">✕</button>
          </div>
        `;
      }).join('');

      // Bind remove buttons
      compareBarItems.querySelectorAll('.compare-item-remove').forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.stopPropagation();
          const id = parseInt(btn.getAttribute('data-id'));
          removeProductFromComparison(id);
        });
      });
    }

    if (btnCompareNow) {
      btnCompareNow.href = `${window.BASE_URL}/products/compare?ids=${ids.join(',')}`;
    }
  }

  function removeProductFromComparison(id) {
    let ids = JSON.parse(localStorage.getItem('compare_products') || '[]');
    let names = JSON.parse(localStorage.getItem('compare_product_names') || '{}');
    let images = JSON.parse(localStorage.getItem('compare_product_images') || '{}');

    ids = ids.filter(item => item !== id);
    delete names[id];
    delete images[id];

    localStorage.setItem('compare_products', JSON.stringify(ids));
    localStorage.setItem('compare_product_names', JSON.stringify(names));
    localStorage.setItem('compare_product_images', JSON.stringify(images));

    // Dispatch custom event to sync with checkboxes on products index page
    window.dispatchEvent(new CustomEvent('compare_updated'));
    updateCompareBar();
  }

  if (btnClearCompare) {
    btnClearCompare.addEventListener('click', () => {
      localStorage.setItem('compare_products', '[]');
      localStorage.setItem('compare_product_names', '{}');
      localStorage.setItem('compare_product_images', '{}');
      window.dispatchEvent(new CustomEvent('compare_updated'));
      updateCompareBar();
    });
  }

  // Initial update
  updateCompareBar();

  // Listen for updates from other scripts/pages
  window.addEventListener('compare_updated', updateCompareBar);
});

// Helper function to escape HTML
function escapeHTML(str) {
  if (!str) return '';
  return str.replace(/[&<>'"]/g, 
    tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag] || tag)
  );
}

// ==============================
// AI CHATBOT WIDGET
// ==============================
document.addEventListener('DOMContentLoaded', () => {
  const chatbotToggle = document.getElementById('chatbotToggle');
  const chatbotWindow = document.getElementById('chatbotWindow');
  const chatbotClose = document.getElementById('chatbotClose');
  const chatbotClear = document.getElementById('chatbotClear');
  const chatbotBody = document.getElementById('chatbotBody');
  const chatbotInput = document.getElementById('chatbotInput');
  const chatbotSend = document.getElementById('chatbotSend');

  if (!chatbotToggle || !chatbotWindow) return;

  // Toggle chatbot window
  chatbotToggle.addEventListener('click', () => {
    if (chatbotWindow.style.display === 'none') {
      chatbotWindow.style.display = 'flex';
      scrollToBottom();
      chatbotInput?.focus();
    } else {
      chatbotWindow.style.display = 'none';
    }
  });

  if (chatbotClose) {
    chatbotClose.addEventListener('click', () => {
      chatbotWindow.style.display = 'none';
    });
  }

  // Quick suggestions buttons
  chatbotWindow.addEventListener('click', (e) => {
    if (e.target.classList.contains('quick-suggest-btn')) {
      const msg = e.target.getAttribute('data-msg');
      if (msg) {
        sendChatMessage(msg);
      }
    }
  });

  // Clear chatbot history (Custom Modal Confirmation)
  const confirmModal = document.getElementById('chatbotConfirmModal');
  const btnConfirmNo = document.getElementById('btnConfirmClearNo');
  const btnConfirmYes = document.getElementById('btnConfirmClearYes');

  if (chatbotClear && confirmModal && btnConfirmNo && btnConfirmYes) {
    chatbotClear.addEventListener('click', () => {
      confirmModal.style.display = 'flex';
    });

    btnConfirmNo.addEventListener('click', () => {
      confirmModal.style.display = 'none';
    });

    btnConfirmYes.addEventListener('click', async () => {
      confirmModal.style.display = 'none';
      try {
        const res = await fetch(window.BASE_URL + '/chatbot/clear');
        const d = await res.json();
        if (d.success) {
          // Restore only initial welcome message and quick suggestions
          if (chatbotBody) {
            chatbotBody.innerHTML = `
              <div class="chatbot-msg system">
                Chào mừng bạn đến với <strong>Gunpla Shop</strong>! Mình là trợ lý AI thông minh chuyên tư vấn về các mô hình Gundam (HG, RG, MG, PG) và dụng cụ lắp ráp. Bạn cần mình trợ giúp gì hôm nay?
              </div>
              <div class="chatbot-quick-suggests">
                <button class="quick-suggest-btn" data-msg="Tôi là người mới chơi thì nên lắp dòng nào?">🆕 Người mới chọn dòng nào?</button>
                <button class="quick-suggest-btn" data-msg="Tư vấn cho tôi một số mẫu HG đẹp có sẵn">🔥 Mẫu HG nổi bật</button>
                <button class="quick-suggest-btn" data-msg="Tôi cần mua dụng cụ lắp ráp gundam cơ bản">🛠️ Dụng cụ lắp ráp</button>
                <button class="quick-suggest-btn" data-msg="Giới thiệu cho tôi các dòng PG đỉnh cao">👑 Mô hình PG cao cấp</button>
              </div>
            `;
          }
          showToast('Đã xóa lịch sử trò chuyện');
        }
      } catch (e) {
        showToast('Lỗi khi xóa lịch sử trò chuyện', true);
      }
    });
  }

  // Handle enter key on input
  if (chatbotInput) {
    chatbotInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        const msg = chatbotInput.value.trim();
        if (msg) {
          sendChatMessage(msg);
        }
      }
    });
  }

  // Handle send button click
  if (chatbotSend) {
    chatbotSend.addEventListener('click', () => {
      const msg = chatbotInput.value.trim();
      if (msg) {
        sendChatMessage(msg);
      }
    });
  }

  function scrollToBottom() {
    if (chatbotBody) {
      chatbotBody.scrollTop = chatbotBody.scrollHeight;
    }
  }

  async function sendChatMessage(message) {
    if (!message || !chatbotBody) return;

    // Append user message
    const userMsgDiv = document.createElement('div');
    userMsgDiv.className = 'chatbot-msg user';
    userMsgDiv.textContent = message;
    chatbotBody.appendChild(userMsgDiv);

    // Clear input
    if (chatbotInput) {
      chatbotInput.value = '';
    }

    scrollToBottom();

    // Show loading indicator
    const loadingDiv = document.createElement('div');
    loadingDiv.className = 'chat-loading';
    loadingDiv.id = 'chatLoading';
    loadingDiv.innerHTML = '<span></span><span></span><span></span>';
    chatbotBody.appendChild(loadingDiv);
    scrollToBottom();

    try {
      const res = await fetch(window.BASE_URL + '/chatbot/message', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ message: message })
      });

      // Remove loading indicator
      const loader = document.getElementById('chatLoading');
      if (loader) loader.remove();

      const d = await res.json();
      if (d.success) {
        // Append model response (can contain HTML links)
        const sysMsgDiv = document.createElement('div');
        sysMsgDiv.className = 'chatbot-msg system';
        sysMsgDiv.innerHTML = d.response;
        chatbotBody.appendChild(sysMsgDiv);

        // Extract recommended products
        const regex = /<a\s+[^>]*href=["'](?:[^"']*\/products\/detail\/(\d+))["'][^>]*>(.*?)<\/a>/gi;
        const matches = [...d.response.matchAll(regex)];
        const recommendedProducts = [];
        const seenIds = new Set();
        for (const match of matches) {
          const id = parseInt(match[1]);
          const name = match[2].replace(/<\/?[^>]+(>|$)/g, "").trim(); // strip inner HTML tags if any
          if (!seenIds.has(id)) {
            seenIds.add(id);
            recommendedProducts.push({ id, name });
          }
        }

        if (recommendedProducts.length > 0) {
          const actionsDiv = document.createElement('div');
          actionsDiv.className = 'chatbot-recommend-actions';
          actionsDiv.innerHTML = recommendedProducts.map(p => `
            <button class="chat-quick-add-btn" onclick="addToCart(${p.id})">
              <span class="icon">🛒</span> Thêm nhanh: <strong>${escapeHTML(p.name)}</strong>
            </button>
          `).join('');
          chatbotBody.appendChild(actionsDiv);
        }
      } else {
        const errorMsgDiv = document.createElement('div');
        errorMsgDiv.className = 'chatbot-msg system';
        errorMsgDiv.style.color = 'var(--red-accent)';
        errorMsgDiv.textContent = d.message || 'Lỗi hệ thống';
        chatbotBody.appendChild(errorMsgDiv);
      }
    } catch (e) {
      // Remove loading indicator if exists
      const loader = document.getElementById('chatLoading');
      if (loader) loader.remove();

      const errorMsgDiv = document.createElement('div');
      errorMsgDiv.className = 'chatbot-msg system';
      errorMsgDiv.style.color = 'var(--red-accent)';
      errorMsgDiv.textContent = 'Lỗi kết nối mạng, vui lòng kiểm tra lại.';
      chatbotBody.appendChild(errorMsgDiv);
    }

    scrollToBottom();
  }
});

// ==============================
// MOBILE MENU TOGGLE
// ==============================
document.addEventListener('DOMContentLoaded', () => {
  const menuToggleBtn = document.getElementById('menuToggleBtn');
  const navLinks = document.querySelector('.nav-links');
  
  if (menuToggleBtn && navLinks) {
    menuToggleBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      navLinks.classList.toggle('active');
    });

    // Close when clicking outside
    document.addEventListener('click', (e) => {
      if (!navLinks.contains(e.target) && e.target !== menuToggleBtn) {
        navLinks.classList.remove('active');
      }
    });
  }
});