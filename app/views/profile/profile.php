<div class="container" style="padding: 40px 24px;">
    <div style="display: grid; grid-template-columns: 280px 1fr; gap: 32px;">
        
        <aside style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 8px; padding: 20px;">
            <div style="text-align: center; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid var(--border);">
                <div style="width: 64px; height: 64px; background: var(--gold); border-radius: 50%; margin: 0 auto 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: var(--bg-void);">
                    <?= substr($user['full_name'], 0, 1) ?>
                </div>
                <div style="font-family: var(--font-display); font-size: 18px; color: var(--gold);"><?= htmlspecialchars($user['full_name']) ?></div>
                <div style="font-family: var(--font-mono); font-size: 10px; color: var(--text-hint);">PILOT ID: #<?= $user['id'] ?></div>
            </div>

            <nav class="user-nav">
                <button class="user-nav-btn active" onclick="showTab('info')">Thông tin cá nhân</button>
                <button class="user-nav-btn" onclick="showTab('orders')">Lịch sử đơn hàng</button>
                <button class="user-nav-btn" onclick="showTab('wishlist')">Sản phẩm yêu thích</button>
                <button class="user-nav-btn" onclick="showTab('policies')">Chính sách shop</button>
            </nav>
        </aside>

        <main>
            <div id="tab-info" class="user-tab-content active">
                <section style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 8px; padding: 32px; margin-bottom: 24px;">
                    <h3 style="font-family: var(--font-mono); color: var(--gold); margin-bottom: 24px;">// CẬP NHẬT THÔNG TIN</h3>
                    <form action="<?= BASE_URL ?>/user/profileUpdate" method="POST">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label class="form-label">Họ và tên</label>
                                <input type="text" name="full_name" class="form-input" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" name="phone" class="form-input" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                            </div>
                            <div class="form-group" style="grid-column: 1/-1;">
                                <label class="form-label">Địa chỉ giao hàng</label>
                                <textarea name="address" class="form-input" rows="2"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                            </div>
                        </div>
                        
                        <h3 style="font-family: var(--font-mono); color: var(--gold); margin: 32px 0 20px;">// ĐỔI MẬT KHẨU (BỎ TRỐNG NẾU GIỮ NGUYÊN)</h3>
                        <div class="form-group">
                            <label class="form-label">Mật khẩu mới</label>
                            <input type="password" name="new_password" class="form-input" placeholder="Tối thiểu 8 ký tự">
                        </div>

                        <button type="submit" class="btn-primary" style="margin-top: 24px; padding: 12px 32px;">LƯU THAY ĐỔI</button>
                    </form>
                </section>
            </div>

            <div id="tab-orders" class="user-tab-content">
                <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 8px; overflow: hidden;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background: var(--bg-surface);">
                            <tr>
                                <th class="u-th">Mã đơn</th>
                                <th class="u-th">Ngày đặt</th>
                                <th class="u-th">Tổng tiền</th>
                                <th class="u-th">Trạng thái</th>
                                <th class="u-th"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($orders)): ?>
                                <tr><td colspan="5" style="padding: 40px; text-align: center; color: var(--text-hint);">Bạn chưa có đơn hàng nào.</td></tr>
                            <?php else: foreach($orders as $o): ?>
                                <tr style="border-bottom: 1px solid var(--border);">
                                    <td class="u-td">#<?= $o['id'] ?></td>
                                    <td class="u-td"><?= date('d/m/Y', strtotime($o['created_at'])) ?></td>
                                    <td class="u-td" style="color: var(--gold);"><?= number_format($o['total'], 0, ',', '.') ?>đ</td>
                                    <td class="u-td"><span class="badge badge-<?= $o['status'] ?>"><?= $o['status'] ?></span></td>
                                    <td class="u-td"><a href="<?= BASE_URL ?>/orders/detail/<?= $o['id'] ?>" class="btn-icon">👁</a></td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="tab-wishlist" class="user-tab-content">
                <div class="product-grid" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">
                    <?php if(empty($favorites)): ?>
                        <div style="grid-column: 1/-1; padding: 40px; text-align: center; color: var(--text-hint);">Danh sách trống.</div>
                    <?php else: foreach($favorites as $p): ?>
                        <div class="product-card">
                             <img src="<?= BASE_URL ?>/<?= $p['thumbnail'] ?>" style="width:100%">
                             <div class="product-info">
                                <div class="product-name"><?= $p['name'] ?></div>
                                <button class="btn-wish active" onclick="toggleFavorite(<?= $p['id'] ?>, this)">♥</button>
                             </div>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <div id="tab-policies" class="user-tab-content">
                <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 8px; padding: 32px; color: var(--text-secondary);">
                    <h2 style="color: var(--gold); margin-bottom: 20px;">CHÍNH SÁCH CỬA HÀNG</h2>
                    <p>1. <strong>Đổi trả:</strong> Trong vòng 7 ngày nếu lỗi do nhà sản xuất (gãy runner, thiếu part nhựa).</p>
                    <p>2. <strong>Vận chuyển:</strong> Miễn phí cho đơn hàng từ 500k tại Bình Dương & TP.HCM.</p>
                    <p>3. <strong>Bảo mật:</strong> Thông tin cá nhân của Pilot được mã hóa và bảo vệ tuyệt đối.</p>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
    .user-nav-btn { width: 100%; text-align: left; padding: 12px 16px; background: none; border: none; color: var(--text-secondary); cursor: pointer; font-family: var(--font-mono); font-size: 12px; transition: 0.3s; border-radius: 4px; margin-bottom: 4px; }
    .user-nav-btn:hover { background: var(--bg-hover); color: var(--gold); }
    .user-nav-btn.active { background: rgba(200, 168, 90, 0.1); color: var(--gold); border-left: 3px solid var(--gold); }
    .user-tab-content { display: none; }
    .user-tab-content.active { display: block; animation: fadeIn 0.4s; }
    .u-th { padding: 12px 16px; text-align: left; font-size: 11px; color: var(--text-hint); text-transform: uppercase; }
    .u-td { padding: 16px; font-size: 13px; }
</style>

<script>
    function showTab(tabId) {
        document.querySelectorAll('.user-tab-content').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.user-nav-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + tabId).classList.add('active');
        event.currentTarget.classList.add('active');
    }
</script>