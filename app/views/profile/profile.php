<div class="container" style="padding: 40px 24px; min-height: 70vh;">
    <div class="breadcrumb" style="margin-bottom: 24px;">
        <a href="<?= BASE_URL ?>/">Trang chủ</a><span>/</span>Tài khoản của tôi
    </div>

    <div style="display: grid; grid-template-columns: 260px 1fr; gap: 32px;">
        <aside style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 8px; padding: 20px; align-self: start;">
            <div style="text-align: center; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid var(--border);">
                <div style="width: 64px; height: 64px; background: var(--gold); border-radius: 50%; margin: 0 auto 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: var(--bg-void); font-family: var(--font-display);">
                    <?= mb_strtoupper(mb_substr($user['full_name'], 0, 1)) ?>
                </div>
                <div style="font-family: var(--font-display); font-size: 18px; color: var(--gold);"><?= htmlspecialchars($user['full_name']) ?></div>
                <div style="font-family: var(--font-mono); font-size: 10px; color: var(--text-hint);">PILOT ID: #<?= $user['id'] ?></div>
            </div>

            <nav class="user-nav">
                <button class="user-nav-btn active" data-tab="info" onclick="switchTab('info')">Thông tin cá nhân</button>
                <button class="user-nav-btn" data-tab="orders" onclick="switchTab('orders')">Lịch sử đơn hàng</button>
                <button class="user-nav-btn" data-tab="wishlist" onclick="switchTab('wishlist')">Sản phẩm yêu thích</button>
                <button class="user-nav-btn" data-tab="policies" onclick="switchTab('policies')">Chính sách shop</button>
                <a href="<?= BASE_URL ?>/user/logout" class="user-nav-btn" style="display: block; text-align: left; margin-top: 10px; color: var(--red-accent);">⏏ Đăng xuất</a>
            </nav>
        </aside>

        <main>
            <?php if (!empty($_SESSION['flash'])): ?>
                <div style="background: rgba(58,158,106,.15); border: 1px solid var(--green); color: var(--green); padding: 12px; border-radius: 6px; margin-bottom: 20px; font-family: var(--font-mono);">
                    <?= $_SESSION['flash']['msg'] ?>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <div id="tab-info" class="user-tab-content active">
                <section style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 8px; padding: 32px; margin-bottom: 24px;">
                    <h3 style="font-family: var(--font-mono); color: var(--gold); margin-bottom: 24px; font-size: 14px;">// CẬP NHẬT THÔNG TIN</h3>
                    <form action="<?= BASE_URL ?>/user/profileUpdate" method="POST">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div style="display:flex; flex-direction:column; gap:5px">
                                <label style="font-family:var(--font-mono); font-size:11px; color:var(--text-hint);">Họ và tên</label>
                                <input type="text" name="full_name" class="form-input" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                            </div>
                            <div style="display:flex; flex-direction:column; gap:5px">
                                <label style="font-family:var(--font-mono); font-size:11px; color:var(--text-hint);">Số điện thoại</label>
                                <input type="text" name="phone" class="form-input" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                            </div>
                            <div style="grid-column: 1/-1; display:flex; flex-direction:column; gap:5px">
                                <label style="font-family:var(--font-mono); font-size:11px; color:var(--text-hint);">Địa chỉ giao hàng mặc định</label>
                                <textarea name="address" class="form-input" rows="2"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                            </div>
                        </div>
                        
                        <h3 style="font-family: var(--font-mono); color: var(--gold); margin: 32px 0 20px; font-size: 14px;">// ĐỔI MẬT KHẨU (Bỏ trống nếu giữ nguyên)</h3>
                        <div style="display:flex; flex-direction:column; gap:5px; max-width: 300px;">
                            <label style="font-family:var(--font-mono); font-size:11px; color:var(--text-hint);">Mật khẩu mới</label>
                            <input type="password" name="new_password" class="form-input" placeholder="Tối thiểu 8 ký tự">
                            <?php if(isset($errors['new_password'])): ?>
                                <span style="color:var(--red-accent); font-size: 11px;"><?= $errors['new_password'] ?></span>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn-primary" style="margin-top: 24px; padding: 12px 32px;">LƯU THAY ĐỔI</button>
                    </form>
                </section>
            </div>

            <div id="tab-orders" class="user-tab-content">
                <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 8px; overflow: hidden;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead style="background: var(--bg-surface);">
                            <tr>
                                <th style="padding: 14px 20px; font-family:var(--font-mono); font-size:10px; color:var(--text-hint);">Mã đơn</th>
                                <th style="padding: 14px 20px; font-family:var(--font-mono); font-size:10px; color:var(--text-hint);">Ngày đặt</th>
                                <th style="padding: 14px 20px; font-family:var(--font-mono); font-size:10px; color:var(--text-hint);">Tổng tiền</th>
                                <th style="padding: 14px 20px; font-family:var(--font-mono); font-size:10px; color:var(--text-hint);">Trạng thái</th>
                                <th style="padding: 14px 20px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $statusLables = ['pending'=>'Chờ xác nhận', 'confirmed'=>'Đã xác nhận', 'shipping'=>'Đang giao', 'delivered'=>'Đã giao', 'cancelled'=>'Đã hủy'];
                            if(empty($orders)): ?>
                                <tr><td colspan="5" style="padding: 40px; text-align: center; color: var(--text-hint);">Bạn chưa có đơn hàng nào.</td></tr>
                            <?php else: foreach($orders as $o): ?>
                                <tr style="border-bottom: 1px solid var(--border);">
                                    <td style="padding: 16px 20px; font-family:var(--font-mono);">#<?= $o['id'] ?></td>
                                    <td style="padding: 16px 20px; font-size:12px; color:var(--text-secondary);"><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
                                    <td style="padding: 16px 20px; color: var(--gold); font-weight: bold;"><?= number_format($o['total'], 0, ',', '.') ?>đ</td>
                                    <td style="padding: 16px 20px;">
                                        <span class="badge badge-<?= $o['status'] ?>"><?= $statusLables[$o['status']] ?? $o['status'] ?></span>
                                    </td>
                                    <td style="padding: 16px 20px;">
                                        <a href="<?= BASE_URL ?>/orders/detail/<?= $o['id'] ?>" class="btn-ghost" style="padding: 6px 12px; font-size: 10px;">Chi tiết</a>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="tab-wishlist" class="user-tab-content">
                <?php if(empty($favorites)): ?>
                    <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 8px; padding: 60px; text-align: center; color: var(--text-hint);">
                        <div style="font-size: 40px; margin-bottom: 10px; opacity:0.5;">♡</div>
                        Bạn chưa yêu thích sản phẩm nào.
                    </div>
                <?php else: ?>
                    <div class="product-grid" style="grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px;">
                        <?php foreach($favorites as $p): ?>
                            <div class="product-card" onclick="window.location='<?= BASE_URL ?>/products/detail/<?=$p['id']?>'">
                                <div class="product-img-wrap">
                                    <?php if(!empty($p['thumbnail'])): ?>
                                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($p['thumbnail']) ?>" alt="">
                                    <?php else: ?>
                                        <div class="img-placeholder"><?= htmlspecialchars($p['grade'] ?? '?') ?></div>
                                    <?php endif; ?>
                                    <div class="quick-add">
                                        <button class="btn-add" onclick="event.stopPropagation();addToCart(<?=$p['id']?>)">+ GIỎ HÀNG</button>
                                        <button class="btn-wish active" onclick="event.stopPropagation(); toggleFavorite(<?= $p['id'] ?>, this)">♥</button>
                                    </div>
                                </div>
                                <div class="product-info">
                                    <div class="product-series"><?= htmlspecialchars($p['series'] ?? '') ?></div>
                                    <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
                                    <div class="product-price-row">
                                        <span class="product-price"><?= number_format($p['price'], 0, ',', '.') ?>đ</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div id="tab-policies" class="user-tab-content">
                <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 8px; padding: 32px; color: var(--text-secondary); line-height: 1.8;">
                    <h2 style="color: var(--gold); margin-bottom: 20px; font-family: var(--font-display); letter-spacing: 0.05em;">CHÍNH SÁCH CỬA HÀNG</h2>
                    <ul style="padding-left: 20px; list-style-type: square; display:flex; flex-direction:column; gap:12px;">
                        <li><strong>Chính sách đổi trả:</strong> Hỗ trợ đổi trả trong vòng 7 ngày nếu lỗi do nhà sản xuất (gãy runner, thiếu part nhựa) với điều kiện seal bọc runner chưa bị xé.</li>
                        <li><strong>Chính sách vận chuyển:</strong> Miễn phí vận chuyển cho đơn hàng từ 500.000đ tại khu vực Bình Dương & TP.HCM.</li>
                        <li><strong>Bảo mật thông tin:</strong> Toàn bộ thông tin cá nhân và lịch sử giao dịch của bạn được mã hóa an toàn và không bao giờ chia sẻ cho bên thứ 3.</li>
                        <li><strong>Thành viên Pilot:</strong> Mọi khách hàng đăng ký tài khoản đều được tích lũy hạng thành viên để nhận mã giảm giá đặc biệt vào ngày sinh nhật.</li>
                    </ul>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
    .user-nav-btn { width: 100%; text-align: left; padding: 12px 16px; background: none; border: none; color: var(--text-secondary); cursor: pointer; font-family: var(--font-mono); font-size: 12px; transition: 0.3s; border-radius: 4px; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.05em; }
    .user-nav-btn:hover { background: var(--bg-hover); color: var(--text-primary); }
    .user-nav-btn.active { background: rgba(200, 168, 90, 0.1); color: var(--gold); border-left: 3px solid var(--gold); font-weight: bold; }
    .user-tab-content { display: none; }
    .user-tab-content.active { display: block; animation: fadeIn 0.4s; }
</style>

<script>
    // Hàm chuyển Tab hiển thị & cập nhật URL không load lại trang
    function switchTab(tabId) {
        document.querySelectorAll('.user-tab-content').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.user-nav-btn').forEach(b => b.classList.remove('active'));
        
        document.getElementById('tab-' + tabId).classList.add('active');
        document.querySelector(`.user-nav-btn[data-tab="${tabId}"]`).classList.add('active');
        
        window.history.replaceState({}, '', '?tab=' + tabId);
    }

    // Đọc tham số `?tab=` khi vừa vào trang (từ email hoặc Header)
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const currentTab = urlParams.get('tab') || 'info';
        switchTab(currentTab);
    });
</script>