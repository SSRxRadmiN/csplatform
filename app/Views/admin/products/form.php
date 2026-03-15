<?php
$isEdit = !empty($product);
$action = $isEdit ? '/admin/products/edit/' . $product['id'] : '/admin/products/create';
?>

<section class="admin-page">
    <div class="admin-header">
        <h1 class="admin-title"><?= $isEdit ? 'Редагувати товар' : 'Новий товар' ?></h1>
        <div class="admin-nav">
            <a href="/admin" class="admin-nav-link">Dashboard</a>
            <a href="/admin/products" class="admin-nav-link active">Товари</a>
            <a href="/admin/orders" class="admin-nav-link">Замовлення</a>
            <a href="/admin/users" class="admin-nav-link">Користувачі</a>
            <a href="/admin/settings" class="admin-nav-link">Налаштування</a>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-section-header">
            <a href="/admin/products" class="admin-link">← Назад до товарів</a>
        </div>

        <form method="post" action="<?= $action ?>" class="admin-form">
            <?= csrf_field() ?>

            <div class="admin-form-grid">
                <div class="admin-form-group">
                    <label class="admin-label">Назва (UA) *</label>
                    <input type="text" name="name_ua" class="admin-input"
                        value="<?= esc(old('name_ua', $product['name_ua'] ?? '')) ?>" required>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Назва (EN)</label>
                    <input type="text" name="name_en" class="admin-input"
                        value="<?= esc(old('name_en', $product['name_en'] ?? '')) ?>">
                </div>

                <div class="admin-form-group admin-form-full">
                    <label class="admin-label">Опис (UA)</label>
                    <textarea name="description_ua" class="admin-textarea" rows="3"><?= esc(old('description_ua', $product['description_ua'] ?? '')) ?></textarea>
                </div>

                <div class="admin-form-group admin-form-full">
                    <label class="admin-label">Опис (EN)</label>
                    <textarea name="description_en" class="admin-textarea" rows="3"><?= esc(old('description_en', $product['description_en'] ?? '')) ?></textarea>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Сервер *</label>
                    <select name="server_id" class="admin-input" required>
                        <option value="">Оберіть...</option>
                        <?php foreach ($servers as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= old('server_id', $product['server_id'] ?? '') == $s['id'] ? 'selected' : '' ?>>
                                <?= esc($s['name']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Категорія *</label>
                    <select name="category_id" class="admin-input" required>
                        <option value="">Оберіть...</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= old('category_id', $product['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                                <?= esc($c['icon'] ?? '') ?> <?= esc($c['name_ua']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Ціна (₴) *</label>
                    <input type="number" name="price" class="admin-input" min="1"
                        value="<?= esc(old('price', $product['price'] ?? '')) ?>" required>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Тривалість (днів)</label>
                    <input type="number" name="duration_days" class="admin-input" min="0"
                        value="<?= esc(old('duration_days', $product['duration_days'] ?? '30')) ?>">
                    <small class="admin-hint">0 = одноразово</small>
                </div>

                <div class="admin-form-group admin-form-full">
                    <label class="admin-label">Зображення (URL)</label>
                    <input type="url" name="image_url" class="admin-input"
                        value="<?= esc(old('image_url', $product['image_url'] ?? '')) ?>"
                        placeholder="https://cs-headshot.com/assets/images/product.png">
                    <small class="admin-hint">URL картинки товару (моделі зброї тощо)</small>
                    <?php if (!empty($product['image_url'])): ?>
                        <div style="margin-top:0.5rem;">
                            <img src="<?= esc($product['image_url']) ?>" alt="Preview"
                                style="max-height:80px; border-radius:6px; border:1px solid rgba(74,222,128,0.15);">
                        </div>
                    <?php endif ?>
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">AMX Access</label>
                    <input type="text" name="amx_access" class="admin-input"
                        value="<?= esc(old('amx_access', $product['amx_access'] ?? '')) ?>"
                        placeholder="t">
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">AMX Flags</label>
                    <input type="text" name="amx_flags" class="admin-input"
                        value="<?= esc(old('amx_flags', $product['amx_flags'] ?? '')) ?>"
                        placeholder="ce">
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">Порядок сортування</label>
                    <input type="number" name="sort_order" class="admin-input" min="0"
                        value="<?= esc(old('sort_order', $product['sort_order'] ?? '0')) ?>">
                </div>

                <div class="admin-form-group">
                    <label class="admin-label">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1"
                            <?= old('is_active', $product['is_active'] ?? 1) ? 'checked' : '' ?>>
                        Активний
                    </label>
                </div>
            </div>

            <button type="submit" class="btn-admin-primary" style="margin-top:1.5rem;">
                <?= $isEdit ? 'Зберегти зміни' : 'Створити товар' ?>
            </button>
        </form>
    </div>
</section>
