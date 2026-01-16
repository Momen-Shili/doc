<?php
$title = $mode === 'edit' ? 'Modifier un modèle' : 'Nouveau modèle';
ob_start();
$hasErrors = !empty($errors);
$waMap = $waMap ?? ['template_name' => '', 'language_code' => 'fr'];
?>
<div class="max-w-2xl">
    <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
        <h1 class="text-lg font-semibold text-white"><?= e($title) ?></h1>
        <p class="mt-1 text-sm text-slate-400">Rédigez un message de rappel clair et concis.</p>

        <?php if ($hasErrors): ?>
            <div id="reminder-template-error" class="mt-4 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200" role="alert">
                <?= e(implode(' ', $errors)) ?>
            </div>
        <?php endif; ?>

        <form method="post" class="mt-6 space-y-5">
            <?= csrf_field() ?>
            <div>
                <label for="reminder-name" class="block text-sm font-medium text-slate-200">Nom <span class="text-rose-400">*</span></label>
                <div class="mt-2">
                    <input id="reminder-name" type="text" name="name" value="<?= e($template['name'] ?? '') ?>" required <?= $hasErrors ? 'aria-describedby="reminder-template-error"' : '' ?> class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <div>
                <label for="reminder-channel" class="block text-sm font-medium text-slate-200">Canal</label>
                <div class="mt-2">
                    <select id="reminder-channel" name="channel" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500">
                        <?php foreach (['SMS', 'WHATSAPP', 'EMAIL'] as $channel): ?>
                            <option value="<?= e($channel) ?>" <?= ($template['channel'] ?? 'SMS') === $channel ? 'selected' : '' ?>><?= e($channel) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div>
                <label for="reminder-body" class="block text-sm font-medium text-slate-200">Message <span class="text-rose-400">*</span></label>
                <div class="mt-2">
                    <textarea id="reminder-body" name="body" rows="6" required <?= $hasErrors ? 'aria-describedby="reminder-template-error"' : '' ?> class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500"><?= e($template['body'] ?? '') ?></textarea>
                </div>
                <p class="mt-2 text-xs text-slate-400">Variables: {patient_nom}, {date}, {heure}, {doctor_nom}, {cabinet_tel}</p>
            </div>

            <div class="rounded-xl bg-slate-800/40 p-4 ring-1 ring-white/10">
                <h2 class="text-sm font-semibold text-slate-100">WhatsApp (optionnel)</h2>
                <p class="mt-1 text-xs text-slate-400">Associez un template WhatsApp Infobip pour ce modèle.</p>
                <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label for="wa-template-name" class="block text-xs font-medium text-slate-200">Nom du template</label>
                        <input id="wa-template-name" type="text" name="wa_template_name" value="<?= e($waMap['template_name'] ?? '') ?>" class="mt-2 w-full rounded-md border-0 bg-slate-900/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10" />
                    </div>
                    <div>
                        <label for="wa-language" class="block text-xs font-medium text-slate-200">Langue</label>
                        <input id="wa-language" type="text" name="wa_language_code" value="<?= e($waMap['language_code'] ?? 'fr') ?>" class="mt-2 w-full rounded-md border-0 bg-slate-900/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10" />
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input id="reminder-active" type="checkbox" name="is_active" value="1" <?= !isset($template['is_active']) || (int)$template['is_active'] === 1 ? 'checked' : '' ?> class="h-4 w-4 rounded border-white/10 bg-slate-800 text-indigo-500 focus:ring-indigo-500" />
                <label for="reminder-active" class="text-sm text-slate-200">Actif</label>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-md bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Enregistrer</button>
                <a href="?r=reminders-templates" class="text-sm font-semibold text-slate-300 hover:text-white">Annuler</a>
            </div>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
?>
