<?php
// Configuration loader
$envFile = __DIR__ . '/../.env.php';
if (!file_exists($envFile)) {
    $envFile = __DIR__ . '/../.env.example.php';
}
$config = require $envFile;

define('APP_NAME', $config['app_name'] ?? 'Cabinet');
define('APP_URL', $config['app_url'] ?? 'http://localhost');

define('DB_HOST', $config['db_host']);
define('DB_NAME', $config['db_name']);
define('DB_USER', $config['db_user']);
define('DB_PASS', $config['db_pass']);

define('LOG_PATH', __DIR__ . '/../storage/logs/app.log');