<?php
function dashboard_index(): void {
    require_auth();
    $doctor = auth_user();

    $today = date('Y-m-d');
    $monthStart = date('Y-m-01');
    $weekStart = date('Y-m-d', strtotime('-6 days'));

    $view = $_GET['view'] ?? 'day';
    $date = $_GET['date'] ?? $today;
    $view = in_array($view, ['day', 'week', 'month'], true) ? $view : 'day';
    $stmt = db()->prepare('SELECT COALESCE(SUM(amount),0) AS total FROM payments WHERE doctor_id = ? AND DATE(received_at) = ?');
    $stmt->execute([$doctor['id'], $today]);
    $todayRevenue = $stmt->fetch()['total'] ?? 0;

    $stmt = db()->prepare('SELECT COALESCE(SUM(amount),0) AS total FROM payments WHERE doctor_id = ? AND DATE(received_at) BETWEEN ? AND ?');
    $stmt->execute([$doctor['id'], $monthStart, date('Y-m-d')]);
    $monthRevenue = $stmt->fetch()['total'] ?? 0;

    $stmt = db()->prepare('SELECT COUNT(*) AS cnt FROM appointments WHERE doctor_id = ? AND DATE(starts_at) = ?');
    $stmt->execute([$doctor['id'], $today]);
    $appointmentsToday = (int)($stmt->fetch()['cnt'] ?? 0);

    $stmt = db()->prepare("SELECT COUNT(*) AS cnt FROM appointments WHERE doctor_id = ? AND status IN ('ANNULE','NO_SHOW') AND DATE(starts_at) BETWEEN ? AND ?");
    $stmt->execute([$doctor['id'], $weekStart, $today]);
    $cancellationsWeek = (int)($stmt->fetch()['cnt'] ?? 0);

    $stmt = db()->prepare('SELECT COUNT(*) AS cnt FROM patients WHERE doctor_id = ? AND DATE(created_at) BETWEEN ? AND ?');
    $stmt->execute([$doctor['id'], $monthStart, $today]);
    $newPatientsMonth = (int)($stmt->fetch()['cnt'] ?? 0);

    $stmt = db()->prepare('SELECT COALESCE(AVG(amount),0) AS avg_amount FROM payments WHERE doctor_id = ? AND DATE(received_at) BETWEEN ? AND ?');
    $stmt->execute([$doctor['id'], $monthStart, $today]);
    $avgTicket = $stmt->fetch()['avg_amount'] ?? 0;

    $stmt = db()->prepare('SELECT p.full_name, p.id FROM patients p WHERE p.doctor_id = ? ORDER BY p.created_at DESC LIMIT 5');
    $stmt->execute([$doctor['id']]);
    $recentPatients = $stmt->fetchAll();

    $stmt = db()->prepare('SELECT DATE(received_at) AS day, SUM(amount) AS total FROM payments WHERE doctor_id = ? AND DATE(received_at) BETWEEN ? AND ? GROUP BY DATE(received_at) ORDER BY day');
    $stmt->execute([$doctor['id'], date('Y-m-d', strtotime('-6 days')), $today]);
    $miniRevenue = $stmt->fetchAll();

    $now = date('Y-m-d H:i:s');
    $nextDay = date('Y-m-d H:i:s', strtotime('+1 day'));
    $stmt = db()->prepare("SELECT a.id, a.starts_at, a.ends_at, p.full_name AS patient_name, p.email, p.phone, p.whatsapp_phone, p.consent_email, p.consent_sms, p.consent_whatsapp FROM appointments a JOIN patients p ON p.id = a.patient_id WHERE a.doctor_id = ? AND a.starts_at BETWEEN ? AND ? AND a.status IN ('PLANIFIE','CONFIRME') AND NOT EXISTS (SELECT 1 FROM reminders_log r WHERE r.doctor_id = a.doctor_id AND r.appointment_id = a.id AND r.status = 'SENT') ORDER BY a.starts_at");
    $stmt->execute([$doctor['id'], $now, $nextDay]);
    $remindersDue = $stmt->fetchAll();

    $notifSettings = notifications_load_settings($doctor['id']);
    $notifConfig = [
        'email' => !empty($notifSettings['smtp_host']) && !empty($notifSettings['smtp_username']) && !empty($notifSettings['smtp_password_enc']) && !empty($notifSettings['smtp_from_email']),
        'sms' => !empty($notifSettings['infobip_api_key_enc']) && !empty($notifSettings['infobip_sms_sender']),
        'whatsapp' => !empty($notifSettings['infobip_api_key_enc']) && !empty($notifSettings['infobip_whatsapp_sender']),
    ];

    echo render('pages/dashboard.php', [
        'doctor' => $doctor,
        'todayRevenue' => $todayRevenue,
        'monthRevenue' => $monthRevenue,
        'appointmentsToday' => $appointmentsToday,
        'cancellationsWeek' => $cancellationsWeek,
        'newPatientsMonth' => $newPatientsMonth,
        'avgTicket' => $avgTicket,
        'view' => $view,
        'date' => $date,
        'recentPatients' => $recentPatients,
        'miniRevenue' => $miniRevenue,
        'remindersDue' => $remindersDue,
        'notifConfig' => $notifConfig,
    ]);
}

function calendar_events_json(): void {
    require_auth();
    $doctor = auth_user();
    $today = date('Y-m-d');
    $mode = $_GET['mode'] ?? 'day';
    $date = $_GET['date'] ?? $today;
    $mode = in_array($mode, ['day', 'week', 'month'], true) ? $mode : 'day';
    $dateTs = strtotime($date) ?: strtotime($today);

    $rangeStart = date('Y-m-d', $dateTs);
    $rangeEnd = $rangeStart;
    if ($mode === 'week') {
        $rangeStart = date('Y-m-d', strtotime('monday this week', $dateTs));
        $rangeEnd = date('Y-m-d', strtotime($rangeStart . ' +6 days'));
    } elseif ($mode === 'month') {
        $rangeStart = date('Y-m-01', $dateTs);
        $rangeEnd = date('Y-m-t', $dateTs);
    }

    $startDateTime = $rangeStart . ' 00:00:00';
    $endDateTime = $rangeEnd . ' 23:59:59';

    $stmt = db()->prepare('SELECT a.id, a.patient_id, a.starts_at, a.ends_at, a.status, a.reason, a.notes, p.full_name AS patient_name FROM appointments a JOIN patients p ON p.id = a.patient_id WHERE a.doctor_id = ? AND a.starts_at BETWEEN ? AND ? ORDER BY a.starts_at');
    $stmt->execute([$doctor['id'], $startDateTime, $endDateTime]);
    $appointments = $stmt->fetchAll();

    $appointmentIds = [];
    foreach ($appointments as $appt) {
        $appointmentIds[] = (int)$appt['id'];
    }

    $servicesByAppointment = [];
    if ($appointmentIds) {
        $placeholders = implode(',', array_fill(0, count($appointmentIds), '?'));
        $params = array_merge([$doctor['id']], $appointmentIds);
        $stmt = db()->prepare("SELECT aps.appointment_id, aps.qty, s.name, s.default_price FROM appointment_services aps JOIN services s ON s.id = aps.service_id WHERE s.doctor_id = ? AND aps.appointment_id IN ({$placeholders})");
        $stmt->execute($params);
        while ($row = $stmt->fetch()) {
            $servicesByAppointment[(int)$row['appointment_id']][] = $row;
        }
    }

    $statusMap = [
        'PLANIFIE' => 'scheduled',
        'CONFIRME' => 'confirmed',
        'TERMINE' => 'completed',
        'ANNULE' => 'cancelled',
        'NO_SHOW' => 'noshow',
    ];

    $events = [];
    foreach ($appointments as $appt) {
        $services = $servicesByAppointment[$appt['id']] ?? [];
        $labels = [];
        $total = 0;
        foreach ($services as $srv) {
            $labels[] = $srv['name'] . ' x' . (int)$srv['qty'];
            $total += (float)$srv['default_price'] * (int)$srv['qty'];
        }
        $statusKey = $statusMap[$appt['status']] ?? 'scheduled';
        $events[] = [
            'id' => (int)$appt['id'],
            'patient_name' => $appt['patient_name'],
            'starts_at' => $appt['starts_at'],
            'ends_at' => $appt['ends_at'],
            'status' => $appt['status'],
            'status_label' => appointment_status_label($appt['status']),
            'reason' => $appt['reason'],
            'notes' => $appt['notes'],
            'service_labels' => $labels,
            'service_total' => $total,
            'service_total_formatted' => format_tnd($total),
            'color_token' => 'status-' . $statusKey,
        ];
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'range' => ['start' => $rangeStart, 'end' => $rangeEnd],
        'now' => date('Y-m-d H:i:s'),
        'events' => $events,
    ], JSON_UNESCAPED_UNICODE);
}

function calendar_event_json(): void {
    require_auth();
    $doctor = auth_user();
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    header('Content-Type: application/json; charset=utf-8');
    if ($id <= 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Rendez-vous introuvable.'], JSON_UNESCAPED_UNICODE);
        return;
    }

    $stmt = db()->prepare('SELECT a.id, a.patient_id, a.starts_at, a.ends_at, a.status, a.reason, a.notes, p.full_name AS patient_name FROM appointments a JOIN patients p ON p.id = a.patient_id WHERE a.doctor_id = ? AND a.id = ? LIMIT 1');
    $stmt->execute([$doctor['id'], $id]);
    $appt = $stmt->fetch();
    if (!$appt) {
        http_response_code(404);
        echo json_encode(['error' => 'Rendez-vous introuvable.'], JSON_UNESCAPED_UNICODE);
        return;
    }

    $stmt = db()->prepare('SELECT aps.qty, s.name, s.default_price FROM appointment_services aps JOIN services s ON s.id = aps.service_id WHERE s.doctor_id = ? AND aps.appointment_id = ?');
    $stmt->execute([$doctor['id'], $id]);
    $services = $stmt->fetchAll();
    $labels = [];
    $total = 0;
    foreach ($services as $srv) {
        $labels[] = $srv['name'] . ' x' . (int)$srv['qty'];
        $total += (float)$srv['default_price'] * (int)$srv['qty'];
    }

    echo json_encode([
        'id' => (int)$appt['id'],
        'patient_name' => $appt['patient_name'],
        'starts_at' => $appt['starts_at'],
        'ends_at' => $appt['ends_at'],
        'status' => $appt['status'],
        'status_label' => appointment_status_label($appt['status']),
        'reason' => $appt['reason'],
        'notes' => $appt['notes'],
        'service_labels' => $labels,
        'service_total' => $total,
        'service_total_formatted' => format_tnd($total),
    ], JSON_UNESCAPED_UNICODE);
}
