<?php
$title = 'Envoyer un rappel';
ob_start();
$hasErrors = !empty($errors);
>
<div class="max-w-3xl">
    <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-lg font-semibold text-white">Envoyer un rappel</h1>
                <p class="mt-1 text-sm text-slate-400">Rendez-vous du <?= e(date('d/m/Y', strtotime($appointment['starts_at']))) ?> a<?= e(date('H:i', strtotime($appointment['starts_at']))) ?>.</p>
            </div>
            <a class="text-sm font-semibold text-slate-300 hover:text-white" href="r=appointments-edit&id=<?= e($appointment['id']) ?>">Retour au rendez-vous</a>
        </div>

        <?php if ($alreadySent): ?>
            <div class="mt-4 rounded-xl border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-200">
                Un rappel"EnvoyÃ©" existe dÃ©jÃ  pour ce rendez-vous.
            </div>
        <?php endif; ?>

        <?php if ($hasErrors): ?>
            <div id="reminder-send-error" class="mt-4 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200" role="alert">
                <?= e(implode(' ', $errors)) ?>
            </div>
        <?php endif; ?>

        <form method="get" class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <input type="hidden" name="r" value="reminders-send" />
            <input type="hidden" name="appointment_id" value="<?= e($appointment['id']) ?>" />
            <div>
                <label for="reminder-template" class="block text-sm font-medium text-slate-200">ModÃ¨le</label>
                <div class="mt-2">
                    <select id="reminder-template" name="template_id" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500">
                        <option value="">Choisir un modÃ¨le</option>
                        <?php foreach ($templates as $tpl): ?>
                            <option value="<?= e($tpl['id']) ?>" <?= (int)$selectedTemplateId === (int)$tpl['id'] ? 'selected' : '' ?>><?= e($tpl['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="flex items-end">
                <button class="inline-flex items-center rounded-md bg-slate-800 px-3 py-2 text-sm font-semibold text-slate-100 ring-1 ring-white/10 hover:bg-slate-700">Appliquer le modÃ¨le</button>
            </div>
        </form>

        <form method="post" class="mt-6 space-y-5">
            <?= csrf_field() ?>
            <input type="hidden" name="appointment_id" value="<?= e($appointment['id']) ?>" />
            <input type="hidden" name="template_id" value="<?= e($selectedTemplateId) ?>" />
            <div>
                <label for="reminder-channel" class="block text-sm font-medium text-slate-200">Canal</label>
                <div class="mt-2">
                    <select id="reminder-channel" name="channel" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500">
                        <?php foreach (['SMS', 'WHATSAPP', 'EMAIL', 'AUTRE']  as $ch): ?>
                            <option value="<?= e($ch) ?>" <?= $channel === $ch ? 'selected' : '' ?>><?= e($ch) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div>
                <label for="reminder-message" class="block text-sm font-medium text-slate-200">Message <span class="text-rose-400">*</span></label>
                <div class="mt-2">
                    <textarea id="reminder-message" name="message" rows="6" required <?= $hasErrors ? 'aria-describedby="reminder-send-error"' : '' ?> class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500"><?= e($message) ?></textarea>
                </div>
                <p class="mt-2 text-xs text-slate-400">Variables: {patient_nom}, {date}, {heure}, {doctor_nom}, {cabinet_tel}</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button name="status" value="SENT" class="inline-flex items-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Enregistrer comme envoy</button>
                <button name="status" value="DRAFT" class="inline-flex items-center rounded-md bg-slate-800 px-3 py-2 text-sm font-semibold text-slate-100 ring-1 ring-white/10 hover:bg-slate-700">Enregistrer brouillon</button>
                <a class="text-sm font-semibold text-slate-300 hover:text-white" href="r=appointments">Annuler</a>
            </div>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
>