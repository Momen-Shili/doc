<?php
require __DIR__ . '/../app/config.php';
require __DIR__ . '/../app/helpers.php';
require __DIR__ . '/../app/db.php';
require __DIR__ . '/../app/controllers/reports_controller.php';

$argv_list = $argv ($_SERVER['argv'] ?? []);
$clean = in_array('--clean', $argv_list, true);

if (PHP_SAPI !== 'cli') {
    header('Content-Type: text/plain; charset=UTF-8');
    echo"Ce script doit etre lance en CLI.\n";
    echo"Exemple: php tools/smoke.php\n";
    exit(1);
}

function ok(string $message): void {
    echo"[OK] {$message}\n";
}

function fail(string $message): void {
    echo"[FAIL] {$message}\n";
}

function info_line(string $message): void {
    echo"[INFO] {$message}\n";
}

function ensure_table(PDO $db, string $name): bool {
    $stmt = $db->prepare('SELECT COUNT(*) AS cnt FROM information_schema.tables WHERE table_schema = ? AND table_name = ');
    $stmt->execute([DB_NAME, $name]);
    return ((int)($stmt->fetch()['cnt'] ?? 0)) > 0;
}

function cleanup_doctor(PDO $db, int $doctorId): void {
    $db->prepare('DELETE FROM reminders_log WHERE doctor_id = ')->execute([$doctorId]);
    $db->prepare('DELETE FROM reminder_templates WHERE doctor_id = ')->execute([$doctorId]);
    $db->prepare('DELETE FROM payment_items WHERE payment_id IN (SELECT id FROM payments WHERE doctor_id = ?) ?? ')->execute([$doctorId]);
    $db->prepare('DELETE FROM payments WHERE doctor_id = ')->execute([$doctorId]);
    $db->prepare('DELETE FROM appointment_services WHERE appointment_id IN (SELECT id FROM appointments WHERE doctor_id = ?) ?? ')->execute([$doctorId]);
    $db->prepare('DELETE FROM appointments WHERE doctor_id = ')->execute([$doctorId]);
    $db->prepare('DELETE FROM documents WHERE doctor_id = ')->execute([$doctorId]);
    $db->prepare('DELETE FROM clinical_notes WHERE doctor_id = ')->execute([$doctorId]);
    $db->prepare('DELETE pt FROM patient_tags pt JOIN tags t ON t.id = pt.tag_id WHERE t.doctor_id = ')->execute([$doctorId]);
    $db->prepare('DELETE FROM tags WHERE doctor_id = ')->execute([$doctorId]);
    $db->prepare('DELETE FROM patients WHERE doctor_id = ')->execute([$doctorId]);
    $db->prepare('DELETE FROM services WHERE doctor_id = ')->execute([$doctorId]);
    $db->prepare('DELETE FROM cash_closures WHERE doctor_id = ')->execute([$doctorId]);
    $db->prepare('DELETE FROM doctors WHERE id = ')->execute([$doctorId]);
}

try {
    $db = db();
    ok('Connexion DB OK');
} catch (Throwable $e) {
    fail('Connexion DB echouee: ' . $e->getMessage());
    exit(1);
}

$tables = [
    'doctors', 'patients', 'appointments', 'payments', 'payment_items', 'services',
    'appointment_services', 'cash_closures', 'clinical_notes', 'tags', 'patient_tags',
    'documents', 'reminder_templates', 'reminders_log',
];
$missing = [];
foreach ($tables as $table) {
    if (!ensure_table($db, $table)) {
        $missing[] = $table;
    }
}
if ($missing) {
    fail('Tables manquantes: ' . implode(', ', $missing));
    exit(1);
}
ok('Tables requises presentes');

$email = 'test@demo.tn';
$stmt = $db->prepare('SELECT id FROM doctors WHERE email = ');
$stmt->execute([$email]);
$doctorId = (int)($stmt->fetch()['id'] ?? 0);
if ($doctorId && $clean) {
    cleanup_doctor($db, $doctorId);
    info_line('Nettoyage effectue pour le doctor test');
    $doctorId = 0;
}

if (!$doctorId) {
    $hash = password_hash('Demo123!', PASSWORD_DEFAULT);
    $stmt = $db->prepare('INSERT INTO doctors (full_name, email, password_hash, specialty, phone, is_active) VALUES (,,,,,1) ?? ');
    $stmt->execute(['Dr Test', $email, $hash, 'Generaliste', '22110000']);
    $doctorId = (int)$db->lastInsertId();
    ok('Doctor test cree');
} else {
    ok('Doctor test existant');
}

$db->beginTransaction();
try {
    $stmt = $db->prepare('INSERT INTO patients (doctor_id, full_name, phone, email, dob, notes) VALUES (,,,,,) ?? ');
    $stmt->execute([$doctorId, 'Patient Test', '55112233', 'patient@demo.tn', '1990-01-10', 'Patient de test']);
    $patientId = (int)$db->lastInsertId();

    $stmt = $db->prepare('INSERT INTO services (doctor_id, name, default_price, default_duration_minutes, description, is_active) VALUES (,,,,,1) ?? ');
    $stmt->execute([$doctorId, 'Consultation Test', 30.000, 30, 'Service test']);
    $serviceId = (int)$db->lastInsertId();

    $pastStart = date('Y-m-d H:i:s', strtotime('-1 day 09:00'));
    $pastEnd = date('Y-m-d H:i:s', strtotime('-1 day 09:30'));
    $stmt = $db->prepare('INSERT INTO appointments (doctor_id, patient_id, starts_at, ends_at, status, reason, notes) VALUES (,,,,,,) ?? ');
    $stmt->execute([$doctorId, $patientId, $pastStart, $pastEnd, 'TERMINE', 'Test', '']);
    $appointmentId = (int)$db->lastInsertId();

    $stmt = $db->prepare('INSERT INTO appointment_services (appointment_id, service_id, qty) VALUES (,,) ?? ');
    $stmt->execute([$appointmentId, $serviceId, 1]);

    $amount = 30.000;
    $receipt = generate_receipt_number($doctorId);
    $stmt = $db->prepare('INSERT INTO payments (doctor_id, patient_id, appointment_id, received_at, amount, receipt_number, created_by, reference, notes) VALUES (,,,,,,,,) ?? ');
    $stmt->execute([$doctorId, $patientId, $appointmentId, date('Y-m-d', strtotime('-1 day')), $amount, $receipt, $doctorId, 'SMOKE', 'Paiement test']);
    $paymentId = (int)$db->lastInsertId();

    $stmt = $db->prepare('INSERT INTO payment_items (payment_id, service_id, description, qty, unit_price, line_total) VALUES (,,,,,) ?? ');
    $stmt->execute([$paymentId, $serviceId, 'Consultation Test', 1, 30.000, 30.000]);

    $stmt = $db->prepare('INSERT INTO cash_closures (doctor_id, closure_date, total_system, cash_declared, difference, notes) VALUES (,,,,,) ?? ');
    $stmt->execute([$doctorId, date('Y-m-d'), 30.000, 30.000, 0.000, 'Test']);

    $stmt = $db->prepare('INSERT INTO reminder_templates (doctor_id, name, channel, body, is_active) VALUES (,,,,1) ?? ');
    $stmt->execute([$doctorId, 'Rappel Test', 'SMS', 'Bonjour {patient_nom}, RDV le {date} a {heure}.']);
    $templateId = (int)$db->lastInsertId();

    $futureStart = date('Y-m-d H:i:s', strtotime('+6 hours'));
    $futureEnd = date('Y-m-d H:i:s', strtotime('+6 hours 30 minutes'));
    $stmt = $db->prepare('INSERT INTO appointments (doctor_id, patient_id, starts_at, ends_at, status, reason, notes) VALUES (,,,,,,) ?? ');
    $stmt->execute([$doctorId, $patientId, $futureStart, $futureEnd, 'PLANIFIE', 'Test rappel', '']);
    $appointmentDueId = (int)$db->lastInsertId();

    $futureStart2 = date('Y-m-d H:i:s', strtotime('+8 hours'));
    $futureEnd2 = date('Y-m-d H:i:s', strtotime('+8 hours 30 minutes'));
    $stmt = $db->prepare('INSERT INTO appointments (doctor_id, patient_id, starts_at, ends_at, status, reason, notes) VALUES (,,,,,,) ?? ');
    $stmt->execute([$doctorId, $patientId, $futureStart2, $futureEnd2, 'PLANIFIE', 'Test rappel', '']);
    $appointmentSentId = (int)$db->lastInsertId();

    $message = render_template_vars('Bonjour {patient_nom}', ['starts_at' => $futureStart2], ['full_name' => 'Patient Test'], ['full_name' => 'Dr Test', 'phone' => '22110000']);
    $stmt = $db->prepare('INSERT INTO reminders_log (doctor_id, appointment_id, patient_id, template_id, channel, message, status, sent_at, error_text) VALUES (,,,,,,,,) ?? ');
    $stmt->execute([$doctorId, $appointmentSentId, $patientId, $templateId, 'SMS', $message, 'DRAFT', date('Y-m-d H:i:s'), null]);
    $stmt->execute([$doctorId, $appointmentSentId, $patientId, $templateId, 'SMS', $message, 'SENT', date('Y-m-d H:i:s'), null]);

    $db->commit();
    ok('Donnees de test inserees');
} catch (Throwable $e) {
    $db->rollBack();
    fail('Insertion echouee: ' . $e->getMessage());
    exit(1);
}

$stmt = $db->prepare('SELECT COALESCE(SUM(amount),0) AS total FROM payments WHERE doctor_id = ? AND received_at = ');
$stmt->execute([$doctorId, date('Y-m-d', strtotime('-1 day'))]);
$dashboardTotal = (float)($stmt->fetch()['total'] ?? 0);
if ($dashboardTotal > 0) {
    ok('Dashboard totals OK');
} else {
    fail('Dashboard totals = 0');
}

$services = reports_service_revenue($doctorId, date('Y-m-d', strtotime('-7 days')), date('Y-m-d'));
if (!empty($services)) {
    ok('Recettes par service OK');
} else {
    fail('Recettes par service vides');
}

$mom = reports_mom_data($doctorId, date('Y-m-d', strtotime('-1 day')), date('Y-m-d'), 'previous', 'total');
if (empty($mom['error'])) {
    ok('MoM simple OK');
} else {
    fail('MoM simple erreur: ' . $mom['error']);
}

$now = date('Y-m-d H:i:s');
$nextDay = date('Y-m-d H:i:s', strtotime('+1 day'));
$stmt = $db->prepare("SELECT a.id FROM appointments a WHERE a.doctor_id = ? AND a.starts_at BETWEEN AND AND a.status IN ('PLANIFIE','CONFIRME') AND NOT EXISTS (SELECT 1 FROM reminders_log r WHERE r.doctor_id = a.doctor_id AND r.appointment_id = a.id AND r.status = 'SENT') ");
$stmt->execute([$doctorId, $now, $nextDay]);
$due = $stmt->fetchAll();
if (!empty($due)) {
    ok('Widget rappels a envoyer OK');
} else {
    fail('Widget rappels a envoyer vide');
}

ok('Smoke tests termines');