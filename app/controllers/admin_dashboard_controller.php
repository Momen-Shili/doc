<?php
function admin_dashboard(): void {
    require_admin_auth();

    $stmt = db()->query('SELECT COUNT(*) AS total, SUM(is_active = 1) AS active FROM doctors');
    $row = $stmt->fetch();
    $totalDoctors = (int)($row['total'] ?? 0);
    $activeDoctors = (int)($row['active'] ?? 0);

    $stmt = db()->query('SELECT COUNT(*) AS suspended FROM doctors WHERE suspended_at IS NOT NULL');
    $suspendedDoctors = (int)($stmt->fetch()['suspended'] ?? 0);

    $stmt = db()->query('SELECT COALESCE(SUM(amount),0) AS total FROM payments');
    $globalRevenue = $stmt->fetch()['total'] ?? 0;

    echo render('admin/pages/dashboard.php', [
        'totalDoctors' => $totalDoctors,
        'activeDoctors' => $activeDoctors,
        'suspendedDoctors' => $suspendedDoctors,
        'globalRevenue' => $globalRevenue,
    ]);
}