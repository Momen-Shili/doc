<?php
function patients_index(): void {
    require_auth();
    $doctor = auth_user();
    $q = trim($_GET['q'] ?? '');
    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = 10;
    $offset = ($page - 1) * $perPage;

    $params = [$doctor['id']];
    $where = 'WHERE doctor_id = ?';
    if ($q !== '') {
        $where .= ' AND (full_name LIKE ? OR email LIKE ? OR phone LIKE ?)';
        $like = '%' . $q . '%';
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }

    $stmt = db()->prepare("SELECT COUNT(*) AS cnt FROM patients {$where}");
    $stmt->execute($params);
    $total = (int)($stmt->fetch()['cnt'] ?? 0);

    $stmt = db()->prepare("SELECT * FROM patients {$where} ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}");
    $stmt->execute($params);
    $patients = $stmt->fetchAll();

    echo render('pages/patients/index.php', [
        'patients' => $patients,
        'q' => $q,
        'page' => $page,
        'perPage' => $perPage,
        'total' => $total,
    ]);
}

function patients_create(): void {
    require_auth();
    $errors = [];

    if (is_post()) {
        verify_csrf();
        $data = [
            'full_name' => trim($_POST['full_name'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'dob' => trim($_POST['dob'] ?? ''),
            'notes' => trim($_POST['notes'] ?? ''),
        ];

        if ($data['full_name'] === '') {
            $errors[] = 'Nom complet requis.';
        }
        if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalide.';
        }

        if (!$errors) {
            $stmt = db()->prepare('INSERT INTO patients (doctor_id, full_name, phone, email, dob, notes) VALUES (?,?,?,?,?,?)');
            $stmt->execute([
                auth_user()['id'],
                $data['full_name'],
                $data['phone'] !== '' ? $data['phone'] : null,
                $data['email'] !== '' ? $data['email'] : null,
                $data['dob'] !== '' ? $data['dob'] : null,
                $data['notes'] !== '' ? $data['notes'] : null,
            ]);
            flash('success', 'Patient ajouté.');
            redirect('?r=patients');
        }
        set_old($data);
    }

    echo render('pages/patients/create.php', ['errors' => $errors]);
}

function patients_edit(): void {
    require_auth();
    $doctorId = auth_user()['id'];
    $id = (int)($_GET['id'] ?? 0);
    $stmt = db()->prepare('SELECT * FROM patients WHERE id = ? AND doctor_id = ?');
    $stmt->execute([$id, $doctorId]);
    $patient = $stmt->fetch();
    if (!$patient) {
        http_response_code(404);
        echo render('pages/error.php', ['message' => 'Patient introuvable.']);
        return;
    }

    $errors = [];
    if (is_post()) {
        verify_csrf();
        $data = [
            'full_name' => trim($_POST['full_name'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'dob' => trim($_POST['dob'] ?? ''),
            'notes' => trim($_POST['notes'] ?? ''),
        ];
        if ($data['full_name'] === '') {
            $errors[] = 'Nom complet requis.';
        }
        if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalide.';
        }

        if (!$errors) {
            $stmt = db()->prepare('UPDATE patients SET full_name = ?, phone = ?, email = ?, dob = ?, notes = ? WHERE id = ? AND doctor_id = ?');
            $stmt->execute([
                $data['full_name'],
                $data['phone'] !== '' ? $data['phone'] : null,
                $data['email'] !== '' ? $data['email'] : null,
                $data['dob'] !== '' ? $data['dob'] : null,
                $data['notes'] !== '' ? $data['notes'] : null,
                $id,
                $doctorId,
            ]);
            flash('success', 'Patient mis à jour.');
            redirect('?r=patients');
        }
        $patient = array_merge($patient, $data);
    }

    echo render('pages/patients/edit.php', ['patient' => $patient, 'errors' => $errors]);
}

function patients_delete(): void {
    require_auth();
    if (!is_post()) {
        http_response_code(405);
        echo render('pages/error.php', ['message' => 'Méthode non autorisée.']);
        return;
    }
    verify_csrf();
    $doctorId = auth_user()['id'];
    $id = (int)($_POST['id'] ?? 0);
    $stmt = db()->prepare('DELETE FROM patients WHERE id = ? AND doctor_id = ?');
    $stmt->execute([$id, $doctorId]);
    flash('success', 'Patient supprimé.');
    redirect('?r=patients');
}

function patients_show(): void {
    require_auth();
    $doctorId = auth_user()['id'];
    $id = (int)($_GET['id'] ?? 0);
    $tab = $_GET['tab'] ?? 'appointments';
    $tab = in_array($tab, ['appointments', 'payments', 'notes', 'documents'], true) ? $tab : 'appointments';

    $patient = patient_or_404($doctorId, $id);
    if (!$patient) {
        return;
    }

    $tags = patient_tags($doctorId, $id);
    $allTags = doctor_tags($doctorId);

    $stmt = db()->prepare('SELECT a.* FROM appointments a WHERE a.doctor_id = ? AND a.patient_id = ? ORDER BY a.starts_at DESC');
    $stmt->execute([$doctorId, $id]);
    $appointments = $stmt->fetchAll();

    $stmt = db()->prepare('SELECT p.* FROM payments p WHERE p.doctor_id = ? AND p.patient_id = ? ORDER BY p.received_at DESC');
    $stmt->execute([$doctorId, $id]);
    $payments = $stmt->fetchAll();

    $stmt = db()->prepare('SELECT * FROM clinical_notes WHERE doctor_id = ? AND patient_id = ? ORDER BY created_at DESC');
    $stmt->execute([$doctorId, $id]);
    $notes = $stmt->fetchAll();

    $stmt = db()->prepare('SELECT * FROM documents WHERE doctor_id = ? AND patient_id = ? ORDER BY uploaded_at DESC');
    $stmt->execute([$doctorId, $id]);
    $documents = $stmt->fetchAll();

    $stmt = db()->prepare("SELECT COUNT(*) AS cnt FROM appointments WHERE doctor_id = ? AND patient_id = ? AND status = 'NO_SHOW'");
    $stmt->execute([$doctorId, $id]);
    $noShowCount = (int)($stmt->fetch()['cnt'] ?? 0);

    $stmt = db()->prepare('SELECT MAX(starts_at) AS last_visit FROM appointments WHERE doctor_id = ? AND patient_id = ?');
    $stmt->execute([$doctorId, $id]);
    $lastVisit = $stmt->fetch()['last_visit'] ?? null;

    $stmt = db()->prepare('SELECT COALESCE(SUM(amount),0) AS total FROM payments WHERE doctor_id = ? AND patient_id = ?');
    $stmt->execute([$doctorId, $id]);
    $totalPaid = $stmt->fetch()['total'] ?? 0;

    echo render('pages/patients/show.php', [
        'patient' => $patient,
        'tags' => $tags,
        'allTags' => $allTags,
        'appointments' => $appointments,
        'payments' => $payments,
        'notes' => $notes,
        'documents' => $documents,
        'tab' => $tab,
        'noShowCount' => $noShowCount,
        'lastVisit' => $lastVisit,
        'totalPaid' => $totalPaid,
    ]);
}

function notes_create(): void {
    require_auth();
    if (!is_post()) {
        http_response_code(405);
        echo render('pages/error.php', ['message' => 'Méthode non autorisée.']);
        return;
    }
    verify_csrf();
    $doctorId = auth_user()['id'];
    $patientId = (int)($_POST['patient_id'] ?? 0);
    $noteText = trim($_POST['note_text'] ?? '');

    if ($noteText === '') {
        flash('error', 'Note vide.');
        redirect('?r=patient&id=' . $patientId . '&tab=notes');
    }

    if (!patient_or_404($doctorId, $patientId, false)) {
        flash('error', 'Patient introuvable.');
        redirect('?r=patients');
    }

    $stmt = db()->prepare('INSERT INTO clinical_notes (doctor_id, patient_id, note_text) VALUES (?,?,?)');
    $stmt->execute([$doctorId, $patientId, $noteText]);
    flash('success', 'Note clinique ajoutée.');
    redirect('?r=patient&id=' . $patientId . '&tab=notes');
}

function notes_edit(): void {
    require_auth();
    if (!is_post()) {
        http_response_code(405);
        echo render('pages/error.php', ['message' => 'Méthode non autorisée.']);
        return;
    }
    verify_csrf();
    $doctorId = auth_user()['id'];
    $noteId = (int)($_POST['note_id'] ?? 0);
    $patientId = (int)($_POST['patient_id'] ?? 0);
    $noteText = trim($_POST['note_text'] ?? '');

    if ($noteText === '') {
        flash('error', 'Note vide.');
        redirect('?r=patient&id=' . $patientId . '&tab=notes');
    }

    $stmt = db()->prepare('UPDATE clinical_notes SET note_text = ? WHERE id = ? AND doctor_id = ? AND patient_id = ?');
    $stmt->execute([$noteText, $noteId, $doctorId, $patientId]);
    flash('success', 'Note clinique mise à jour.');
    redirect('?r=patient&id=' . $patientId . '&tab=notes');
}

function notes_delete(): void {
    require_auth();
    if (!is_post()) {
        http_response_code(405);
        echo render('pages/error.php', ['message' => 'Méthode non autorisée.']);
        return;
    }
    verify_csrf();
    $doctorId = auth_user()['id'];
    $noteId = (int)($_POST['note_id'] ?? 0);
    $patientId = (int)($_POST['patient_id'] ?? 0);

    $stmt = db()->prepare('DELETE FROM clinical_notes WHERE id = ? AND doctor_id = ? AND patient_id = ?');
    $stmt->execute([$noteId, $doctorId, $patientId]);
    flash('success', 'Note clinique supprimée.');
    redirect('?r=patient&id=' . $patientId . '&tab=notes');
}

function tags_add(): void {
    require_auth();
    if (!is_post()) {
        http_response_code(405);
        echo render('pages/error.php', ['message' => 'Méthode non autorisée.']);
        return;
    }
    verify_csrf();
    $doctorId = auth_user()['id'];
    $patientId = (int)($_POST['patient_id'] ?? 0);
    $name = trim($_POST['tag_name'] ?? '');
    $color = trim($_POST['tag_color'] ?? '');

    if ($name === '') {
        flash('error', 'Nom de tag requis.');
        redirect('?r=patient&id=' . $patientId);
    }

    if (!patient_or_404($doctorId, $patientId, false)) {
        flash('error', 'Patient introuvable.');
        redirect('?r=patients');
    }

    $stmt = db()->prepare('SELECT id FROM tags WHERE doctor_id = ? AND name = ?');
    $stmt->execute([$doctorId, $name]);
    $tag = $stmt->fetch();
    if (!$tag) {
        $stmt = db()->prepare('INSERT INTO tags (doctor_id, name, color) VALUES (?,?,?)');
        $stmt->execute([$doctorId, $name, $color !== '' ? $color : null]);
        $tagId = (int)db()->lastInsertId();
    } else {
        $tagId = (int)$tag['id'];
    }

    $stmt = db()->prepare('INSERT IGNORE INTO patient_tags (patient_id, tag_id) VALUES (?, ?)');
    $stmt->execute([$patientId, $tagId]);
    flash('success', 'Tag ajouté.');
    redirect('?r=patient&id=' . $patientId);
}

function tags_remove(): void {
    require_auth();
    if (!is_post()) {
        http_response_code(405);
        echo render('pages/error.php', ['message' => 'Méthode non autorisée.']);
        return;
    }
    verify_csrf();
    $doctorId = auth_user()['id'];
    $patientId = (int)($_POST['patient_id'] ?? 0);
    $tagId = (int)($_POST['tag_id'] ?? 0);

    $stmt = db()->prepare('DELETE pt FROM patient_tags pt JOIN tags t ON t.id = pt.tag_id WHERE pt.patient_id = ? AND pt.tag_id = ? AND t.doctor_id = ?');
    $stmt->execute([$patientId, $tagId, $doctorId]);
    flash('success', 'Tag retiré.');
    redirect('?r=patient&id=' . $patientId);
}

function documents_upload(): void {
    require_auth();
    if (!is_post()) {
        http_response_code(405);
        echo render('pages/error.php', ['message' => 'Méthode non autorisée.']);
        return;
    }
    verify_csrf();
    $doctorId = auth_user()['id'];
    $patientId = (int)($_POST['patient_id'] ?? 0);
    $note = trim($_POST['note'] ?? '');

    if (!patient_or_404($doctorId, $patientId, false)) {
        flash('error', 'Patient introuvable.');
        redirect('?r=patients');
    }

    if (empty($_FILES['document']['name'])) {
        flash('error', 'Document requis.');
        redirect('?r=patient&id=' . $patientId . '&tab=documents');
    }

    $file = $_FILES['document'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        flash('error', 'Erreur de téléchargement.');
        redirect('?r=patient&id=' . $patientId . '&tab=documents');
    }
    if ($file['size'] > 10 * 1024 * 1024) {
        flash('error', 'Fichier trop volumineux (10 MB max).');
        redirect('?r=patient&id=' . $patientId . '&tab=documents');
    }

    $original = basename($file['name']);
    $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    $allowedExt = ['pdf', 'jpg', 'jpeg', 'png'];
    if (!in_array($ext, $allowedExt, true)) {
        flash('error', 'Extension de fichier non autorisée.');
        redirect('?r=patient&id=' . $patientId . '&tab=documents');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $mimeMap = [
        'application/pdf' => 'pdf',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    ];
    if (!isset($mimeMap[$mime])) {
        flash('error', 'Format de fichier non autorisé.');
        redirect('?r=patient&id=' . $patientId . '&tab=documents');
    }
    if ($mimeMap[$mime] !== $ext && !($mimeMap[$mime] === 'jpg' && $ext === 'jpeg')) {
        flash('error', 'Le type de fichier ne correspond pas à l\'extension.');
        redirect('?r=patient&id=' . $patientId . '&tab=documents');
    }

    $dir = __DIR__ . '/../../storage/uploads/doctor_' . $doctorId . '/patients/' . $patientId;
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    $fileName = bin2hex(random_bytes(16)) . '.' . $mimeMap[$mime];
    $dest = $dir . '/' . $fileName;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        flash('error', 'Impossible de sauvegarder le document.');
        redirect('?r=patient&id=' . $patientId . '&tab=documents');
    }

    $relativePath = 'storage/uploads/doctor_' . $doctorId . '/patients/' . $patientId . '/' . $fileName;
    $stmt = db()->prepare('INSERT INTO documents (doctor_id, patient_id, file_name, file_path, mime_type, size_bytes, note) VALUES (?,?,?,?,?,?,?)');
    $stmt->execute([$doctorId, $patientId, $original, $relativePath, $mime, (int)$file['size'], $note !== '' ? $note : null]);

    flash('success', 'Document ajouté.');
    redirect('?r=patient&id=' . $patientId . '&tab=documents');
}

function documents_delete(): void {
    require_auth();
    if (!is_post()) {
        http_response_code(405);
        echo render('pages/error.php', ['message' => 'Méthode non autorisée.']);
        return;
    }
    verify_csrf();
    $doctorId = auth_user()['id'];
    $docId = (int)($_POST['doc_id'] ?? 0);
    $patientId = (int)($_POST['patient_id'] ?? 0);

    $stmt = db()->prepare('SELECT file_path FROM documents WHERE id = ? AND doctor_id = ? AND patient_id = ?');
    $stmt->execute([$docId, $doctorId, $patientId]);
    $doc = $stmt->fetch();
    if ($doc) {
        $path = __DIR__ . '/../../' . $doc['file_path'];
        if (is_file($path)) {
            unlink($path);
        }
        $stmt = db()->prepare('DELETE FROM documents WHERE id = ? AND doctor_id = ? AND patient_id = ?');
        $stmt->execute([$docId, $doctorId, $patientId]);
        flash('success', 'Document supprimé.');
    }
    redirect('?r=patient&id=' . $patientId . '&tab=documents');
}

function documents_download(): void {
    require_auth();
    $doctorId = auth_user()['id'];
    $docId = (int)($_GET['id'] ?? 0);
    $stmt = db()->prepare('SELECT file_name, file_path, mime_type FROM documents WHERE id = ? AND doctor_id = ?');
    $stmt->execute([$docId, $doctorId]);
    $doc = $stmt->fetch();
    if (!$doc) {
        http_response_code(404);
        echo render('pages/error.php', ['message' => 'Document introuvable.']);
        return;
    }
    $path = __DIR__ . '/../../' . $doc['file_path'];
    if (!is_file($path)) {
        http_response_code(404);
        echo render('pages/error.php', ['message' => 'Fichier introuvable.']);
        return;
    }
    $safeName = str_replace(["\r", "\n", "\""], '', $doc['file_name']);
    header('Content-Type: ' . $doc['mime_type']);
    header('Content-Disposition: attachment; filename="' . $safeName . '"');
    readfile($path);
    exit;
}

function patient_or_404(int $doctorId, int $patientId, bool $render = true) {
    $stmt = db()->prepare('SELECT * FROM patients WHERE id = ? AND doctor_id = ?');
    $stmt->execute([$patientId, $doctorId]);
    $patient = $stmt->fetch();
    if (!$patient && $render) {
        http_response_code(404);
        echo render('pages/error.php', ['message' => 'Patient introuvable.']);
        return null;
    }
    return $patient ? $patient : null;
}

function doctor_tags(int $doctorId): array {
    $stmt = db()->prepare('SELECT * FROM tags WHERE doctor_id = ? ORDER BY name');
    $stmt->execute([$doctorId]);
    return $stmt->fetchAll();
}

function patient_tags(int $doctorId, int $patientId): array {
    $stmt = db()->prepare('SELECT t.* FROM patient_tags pt JOIN tags t ON t.id = pt.tag_id WHERE pt.patient_id = ? AND t.doctor_id = ? ORDER BY t.name');
    $stmt->execute([$patientId, $doctorId]);
    return $stmt->fetchAll();
}