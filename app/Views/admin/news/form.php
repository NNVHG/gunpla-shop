<?php
$isEdit = $isEdit ?? false;
$news   = $news ?? [];
$errors = $errors ?? [];
?>
<div class="admin-header">
    <h1 class="admin-title"><?= $isEdit ? 'Cập nhật tin tức' : 'Thêm bài viết mới' ?></h1>
    <a href="<?= BASE_URL ?>/admin/news" class="btn" style="color:var(--text-hint)">Quay lại danh sách</a>
</div>

<?php if (!empty($errors)): ?>
    <div class="flash error" style="background: var(--bg-panel); border-left: 4px solid var(--red); padding: 12px; margin-bottom: 20px;">
        <div style="color: var(--red); font-weight: bold;">Có lỗi nhập liệu xảy ra:</div>
        <ul style="margin: 4px 0 0 20px; padding: 0; color: var(--text-1);">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="" method="POST" enctype="multipart/form-data" class="admin-table-wrap" style="padding: 24px; max-width: 850px;">
    <div class="form-group" style="margin-bottom: 20px;">
        <label style="color: var(--text-1); display: block; margin-bottom: 6px; font-weight: 500;">Tiêu đề bài viết *</label>
        <input type="text" name="title" value="<?= htmlspecialchars($news['title'] ?? '') ?>" required style="width:100%; padding:10px; background:var(--bg-card); border:1px solid var(--border); color:var(--text-1); border-radius:4px; font-family:inherit;">
    </div>

    <div class="form-group" style="margin-bottom: 20px;">
        <label style="color: var(--text-1); display: block; margin-bottom: 6px; font-weight: 500;">Đoạn tóm tắt bài viết (Summary)</label>
        <textarea name="summary" rows="3" style="width:100%; padding:10px; background:var(--bg-card); border:1px solid var(--border); color:var(--text-1); border-radius:4px; font-family:inherit; resize: vertical;"><?= htmlspecialchars($news['summary'] ?? '') ?></textarea>
    </div>

    <div class="admin-table-wrap" style="margin-bottom: 20px; padding: 16px; border: 1px dashed var(--border); background: var(--bg-panel); border-radius: 4px;">
        <label style="margin-bottom: 8px; display: block; color: var(--text-1); font-weight: 500;">Hình ảnh đại diện bài viết</label>
        <input type="file" name="image_path" style="border: none; padding: 0; background: transparent; color: var(--text-2); cursor: pointer;">
        <?php if ($isEdit && !empty($news['image_path'])): ?>
            <div style="margin-top: 14px;">
                <p style="font-family: var(--font-m); font-size:13px; color: var(--text-3); margin-bottom: 6px;">Hình ảnh hiện tại:</p>
                <img src="<?= BASE_URL . '/' . $news['image_path'] ?>" style="width: 160px; height: 100px; object-fit: cover; border-radius: 4px; border: 1px solid var(--border);">
            </div>
        <?php endif; ?>
    </div>

    <div class="form-group" style="margin-bottom: 20px;">
        <label style="color: var(--text-1); display: block; margin-bottom: 6px; font-weight: 500;">Nội dung bài viết chi tiết *</label>
        <textarea name="content" rows="14" required style="width:100%; padding:10px; background:var(--bg-card); border:1px solid var(--border); color:var(--text-1); border-radius:4px; font-family:var(--font-m); line-height: 1.5; resize: vertical;"><?= htmlspecialchars($news['content'] ?? '') ?></textarea>
    </div>

    <div class="form-group" style="margin-bottom: 24px;">
        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; text-transform: none; color: var(--text-1); font-size:15px;">
    
            <input type="checkbox" name="is_active" value="1" 
                <?= ($isEdit ? (isset($news['is_active']) && $news['is_active'] == 1 ? 'checked' : '') : 'checked') ?> 
                style="width: 17px; height: 17px; accent-color: var(--gold); cursor: pointer;">
                
            Cho phép hiển thị công khai bài viết này ngoài trang chủ
        </label>
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid var(--border); padding-top: 20px;">
        <a href="<?= BASE_URL ?>/admin/news" class="btn" style="background: var(--bg-panel); color: var(--text-2); border-color: var(--border);">Hủy bỏ</a>
        <button type="submit" class="btn btn-gold" style="min-width: 140px; font-weight: bold; letter-spacing: 0.05em;">
            <?= $isEdit ? 'CẬP NHẬT' : 'THÊM MỚI' ?>
        </button>
    </div>
</form>