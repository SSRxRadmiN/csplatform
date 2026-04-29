<div class="admin-header">
    <h1 class="admin-title"><?= $adminTitle ?? 'Адмін-панель' ?></h1>
    <div class="admin-nav">
        <?php
        $adminNav = [
            '/admin'            => 'Dashboard',
            '/admin/products'   => 'Товари',
            '/admin/categories' => 'Категорії',
            '/admin/orders'     => 'Замовлення',
            '/admin/users'      => 'Користувачі',
            '/admin/privileges' => 'Привілеї',
            '/admin/servers'    => 'Сервери',
            '/admin/settings'   => 'Налаштування',
        ];
        $currentPath = '/' . trim(uri_string(), '/');
        foreach ($adminNav as $url => $label):
            // Активний якщо точний збіг або починається з цього шляху (крім /admin)
            $isActive = ($url === '/admin')
                ? ($currentPath === '/admin' || $currentPath === '/admin/')
                : str_starts_with($currentPath, $url);
        ?>
            <a href="<?= $url ?>" class="admin-nav-link <?= $isActive ? 'active' : '' ?>"><?= $label ?></a>
        <?php endforeach ?>
    </div>
</div>
