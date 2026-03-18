<div class="auth-container">
    <div class="auth-card">
        <div class="auth-glow"></div>

        <div class="auth-header">
            <div class="auth-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="8.5" cy="7" r="4"/>
                    <line x1="20" y1="8" x2="20" y2="14"/>
                    <line x1="23" y1="11" x2="17" y2="11"/>
                </svg>
            </div>
            <h1 class="auth-title"><?= lang('Auth.reg_title') ?></h1>
            <p class="auth-subtitle"><?= lang('Auth.reg_subtitle') ?></p>
        </div>

        <form method="post" action="/register" class="auth-form" autocomplete="on">
            <?= csrf_field() ?>
            <?= antispam_fields() ?>

            <div class="form-group">
                <label for="username" class="form-label"><?= lang('Auth.reg_username') ?></label>
                <div class="form-input-wrap">
                    <svg class="form-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                    <input type="text" id="username" name="username" class="form-input" placeholder="YourNickname" value="<?= old('username') ?>" autofocus autocomplete="username" minlength="3" maxlength="32">
                </div>
            </div>

            <div class="form-group">
                <label for="email" class="form-label"><?= lang('Auth.reg_email') ?></label>
                <div class="form-input-wrap">
                    <svg class="form-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                    </svg>
                    <input type="email" id="email" name="email" class="form-input" placeholder="your@email.com" value="<?= old('email') ?>" required autocomplete="email">
                </div>
            </div>

            <div class="form-group">
                <label for="steam_id" class="form-label">
                    <?= lang('Auth.reg_steam') ?>
                    <span class="form-label-hint">(<?= lang('Auth.reg_steam_format') ?>)</span>
                </label>
                <div class="form-input-wrap">
                    <svg class="form-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M6 3h12l4 6-10 13L2 9z"/>
                    </svg>
                    <input type="text" id="steam_id" name="steam_id" class="form-input" placeholder="STEAM_0:1:12345678" value="<?= old('steam_id') ?>" required autocomplete="off" pattern="STEAM_[0-5]:[01]:\d+" title="STEAM_0:0:12345678">
                </div>
                <p class="form-hint"><?= lang('Auth.reg_steam_hint') ?></p>
            </div>

            <div class="form-group">
                <label for="password" class="form-label"><?= lang('Auth.reg_password') ?></label>
                <div class="form-input-wrap">
                    <svg class="form-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <input type="password" id="password" name="password" class="form-input" placeholder="<?= lang('Auth.reg_password_hint') ?>" required autocomplete="new-password" minlength="6">
                    <button type="button" class="form-input-toggle" onclick="togglePassword(this)" aria-label="<?= lang('Auth.show_password') ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="eye-open"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="eye-closed" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirm" class="form-label"><?= lang('Auth.reg_confirm') ?></label>
                <div class="form-input-wrap">
                    <svg class="form-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-input" placeholder="<?= lang('Auth.reg_confirm_hint') ?>" required autocomplete="new-password" minlength="6">
                </div>
            </div>

            <button type="submit" class="btn-auth">
                <span><?= lang('Auth.reg_btn') ?></span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                </svg>
            </button>
        </form>

        <div class="auth-footer">
            <p><?= lang('Auth.reg_has_account') ?> <a href="/login" class="auth-link"><?= lang('Auth.reg_login_link') ?></a></p>
        </div>
    </div>
</div>

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
