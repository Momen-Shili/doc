<?php
function public_home(): void {
    $lat = filter_input(INPUT_GET, 'lat', FILTER_VALIDATE_FLOAT);
    $lng = filter_input(INPUT_GET, 'lng', FILTER_VALIDATE_FLOAT);
    $lat = (is_float($lat) && $lat >= -90 && $lat <= 90) ? $lat : null;
    $lng = (is_float($lng) && $lng >= -180 && $lng <= 180) ? $lng : null;
    $featured = public_featured_doctors(6, $lat, $lng);
    echo render('public/home.php', [
        'featured' => $featured,
    ]);
}

function public_doctors(): void {
    $q = trim($_GET['q'] ?? '');
    $specialty = trim($_GET['specialty'] ?? '');
    $city = trim($_GET['city'] ?? '');
    $lat = filter_input(INPUT_GET, 'lat', FILTER_VALIDATE_FLOAT);
    $lng = filter_input(INPUT_GET, 'lng', FILTER_VALIDATE_FLOAT);
    $lat = (is_float($lat) && $lat >= -90 && $lat <= 90) ? $lat : null;
    $lng = (is_float($lng) && $lng >= -180 && $lng <= 180) ? $lng : null;
    $around = ($_GET['around'] ?? '') === '1';
    $radius = (int)($_GET['radius'] ?? 0);
    if ($around && !in_array($radius, [5, 10, 25], true)) {
        $radius = 10;
    }
    $sort = trim($_GET['sort'] ?? 'relevance');
    if (!in_array($sort, ['relevance', 'distance'], true)) {
        $sort = 'relevance';
    }
    $doctors = public_search_doctors($q, $specialty, $city, $lat, $lng, $around ? $radius : 0, $sort, $around);
    echo render('public/doctors.php', [
        'doctors' => $doctors,
        'q' => $q,
        'specialty' => $specialty,
        'city' => $city,
        'around' => $around,
        'radius' => $radius,
        'sort' => $sort,
        'lat' => $lat,
        'lng' => $lng,
    ]);
}

function public_doctor(): void {
    $slug = trim($_GET['slug'] ?? '');
    if ($slug === '') {
        http_response_code(404);
        echo render('public/thanks.php', ['message' => 'Medecin introuvable.']);
        return;
    }
    $doctor = public_find_doctor($slug);
    if (!$doctor) {
        http_response_code(404);
        echo render('public/thanks.php', ['message' => 'Medecin introuvable.']);
        return;
    }
    $services = public_doctor_services($doctor['id']);
    echo render('public/doctor.php', [
        'doctor' => $doctor,
        'services' => $services,
    ]);
}

function public_slots_json(): void {
    header('Content-Type: application/json; charset=utf-8');
    $slug = trim($_GET['slug'] ?? '');
    $serviceId = (int)($_GET['service_id'] ?? 0);
    $date = trim($_GET['date'] ?? '');
    if ($slug === '' || $serviceId <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        http_response_code(422);
        echo json_encode(['error' => 'Parametres invalides.'], JSON_UNESCAPED_UNICODE);
        return;
    }
    $doctor = public_find_doctor($slug);
    if (!$doctor) {
        http_response_code(404);
        echo json_encode(['error' => 'Medecin introuvable.'], JSON_UNESCAPED_UNICODE);
        return;
    }
    $service = public_find_service($doctor['id'], $serviceId);
    if (!$service) {
        http_response_code(404);
        echo json_encode(['error' => 'Service introuvable.'], JSON_UNESCAPED_UNICODE);
        return;
    }
    $slots = public_available_slots($doctor['id'], $date, (int)$service['default_duration_minutes']);
    echo json_encode([
        'date' => $date,
        'duration_minutes' => (int)$service['default_duration_minutes'],
        'slots' => $slots,
    ], JSON_UNESCAPED_UNICODE);
}

function public_geocode_reverse(): void {
    header('Content-Type: application/json; charset=utf-8');
    $lat = filter_input(INPUT_GET, 'lat', FILTER_VALIDATE_FLOAT);
    $lng = filter_input(INPUT_GET, 'lng', FILTER_VALIDATE_FLOAT);
    if (!is_float($lat) || !is_float($lng)) {
        http_response_code(422);
        echo json_encode(['error' => 'Coordonnees invalides.'], JSON_UNESCAPED_UNICODE);
        return;
    }
    $result = reverse_geocode($lat, $lng);
    if (!$result) {
        echo json_encode(['city' => null], JSON_UNESCAPED_UNICODE);
        return;
    }
    echo json_encode(['city' => $result['city'] ?? null], JSON_UNESCAPED_UNICODE);
}

function public_book(): void {
    if (!is_post()) {
        http_response_code(405);
        echo render('public/thanks.php', ['message' => 'Methode non autorisee.']);
        return;
    }
    verify_csrf();
    $slug = trim($_POST['slug'] ?? '');
    $serviceId = (int)($_POST['service_id'] ?? 0);
    $date = trim($_POST['date'] ?? '');
    $slot = trim($_POST['slot'] ?? '');
    $name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $honeypot = trim($_POST['website'] ?? '');

    $errors = [];
    if ($honeypot !== '') {
        $errors[] = 'Erreur de validation.';
    }
    if ($slug === '' || $serviceId <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !preg_match('/^\d{2}:\d{2}$/', $slot)) {
        $errors[] = 'Veuillez selectionner un service et un horaire.';
    }
    if ($name === '' || $phone === '' || $email === '') {
        $errors[] = 'Nom, telephone et email sont requis.';
    }
    $phoneNorm = normalize_tn_phone($phone);
    if (!$phoneNorm) {
        $errors[] = 'Telephone invalide.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email invalide.';
    }

    if ($errors) {
        echo render('public/thanks.php', ['message' => implode(' ', $errors)]);
        return;
    }

    if (!public_rate_limit_ok('ip:' . public_client_ip(), 10, 3600)) {
        echo render('public/thanks.php', ['message' => 'Trop de demandes. Reessayez plus tard.']);
        return;
    }
    if (!public_rate_limit_ok('phone:' . $phoneNorm . ':' . date('Y-m-d'), 3, 86400)) {
        echo render('public/thanks.php', ['message' => 'Limite atteinte pour ce numero.']);
        return;
    }

    $doctor = public_find_doctor($slug);
    if (!$doctor) {
        echo render('public/thanks.php', ['message' => 'Medecin introuvable.']);
        return;
    }
    $service = public_find_service($doctor['id'], $serviceId);
    if (!$service) {
        echo render('public/thanks.php', ['message' => 'Service introuvable.']);
        return;
    }
    $duration = (int)$service['default_duration_minutes'];
    $startsAt = $date . ' ' . $slot . ':00';
    $endsAt = date('Y-m-d H:i:s', strtotime($startsAt . ' +' . $duration . ' minutes'));

    if (appointment_overlaps($doctor['id'], $startsAt, $endsAt)) {
        echo render('public/thanks.php', ['message' => 'Ce creneau n\'est plus disponible.']);
        return;
    }

    $token = bin2hex(random_bytes(16));
    $stmt = db()->prepare('INSERT INTO appointments (doctor_id, patient_id, starts_at, ends_at, status, reason, notes, public_patient_name, public_patient_phone, public_patient_email, public_booking_token, source) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)');
    $stmt->execute([
        $doctor['id'],
        null,
        $startsAt,
        $endsAt,
        'PENDING',
        $service['name'],
        null,
        $name,
        $phoneNorm,
        $email,
        $token,
        'PUBLIC',
    ]);
    $appointmentId = (int)db()->lastInsertId();

    public_enqueue_booking_emails($doctor, $name, $phoneNorm, $email, $startsAt, $service['name']);

    $ref = substr($token, -6);
    redirect('?r=public-thanks&ref=' . urlencode($ref));
}

function public_thanks(): void {
    $ref = trim($_GET['ref'] ?? '');
    echo render('public/thanks.php', [
        'message' => $ref ? ('Merci. Votre demande a ete envoyee. Reference: ' . e($ref)) : 'Merci. Votre demande a ete envoyee.',
    ]);
}

function public_theme_toggle(): void {
    if (!is_post()) {
        redirect('?r=public-home');
    }
    verify_csrf();
    $current = $_POST['current'] ?? 'light';
    $next = $current === 'dark' ? 'light' : 'dark';
    setcookie('theme', $next, [
        'expires' => time() + (86400 * 30),
        'path' => '/',
        'samesite' => 'Lax',
        'httponly' => false,
    ]);
    $redirect = safe_redirect_path($_POST['redirect'] ?? '?r=public-home', '?r=public-home');
    redirect($redirect);
}

function public_featured_doctors(int $limit, ?float $lat = null, ?float $lng = null): array {
    $stmt = db()->prepare('SELECT id, full_name, specialty, city, address_public, public_slug, public_photo, latitude, longitude FROM doctors WHERE is_public = 1 AND is_active = 1 ORDER BY created_at DESC LIMIT ?');
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    $doctors = $stmt->fetchAll();
    $doctors = public_attach_next_slots($doctors);
    if ($lat !== null && $lng !== null) {
        foreach ($doctors as &$doc) {
            if (!empty($doc['latitude']) && !empty($doc['longitude'])) {
                $doc['distance_km'] = haversine_km($lat, $lng, (float)$doc['latitude'], (float)$doc['longitude']);
            }
        }
    }
    return $doctors;
}

function public_search_doctors(string $q, string $specialty, string $city, ?float $lat, ?float $lng, int $radius, string $sort, bool $around): array {
    $hasGeo = ($lat !== null && $lng !== null);
    $sql = 'SELECT id, full_name, specialty, city, address_public, public_slug, public_photo, latitude, longitude';
    if ($hasGeo) {
        $sql .= ', (6371 * ACOS(COS(RADIANS(?)) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS(?)) + SIN(RADIANS(?)) * SIN(RADIANS(latitude)))) AS distance_km';
    }
    $sql .= ' FROM doctors WHERE is_public = 1 AND is_active = 1';
    $params = [];
    if ($hasGeo) {
        $params[] = $lat;
        $params[] = $lng;
        $params[] = $lat;
    }
    if ($q !== '') {
        $sql .= ' AND full_name LIKE ?';
        $params[] = '%' . $q . '%';
    }
    if ($specialty !== '') {
        $sql .= ' AND specialty LIKE ?';
        $params[] = '%' . $specialty . '%';
    }
    if ($city !== '') {
        $sql .= ' AND city LIKE ?';
        $params[] = '%' . $city . '%';
    }
    if ($hasGeo && $around && $radius > 0) {
        $sql .= ' HAVING distance_km <= ?';
        $params[] = $radius;
    }
    if ($hasGeo && $sort === 'distance') {
        $sql .= ' ORDER BY distance_km IS NULL, distance_km ASC, full_name';
    } else {
        $sql .= ' ORDER BY full_name';
    }
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
    return public_attach_next_slots($rows);
}

function public_attach_next_slots(array $doctors): array {
    if (!$doctors) {
        return [];
    }
    $doctorIds = array_map(function ($d) { return (int)$d['id']; }, $doctors);

    $minDurations = [];
    $placeholders = implode(',', array_fill(0, count($doctorIds), '?'));
    $stmt = db()->prepare("SELECT doctor_id, MIN(default_duration_minutes) AS min_duration FROM services WHERE doctor_id IN ({$placeholders}) AND is_active = 1 GROUP BY doctor_id");
    $stmt->execute($doctorIds);
    while ($row = $stmt->fetch()) {
        $minDurations[(int)$row['doctor_id']] = (int)$row['min_duration'];
    }

    $working = public_working_hours_for_doctors($doctorIds);
    $appointments = public_upcoming_appointments($doctorIds, 14);

    foreach ($doctors as &$doc) {
        $duration = $minDurations[(int)$doc['id']] ?? 30;
        $doc['next_slot'] = public_next_available_slot((int)$doc['id'], $duration, $working, $appointments);
    }
    return $doctors;
}

function public_find_doctor(string $slug): ?array {
    $stmt = db()->prepare('SELECT * FROM doctors WHERE public_slug = ? AND is_public = 1 AND is_active = 1 LIMIT 1');
    $stmt->execute([$slug]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function public_doctor_services(int $doctorId): array {
    $stmt = db()->prepare('SELECT id, name, default_duration_minutes, default_price FROM services WHERE doctor_id = ? AND is_active = 1 ORDER BY name');
    $stmt->execute([$doctorId]);
    return $stmt->fetchAll();
}

function public_find_service(int $doctorId, int $serviceId): ?array {
    $stmt = db()->prepare('SELECT id, name, default_duration_minutes FROM services WHERE doctor_id = ? AND id = ? AND is_active = 1');
    $stmt->execute([$doctorId, $serviceId]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function public_working_hours_for_doctors(array $doctorIds): array {
    $result = [];
    if (!$doctorIds) {
        return $result;
    }
    $placeholders = implode(',', array_fill(0, count($doctorIds), '?'));
    $stmt = db()->prepare("SELECT doctor_id, weekday, start_time, end_time, slot_step_minutes FROM doctor_working_hours WHERE doctor_id IN ({$placeholders}) ORDER BY start_time");
    $stmt->execute($doctorIds);
    while ($row = $stmt->fetch()) {
        $result[(int)$row['doctor_id']][] = $row;
    }
    return $result;
}

function public_upcoming_appointments(array $doctorIds, int $days): array {
    $result = [];
    if (!$doctorIds) {
        return $result;
    }
    $placeholders = implode(',', array_fill(0, count($doctorIds), '?'));
    $start = date('Y-m-d 00:00:00');
    $end = date('Y-m-d 23:59:59', strtotime('+' . ($days - 1) . ' days'));
    $stmt = db()->prepare("SELECT doctor_id, starts_at, ends_at, status FROM appointments WHERE doctor_id IN ({$placeholders}) AND starts_at BETWEEN ? AND ? AND status != 'ANNULE'");
    $params = array_merge($doctorIds, [$start, $end]);
    $stmt->execute($params);
    while ($row = $stmt->fetch()) {
        $dateKey = date('Y-m-d', strtotime($row['starts_at']));
        $result[(int)$row['doctor_id']][$dateKey][] = [
            'starts_at' => $row['starts_at'],
            'ends_at' => $row['ends_at'],
            'status' => $row['status'],
        ];
    }
    return $result;
}

function public_next_available_slot(int $doctorId, int $duration, array $working, array $appointments): ?string {
    $hours = $working[$doctorId] ?? [];
    if (!$hours) {
        return null;
    }
    for ($i = 0; $i < 14; $i++) {
        $date = date('Y-m-d', strtotime('+' . $i . ' days'));
        $weekday = (int)date('N', strtotime($date));
        $dayHours = array_filter($hours, function ($h) use ($weekday) {
            return (int)$h['weekday'] === $weekday;
        });
        foreach ($dayHours as $block) {
            $slots = public_generate_slots($date, $block['start_time'], $block['end_time'], (int)$block['slot_step_minutes'], $duration);
            foreach ($slots as $slot) {
                if (!public_slot_overlaps($doctorId, $date, $slot, $duration, $appointments)) {
                    return $date . ' ' . $slot;
                }
            }
        }
    }
    return null;
}

function public_available_slots(int $doctorId, string $date, int $duration): array {
    $weekday = (int)date('N', strtotime($date));
    $stmt = db()->prepare('SELECT start_time, end_time, slot_step_minutes FROM doctor_working_hours WHERE doctor_id = ? AND weekday = ?');
    $stmt->execute([$doctorId, $weekday]);
    $blocks = $stmt->fetchAll();
    if (!$blocks) {
        return [];
    }
    $appointments = public_upcoming_appointments([$doctorId], 1);
    $slots = [];
    foreach ($blocks as $block) {
        $slots = array_merge($slots, public_generate_slots($date, $block['start_time'], $block['end_time'], (int)$block['slot_step_minutes'], $duration, $appointments, $doctorId));
    }
    return $slots;
}

function public_generate_slots(string $date, string $startTime, string $endTime, int $step, int $duration, array $appointments = [], int $doctorId = 0): array {
    $slots = [];
    $start = strtotime($date . ' ' . $startTime);
    $end = strtotime($date . ' ' . $endTime);
    if ($start === false || $end === false) {
        return $slots;
    }
    for ($t = $start; $t + ($duration * 60) <= $end; $t += ($step * 60)) {
        $slot = date('H:i', $t);
        if ($doctorId && public_slot_overlaps($doctorId, $date, $slot, $duration, $appointments)) {
            continue;
        }
        $slots[] = $slot;
    }
    return $slots;
}

function public_slot_overlaps(int $doctorId, string $date, string $slot, int $duration, array $appointments): bool {
    $dayAppts = $appointments[$doctorId][$date] ?? [];
    if (!$dayAppts) {
        return false;
    }
    $start = strtotime($date . ' ' . $slot);
    $end = $start + ($duration * 60);
    foreach ($dayAppts as $appt) {
        $aStart = strtotime($appt['starts_at']);
        $aEnd = strtotime($appt['ends_at']);
        if ($start < $aEnd && $end > $aStart) {
            return true;
        }
    }
    return false;
}

function public_rate_limit_ok(string $key, int $max, int $window): bool {
    $stmt = db()->prepare('SELECT id, hits, reset_at FROM rate_limits WHERE rl_key = ? LIMIT 1');
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    $now = time();
    if (!$row) {
        $stmt = db()->prepare('INSERT INTO rate_limits (rl_key, hits, reset_at) VALUES (?,?,?)');
        $stmt->execute([$key, 1, date('Y-m-d H:i:s', $now + $window)]);
        return true;
    }
    $resetAt = strtotime($row['reset_at']);
    if ($resetAt <= $now) {
        $stmt = db()->prepare('UPDATE rate_limits SET hits = 1, reset_at = ? WHERE id = ?');
        $stmt->execute([date('Y-m-d H:i:s', $now + $window), $row['id']]);
        return true;
    }
    if ((int)$row['hits'] >= $max) {
        return false;
    }
    $stmt = db()->prepare('UPDATE rate_limits SET hits = hits + 1 WHERE id = ?');
    $stmt->execute([$row['id']]);
    return true;
}

function public_client_ip(): string {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    return $ip;
}

function public_enqueue_booking_emails(array $doctor, string $patientName, string $phone, string $email, string $startsAt, string $serviceName): void {
    $date = date('d/m/Y', strtotime($startsAt));
    $time = date('H:i', strtotime($startsAt));
    $doctorSubject = 'Nouvelle demande de rendez-vous';
    $doctorMessage = "Nouvelle demande de rendez-vous.\nMedecin: " . ($doctor['full_name'] ?? '') . "\nDate: {$date} {$time}\nService: {$serviceName}\nPatient: {$patientName} ({$phone})\nEmail: {$email}";
    enqueue_outbox_email((int)$doctor['id'], $doctor['email'], $doctorSubject, $doctorMessage);

    $patientSubject = 'Demande de rendez-vous envoyee';
    $patientMessage = "Votre demande a ete envoyee.\nMedecin: " . ($doctor['full_name'] ?? '') . "\nDate: {$date} {$time}\nService: {$serviceName}\nAdresse: " . ($doctor['address_public'] ?? '');
    enqueue_outbox_email((int)$doctor['id'], $email, $patientSubject, $patientMessage);
}

