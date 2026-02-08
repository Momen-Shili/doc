<?php
$title = 'Ajouter un rendez-vous';
ob_start();
$statuses = [
    'PENDING' => 'EN_ATTENTE',
    'PLANIFIE' => 'PLANIFIE',
    'CONFIRME' => 'CONFIRME',
    'TERMINE' => 'TERMINE',
    'ANNULE' => 'ANNULE',
    'NO_SHOW' => 'ABSENT',
];
$hasErrors = !empty($errors);
?>
<div class="max-w-2xl">
    <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
        <h1 class="text-lg font-semibold text-white">Ajouter un rendez-vous</h1>
        <p class="mt-1 text-sm text-slate-400">Planifiez une consultation.</p>

        <?php if ($hasErrors): ?>
            <div id="appointment-create-error" class="mt-4 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200" role="alert">
                <?= e(implode(' ', $errors)) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="?r=appointments-create" class="mt-6 space-y-5">
            <?= csrf_field() ?>
            <div>
                <label for="appointment-patient" class="block text-sm font-medium text-slate-200">Patient <span class="text-rose-400">*</span></label>
                <div class="mt-2">
                    <select id="appointment-patient" name="patient_id" required <?= $hasErrors ? 'aria-describedby="appointment-create-error"' : '' ?> class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500">
                        <option value="">Sélectionner</option>
                        <?php foreach ($patients as $p): ?>
                            <option value="<?= e($p['id']) ?>" <?= (int)old('patient_id') === (int)$p['id'] ? 'selected' : '' ?>><?= e($p['full_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="appointment-starts" class="block text-sm font-medium text-slate-200">Début <span class="text-rose-400">*</span></label>
                    <div class="mt-2">
                        <input id="appointment-starts" type="datetime-local" name="starts_at" value="<?= e(old('starts_at', $prefillStartsAt ?? '')) ?>" required <?= $hasErrors ? 'aria-describedby="appointment-create-error"' : '' ?> class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                    </div>
                </div>
                <div>
                    <label for="appointment-ends" class="block text-sm font-medium text-slate-200">Fin</label>
                    <div class="mt-2">
                        <input id="appointment-ends" type="datetime-local" name="ends_at" value="<?= e(old('ends_at')) ?>" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                    </div>
                    <p class="mt-1 text-xs text-slate-400">Auto-calculée selon les services si vide.</p>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-200">Actes / Services</label>
                <div class="mt-2 space-y-2">
                    <?php if (!$services): ?>
                        <p class="text-sm text-slate-400">Aucun service pour le moment.</p>
                    <?php else: ?>
                        <?php foreach ($services as $service): ?>
                            <div class="flex items-center justify-between gap-4 rounded-md bg-slate-800/70 p-3">
                                <div>
                                    <p class="text-sm font-semibold text-white"><?= e($service['name']) ?></p>
                                    <p class="text-xs text-slate-400">Prix: <?= e(format_tnd($service['default_price'])) ?> - Durée: <?= e($service['default_duration_minutes']) ?> min</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="hidden" name="service_id[]" value="<?= e($service['id']) ?>" />
                                    <input type="number" min="0" name="service_qty[]" value="0" data-price="<?= e($service['default_price']) ?>" data-duration="<?= e($service['default_duration_minutes']) ?>" class="w-20 rounded-md border-0 bg-slate-900/60 py-1.5 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <p class="mt-2 text-xs text-slate-400">Quantité 0 pour exclure.</p>
                <p class="mt-2 text-xs text-slate-400">Total suggéré: <span id="service-total">0,000 TND</span> - Durée suggérée: <span id="service-duration">0</span> min</p>
            </div>
            <div>
                <label for="appointment-status" class="block text-sm font-medium text-slate-200">Statut</label>
                <div class="mt-2">
                    <select id="appointment-status" name="status" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500">
                        <?php foreach ($statuses as $value => $label): ?>
                            <option value="<?= e($value) ?>" <?= old('status','PLANIFIE') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div>
                <label for="appointment-reason" class="block text-sm font-medium text-slate-200">Motif</label>
                <div class="mt-2">
                    <input id="appointment-reason" type="text" name="reason" value="<?= e(old('reason')) ?>" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <div>
                <label for="appointment-notes" class="block text-sm font-medium text-slate-200">Notes</label>
                <div class="mt-2">
                    <textarea id="appointment-notes" name="notes" rows="3" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500"><?= e(old('notes')) ?></textarea>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button class="inline-flex items-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Enregistrer</button>
                <a class="text-sm font-semibold text-slate-300 hover:text-white" href="?r=appointments">Annuler</a>
            </div>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
clear_old();
require __DIR__ . '/../../layout.php';
?>
<script>
document.querySelectorAll('input[name="service_qty[]"]').forEach(function (input) {
    input.addEventListener('input', updateServiceSummary);
});
function updateServiceSummary() {
    var total = 0;
    var duration = 0;
    document.querySelectorAll('input[name="service_qty[]"]').forEach(function (input) {
        var qty = parseFloat(input.value || '0');
        var price = parseFloat(input.dataset.price || '0');
        var dur = parseInt(input.dataset.duration || '0', 10);
        if (qty > 0) {
            total += qty * price;
            duration += qty * dur;
        }
    });
    var formatted = total.toLocaleString('fr-FR', { minimumFractionDigits: 3, maximumFractionDigits: 3 }) + ' TND';
    var totalEl = document.getElementById('service-total');
    var durationEl = document.getElementById('service-duration');
    if (totalEl) totalEl.textContent = formatted;
    if (durationEl) durationEl.textContent = duration.toString();
}
updateServiceSummary();
</script>
<script>
var createForm = document.querySelector('form[action*="appointments-create"]');
if (createForm) {
    createForm.addEventListener('submit', function () {
        localStorage.setItem('calendarRefresh', '1');
    });
}
</script>
