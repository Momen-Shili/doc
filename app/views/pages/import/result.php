<?php
$title = 'Résultat import';
ob_start();
?>
<div class="max-w-xl">
    <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
        <h1 class="text-lg font-semibold text-white">Résultat import</h1>
        <p class="mt-2 text-sm text-slate-400"><?= e($result['message'])    ?></p>
        <dl class="mt-6 divide-y divide-white/10 border-t border-white/10 text-sm">
            <div class="py-3 flex items-center justify-between">
                <dt class="text-slate-400">Importées</dt>
                <dd class="font-semibold text-white"><?= e($result['imported'])    ?></dd>
            </div>
            <div class="py-3 flex items-center justify-between">
                <dt class="text-slate-400">Ignorées</dt>
                <dd class="font-semibold text-white"><?= e($result['ignored'])    ?></dd>
            </div>
            <div class="py-3 flex items-center justify-between">
                <dt class="text-slate-400">Erreurs</dt>
                <dd class="font-semibold text-white"><?= e($result['errors'])    ?></dd>
            </div>
        </dl>
        <div class="mt-6">
            <a class="text-sm font-semibold text-indigo-300 hover:text-indigo-200" href="?r=import">Nouvel import</a>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
?>