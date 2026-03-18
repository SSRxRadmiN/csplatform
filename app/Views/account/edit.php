<section class="account-page">
    <div class="account-page-header">
        <a href="/account" class="product-breadcrumb-link"><?= lang('Account.back_to_account') ?></a>
        <h1 class="account-page-title"><?= lang('Account.edit_title') ?></h1>
    </div>

    <div class="edit-card">
        <form method="post" action="/account/update" class="auth-form" autocomplete="on">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="username" class="form-label"><?= lang('Account.username') ?></label>
                <div class="form-input-wrap">
                    <svg class="form-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <input type="text" id="username" name="username" class="form-input" value="<?= esc($user['username'] ?? '') ?>" placeholder="YourNickname" maxlength="64" autocomplete="username">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label"><?= lang('Account.email') ?></label>
                <div class="form-input-wrap">
                    <svg class="form-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    <input type="email" class="form-input form-input--disabled" value="<?= esc($user['email'] ?? '') ?>" disabled>
                </div>
                <p class="form-hint"><?= lang('Account.email_readonly') ?></p>
            </div>

            <div class="form-group">
                <label for="steam_id" class="form-label">
                    <?= lang('Account.steam_id') ?>
                    <span class="form-label-hint">(<?= lang('Account.steam_format') ?>)</span>
                </label>
                <div class="form-input-wrap">
                    <svg class="form-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 3h12l4 6-10 13L2 9z"/></svg>
                    <input type="text" id="steam_id" name="steam_id" class="form-input" value="<?= esc($user['steam_id'] ?? '') ?>" placeholder="STEAM_0:1:12345678" required autocomplete="off" pattern="STEAM_[0-5]:[01]:\d+">
                </div>
            </div>

            <div class="form-divider">
                <span><?= lang('Account.change_password') ?></span>
            </div>

            <div class="form-group">
                <label for="new_password" class="form-label"><?= lang('Account.new_password') ?></label>
                <div class="form-input-wrap">
                    <svg class="form-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <input type="password" id="new_password" name="new_password" class="form-input" placeholder="<?= lang('Account.new_password_hint') ?>" autocomplete="new-password" minlength="6">
                    <button type="button" class="form-input-toggle" onclick="togglePassword(this)" aria-label="<?= lang('Auth.show_password') ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="eye-open"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="eye-closed" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-auth">
                <span><?= lang('Account.save') ?></span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </button>
        </form>
    </div>
</section>

<script>
function togglePassword(btn) {
    const wrap = btn.closest('.form-input-wrap');
    const input = wrap.querySelector('input');
    const open = btn.querySelector('.eye-open');
    const closed = btn.querySelector('.eye-closed');
    if (input.type === 'password') { input.type = 'text'; open.style.display = 'none'; closed.style.display = 'block'; }
    else { input.type = 'password'; open.style.display = 'block'; closed.style.display = 'none'; }
}
</script>
