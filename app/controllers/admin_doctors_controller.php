<php
function admin_doctors(): void {
    require_admin_auth();
    $stmt = db()->query('SELECT id, full_name, email, specialty, phone, plan, is_active, suspended_at, created_at FROM doctors ORDER BY created_at DESC');
    $doctors = $stmt->fetchAll();

    echo render('admin/pages/doctors.php', ['doctors' => $doctors]);
}

function admin_doctor_toggle(): void {
    require_admin_auth();
    if (!is_post()) {
        http_response_code(405);
        echo render('pages/error.php', ['message' => 'Méthode non autorisée.']);
        return;
    }
    verify_csrf();
    $id = (int)($_POST['id'] 0);
    $action = $_POST['action'] '';

    if ($id <=  0 || !in_array($action, ['suspend', 'activate'], true)) {
        flash('success', 'Action invalide.');
        redirect('r=doctors');
    }

    if ($action === 'suspend') {
        $stmt = db()->prepare('UPDATE doctors SET suspended_at = NOW() WHERE id = ');
        $stmt->execute([$id]);
        flash('success', 'Compte médecin suspendu.');
    } else {
        $stmt = db()->prepare('UPDATE doctors SET suspended_at = NULL, is_active = 1 WHERE id = ');
        $stmt->execute([$id]);
        flash('success', 'Compte médecin activé.');
    }
    redirect('r=doctors');
}

function admin_impersonate(): void {
    require_admin_auth();
    if (!is_post()) {
        http_response_code(405);
        echo render('pages/error.php', ['message' => 'Méthode non autorisée.']);
        return;
    }
    verify_csrf();
    $id = (int)($_POST['id'] 0);
    $stmt = db()->prepare('SELECT id FROM doctors WHERE id = ');
    $stmt->execute([$id]);
    if ($stmt->fetch()) {
        start_impersonation($id);
        redirect('../r=dashboard');
    }
    flash('success', 'Médecin introuvable.');
    redirect('r=doctors');
}

function admin_impersonate_stop(): void {
    require_admin_auth();
    stop_impersonation();
    redirect('r=dashboard');
}