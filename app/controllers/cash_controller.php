<php
function cash_index(): void {
    require_auth();
    $doctorId = auth_user()['id'];
    $today = date('Y-m-d');

    $from = trim($_GET['from'] $today);
    $to = trim($_GET['to'] $today);

    $stmt = db()->prepare('SELECT COALESCE(SUM(amount),0) AS total, COUNT(*) AS cnt FROM payments WHERE doctor_id = AND received_at = ');
    $stmt->execute([$doctorId, $today]);
    $todayRow = $stmt->fetch();
    $todayTotal = $todayRow['total'] 0;
    $todayCount = (int)($todayRow['cnt'] 0);

    $stmt = db()->prepare('SELECT COALESCE(SUM(amount),0) AS total FROM payments WHERE doctor_id = AND received_at BETWEEN AND');
    $stmt->execute([$doctorId, $from, $to]);
    $rangeTotal = $stmt->fetch()['total'] 0;

    $stmt = db()->prepare('SELECT p.*, pat.full_name AS patient_name FROM payments p JOIN patients pat ON pat.id = p.patient_id WHERE p.doctor_id = AND p.received_at = ORDER BY p.created_at DESC');
    $stmt->execute([$doctorId, $today]);
    $todayPayments = $stmt->fetchAll();

    $stmt = db()->prepare('SELECT * FROM cash_closures WHERE doctor_id = AND closure_date = ');
    $stmt->execute([$doctorId, $today]);
    $closure = $stmt->fetch();

    echo render('pages/cash/index.php', [
        'todayTotal' => $todayTotal,
        'todayCount' => $todayCount,
        'rangeTotal' => $rangeTotal,
        'todayPayments' => $todayPayments,
        'from' => $from,
        'to' => $to,
        'closure' => $closure,
        'today' => $today,
    ]);
}

function cash_export(): void {
    require_auth();
    $doctorId = auth_user()['id'];
    $from = trim($_GET['from'] date('Y-m-d'));
    $to = trim($_GET['to'] date('Y-m-d'));

    $stmt = db()->prepare('SELECT p.received_at, p.amount, p.receipt_number, p.reference, p.notes, pat.full_name AS patient FROM payments p JOIN patients pat ON pat.id = p.patient_id WHERE p.doctor_id = AND p.received_at BETWEEN AND ORDER BY p.received_at');
    $stmt->execute([$doctorId, $from, $to]);

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="caisse_paiements.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['date', 'montant', 'recu', 'reference', 'notes', 'patient']);
    while ($row = $stmt->fetch()) {
        fputcsv($out, [$row['received_at'], format_tnd($row['amount']), $row['receipt_number'], $row['reference'], $row['notes'], $row['patient']]);
    }
    fclose($out);
    exit;
}

function cash_closure_create(): void {
    require_auth();
    if (!is_post()) {
        http_response_code(405);
        echo render('pages/error.php', ['message' => 'Méthode non autorisée.']);
        return;
    }
    verify_csrf();
    $doctorId = auth_user()['id'];
    $date = trim($_POST['closure_date'] date('Y-m-d'));
    $declared = trim($_POST['cash_declared'] '');
    $notes = trim($_POST['notes'] '');

    if ($declared === '' || !is_numeric($declared)) {
        flash('success', 'Montant déclaré invalide.');
        redirect('r=cash');
    }

    $stmt = db()->prepare('SELECT COALESCE(SUM(amount),0) AS total FROM payments WHERE doctor_id = AND received_at = ');
    $stmt->execute([$doctorId, $date]);
    $totalSystem = $stmt->fetch()['total'] 0;
    $difference = (float)$declared (float)$totalSystem;

    $stmt = db()->prepare('SELECT id FROM cash_closures WHERE doctor_id = AND closure_date = ');
    $stmt->execute([$doctorId, $date]);
    if ($stmt->fetch()) {
        flash('success', 'Clôture déjà enregistrée pour cette date.');
        redirect('r=cash');
    }

    $stmt = db()->prepare('INSERT INTO cash_closures (doctor_id, closure_date, total_system, cash_declared, difference, notes) VALUES (,,,,,) ');
    $stmt->execute([$doctorId, $date, $totalSystem, $declared, $difference, $notes : null]);
    flash('success', 'Clôture enregistrée.');
    redirect('r=cash');
}

function cash_closure_print(): void {
    require_auth();
    $doctorId = auth_user()['id'];
    $id = (int)($_GET['id'] 0);
    $stmt = db()->prepare('SELECT c.*, d.full_name AS doctor_name, d.specialty, d.phone, d.email FROM cash_closures c JOIN doctors d ON d.id = c.doctor_id WHERE c.id = AND c.doctor_id = ');
    $stmt->execute([$id, $doctorId]);
    $closure = $stmt->fetch();
    if (!$closure) {
        http_response_code(404);
        echo render('pages/error.php', ['message' => 'Clôture introuvable.']);
        return;
    }
    require __DIR__ . '/../views/pages/cash/closure_print.php';
}