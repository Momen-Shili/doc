<?php
function appointments_index(): void {
    require_auth();
    $doctorId = auth_user()['id'];
    $date = trim($_GET['date'] ?? '');
    $status = trim($_GET['status'] ?? '');

    $params = [$doctorId];
    $where = 'WHERE a.doctor_id = ?';
    if ($date !== '') {
        $where .= ' AND DATE(a.starts_at) = ?';
        $params[] = $date;
    }
    if ($status !== '') {
        $where .= ' AND a.status = ?';
        $params[] = $status;
    }

    $stmt = db()->prepare("SELECT a.*, p.full_name AS patient_name FROM appointments a JOIN patients p ON p.id = a.patient_id {$where} ORDER BY a.starts_at DESC");
    $stmt->execute($params);
    $appointments = $stmt->fetchAll();

    echo render('pages/appointments/index.php', [
        'appointments' => $appointments,
        'date' => $date,
        'status' => $status,
    ]);
}

function appointments_create(): void {
    require_auth();
    $doctorId = auth_user()['id'];
    $patients = load_patients($doctorId);
    $services = load_services_for_form($doctorId, []);
    $errors = [];
    $prefillDate = trim($_GET['date'] ?? '');
    $prefillTime = trim($_GET['time'] ?? '');
    $prefillStartsAt = '';
    if ($prefillDate !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $prefillDate)) {
        if ($prefillTime !== '' && preg_match('/^\d{2}:\d{2}$/', $prefillTime)) {
            $prefillStartsAt = $prefillDate . 'T' . $prefillTime;
        } else {
            $prefillStartsAt = $prefillDate . 'T09:00';
        }
    }

    if (is_post()) {
        verify_csrf();
        $data = appointment_payload();
        $selectedServices = appointment_services_from_post($doctorId);
        $summary = services_summary($selectedServices);
        if ($data['ends_at'] === '' && $data['starts_at'] !== '' && $summary['total_duration'] > 0) {
            $data['ends_at'] = date('Y-m-d\TH:i', strtotime($data['starts_at'] . ' +' . $summary['total_duration'] . ' minutes'));
        }
        $errors = validate_appointment($data);

        if (!$errors && appointment_overlaps($doctorId, $data['starts_at'], $data['ends_at'])) {
            $errors[] = 'Chevauchement détecté avec un autre rendez-vous.';
        }

        if (!$errors) {
            $stmt = db()->prepare('INSERT INTO appointments (doctor_id, patient_id, starts_at, ends_at, status, reason, notes) VALUES (?,?,?,?,?,?,?)');
            $stmt->execute([
                $doctorId,
                $data['patient_id'],
                $data['starts_at'],
                $data['ends_at'],
                $data['status'],
                $data['reason'] !== '' ? $data['reason'] : null,
                $data['notes'] !== '' ? $data['notes'] : null,
            ]);
            $appointmentId = (int)db()->lastInsertId();
            sync_appointment_services($doctorId, $appointmentId, $selectedServices);
            flash('success', 'Rendez-vous ajouté.');
            redirect('?r=appointments');
        }
        set_old($data);
    }

    echo render('pages/appointments/create.php', [
        'errors' => $errors,
        'patients' => $patients,
        'services' => $services,
        'prefillStartsAt' => $prefillStartsAt,
    ]);
}

function appointments_edit(): void {
    require_auth();
    $doctorId = auth_user()['id'];
    $id = (int)($_GET['id'] ?? 0);
    $patients = load_patients($doctorId);

    $stmt = db()->prepare('SELECT * FROM appointments WHERE id = ? AND doctor_id = ?');
    $stmt->execute([$id, $doctorId]);
    $appointment = $stmt->fetch();
    if (!$appointment) {
        http_response_code(404);
        echo render('pages/error.php', ['message' => 'Rendez-vous introuvable.']);
        return;
    }

    $selectedServices = appointment_services_for_edit($doctorId, $id);
    $services = load_services_for_form($doctorId, array_column($selectedServices, 'service_id'));
    $errors = [];
    if (is_post()) {
        verify_csrf();
        $data = appointment_payload();
        $selectedServices = appointment_services_from_post($doctorId);
        $summary = services_summary($selectedServices);
        if ($data['ends_at'] === '' && $data['starts_at'] !== '' && $summary['total_duration'] > 0) {
            $data['ends_at'] = date('Y-m-d\TH:i', strtotime($data['starts_at'] . ' +' . $summary['total_duration'] . ' minutes'));
        }
        $errors = validate_appointment($data);

        if (!$errors && appointment_overlaps($doctorId, $data['starts_at'], $data['ends_at'], $id)) {
            $errors[] = 'Chevauchement détecté avec un autre rendez-vous.';
        }

        if (!$errors) {
            $stmt = db()->prepare('UPDATE appointments SET patient_id = ?, starts_at = ?, ends_at = ?, status = ?, reason = ?, notes = ? WHERE id = ? AND doctor_id = ?');
            $stmt->execute([
                $data['patient_id'],
                $data['starts_at'],
                $data['ends_at'],
                $data['status'],
                $data['reason'] !== '' ? $data['reason'] : null,
                $data['notes'] !== '' ? $data['notes'] : null,
                $id,
                $doctorId,
            ]);
            sync_appointment_services($doctorId, $id, $selectedServices);
            flash('success', 'Rendez-vous mis à jour.');
            redirect('?r=appointments');
        }
        $appointment = array_merge($appointment, $data);
    }

    echo render('pages/appointments/edit.php', [
        'errors' => $errors,
        'patients' => $patients,
        'appointment' => $appointment,
        'services' => $services,
        'selectedServices' => $selectedServices,
        'serviceSummary' => services_summary($selectedServices),
    ]);
}

function appointments_cancel(): void {
    require_auth();
    if (!is_post()) {
        http_response_code(405);
        echo render('pages/error.php', ['message' => 'Méthode non autorisée.']);
        return;
    }
    verify_csrf();
    $doctorId = auth_user()['id'];
    $id = (int)($_POST['id'] ?? 0);
    $stmt = db()->prepare("UPDATE appointments SET status = 'ANNULE' WHERE id = ? AND doctor_id = ?");
    $stmt->execute([$id, $doctorId]);
    flash('success', 'Rendez-vous annulé.');
    redirect('?r=appointments');
}

function load_patients(int $doctorId): array {
    $stmt = db()->prepare('SELECT id, full_name FROM patients WHERE doctor_id = ? ORDER BY full_name');
    $stmt->execute([$doctorId]);
    return $stmt->fetchAll();
}

function appointment_payload(): array {
    return [
        'patient_id' => (int)($_POST['patient_id'] ?? 0),
        'starts_at' => trim($_POST['starts_at'] ?? ''),
        'ends_at' => trim($_POST['ends_at'] ?? ''),
        'status' => trim($_POST['status'] ?? 'PLANIFIE'),
        'reason' => trim($_POST['reason'] ?? ''),
        'notes' => trim($_POST['notes'] ?? ''),
    ];
}

function validate_appointment(array $data): array {
    $errors = [];
    $allowed = ['PLANIFIE', 'CONFIRME', 'TERMINE', 'ANNULE', 'NO_SHOW'];
    if ($data['patient_id'] <= 0) {
        $errors[] = 'Patient requis.';
    }
    if ($data['starts_at'] === '' || $data['ends_at'] === '') {
        $errors[] = 'Dates de début et fin requises.';
    } else {
        $start = strtotime($data['starts_at']);
        $end = strtotime($data['ends_at']);
        if ($start === false || $end === false) {
            $errors[] = 'Dates invalides.';
        } elseif ($end <= $start) {
            $errors[] = 'La fin doit être après le début.';
        }
    }
    if (!in_array($data['status'], $allowed, true)) {
        $errors[] = 'Statut invalide.';
    }
    return $errors;
}

function appointment_overlaps(int $doctorId, string $start, string $end, int $excludeId = 0): bool {
    $query = 'SELECT COUNT(*) AS cnt FROM appointments WHERE doctor_id = ? AND id != ? AND NOT (ends_at <= ? OR starts_at >= ?)';
    $stmt = db()->prepare($query);
    $stmt->execute([$doctorId, $excludeId, $start, $end]);
    return ((int)($stmt->fetch()['cnt'] ?? 0)) > 0;
}

function load_services_for_form(int $doctorId, array $selectedIds): array {
    $ids = array_filter(array_map('intval', $selectedIds));
    $params = [$doctorId];
    $sql = 'SELECT * FROM services WHERE doctor_id = ? AND is_active = 1';
    if ($ids) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql .= " OR (doctor_id = ? AND id IN ({$placeholders}))";
        $params[] = $doctorId;
        $params = array_merge($params, $ids);
    }
    $stmt = db()->prepare($sql . ' ORDER BY name');
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function appointment_services_from_post(int $doctorId): array {
    $serviceIds = $_POST['service_id'] ?? [];
    $qtys = $_POST['service_qty'] ?? [];
    $pairs = [];
    foreach ($serviceIds as $idx => $serviceId) {
        $id = (int)$serviceId;
        $qty = (int)($qtys[$idx] ?? 1);
        if ($id > 0 && $qty > 0) {
            $pairs[$id] = $qty;
        }
    }
    if (!$pairs) {
        return [];
    }
    $placeholders = implode(',', array_fill(0, count($pairs), '?'));
    $params = array_merge([$doctorId], array_keys($pairs));
    $stmt = db()->prepare("SELECT * FROM services WHERE doctor_id = ? AND id IN ({$placeholders})");
    $stmt->execute($params);
    $services = $stmt->fetchAll();
    $result = [];
    foreach ($services as $service) {
        $qty = $pairs[(int)$service['id']] ?? 1;
        $result[] = [
            'service_id' => (int)$service['id'],
            'name' => $service['name'],
            'default_price' => (float)$service['default_price'],
            'default_duration_minutes' => (int)$service['default_duration_minutes'],
            'qty' => $qty,
        ];
    }
    return $result;
}

function appointment_services_for_edit(int $doctorId, int $appointmentId): array {
    $stmt = db()->prepare('SELECT s.*, aps.qty FROM appointment_services aps JOIN services s ON s.id = aps.service_id WHERE aps.appointment_id = ? AND s.doctor_id = ? ORDER BY s.name');
    $stmt->execute([$appointmentId, $doctorId]);
    $rows = $stmt->fetchAll();
    $result = [];
    foreach ($rows as $row) {
        $result[] = [
            'service_id' => (int)$row['id'],
            'name' => $row['name'],
            'default_price' => (float)$row['default_price'],
            'default_duration_minutes' => (int)$row['default_duration_minutes'],
            'qty' => (int)$row['qty'],
        ];
    }
    return $result;
}

function services_summary(array $services): array {
    $totalPrice = 0.0;
    $totalDuration = 0;
    foreach ($services as $service) {
        $qty = (int)$service['qty'];
        $totalPrice += $qty * (float)$service['default_price'];
        $totalDuration += $qty * (int)$service['default_duration_minutes'];
    }
    return ['total_price' => $totalPrice, 'total_duration' => $totalDuration];
}

function sync_appointment_services(int $doctorId, int $appointmentId, array $services): void {
    $stmt = db()->prepare('DELETE aps FROM appointment_services aps JOIN appointments a ON a.id = aps.appointment_id WHERE aps.appointment_id = ? AND a.doctor_id = ?');
    $stmt->execute([$appointmentId, $doctorId]);
    if (!$services) {
        return;
    }
    $stmt = db()->prepare('INSERT INTO appointment_services (appointment_id, service_id, qty) VALUES (?,?,?)');
    foreach ($services as $service) {
        $stmt->execute([$appointmentId, $service['service_id'], $service['qty']]);
    }
}