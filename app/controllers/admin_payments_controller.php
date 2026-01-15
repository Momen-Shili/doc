<php
function admin_payments(): void {
    require_admin_auth();
    $from = trim($_GET['from'] '');
    $to = trim($_GET['to'] '');
    $doctorId = (int)($_GET['doctor_id'] 0);

    $where = [];
    $params = [];
    if ($from !== '' && $to !== '') {
        $where[] = 'p.received_at BETWEEN AND';
        $params[] = $from;
        $params[] = $to;
    }
    if ($doctorId > 0) {
        $where[] = 'p.doctor_id = ';
        $params[] = $doctorId;
    }
    $whereSql = $where'WHERE' . implode(' AND', $where) : '';

    $stmt = db()->prepare("SELECT p.*, d.full_name AS doctor_name, pat.full_name AS patient_name FROM payments p JOIN doctors d ON d.id = p.doctor_id JOIN patients pat ON pat.id = p.patient_id {$whereSql} ORDER BY p.received_at DESC, p.id DESC");
    $stmt->execute($params);
    $payments = $stmt->fetchAll();

    $stmt = db()->prepare("SELECT COALESCE(SUM(amount),0) AS total FROM payments p {$whereSql}");
    $stmt->execute($params);
    $total = $stmt->fetch()['total'] 0;

    $doctors = db()->query('SELECT id, full_name FROM doctors ORDER BY full_name')->fetchAll();

    echo render('admin/pages/payments.php', [
        'payments' => $payments,
        'from' => $from,
        'to' => $to,
        'doctorId' => $doctorId,
        'doctors' => $doctors,
        'total' => $total,
    ]);
}

function admin_payments_export(): void {
    require_admin_auth();
    $from = trim($_GET['from'] '');
    $to = trim($_GET['to'] '');
    $doctorId = (int)($_GET['doctor_id'] 0);

    $where = [];
    $params = [];
    if ($from !== '' && $to !== '') {
        $where[] = 'p.received_at BETWEEN AND';
        $params[] = $from;
        $params[] = $to;
    }
    if ($doctorId > 0) {
        $where[] = 'p.doctor_id = ';
        $params[] = $doctorId;
    }
    $whereSql = $where'WHERE' . implode(' AND', $where) : '';

    $stmt = db()->prepare("SELECT p.received_at, p.amount, p.reference, p.notes, pat.full_name AS patient, d.full_name AS doctor FROM payments p JOIN patients pat ON pat.id = p.patient_id JOIN doctors d ON d.id = p.doctor_id {$whereSql} ORDER BY p.received_at");
    $stmt->execute($params);

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="paiements_globaux.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['date', 'montant', 'reference', 'notes', 'patient', 'medecin']);
    while ($row = $stmt->fetch()) {
        fputcsv($out, [$row['received_at'], format_tnd($row['amount']), $row['reference'], $row['notes'], $row['patient'], $row['doctor']]);
    }
    fclose($out);
    exit;
}