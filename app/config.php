<?php
// Configuration loader
$envFile = __DIR__ . '/../.env.php';
if (!file_exists($envFile)) {
    $envFile = __DIR__ . '/../.env.example.php';
}
$config = require $envFile;

define('APP_NAME', $config['app_name'] ?? 'Cabinet');
define('APP_URL', $config['app_url'] ?? 'http://localhost');
define('APP_KEY', $config['app_key'] ?? '89e3cfa7492e47db1d4f57dcb2c0f662-4cb87092-adb8-4927-88ee-d7d228ad8350');
define('INFOBIP_BASE_URL', $config['infobip_base_url'] ?? 'https://6zjpx5.api.infobip.com');

define('DB_HOST', $config['db_host']);
define('DB_NAME', $config['db_name']);
define('DB_USER', $config['db_user']);
define('DB_PASS', $config['db_pass']);

define('LOG_PATH', __DIR__ . '/../storage/logs/app.log');
