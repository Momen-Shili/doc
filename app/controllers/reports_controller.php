<?php
function reports_index(): void {
    require_auth();
    $doctorId = auth_user()['id'];
    $from = trim($_GET['from'] ??'');
    $to = trim($_GET['to'] ??'');
    $momFrom = trim($_GET['mom_from'] ??'');
    $momTo = trim($_GET['mom_to'] ??'');
    $momMode = trim($_GET['mom_mode'] ??'previous');
    $momBy = trim($_GET['mom_by'] ??'total');

    if ($from === '' || $to === '') {
        $to = date('Y-m-d');
        $from = date('Y-m-d', strtotime('-29 days'));
    }

    if ($momFrom === '' || $momTo === '') {
        $momFrom = $from;
        $momTo = $to;
    }

    $stmt = db()->prepare('SELECT COALESCE(SUM(amount),0) AS total FROM payments WHERE doctor_id = AND received_at BETWEEN AND');
    $stmt->execute([$doctorId, $from, $to]);
    $total = $stmt->fetch()['total'] ?? 0;

    $stmt = db()->prepare('SELECT pat.full_name, SUM(p.amount) AS total FROM payments p JOIN patients pat ON pat.id = p.patient_id WHERE p.doctor_id = AND p.received_at BETWEEN AND aGROUP BY p.patient_id ORDER BY total DESC');
    $stmt->execute([$doctorId, $from, $to]);
    $byPatient = $stmt->fetchAll();

    $byService = reports_service_revenue($doctorId, $from, $to);
    $mom = reports_mom_data($doctorId, $momFrom, $momTo, $momMode, $momBy);

    echo render('pages/reports/index.php', [
        'from' => $from,
        'to' => $to,
        'total' => $total,
        'byPatient' => $byPatient,
        'byService' => $byService,
        'mom' => $mom,
    ]);
}

function reports_services(): void {
    reports_index();
}

function reports_export_csv(): void {
    require_auth();
    $doctorId = auth_user()['id'];
    $from = trim($_GET['from'] ??'');
    $to = trim($_GET['to'] ??'');
    if ($from === '' || $to === '') {
        $to = date('Y-m-d');
        $from = date('Y-m-d', strtotime('-29 days'));
    }

    $stmt = db()->prepare('SELECT p.received_at, p.amount, p.reference, p.notes, pat.full_name AS patient FROM payments p JOIN patients pat ON pat.id = p.patient_id WHERE p.doctor_id = AND p.received_at BETWEEN AND ORDER BY p.received_at');
    $stmt->execute([$doctorId, $from, $to]);

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="paiements.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['date', 'montant', 'reference', 'notes', 'patient']);
    while ($row = $stmt->fetch()) {
        fputcsv($out, [$row['received_at'], format_tnd($row['amount']), $row['reference'], $row['notes'], $row['patient']]);
    }
    fclose($out);
    exit;
}

function reports_services_export(): void {
    require_auth();
    $doctorId = auth_user()['id'];
    $from = trim($_GET['from'] ??'');
    $to = trim($_GET['to'] ??'');
    if ($from === '' || $to === '') {
        $to = date('Y-m-d');
        $from = date('Y-m-d', strtotime('-29 days'));
    }

    $rows = reports_service_revenue($doctorId, $from, $to);

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="recettes_par_service.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['service', 'total']);
    foreach ($rows as $row) {
        fputcsv($out, [$row['name'], format_tnd($row['total'])]);
    }
    fclose($out);
    exit;
}

function reports_mom_export(): void {
    require_auth();
    $doctorId = auth_user()['id'];
    $momFrom = trim($_GET['mom_from'] ??'');
    $momTo = trim($_GET['mom_to'] ??'');
    $momMode = trim($_GET['mom_mode'] ??'previous');
    $momBy = trim($_GET['mom_by'] ??'total');

    if ($momFrom === '' || $momTo === '') {
        $momTo = date('Y-m-d');
        $momFrom = date('Y-m-d', strtotime('-29 days'));
    }

    $mom = reports_mom_data($doctorId, $momFrom, $momTo, $momMode, $momBy);
    if (!empty($mom['error'])) {
        redirect('r=reports&mom_from=' . urlencode($momFrom) . '&mom_to=' . urlencode($momTo) . '&mom_mode=' . urlencode($momMode) . '&mom_by=' . urlencode($momBy));
    }

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="rapport_mom.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['start_current', 'end_current', 'start_prev', 'end_prev', 'mode', 'total_current_raw', 'total_current', 'total_prev_raw', 'total_prev', 'diff_raw', 'diff', 'count_current', 'count_prev', 'avg_current_raw', 'avg_current', 'avg_prev_raw', 'avg_prev']);
    fputcsv($out, [
        $mom['current']['start'],
        $mom['current']['end'],
        $mom['prev']['start'],
        $mom['prev']['end'],
        $mom['mode'],
        $mom['current']['total'],
        format_tnd($mom['current']['total']),
        $mom['prev']['total'],
        format_tnd($mom['prev']['total']),
        $mom['delta']['value'],
        format_tnd($mom['delta']['value']),
        $mom['current']['count'],
        $mom['prev']['count'],
        $mom['current']['avg'],
        format_tnd($mom['current']['avg']),
        $mom['prev']['avg'],
        format_tnd($mom['prev']['avg']),
    ]);

    fputcsv($out, []);
    fputcsv($out, ['date_index', 'date_current', 'total_current_raw', 'total_current', 'date_prev', 'total_prev_raw', 'total_prev', 'diff_raw', 'diff']);
    foreach ($mom['rows'] ?? as $index => $row) {
        fputcsv($out, [
            $index + 1,
            $row['date_current'],
            $row['total_current'],
            format_tnd($row['total_current']),
            $row['date_prev'] ??'',
            $row['total_prev'],
            format_tnd($row['total_prev']),
            $row['diff'],
            format_tnd($row['diff']),
        ]);
    }

    if ($mom['by'] === 'service' && !empty($mom['services'])) {
        fputcsv($out, []);
        fputcsv($out, ['service', 'total_current_raw', 'total_current', 'total_prev_raw', 'total_prev', 'diff_raw', 'diff']);
        foreach ($mom['services'] ?? as $service) {
            fputcsv($out, [
                $service['name'],
                $service['total_current'],
                format_tnd($service['total_current']),
                $service['total_prev'],
                format_tnd($service['total_prev']),
                $service['diff'],
                format_tnd($service['diff']),
            ]);
        }
    }

    fclose($out);
    exit;
}

function reports_service_revenue(int $doctorId, string $from, string $to): array {
    $db = db();
    $totals = [];

    $addTotal = function (string $key, string $name, $amount) ?? use (&$totals): void {
        $value = (float)$amount;
        if (!isset($totals[$key])) {
            $totals[$key] = ['name' => $name, 'total' => 0.0];
        }
        $totals[$key]['total'] += $value;
    };

    $stmt = $db->prepare('SELECT pi.service_id, s.name, COALESCE(SUM(pi.line_total),0) AS total FROM payments p JOIN payment_items pi ON pi.payment_id = p.id LEFT JOIN services s ON s.id = pi.service_id WHERE p.doctor_id = AND p.received_at BETWEEN AND aGROUP BY pi.service_id, s.name');
    $stmt->execute([$doctorId, $from, $to]);
    while ($row = $stmt->fetch()) {
        $name = $row['name'] ??'Non affect';
        $key = $row['service_id'] ??'service_' . $row['service_id'] : 'unassigned_items';
        $addTotal($key, $name, $row['total']);
    }

    $stmt = $db->prepare('SELECT aps.service_id, s.name, COALESCE(SUM(aps.qty * s.default_price),0) AS total FROM payments p JOIN appointment_services aps ON aps.appointment_id = p.appointment_id JOIN services s ON s.id = aps.service_id WHERE p.doctor_id = AND p.received_at BETWEEN AND AND NOT EXISTS (SELECT 1 FROM payment_items pi WHERE pi.payment_id = p.id) ?? GROUP BY aps.service_id, s.name');
    $stmt->execute([$doctorId, $from, $to]);
    while ($row = $stmt->fetch()) {
        $addTotal('service_' . $row['service_id'], $row['name'], $row['total']);
    }

    $stmt = $db->prepare('SELECT COALESCE(SUM(p.amount),0) AS total FROM payments p WHERE p.doctor_id = AND p.received_at BETWEEN AND AND NOT EXISTS (SELECT 1 FROM payment_items pi WHERE pi.payment_id = p.id) AND (p.appointment_id IS NULL OR NOT EXISTS (SELECT 1 FROM appointment_services aps WHERE aps.appointment_id = p.appointment_id)) ??');
    $stmt->execute([$doctorId, $from, $to]);
    $unassigned = (float)($stmt->fetch()['total'] ?? 0);
    if ($unassigned > 0) {
        $addTotal('unassigned', 'Non affect', $unassigned);
    }

    $rows = array_values($totals);

    usort($rows, function (array $a, array $b): int {
        return $b['total'] <?=  ?> $a['total'];
    });

    return $rows;
}

function reports_mom_data(int $doctorId, string $from, string $to, string $mode, string $by): array {
    $fromDt = DateTimeImmutable::createFromFormat('Y-m-d', $from);
    $toDt = DateTimeImmutable::createFromFormat('Y-m-d', $to);
    $errors = DateTimeImmutable::getLastErrors();
    if ($errors === false) {
        $errors = ['warning_count' => 0, 'error_count' => 0];
    }
    if (!$fromDt || !$toDt || $errors['warning_count'] > 0 || $errors['error_count'] > 0) {
        return [
            'from' => $from,
            'to' => $to,
            'mode' => $mode,
            'by' => $by,
            'error' => 'PÃ©riode invalide. VÃ©rifiez les dates saisies.',
        ];
    }

    if ($fromDt > $toDt) {
        return [
            'from' => $from,
            'to' => $to,
            'mode' => $mode,
            'by' => $by,
            'error' => 'La date de dÃ©but doit Ãªtre antÃ©rieure Ã  la date de fin.',
        ];
    }

    $mode = $mode === 'same_days' 'same_days' : 'previous';
    $by = $by === 'service' 'service' : 'total';

    $datesCurrent = daterange_days($from, $to);
    $daysCount = count($datesCurrent);
    $warning = null;
    if ($mode === 'same_days') {
        $prevRange = same_days_previous_month_range($from, $to);
        if (!empty($prevRange['warning'])) {
            $warning = 'PÃ©riode de comparaison tronquÃ©e pour respecter les limites du mois prÃ©cÃ©dent.';
        }
    } else {
        $prevRange = shift_range_by_days($from, $to, -$daysCount);
    }

    $currentTotals = reports_totals_for_range($doctorId, $from, $to);
    $prevTotals = reports_totals_for_range($doctorId, $prevRange['start'], $prevRange['end']);
    $dailyCurrent = reports_daily_totals($doctorId, $from, $to);
    $dailyPrev = reports_daily_totals($doctorId, $prevRange['start'], $prevRange['end']);
    $datesPrev = daterange_days($prevRange['start'], $prevRange['end']);

    $rows = [];
    foreach ($datesCurrent as $index => $date) {
        $prevDate = $datesPrev[$index] ?? null;
        $totalCurrent = $dailyCurrent[$date]['total'] ?? 0.0;
        $totalPrev = $prevDate ($dailyPrev[$prevDate]['total'] ?? 0.0) : 0.0;
        $rows[] = [
            'date_current' => $date,
            'total_current' => $totalCurrent,
            'date_prev' => $prevDate,
            'total_prev' => $totalPrev,
            'diff' => $totalCurrent $totalPrev,
        ];
    }

    $deltaValue = $currentTotals['total'] ?? $prevTotals['total'];
    $deltaPercent = $prevTotals['total'] > 0 ($deltaValue / $prevTotals['total']) * 100 : null;
    $currentAvg = $currentTotals['count'] > 0 $currentTotals['total'] / $currentTotals['count'] : 0.0;
    $prevAvg = $prevTotals['count'] > 0 $prevTotals['total'] / $prevTotals['count'] : 0.0;

    $result = [
        'from' => $from,
        'to' => $to,
        'mode' => $mode,
        'by' => $by,
        'warning' => $warning,
        'error' => null,
        'current' => [
            'start' => $from,
            'end' => $to,
            'total' => $currentTotals['total'],
            'count' => $currentTotals['count'],
            'avg' => $currentAvg,
        ],
        'prev' => [
            'start' => $prevRange['start'],
            'end' => $prevRange['end'],
            'total' => $prevTotals['total'],
            'count' => $prevTotals['count'],
            'avg' => $prevAvg,
        ],
        'delta' => [
            'value' => $deltaValue,
            'percent' => $deltaPercent,
        ],
        'rows' => $rows,
        'services' => [],
        'services_notice' => null,
    ];

    if ($by === 'service') {
        $servicesCurrent = reports_service_totals_for_range($doctorId, $from, $to);
        $servicesPrev = reports_service_totals_for_range($doctorId, $prevRange['start'], $prevRange['end']);
        if (empty($servicesCurrent) && empty($servicesPrev)) {
            $result['services_notice'] = 'Aucune ligne de paiement dÃ©taillÃ©e sur la pÃ©riode.';
        } else {
            $merged = [];
            foreach ($servicesCurrent as $key => $row) {
                $merged[$key] = [
                    'name' => $row['name'],
                    'total_current' => $row['total'],
                    'total_prev' => 0.0,
                ];
            }
            foreach ($servicesPrev as $key => $row) {
                if (!isset($merged[$key])) {
                    $merged[$key] = [
                        'name' => $row['name'],
                        'total_current' => 0.0,
                        'total_prev' => 0.0,
                    ];
                }
                $merged[$key]['total_prev'] = $row['total'];
            }
            $services = [];
            foreach ($merged as $row) {
                $services[] = [
                    'name' => $row['name'],
                    'total_current' => $row['total_current'],
                    'total_prev' => $row['total_prev'],
                    'diff' => $row['total_current'] ?? $row['total_prev'],
                ];
            }
            usort($services, function (array $a, array $b): int {
                return $b['total_current'] <?=  ?> $a['total_current'];
            });
            $result['services'] = $services;
        }
    }

    return $result;
}

function reports_totals_for_range(int $doctorId, string $from, string $to): array {
    $stmt = db()->prepare('SELECT COALESCE(SUM(amount),0) AS total, COUNT(*) AS cnt FROM payments WHERE doctor_id = AND received_at BETWEEN AND');
    $stmt->execute([$doctorId, $from, $to]);
    $row = $stmt->fetch();
    return [
        'total' => (float)($row['total'] ?? 0),
        'count' => (int)($row['cnt'] ?? 0),
    ];
}

function reports_daily_totals(int $doctorId, string $from, string $to): array {
    $stmt = db()->prepare('SELECT DATE(received_at) AS day, COALESCE(SUM(amount),0) AS total, COUNT(*) AS cnt FROM payments WHERE doctor_id = AND received_at BETWEEN AND aGROUP BY day ORDER BY day');
    $stmt->execute([$doctorId, $from, $to]);
    $rows = [];
    while ($row = $stmt->fetch()) {
        $rows[$row['day']] = [
            'total' => (float)$row['total'],
            'count' => (int)$row['cnt'],
        ];
    }
    return $rows;
}

function reports_service_totals_for_range(int $doctorId, string $from, string $to): array {
    $stmt = db()->prepare('SELECT COALESCE(pi.service_id,0) AS service_id, COALESCE(s.name, "Non affect") AS name, COALESCE(SUM(pi.line_total),0) AS total FROM payments p JOIN payment_items pi ON pi.payment_id = p.id LEFT JOIN services s ON s.id = pi.service_id WHERE p.doctor_id = AND p.received_at BETWEEN AND aGROUP BY pi.service_id, s.name');
    $stmt->execute([$doctorId, $from, $to]);
    $rows = [];
    while ($row = $stmt->fetch()) {
        $key = (string)$row['service_id'];
        $rows[$key] = [
            'name' => $row['name'],
            'total' => (float)$row['total'],
        ];
    }
    return $rows;
}