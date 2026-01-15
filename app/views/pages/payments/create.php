<?php
$title = 'Ajouter un paiement';
ob_start();
$hasErrors = !empty($errors);
>
<div class="max-w-2xl">
    <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
        <h1 class="text-lg font-semibold text-white">Ajouter un paiement (espèces)</h1>
        <p class="mt-1 text-sm text-slate-400">Enregistrer un paiement reçu.</p>

        <?php if ($hasErrors): ?>
            <div id="payment-create-error" class="mt-4 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200" role="alert">
                <?= e(implode(' ', $errors)) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="r=payments-create" class="mt-6 space-y-5">
            <?= csrf_field() ?>
            <div>
                <label for="payment-patient" class="block text-sm font-medium text-slate-200">Patient <span class="text-rose-400">*</span></label>
                <div class="mt-2">
                    <select id="payment-patient" name="patient_id" required <?= $hasErrors ? 'aria-describedby="payment-create-error"' : '' ?> class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500">
                        <option value="">Sélectionner</option>
                        <?php foreach ($patients as $p): ?>
                            <option value="<?= e($p['id']) ?>" <?= (int)old('patient_id') === (int)$p['id'] ? 'selected' : '' ?>><?= e($p['full_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div>
                <label for="payment-appointment" class="block text-sm font-medium text-slate-200">Rendez-vous (optionnel)</label>
                <div class="mt-2">
                    <select id="payment-appointment" name="appointment_id" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500">
                        <option value="">Aucun</option>
                        <?php foreach ($appointments as $a): ?>
                            <option value="<?= e($a['id']) ?>" <?= (int)old('appointment_id') === (int)$a['id'] ? 'selected' : '' ?>><?= e($a['patient_name']) ?> - <?= e($a['starts_at']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if (!empty($appointmentServices)): ?>
                    <p class="mt-2 text-xs text-slate-400">Total suggéré: <?= e(format_tnd($appointmentSummary['total_price'] ?? 0)) ?></p>
                <?php endif; ?>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="payment-received" class="block text-sm font-medium text-slate-200">Date de paiement <span class="text-rose-400">*</span></label>
                    <div class="mt-2">
                        <input id="payment-received" type="date" name="received_at" value="<?= e(old('received_at', date('Y-m-d'))) ?>" required <?= $hasErrors ? 'aria-describedby="payment-create-error"' : '' ?> class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                    </div>
                </div>
                <div>
                    <label for="payment-amount" class="block text-sm font-medium text-slate-200">Montant (TND) ?? <span class="text-rose-400">*</span></label>
                    <div class="mt-2">
                        <input id="payment-amount" type="number" step="0.001" name="amount" value="<?= e(old('amount')) ?>" required <?= $hasErrors ? 'aria-describedby="payment-create-error"' : '' ?> class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-200">Actes / Services (optionnel)</label>
                <div class="mt-2 space-y-2">
                    <?php if (!$services): ?>
                        <p class="text-sm text-slate-400">Aucun service pour le moment.</p>
                    <?php else: ?>
                        <?php foreach ($services as $service): ?>
                            <div class="flex items-center justify-between gap-4 rounded-md bg-slate-800/70 p-3">
                                <div>
                                    <p class="text-sm font-semibold text-white"><?= e($service['name']) ?></p>
                                    <p class="text-xs text-slate-400">Prix: <?= e(format_tnd($service['default_price'])) ?></p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="hidden" name="service_id[] " value="<?= e($service['id']) ?>" />
                                    <input type="number" min="0" name="service_qty[] " value="0" data-price="<?= e($service['default_price']) ?>" class="w-20 rounded-md border-0 bg-slate-900/60 py-1.5 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <p class="mt-2 text-xs text-slate-400">Quantita= 0 pour exclure.</p>
                <p class="mt-2 text-xs text-slate-400">Total suggéré: <span id="payment-service-total">0,000 TND</span></p>
                <label class="mt-3 flex items-center gap-2 text-sm text-slate-200">
                    <input type="checkbox" name="detail_items" class="rounded border-slate-600 bg-slate-900 text-indigo-500 focus:ring-indigo-500" />
                    Détailler le paiement
                </label>
            </div>
            <div>
                <label for="payment-reference" class="block text-sm font-medium text-slate-200">Référence (optionnel)</label>
                <div class="mt-2">
                    <input id="payment-reference" type="text" name="reference" value="<?= e(old('reference')) ?>" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <div>
                <label for="payment-notes" class="block text-sm font-medium text-slate-200">Notes</label>
                <div class="mt-2">
                    <textarea id="payment-notes" name="notes" rows="3" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500"><?= e(old('notes')) ?></textarea>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button class="inline-flex items-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Enregistrer</button>
                <a class="text-sm font-semibold text-slate-300 hover:text-white" href="r=payments">Annuler</a>
            </div>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
clear_old();
require __DIR__ . '/../../layout.php';
>
<script>
function updatePaymentServicesTotal() {
    var total = 0;
    document.querySelectorAll('input[name="service_qty[] "] ').forEach(function (input) {
        var qty = parseFloat(input.value || '0');
        var price = parseFloat(input.dataset.price || '0');
        if (qty > 0) {
            total += qty * price;
        }
    });
    var formatted = total.toLocaleString('fr-FR', { minimumFractionDigits: 3, maximumFractionDigits: 3 }) ?? + ' TND';
    var el = document.getElementById('payment-service-total');
    if (el) ?? el.textContent = formatted;
}
document.querySelectorAll('input[name="service_qty[] "] ').forEach(function (input) {
    input.addEventListener('input', updatePaymentServicesTotal);
});
updatePaymentServicesTotal();
</script>