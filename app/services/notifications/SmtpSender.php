<?php
require_once __DIR__ . '/NotificationSenderInterface.php';

class SmtpSender implements NotificationSenderInterface {
    public function send(array $job): array {
        $host = $job['host'] ?? '';
        $port = (int)($job['port'] ?? 0);
        $secure = $job['secure'] ?? 'tls';
        $username = $job['username'] ?? '';
        $password = $job['password'] ?? '';
        $fromEmail = $job['from_email'] ?? '';
        $fromName = $job['from_name'] ?? '';
        $toEmail = $job['to'] ?? '';
        $subject = $job['subject'] ?? 'Rappel de rendez-vous';
        $body = $job['body'] ?? '';

        if ($host === '' || $port <= 0 || $username === '' || $password === '' || $fromEmail === '' || $toEmail === '' || $body === '') {
            return ['success' => false, 'external_id' => null, 'error' => 'Parametres SMTP incomplets.', 'raw' => null];
        }

        $socketHost = $host;
        if ($secure === 'ssl') {
            $socketHost = 'ssl://' . $host;
        }

        $fp = @fsockopen($socketHost, $port, $errno, $errstr, 15);
        if (!$fp) {
            return ['success' => false, 'external_id' => null, 'error' => 'Connexion SMTP echouee: ' . $errstr, 'raw' => null];
        }

        $this->expect($fp, 220);
        $this->sendLine($fp, 'EHLO localhost');
        $this->expect($fp, 250);

        if ($secure === 'tls') {
            $this->sendLine($fp, 'STARTTLS');
            $this->expect($fp, 220);
            if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                fclose($fp);
                return ['success' => false, 'external_id' => null, 'error' => 'TLS non disponible.', 'raw' => null];
            }
            $this->sendLine($fp, 'EHLO localhost');
            $this->expect($fp, 250);
        }

        $this->sendLine($fp, 'AUTH LOGIN');
        $this->expect($fp, 334);
        $this->sendLine($fp, base64_encode($username));
        $this->expect($fp, 334);
        $this->sendLine($fp, base64_encode($password));
        $this->expect($fp, 235);

        $this->sendLine($fp, 'MAIL FROM:<' . $fromEmail . '>');
        $this->expect($fp, 250);
        $this->sendLine($fp, 'RCPT TO:<' . $toEmail . '>');
        $this->expect($fp, 250);
        $this->sendLine($fp, 'DATA');
        $this->expect($fp, 354);

        $headers = [];
        $headers[] = 'From: ' . ($fromName ? $fromName . ' <' . $fromEmail . '>' : $fromEmail);
        $headers[] = 'To: ' . $toEmail;
        $headers[] = 'Subject: ' . $subject;
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';

        $data = implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.";
        $this->sendLine($fp, $data);
        $this->expect($fp, 250);

        $this->sendLine($fp, 'QUIT');
        fclose($fp);

        return ['success' => true, 'external_id' => null, 'error' => null, 'raw' => null];
    }

    private function sendLine($fp, string $line): void {
        fwrite($fp, $line . "\r\n");
    }

    private function expect($fp, int $code): void {
        $line = '';
        while (($str = fgets($fp, 515)) !== false) {
            $line = $str;
            if (isset($str[3]) && $str[3] === ' ') {
                break;
            }
        }
        if ((int)substr($line, 0, 3) !== $code) {
            throw new RuntimeException('SMTP erreur: ' . trim($line));
        }
    }
}
