<?php
$title = 'Profil du medecin';
$icons = [
    'map' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-6-5.33-6-10a6 6 0 0 1 12 0c0 4.67-6 10-6 10z"></path><circle cx="12" cy="11" r="2"></circle></svg>',
    'phone' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.9v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.18 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.72c.12.81.3 1.6.54 2.36a2 2 0 0 1-.45 2.11L8.1 9.1a16 16 0 0 0 6 6l.91-1.09a2 2 0 0 1 2.11-.45c.76.24 1.55.42 2.36.54A2 2 0 0 1 22 16.9z"></path></svg>',
];
ob_start();
?>
<main class="public-main">
    <section class="public-doctor">
        <div class="public-container doctor-profile-grid">
            <div>
                <div class="doctor-profile-card">
                    <?php if (!empty($doctor['public_photo'])): ?>
                        <img class="doctor-avatar large" src="<?= e($doctor['public_photo']) ?>" alt="<?= e($doctor['full_name']) ?>">
                    <?php else: ?>
                        <div class="doctor-avatar large"><?= e(strtoupper(substr($doctor['full_name'], 0, 1))) ?></div>
                    <?php endif; ?>
                    <div>
                        <h2><?= e($doctor['full_name']) ?></h2>
                        <p><?= e($doctor['specialty'] ?? 'Medecin') ?></p>
                        <span class="doctor-location"><?= $icons['map'] ?><?= e($doctor['city'] ?? '') ?><?= $doctor['address_public'] ? ' · ' . e($doctor['address_public']) : '' ?></span>
                        <?php if (!empty($doctor['public_phone'])): ?>
                            <span class="doctor-location"><?= $icons['phone'] ?><?= e($doctor['public_phone']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="doctor-bio">
                    <h3>Presentation</h3>
                    <p><?= e($doctor['bio_public'] ?? 'Medecin independant disponible sur rendez-vous.') ?></p>
                </div>

                <div class="doctor-services">
                    <h3>Services</h3>
                    <?php if (!$services): ?>
                        <p>Aucun service public pour le moment.</p>
                    <?php else: ?>
                        <ul>
                            <?php foreach ($services as $service): ?>
                                <li>
                                    <span><?= e($service['name']) ?></span>
                                    <span><?= e($service['default_duration_minutes']) ?> min</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

            <aside class="public-booking-card">
                <h3>Prendre rendez-vous</h3>
                <form method="post" action="?r=public-book" class="public-booking-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="slug" value="<?= e($doctor['public_slug']) ?>">
                    <input type="hidden" name="slot" id="slot_time">
                    <input type="hidden" name="date" id="slot_date">
                    <input type="hidden" name="website" class="hp-field" value="">

                    <label>Service
                        <select name="service_id" id="service_id" required>
                            <option value="">Selectionner</option>
                            <?php foreach ($services as $service): ?>
                                <option value="<?= e($service['id']) ?>" data-duration="<?= e($service['default_duration_minutes']) ?>">
                                    <?= e($service['name']) ?> (<?= e($service['default_duration_minutes']) ?> min)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label>Date
                        <input type="date" name="date" id="slot_date_input" required>
                    </label>

                    <div>
                        <div class="booking-label">Creneaux disponibles</div>
                        <div class="public-slots" id="slots"></div>
                    </div>

                    <label>Nom complet
                        <input type="text" name="full_name" required>
                    </label>
                    <label>Telephone
                        <input type="text" name="phone" required>
                    </label>
                    <label>Email
                        <input type="email" name="email" required>
                    </label>

                    <button type="submit" class="public-btn primary full">Envoyer la demande</button>
                </form>
            </aside>
        </div>
    </section>
</main>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
