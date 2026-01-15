<?php
// CSRF protection
function csrf_token(): string {
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string {
    return'<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void {
    if (!is_post()) {
        return;
    }
    $token = $_POST['_csrf'] ??'';
    if (!$token || !hash_equals($_SESSION['_csrf'] ??'', $token)) {
        http_response_code(419);
        echo render('pages/error.php', ['message' => 'Jeton CSRF invalide.']);
        exit;
    }
}