<?php
function dashboard_index(): void {
    require_auth();
    $doctor = auth_user();

    $today = date('Y-m-d');
    $monthStart = date('Y-m-01');
    $last14 = date('Y-m-d', strtotime('-13 days'));
    $weekStart = date('Y-m-d', strtotime('-6 days'));

    $view = $_GET['view'] ?? 'day';
    $date = $_GET['date'] ?? $today;
    $view = in_array($view, ['day', 'week', 'month'], true) ? $view : 'day';
    $dateTs = strtotime($date) ?: strtotime($today);

    $stmt = db()->prepare('SELECT COALESCE(SUM(amount),0) AS total FROM payments WHERE doctor_id = ? AND DATE(received_at) = ?');
    $stmt->execute([$doctor['id'], $today]);
    $todayRevenue = $stmt->fetch()['total'] ?? 0;

    $stmt = db()->prepare('SELECT COALESCE(SUM(amount),0) AS total FROM payments WHERE doctor_id = ? AND DATE(received_at) BETWEEN ? AND ?');
    $stmt->execute([$doctor['id'], $monthStart, date('Y-m-d')]);
    $monthRevenue = $stmt->fetch()['total'] ?? 0;

    $stmt = db()->prepare('SELECT DATE(received_at) AS day, SUM(amount) AS total FROM payments WHERE doctor_id = ? AND DATE(received_at) BETWEEN ? AND ? GROUP BY DATE(received_at) ORDER BY day');
    $stmt->execute([$doctor['id'], $last14, $today]);
    $last14Days = $stmt->fetchAll();

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

    $stmt = db()->prepare('SELECT a.*, p.full_name AS patient_name FROM appointments a JOIN patients p ON p.id = a.patient_id WHERE a.doctor_id = ? AND DATE(a.starts_at) = ? ORDER BY a.starts_at');
    $stmt->execute([$doctor['id'], $today]);
    $todayAppointments = $stmt->fetchAll();

    $rangeStart = $today;
    $rangeEnd = $today;
    if ($view === 'week') {
        $rangeStart = date('Y-m-d', strtotime('monday this week', $dateTs));
        $rangeEnd = date('Y-m-d', strtotime($rangeStart . ' +6 days'));
    } elseif ($view === 'month') {
        $rangeStart = date('Y-m-01', $dateTs);
        $rangeEnd = date('Y-m-t', $dateTs);
    }

    $stmt = db()->prepare('SELECT a.*, p.full_name AS patient_name FROM appointments a JOIN patients p ON p.id = a.patient_id WHERE a.doctor_id = ? AND DATE(a.starts_at) BETWEEN ? AND ? ORDER BY a.starts_at');
    $stmt->execute([$doctor['id'], $rangeStart, $rangeEnd]);
    $calendarAppointments = $stmt->fetchAll();

    $appointmentsByDate = [];
    $appointmentIds = [];
    foreach ($calendarAppointments as $appt) {
        $key = date('Y-m-d', strtotime($appt['starts_at']));
        $appointmentsByDate[$key][] = $appt;
        $appointmentIds[] = (int)$appt['id'];
    }

    $servicesByAppointment = [];
    if ($appointmentIds) {
        $placeholders = implode(',', array_fill(0, count($appointmentIds), '?'));
        $params = array_merge([$doctor['id']], $appointmentIds);
        $stmt = db()->prepare("SELECT aps.appointment_id, aps.qty, s.name, s.default_price FROM appointment_services aps JOIN services s ON s.id = aps.service_id WHERE s.doctor_id = ? AND aps.appointment_id IN ({$placeholders})");
        $stmt->execute($params);
        while ($row = $stmt->fetch()) {
            $servicesByAppointment[$row['appointment_id']][] = $row;
        }
    }

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
        'last14Days' => $last14Days,
        'todayAppointments' => $todayAppointments,
        'appointmentsToday' => $appointmentsToday,
        'cancellationsWeek' => $cancellationsWeek,
        'newPatientsMonth' => $newPatientsMonth,
        'avgTicket' => $avgTicket,
        'view' => $view,
        'date' => $date,
        'rangeStart' => $rangeStart,
        'rangeEnd' => $rangeEnd,
        'appointmentsByDate' => $appointmentsByDate,
        'servicesByAppointment' => $servicesByAppointment,
        'recentPatients' => $recentPatients,
        'miniRevenue' => $miniRevenue,
        'remindersDue' => $remindersDue,
        'notifConfig' => $notifConfig,
    ]);
}
