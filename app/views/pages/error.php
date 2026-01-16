<?php
$title = 'Erreur';
ob_start();
?>
<div class="max-w-xl">
    <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
        <h1 class="text-lg font-semibold text-white">Erreur</h1>
        <p class="mt-2 text-sm text-slate-400"><?= e($message ?? 'Une erreur est survenue.') ?></p>
        <div class="mt-6">
            <a class="text-sm font-semibold text-indigo-300 hover:text-indigo-200" href="?r=dashboard">Retour au tableau de bord</a>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
