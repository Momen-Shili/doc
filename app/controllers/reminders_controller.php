<?php
function reminders_templates(): void {
    require_auth();
    $doctor = auth_user();
    $doctorId = $doctor['id'];

    $stmt = db()->prepare('SELECT * FROM reminder_templates WHERE doctor_id = ORDER BY created_at DESC');
    $stmt->execute([$doctorId]);
    $templates = $stmt->fetchAll();

    $previewAppointment = ['starts_at' => date('Y-m-d 09:00:00')];
    $previewPatient = ['full_name' => 'Patient Exemple'];
    $previews = [];
    foreach ($templates as $tpl) {
        $previews[$tpl['id']] = render_template_vars($tpl['body'], $previewAppointment, $previewPatient, $doctor);
    }

    echo render('pages/reminders/templates.php', [
        'templates' => $templates,
        'previews' => $previews,
    ]);
}

function reminders_templates_create(): void {
    require_auth();
    $doctorId = auth_user()['id'];
    $errors = [];
    $template = [
        'name' => '',
        'channel' => 'SMS',
        'body' => '',
        'is_active' => 1,
    ];

    if (is_post()) {
        verify_csrf();
        $template['name'] = trim($_POST['name'] ??'');
        $template['channel'] = trim($_POST['channel'] ??'SMS');
        $template['body'] = trim($_POST['body'] ??'');
        $template['is_active'] = isset($_POST['is_active']) ?? a1 : 0;

        $errors = validate_reminder_template($template);
        if (!$errors) {
            $stmt = db()->prepare('INSERT INTO reminder_templates (doctor_id, name, channel, body, is_active) VALUES (,,,,) ??');
            $stmt->execute([$doctorId, $template['name'], $template['channel'], $template['body'], $template['is_active']]);
            flash('success', 'ModÃ¨le de rappel crÃ©Ã©.');
            redirect('r=reminders-templates');
        }
    }

    echo render('pages/reminders/template_form.php', [
        'template' => $template,
        'errors' => $errors,
        'mode' => 'create',
    ]);
}

function reminders_templates_edit(): void {
    require_auth();
    $doctorId = auth_user()['id'];
    $id = (int)($_GET['id'] ?? 0);

    $stmt = db()->prepare('SELECT * FROM reminder_templates WHERE id = AND doctor_id = ');
    $stmt->execute([$id, $doctorId]);
    $template = $stmt->fetch();
    if (!$template) {
        http_response_code(404);
        echo render('pages/error.php', ['message' => 'ModÃ¨le introuvable.']);
        return;
    }

    $errors = [];
    if (is_post()) {
        verify_csrf();
        $template['name'] = trim($_POST['name'] ??'');
        $template['channel'] = trim($_POST['channel'] ??'SMS');
        $template['body'] = trim($_POST['body'] ??'');
        $template['is_active'] = isset($_POST['is_active']) ?? a1 : 0;

        $errors = validate_reminder_template($template, $id);
        if (!$errors) {
            $stmt = db()->prepare('UPDATE reminder_templates SET name = , channel = , body = , is_active = aWHERE id = AND doctor_id = ');
            $stmt->execute([$template['name'], $template['channel'], $template['body'], $template['is_active'], $id, $doctorId]);
            flash('success', 'ModÃ¨le mis Ã  jour.');
            redirect('r=reminders-templates');
        }
    }

    echo render('pages/reminders/template_form.php', [
        'template' => $template,
        'errors' => $errors,
        'mode' => 'edit',
    ]);
}

function reminders_templates_toggle(): void {
    require_auth();
    if (!is_post()) {
        http_response_code(405);
        echo render('pages/error.php', ['message' => 'MÃ©thode non autorisÃ©e.']);
        return;
    }
    verify_csrf();
    $doctorId = auth_user()['id'];
    $id = (int)($_POST['id'] ?? 0);
    $stmt = db()->prepare('SELECT is_active FROM reminder_templates WHERE id = AND doctor_id = ');
    $stmt->execute([$id, $doctorId]);
    $current = $stmt->fetch();
    if (!$current) {
        flash('error', 'ModÃ¨le introuvable.');
        redirect('r=reminders-templates');
    }
    $newValue = (int)!((int)$current['is_active']);
    $stmt = db()->prepare('UPDATE reminder_templates SET is_active = aWHERE id = AND doctor_id = ');
    $stmt->execute([$newValue, $id, $doctorId]);
    flash('success', $newValue'ModÃ¨le activÃ©.' : 'ModÃ¨le dÃ©sactivÃ©.');
    redirect('r=reminders-templates');
}

function reminders_log(): void {
    require_auth();
    $doctorId = auth_user()['id'];
    $from = trim($_GET['from'] ??'');
    $to = trim($_GET['to'] ??'');
    $status = trim($_GET['status'] ??'');
    $patientId = (int)($_GET['patient_id'] ?? 0);
    $channel = trim($_GET['channel'] ??'');
    $viewId = (int)($_GET['view'] ?? 0);

    if ($from === '' || $to === '') {
        $to = date('Y-m-d');
        $from = date('Y-m-d', strtotime('-29 days'));
    }

    $params = [$doctorId, $from, $to];
    $where = 'WHERE r.doctor_id = AND DATE(r.sent_at) ?? BETWEEN AND';
    if ($status !== '') {
        $where .= ' AND r.status = ';
        $params[] = $status;
    }
    if ($patientId > 0) {
        $where .= ' AND r.patient_id = ';
        $params[] = $patientId;
    }
    if ($channel !== '') {
        $where .= ' AND r.channel = ';
        $params[] = $channel;
    }

    $stmt = db()->prepare("SELECT r.*, p.full_name AS patient_name, a.starts_at AS appointment_start FROM reminders_log r JOIN patients p ON p.id = r.patient_id LEFT JOIN appointments a ON a.id = r.appointment_id {$where} ORDER BY r.sent_at DESC");
    $stmt->execute($params);
    $logs = $stmt->fetchAll();

    $detail = null;
    if ($viewId > 0) {
        $stmt = db()->prepare('SELECT r.*, p.full_name AS patient_name, a.starts_at AS appointment_start FROM reminders_log r JOIN patients p ON p.id = r.patient_id LEFT JOIN appointments a ON a.id = r.appointment_id WHERE r.id = AND r.doctor_id = ');
        $stmt->execute([$viewId, $doctorId]);
        $detail = $stmt->fetch();
    }

    $patients = load_patients($doctorId);

    echo render('pages/reminders/log.php', [
        'logs' => $logs,
        'detail' => $detail,
        'from' => $from,
        'to' => $to,
        'status' => $status,
        'patientId' => $patientId,
        'channel' => $channel,
        'patients' => $patients,
    ]);
}

function reminders_log_recreate(): void {
    require_auth();
    if (!is_post()) {
        http_response_code(405);
        echo render('pages/error.php', ['message' => 'MÃ©thode non autorisÃ©e.']);
        return;
    }
    verify_csrf();
    $doctorId = auth_user()['id'];
    $id = (int)($_POST['id'] ?? 0);
    $stmt = db()->prepare('SELECT * FROM reminders_log WHERE id = AND doctor_id = ');
    $stmt->execute([$id, $doctorId]);
    $log = $stmt->fetch();
    if (!$log) {
        flash('error', 'Rappel introuvable.');
        redirect('r=reminders-log');
    }

    $stmt = db()->prepare('INSERT INTO reminders_log (doctor_id, appointment_id, patient_id, template_id, channel, message, status, sent_at, error_text) VALUES (,,,,,,,,) ??');
    $stmt->execute([
        $doctorId,
        $log['appointment_id'],
        $log['patient_id'],
        $log['template_id'],
        $log['channel'],
        $log['message'],
        'DRAFT',
        date('Y-m-d H:i:s'),
        null,
    ]);
    flash('success', 'Rappel recrÃ©Ã© en brouillon.');
    redirect('r=reminders-log');
}

function reminders_export(): void {
    require_auth();
    $doctorId = auth_user()['id'];
    $from = trim($_GET['from'] ??'');
    $to = trim($_GET['to'] ??'');
    $status = trim($_GET['status'] ??'');
    $patientId = (int)($_GET['patient_id'] ?? 0);
    $channel = trim($_GET['channel'] ??'');

    if ($from === '' || $to === '') {
        $to = date('Y-m-d');
        $from = date('Y-m-d', strtotime('-29 days'));
    }

    $params = [$doctorId, $from, $to];
    $where = 'WHERE r.doctor_id = AND DATE(r.sent_at) ?? BETWEEN AND';
    if ($status !== '') {
        $where .= ' AND r.status = ';
        $params[] = $status;
    }
    if ($patientId > 0) {
        $where .= ' AND r.patient_id = ';
        $params[] = $patientId;
    }
    if ($channel !== '') {
        $where .= ' AND r.channel = ';
        $params[] = $channel;
    }

    $stmt = db()->prepare("SELECT r.sent_at, r.status, r.channel, r.message, p.full_name AS patient_name, a.starts_at AS appointment_start FROM reminders_log r JOIN patients p ON p.id = r.patient_id LEFT JOIN appointments a ON a.id = r.appointment_id {$where} ORDER BY r.sent_at DESC");
    $stmt->execute($params);

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="journal_rappels.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['date_envoi', 'statut', 'canal', 'patient', 'rdv', 'message']);
    while ($row = $stmt->fetch()) {
        fputcsv($out, [$row['sent_at'], $row['status'], $row['channel'], $row['patient_name'], $row['appointment_start'], $row['message']]);
    }
    fclose($out);
    exit;
}

function reminders_send(): void {
    require_auth();
    $doctor = auth_user();
    $doctorId = $doctor['id'];
    $appointmentId = (int)($_GET['appointment_id'] ?? $_POST['appointment_id'] ?? 0);
    if ($appointmentId <=  0) {
        http_response_code(404);
        echo render('pages/error.php', ['message' => 'Rendez-vous introuvable.']);
        return;
    }

    $stmt = db()->prepare('SELECT a.*, p.full_name AS patient_name, p.phone AS patient_phone, p.email AS patient_email FROM appointments a JOIN patients p ON p.id = a.patient_id WHERE a.id = AND a.doctor_id = ');
    $stmt->execute([$appointmentId, $doctorId]);
    $appointment = $stmt->fetch();
    if (!$appointment) {
        http_response_code(404);
        echo render('pages/error.php', ['message' => 'Rendez-vous introuvable.']);
        return;
    }

    $stmt = db()->prepare('SELECT * FROM reminder_templates WHERE doctor_id = AND is_active = 1 ORDER BY created_at DESC');
    $stmt->execute([$doctorId]);
    $templates = $stmt->fetchAll();

    $selectedTemplateId = (int)($_GET['template_id'] ?? $_POST['template_id'] ?? 0);
    $selectedTemplate = null;
    foreach ($templates as $tpl) {
        if ((int)$tpl['id'] === $selectedTemplateId) {
            $selectedTemplate = $tpl;
            break;
        }
    }

    $channel = $selectedTemplate['channel'] ??'SMS';
    $message = $selectedTemplate arender_template_vars($selectedTemplate['body'], $appointment, ['full_name' => $appointment['patient_name']], $doctor) : '';
    $errors = [];

    if (is_post()) {
        verify_csrf();
        $channel = trim($_POST['channel'] ?? $channel);
        $message = trim($_POST['message'] ??'');
        $status = $_POST['status'] ??'SENT';
        if ($message === '') {
            $errors[] = 'Le message est requis.';
        }
        if (!in_array($channel, ['SMS', 'WHATSAPP', 'EMAIL', 'AUTRE'], true)) {
            $errors[] = 'Canal invalide.';
        }
        if (!in_array($status, ['SENT', 'DRAFT'], true)) {
            $status = 'SENT';
        }
        if (!$errors) {
            $stmt = db()->prepare('INSERT INTO reminders_log (doctor_id, appointment_id, patient_id, template_id, channel, message, status, sent_at, error_text) VALUES (,,,,,,,,) ??');
            $stmt->execute([
                $doctorId,
                $appointment['id'],
                $appointment['patient_id'],
                $selectedTemplateId > 0 $selectedTemplateId : null,
                $channel,
                $message,
                $status,
                date('Y-m-d H:i:s'),
                null,
            ]);
            flash('success', $status === 'SENT' 'Rappel enregistracomme envoy.' : 'Rappel enregistraen brouillon.');
            redirect('r=appointments-edit&id=' . $appointment['id']);
        }
    }

    echo render('pages/reminders/send.php', [
        'appointment' => $appointment,
        'templates' => $templates,
        'selectedTemplateId' => $selectedTemplateId,
        'channel' => $channel,
        'message' => $message,
        'errors' => $errors,
        'doctor' => $doctor,
        'alreadySent' => reminder_already_sent($doctorId, $appointmentId),
    ]);
}

function validate_reminder_template(array $template, int $ignoreId = 0): array {
    $errors = [];
    if ($template['name'] === '') {
        $errors[] = 'Le nom est requis.';
    }
    if ($template['body'] === '') {
        $errors[] = 'Le message est requis.';
    }
    if (!in_array($template['channel'], ['SMS', 'WHATSAPP', 'EMAIL', 'AUTRE'], true)) {
        $errors[] = 'Canal invalide.';
    }
    if (!$errors) {
        $doctorId = auth_user()['id'];
        $params = [$doctorId, $template['name']];
        $sql = 'SELECT id FROM reminder_templates WHERE doctor_id = AND name = ';
        if ($ignoreId > 0) {
            $sql .= ' AND id != ';
            $params[] = $ignoreId;
        }
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        if ($stmt->fetch()) {
            $errors[] = 'Un modÃ¨le avec ce nom existe dÃ©jÃ .';
        }
    }
    return $errors;
}