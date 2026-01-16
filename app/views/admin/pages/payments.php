<?php
$title = 'Revenus';
ob_start();
?>
<div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-lg font-semibold text-white">Revenus (espèces)</h1>
            <p class="mt-1 text-sm text-slate-400">Vue globale des paiements.</p>
        </div>
        <a class="inline-flex items-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400" href="?r=payments-export&from=<?= e($from)    ?>&to=<?= e($to)    ?>&doctor_id=<?= e($doctorId)    ?>">Exporter CSV</a>
    </div>
</div>

<div class="mt-6 rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <h2 class="text-base font-semibold text-white">Filtrer la période</h2>
    <form method="get" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-4 sm:items-end">
        <input type="hidden" name="r" value="payments" />
        <div>
            <label for="admin-payments-from" class="block text-sm font-medium text-slate-200">Du</label>
            <div class="mt-2">
                <input id="admin-payments-from" type="date" name="from" value="<?= e($from)    ?>" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
            </div>
        </div>
        <div>
            <label for="admin-payments-to" class="block text-sm font-medium text-slate-200">Au</label>
            <div class="mt-2">
                <input id="admin-payments-to" type="date" name="to" value="<?= e($to)    ?>" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
            </div>
        </div>
        <div>
            <label for="admin-payments-doctor" class="block text-sm font-medium text-slate-200">Médecin</label>
            <div class="mt-2">
                <select id="admin-payments-doctor" name="doctor_id" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tous médecins</option>
                    <?php foreach ($doctors as $doc): ?>
                        <option value="<?= e($doc['id'])    ?>" <?= (int)$doctorId === (int)$doc['id'] ? 'selected' : ''    ?>><?= e($doc['full_name'])    ?></option>
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
                    <th class="px-3 py-2 text-left" scope="col">Date</th>
                    <th class="px-3 py-2 text-left" scope="col">Médecin</th>
                    <th class="px-3 py-2 text-left" scope="col">Patient</th>
                    <th class="px-3 py-2 text-left" scope="col">Montant</th>
                    <th class="px-3 py-2 text-left" scope="col">Référence</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php if (!$payments): ?>
                    <tr>
                        <td colspan="5" class="px-3 py-6 text-center text-sm text-slate-400">Aucun paiement.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($payments as $payment): ?>
                        <tr class="odd:bg-slate-800/40">
                            <td class="px-3 py-3 text-slate-100"><?= e($payment['received_at'])    ?></td>
                            <td class="px-3 py-3 text-slate-300"><?= e($payment['doctor_name'])    ?></td>
                            <td class="px-3 py-3 text-slate-300"><?= e($payment['patient_name'])    ?></td>
                            <td class="px-3 py-3 text-slate-300"><?= e(format_tnd($payment['amount']))    ?></td>
                            <td class="px-3 py-3 text-slate-300"><?= e($payment['reference'])    ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="px-3 py-3 text-sm font-semibold text-slate-100">Total période</td>
                    <td class="px-3 py-3 text-sm font-semibold text-slate-100"><?= e(format_tnd($total))    ?></td>
                    <td class="px-3 py-3"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>