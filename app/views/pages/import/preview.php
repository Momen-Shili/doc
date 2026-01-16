<?php
$title = 'Prévisualisation';
ob_start();
?>
<div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <h1 class="text-lg font-semibold text-white">Prévisualisation (20 lignes)</h1>
    <p class="mt-1 text-sm text-slate-400">Vérifiez les données avant import.</p>

    <?php if (!$rows): ?>
        <p class="mt-4 text-sm text-slate-400">Aucune donnée.</p>
    <?php else: ?>
        <div class="mt-6 -mx-3 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-xs uppercase text-slate-400">
                    <tr>
                        <?php foreach (array_keys($rows[0])  as $h): ?>
                            <th class="px-3 py-2 text-left" scope="col"><?= e($h)    ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php foreach ($rows as $row): ?>
                        <tr class="odd:bg-slate-800/40">
                            <?php foreach ($row as $val): ?>
                                <td class="px-3 py-2 text-slate-300"><?= e($val)    ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="mt-6 flex flex-wrap gap-3">
        <a class="inline-flex items-center rounded-md bg-slate-800 px-3 py-2 text-sm font-semibold text-slate-100 ring-1 ring-white/10 hover:bg-slate-700" href="?r=import-map">Modifier le mapping</a>
        <form method="post" action="?r=import-validate">
            <?= csrf_field()    ?>
            <button class="inline-flex items-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Valider</button>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
?>