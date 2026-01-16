<?php
// Admin authentication helpers
function admin_user(): ?array {
    if (empty($_SESSION['admin_id'])) {
        return null;
    }
    static $admin;
    if ($admin) {
        return $admin;
    }
    $stmt = db()->prepare('SELECT id, name, email, is_active, theme_preference FROM admins WHERE id = ?');
    $stmt->execute([$_SESSION['admin_id']]);
    $admin = $stmt->fetch();
    return $admin ? $admin : null;
}

function login_admin(int $id): void {
    $_SESSION['admin_id'] = $id;
}

function logout_admin(): void {
    unset($_SESSION['admin_id']);
    unset($_SESSION['impersonate_doctor_id']);
}

function is_admin(): bool {
    return admin_user() !== null;
}

function start_impersonation(int $doctorId): void {
    $_SESSION['impersonate_doctor_id'] = $doctorId;
}

function stop_impersonation(): void {
    unset($_SESSION['impersonate_doctor_id']);
}

function impersonated_doctor_id(): ?int {
    $id = $_SESSION['impersonate_doctor_id'] ?? null;
    return $id ? (int)$id : null;
}

function is_impersonating(): bool {
    return impersonated_doctor_id() !== null;
}
