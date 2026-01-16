<?php
function notifications_settings(): void {
    require_auth();
    $doctor = auth_user();
    $doctorId = $doctor['id'];

    $settings = notifications_load_settings($doctorId);
    $hasSmtpPassword = !empty($settings['smtp_password_enc']);
    $hasInfobipKey = !empty($settings['infobip_api_key_enc']);

    $errors = [];
    if (is_post() && ($_POST['action'] ?? 'save') === 'save') {
        verify_csrf();
        $smtpHost = trim($_POST['smtp_host'] ?? '');
        $smtpPort = (int)($_POST['smtp_port'] ?? 0);
        $smtpSecure = trim($_POST['smtp_secure'] ?? 'tls');
        $smtpUsername = trim($_POST['smtp_username'] ?? '');
        $smtpPassword = trim($_POST['smtp_password'] ?? '');
        $smtpFromEmail = trim($_POST['smtp_from_email'] ?? '');
        $smtpFromName = trim($_POST['smtp_from_name'] ?? '');

        $infobipBaseUrl = trim($_POST['infobip_base_url'] ?? '') ?: INFOBIP_BASE_URL;
        $infobipApiKey = trim($_POST['infobip_api_key'] ?? '');
        $smsSender = trim($_POST['infobip_sms_sender'] ?? '');
        $waSender = trim($_POST['infobip_whatsapp_sender'] ?? '');

        if ($smtpSecure !== 'tls' && $smtpSecure !== 'ssl' && $smtpSecure !== 'none') {
            $smtpSecure = 'tls';
        }

        try {
            $smtpPasswordEnc = $settings['smtp_password_enc'] ?? null;
            if ($smtpPassword !== '') {
                $smtpPasswordEnc = encrypt_secret($smtpPassword);
            }

            $infobipApiKeyEnc = $settings['infobip_api_key_enc'] ?? null;
            if ($infobipApiKey !== '') {
                $infobipApiKeyEnc = encrypt_secret($infobipApiKey);
            }

            $stmt = db()->prepare('INSERT INTO doctor_notification_settings (doctor_id, smtp_host, smtp_port, smtp_secure, smtp_username, smtp_password_enc, smtp_from_email, smtp_from_name, infobip_base_url, infobip_api_key_enc, infobip_sms_sender, infobip_whatsapp_sender) VALUES (?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE smtp_host=VALUES(smtp_host), smtp_port=VALUES(smtp_port), smtp_secure=VALUES(smtp_secure), smtp_username=VALUES(smtp_username), smtp_password_enc=VALUES(smtp_password_enc), smtp_from_email=VALUES(smtp_from_email), smtp_from_name=VALUES(smtp_from_name), infobip_base_url=VALUES(infobip_base_url), infobip_api_key_enc=VALUES(infobip_api_key_enc), infobip_sms_sender=VALUES(infobip_sms_sender), infobip_whatsapp_sender=VALUES(infobip_whatsapp_sender)');
            $stmt->execute([
                $doctorId,
                $smtpHost !== '' ? $smtpHost : null,
                $smtpPort > 0 ? $smtpPort : null,
                $smtpSecure,
                $smtpUsername !== '' ? $smtpUsername : null,
                $smtpPasswordEnc,
                $smtpFromEmail !== '' ? $smtpFromEmail : null,
                $smtpFromName !== '' ? $smtpFromName : null,
                $infobipBaseUrl,
                $infobipApiKeyEnc,
                $smsSender !== '' ? $smsSender : null,
                $waSender !== '' ? $waSender : null,
            ]);

            flash('success', 'Paramètres de notifications mis à jour.');
            redirect('?r=notifications-settings');
        } catch (Throwable $ex) {
            $errors[] = 'Impossible d\'enregistrer les secrets. Vérifiez APP_KEY.';
        }
    }

    $templates = [];
    $stmt = db()->prepare('SELECT id, name FROM reminder_templates WHERE doctor_id = ? ORDER BY name');
    $stmt->execute([$doctorId]);
    $templates = $stmt->fetchAll();

    echo render('pages/notifications/settings.php', [
        'settings' => $settings,
        'hasSmtpPassword' => $hasSmtpPassword,
        'hasInfobipKey' => $hasInfobipKey,
        'doctor' => $doctor,
        'templates' => $templates,
        'errors' => $errors,
    ]);
}

function notifications_test_email(): void {
    require_auth();
    if (!is_post()) {
        http_response_code(405);
        echo render('pages/error.php', ['message' => 'Méthode non autorisée.']);
        return;
    }
    verify_csrf();
    $doctor = auth_user();
    $doctorId = $doctor['id'];

    $settings = notifications_load_settings($doctorId);
    if (empty($settings['smtp_host']) || empty($settings['smtp_username']) || empty($settings['smtp_password_enc']) || empty($settings['smtp_from_email'])) {
        flash('error', 'Configuration email incomplète.');
        redirect('?r=notifications-settings');
    }
    if (empty($doctor['email'])) {
        flash('error', 'Email médecin manquant.');
        redirect('?r=notifications-settings');
    }

    $message = 'Test email de rappel - ' . date('d/m/Y H:i');
    $logId = notifications_create_test_log($doctorId, $doctor, 'EMAIL', $message);
    $payload = [
        'doctor_id' => $doctorId,
        'patient_id' => null,
        'appointment_id' => null,
        'reminder_log_id' => $logId,
        'channel' => 'EMAIL',
        'message' => $message,
        'subject' => 'Test notification',
    ];
    $stmt = db()->prepare('INSERT INTO notifications_outbox (doctor_id, reminder_log_id, channel, to_address, payload_json) VALUES (?,?,?,?,?)');
    $stmt->execute([
        $doctorId,
        $logId,
        'EMAIL',
        $doctor['email'],
        json_encode($payload, JSON_UNESCAPED_UNICODE),
    ]);
    flash('success', 'Email test mis en file d\'attente.');
    redirect('?r=notifications-settings');
}

function notifications_test_sms(): void {
    require_auth();
    if (!is_post()) {
        http_response_code(405);
        echo render('pages/error.php', ['message' => 'Méthode non autorisée.']);
        return;
    }
    verify_csrf();
    $doctor = auth_user();
    $doctorId = $doctor['id'];

    $settings = notifications_load_settings($doctorId);
    if (empty($settings['infobip_api_key_enc']) || empty($settings['infobip_sms_sender'])) {
        flash('error', 'Configuration SMS incomplète.');
        redirect('?r=notifications-settings');
    }
    if (empty($doctor['phone'])) {
        flash('error', 'Téléphone médecin manquant.');
        redirect('?r=notifications-settings');
    }

    $to = normalize_tn_phone($doctor['phone']);
    if (!$to) {
        flash('error', 'Téléphone médecin invalide.');
        redirect('?r=notifications-settings');
    }

    $message = 'Test SMS de rappel - ' . date('d/m/Y H:i');
    $logId = notifications_create_test_log($doctorId, $doctor, 'SMS', $message);
    $payload = [
        'doctor_id' => $doctorId,
        'patient_id' => null,
        'appointment_id' => null,
        'reminder_log_id' => $logId,
        'channel' => 'SMS',
        'message' => $message,
    ];
    $stmt = db()->prepare('INSERT INTO notifications_outbox (doctor_id, reminder_log_id, channel, to_address, payload_json) VALUES (?,?,?,?,?)');
    $stmt->execute([
        $doctorId,
        $logId,
        'SMS',
        $to,
        json_encode($payload, JSON_UNESCAPED_UNICODE),
    ]);
    flash('success', 'SMS test mis en file d\'attente.');
    redirect('?r=notifications-settings');
}

function notifications_test_whatsapp(): void {
    require_auth();
    if (!is_post()) {
        http_response_code(405);
        echo render('pages/error.php', ['message' => 'Méthode non autorisée.']);
        return;
    }
    verify_csrf();
    $doctor = auth_user();
    $doctorId = $doctor['id'];
    $templateId = (int)($_POST['template_id'] ?? 0);

    $settings = notifications_load_settings($doctorId);
    if (empty($settings['infobip_api_key_enc']) || empty($settings['infobip_whatsapp_sender'])) {
        flash('error', 'Configuration WhatsApp incomplète.');
        redirect('?r=notifications-settings');
    }
    if (empty($doctor['phone'])) {
        flash('error', 'Téléphone médecin manquant.');
        redirect('?r=notifications-settings');
    }
    $to = normalize_tn_phone($doctor['phone']);
    if (!$to) {
        flash('error', 'Téléphone médecin invalide.');
        redirect('?r=notifications-settings');
    }
    if ($templateId <= 0) {
        flash('error', 'Sélectionnez un modèle pour le test WhatsApp.');
        redirect('?r=notifications-settings');
    }

    $stmt = db()->prepare('SELECT * FROM whatsapp_template_map WHERE doctor_id = ? AND reminder_template_id = ?');
    $stmt->execute([$doctorId, $templateId]);
    $waMap = $stmt->fetch();
    if (!$waMap || empty($waMap['template_name'])) {
        flash('error', 'Template WhatsApp non configuré pour ce modèle.');
        redirect('?r=notifications-settings');
    }

    $message = 'Test WhatsApp de rappel - ' . date('d/m/Y H:i');
    $logId = notifications_create_test_log($doctorId, $doctor, 'WHATSAPP', $message);
    $payload = [
        'doctor_id' => $doctorId,
        'patient_id' => null,
        'appointment_id' => null,
        'reminder_log_id' => $logId,
        'channel' => 'WHATSAPP',
        'message' => $message,
        'template_name' => $waMap['template_name'],
        'language_code' => $waMap['language_code'] ?? 'fr',
        'template_params' => [$message],
    ];
    $stmt = db()->prepare('INSERT INTO notifications_outbox (doctor_id, reminder_log_id, channel, to_address, payload_json) VALUES (?,?,?,?,?)');
    $stmt->execute([
        $doctorId,
        $logId,
        'WHATSAPP',
        $to,
        json_encode($payload, JSON_UNESCAPED_UNICODE),
    ]);
    flash('success', 'WhatsApp test mis en file d\'attente.');
    redirect('?r=notifications-settings');
}

function notifications_load_settings(int $doctorId): array {
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

function notifications_create_test_log(int $doctorId, array $doctor, string $channel, string $message): int {
    $patientId = notifications_get_or_create_test_patient($doctorId, $doctor);
    $appointmentId = notifications_get_or_create_test_appointment($doctorId, $patientId);

    $stmt = db()->prepare('INSERT INTO reminders_log (doctor_id, appointment_id, patient_id, template_id, channel, message, status, sent_at, error_text) VALUES (?,?,?,?,?,?,?,?,?)');
    $stmt->execute([
        $doctorId,
        $appointmentId,
        $patientId,
        null,
        $channel,
        $message,
        'DRAFT',
        date('Y-m-d H:i:s'),
        null,
    ]);
    return (int)db()->lastInsertId();
}

function notifications_get_or_create_test_patient(int $doctorId, array $doctor): int {
    $stmt = db()->prepare('SELECT id FROM patients WHERE doctor_id = ? AND full_name = ? LIMIT 1');
    $stmt->execute([$doctorId, 'Test Notifications']);
    $patient = $stmt->fetch();
    if ($patient) {
        return (int)$patient['id'];
    }

    $stmt = db()->prepare('INSERT INTO patients (doctor_id, full_name, phone, email, notes, consent_email, consent_sms, consent_whatsapp) VALUES (?,?,?,?,?,?,?,?)');
    $stmt->execute([
        $doctorId,
        'Test Notifications',
        $doctor['phone'] ?? null,
        $doctor['email'] ?? null,
        'Enregistrements de test pour les notifications.',
        1,
        1,
        1,
    ]);
    return (int)db()->lastInsertId();
}

function notifications_get_or_create_test_appointment(int $doctorId, int $patientId): int {
    $stmt = db()->prepare('SELECT id FROM appointments WHERE doctor_id = ? AND patient_id = ? AND reason = ? ORDER BY starts_at DESC LIMIT 1');
    $stmt->execute([$doctorId, $patientId, 'Test notifications']);
    $appointment = $stmt->fetch();
    if ($appointment) {
        return (int)$appointment['id'];
    }

    $start = date('Y-m-d H:i:s');
    $end = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    $stmt = db()->prepare('INSERT INTO appointments (doctor_id, patient_id, starts_at, ends_at, status, reason, notes) VALUES (?,?,?,?,?,?,?)');
    $stmt->execute([$doctorId, $patientId, $start, $end, 'CONFIRME', 'Test notifications', '']);
    return (int)db()->lastInsertId();
}
