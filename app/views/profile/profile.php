<?php
/**
 * @var array $user
 * @var array $orders
 * @var array $favorites
 */
?>
<div class="container profile-container">
    <div class="breadcrumb">
        <a href="<?= BASE_URL ?>/">Trang chủ</a><span>/</span>Tài khoản của tôi
    </div>

    <div class="profile-layout">
        <aside class="profile-sidebar">
            <div class="profile-avatar-card">
                <div class="profile-avatar">
                    <?= mb_strtoupper(mb_substr($user['full_name'], 0, 1)) ?>
                </div>
                <div class="profile-username"><?= htmlspecialchars($user['full_name']) ?></div>
                <div class="profile-pilot-id">PILOT ID: #<?= $user['id'] ?></div>
            </div>

            <nav class="user-nav">
                <button class="user-nav-btn active" data-tab="info" onclick="switchTab('info')">Thông tin cá nhân</button>
                <button class="user-nav-btn" data-tab="orders" onclick="switchTab('orders')">Lịch sử đơn hàng</button>
                <button class="user-nav-btn" data-tab="wishlist" onclick="switchTab('wishlist')">Sản phẩm yêu thích</button>
                <button class="user-nav-btn" data-tab="policies" onclick="switchTab('policies')">Chính sách shop</button>
                <a href="<?= BASE_URL ?>/user/logout" class="user-nav-btn logout-btn">⏏ Đăng xuất</a>
            </nav>
        </aside>

        <main class="profile-main">
            <?php if (!empty($_SESSION['flash'])): ?>
                <div class="alert-success-box">
                    <?= $_SESSION['flash']['msg'] ?>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <div id="tab-info" class="user-tab-content active">
                <section class="profile-section">
                    <h3 class="profile-section-title">// CẬP NHẬT THÔNG TIN</h3>
                    <form action="<?= BASE_URL ?>/user/profileUpdate" method="POST">
                        <div class="profile-form-grid">
                            <div class="form-group">
                                <label class="form-label">Họ và tên</label>
                                <input type="text" name="full_name" class="form-input" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" name="phone" class="form-input" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                            </div>
                            <div class="form-group col-span-2">
                                <label class="form-label">Địa chỉ giao hàng mặc định</label>
                                <textarea name="address" class="form-input" rows="2"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                            </div>
                        </div>
                        
                        <h3 class="profile-section-title password-title">// ĐỔI MẬT KHẨU (Bỏ trống nếu giữ nguyên)</h3>
                        <div class="form-group max-w-300">
                            <label class="form-label">Mật khẩu mới</label>
                            <input type="password" name="new_password" class="form-input" placeholder="Tối thiểu 8 ký tự">
                            <?php if(isset($errors['new_password'])): ?>
                                <span class="form-error"><?= $errors['new_password'] ?></span>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn-primary profile-submit-btn">LƯU THAY ĐỔI</button>
                    </form>
                </section>
            </div>

            <div id="tab-orders" class="user-tab-content">
                <div class="profile-table-container table-responsive">
                    <table class="profile-table">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Ngày đặt</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $statusLables = ['pending'=>'Chờ xác nhận', 'confirmed'=>'Đã xác nhận', 'shipping'=>'Đang giao', 'delivered'=>'Đã giao', 'cancelled'=>'Đã hủy'];
                            if(empty($orders)): ?>
                                <tr><td colspan="5" class="profile-table-empty">Bạn chưa có đơn hàng nào.</td></tr>
                            <?php else: foreach($orders as $o): ?>
                                <tr>
                                    <td class="order-id">#<?= $o['id'] ?></td>
                                    <td class="order-date"><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
                                    <td class="order-total"><?= number_format($o['total'], 0, ',', '.') ?>đ</td>
                                    <td>
                                        <span class="badge badge-<?= $o['status'] ?>"><?= $statusLables[$o['status']] ?? $o['status'] ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= BASE_URL ?>/orders/detail/<?= $o['id'] ?>" class="btn-ghost order-btn">Chi tiết</a>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="tab-wishlist" class="user-tab-content">
                <?php if(empty($favorites)): ?>
                    <div class="profile-empty-state">
                        <div class="empty-icon">♡</div>
                        Bạn chưa yêu thích sản phẩm nào.
                    </div>
                <?php else: ?>
                    <div class="product-grid profile-wishlist-grid">
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
                <div class="profile-policies-card">
                    <h2 class="policies-title">CHÍNH SÁCH CỬA HÀNG</h2>
                    <ul class="policies-list">
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