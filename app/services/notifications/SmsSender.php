<?php
require_once __DIR__ . '/NotificationSenderInterface.php';
require_once __DIR__ . '/InfobipClient.php';

class SmsSender implements NotificationSenderInterface {
    public function send(array $job): array {
        $baseUrl = $job['base_url'] ?? INFOBIP_BASE_URL;
        $apiKey = $job['api_key'] ?? '';
        $sender = $job['sender'] ?? '';
        $to = $job['to'] ?? '';
        $text = $job['message'] ?? '';

        if ($apiKey === '' || $sender === '' || $to === '' || $text === '') {
            return ['success' => false, 'external_id' => null, 'error' => 'Parametres SMS incomplets.', 'raw' => null];
        }

        $payload = [
            'messages' => [
                [
                    'from' => $sender,
                    'destinations' => [['to' => $to]],
                    'text' => $text,
                ],
            ],
        ];
        $resp = infobip_request($baseUrl, $apiKey, '/sms/2/text/advanced', $payload);
        if (!$resp['success']) {
            return ['success' => false, 'external_id' => null, 'error' => $resp['error'], 'raw' => $resp['data']];
        }
        $messageId = $resp['data']['messages'][0]['messageId'] ?? null;
        $groupId = $resp['data']['messages'][0]['status']['groupId'] ?? null;
        if ($groupId !== null && (int)$groupId !== 1) {
            return ['success' => false, 'external_id' => $messageId, 'error' => 'SMS refuse par Infobip.', 'raw' => $resp['data']];
        }
        return ['success' => true, 'external_id' => $messageId, 'error' => null, 'raw' => $resp['data']];
    }
}
