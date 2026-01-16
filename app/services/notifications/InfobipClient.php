<?php
function infobip_request(string $baseUrl, string $apiKey, string $path, array $payload): array {
    $url = rtrim($baseUrl, '/') . $path;
    $ch = curl_init($url);
    $body = json_encode($payload);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: App ' . $apiKey,
            'Content-Type: application/json',
            'Accept: application/json',
        ],
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_TIMEOUT => 20,
    ]);
    $response = curl_exec($ch);
    $err = curl_error($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false) {
        return ['success' => false, 'status' => $status, 'error' => $err ?: 'Erreur cURL', 'data' => null];
    }
    $data = json_decode($response, true);
    if ($status < 200 || $status >= 300) {
        return ['success' => false, 'status' => $status, 'error' => $data['requestError']['serviceException']['text'] ?? 'Erreur API', 'data' => $data];
    }
    return ['success' => true, 'status' => $status, 'error' => null, 'data' => $data];
}
