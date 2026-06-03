<div class="auth-container">
    <div class="auth-card" style="text-align:center;">
        <div class="auth-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"/>
            </svg>
        </div>
        <h2 class="auth-title">Переадресація на оплату...</h2>
        <p class="auth-subtitle">Зачекайте, вас буде перенаправлено. Якщо цього не сталося — натисніть кнопку нижче.</p>

        <form id="cassaForm" method="post" action="<?= esc($payment['action']) ?>">
            <?php foreach ($payment['fields'] as $name => $value): ?>
                <input type="hidden" name="<?= esc($name) ?>" value="<?= esc($value) ?>">
            <?php endforeach ?>

            <button type="submit" class="btn-auth" style="margin-top:1.5rem;">
                <span>Перейти до оплати</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </button>
        </form>
    </div>
</div>

<script>
    document.getElementById('cassaForm').submit();
</script>
