<?php
$title = 'Journal des rappels';
ob_start();
$statuses = ['DRAFT' => 'Brouillon', 'SENT' => 'Envoyé', 'FAILED' => 'Échec'];
$channels = ['SMS', 'WHATSAPP', 'EMAIL'];
?>
<div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-lg font-semibold text-white">Journal des rappels</h1>
            <p class="mt-1 text-sm text-slate-400">Historique des rappels enregistrés.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a class="inline-flex items-center rounded-md bg-slate-800 px-3 py-2 text-sm font-semibold text-slate-100 ring-1 ring-white/10 hover:bg-slate-700" href="?r=reminders-templates">Modèles</a>
            <a class="inline-flex items-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400" href="?r=reminders-export&from=<?= e($from) ?>&to=<?= e($to) ?>&status=<?= e($status) ?>&patient_id=<?= e($patientId) ?>&channel=<?= e($channel) ?>">Exporter CSV</a>
        </div>
    </div>
</div>

<div class="mt-6 rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <form method="get" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-6">
        <input type="hidden" name="r" value="reminders-log" />
        <div>
            <label for="reminders-from" class="block text-sm font-medium text-slate-200">Du</label>
            <div class="mt-2">
                <input id="reminders-from" type="date" name="from" value="<?= e($from) ?>" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
            </div>
        </div>
        <div>
            <label for="reminders-to" class="block text-sm font-medium text-slate-200">Au</label>
            <div class="mt-2">
                <input id="reminders-to" type="date" name="to" value="<?= e($to) ?>" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
            </div>
        </div>
        <div>
            <label for="reminders-status" class="block text-sm font-medium text-slate-200">Statut</label>
            <div class="mt-2">
                <select id="reminders-status" name="status" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tous</option>
                    <?php foreach ($statuses as $key => $label): ?>
                        <option value="<?= e($key) ?>" <?= $status === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div>
            <label for="reminders-patient" class="block text-sm font-medium text-slate-200">Patient</label>
            <div class="mt-2">
                <select id="reminders-patient" name="patient_id" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tous</option>
                    <?php foreach ($patients as $patient): ?>
                        <option value="<?= e($patient['id']) ?>" <?= (int)$patientId === (int)$patient['id'] ? 'selected' : '' ?>><?= e($patient['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div>
            <label for="reminders-channel" class="block text-sm font-medium text-slate-200">Canal</label>
            <div class="mt-2">
                <select id="reminders-channel" name="channel" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tous</option>
                    <?php foreach ($channels as $ch): ?>
                        <option value="<?= e($ch) ?>" <?= $channel === $ch ? 'selected' : '' ?>><?= e($ch) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="flex items-end">
            <button class="inline-flex items-center justify-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Filtrer</button>
        </div>
    </form>
</div>

<?php if (!empty($detail)): ?>
    <div class="mt-6 rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
        <h2 class="text-base font-semibold text-white">Détail du rappel</h2>
        <dl class="mt-4 space-y-2 text-sm">
            <div class="flex items-center justify-between">
                <dt class="text-slate-400">Date</dt>
                <dd class="text-slate-100"><?= e($detail['sent_at']) ?></dd>
            </div>
            <div class="flex items-center justify-between">
                <dt class="text-slate-400">Patient</dt>
                <dd class="text-slate-100"><?= e($detail['patient_name']) ?></dd>
            </div>
            <div class="flex items-center justify-between">
                <dt class="text-slate-400">RDV</dt>
                <dd class="text-slate-100"><?= e($detail['appointment_start'] ?? '') ?></dd>
            </div>
            <div class="flex items-center justify-between">
                <dt class="text-slate-400">Canal</dt>
                <dd class="text-slate-100"><?= e($detail['channel']) ?></dd>
            </div>
            <div class="flex items-center justify-between">
                <dt class="text-slate-400">Statut</dt>
                <dd class="text-slate-100"><?= e($statuses[$detail['status']] ?? $detail['status']) ?></dd>
            </div>
        </dl>
        <div class="mt-4 rounded-xl bg-slate-800/70 p-4 text-sm text-slate-100">
            <?= nl2br(e($detail['message'])) ?>
        </div>
    </div>
<?php endif; ?>

<div class="mt-6 rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <div class="-mx-3 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="text-xs uppercase text-slate-400">
                <tr>
                    <th class="px-3 py-2 text-left" scope="col">Date/heure</th>
                    <th class="px-3 py-2 text-left" scope="col">Patient</th>
                    <th class="px-3 py-2 text-left" scope="col">RDV</th>
                    <th class="px-3 py-2 text-left" scope="col">Canal</th>
                    <th class="px-3 py-2 text-left" scope="col">Statut</th>
                    <th class="px-3 py-2 text-right" scope="col">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="6" class="px-3 py-6 text-center text-sm text-slate-400">Aucun rappel pour la période.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr class="odd:bg-slate-800/40">
                            <td class="px-3 py-3 text-slate-100"><?= e($log['sent_at']) ?></td>
                            <td class="px-3 py-3 text-slate-300"><?= e($log['patient_name']) ?></td>
                            <td class="px-3 py-3 text-slate-300"><?= e($log['appointment_start'] ?? '') ?></td>
                            <td class="px-3 py-3 text-slate-300"><?= e($log['channel']) ?></td>
                            <td class="px-3 py-3 text-slate-300"><?= e($statuses[$log['status']] ?? $log['status']) ?></td>
                            <td class="px-3 py-3 text-right text-sm font-semibold">
                                <a class="text-indigo-300 hover:text-indigo-200" href="?r=reminders-log&view=<?= e($log['id']) ?>&from=<?= e($from) ?>&to=<?= e($to) ?>&status=<?= e($status) ?>&patient_id=<?= e($patientId) ?>&channel=<?= e($channel) ?>">Voir</a>
                                <form method="post" action="?r=reminders-log-recreate" class="inline" onsubmit="return confirm('Recréer ce rappel en brouillon ?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= e($log['id']) ?>" />
                                    <button class="ml-3 text-slate-300 hover:text-white">Recréer</button>
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
require __DIR__ . '/../../layout.php';
?>
