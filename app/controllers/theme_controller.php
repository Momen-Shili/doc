<?php

function theme_toggle(): void {
    require_auth();
    if (!is_post()) {
        redirect('?r=dashboard');
    }
    verify_csrf();
    $user = auth_user();
    if (!$user) {
        redirect('?r=login');
    }
    $theme = ($_POST['theme'] ?? '') === 'light' ? 'light' : 'dark';
    $stmt = db()->prepare('UPDATE doctors SET theme_preference = ? WHERE id = ?');
    $stmt->execute([$theme, $user['id']]);
    set_theme_cookie($theme);
    $redirect = safe_redirect_path($_POST['redirect'] ?? '?r=dashboard', '?r=dashboard');
    redirect($redirect);
}

function admin_theme_toggle(): void {
    require_admin_auth();
    if (!is_post()) {
        redirect('?r=dashboard');
    }
    verify_csrf();
    $admin = admin_user();
    if (!$admin) {
        redirect('?r=login');
    }
    $theme = ($_POST['theme'] ?? '') === 'light' ? 'light' : 'dark';
    $stmt = db()->prepare('UPDATE admins SET theme_preference = ? WHERE id = ?');
    $stmt->execute([$theme, $admin['id']]);
    set_theme_cookie($theme);
    $redirect = safe_redirect_path($_POST['redirect'] ?? '?r=dashboard', '?r=dashboard');
    redirect($redirect);
}
