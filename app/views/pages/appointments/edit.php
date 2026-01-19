<?php
$title = 'Modifier un rendez-vous';
ob_start();
$statuses = [
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
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-lg font-semibold text-white">Modifier un rendez-vous</h1>
                <p class="mt-1 text-sm text-slate-400">Mettez à jour la consultation.</p>
            </div>
            <a class="inline-flex items-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400" href="?r=reminders-send&appointment_id=<?= e($appointment['id']) ?>">Envoyer un rappel</a>
        </div>

        <?php if ($hasErrors): ?>
            <div id="appointment-edit-error" class="mt-4 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200" role="alert">
                <?= e(implode(' ', $errors)) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="?r=appointments-edit&id=<?= e($appointment['id']) ?>" class="mt-6 space-y-5">
            <?= csrf_field() ?>
            <div>
                <label for="appointment-edit-patient" class="block text-sm font-medium text-slate-200">Patient <span class="text-rose-400">*</span></label>
                <div class="mt-2">
                    <select id="appointment-edit-patient" name="patient_id" required <?= $hasErrors ? 'aria-describedby="appointment-edit-error"' : '' ?> class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500">
                        <?php foreach ($patients as $p): ?>
                            <option value="<?= e($p['id']) ?>" <?= (int)$appointment['patient_id'] === (int)$p['id'] ? 'selected' : '' ?>><?= e($p['full_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="appointment-edit-starts" class="block text-sm font-medium text-slate-200">Début <span class="text-rose-400">*</span></label>
                    <div class="mt-2">
                        <input id="appointment-edit-starts" type="datetime-local" name="starts_at" value="<?= e($appointment['starts_at']) ?>" required <?= $hasErrors ? 'aria-describedby="appointment-edit-error"' : '' ?> class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                    </div>
                </div>
                <div>
                    <label for="appointment-edit-ends" class="block text-sm font-medium text-slate-200">Fin</label>
                    <div class="mt-2">
                        <input id="appointment-edit-ends" type="datetime-local" name="ends_at" value="<?= e($appointment['ends_at']) ?>" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
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
                            <?php $qty = $serviceSelections[$service['id']] ?? 0; ?>
                            <div class="flex items-center justify-between gap-4 rounded-md bg-slate-800/70 p-3">
                                <div>
                                    <p class="text-sm font-semibold text-white"><?= e($service['name']) ?></p>
                                    <p class="text-xs text-slate-400">Prix: <?= e(format_tnd($service['default_price'])) ?> - Durée: <?= e($service['default_duration_minutes']) ?> min</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="hidden" name="service_id[]" value="<?= e($service['id']) ?>" />
                                    <input type="number" min="0" name="service_qty[]" value="<?= e($qty) ?>" data-price="<?= e($service['default_price']) ?>" data-duration="<?= e($service['default_duration_minutes']) ?>" class="w-20 rounded-md border-0 bg-slate-900/60 py-1.5 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <p class="mt-2 text-xs text-slate-400">Quantité 0 pour exclure.</p>
                <p class="mt-2 text-xs text-slate-400">Total suggéré: <span id="service-total">0,000 TND</span> - Durée suggérée: <span id="service-duration">0</span> min</p>
            </div>
            <div>
                <label for="appointment-edit-status" class="block text-sm font-medium text-slate-200">Statut</label>
                <div class="mt-2">
                    <select id="appointment-edit-status" name="status" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500">
                        <?php foreach ($statuses as $value => $label): ?>
                            <option value="<?= e($value) ?>" <?= $appointment['status'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div>
                <label for="appointment-edit-reason" class="block text-sm font-medium text-slate-200">Motif</label>
                <div class="mt-2">
                    <input id="appointment-edit-reason" type="text" name="reason" value="<?= e($appointment['reason']) ?>" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <div>
                <label for="appointment-edit-notes" class="block text-sm font-medium text-slate-200">Notes</label>
                <div class="mt-2">
                    <textarea id="appointment-edit-notes" name="notes" rows="3" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500"><?= e($appointment['notes']) ?></textarea>
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
var editForm = document.querySelector('form[action*="appointments-edit"]');
if (editForm) {
    editForm.addEventListener('submit', function () {
        localStorage.setItem('calendarRefresh', '1');
    });
}
</script>
