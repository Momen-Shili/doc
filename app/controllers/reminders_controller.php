<?php
function reminders_templates(): void {
    require_auth();
    $doctor = auth_user();
    $doctorId = $doctor['id'];

    $stmt = db()->prepare('SELECT t.*, w.template_name AS wa_template_name, w.language_code AS wa_language_code FROM reminder_templates t LEFT JOIN whatsapp_template_map w ON w.reminder_template_id = t.id AND w.doctor_id = ? WHERE t.doctor_id = ? ORDER BY t.created_at DESC');
    $stmt->execute([$doctorId, $doctorId]);
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
    $waMap = ['template_name' => '', 'language_code' => 'fr'];

    if (is_post()) {
        verify_csrf();
        $template['name'] = trim($_POST['name'] ?? '');
        $template['channel'] = trim($_POST['channel'] ?? 'SMS');
        $template['body'] = trim($_POST['body'] ?? '');
        $template['is_active'] = isset($_POST['is_active']) ? 1 : 0;
        $waMap['template_name'] = trim($_POST['wa_template_name'] ?? '');
        $waMap['language_code'] = trim($_POST['wa_language_code'] ?? 'fr');

        $errors = validate_reminder_template($template);
        if (!$errors) {
            $stmt = db()->prepare('INSERT INTO reminder_templates (doctor_id, name, channel, body, is_active) VALUES (?,?,?,?,?)');
            $stmt->execute([$doctorId, $template['name'], $template['channel'], $template['body'], $template['is_active']]);
            $templateId = (int)db()->lastInsertId();
            upsert_whatsapp_map($doctorId, $templateId, $waMap['template_name'], $waMap['language_code']);
            flash('success', 'Modèle de rappel créé.');
            redirect('?r=reminders-templates');
        }
    }

    echo render('pages/reminders/template_form.php', [
        'template' => $template,
        'waMap' => $waMap,
        'errors' => $errors,
        'mode' => 'create',
    ]);
}

function reminders_templates_edit(): void {
    require_auth();
    $doctorId = auth_user()['id'];
    $id = (int)($_GET['id'] ?? 0);

    $stmt = db()->prepare('SELECT t.*, w.template_name AS wa_template_name, w.language_code AS wa_language_code FROM reminder_templates t LEFT JOIN whatsapp_template_map w ON w.reminder_template_id = t.id AND w.doctor_id = ? WHERE t.id = ? AND t.doctor_id = ?');
    $stmt->execute([$doctorId, $id, $doctorId]);
    $template = $stmt->fetch();
    if (!$template) {
        http_response_code(404);
        echo render('pages/error.php', ['message' => 'Modèle introuvable.']);
        return;
    }

    $errors = [];
    $waMap = [
        'template_name' => $template['wa_template_name'] ?? '',
        'language_code' => $template['wa_language_code'] ?? 'fr',
    ];
    if (is_post()) {
        verify_csrf();
        $template['name'] = trim($_POST['name'] ?? '');
        $template['channel'] = trim($_POST['channel'] ?? 'SMS');
        $template['body'] = trim($_POST['body'] ?? '');
        $template['is_active'] = isset($_POST['is_active']) ? 1 : 0;
        $waMap['template_name'] = trim($_POST['wa_template_name'] ?? '');
        $waMap['language_code'] = trim($_POST['wa_language_code'] ?? 'fr');

        $errors = validate_reminder_template($template, $id);
        if (!$errors) {
            $stmt = db()->prepare('UPDATE reminder_templates SET name = ?, channel = ?, body = ?, is_active = ? WHERE id = ? AND doctor_id = ?');
            $stmt->execute([$template['name'], $template['channel'], $template['body'], $template['is_active'], $id, $doctorId]);
            upsert_whatsapp_map($doctorId, $id, $waMap['template_name'], $waMap['language_code']);
            flash('success', 'Modèle mis à jour.');
            redirect('?r=reminders-templates');
        }
    }

    echo render('pages/reminders/template_form.php', [
        'template' => $template,
        'waMap' => $waMap,
        'errors' => $errors,
        'mode' => 'edit',
    ]);
}

function reminders_templates_toggle(): void {
    require_auth();
    if (!is_post()) {
        http_response_code(405);
        echo render('pages/error.php', ['message' => 'Méthode non autorisée.']);
        return;
    }
    verify_csrf();
    $doctorId = auth_user()['id'];
    $id = (int)($_POST['id'] ?? 0);
    $stmt = db()->prepare('SELECT is_active FROM reminder_templates WHERE id = ? AND doctor_id = ?');
    $stmt->execute([$id, $doctorId]);
    $current = $stmt->fetch();
    if (!$current) {
        flash('error', 'Modèle introuvable.');
        redirect('?r=reminders-templates');
    }
    $newValue = (int)!((int)$current['is_active']);
    $stmt = db()->prepare('UPDATE reminder_templates SET is_active = ? WHERE id = ? AND doctor_id = ?');
    $stmt->execute([$newValue, $id, $doctorId]);
    flash('success', $newValue ? 'Modèle activé.' : 'Modèle désactivé.');
    redirect('?r=reminders-templates');
}

function reminders_log(): void {
    require_auth();
    $doctorId = auth_user()['id'];
    $from = trim($_GET['from'] ?? '');
    $to = trim($_GET['to'] ?? '');
    $status = trim($_GET['status'] ?? '');
    $patientId = (int)($_GET['patient_id'] ?? 0);
    $channel = trim($_GET['channel'] ?? '');
    $viewId = (int)($_GET['view'] ?? 0);

    if ($from === '' || $to === '') {
        $to = date('Y-m-d');
        $from = date('Y-m-d', strtotime('-29 days'));
    }

    $params = [$doctorId, $from, $to];
    $where = 'WHERE r.doctor_id = ? AND DATE(r.sent_at) BETWEEN ? AND ?';
    if ($status !== '') {
        $where .= ' AND r.status = ?';
        $params[] = $status;
    }
    if ($patientId > 0) {
        $where .= ' AND r.patient_id = ?';
        $params[] = $patientId;
    }
    if ($channel !== '') {
        $where .= ' AND r.channel = ?';
        $params[] = $channel;
    }

    $stmt = db()->prepare("SELECT r.*, p.full_name AS patient_name, a.starts_at AS appointment_start FROM reminders_log r JOIN patients p ON p.id = r.patient_id LEFT JOIN appointments a ON a.id = r.appointment_id {$where} ORDER BY r.sent_at DESC");
    $stmt->execute($params);
    $logs = $stmt->fetchAll();

    $detail = null;
    if ($viewId > 0) {
        $stmt = db()->prepare('SELECT r.*, p.full_name AS patient_name, a.starts_at AS appointment_start FROM reminders_log r JOIN patients p ON p.id = r.patient_id LEFT JOIN appointments a ON a.id = r.appointment_id WHERE r.id = ? AND r.doctor_id = ?');
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
        echo render('pages/error.php', ['message' => 'Méthode non autorisée.']);
        return;
    }
    verify_csrf();
    $doctorId = auth_user()['id'];
    $id = (int)($_POST['id'] ?? 0);
    $stmt = db()->prepare('SELECT * FROM reminders_log WHERE id = ? AND doctor_id = ?');
    $stmt->execute([$id, $doctorId]);
    $log = $stmt->fetch();
    if (!$log) {
        flash('error', 'Rappel introuvable.');
        redirect('?r=reminders-log');
    }

    $stmt = db()->prepare('INSERT INTO reminders_log (doctor_id, appointment_id, patient_id, template_id, channel, message, status, sent_at, error_text) VALUES (?,?,?,?,?,?,?,?,?)');
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
    flash('success', 'Rappel recréé en brouillon.');
    redirect('?r=reminders-log');
}

function reminders_export(): void {
    require_auth();
    $doctorId = auth_user()['id'];
    $from = trim($_GET['from'] ?? '');
    $to = trim($_GET['to'] ?? '');
    $status = trim($_GET['status'] ?? '');
    $patientId = (int)($_GET['patient_id'] ?? 0);
    $channel = trim($_GET['channel'] ?? '');

    if ($from === '' || $to === '') {
        $to = date('Y-m-d');
        $from = date('Y-m-d', strtotime('-29 days'));
    }

    $params = [$doctorId, $from, $to];
    $where = 'WHERE r.doctor_id = ? AND DATE(r.sent_at) BETWEEN ? AND ?';
    if ($status !== '') {
        $where .= ' AND r.status = ?';
        $params[] = $status;
    }
    if ($patientId > 0) {
        $where .= ' AND r.patient_id = ?';
        $params[] = $patientId;
    }
    if ($channel !== '') {
        $where .= ' AND r.channel = ?';
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
    $appointmentId = (int)($_GET['appointment_id'] ?? ($_POST['appointment_id'] ?? 0));
    if ($appointmentId <= 0) {
        http_response_code(404);
        echo render('pages/error.php', ['message' => 'Rendez-vous introuvable.']);
        return;
    }

    $stmt = db()->prepare('SELECT a.*, p.full_name AS patient_name, p.phone AS patient_phone, p.email AS patient_email, p.whatsapp_phone, p.consent_email, p.consent_sms, p.consent_whatsapp FROM appointments a JOIN patients p ON p.id = a.patient_id WHERE a.id = ? AND a.doctor_id = ?');
    $stmt->execute([$appointmentId, $doctorId]);
    $appointment = $stmt->fetch();
    if (!$appointment) {
        http_response_code(404);
        echo render('pages/error.php', ['message' => 'Rendez-vous introuvable.']);
        return;
    }

    $stmt = db()->prepare('SELECT * FROM reminder_templates WHERE doctor_id = ? AND is_active = 1 ORDER BY created_at DESC');
    $stmt->execute([$doctorId]);
    $templates = $stmt->fetchAll();

    $selectedTemplateId = (int)($_GET['template_id'] ?? ($_POST['template_id'] ?? 0));
    $selectedTemplate = null;
    foreach ($templates as $tpl) {
        if ((int)$tpl['id'] === $selectedTemplateId) {
            $selectedTemplate = $tpl;
            break;
        }
    }

    $channel = $selectedTemplate['channel'] ?? 'SMS';
    $requestedChannel = trim($_GET['channel'] ?? '');
    if (in_array($requestedChannel, ['SMS', 'WHATSAPP', 'EMAIL'], true)) {
        $channel = $requestedChannel;
    }
    $message = $selectedTemplate ? render_template_vars($selectedTemplate['body'], $appointment, ['full_name' => $appointment['patient_name']], $doctor) : '';
    $errors = [];

    $settings = load_doctor_notification_settings($doctorId);
    $smtpConfigured = !empty($settings['smtp_host']) && !empty($settings['smtp_username']) && !empty($settings['smtp_password_enc']) && !empty($settings['smtp_from_email']);
    $smsConfigured = !empty($settings['infobip_api_key_enc']) && !empty($settings['infobip_sms_sender']);
    $waConfigured = !empty($settings['infobip_api_key_enc']) && !empty($settings['infobip_whatsapp_sender']);

    $waMap = null;
    if ($selectedTemplateId > 0) {
        $stmt = db()->prepare('SELECT * FROM whatsapp_template_map WHERE doctor_id = ? AND reminder_template_id = ?');
        $stmt->execute([$doctorId, $selectedTemplateId]);
        $waMap = $stmt->fetch();
    }

    if (is_post()) {
        verify_csrf();
        $channel = trim($_POST['channel'] ?? $channel);
        $message = trim($_POST['message'] ?? '');

        if ($message === '') {
            $errors[] = 'Le message est requis.';
        }
        if (!in_array($channel, ['SMS', 'WHATSAPP', 'EMAIL'], true)) {
            $errors[] = 'Canal invalide.';
        }

        $toAddress = '';
        $subject = 'Rappel de rendez-vous';

        if ($channel === 'EMAIL') {
            if (!$smtpConfigured) {
                $errors[] = 'Configuration email incomplète.';
            }
            if (empty($appointment['patient_email'])) {
                $errors[] = 'Email patient manquant.';
            }
            if (empty($appointment['consent_email'])) {
                $errors[] = 'Consentement email non accordé.';
            }
            $toAddress = $appointment['patient_email'] ?? '';
        } elseif ($channel === 'SMS') {
            if (!$smsConfigured) {
                $errors[] = 'Configuration SMS incomplète.';
            }
            if (empty($appointment['patient_phone'])) {
                $errors[] = 'Téléphone patient manquant.';
            }
            if (empty($appointment['consent_sms'])) {
                $errors[] = 'Consentement SMS non accordé.';
            }
            $toAddress = normalize_tn_phone($appointment['patient_phone'] ?? '') ?? '';
            if ($toAddress === '') {
                $errors[] = 'Numéro SMS invalide.';
            }
        } elseif ($channel === 'WHATSAPP') {
            if (!$waConfigured) {
                $errors[] = 'Configuration WhatsApp incomplète.';
            }
            if (empty($appointment['consent_whatsapp'])) {
                $errors[] = 'Consentement WhatsApp non accordé.';
            }
            $phone = $appointment['whatsapp_phone'] ?: $appointment['patient_phone'];
            if (empty($phone)) {
                $errors[] = 'Numéro WhatsApp manquant.';
            }
            $toAddress = normalize_tn_phone($phone ?? '') ?? '';
            if ($toAddress === '') {
                $errors[] = 'Numéro WhatsApp invalide.';
            }
            if (!$waMap || empty($waMap['template_name'])) {
                $errors[] = 'Template WhatsApp non configuré pour ce modèle.';
            }
        }

        if (!$errors) {
            $stmt = db()->prepare('INSERT INTO reminders_log (doctor_id, appointment_id, patient_id, template_id, channel, message, status, sent_at, error_text) VALUES (?,?,?,?,?,?,?,?,?)');
            $stmt->execute([
                $doctorId,
                $appointment['id'],
                $appointment['patient_id'],
                $selectedTemplateId > 0 ? $selectedTemplateId : null,
                $channel,
                $message,
                'DRAFT',
                date('Y-m-d H:i:s'),
                null,
            ]);
            $logId = (int)db()->lastInsertId();

            $payload = [
                'doctor_id' => $doctorId,
                'patient_id' => $appointment['patient_id'],
                'appointment_id' => $appointment['id'],
                'reminder_log_id' => $logId,
                'channel' => $channel,
                'message' => $message,
                'subject' => $subject,
            ];
            if ($channel === 'WHATSAPP' && $waMap) {
                $payload['template_name'] = $waMap['template_name'];
                $payload['language_code'] = $waMap['language_code'] ?? 'fr';
                $payload['template_params'] = [$message];
            }

            $stmt = db()->prepare('INSERT INTO notifications_outbox (doctor_id, reminder_log_id, channel, to_address, payload_json) VALUES (?,?,?,?,?)');
            $stmt->execute([
                $doctorId,
                $logId,
                $channel,
                $toAddress,
                json_encode($payload, JSON_UNESCAPED_UNICODE),
            ]);

            flash('success', 'Rappel mis en file d\'attente. Pour envoyer: php tools/worker_send_notifications.php');
            redirect('?r=appointments-edit&id=' . $appointment['id']);
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
        'smtpConfigured' => $smtpConfigured,
        'smsConfigured' => $smsConfigured,
        'waConfigured' => $waConfigured,
        'waMap' => $waMap,
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
    if (!in_array($template['channel'], ['SMS', 'WHATSAPP', 'EMAIL'], true)) {
        $errors[] = 'Canal invalide.';
    }
    if (!$errors) {
        $doctorId = auth_user()['id'];
        $params = [$doctorId, $template['name']];
        $sql = 'SELECT id FROM reminder_templates WHERE doctor_id = ? AND name = ?';
        if ($ignoreId > 0) {
            $sql .= ' AND id != ?';
            $params[] = $ignoreId;
        }
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        if ($stmt->fetch()) {
            $errors[] = 'Un modèle avec ce nom existe déjà.';
        }
    }
    return $errors;
}

function upsert_whatsapp_map(int $doctorId, int $templateId, string $templateName, string $languageCode): void {
    $templateName = trim($templateName);
    $languageCode = trim($languageCode) !== '' ? trim($languageCode) : 'fr';
    if ($templateName === '') {
        $stmt = db()->prepare('DELETE FROM whatsapp_template_map WHERE doctor_id = ? AND reminder_template_id = ?');
        $stmt->execute([$doctorId, $templateId]);
        return;
    }

    $stmt = db()->prepare('SELECT id FROM whatsapp_template_map WHERE doctor_id = ? AND reminder_template_id = ?');
    $stmt->execute([$doctorId, $templateId]);
    $existing = $stmt->fetch();
    if ($existing) {
        $stmt = db()->prepare('UPDATE whatsapp_template_map SET template_name = ?, language_code = ? WHERE id = ?');
        $stmt->execute([$templateName, $languageCode, $existing['id']]);
    } else {
        $stmt = db()->prepare('INSERT INTO whatsapp_template_map (doctor_id, reminder_template_id, template_name, language_code) VALUES (?,?,?,?)');
        $stmt->execute([$doctorId, $templateId, $templateName, $languageCode]);
    }
}

function load_doctor_notification_settings(int $doctorId): array {
    $stmt = db()->prepare('SELECT * FROM doctor_notification_settings WHERE doctor_id = ?');
    $stmt->execute([$doctorId]);
    $settings = $stmt->fetch();
    if (!$settings) {
        return [
            'doctor_id' => $doctorId,
            'infobip_base_url' => INFOBIP_BASE_URL,
        ];
    }
    if (empty($settings['infobip_base_url'])) {
        $settings['infobip_base_url'] = INFOBIP_BASE_URL;
    }
    return $settings;
}
