<?php
// Helpers
function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): void {
    header('Location: ' . $path);
    exit;
}

function render(string $view, array $data = []): string {
    $viewPath = $view;
    extract($data);
    ob_start();
    require __DIR__ . '/views/' . $viewPath;
    return ob_get_clean();
}

function log_error(string $message): void {
    $dir = dirname(LOG_PATH);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
    file_put_contents(LOG_PATH, $line, FILE_APPEND);
}

function format_tnd($amount): string {
    $value = is_numeric($amount) ? (float)$amount : 0.0;
    return number_format($value, 3, ',', ' ') . ' TND';
}

function encrypt_secret(string $plaintext): string {
    $key = APP_KEY;
    if ($key === '') {
        throw new RuntimeException('APP_KEY manquant.');
    }
    if (str_starts_with($key, 'base64:')) {
        $key = base64_decode(substr($key, 7)) ?: '';
    }
    $iv = random_bytes(12);
    $tag = '';
    $cipher = openssl_encrypt($plaintext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    if ($cipher === false) {
        throw new RuntimeException('Chiffrement impossible.');
    }
    return base64_encode($iv . $tag . $cipher);
}

function decrypt_secret(?string $ciphertext): string {
    if (!$ciphertext) {
        return '';
    }
    $key = APP_KEY;
    if ($key === '') {
        throw new RuntimeException('APP_KEY manquant.');
    }
    if (str_starts_with($key, 'base64:')) {
        $key = base64_decode(substr($key, 7)) ?: '';
    }
    $raw = base64_decode($ciphertext, true);
    if ($raw === false || strlen($raw) < 28) {
        return '';
    }
    $iv = substr($raw, 0, 12);
    $tag = substr($raw, 12, 16);
    $cipher = substr($raw, 28);
    $plain = openssl_decrypt($cipher, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    return $plain !== false ? $plain : '';
}

function normalize_tn_phone(string $phone): ?string {
    $digits = preg_replace('/\D+/', '', $phone);
    if ($digits === null || $digits === '') {
        return null;
    }
    if (strlen($digits) === 8) {
        return '+216' . $digits;
    }
    if (strlen($digits) === 11 && str_starts_with($digits, '216')) {
        return '+216' . substr($digits, 3);
    }
    if (strlen($digits) === 12 && str_starts_with($digits, '216')) {
        return '+' . $digits;
    }
    if (str_starts_with($phone, '+216') && strlen($digits) === 11) {
        return '+216' . substr($digits, 3);
    }
    return null;
}

function set_theme_cookie(string $theme): void {
    $value = ($theme === 'light') ? 'light' : 'dark';
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    setcookie('theme', $value, [
        'expires' => time() + 31536000,
        'path' => '/',
        'samesite' => 'Lax',
        'secure' => $secure,
    ]);
}

function current_theme(): string {
    static $theme;
    if ($theme) {
        return $theme;
    }
    $doctor = auth_user();
    if ($doctor && !empty($doctor['theme_preference'])) {
        $theme = $doctor['theme_preference'];
        return $theme;
    }
    $admin = admin_user();
    if ($admin && !empty($admin['theme_preference'])) {
        $theme = $admin['theme_preference'];
        return $theme;
    }
    $cookie = $_COOKIE['theme'] ?? '';
    if ($cookie === 'light' || $cookie === 'dark') {
        $theme = $cookie;
        return $theme;
    }
    $theme = 'dark';
    return $theme;
}

function asset_url(string $path): string {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    if (str_ends_with($scriptDir, '/admin')) {
        $scriptDir = substr($scriptDir, 0, -strlen('/admin'));
    }
    if (str_ends_with($scriptDir, '/public')) {
        $scriptDir = substr($scriptDir, 0, -strlen('/public'));
    }
    $scriptDir = rtrim($scriptDir, '/');
    return $scriptDir . '/' . ltrim($path, '/');
}

function safe_redirect_path(string $path, string $default = '?r=dashboard'): string {
    $path = trim($path);
    if ($path === '') {
        return $default;
    }
    if (preg_match('/^\s*https?:/i', $path)) {
        return $default;
    }
    if (str_contains($path, "\n") || str_contains($path, "\r")) {
        return $default;
    }
    return $path;
}

function appointment_status_label(string $status): string {
    if ($status === 'NO_SHOW') {
        return 'ABSENT';
    }
    return $status;
}

function appointment_status_label_fr(string $status): string {
    $map = [
        'PLANIFIE' => 'Planifie',
        'CONFIRME' => 'Confirme',
        'TERMINE' => 'Termine',
        'ANNULE' => 'Annule',
        'NO_SHOW' => 'Absent',
    ];
    return $map[$status] ?? $status;
}

function generate_receipt_number(int $doctorId): string {
    $year = date('Y');
    $stmt = db()->prepare('SELECT COUNT(*) AS cnt FROM payments WHERE doctor_id = ? AND YEAR(created_at) = ?');
    $stmt->execute([$doctorId, $year]);
    $seq = (int)($stmt->fetch()['cnt'] ?? 0) + 1;
    for ($i = 0; $i < 5; $i++) {
        $candidate = sprintf('RC-%s-%06d', $year, $seq + $i);
        $check = db()->prepare('SELECT id FROM payments WHERE doctor_id = ? AND receipt_number = ?');
        $check->execute([$doctorId, $candidate]);
        if (!$check->fetch()) {
            return $candidate;
        }
    }
    return sprintf('RC-%s-%06d', $year, time() % 1000000);
}

function is_post(): bool {
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function old(string $key, $default = '') {
    return $_SESSION['_old'][$key] ?? $default;
}

function flash(string $key, $value = null) {
    if ($value === null) {
        $val = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $val;
    }
    $_SESSION['_flash'][$key] = $value;
}

function set_old(array $data): void {
    $_SESSION['_old'] = $data;
}

function clear_old(): void {
    unset($_SESSION['_old']);
}

function render_template_vars(string $body, array $appointment, array $patient, array $doctor): string {
    $date = '';
    $time = '';
    if (!empty($appointment['starts_at'])) {
        $ts = strtotime($appointment['starts_at']);
        if ($ts !== false) {
            $date = date('d/m/Y', $ts);
            $time = date('H:i', $ts);
        }
    }
    $replacements = [
        '{patient_nom}' => $patient['full_name'] ?? '',
        '{date}' => $date,
        '{heure}' => $time,
        '{doctor_nom}' => $doctor['full_name'] ?? '',
        '{cabinet_tel}' => $doctor['phone'] ?? '',
    ];
    return strtr($body, $replacements);
}

function reminder_already_sent(int $doctorId, int $appointmentId): bool {
    $stmt = db()->prepare("SELECT id FROM reminders_log WHERE doctor_id = ? AND appointment_id = ? AND status = 'SENT' LIMIT 1");
    $stmt->execute([$doctorId, $appointmentId]);
    return (bool)$stmt->fetch();
}

function rate_limit_status(string $key, int $max = 5, int $window = 300, int $lock = 600): array {
    $bucket = $_SESSION['_rate'][$key] ?? ['count' => 0, 'first' => time(), 'locked_until' => 0];
    $now = time();

    if (!empty($bucket['locked_until']) && $bucket['locked_until'] > $now) {
        return ['allowed' => false, 'retry_after' => $bucket['locked_until'] - $now];
    }

    if ($now - ($bucket['first'] ?? $now) > $window) {
        $bucket = ['count' => 0, 'first' => $now, 'locked_until' => 0];
    }

    $_SESSION['_rate'][$key] = $bucket;
    return ['allowed' => ($bucket['count'] ?? 0) < $max, 'retry_after' => 0];
}

function rate_limit_register(string $key, int $max = 5, int $window = 300, int $lock = 600): void {
    $now = time();
    $bucket = $_SESSION['_rate'][$key] ?? ['count' => 0, 'first' => $now, 'locked_until' => 0];
    if ($now - ($bucket['first'] ?? $now) > $window) {
        $bucket = ['count' => 0, 'first' => $now, 'locked_until' => 0];
    }
    $bucket['count'] = ($bucket['count'] ?? 0) + 1;
    if ($bucket['count'] >= $max) {
        $bucket['locked_until'] = $now + $lock;
    }
    $_SESSION['_rate'][$key] = $bucket;
}

function rate_limit_clear(string $key): void {
    unset($_SESSION['_rate'][$key]);
}

function daterange_days(string $start, string $end): array {
    $dates = [];
    $current = DateTimeImmutable::createFromFormat('Y-m-d', $start);
    $finish = DateTimeImmutable::createFromFormat('Y-m-d', $end);
    if (!$current || !$finish) {
        return $dates;
    }
    while ($current <= $finish) {
        $dates[] = $current->format('Y-m-d');
        $current = $current->modify('+1 day');
    }
    return $dates;
}

function shift_range_by_days(string $start, string $end, int $days): array {
    $startDt = DateTimeImmutable::createFromFormat('Y-m-d', $start);
    $endDt = DateTimeImmutable::createFromFormat('Y-m-d', $end);
    if (!$startDt || !$endDt) {
        return ['start' => $start, 'end' => $end];
    }
    $startShifted = $startDt->modify(($days >= 0 ? '+' : '') . $days . ' days');
    $endShifted = $endDt->modify(($days >= 0 ? '+' : '') . $days . ' days');
    return ['start' => $startShifted->format('Y-m-d'), 'end' => $endShifted->format('Y-m-d')];
}

function same_days_previous_month_range(string $start, string $end): array {
    $startDt = DateTimeImmutable::createFromFormat('Y-m-d', $start);
    $endDt = DateTimeImmutable::createFromFormat('Y-m-d', $end);
    if (!$startDt || !$endDt) {
        return ['start' => $start, 'end' => $end, 'warning' => false];
    }

    $warning = false;
    $startMonth = (int)$startDt->format('m') - 1;
    $startYear = (int)$startDt->format('Y');
    if ($startMonth < 1) {
        $startMonth = 12;
        $startYear -= 1;
    }
    $startDay = (int)$startDt->format('d');
    $startMaxDay = cal_days_in_month(CAL_GREGORIAN, $startMonth, $startYear);
    if ($startDay > $startMaxDay) {
        $startDay = $startMaxDay;
        $warning = true;
    }
    $startPrev = sprintf('%04d-%02d-%02d', $startYear, $startMonth, $startDay);

    $endMonth = (int)$endDt->format('m') - 1;
    $endYear = (int)$endDt->format('Y');
    if ($endMonth < 1) {
        $endMonth = 12;
        $endYear -= 1;
    }
    $endDay = (int)$endDt->format('d');
    $endMaxDay = cal_days_in_month(CAL_GREGORIAN, $endMonth, $endYear);
    if ($endDay > $endMaxDay) {
        $endDay = $endMaxDay;
        $warning = true;
    }
    $endPrev = sprintf('%04d-%02d-%02d', $endYear, $endMonth, $endDay);

    return ['start' => $startPrev, 'end' => $endPrev, 'warning' => $warning];
}
