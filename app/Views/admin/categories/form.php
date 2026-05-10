<?php
/**
 * @var array|null $category
 * @var array $parents
 * @var string $title
 */
$isEdit = !empty($category);
$action = $isEdit ? BASE_URL . '/admin/categories/update/' . $category['id'] : BASE_URL . '/admin/categories/store';
$types = ['scale', 'grade', 'series', 'manufacturer', 'tool', 'accessory', 'chemical', 'combo'];
?>
<div class="admin-header">
    <h1 class="admin-title"><?= htmlspecialchars($title) ?></h1>
    <a href="<?= BASE_URL ?>/admin/categories" class="btn-gold" style="background:transparent; border-color:var(--text-hint); color:var(--text-hint)">
        Quay lại
    </a>
</div>

<form action="<?= $action ?>" method="POST" class="admin-table-wrap" style="padding: 24px; max-width: 600px;">
    
    <div class="form-group" style="margin-bottom: 20px;">
        <label class="form-label" style="display:block; margin-bottom:8px; color:var(--text-secondary)">Tên danh mục <span style="color:var(--gold)">*</span></label>
        <input type="text" name="name" class="form-input" required 
               value="<?= htmlspecialchars($category['name'] ?? '') ?>"
               style="width: 100%; padding: 10px; background: var(--bg-main); border: 1px solid var(--border); color: var(--text-primary); border-radius: 4px;">
    </div>

    <div class="form-group" style="margin-bottom: 20px;">
        <label class="form-label" style="display:block; margin-bottom:8px; color:var(--text-secondary)">Loại (Type) <span style="color:var(--gold)">*</span></label>
        <select name="type" class="form-input" required style="width: 100%; padding: 10px; background: var(--bg-main); border: 1px solid var(--border); color: var(--text-primary); border-radius: 4px;">
            <option value="">-- Chọn loại --</option>
            <?php foreach ($types as $t): ?>
                <option value="<?= $t ?>" <?= (($category['type'] ?? '') === $t) ? 'selected' : '' ?>>
                    <?= ucfirst($t) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group" style="margin-bottom: 24px;">
        <label class="form-label" style="display:block; margin-bottom:8px; color:var(--text-secondary)">Danh mục cha (Tùy chọn)</label>
        <select name="parent_id" class="form-input" style="width: 100%; padding: 10px; background: var(--bg-main); border: 1px solid var(--border); color: var(--text-primary); border-radius: 4px;">
            <option value="">-- Không có --</option>
            <?php foreach ($parents as $p): ?>
                <option value="<?= $p['id'] ?>" <?= (($category['parent_id'] ?? '') == $p['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['name']) ?> (<?= ucfirst($p['type']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit" class="btn-gold" style="width: 100%; padding: 12px; font-size: 16px;">
        <?= $isEdit ? 'Cập nhật' : 'Thêm mới' ?>
    </button>
</form>
