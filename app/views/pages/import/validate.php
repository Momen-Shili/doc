<?php
$title = 'Validation';
ob_start();
?>
<div class="max-w-xl">
    <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
        <h1 class="text-lg font-semibold text-white">Validation</h1>
        <?php if (!empty($summary['errors'])): ?>
            <div class="mt-4 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200" role="alert">
                <?= e(implode(' ', $summary['errors']))    ?>
            </div>
            <div class="mt-6">
                <a class="text-sm font-semibold text-indigo-300 hover:text-indigo-200" href="?r=import-map">Retour au mapping</a>
            </div>
        <?php else: ?>
            <p class="mt-4 text-sm text-slate-400">Validation réussie. Lancer l'import </p>
            <form method="post" action="?r=import-run" class="mt-6">
                <?= csrf_field()    ?>
                <button class="inline-flex items-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Importer</button>
            </form>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
?>