<?php

/**
 * Chi tiết sản phẩm
 * @var array  $product       Thông tin sản phẩm (kèm images[])
 * @var array  $related       Sản phẩm liên quan
 * @var array  $reviews       Danh sách đánh giá đã duyệt
 * @var float  $avgRating     Điểm trung bình (0.0 – 5.0)
 * @var int    $totalReviews  Tổng số đánh giá
 * @var bool   $hasReviewed   User hiện tại đã đánh giá chưa
 */
$p      = $product;
$images = $p['images'] ?? [];
$stock  = (int) $p['stock'];
$stockClass = $stock === 0 ? 'out' : ($stock <= 5 ? 'low' : 'ok');
$stockText  = $stock === 0 ? 'Hết hàng' : ($stock <= 5 ? "Còn $stock sản phẩm" : "Còn hàng");
?>
<div class="container">
  <div class="breadcrumb">
    <a href="<?= BASE_URL ?>/">Trang chủ</a><span>/</span>
    <a href="<?= BASE_URL ?>/products">Sản phẩm</a><span>/</span>
    <?= htmlspecialchars($p['name']) ?>
  </div>
  <div class="product-detail-grid">
    <div class="detail-images">
      <div class="main-img" id="mainImgWrap">
        <?php if (!empty($images)): ?>
          <img src="<?= htmlspecialchars($images[0]['image_path']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" id="mainImg">
        <?php else: ?>
          <div class="img-placeholder"><?= htmlspecialchars($p['grade'] ?? '?') ?></div>
        <?php endif; ?>
      </div>
      <?php if (count($images) > 1): ?>
        <div class="thumb-strip">
          <?php foreach ($images as $i => $img): ?>
            <div class="thumb-item<?= $i === 0 ? ' active' : '' ?>" onclick="switchImg('<?= htmlspecialchars($img['image_path']) ?>', this)">
              <img src="<?= htmlspecialchars($img['image_path']) ?>" alt="">
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    <div class="detail-info">
      <div class="detail-series"><?= htmlspecialchars($p['series'] ?? '') ?></div>
      <h1 class="detail-name"><?= htmlspecialchars($p['name']) ?></h1>
      <div class="detail-price"><?= number_format($p['price'], 0, ',', '.') ?>đ</div>
      <div class="detail-tags">
        <?php if ($p['grade']): ?><span class="detail-tag"><?= htmlspecialchars($p['grade']) ?></span><?php endif; ?>
        <?php if ($p['scale']): ?><span class="detail-tag"><?= htmlspecialchars($p['scale']) ?></span><?php endif; ?>
        <?php if ($p['category_name'] ?? ''): ?><span class="detail-tag"><?= htmlspecialchars($p['category_name']) ?></span><?php endif; ?>
      </div>
      <div class="detail-stock <?= $stockClass ?>"><?= $stockText ?></div>
      <?php if ($stock > 0): ?>
        <div class="qty-row">
          <div class="qty-control">
            <button onclick="changeQty(-1, <?= $stock ?>)">−</button>
            <span id="qtyDisplay">1</span>
            <button onclick="changeQty(1, <?= $stock ?>)">+</button>
          </div>
          <button class="btn-add-large" onclick="addToCartDetail(<?= $p['id'] ?>)">+ THÊM VÀO GIỎ</button>
        </div>
      <?php else: ?>
        <button class="btn-add-large" disabled style="opacity:.4;cursor:not-allowed">HẾT HÀNG</button>
      <?php endif; ?>
      <?php if (!empty($p['description'])): ?>
        <div class="detail-desc"><?= nl2br(htmlspecialchars($p['description'])) ?></div>
      <?php endif; ?>
    </div>
  </div>

  <?php if (!empty($related)): ?>
    <div style="padding-bottom:48px">
      <div class="section-head">
        <h2 class="section-title">Liên quan</h2>
      </div>
      <div class="product-grid">
        <?php foreach ($related as $r): ?>
          <div class="product-card" onclick="window.location='<?= BASE_URL ?>/products/detail/<?= $r['id'] ?>'">
            <div class="product-img-wrap">
              <?php if (!empty($r['thumbnail_path'])): ?>
                <img src="<?= htmlspecialchars($r['thumbnail_path']) ?>" alt="<?= htmlspecialchars($r['name']) ?>" loading="lazy">
              <?php else: ?>
                <div class="img-placeholder"><?= htmlspecialchars($r['grade'] ?? '?') ?></div>
              <?php endif; ?>
              <span class="grade-badge"><?= htmlspecialchars($r['grade'] ?? '') ?></span>
            </div>
            <div class="product-info">
              <div class="product-series"><?= htmlspecialchars($r['series'] ?? '') ?></div>
              <div class="product-name"><?= htmlspecialchars($r['name']) ?></div>
              <div class="product-price-row"><span class="product-price"><?= number_format($r['price'], 0, ',', '.') ?>đ</span></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <div id="reviews" style="padding-bottom:64px;">

    <div class="section-head" style="margin-bottom:24px;">
      <h2 class="section-title">Đánh giá</h2>
      <?php if ($totalReviews > 0): ?>
        <div style="display:flex;align-items:center;gap:10px;font-family:var(--font-mono);">
          <span style="font-family:var(--font-display);font-size:38px;color:var(--gold);line-height:1;"><?= number_format($avgRating, 1) ?></span>
          <div>
            <div style="color:var(--gold);font-size:18px;letter-spacing:2px;">
              <?php for ($i = 1; $i <= 5; $i++): ?>
                <?= $i <= round($avgRating) ? '★' : '☆' ?>
              <?php endfor; ?>
            </div>
            <div style="font-size:10px;color:var(--text-hint);letter-spacing:.1em;"><?= $totalReviews ?> ĐÁNH GIÁ</div>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <?php if (!empty($_SESSION['review_success'])): ?>
      <div style="background:rgba(58,158,106,.14);border:1px solid rgba(58,158,106,.35);color:#5cba88;padding:12px 16px;border-radius:6px;font-family:var(--font-mono);font-size:12px;margin-bottom:20px;">
        ✓ <?= htmlspecialchars($_SESSION['review_success']) ?>
      </div>
      <?php unset($_SESSION['review_success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['review_errors'])): ?>
      <div style="background:rgba(200,64,64,.12);border:1px solid rgba(200,64,64,.3);color:#e07070;padding:12px 16px;border-radius:6px;font-family:var(--font-mono);font-size:12px;margin-bottom:20px;">
        <?php foreach ($_SESSION['review_errors'] as $err): ?>
          <div>✕ <?= htmlspecialchars($err) ?></div>
        <?php endforeach;
        unset($_SESSION['review_errors']); ?>
      </div>
    <?php endif; ?>

    <?php if (empty($reviews)): ?>
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:8px;padding:32px;text-align:center;color:var(--text-hint);font-family:var(--font-mono);font-size:11px;margin-bottom:32px;">
        Chưa có đánh giá nào. Hãy là người đầu tiên!
      </div>
    <?php else: ?>
      <div style="display:flex;flex-direction:column;gap:14px;margin-bottom:32px;">
        <?php foreach ($reviews as $rv): ?>
          <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:8px;padding:20px 24px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
              <div>
                <span style="font-weight:600;color:var(--text-primary);"><?= htmlspecialchars($rv['full_name']) ?></span>
                <span style="font-family:var(--font-mono);font-size:9px;color:var(--text-hint);margin-left:12px;">
                  <?= date('d/m/Y H:i', strtotime($rv['created_at'])) ?>
                </span>
              </div>
              <div style="color:var(--gold);font-size:16px;letter-spacing:2px;">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                  <?= $i <= (int)$rv['rating'] ? '★' : '☆' ?>
                <?php endfor; ?>
              </div>
            </div>
            <p style="color:var(--text-secondary);font-size:14px;line-height:1.7;margin:0;">
              <?= nl2br(htmlspecialchars($rv['comment'])) ?>
            </p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if (empty($_SESSION['user'])): ?>
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:8px;padding:28px;text-align:center;color:var(--text-hint);font-family:var(--font-mono);font-size:12px;">
        <a href="<?= BASE_URL ?>/user/login" style="color:var(--gold);">Đăng nhập</a> để viết đánh giá.
      </div>
    <?php elseif ($hasReviewed): ?>
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:8px;padding:28px;text-align:center;color:var(--text-hint);font-family:var(--font-mono);font-size:12px;">
        ✓ Bạn đã đánh giá sản phẩm này rồi.
      </div>
    <?php else: ?>
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:8px;padding:28px;">
        <h3 style="font-family:var(--font-mono);font-size:12px;color:var(--gold);letter-spacing:.15em;text-transform:uppercase;margin-bottom:22px;">
          // VIẾT ĐÁNH GIÁ CỦA BẠN
        </h3>

        <form method="POST" action="<?= BASE_URL ?>/products/submitreview">
          <input type="hidden" name="product_id" value="<?= $p['id'] ?>">

          <div style="margin-bottom:20px;">
            <label style="font-family:var(--font-mono);font-size:10px;color:var(--text-hint);letter-spacing:.12em;text-transform:uppercase;display:block;margin-bottom:10px;">
              Số sao *
            </label>
            <div style="display:flex;gap:6px;flex-direction:row-reverse;justify-content:flex-end;" class="star-rating">
              <?php for ($i = 5; $i >= 1; $i--): ?>
                <label style="cursor:pointer;font-size:28px;color:var(--border-mid);transition:color .15s;" title="<?= $i ?> sao">
                  <input type="radio" name="rating" value="<?= $i ?>" required
                    style="position:absolute;opacity:0;width:0;height:0;">
                  ★
                </label>
              <?php endfor; ?>
            </div>
          </div>

          <div style="margin-bottom:20px;">
            <label style="font-family:var(--font-mono);font-size:10px;color:var(--text-hint);letter-spacing:.12em;text-transform:uppercase;display:block;margin-bottom:8px;">
              Nội dung đánh giá * <span style="color:var(--text-hint);font-size:9px;">(tối thiểu 10 ký tự)</span>
            </label>
            <textarea name="comment" rows="4" required minlength="10" placeholder="Chia sẻ cảm nhận của bạn về sản phẩm..."
              style="width:100%;padding:12px 16px;background:var(--bg-surface);border:1px solid var(--border);border-radius:6px;color:var(--text-primary);font-family:var(--font-body);font-size:14px;line-height:1.6;outline:none;resize:vertical;transition:border-color .2s;"
              onfocus="this.style.borderColor='var(--gold-dim)'"
              onblur="this.style.borderColor='var(--border)'"></textarea>
          </div>

          <button type="submit" class="btn-primary" style="padding:12px 32px;font-size:13px;">
            GỬI ĐÁNH GIÁ
          </button>
        </form>
      </div>

      <style>
        .star-rating label {
          color: var(--border-mid);
        }

        .star-rating input:checked~label,
        .star-rating label:hover,
        .star-rating label:hover~label {
          color: var(--gold) !important;
        }
      </style>
    <?php endif; ?>

  </div>

</div>