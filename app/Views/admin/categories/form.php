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
    <a href="<?= BASE_URL ?>/admin/categories" class="btn">Quay lại</a>
</div>

<form action="<?= $action ?>" method="POST" class="admin-table-wrap" style="padding: 24px; max-width: 600px;">
    
    <div class="form-group" style="margin-bottom: 20px;">
        <label>Tên danh mục *</label>
        <input type="text" name="name" required value="<?= htmlspecialchars($category['name'] ?? '') ?>">
    </div>

    <div class="form-group" style="margin-bottom: 20px;">
        <label>Phân loại (Type) *</label>
        <select name="type" required>
            <option value="">-- Chọn loại --</option>
            <?php foreach ($types as $t): ?>
                <option value="<?= $t ?>" <?= (($category['type'] ?? '') === $t) ? 'selected' : '' ?>>
                    <?= ucfirst($t) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group" style="margin-bottom: 24px;">
        <label>Danh mục cha (Tùy chọn)</label>
        <select name="parent_id">
            <option value="">-- Không có --</option>
            <?php foreach ($parents as $p): ?>
                <option value="<?= $p['id'] ?>" <?= (($category['parent_id'] ?? '') == $p['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['name']) ?> (<?= ucfirst($p['type']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit" class="btn btn-gold" style="width: 100%; padding: 12px; font-size:18px;">
        <?= $isEdit ? 'CẬP NHẬT' : 'THÊM MỚI' ?>
    </button>
</form>