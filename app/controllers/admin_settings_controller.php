<?php
function admin_settings(): void {
    require_admin_auth();
    $errors = [];

    $settings = load_settings();

    if (is_post()) {
        verify_csrf();
        $appName = trim($_POST['app_name'] ?? '');
        $supportEmail = trim($_POST['support_email'] ?? '');

        if ($appName === '') {
            $errors[] = 'Nom de la plateforme requis.';
        }
        if ($supportEmail !== '' && !filter_var($supportEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email de support invalide.';
        }

        if (!$errors) {
            save_setting('app_name', $appName);
            save_setting('support_email', $supportEmail);
            flash('success', 'Paramètres mis à jour.');
            redirect('?r=settings');
        }
        $settings['app_name'] = $appName;
        $settings['support_email'] = $supportEmail;
    }

    echo render('admin/pages/settings.php', ['settings' => $settings, 'errors' => $errors]);
}

function load_settings(): array {
    $stmt = db()->query('SELECT setting_key, setting_value FROM platform_settings');
    $rows = $stmt->fetchAll();
    $settings = ['app_name' => APP_NAME, 'support_email' => ''];
    foreach ($rows as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    return $settings;
}

function save_setting(string $key, string $value): void {
    $stmt = db()->prepare('INSERT INTO platform_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
    $stmt->execute([$key, $value]);
}
