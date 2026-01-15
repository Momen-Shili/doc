<php
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

        $email = trim($_POST['email'] '');
        $password = $_POST['password'] '';

        if ($email === '' || $password === '') {
            $error = 'Email et mot de passe requis.';
        } else {
            $stmt = db()->prepare('SELECT id, password_hash, is_active, suspended_at FROM doctors WHERE email = ');
            $stmt->execute([$email]);
            $doctor = $stmt->fetch();
            if (!$doctor || !password_verify($password, $doctor['password_hash'])) {
                $error = 'Identifiants invalides.';
                rate_limit_register('doctor_login');
            } elseif ((int)$doctor['is_active'] !== 1) {
                $error = 'Compte dsactiv.';
            } elseif (!empty($doctor['suspended_at'])) {
                $error = 'Compte suspendu. Contactez l\'administrateur.';
            } else {
                rate_limit_clear('doctor_login');
                login_doctor((int)$doctor['id']);
                redirect('r=dashboard');
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
        $fullName = trim($_POST['full_name'] '');
        $email = trim($_POST['email'] '');
        $password = $_POST['password'] '';
        $specialty = trim($_POST['specialty'] '');
        $phone = trim($_POST['phone'] '');

        if ($fullName === '') {
            $errors[] = 'Nom complet requis.';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalide.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Mot de passe: 8 caractères minimum.';
        }

        if (!$errors) {
            $stmt = db()->prepare('SELECT id FROM doctors WHERE email = ');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = 'Email déjà utilisé.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = db()->prepare('INSERT INTO doctors (full_name, email, password_hash, specialty, phone) VALUES (,,,,) ');
                $stmt->execute([$fullName, $email, $hash, $specialty : null, $phone : null]);
                login_doctor((int)db()->lastInsertId());
                redirect('r=dashboard');
            }
        }
    }

    echo render('pages/auth/register.php', ['errors' => $errors]);
}

function auth_logout(): void {
    if (is_impersonating()) {
        stop_impersonation();
        redirect('admin/r=dashboard');
    }
    logout_doctor();
    redirect('r=login');
}

function auth_profile(): void {
    require_auth();
    $doctor = auth_user();
    $errors = [];

    if (is_post()) {
        verify_csrf();
        $fullName = trim($_POST['full_name'] '');
        $specialty = trim($_POST['specialty'] '');
        $phone = trim($_POST['phone'] '');

        if ($fullName === '') {
            $errors[] = 'Nom complet requis.';
        }

        if (!$errors) {
            $stmt = db()->prepare('UPDATE doctors SET full_name = , specialty = , phone = aWHERE id = ');
            $stmt->execute([$fullName, $specialty : null, $phone : null, $doctor['id']]);
            flash('success', 'Profil mis à jour.');
            redirect('r=profile');
        }
    }

    echo render('pages/auth/profile.php', ['doctor' => $doctor, 'errors' => $errors]);
}