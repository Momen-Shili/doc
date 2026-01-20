<?php
function calendar_events_json(): void {
    $doctor = auth_user();
    header('Content-Type: application/json; charset=utf-8');
    if (!$doctor) {
        http_response_code(401);
        echo json_encode(['error' => 'Session expiree.'], JSON_UNESCAPED_UNICODE);
        return;
    }

    $today = date('Y-m-d');
    $view = $_GET['view'] ?? $_GET['mode'] ?? 'month';
    $date = $_GET['date'] ?? $today;
    $view = in_array($view, ['day', 'week', 'month'], true) ? $view : 'month';
    $dateTs = strtotime($date) ?: strtotime($today);

    $selectedDate = date('Y-m-d', $dateTs);
    $rangeStart = $selectedDate;
    $rangeEnd = $selectedDate;
    if ($view === 'week') {
        $rangeStart = date('Y-m-d', strtotime('monday this week', $dateTs));
        $rangeEnd = date('Y-m-d', strtotime($rangeStart . ' +6 days'));
    } elseif ($view === 'month') {
        $rangeStart = date('Y-m-01', $dateTs);
        $rangeEnd = date('Y-m-t', $dateTs);
    }

    $events = calendar_fetch_events($doctor['id'], $rangeStart, $rangeEnd);
    echo json_encode([
        'view' => $view,
        'range' => [
            'start' => $rangeStart . ' 00:00:00',
            'end' => $rangeEnd . ' 23:59:59',
        ],
        'today' => date('Y-m-d'),
        'selected_date' => $selectedDate,
        'events' => $events,
    ], JSON_UNESCAPED_UNICODE);
}

function calendar_event_json(): void {
    $doctor = auth_user();
    header('Content-Type: application/json; charset=utf-8');
    if (!$doctor) {
        http_response_code(401);
        echo json_encode(['error' => 'Session expiree.'], JSON_UNESCAPED_UNICODE);
        return;
    }

    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Rendez-vous introuvable.'], JSON_UNESCAPED_UNICODE);
        return;
    }

    $stmt = db()->prepare('SELECT a.id, a.patient_id, a.starts_at, a.ends_at, a.status, a.reason, a.notes, p.full_name AS patient_name, p.phone AS patient_phone FROM appointments a JOIN patients p ON p.id = a.patient_id WHERE a.doctor_id = ? AND a.id = ? LIMIT 1');
    $stmt->execute([$doctor['id'], $id]);
    $appt = $stmt->fetch();
    if (!$appt) {
        http_response_code(404);
        echo json_encode(['error' => 'Rendez-vous introuvable.'], JSON_UNESCAPED_UNICODE);
        return;
    }

    $services = calendar_services_for_appointment($doctor['id'], $id);
    $serviceItems = [];
    $labels = [];
    $total = 0.0;
    foreach ($services as $srv) {
        $serviceItems[] = ['id' => (int)$srv['id'], 'name' => $srv['name'], 'qty' => (int)$srv['qty']];
        $labels[] = $srv['name'] . ' x' . (int)$srv['qty'];
        $total += (float)$srv['default_price'] * (int)$srv['qty'];
    }

    echo json_encode([
        'id' => (int)$appt['id'],
        'patient_id' => (int)$appt['patient_id'],
        'patient_name' => $appt['patient_name'],
        'patient_phone' => $appt['patient_phone'],
        'starts_at' => $appt['starts_at'],
        'ends_at' => $appt['ends_at'],
        'status' => $appt['status'],
        'status_label_fr' => appointment_status_label_fr($appt['status']),
        'reason' => $appt['reason'],
        'notes' => $appt['notes'],
        'services' => $serviceItems,
        'services_text' => implode(', ', $labels),
        'service_total' => $total,
        'service_total_formatted' => format_tnd($total),
        'color_token' => calendar_status_color($appt['status']),
    ], JSON_UNESCAPED_UNICODE);
}

function calendar_day_agenda_json(): void {
    $doctor = auth_user();
    header('Content-Type: application/json; charset=utf-8');
    if (!$doctor) {
        http_response_code(401);
        echo json_encode(['error' => 'Session expiree.'], JSON_UNESCAPED_UNICODE);
        return;
    }
    $date = $_GET['date'] ?? date('Y-m-d');
    $dateTs = strtotime($date) ?: time();
    $selectedDate = date('Y-m-d', $dateTs);
    $events = calendar_fetch_events($doctor['id'], $selectedDate, $selectedDate);
    echo json_encode(['selected_date' => $selectedDate, 'events' => $events], JSON_UNESCAPED_UNICODE);
}

function patients_search_json(): void {
    $doctor = auth_user();
    header('Content-Type: application/json; charset=utf-8');
    if (!$doctor) {
        http_response_code(401);
        echo json_encode(['error' => 'Session expiree.'], JSON_UNESCAPED_UNICODE);
        return;
    }
    $q = trim($_GET['q'] ?? '');
    if ($q === '') {
        echo json_encode([], JSON_UNESCAPED_UNICODE);
        return;
    }
    $stmt = db()->prepare('SELECT id, full_name, phone FROM patients WHERE doctor_id = ? AND (full_name LIKE ? OR phone LIKE ?) ORDER BY full_name LIMIT 10');
    $like = '%' . $q . '%';
    $stmt->execute([$doctor['id'], $like, $like]);
    $rows = $stmt->fetchAll();
    echo json_encode($rows ?: [], JSON_UNESCAPED_UNICODE);
}

function services_json(): void {
    $doctor = auth_user();
    header('Content-Type: application/json; charset=utf-8');
    if (!$doctor) {
        http_response_code(401);
        echo json_encode(['error' => 'Session expiree.'], JSON_UNESCAPED_UNICODE);
        return;
    }
    $stmt = db()->prepare('SELECT id, name, default_price, default_duration_minutes FROM services WHERE doctor_id = ? AND is_active = 1 ORDER BY name');
    $stmt->execute([$doctor['id']]);
    echo json_encode($stmt->fetchAll() ?: [], JSON_UNESCAPED_UNICODE);
}

function calendar_event_create(): void {
    calendar_event_save(0);
}

function calendar_event_update(): void {
    $id = (int)($_GET['id'] ?? 0);
    calendar_event_save($id);
}

function calendar_event_delete(): void {
    $doctor = auth_user();
    header('Content-Type: application/json; charset=utf-8');
    if (!$doctor) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Session expiree.'], JSON_UNESCAPED_UNICODE);
        return;
    }
    verify_csrf();
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Rendez-vous introuvable.'], JSON_UNESCAPED_UNICODE);
        return;
    }
    $stmt = db()->prepare('DELETE FROM appointments WHERE id = ? AND doctor_id = ?');
    $stmt->execute([$id, $doctor['id']]);
    echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
}

function calendar_event_status(): void {
    $doctor = auth_user();
    header('Content-Type: application/json; charset=utf-8');
    if (!$doctor) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Session expiree.'], JSON_UNESCAPED_UNICODE);
        return;
    }
    verify_csrf();
    $id = (int)($_GET['id'] ?? 0);
    $status = trim($_POST['status'] ?? '');
    $allowed = ['PLANIFIE', 'CONFIRME', 'TERMINE', 'ANNULE', 'NO_SHOW'];
    if ($id <= 0 || !in_array($status, $allowed, true)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'error' => 'Statut invalide.'], JSON_UNESCAPED_UNICODE);
        return;
    }
    $stmt = db()->prepare('UPDATE appointments SET status = ? WHERE id = ? AND doctor_id = ?');
    $stmt->execute([$status, $id, $doctor['id']]);
    echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
}

function calendar_event_save(int $id): void {
    $doctor = auth_user();
    header('Content-Type: application/json; charset=utf-8');
    if (!$doctor) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Session expiree.'], JSON_UNESCAPED_UNICODE);
        return;
    }
    verify_csrf();

    $payload = calendar_payload();
    $errors = calendar_validate($doctor['id'], $payload, $id);

    $selectedServices = appointment_services_from_post($doctor['id']);
    $summary = services_summary($selectedServices);
    if ($payload['ends_at'] === '' && $payload['starts_at'] !== '' && $summary['total_duration'] > 0) {
        $payload['ends_at'] = date('Y-m-d H:i:s', strtotime($payload['starts_at'] . ' +' . $summary['total_duration'] . ' minutes'));
    }

    if (!$errors && appointment_overlaps($doctor['id'], $payload['starts_at'], $payload['ends_at'], $id)) {
        $errors['starts_at'] = 'Chevauchement detecte.';
    }

    if ($errors) {
        http_response_code(422);
        echo json_encode(['success' => false, 'errors' => $errors], JSON_UNESCAPED_UNICODE);
        return;
    }

    if ($id > 0) {
        $stmt = db()->prepare('UPDATE appointments SET patient_id = ?, starts_at = ?, ends_at = ?, status = ?, reason = ?, notes = ? WHERE id = ? AND doctor_id = ?');
        $stmt->execute([
            $payload['patient_id'],
            $payload['starts_at'],
            $payload['ends_at'],
            $payload['status'],
            $payload['reason'] !== '' ? $payload['reason'] : null,
            $payload['notes'] !== '' ? $payload['notes'] : null,
            $id,
            $doctor['id'],
        ]);
        sync_appointment_services($doctor['id'], $id, $selectedServices);
        echo json_encode(['success' => true, 'id' => $id], JSON_UNESCAPED_UNICODE);
        return;
    }

    $stmt = db()->prepare('INSERT INTO appointments (doctor_id, patient_id, starts_at, ends_at, status, reason, notes) VALUES (?,?,?,?,?,?,?)');
    $stmt->execute([
        $doctor['id'],
        $payload['patient_id'],
        $payload['starts_at'],
        $payload['ends_at'],
        $payload['status'],
        $payload['reason'] !== '' ? $payload['reason'] : null,
        $payload['notes'] !== '' ? $payload['notes'] : null,
    ]);
    $newId = (int)db()->lastInsertId();
    sync_appointment_services($doctor['id'], $newId, $selectedServices);
    echo json_encode(['success' => true, 'id' => $newId], JSON_UNESCAPED_UNICODE);
}

function calendar_payload(): array {
    $startsAt = trim($_POST['starts_at'] ?? '');
    $endsAt = trim($_POST['ends_at'] ?? '');
    return [
        'patient_id' => (int)($_POST['patient_id'] ?? 0),
        'starts_at' => calendar_normalize_datetime($startsAt),
        'ends_at' => calendar_normalize_datetime($endsAt),
        'status' => trim($_POST['status'] ?? 'PLANIFIE'),
        'reason' => trim($_POST['reason'] ?? ''),
        'notes' => trim($_POST['notes'] ?? ''),
    ];
}

function calendar_normalize_datetime(string $value): string {
    if ($value === '') {
        return '';
    }
    $value = str_replace('T', ' ', $value);
    $ts = strtotime($value);
    if ($ts === false) {
        return '';
    }
    return date('Y-m-d H:i:s', $ts);
}

function calendar_validate(int $doctorId, array $payload, int $id): array {
    $errors = [];
    $allowed = ['PLANIFIE', 'CONFIRME', 'TERMINE', 'ANNULE', 'NO_SHOW'];
    if ($payload['patient_id'] <= 0) {
        $errors['patient_id'] = 'Patient requis.';
    } else {
        $stmt = db()->prepare('SELECT id FROM patients WHERE id = ? AND doctor_id = ?');
        $stmt->execute([$payload['patient_id'], $doctorId]);
        if (!$stmt->fetch()) {
            $errors['patient_id'] = 'Patient invalide.';
        }
    }
    if ($payload['starts_at'] === '' || $payload['ends_at'] === '') {
        $errors['starts_at'] = 'Dates de debut et fin requises.';
    } else {
        $start = strtotime($payload['starts_at']);
        $end = strtotime($payload['ends_at']);
        if ($start === false || $end === false) {
            $errors['starts_at'] = 'Dates invalides.';
        } elseif ($end <= $start) {
            $errors['ends_at'] = 'La fin doit etre apres le debut.';
        }
    }
    if (!in_array($payload['status'], $allowed, true)) {
        $errors['status'] = 'Statut invalide.';
    }
    if ($id > 0) {
        $stmt = db()->prepare('SELECT id FROM appointments WHERE id = ? AND doctor_id = ?');
        $stmt->execute([$id, $doctorId]);
        if (!$stmt->fetch()) {
            $errors['id'] = 'Rendez-vous introuvable.';
        }
    }
    return $errors;
}

function calendar_fetch_events(int $doctorId, string $rangeStart, string $rangeEnd): array {
    $startDateTime = $rangeStart . ' 00:00:00';
    $endDateTime = $rangeEnd . ' 23:59:59';
    $stmt = db()->prepare('SELECT a.id, a.patient_id, a.starts_at, a.ends_at, a.status, a.reason, a.notes, p.full_name AS patient_name, p.phone AS patient_phone FROM appointments a JOIN patients p ON p.id = a.patient_id WHERE a.doctor_id = ? AND a.starts_at BETWEEN ? AND ? ORDER BY a.starts_at');
    $stmt->execute([$doctorId, $startDateTime, $endDateTime]);
    $appointments = $stmt->fetchAll();

    $appointmentIds = [];
    foreach ($appointments as $appt) {
        $appointmentIds[] = (int)$appt['id'];
    }
    $servicesByAppointment = calendar_services_by_appointment($doctorId, $appointmentIds);

    $events = [];
    foreach ($appointments as $appt) {
        $services = $servicesByAppointment[$appt['id']] ?? [];
        $serviceItems = [];
        $labels = [];
        foreach ($services as $srv) {
            $serviceItems[] = ['id' => (int)$srv['id'], 'name' => $srv['name'], 'qty' => (int)$srv['qty']];
            $labels[] = $srv['name'] . ' x' . (int)$srv['qty'];
        }
        $events[] = [
            'id' => (int)$appt['id'],
            'patient_id' => (int)$appt['patient_id'],
            'patient_name' => $appt['patient_name'],
            'patient_phone' => $appt['patient_phone'],
            'date' => date('Y-m-d', strtotime($appt['starts_at'])),
            'starts_at' => $appt['starts_at'],
            'ends_at' => $appt['ends_at'],
            'status' => $appt['status'],
            'status_label_fr' => appointment_status_label_fr($appt['status']),
            'reason' => $appt['reason'],
            'notes' => $appt['notes'],
            'services' => $serviceItems,
            'services_text' => implode(', ', $labels),
            'color_token' => calendar_status_color($appt['status']),
        ];
    }
    return $events;
}

function calendar_services_by_appointment(int $doctorId, array $appointmentIds): array {
    $servicesByAppointment = [];
    if (!$appointmentIds) {
        return $servicesByAppointment;
    }
    $placeholders = implode(',', array_fill(0, count($appointmentIds), '?'));
    $params = array_merge([$doctorId], $appointmentIds);
    $stmt = db()->prepare("SELECT aps.appointment_id, aps.qty, s.id, s.name, s.default_price FROM appointment_services aps JOIN services s ON s.id = aps.service_id WHERE s.doctor_id = ? AND aps.appointment_id IN ({$placeholders})");
    $stmt->execute($params);
    while ($row = $stmt->fetch()) {
        $servicesByAppointment[(int)$row['appointment_id']][] = $row;
    }
    return $servicesByAppointment;
}

function calendar_services_for_appointment(int $doctorId, int $appointmentId): array {
    $stmt = db()->prepare('SELECT s.id, s.name, s.default_price, aps.qty FROM appointment_services aps JOIN services s ON s.id = aps.service_id WHERE s.doctor_id = ? AND aps.appointment_id = ?');
    $stmt->execute([$doctorId, $appointmentId]);
    return $stmt->fetchAll();
}

function calendar_status_color(string $status): string {
    $map = [
        'PLANIFIE' => 'status-scheduled',
        'CONFIRME' => 'status-confirmed',
        'TERMINE' => 'status-completed',
        'ANNULE' => 'status-cancelled',
        'NO_SHOW' => 'status-noshow',
    ];
    return $map[$status] ?? 'status-scheduled';
}
