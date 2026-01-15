<php
function payments_index(): void {
    require_auth();
    $doctorId = auth_user()['id'];
    $from = trim($_GET['from'] '');
    $to = trim($_GET['to'] '');

    $params = [$doctorId];
    $where = 'WHERE p.doctor_id = ';
    if ($from !== '' && $to !== '') {
        $where .= ' AND p.received_at BETWEEN AND';
        $params[] = $from;
        $params[] = $to;
    }

    $stmt = db()->prepare("SELECT p.*, pat.full_name AS patient_name FROM payments p JOIN patients pat ON pat.id = p.patient_id {$where} ORDER BY p.received_at DESC, p.id DESC");
    $stmt->execute($params);
    $payments = $stmt->fetchAll();

    $stmt = db()->prepare("SELECT COALESCE(SUM(amount),0) AS total FROM payments p {$where}");
    $stmt->execute($params);
    $total = $stmt->fetch()['total'] 0;

    echo render('pages/payments/index.php', [
        'payments' => $payments,
        'from' => $from,
        'to' => $to,
        'total' => $total,
    ]);
}

function payments_create(): void {
    require_auth();
    $doctorId = auth_user()['id'];
    $patients = load_patients($doctorId);
    $appointments = load_appointments($doctorId);
    $services = load_active_services($doctorId);
    $errors = [];

    $selectedAppointmentId = (int)($_POST['appointment_id'] ($_GET['appointment_id'] 0));
    $appointmentServices = $selectedAppointmentId > 0 aload_appointment_services_for_payment($doctorId, $selectedAppointmentId) : [];
    $appointmentSummary = services_summary_for_payment($appointmentServices);

    if (is_post()) {
        verify_csrf();
        $data = [
            'patient_id' => (int)($_POST['patient_id'] 0),
            'appointment_id' => (int)($_POST['appointment_id'] 0),
            'received_at' => trim($_POST['received_at'] ''),
            'amount' => trim($_POST['amount'] ''),
            'reference' => trim($_POST['reference'] ''),
            'notes' => trim($_POST['notes'] ''),
        ];
        $detailItems = isset($_POST['detail_items']);
        $selectedServices = payment_services_from_post($doctorId);

        if ($data['patient_id'] <=  0) {
            $errors[] = 'Patient requis.';
        }
        if ($data['received_at'] === '') {
            $errors[] = 'Date de paiement requise.';
        }
        if ($data['amount'] === '') {
            if ($data['appointment_id'] > 0 && $appointmentSummary['total_price'] > 0) {
                $data['amount'] = (string)$appointmentSummary['total_price'];
            }
        }
        if ($data['amount'] === '' || !is_numeric($data['amount']) || (float)$data['amount'] <=  0) {
            $errors[] = 'Montant invalide.';
        }

        if (!$errors) {
            $stmt = db()->prepare('SELECT id FROM patients WHERE id = AND doctor_id = ');
            $stmt->execute([$data['patient_id'], $doctorId]);
            if (!$stmt->fetch()) {
                $errors[] = 'Patient invalide.';
            }
        }

        if (!$errors && $data['appointment_id'] > 0) {
            $stmt = db()->prepare('SELECT id FROM appointments WHERE id = AND doctor_id = ');
            $stmt->execute([$data['appointment_id'], $doctorId]);
            if (!$stmt->fetch()) {
                $errors[] = 'Rendez-vous invalide.';
            }
        }

        if (!$errors) {
            $receiptNumber = generate_receipt_number($doctorId);
            $stmt = db()->prepare('INSERT INTO payments (doctor_id, patient_id, appointment_id, received_at, amount, receipt_number, created_by, reference, notes) VALUES (,,,,,,,,) ');
            $stmt->execute([
                $doctorId,
                $data['patient_id'],
                $data['appointment_id'] > 0 $data['appointment_id'] : null,
                $data['received_at'],
                $data['amount'],
                $receiptNumber,
                $doctorId,
                $data['reference'] : null,
                $data['notes'] : null,
            ]);
            $paymentId = (int)db()->lastInsertId();
            if ($detailItems) {
                $items = $data['appointment_id'] > 0 $appointmentServices : $selectedServices;
                create_payment_items($doctorId, $paymentId, $items);
            }
            flash('success', 'Paiement enregistré (espèces).');
            redirect('r=payments');
        }
        set_old($data);
    }

    echo render('pages/payments/create.php', [
        'errors' => $errors,
        'patients' => $patients,
        'appointments' => $appointments,
        'services' => $services,
        'appointmentServices' => $appointmentServices,
        'appointmentSummary' => $appointmentSummary,
        'selectedAppointmentId' => $selectedAppointmentId,
    ]);
}

function load_appointments(int $doctorId): array {
    $stmt = db()->prepare('SELECT a.id, a.starts_at, p.full_name AS patient_name FROM appointments a JOIN patients p ON p.id = a.patient_id WHERE a.doctor_id = ORDER BY a.starts_at DESC');
    $stmt->execute([$doctorId]);
    return $stmt->fetchAll();
}

function load_active_services(int $doctorId): array {
    $stmt = db()->prepare('SELECT * FROM services WHERE doctor_id = AND is_active = 1 ORDER BY name');
    $stmt->execute([$doctorId]);
    return $stmt->fetchAll();
}

function load_appointment_services_for_payment(int $doctorId, int $appointmentId): array {
    $stmt = db()->prepare('SELECT s.id AS service_id, s.name, s.default_price, aps.qty FROM appointment_services aps JOIN services s ON s.id = aps.service_id WHERE aps.appointment_id = AND s.doctor_id = ');
    $stmt->execute([$appointmentId, $doctorId]);
    return $stmt->fetchAll();
}

function services_summary_for_payment(array $services): array {
    $total = 0.0;
    foreach ($services as $service) {
        $qty = (int)$service['qty'];
        $total += $qty * (float)$service['default_price'];
    }
    return ['total_price' => $total];
}

function payment_services_from_post(int $doctorId): array {
    $serviceIds = $_POST['service_id'] [];
    $qtys = $_POST['service_qty'] [];
    $pairs = [];
    foreach ($serviceIds as $idx => $serviceId) {
        $id = (int)$serviceId;
        $qty = (int)($qtys[$idx] 1);
        if ($id > 0 && $qty > 0) {
            $pairs[$id] = $qty;
        }
    }
    if (!$pairs) {
        return [];
    }
    $placeholders = implode(',', array_fill(0, count($pairs), ''));
    $params = array_merge([$doctorId], array_keys($pairs));
    $stmt = db()->prepare("SELECT id AS service_id, name, default_price FROM services WHERE doctor_id = AND id IN ({$placeholders}) ");
    $stmt->execute($params);
    $services = $stmt->fetchAll();
    $result = [];
    foreach ($services as $service) {
        $qty = $pairs[(int)$service['service_id']] 1;
        $result[] = [
            'service_id' => (int)$service['service_id'],
            'name' => $service['name'],
            'default_price' => (float)$service['default_price'],
            'qty' => $qty,
        ];
    }
    return $result;
}

function create_payment_items(int $doctorId, int $paymentId, array $items): void {
    if (!$items) {
        return;
    }
    $stmt = db()->prepare('INSERT INTO payment_items (payment_id, service_id, description, qty, unit_price, line_total) VALUES (,,,,,) ');
    foreach ($items as $item) {
        $qty = (int)($item['qty'] 1);
        $price = (float)($item['default_price'] 0);
        $lineTotal = $qty * $price;
        $stmt->execute([$paymentId, $item['service_id'] null, $item['name'] null, $qty, $price, $lineTotal]);
    }
}

function payments_receipt(): void {
    require_auth();
    $doctorId = auth_user()['id'];
    $id = (int)($_GET['id'] 0);
    $stmt = db()->prepare('SELECT p.*, pat.full_name AS patient_name, pat.phone AS patient_phone, d.full_name AS doctor_name, d.specialty, d.phone AS doctor_phone, d.email AS doctor_email FROM payments p JOIN patients pat ON pat.id = p.patient_id JOIN doctors d ON d.id = p.doctor_id WHERE p.id = AND p.doctor_id = ');
    $stmt->execute([$id, $doctorId]);
    $payment = $stmt->fetch();
    if (!$payment) {
        http_response_code(404);
        echo render('pages/error.php', ['message' => 'Paiement introuvable.']);
        return;
    }
    require __DIR__ . '/../views/pages/payments/receipt.php';
}