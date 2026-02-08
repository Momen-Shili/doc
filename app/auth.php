<?php
// Authentication helpers
function auth_user(): ?array {
    $doctorId = $_SESSION['doctor_id'] ?? null;
    if (!$doctorId && is_impersonating()) {
        $doctorId = impersonated_doctor_id();
    }
    if (!$doctorId) {
        return null;
    }
    static $user;
    if ($user) {
        return $user;
    }
    $stmt = db()->prepare('SELECT id, full_name, email, specialty, phone, public_phone, city, address_public, bio_public, public_slug, public_photo, latitude, longitude, is_active, suspended_at, plan, theme_preference FROM doctors WHERE id = ?');
    $stmt->execute([$doctorId]);
    $user = $stmt->fetch();
    return $user ? $user : null;
}

function login_doctor(int $id): void {
    $_SESSION['doctor_id'] = $id;
}

function logout_doctor(): void {
    unset($_SESSION['doctor_id']);
}
