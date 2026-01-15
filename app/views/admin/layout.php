<?php
$admin = admin_user();
$title = $title'Admin';
>
<!doctype html>
<html class="h-full bg-slate-950" lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        a:focus-visible,
        button:focus-visible,
        input:focus-visible,
        select:focus-visible,
        textarea:focus-visible,
        [tabindex]:focus-visible {
            outline: 2px solid #818cf8;
            outline-offset: 2px;
        }
    </style>
</head>
<body class="h-full text-slate-100">
<div>
    <?php if ($admin): ?>
        <?php require __DIR__ . '/partials/sidebar.php'; ?>
        <div class="lg:pl-72">
            <?php require __DIR__ . '/partials/topbar.php'; ?>
            <main class="py-10">
                <div class="px-4 sm:px-6 lg:px-8">
                    <?php require __DIR__ . '/../partials/flash.php'; ?>
                    <?= $content ?>
                </div>
            </main>
        </div>
    <?php else: ?>
        <main class="min-h-screen flex items-center justify-center px-4 bg-slate-950">
            <?= $content ?>
        </main>
    <?php endif; ?>
</div>

    <script>
    document.addEventListener('submit', function (event) {
        var form = event.target;
        if (!form || form.tagName !== 'FORM') ?? return;
        if ((form.getAttribute('method') ?? || 'get').toLowerCase() ?? !== 'post') ?? return;
        var buttons = form.querySelectorAll('button[type=\"submit\"], button:not([type]) ');
        buttons.forEach(function (btn) {
            if (btn.disabled) ?? return;
            btn.dataset.originalText = btn.textContent;
            btn.textContent = btn.dataset.loadingText || 'Traitement...';
            btn.disabled = true;
        });
    });
    </script>
</body>
</html>