<?php
// Admin router
require_once __DIR__ . '/controllers/admin_auth_controller.php';
require_once __DIR__ . '/controllers/admin_dashboard_controller.php';
require_once __DIR__ . '/controllers/admin_doctors_controller.php';
require_once __DIR__ . '/controllers/admin_payments_controller.php';
require_once __DIR__ . '/controllers/admin_settings_controller.php';

$route = $_GET['r'] ??'dashboard';

switch ($route) {
    case'login':
        admin_login();
        break;
    case'logout':
        admin_logout();
        break;
    case'dashboard':
        admin_dashboard();
        break;
    case'doctors':
        admin_doctors();
        break;
    case'doctor-toggle':
        admin_doctor_toggle();
        break;
    case'impersonate':
        admin_impersonate();
        break;
    case'impersonate-stop':
        admin_impersonate_stop();
        break;
    case'payments':
        admin_payments();
        break;
    case'payments-export':
        admin_payments_export();
        break;
    case'settings':
        admin_settings();
        break;
    default:
        http_response_code(404);
        echo render('pages/error.php', ['message' => 'Page introuvable.']);
}