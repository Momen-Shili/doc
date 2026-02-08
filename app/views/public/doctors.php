<?php
$title = 'Trouver un medecin';
$icons = [
    'map' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-6-5.33-6-10a6 6 0 0 1 12 0c0 4.67-6 10-6 10z"></path><circle cx="12" cy="11" r="2"></circle></svg>',
    'clock' => '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg>',
];
ob_start();
?>
<main class="public-main">
    <section class="public-directory">
        <div class="public-container directory-grid">
            <aside class="directory-filters">
                <h2>Filtrer</h2>
                <form method="get" class="filter-form" id="public-search-form">
                    <input type="hidden" name="r" value="public-doctors">
                    <input type="hidden" name="lat" id="geo-lat" value="<?= e((string)($lat ?? '')) ?>">
                    <input type="hidden" name="lng" id="geo-lng" value="<?= e((string)($lng ?? '')) ?>">
                    <input type="hidden" name="around" id="geo-around" value="<?= $around ? '1' : '0' ?>">
                    <label>Nom
                        <input type="text" name="q" placeholder="Nom" value="<?= e($q) ?>">
                    </label>
                    <label>Specialite
                        <input type="text" name="specialty" placeholder="Specialite" value="<?= e($specialty) ?>">
                    </label>
                    <label>Ville
                        <input type="text" name="city" placeholder="Ville" value="<?= e($city) ?>">
                    </label>
                    <label>Distance
                        <select name="radius" id="geo-radius" <?= $around ? '' : 'disabled' ?>>
                            <option value="5" <?= $radius === 5 ? 'selected' : '' ?>>5 km</option>
                            <option value="10" <?= $radius === 10 ? 'selected' : '' ?>>10 km</option>
                            <option value="25" <?= $radius === 25 ? 'selected' : '' ?>>25 km</option>
                        </select>
                    </label>
                    <button type="button" class="public-btn ghost small" id="geo-toggle"><?= $around ? 'Autour de vous' : 'Activer Autour de vous' ?></button>
                    <button class="public-btn primary full" type="submit">Rechercher</button>
                </form>
            </aside>
            <div>
                <div class="public-section-title align-left">
                    <span>Annuaire</span>
                    <h2>Trouvez le bon medecin</h2>
                    <p>Filtrez par specialite, ville ou nom.</p>
                </div>
                <div class="sort-row">
                    <label>Trier par
                        <select name="sort" id="sort-select">
                            <option value="relevance" <?= $sort === 'relevance' ? 'selected' : '' ?>>Pertinence</option>
                            <option value="distance" <?= $sort === 'distance' ? 'selected' : '' ?>>Distance</option>
                        </select>
                    </label>
                </div>
                <div class="doctor-grid">
                    <?php if (!$doctors): ?>
                        <p>Aucun medecin trouve.</p>
                    <?php else: ?>
                        <?php foreach ($doctors as $doc): ?>
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
                                        <span class="doctor-location"><?= $icons['map'] ?><?= e($doc['city'] ?? '') ?><?= $doc['address_public'] ? ' · ' . e($doc['address_public']) : '' ?></span>
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
        </div>
    </section>
</main>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
