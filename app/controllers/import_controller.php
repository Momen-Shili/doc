<?php
function import_index(): void {
    require_auth();
    $doctorId = auth_user()['id'];
    $errors = [];

    if (is_post()) {
        verify_csrf();
        if (empty($_FILES['csv_file']['name'])) {
            $errors[] = 'Fichier CSV requis.';
        } else {
            $file = $_FILES['csv_file'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors[] = 'Erreur de téléchargement.';
            } else {
                $name = strtolower($file['name']);
                if (!preg_match('/^(patients|appointments|payments)\.csv$/', $name, $m)) {
                    $errors[] = 'Nom de fichier invalide. Utilisez patients.csv, appointments.csv ou payments.csv.';
                } else {
                    $type = $m[1];
                    $dir = __DIR__ . '/../../storage/uploads/doctor_' . $doctorId;
                    if (!is_dir($dir)) {
                        mkdir($dir, 0775, true);
                    }
                    $dest = $dir . '/' . uniqid($type . '_', true) . '.csv';
                    if (!move_uploaded_file($file['tmp_name'], $dest)) {
                        $errors[] = 'Impossible de sauvegarder le fichier.';
                    } else {
                        $_SESSION['import'] = [
                            'type' => $type,
                            'path' => $dest,
                            'mapping' => [],
                        ];
                        redirect('?r=import-map');
                    }
                }
            }
        }
    }

    echo render('pages/import/index.php', ['errors' => $errors]);
}

function import_map(): void {
    require_auth();
    $import = $_SESSION['import'] ?? null;
    if (!$import) {
        redirect('?r=import');
    }

    $headers = csv_headers($import['path']);
    if (!$headers) {
        echo render('pages/error.php', ['message' => 'CSV vide ou invalide.']);
        return;
    }

    $fields = import_fields($import['type']);
    if (is_post()) {
        verify_csrf();
        $mapping = [];
        foreach ($fields as $field) {
            $mapping[$field] = trim($_POST['map_' . $field] ?? '');
        }
        $_SESSION['import']['mapping'] = $mapping;
        redirect('?r=import-preview');
    }

    echo render('pages/import/map.php', [
        'headers' => $headers,
        'fields' => $fields,
        'type' => $import['type'],
    ]);
}

function import_preview(): void {
    require_auth();
    $import = $_SESSION['import'] ?? null;
    if (!$import) {
        redirect('?r=import');
    }

    $rows = csv_preview($import['path'], 20);
    $fields = import_fields($import['type']);

    echo render('pages/import/preview.php', [
        'rows' => $rows,
        'fields' => $fields,
        'mapping' => $import['mapping'] ?? [],
        'type' => $import['type'],
    ]);
}

function import_validate(): void {
    require_auth();
    if (!is_post()) {
        http_response_code(405);
        echo render('pages/error.php', ['message' => 'Méthode non autorisée.']);
        return;
    }
    verify_csrf();
    $import = $_SESSION['import'] ?? null;
    if (!$import) {
        redirect('?r=import');
    }

    $summary = validate_import($import);
    $_SESSION['import']['summary'] = $summary;
    echo render('pages/import/validate.php', ['summary' => $summary]);
}

function import_run(): void {
    require_auth();
    if (!is_post()) {
        http_response_code(405);
        echo render('pages/error.php', ['message' => 'Méthode non autorisée.']);
        return;
    }
    verify_csrf();
    $doctorId = auth_user()['id'];
    $import = $_SESSION['import'] ?? null;
    if (!$import) {
        redirect('?r=import');
    }

    $result = run_import($doctorId, $import);
    unset($_SESSION['import']);
    echo render('pages/import/result.php', ['result' => $result]);
}

function csv_headers(string $path): array {
    if (!is_file($path)) {
        return [];
    }
    if (($handle = fopen($path, 'r')) === false) {
        return [];
    }
    $headers = fgetcsv($handle, 0, ',');
    fclose($handle);
    if (!$headers) {
        return [];
    }
    return array_map('trim', $headers);
}

function csv_preview(string $path, int $limit): array {
    $rows = [];
    if (($handle = fopen($path, 'r')) === false) {
        return $rows;
    }
    $headers = fgetcsv($handle, 0, ',');
    if (!$headers) {
        fclose($handle);
        return [];
    }
    while (($row = fgetcsv($handle, 0, ',')) !== false && count($rows) < $limit) {
        $rows[] = array_combine($headers, $row + array_fill(0, count($headers), ''));
    }
    fclose($handle);
    return $rows;
}

function import_fields(string $type): array {
    if ($type === 'patients') {
        return ['external_id', 'full_name', 'phone', 'email', 'dob', 'notes'];
    }
    if ($type === 'appointments') {
        return ['external_id', 'patient_external_id', 'patient_id', 'starts_at', 'ends_at', 'status', 'reason', 'notes'];
    }
    return ['external_id', 'patient_external_id', 'patient_id', 'appointment_external_id', 'appointment_id', 'received_at', 'amount', 'reference', 'notes'];
}

function validate_import(array $import): array {
    $mapping = $import['mapping'] ?? [];
    $errors = [];
    foreach ($mapping as $field => $col) {
        if ($field === 'external_id') {
            continue;
        }
        if ($col === '') {
            $errors[] = "Colonne manquante pour {$field}.";
        }
    }
    return [
        'errors' => $errors,
        'ready' => count($errors) === 0,
    ];
}

function run_import(int $doctorId, array $import): array {
    $mapping = $import['mapping'] ?? [];
    $path = $import['path'];
    $type = $import['type'];

    $imported = 0;
    $ignored = 0;
    $errors = 0;

    $pdo = db();
    $pdo->beginTransaction();
    try {
        if (($handle = fopen($path, 'r')) === false) {
            throw new RuntimeException('Impossible de lire le CSV.');
        }
        $headers = fgetcsv($handle, 0, ',');
        if (!$headers) {
            throw new RuntimeException('CSV vide.');
        }

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $assoc = array_combine($headers, $row + array_fill(0, count($headers), ''));
            $data = map_row($assoc, $mapping);

            $ok = false;
            if ($type === 'patients') {
                $ok = import_patient_row($doctorId, $data);
            } elseif ($type === 'appointments') {
                $ok = import_appointment_row($doctorId, $data);
            } else {
                $ok = import_payment_row($doctorId, $data);
            }

            if ($ok === true) {
                $imported++;
            } elseif ($ok === false) {
                $ignored++;
            } else {
                $errors++;
            }
        }
        fclose($handle);
        $pdo->commit();
    } catch (Throwable $ex) {
        $pdo->rollBack();
        log_error('Import error: ' . $ex->getMessage());
        return [
            'imported' => $imported,
            'ignored' => $ignored,
            'errors' => $errors + 1,
            'message' => 'Erreur pendant l\'import.',
        ];
    }

    return [
        'imported' => $imported,
        'ignored' => $ignored,
        'errors' => $errors,
        'message' => 'Import terminé.',
    ];
}

function map_row(array $assoc, array $mapping): array {
    $data = [];
    foreach ($mapping as $field => $column) {
        $data[$field] = $column !== '' ? trim($assoc[$column] ?? '') : '';
    }
    return $data;
}

function import_patient_row(int $doctorId, array $data) {
    if (($data['full_name'] ?? '') === '') {
        return null;
    }
    if (!empty($data['external_id'])) {
        $stmt = db()->prepare('SELECT id FROM patients WHERE doctor_id = ? AND external_id = ?');
        $stmt->execute([$doctorId, $data['external_id']]);
        if ($stmt->fetch()) {
            return false;
        }
    }
    $stmt = db()->prepare('INSERT INTO patients (doctor_id, external_id, full_name, phone, email, dob, notes) VALUES (?,?,?,?,?,?,?)');
    $stmt->execute([
        $doctorId,
        $data['external_id'] !== '' ? $data['external_id'] : null,
        $data['full_name'],
        $data['phone'] !== '' ? $data['phone'] : null,
        $data['email'] !== '' ? $data['email'] : null,
        $data['dob'] !== '' ? $data['dob'] : null,
        $data['notes'] !== '' ? $data['notes'] : null,
    ]);
    return true;
}

function import_appointment_row(int $doctorId, array $data) {
    if (($data['starts_at'] ?? '') === '' || ($data['ends_at'] ?? '') === '') {
        return null;
    }
    $patientId = resolve_patient_id($doctorId, $data);
    if (!$patientId) {
        return null;
    }
    if (!empty($data['external_id'])) {
        $stmt = db()->prepare('SELECT id FROM appointments WHERE doctor_id = ? AND external_id = ?');
        $stmt->execute([$doctorId, $data['external_id']]);
        if ($stmt->fetch()) {
            return false;
        }
    }
    $status = strtoupper($data['status'] ?? 'PLANIFIE');
    if ($status === 'ABSENT') {
        $status = 'NO_SHOW';
    }
    $allowed = ['PLANIFIE', 'CONFIRME', 'TERMINE', 'ANNULE', 'NO_SHOW'];
    if (!in_array($status, $allowed, true)) {
        $status = 'PLANIFIE';
    }
    $stmt = db()->prepare('INSERT INTO appointments (doctor_id, external_id, patient_id, starts_at, ends_at, status, reason, notes) VALUES (?,?,?,?,?,?,?,?)');
    $stmt->execute([
        $doctorId,
        $data['external_id'] !== '' ? $data['external_id'] : null,
        $patientId,
        $data['starts_at'],
        $data['ends_at'],
        $status,
        $data['reason'] !== '' ? $data['reason'] : null,
        $data['notes'] !== '' ? $data['notes'] : null,
    ]);
    return true;
}

function import_payment_row(int $doctorId, array $data) {
    if (($data['received_at'] ?? '') === '' || ($data['amount'] ?? '') === '') {
        return null;
    }
    $patientId = resolve_patient_id($doctorId, $data);
    if (!$patientId) {
        return null;
    }
    $appointmentId = resolve_appointment_id($doctorId, $data);

    if (!empty($data['external_id'])) {
        $stmt = db()->prepare('SELECT id FROM payments WHERE doctor_id = ? AND external_id = ?');
        $stmt->execute([$doctorId, $data['external_id']]);
        if ($stmt->fetch()) {
            return false;
        }
    }

    $stmt = db()->prepare('INSERT INTO payments (doctor_id, external_id, patient_id, appointment_id, received_at, amount, reference, notes) VALUES (?,?,?,?,?,?,?,?)');
    $stmt->execute([
        $doctorId,
        $data['external_id'] !== '' ? $data['external_id'] : null,
        $patientId,
        $appointmentId ?: null,
        $data['received_at'],
        $data['amount'],
        $data['reference'] !== '' ? $data['reference'] : null,
        $data['notes'] !== '' ? $data['notes'] : null,
    ]);
    return true;
}

function resolve_patient_id(int $doctorId, array $data): ?int {
    if (!empty($data['patient_id'])) {
        $stmt = db()->prepare('SELECT id FROM patients WHERE id = ? AND doctor_id = ?');
        $stmt->execute([(int)$data['patient_id'], $doctorId]);
        $row = $stmt->fetch();
        return $row ? (int)$row['id'] : null;
    }
    if (!empty($data['patient_external_id'])) {
        $stmt = db()->prepare('SELECT id FROM patients WHERE doctor_id = ? AND external_id = ?');
        $stmt->execute([$doctorId, $data['patient_external_id']]);
        $row = $stmt->fetch();
        return $row ? (int)$row['id'] : null;
    }
    return null;
}

function resolve_appointment_id(int $doctorId, array $data): ?int {
    if (!empty($data['appointment_id'])) {
        $stmt = db()->prepare('SELECT id FROM appointments WHERE id = ? AND doctor_id = ?');
        $stmt->execute([(int)$data['appointment_id'], $doctorId]);
        $row = $stmt->fetch();
        return $row ? (int)$row['id'] : null;
    }
    if (!empty($data['appointment_external_id'])) {
        $stmt = db()->prepare('SELECT id FROM appointments WHERE doctor_id = ? AND external_id = ?');
        $stmt->execute([$doctorId, $data['appointment_external_id']]);
        $row = $stmt->fetch();
        return $row ? (int)$row['id'] : null;
    }
    return null;
}