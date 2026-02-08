<?php
require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/services/notifications/NotificationSenderInterface.php';
require_once __DIR__ . '/../app/services/notifications/InfobipClient.php';
require_once __DIR__ . '/../app/services/notifications/SmsSender.php';
require_once __DIR__ . '/../app/services/notifications/WhatsAppSender.php';
require_once __DIR__ . '/../app/services/notifications/SmtpSender.php';

function outbox_fetch_jobs(): array {
    $stmt = db()->prepare("SELECT * FROM notifications_outbox WHERE status = 'PENDING' AND (next_retry_at IS NULL OR next_retry_at <= NOW()) ORDER BY id ASC LIMIT 50");
    $stmt->execute();
    return $stmt->fetchAll();
}

function load_doctor_settings(int $doctorId): array {
    $stmt = db()->prepare('SELECT * FROM doctor_notification_settings WHERE doctor_id = ?');
    $stmt->execute([$doctorId]);
    $settings = $stmt->fetch();
    if (!$settings) {
        return ['doctor_id' => $doctorId, 'infobip_base_url' => INFOBIP_BASE_URL];
    }
    if (empty($settings['infobip_base_url'])) {
        $settings['infobip_base_url'] = INFOBIP_BASE_URL;
    }
    return $settings;
}

function build_email_body(array $payload, int $doctorId): string {
    $message = $payload['message'] ?? '';
    $appointmentId = (int)($payload['appointment_id'] ?? 0);
    if ($appointmentId <= 0) {
        return $message;
    }
    $stmt = db()->prepare('SELECT a.starts_at, p.full_name AS patient_name FROM appointments a JOIN patients p ON p.id = a.patient_id WHERE a.id = ? AND a.doctor_id = ?');
    $stmt->execute([$appointmentId, $doctorId]);
    $row = $stmt->fetch();
    if (!$row) {
        return $message;
    }
    $date = date('d/m/Y', strtotime($row['starts_at']));
    $time = date('H:i', strtotime($row['starts_at']));
    return $message . "\n\nRDV: " . $date . ' à ' . $time . "\nPatient: " . ($row['patient_name'] ?? '');
}

function backoff_minutes(int $tries): int {
    if ($tries <= 1) {
        return 5;
    }
    if ($tries === 2) {
        return 30;
    }
    return 120;
}

$jobs = outbox_fetch_jobs();
$sent = 0;
$failed = 0;

foreach ($jobs as $job) {
    $jobId = (int)$job['id'];
    $doctorId = (int)$job['doctor_id'];
    $channel = $job['channel'];
    $payload = json_decode($job['payload_json'], true);

    if (!is_array($payload)) {
        log_error('Outbox: payload invalide pour id=' . $jobId);
        $failed++;
        db()->prepare("UPDATE notifications_outbox SET status = 'FAILED', last_error = ? WHERE id = ?")
            ->execute(['Payload invalide', $jobId]);
        continue;
    }

    try {
        $settings = load_doctor_settings($doctorId);
        $response = null;

        if ($channel === 'SMS') {
            $sender = new SmsSender();
            $response = $sender->send([
                'base_url' => $settings['infobip_base_url'] ?? INFOBIP_BASE_URL,
                'api_key' => decrypt_secret($settings['infobip_api_key_enc'] ?? ''),
                'sender' => $settings['infobip_sms_sender'] ?? '',
                'to' => $job['to_address'],
                'message' => $payload['message'] ?? '',
            ]);
        } elseif ($channel === 'WHATSAPP') {
            $sender = new WhatsAppSender();
            $response = $sender->send([
                'base_url' => $settings['infobip_base_url'] ?? INFOBIP_BASE_URL,
                'api_key' => decrypt_secret($settings['infobip_api_key_enc'] ?? ''),
                'sender' => $settings['infobip_whatsapp_sender'] ?? '',
                'to' => $job['to_address'],
                'template_name' => $payload['template_name'] ?? '',
                'language' => $payload['language_code'] ?? 'fr',
                'template_params' => $payload['template_params'] ?? [$payload['message'] ?? ''],
            ]);
        } elseif ($channel === 'EMAIL') {
            $sender = new SmtpSender();
            $response = $sender->send([
                'host' => $settings['smtp_host'] ?? '',
                'port' => $settings['smtp_port'] ?? 0,
                'secure' => $settings['smtp_secure'] ?? 'tls',
                'username' => $settings['smtp_username'] ?? '',
                'password' => decrypt_secret($settings['smtp_password_enc'] ?? ''),
                'from_email' => $settings['smtp_from_email'] ?? '',
                'from_name' => $settings['smtp_from_name'] ?? '',
                'to' => $job['to_address'],
                'subject' => $payload['subject'] ?? 'Rappel de rendez-vous',
                'body' => build_email_body($payload, $doctorId),
            ]);
        } else {
            throw new RuntimeException('Canal inconnu: ' . $channel);
        }

        if (!empty($response['success'])) {
            db()->prepare("UPDATE notifications_outbox SET status = 'SENT', external_id = ?, last_error = NULL WHERE id = ?")
                ->execute([$response['external_id'], $jobId]);
            if (!empty($job['reminder_log_id'])) {
                db()->prepare("UPDATE reminders_log SET status = 'SENT', sent_at = NOW(), error_text = NULL WHERE id = ? AND doctor_id = ?")
                    ->execute([(int)$job['reminder_log_id'], $doctorId]);
            }
            $sent++;
        } else {
            $error = $response['error'] ?? 'Erreur envoi.';
            throw new RuntimeException($error);
        }
    } catch (Throwable $ex) {
        $tries = (int)$job['tries'] + 1;
        $status = $tries >= 3 ? 'FAILED' : 'PENDING';
        $nextRetry = null;
        if ($status === 'PENDING') {
            $minutes = backoff_minutes($tries);
            $nextRetry = date('Y-m-d H:i:s', strtotime('+' . $minutes . ' minutes'));
        }
        db()->prepare("UPDATE notifications_outbox SET status = ?, tries = ?, last_error = ?, next_retry_at = ? WHERE id = ?")
            ->execute([$status, $tries, $ex->getMessage(), $nextRetry, $jobId]);
        if ($status === 'FAILED') {
            if (!empty($job['reminder_log_id'])) {
                db()->prepare("UPDATE reminders_log SET status = 'FAILED', error_text = ? WHERE id = ? AND doctor_id = ?")
                    ->execute([$ex->getMessage(), (int)$job['reminder_log_id'], $doctorId]);
            }
        }
        log_error('Notification failed id=' . $jobId . ' error=' . $ex->getMessage());
        $failed++;
    }
}

echo "[OK] Jobs traites: " . count($jobs) . "\n";
echo "[OK] Envoyes: " . $sent . "\n";
echo "[OK] Echecs: " . $failed . "\n";
exit(0);
