<?php
$title = $title ?? APP_NAME;
$theme = $_COOKIE['theme'] ?? 'light';
if (!in_array($theme, ['light', 'dark'], true)) {
    $theme = 'light';
}
$currentPath = $_SERVER['REQUEST_URI'] ?? '?r=public-home';
?>
<!doctype html>
<html class="h-full" lang="fr" data-theme="<?= e($theme) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset_url('UI/assets/theme.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('assets/public.css')) ?>">
</head>
<body class="h-full">
<div class="public-page">
    <header class="public-header">
        <div class="public-container public-header-inner public-header-bar">
            <a class="public-brand" href="?r=public-home">
                <span class="brand-dot">S</span>
                <span>Smart Care</span>
            </a>
            <nav class="public-nav">
                <a href="?r=public-home">Accueil</a>
                <a href="?r=public-doctors">Trouver un medecin</a>
                <a href="?r=public-doctors">Services</a>
                <a href="?r=public-home#about">A propos</a>
            </nav>
            <div class="public-actions">
                <form method="post" action="?r=public-theme-toggle">
                    <?= csrf_field() ?>
                    <input type="hidden" name="current" value="<?= e($theme) ?>">
                    <input type="hidden" name="redirect" value="<?= e($currentPath) ?>">
                    <button type="submit" class="public-btn ghost small"><?= $theme === 'dark' ? 'Mode clair' : 'Mode sombre' ?></button>
                </form>
                <a class="public-btn primary small" href="?r=public-doctors">Prendre RDV</a>
            </div>
        </div>
    </header>

    <?= $content ?>

    <footer class="public-footer">
        <div class="public-container public-footer-grid">
            <div>
                <div class="public-brand">
                    <span class="brand-dot">S</span>
                    <span>Smart Care</span>
                </div>
                <p>Plateforme de rendez-vous pour medecins independants.</p>
                <form class="public-newsletter">
                    <input type="email" placeholder="Votre email">
                    <button type="button">OK</button>
                </form>
            </div>
            <div>
                <h4>Liens utiles</h4>
                <a href="?r=public-home">Accueil</a>
                <a href="?r=public-doctors">Trouver un medecin</a>
                <a href="?r=public-home#about">A propos</a>
                <a href="?r=public-home#contact">Contact</a>
            </div>
            <div>
                <h4>Contact</h4>
                <div class="footer-contact">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.9v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.18 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.72c.12.81.3 1.6.54 2.36a2 2 0 0 1-.45 2.11L8.1 9.1a16 16 0 0 0 6 6l.91-1.09a2 2 0 0 1 2.11-.45c.76.24 1.55.42 2.36.54A2 2 0 0 1 22 16.9z"></path></svg>
                    <span>+216 71 000 000</span>
                </div>
                <div class="footer-contact">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="m22 7-10 7L2 7"></path></svg>
                    <span>contact@smartcare.tn</span>
                </div>
                <div class="footer-contact">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-6-5.33-6-10a6 6 0 0 1 12 0c0 4.67-6 10-6 10z"></path><circle cx="12" cy="11" r="2"></circle></svg>
                    <span>Tunis, Tunisie</span>
                </div>
            </div>
        </div>
        <div class="public-footer-bottom">© 2026 Smart Care. Tous droits reserves.</div>
    </footer>
</div>
<script src="<?= e(asset_url('assets/public.js')) ?>"></script>
</body>
</html>
