<?php
function admin_login(): void {
    require_admin_guest();
    $error = null;

    if (is_post()) {
        verify_csrf();
        $rate = rate_limit_status('admin_login');
        if (!$rate['allowed']) {
            $error = 'Trop de tentatives. Ressayez plus tard.';
            echo render('admin/pages/login.php', ['error' => $error]);
            return;
        }

        $email = trim($_POST['email'] ??'');
        $password = $_POST['password'] ??'';

        if ($email === '' || $password === '') {
            $error = 'Email et mot de passe requis.';
        } else {
            $stmt = db()->prepare('SELECT id, password_hash, is_active FROM admins WHERE email = ');
            $stmt->execute([$email]);
            $admin = $stmt->fetch();
            if (!$admin || !password_verify($password, $admin['password_hash'])) {
                $error = 'Identifiants invalides.';
                rate_limit_register('admin_login');
            } elseif ((int)$admin['is_active'] !== 1) {
                $error = 'Compte dsactiv.';
            } else {
                rate_limit_clear('admin_login');
                login_admin((int)$admin['id']);
                redirect('r=dashboard');
            }
        }
    }

    echo render('admin/pages/login.php', ['error' => $error]);
}

function admin_logout(): void {
    logout_admin();
    redirect('r=login');
}