<?php
$title = 'Accueil';
$specialties = [
    ['label' => 'Dermatologie', 'icon' => 'stethoscope'],
    ['label' => 'Cardiologie', 'icon' => 'heart'],
    ['label' => 'Pediatrie', 'icon' => 'baby'],
    ['label' => 'Gynecologie', 'icon' => 'user'],
    ['label' => 'Ophtalmologie', 'icon' => 'eye'],
    ['label' => 'Dentiste', 'icon' => 'tooth'],
    ['label' => 'ORL', 'icon' => 'ear'],
    ['label' => 'Generaliste', 'icon' => 'plus'],
];
$icons = [
    'search' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></svg>',
    'map' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-6-5.33-6-10a6 6 0 0 1 12 0c0 4.67-6 10-6 10z"></path><circle cx="12" cy="11" r="2"></circle></svg>',
    'doctor' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4"></path><circle cx="12" cy="10" r="3"></circle><path d="M4 22a8 8 0 0 1 16 0"></path></svg>',
    'calendar' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M16 2v4M8 2v4M3 10h18"></path></svg>',
    'clock' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg>',
    'stethoscope' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 5a2 2 0 0 1 4 0v3a4 4 0 0 1-4 4H2"></path><path d="M8 5a2 2 0 0 1 4 0v3a4 4 0 0 1-4 4H6"></path><path d="M12 13v2a4 4 0 0 0 4 4h1"></path><circle cx="20" cy="17" r="3"></circle></svg>',
    'heart' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"></path></svg>',
    'baby' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M8 14a4 4 0 0 0 8 0"></path><path d="M9 9h.01M15 9h.01"></path></svg>',
    'user' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="7" r="4"></circle><path d="M5.5 21a8.5 8.5 0 0 1 13 0"></path></svg>',
    'eye' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"></path><circle cx="12" cy="12" r="3"></circle></svg>',
    'tooth' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a7 7 0 0 0-7 7c0 3 2 7 2 10a3 3 0 0 0 6 0c0-2 1-4 2-4s2 2 2 4a3 3 0 0 0 6 0c0-3 2-7 2-10a7 7 0 0 0-7-7z"></path></svg>',
    'ear' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 8a6 6 0 0 1 12 0c0 4-3 6-3 9a3 3 0 0 1-6 0"></path><path d="M9 12a3 3 0 0 1 6 0"></path></svg>',
    'plus' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"></path></svg>',
    'shield' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>',
];
ob_start();
?>
<main class="public-main">
    <section class="public-hero">
        <div class="public-container public-hero-grid">
            <div class="public-hero-copy">
                <span class="public-badge">Plateforme medicale</span>
                <h1>Trouvez votre medecin et prenez rendez-vous en toute simplicite</h1>
                <p>Recherchez un medecin en Tunisie, choisissez un service et reservez un creneau fiable sans creer de compte patient.</p>
                <form class="public-search" action="?r=public-doctors" method="get" id="public-search-form">
                    <input type="hidden" name="r" value="public-doctors">
                    <input type="hidden" name="lat" id="geo-lat">
                    <input type="hidden" name="lng" id="geo-lng">
                    <input type="hidden" name="around" id="geo-around" value="0">
                    <div class="field">
                        <span class="field-icon"><?= $icons['stethoscope'] ?></span>
                        <input type="text" name="specialty" placeholder="Specialite">
                    </div>
                    <div class="field">
                        <span class="field-icon"><?= $icons['map'] ?></span>
                        <input type="text" name="city" placeholder="Ville">
                    </div>
                    <div class="field">
                        <span class="field-icon"><?= $icons['doctor'] ?></span>
                        <input type="text" name="q" placeholder="Nom du medecin">
                    </div>
                    <button type="submit" class="public-btn primary">Trouver un medecin</button>
                </form>
                <div class="geo-row">
                    <button type="button" class="public-btn ghost small" id="geo-toggle">Autour de vous</button>
                    <select name="radius" id="geo-radius" class="geo-select" form="public-search-form" disabled>
                        <option value="5">5 km</option>
                        <option value="10">10 km</option>
                        <option value="25">25 km</option>
                    </select>
                    <span class="geo-city" id="geo-city"></span>
                </div>
                <div class="public-hero-note">Confirmation par email. Paiement en cabinet.</div>
            </div>
            <div class="public-hero-visual">
                <div class="hero-illustration">
                    <div class="hero-card hero-card-main">
                        <div class="hero-card-header">
                            <span>Calendrier</span>
                            <span class="muted">Janvier 2026</span>
                        </div>
                        <div class="hero-card-grid">
                            <span>1</span><span>2</span><span class="active">3</span><span>4</span><span>5</span>
                            <span>6</span><span>7</span><span>8</span><span>9</span><span>10</span>
                        </div>
                    </div>
                    <div class="hero-card hero-card-popup">
                        <div class="hero-pill">RDV confirme</div>
                        <strong>Dr. Selma H.</strong>
                        <span class="muted">09:30 - 10:00</span>
                    </div>
                    <div class="hero-card hero-card-profile">
                        <div class="avatar">SH</div>
                        <div>
                            <strong>Dr. Selma H.</strong>
                            <span class="muted">Dermatologie</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="public-tiles">
        <div class="public-container">
            <div class="public-section-title align-left">
                <span>Que cherchez-vous ?</span>
                <h2>Une seule plateforme pour vos rendez-vous medicaux</h2>
            </div>
            <div class="tiles-grid">
                <div class="tile">
                    <div class="tile-icon"><?= $icons['doctor'] ?></div>
                    <div>
                        <h3>Consulter un medecin</h3>
                        <p>Prenez rendez-vous en cabinet ou en teleconsultation.</p>
                    </div>
                </div>
                <div class="tile">
                    <div class="tile-icon"><?= $icons['calendar'] ?></div>
                    <div>
                        <h3>Creneaux fiables</h3>
                        <p>Disponibilites actualisees et confirmation rapide.</p>
                    </div>
                </div>
                <div class="tile">
                    <div class="tile-icon"><?= $icons['shield'] ?></div>
                    <div>
                        <h3>Plateforme de confiance</h3>
                        <p>Medecins verifies et informations claires.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="public-specialties">
        <div class="public-container">
            <div class="public-section-title align-left">
                <span>Specialites</span>
                <h2>Trouvez rapidement un medecin par specialite</h2>
            </div>
            <div class="specialty-grid">
                <?php foreach ($specialties as $specialty): ?>
                    <div class="specialty-card">
                        <div class="specialty-icon">
                            <?= $icons[$specialty['icon']] ?>
                        </div>
                        <span><?= e($specialty['label']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="public-featured">
        <div class="public-container">
            <div class="public-section-title align-left">
                <span>Medecins</span>
                <h2>Medecins mis en avant</h2>
            </div>
            <div class="doctor-grid">
                <?php if (!$featured): ?>
                    <p>Aucun medecin public pour le moment.</p>
                <?php else: ?>
                    <?php foreach ($featured as $doc): ?>
                        <div class="doctor-card">
                            <div class="doctor-header">
                                <?php if (!empty($doc['public_photo'])): ?>
                                    <img class="doctor-avatar" src="<?= e($doc['public_photo']) ?>" alt="<?= e($doc['full_name']) ?>">
                                <?php else: ?>
                                    <div class="doctor-avatar"><?= e(strtoupper(substr($doc['full_name'], 0, 1))) ?></div>
                                <?php endif; ?>
                                <div>
                                    <h3><?= e($doc['full_name']) ?></h3>
                                    <p><?= e($doc['specialty'] ?? 'Medecin generaliste') ?></p>
                                    <span class="doctor-location"><?= $icons['map'] ?><?= e($doc['city'] ?? 'Tunisie') ?></span>
                                </div>
                            </div>
                            <div class="doctor-slot">
                                <?= $icons['clock'] ?>
                                <?php if (!empty($doc['next_slot'])): ?>
                                    Prochain creneau: <?= e(date('d/m/Y H:i', strtotime($doc['next_slot']))) ?>
                                <?php else: ?>
                                    Aucun creneau disponible
                                <?php endif; ?>
                            </div>
                            <?php if (isset($doc['distance_km']) && $doc['distance_km'] !== null): ?>
                                <div class="doctor-distance">A <?= number_format((float)$doc['distance_km'], 1, ',', ' ') ?> km</div>
                            <?php endif; ?>
                            <a class="public-btn primary full" href="?r=public-doctor&slug=<?= e($doc['public_slug']) ?>">Prendre rendez-vous</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="public-cta">
        <div class="public-container cta-inner">
            <div>
                <h2>Besoin d'un rendez-vous rapidement ?</h2>
                <p>Choisissez votre medecin, votre service et votre creneau en quelques minutes.</p>
            </div>
            <a class="public-btn primary small" href="?r=public-doctors">Trouver un medecin</a>
        </div>
    </section>
</main>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
