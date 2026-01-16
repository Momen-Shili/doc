<?php
$admin = admin_user();
$theme = current_theme();
$toggleTheme = $theme === 'dark' ? 'light' : 'dark';
$toggleLabel = $theme === 'dark' ? 'Mode clair' : 'Mode sombre';
$redirect = $_SERVER['REQUEST_URI'] ?? '?r=dashboard';
?>
<div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-slate-800 bg-slate-950/80 px-4 shadow-sm backdrop-blur sm:gap-x-6 sm:px-6 lg:px-8 print-hidden">
    <button type="button" class="-m-2.5 p-2.5 text-slate-200 lg:hidden">
        <span class="sr-only">Ouvrir le menu</span>
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>

    <div class="h-6 w-px bg-slate-800 lg:hidden" aria-hidden="true"></div>

    <div class="flex flex-1 items-center justify-between">
        <div class="text-sm font-semibold text-white">Administration</div>
        <div class="flex items-center gap-x-3">
            <form method="post" action="?r=theme-toggle">
                <?= csrf_field() ?>
                <input type="hidden" name="theme" value="<?= e($toggleTheme) ?>">
                <input type="hidden" name="redirect" value="<?= e($redirect) ?>">
                <button type="submit" class="inline-flex items-center rounded-md bg-slate-800 px-3 py-2 text-sm font-semibold text-slate-100 ring-1 ring-white/10 hover:bg-slate-700" aria-label="<?= e($toggleLabel) ?>">
                    <?= e($toggleLabel) ?>
                </button>
            </form>
            <div class="text-sm text-slate-400"><?= e($admin['name'] ?? '') ?></div>
        </div>
    </div>
</div>
