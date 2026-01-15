<?php
$title = 'Rendez-vous';
ob_start();
$statuses = ['PLANIFIE','CONFIRME','TERMINE','ANNULE','NO_SHOW'];
>
<div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-lg font-semibold text-white">Rendez-vous</h1>
            <p class="mt-1 text-sm text-slate-400">Planifiez et suivez vos consultations.</p>
        </div>
        <a class="inline-flex items-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400" href="r=appointments-create">Ajouter un rendez-vous</a>
    </div>
</div>

<div class="mt-6 rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <h2 class="text-base font-semibold text-white">Filtres</h2>
    <form method="get" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3 sm:items-end">
        <input type="hidden" name="r" value="appointments" />
        <div>
            <label for="appointments-date" class="block text-sm font-medium text-slate-200">Date</label>
            <div class="mt-2">
                <input id="appointments-date" type="date" name="date" value="<?= e($date) ?>" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
            </div>
        </div>
        <div>
            <label for="appointments-status" class="block text-sm font-medium text-slate-200">Statut</label>
            <div class="mt-2">
                <select id="appointments-status" name="status" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tous statuts</option>
                    <?php foreach ($statuses as $s): ?>
                        <option value="<?= e($s) ?>" <?= $status === $s ? 'selected' : '' ?>><?= e(appointment_status_label($s)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div>
            <button class="inline-flex items-center justify-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Filtrer</button>
        </div>
    </form>
</div>

<div class="mt-6 rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <div class="-mx-3 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="text-xs uppercase text-slate-400">
                <tr>
                    <th class="px-3 py-2 text-left" scope="col">Patient</th>
                    <th class="px-3 py-2 text-left" scope="col">Début</th>
                    <th class="px-3 py-2 text-left" scope="col">Fin</th>
                    <th class="px-3 py-2 text-left" scope="col">Statut</th>
                    <th class="px-3 py-2 text-right" scope="col">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php if (!$appointments): ?>
                    <tr>
                        <td colspan="5" class="px-3 py-6 text-center text-sm text-slate-400">Aucun rendez-vous.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($appointments as $appt): ?>
                        <tr class="odd:bg-slate-800/40">
                            <td class="px-3 py-3 text-slate-100"><?= e($appt['patient_name']) ?></td>
                            <td class="px-3 py-3 text-slate-300"><?= e($appt['starts_at']) ?></td>
                            <td class="px-3 py-3 text-slate-300"><?= e($appt['ends_at']) ?></td>
                            <td class="px-3 py-3 text-slate-300"><?= e(appointment_status_label($appt['status'])) ?></td>
                            <td class="px-3 py-3 text-right text-sm font-semibold">
                                <a class="text-indigo-300 hover:text-indigo-200" href="r=appointments-edit&id=<?= e($appt['id']) ?>">Modifier</a>
                                <?php if ($appt['status'] ?? !== 'ANNULE'): ?>
                                    <form method="post" action="r=appointments-cancel" class="inline" onsubmit="return confirm('Annuler ce rendez-vous') ">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= e($appt['id']) ?>" />
                                        <button class="ml-3 text-rose-300 hover:text-rose-200">Annuler</button>
                                    </form>
                                <?php endif; ?>
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
require __DIR__ . '/../../layout.php';
>