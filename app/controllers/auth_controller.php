<?php
function auth_login(): void {
    require_guest();
    $error = null;

    if (is_post()) {
        verify_csrf();
        $rate = rate_limit_status('doctor_login');
        if (!$rate['allowed']) {
            $error = 'Trop de tentatives. Ressayez plus tard.';
            echo render('pages/auth/login.php', ['error' => $error]);
            return;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $error = 'Email et mot de passe requis.';
        } else {
            $stmt = db()->prepare('SELECT id, password_hash, is_active, suspended_at FROM doctors WHERE email = ?');
            $stmt->execute([$email]);
            $doctor = $stmt->fetch();
            if (!$doctor || !password_verify($password, $doctor['password_hash'])) {
                $error = 'Identifiants invalides.';
                rate_limit_register('doctor_login');
            } elseif ((int)$doctor['is_active'] !== 1) {
                $error = 'Compte desactive.';
            } elseif (!empty($doctor['suspended_at'])) {
                $error = 'Compte suspendu. Contactez l\'administrateur.';
            } else {
                rate_limit_clear('doctor_login');
                login_doctor((int)$doctor['id']);
                redirect('?r=dashboard');
            }
        }
    }

    echo render('pages/auth/login.php', ['error' => $error]);
}

function auth_register(): void {
    require_guest();
    $errors = [];

    if (is_post()) {
        verify_csrf();
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $specialty = trim($_POST['specialty'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if ($fullName === '') {
            $errors[] = 'Nom complet requis.';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalide.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Mot de passe : 8 caracteres minimum.';
        }

        if (!$errors) {
            $stmt = db()->prepare('SELECT id FROM doctors WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = 'Email deja utilise.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = db()->prepare('INSERT INTO doctors (full_name, email, password_hash, specialty, phone) VALUES (?,?,?,?,?)');
                $stmt->execute([$fullName, $email, $hash, $specialty !== '' ? $specialty : null, $phone !== '' ? $phone : null]);
                login_doctor((int)db()->lastInsertId());
                redirect('?r=dashboard');
            }
        }
    }

    echo render('pages/auth/register.php', ['errors' => $errors]);
}

function auth_logout(): void {
    if (is_impersonating()) {
        stop_impersonation();
        redirect('admin/?r=dashboard');
    }
    logout_doctor();
    redirect('?r=login');
}

function auth_profile(): void {
    require_auth();
    $doctor = auth_user();
    $errors = [];

    if (is_post()) {
        verify_csrf();
        $fullName = trim($_POST['full_name'] ?? '');
        $specialty = trim($_POST['specialty'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $publicPhone = trim($_POST['public_phone'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $address = trim($_POST['address_public'] ?? '');
        $bio = trim($_POST['bio_public'] ?? '');
        $latitude = trim($_POST['latitude'] ?? '');
        $longitude = trim($_POST['longitude'] ?? '');

        if ($fullName === '') {
            $errors[] = 'Nom complet requis.';
        }

        $latValue = null;
        $lngValue = null;
        if ($latitude !== '' && $longitude !== '') {
            $lat = (float)$latitude;
            $lng = (float)$longitude;
            if ($lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180) {
                $latValue = $lat;
                $lngValue = $lng;
            }
        }

        $publicPhoto = $doctor['public_photo'] ?? null;
        if (!empty($_FILES['public_photo']) && $_FILES['public_photo']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['public_photo'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors[] = 'Erreur lors du televersement.';
            } else {
                if ($file['size'] > 10 * 1024 * 1024) {
                    $errors[] = 'Photo trop volumineuse (10 Mo max).';
                } else {
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png'];
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = $finfo ? finfo_file($finfo, $file['tmp_name']) : '';
                    if ($finfo) {
                        finfo_close($finfo);
                    }
                    if (!in_array($ext, $allowed, true) || !in_array($mime, ['image/jpeg', 'image/png'], true)) {
                        $errors[] = 'Format de photo invalide.';
                    } else {
                        $dir = __DIR__ . '/../public/uploads/doctor_' . $doctor['id'] . '/public';
                        if (!is_dir($dir)) {
                            mkdir($dir, 0775, true);
                        }
                        $name = 'photo_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                        $dest = $dir . '/' . $name;
                        if (move_uploaded_file($file['tmp_name'], $dest)) {
                            $publicPhoto = 'uploads/doctor_' . $doctor['id'] . '/public/' . $name;
                        } else {
                            $errors[] = 'Impossible de sauvegarder la photo.';
                        }
                    }
                }
            }
        }

        $slug = $doctor['public_slug'] ?? '';
        if ($slug === '') {
            $base = slugify($fullName);
            $slug = unique_doctor_slug((int)$doctor['id'], $base);
        }

        if (!$errors) {
            $stmt = db()->prepare('UPDATE doctors SET full_name = ?, specialty = ?, phone = ?, public_phone = ?, city = ?, address_public = ?, bio_public = ?, public_photo = ?, public_slug = ?, latitude = ?, longitude = ?, geo_updated_at = ? WHERE id = ?');
            $stmt->execute([
                $fullName,
                $specialty !== '' ? $specialty : null,
                $phone !== '' ? $phone : null,
                $publicPhone !== '' ? $publicPhone : null,
                $city !== '' ? $city : null,
                $address !== '' ? $address : null,
                $bio !== '' ? $bio : null,
                $publicPhoto,
                $slug,
                $latValue,
                $lngValue,
                ($latValue !== null && $lngValue !== null) ? date('Y-m-d H:i:s') : null,
                $doctor['id'],
            ]);
            flash('success', 'Profil mis a jour.');
            redirect('?r=profile');
        }
    }

    echo render('pages/auth/profile.php', ['doctor' => $doctor, 'errors' => $errors]);
}

function doctor_geocode_address(): void {
    require_auth();
    header('Content-Type: application/json; charset=utf-8');
    if (!is_post()) {
        http_response_code(405);
        echo json_encode(['error' => 'Methode non autorisee.'], JSON_UNESCAPED_UNICODE);
        return;
    }
    verify_csrf();
    $doctorId = (int)auth_user()['id'];
    $rate = rate_limit_status('geo_' . $doctorId, 5, 60, 300);
    if (!$rate['allowed']) {
        echo json_encode(['error' => 'Trop de demandes. Reessayez.'], JSON_UNESCAPED_UNICODE);
        return;
    }
    $address = trim($_POST['address_public'] ?? '');
    $city = trim($_POST['city'] ?? '');
    if ($address === '' && $city === '') {
        echo json_encode(['error' => 'Adresse requise.'], JSON_UNESCAPED_UNICODE);
        return;
    }
    $query = trim($address . ' ' . $city . ' Tunisie');
    $result = geocode_address($query);
    if (!$result) {
        echo json_encode(['error' => 'Adresse introuvable.'], JSON_UNESCAPED_UNICODE);
        return;
    }
    echo json_encode([
        'lat' => $result['lat'],
        'lng' => $result['lng'],
        'city' => $result['city'] ?? $city,
        'address' => $result['address'] ?? null,
    ], JSON_UNESCAPED_UNICODE);
}
