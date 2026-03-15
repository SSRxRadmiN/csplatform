<?php
$isEdit = !empty($category);
$action = $isEdit ? '/admin/categories/edit/' . $category['id'] : '/admin/categories/create';
?>

<section class="admin-page">
    <div class="admin-header">
        <h1 class="admin-title"><?= $isEdit ? 'Редагувати категорію' : 'Нова категорія' ?></h1>
        <div class="admin-nav">
            <a href="/admin" class="admin-nav-link">Dashboard</a>
            <a href="/admin/products" class="admin-nav-link">Товари</a>
            <a href="/admin/categories" class="admin-nav-link active">Категорії</a>
            <a href="/admin/orders" class="admin-nav-link">Замовлення</a>
            <a href="/admin/users" class="admin-nav-link">Користувачі</a>
            <a href="/admin/settings" class="admin-nav-link">Налаштування</a>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-section-header">
            <a href="/admin/categories" class="admin-link">← Назад до категорій</a>
        </div>

        <form method="post" action="<?= $action ?>" class="admin-form">
            <?= csrf_field() ?>

            <div class="admin-form-grid">
                <div class="admin-form-group">
                    <label class="admin-label">Slug *</label>
                    <input type="text" name="slug" class="admin-input"
                        value="<?= esc(old('slug', $category['slug'] ?? '')) ?>"
                        placeholder="vip" required pattern="[a-z0-9_-]+">
                    <small class="admin-hint">Тільки латиниця, цифри, дефіс, підкреслення</small>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Іконка (emoji)</label>
                    <input type="text" name="icon" class="admin-input"
                        value="<?= esc(old('icon', $category['icon'] ?? '')) ?>"
                        placeholder="⭐">
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Назва (UA) *</label>
                    <input type="text" name="name_ua" class="admin-input"
                        value="<?= esc(old('name_ua', $category['name_ua'] ?? '')) ?>" required>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Назва (EN)</label>
                    <input type="text" name="name_en" class="admin-input"
                        value="<?= esc(old('name_en', $category['name_en'] ?? '')) ?>">
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Колір (hex)</label>
                    <div style="display:flex;gap:0.5rem;align-items:center;">
                        <input type="color" name="color" style="width:40px;height:36px;border:none;background:transparent;cursor:pointer;"
                            value="<?= esc(old('color', $category['color'] ?? '#4ade80')) ?>">
                        <input type="text" class="admin-input" style="flex:1;"
                            value="<?= esc(old('color', $category['color'] ?? '#4ade80')) ?>"
                            readonly id="color-text">
                    </div>
                    <small class="admin-hint">Використовується для бейджів у магазині</small>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Порядок сортування</label>
                    <input type="number" name="sort_order" class="admin-input" min="0"
                        value="<?= esc(old('sort_order', $category['sort_order'] ?? '0')) ?>">
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1"
                            <?= old('is_active', $category['is_active'] ?? 1) ? 'checked' : '' ?>>
                        Активна
                    </label>
                </div>
            </div>

            <button type="submit" class="btn-admin-primary" style="margin-top:1.5rem;">
                <?= $isEdit ? 'Зберегти зміни' : 'Створити категорію' ?>
            </button>
        </form>
    </div>
</section>

<script>
document.querySelector('input[type="color"]').addEventListener('input', function() {
    document.getElementById('color-text').value = this.value;
});
</script>
