<?php
$title = 'Médecins';
ob_start();
?>
<div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <h1 class="text-lg font-semibold text-white">Médecins</h1>
    <p class="mt-1 text-sm text-slate-400">Gestion des comptes médecins.</p>
</div>

<div class="mt-6 rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <div class="-mx-3 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="text-xs uppercase text-slate-400">
                <tr>
                    <th class="px-3 py-2 text-left" scope="col">Nom</th>
                    <th class="px-3 py-2 text-left" scope="col">Email</th>
                    <th class="px-3 py-2 text-left" scope="col">Plan</th>
                    <th class="px-3 py-2 text-left" scope="col">Statut</th>
                    <th class="px-3 py-2 text-right" scope="col">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php if (!$doctors): ?>
                    <tr>
                        <td colspan="5" class="px-3 py-6 text-center text-sm text-slate-400">Aucun médecin.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($doctors as $doc): ?>
                        <tr class="odd:bg-slate-800/40">
                            <td class="px-3 py-3 text-slate-100"><?= e($doc['full_name'])    ?></td>
                            <td class="px-3 py-3 text-slate-300"><?= e($doc['email'])    ?></td>
                            <td class="px-3 py-3 text-slate-300"><?= e($doc['plan'])    ?></td>
                            <td class="px-3 py-3">
                                <?php if (!empty($doc['suspended_at'])): ?>
                                    <span class="inline-flex items-center rounded-full bg-rose-500/20 px-2 py-1 text-xs font-semibold text-rose-200">Suspendu</span>
                                <?php elseif ((int)$doc['is_active'] === 1): ?>
                                    <span class="inline-flex items-center rounded-full bg-emerald-500/20 px-2 py-1 text-xs font-semibold text-emerald-200">Actif</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center rounded-full bg-slate-700 px-2 py-1 text-xs font-semibold text-slate-200">Inactif</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 py-3 text-right text-sm font-semibold">
                                <?php if (!empty($doc['suspended_at'])): ?>
                                    <form method="post" action="?r=doctor-toggle" class="inline">
                                        <?= csrf_field()    ?>
                                        <input type="hidden" name="id" value="<?= e($doc['id'])    ?>" />
                                        <input type="hidden" name="action" value="activate" />
                                        <button class="text-emerald-300 hover:text-emerald-200">Activer</button>
                                    </form>
                                <?php else: ?>
                                    <form method="post" action="?r=doctor-toggle" class="inline" onsubmit="return confirm('Suspendre ce médecin ?');">
                                        <?= csrf_field()    ?>
                                        <input type="hidden" name="id" value="<?= e($doc['id'])    ?>" />
                                        <input type="hidden" name="action" value="suspend" />
                                        <button class="text-rose-300 hover:text-rose-200">Suspendre</button>
                                    </form>
                                <?php endif; ?>
                                <form method="post" action="?r=impersonate" class="inline">
                                    <?= csrf_field()    ?>
                                    <input type="hidden" name="id" value="<?= e($doc['id'])    ?>" />
                                    <button class="ml-3 text-indigo-300 hover:text-indigo-200">Impersonner</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>