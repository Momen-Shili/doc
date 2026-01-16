<?php
require_once __DIR__ . '/NotificationSenderInterface.php';
require_once __DIR__ . '/InfobipClient.php';

class WhatsAppSender implements NotificationSenderInterface {
    public function send(array $job): array {
        $baseUrl = $job['base_url'] ?? INFOBIP_BASE_URL;
        $apiKey = $job['api_key'] ?? '';
        $to = $job['to'] ?? '';
        $templateName = $job['template_name'] ?? '';
        $language = $job['language'] ?? 'fr';
        $params = $job['template_params'] ?? [];
        $from = $job['sender'] ?? '';

        if ($apiKey === '' || $to === '' || $templateName === '' || $from === '') {
            return ['success' => false, 'external_id' => null, 'error' => 'Parametres WhatsApp incomplets.', 'raw' => null];
        }

        $components = [];
        if ($params) {
            $parameters = [];
            foreach ($params as $value) {
                $parameters[] = ['type' => 'text', 'text' => (string)$value];
            }
            $components[] = ['type' => 'body', 'parameters' => $parameters];
        }

        $payload = [
            'from' => $from,
            'to' => $to,
            'template' => [
                'name' => $templateName,
                'language' => $language,
            ],
        ];
        if ($components) {
            $payload['template']['components'] = $components;
        }

        $resp = infobip_request($baseUrl, $apiKey, '/whatsapp/1/message/template', $payload);
        if (!$resp['success']) {
            return ['success' => false, 'external_id' => null, 'error' => $resp['error'], 'raw' => $resp['data']];
        }
        $messageId = $resp['data']['messages'][0]['messageId'] ?? null;
        return ['success' => true, 'external_id' => $messageId, 'error' => null, 'raw' => $resp['data']];
    }
}
