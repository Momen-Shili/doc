<?php
$title = 'Patients';
ob_start();
$totalPages = (int)ceil($total / $perPage);
?>
<div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-lg font-semibold text-white">Patients</h1>
            <p class="mt-1 text-sm text-slate-400">Gestion de vos patients.</p>
        </div>
        <a class="inline-flex items-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400" href="?r=patients-create">Ajouter un patient</a>
    </div>
</div>

<div class="mt-6 rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <form method="get" class="flex flex-col gap-3 sm:flex-row sm:items-end">
        <input type="hidden" name="r" value="patients" />
        <div class="sm:flex-1">
            <label for="patients-search" class="block text-sm font-medium text-slate-200">Recherche</label>
            <div class="mt-2">
                <input id="patients-search" type="text" name="q" value="<?= e($q)    ?>" placeholder="Nom, email ou téléphone" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
            </div>
        </div>
        <button class="inline-flex items-center justify-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Rechercher</button>
    </form>
</div>

<div class="mt-6 rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <div class="-mx-3 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="text-xs uppercase text-slate-400">
                <tr>
                    <th class="px-3 py-2 text-left" scope="col">Nom</th>
                    <th class="px-3 py-2 text-left" scope="col">Téléphone</th>
                    <th class="px-3 py-2 text-left" scope="col">Email</th>
                    <th class="px-3 py-2 text-right" scope="col">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php if (!$patients): ?>
                    <tr>
                        <td colspan="4" class="px-3 py-6 text-center text-sm text-slate-400">Aucun patient trouvé.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($patients as $patient): ?>
                        <tr class="odd:bg-slate-800/40">
                            <td class="px-3 py-3 text-slate-100">
                                <a class="text-indigo-300 hover:text-indigo-200" href="?r=patient&id=<?= e($patient['id'])    ?>"><?= e($patient['full_name'])    ?></a>
                            </td>
                            <td class="px-3 py-3 text-slate-300"><?= e($patient['phone'])    ?></td>
                            <td class="px-3 py-3 text-slate-300"><?= e($patient['email'])    ?></td>
                            <td class="px-3 py-3 text-right text-sm font-semibold">
                                <a class="text-indigo-300 hover:text-indigo-200" href="?r=patient&id=<?= e($patient['id'])    ?>">Voir</a>
                                <a class="ml-3 text-slate-300 hover:text-white" href="?r=patients-edit&id=<?= e($patient['id'])    ?>">Modifier</a>
                                <form method="post" action="?r=patients-delete" class="inline" onsubmit="return confirm('Supprimer ce patient ?');">
                                    <?= csrf_field()    ?>
                                    <input type="hidden" name="id" value="<?= e($patient['id'])    ?>" />
                                    <button class="ml-3 text-rose-300 hover:text-rose-200">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($totalPages > 1): ?>
<div class="mt-6 flex items-center justify-center gap-2">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a class="inline-flex items-center rounded-md px-3 py-2 text-sm font-semibold <?= $i === $page ? 'bg-indigo-500 text-white' : 'bg-slate-800 text-slate-200 ring-1 ring-inset ring-white/10 hover:bg-slate-700' ?>" href="?r=patients&q=<?= e($q) ?>&page=<?= $i ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>
</div>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
?>
