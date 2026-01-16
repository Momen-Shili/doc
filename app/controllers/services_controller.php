<?php
function services_index(): void {
    require_auth();
    $doctorId = auth_user()['id'];
    $stmt = db()->prepare('SELECT * FROM services WHERE doctor_id = ? ORDER BY created_at DESC');
    $stmt->execute([$doctorId]);
    $services = $stmt->fetchAll();
    echo render('pages/services/index.php', ['services' => $services]);
}

function services_create(): void {
    require_auth();
    $errors = [];
    if (is_post()) {
        verify_csrf();
        $data = service_payload();
        $errors = validate_service($data);
        if (!$errors) {
            $stmt = db()->prepare('SELECT id FROM services WHERE doctor_id = ? AND name = ?');
            $stmt->execute([auth_user()['id'], $data['name']]);
            if ($stmt->fetch()) {
                $errors[] = 'Un service avec ce nom existe déjà.';
            } else {
                $stmt = db()->prepare('INSERT INTO services (doctor_id, name, default_price, default_duration_minutes, description, is_active) VALUES (?,?,?,?,?,?)');
                $stmt->execute([
                    auth_user()['id'],
                    $data['name'],
                    $data['default_price'],
                    $data['default_duration_minutes'],
                    $data['description'] !== '' ? $data['description'] : null,
                    $data['is_active'],
                ]);
                flash('success', 'Service créé.');
                redirect('?r=services');
            }
        }
    }
    echo render('pages/services/create.php', ['errors' => $errors]);
}

function services_edit(): void {
    require_auth();
    $doctorId = auth_user()['id'];
    $id = (int)($_GET['id'] ?? 0);
    $stmt = db()->prepare('SELECT * FROM services WHERE id = ? AND doctor_id = ?');
    $stmt->execute([$id, $doctorId]);
    $service = $stmt->fetch();
    if (!$service) {
        http_response_code(404);
        echo render('pages/error.php', ['message' => 'Service introuvable.']);
        return;
    }

    $errors = [];
    if (is_post()) {
        verify_csrf();
        $data = service_payload();
        $errors = validate_service($data);
        if (!$errors) {
            $stmt = db()->prepare('SELECT id FROM services WHERE doctor_id = ? AND name = ? AND id <> ?');
            $stmt->execute([$doctorId, $data['name'], $id]);
            if ($stmt->fetch()) {
                $errors[] = 'Un service avec ce nom existe déjà.';
            } else {
                $stmt = db()->prepare('UPDATE services SET name = ?, default_price = ?, default_duration_minutes = ?, description = ?, is_active = ? WHERE id = ? AND doctor_id = ?');
                $stmt->execute([
                    $data['name'],
                    $data['default_price'],
                    $data['default_duration_minutes'],
                    $data['description'] !== '' ? $data['description'] : null,
                    $data['is_active'],
                    $id,
                    $doctorId,
                ]);
                flash('success', 'Service mis à jour.');
                redirect('?r=services');
            }
        }
        $service = array_merge($service, $data);
    }

    echo render('pages/services/edit.php', ['service' => $service, 'errors' => $errors]);
}

function services_toggle(): void {
    require_auth();
    if (!is_post()) {
        http_response_code(405);
        echo render('pages/error.php', ['message' => 'Méthode non autorisée.']);
        return;
    }
    verify_csrf();
    $doctorId = auth_user()['id'];
    $id = (int)($_POST['id'] ?? 0);
    $stmt = db()->prepare('UPDATE services SET is_active = IF(is_active=1,0,1) WHERE id = ? AND doctor_id = ?');
    $stmt->execute([$id, $doctorId]);
    flash('success', 'Statut du service mis à jour.');
    redirect('?r=services');
}

function service_payload(): array {
    return [
        'name' => trim($_POST['name'] ?? ''),
        'default_price' => trim($_POST['default_price'] ?? '0'),
        'default_duration_minutes' => (int)($_POST['default_duration_minutes'] ?? 0),
        'description' => trim($_POST['description'] ?? ''),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];
}

function validate_service(array $data): array {
    $errors = [];
    if ($data['name'] === '') {
        $errors[] = 'Nom requis.';
    }
    if ($data['default_price'] === '' || !is_numeric($data['default_price'])) {
        $errors[] = 'Prix par défaut invalide.';
    }
    if ($data['default_duration_minutes'] < 0) {
        $errors[] = 'Durée par défaut invalide.';
    }
    return $errors;
}