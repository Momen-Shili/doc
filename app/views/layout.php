<?php
$user = auth_user();
$title = $title ?? APP_NAME;
$theme = current_theme();
?>
<!doctype html>
<html class="h-full" lang="fr" data-theme="<?= e($theme) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= e(asset_url('UI/assets/theme.css')) ?>">
    <style>
        a:focus-visible,
        button:focus-visible,
        input:focus-visible,
        select:focus-visible,
        textarea:focus-visible,
        [tabindex]:focus-visible {
            outline: 2px solid var(--ring);
            outline-offset: 2px;
        }
    </style>
</head>
<body class="h-full text-slate-100">
<div>
    <?php if ($user): ?>
        <?php require __DIR__ . '/partials/sidebar.php'; ?>
        <div class="lg:pl-72">
            <?php require __DIR__ . '/partials/topbar.php'; ?>
            <main class="py-10">
                <div class="px-4 sm:px-6 lg:px-8">
                    <?php if (is_impersonating() && admin_user()): ?>
                        <div class="mb-6 border-l-4 border-amber-400 bg-amber-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3 text-sm text-amber-800">
                                    <p>
                                        <span class="font-semibold">Mode impersonation :</span>
                                        vous agissez en tant que Dr. <?= e($user['full_name'] ?? '') ?>.
                                        <a class="font-semibold underline hover:text-amber-700" href="admin/?r=impersonate-stop">Quitter</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php require __DIR__ . '/partials/flash.php'; ?>
                    <?= $content ?>
                </div>
            </main>
        </div>
    <?php elseif (isset($isLandingPage) && $isLandingPage): ?>
        <!-- Landing page: full width, no container -->
        <?= $content ?>
    <?php else: ?>
        <!-- Login/Register pages: centered container -->
        <main class="min-h-screen flex items-center justify-center px-4 bg-slate-950">
            <?= $content ?>
        </main>
    <?php endif; ?>
</div>

    
    <script>
    document.addEventListener('submit', function (event) {
        var form = event.target;
        if (!form || form.tagName !== 'FORM') return;
        if ((form.getAttribute('method') || 'get').toLowerCase() !== 'post') return;
        var buttons = form.querySelectorAll('button[type=\"submit\"], button:not([type])');
        buttons.forEach(function (btn) {
            if (btn.disabled) return;
            btn.dataset.originalText = btn.textContent;
            btn.textContent = btn.dataset.loadingText || 'Traitement...';
            btn.disabled = true;
        });
    });
    </script>
</body>
</html>