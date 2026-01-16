<?php
$title = 'Rapports';
ob_start();
$momMode = $mom['mode'] ?? 'previous';
$momBy = $mom['by'] ?? 'total';
?>
<div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-lg font-semibold text-white">Rapports</h1>
            <p class="text-sm text-slate-400">Analyse du chiffre d'affaires en espèces.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a class="inline-flex items-center rounded-md bg-slate-800 px-3 py-2 text-sm font-semibold text-slate-100 ring-1 ring-white/10 hover:bg-slate-700" href="?r=reports-export&from=<?= e($from) ?>&to=<?= e($to) ?>">Exporter paiements CSV</a>
            <a class="inline-flex items-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400" href="?r=reports-services-export&from=<?= e($from) ?>&to=<?= e($to) ?>">Exporter services CSV</a>
        </div>
    </div>
</div>

<div class="mt-6 rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <h2 class="text-base font-semibold text-white">Filtrer la période</h2>
    <form method="get" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3 sm:items-end">
        <input type="hidden" name="r" value="reports" />
        <div>
            <label for="reports-from" class="block text-sm font-medium text-slate-200">Du</label>
            <div class="mt-2">
                <input id="reports-from" type="date" name="from" value="<?= e($from) ?>" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
            </div>
        </div>
        <div>
            <label for="reports-to" class="block text-sm font-medium text-slate-200">Au</label>
            <div class="mt-2">
                <input id="reports-to" type="date" name="to" value="<?= e($to) ?>" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
            </div>
        </div>
        <div>
            <button class="inline-flex items-center justify-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Filtrer</button>
        </div>
    </form>
</div>

<div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div class="rounded-2xl bg-slate-900/60 p-4 ring-1 ring-white/10">
        <dl>
            <dt class="text-xs uppercase tracking-wide text-slate-400">Chiffre d'affaires</dt>
            <dd class="mt-2 text-lg font-semibold text-white"><?= e(format_tnd($total)) ?></dd>
            <dd class="mt-1 text-xs text-slate-400">Période : <?= e($from) ?> à <?= e($to) ?></dd>
        </dl>
    </div>
</div>

<div class="mt-6 rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-base font-semibold text-white">Comparaison MoM</h2>
            <p class="mt-1 text-sm text-slate-400">Comparaison du cash sur deux périodes équivalentes.</p>
        </div>
        <a class="inline-flex items-center rounded-md bg-slate-800 px-3 py-2 text-sm font-semibold text-slate-100 ring-1 ring-white/10 hover:bg-slate-700" href="?r=reports-mom-export&mom_from=<?= e($mom['from'] ?? $from) ?>&mom_to=<?= e($mom['to'] ?? $to) ?>&mom_mode=<?= e($momMode) ?>&mom_by=<?= e($momBy) ?>">Exporter MoM CSV</a>
    </div>

    <?php if (!empty($mom['error'])): ?>
        <div class="mt-4 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200" role="alert">
            <?= e($mom['error']) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($mom['warning'])): ?>
        <div class="mt-4 rounded-xl border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-200">
            <?= e($mom['warning']) ?>
        </div>
    <?php endif; ?>

    <form method="get" class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-12">
        <input type="hidden" name="r" value="reports" />
        <div class="lg:col-span-3">
            <label for="mom-from" class="block text-sm font-medium text-slate-200">Début</label>
            <div class="mt-2">
                <input id="mom-from" type="date" name="mom_from" value="<?= e($mom['from'] ?? $from) ?>" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
            </div>
        </div>
        <div class="lg:col-span-3">
            <label for="mom-to" class="block text-sm font-medium text-slate-200">Fin</label>
            <div class="mt-2">
                <input id="mom-to" type="date" name="mom_to" value="<?= e($mom['to'] ?? $to) ?>" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
            </div>
        </div>
        <fieldset class="lg:col-span-4">
            <legend class="block text-sm font-medium text-slate-200">Mode de comparaison</legend>
            <div class="mt-2 flex flex-col gap-2">
                <label class="flex items-center gap-2 text-sm text-slate-300">
                    <input type="radio" name="mom_mode" value="previous" class="h-4 w-4 text-indigo-500 focus:ring-indigo-500" <?= $momMode === 'previous' ? 'checked' : '' ?> />
                    Période précédente (même durée)
                </label>
                <label class="flex items-center gap-2 text-sm text-slate-300">
                    <input type="radio" name="mom_mode" value="same_days" class="h-4 w-4 text-indigo-500 focus:ring-indigo-500" <?= $momMode === 'same_days' ? 'checked' : '' ?> />
                    Mêmes jours du mois précédent
                </label>
            </div>
        </fieldset>
        <div class="lg:col-span-2">
            <label for="mom-by" class="block text-sm font-medium text-slate-200">Comparaison</label>
            <div class="mt-2">
                <select id="mom-by" name="mom_by" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500">
                    <option value="total" <?= $momBy === 'total' ? 'selected' : '' ?>>Total</option>
                    <option value="service" <?= $momBy === 'service' ? 'selected' : '' ?>>Par service</option>
                </select>
            </div>
        </div>
        <div class="lg:col-span-12">
            <button class="inline-flex items-center justify-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Comparer</button>
        </div>
    </form>

    <?php if (empty($mom['error'])): ?>
        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-2xl bg-slate-900/60 p-4 ring-1 ring-white/10">
                <p class="text-xs uppercase tracking-wide text-slate-400">Total période actuelle</p>
                <p class="mt-2 text-lg font-semibold text-white"><?= e(format_tnd($mom['current']['total'] ?? 0)) ?></p>
                <p class="mt-1 text-xs text-slate-400"><?= e($mom['current']['start'] ?? '') ?> à <?= e($mom['current']['end'] ?? '') ?></p>
            </div>
            <div class="rounded-2xl bg-slate-900/60 p-4 ring-1 ring-white/10">
                <p class="text-xs uppercase tracking-wide text-slate-400">Total comparaison</p>
                <p class="mt-2 text-lg font-semibold text-white"><?= e(format_tnd($mom['prev']['total'] ?? 0)) ?></p>
                <p class="mt-1 text-xs text-slate-400"><?= e($mom['prev']['start'] ?? '') ?> à <?= e($mom['prev']['end'] ?? '') ?></p>
            </div>
            <div class="rounded-2xl bg-slate-900/60 p-4 ring-1 ring-white/10">
                <p class="text-xs uppercase tracking-wide text-slate-400">? valeur</p>
                <p class="mt-2 text-lg font-semibold text-white"><?= e(format_tnd($mom['delta']['value'] ?? 0)) ?></p>
                <p class="mt-1 text-xs text-slate-400">
                    ? % :
                    <?php if (($mom['delta']['percent'] ?? null) === null): ?>
                        —
                    <?php else: ?>
                        <?= e(number_format($mom['delta']['percent'], 1, ',', ' ')) ?> %
                    <?php endif; ?>
                </p>
            </div>
            <div class="rounded-2xl bg-slate-900/60 p-4 ring-1 ring-white/10">
                <p class="text-xs uppercase tracking-wide text-slate-400">Nb paiements</p>
                <p class="mt-2 text-lg font-semibold text-white"><?= e($mom['current']['count'] ?? 0) ?> / <?= e($mom['prev']['count'] ?? 0) ?></p>
                <p class="mt-1 text-xs text-slate-400">Actuel vs comparaison</p>
            </div>
            <div class="rounded-2xl bg-slate-900/60 p-4 ring-1 ring-white/10">
                <p class="text-xs uppercase tracking-wide text-slate-400">Ticket moyen</p>
                <p class="mt-2 text-lg font-semibold text-white"><?= e(format_tnd($mom['current']['avg'] ?? 0)) ?> / <?= e(format_tnd($mom['prev']['avg'] ?? 0)) ?></p>
                <p class="mt-1 text-xs text-slate-400">Actuel vs comparaison</p>
            </div>
        </div>

        <div class="mt-6 -mx-3 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-xs uppercase text-slate-400">
                    <tr>
                        <th class="px-3 py-2 text-left" scope="col">Date</th>
                        <th class="px-3 py-2 text-left" scope="col">Total actuel</th>
                        <th class="px-3 py-2 text-left" scope="col">Total comparaison</th>
                        <th class="px-3 py-2 text-left" scope="col">?</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if (empty($mom['rows'])): ?>
                        <tr>
                            <td colspan="4" class="px-3 py-6 text-center text-sm text-slate-400">Aucune donnée pour la période.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($mom['rows'] as $row): ?>
                            <tr class="odd:bg-slate-800/40">
                                <td class="px-3 py-2 text-slate-100"><?= e($row['date_current']) ?></td>
                                <td class="px-3 py-2 text-slate-300"><?= e(format_tnd($row['total_current'])) ?></td>
                                <td class="px-3 py-2 text-slate-300">
                                    <?php if (!empty($row['date_prev'])): ?>
                                        <?= e(format_tnd($row['total_prev'])) ?>
                                        <span class="ml-2 text-xs text-slate-500">(<?= e($row['date_prev']) ?>)</span>
                                    <?php else: ?>
                                        <span class="text-slate-500">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 py-2 text-slate-300"><?= e(format_tnd($row['diff'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($momBy === 'service'): ?>
            <div class="mt-6">
                <h3 class="text-base font-semibold text-white">Comparaison par service</h3>
                <?php if (!empty($mom['services_notice'])): ?>
                    <p class="mt-3 text-sm text-slate-400"><?= e($mom['services_notice']) ?></p>
                <?php else: ?>
                    <div class="mt-4 -mx-3 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-xs uppercase text-slate-400">
                                <tr>
                                    <th class="px-3 py-2 text-left" scope="col">Service</th>
                                    <th class="px-3 py-2 text-left" scope="col">Total actuel</th>
                                    <th class="px-3 py-2 text-left" scope="col">Total comparaison</th>
                                    <th class="px-3 py-2 text-left" scope="col">?</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <?php foreach ($mom['services'] as $service): ?>
                                    <tr class="odd:bg-slate-800/40">
                                        <td class="px-3 py-2 text-slate-100"><?= e($service['name']) ?></td>
                                        <td class="px-3 py-2 text-slate-300"><?= e(format_tnd($service['total_current'])) ?></td>
                                        <td class="px-3 py-2 text-slate-300"><?= e(format_tnd($service['total_prev'])) ?></td>
                                        <td class="px-3 py-2 text-slate-300"><?= e(format_tnd($service['diff'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<div class="mt-6 rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <h2 class="text-base font-semibold text-white">Chiffre d'affaires par patient</h2>
    <div class="mt-4">
        <?php if (!$byPatient): ?>
            <p class="text-sm text-slate-400">Aucun paiement sur la période.</p>
        <?php else: ?>
            <div class="-mx-3 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-xs uppercase text-slate-400">
                        <tr>
                            <th class="px-3 py-2 text-left" scope="col">Patient</th>
                            <th class="px-3 py-2 text-left" scope="col">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php foreach ($byPatient as $row): ?>
                            <tr class="odd:bg-slate-800/40">
                                <td class="px-3 py-2 text-slate-100"><?= e($row['full_name']) ?></td>
                                <td class="px-3 py-2 text-slate-300"><?= e(format_tnd($row['total'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="mt-6 rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <h2 class="text-base font-semibold text-white">Recettes par service</h2>
    <div class="mt-4">
        <?php if (empty($byService)): ?>
            <p class="text-sm text-slate-400">Aucune recette sur la période.</p>
        <?php else: ?>
            <div class="-mx-3 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-xs uppercase text-slate-400">
                        <tr>
                            <th class="px-3 py-2 text-left" scope="col">Service</th>
                            <th class="px-3 py-2 text-left" scope="col">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php foreach ($byService as $row): ?>
                            <tr class="odd:bg-slate-800/40">
                                <td class="px-3 py-2 text-slate-100"><?= e($row['name']) ?></td>
                                <td class="px-3 py-2 text-slate-300"><?= e(format_tnd($row['total'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
?>