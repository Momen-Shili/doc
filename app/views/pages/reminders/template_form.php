<php
$title = $mode === 'edit' 'Modifier un modÃ¨le' : 'Nouveau modÃ¨le';
ob_start();
$hasErrors = !empty($errors);
>
<div class="max-w-2xl">
    <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
        <h1 class="text-lg font-semibold text-white"><= e($title) ></h1>
        <p class="mt-1 text-sm text-slate-400">RÃ©digez un message de rappel clair et concis.</p>

        <php if ($hasErrors): >
            <div id="reminder-template-error" class="mt-4 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200" role="alert">
                <= e(implode(' ', $errors)) >
            </div>
        <php endif; >

        <form method="post" class="mt-6 space-y-5">
            <= csrf_field() >
            <div>
                <label for="reminder-name" class="block text-sm font-medium text-slate-200">Nom <span class="text-rose-400">*</span></label>
                <div class="mt-2">
                    <input id="reminder-name" type="text" name="name" value="<= e($template['name'] '') >" required <= $hasErrors 'aria-describedby="reminder-template-error"' : '' > class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <div>
                <label for="reminder-channel" class="block text-sm font-medium text-slate-200">Canal</label>
                <div class="mt-2">
                    <select id="reminder-channel" name="channel" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500">
                        <php foreach (['SMS', 'WHATSAPP', 'EMAIL', 'AUTRE']  as $channel): >
                            <option value="<= e($channel) >" <= ($template['channel'] 'SMS') === $channel 'selected' : '' >><= e($channel) ></option>
                        <php endforeach; >
                    </select>
                </div>
            </div>
            <div>
                <label for="reminder-body" class="block text-sm font-medium text-slate-200">Message <span class="text-rose-400">*</span></label>
                <div class="mt-2">
                    <textarea id="reminder-body" name="body" rows="6" required <= $hasErrors 'aria-describedby="reminder-template-error"' : '' > class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500"><= e($template['body'] '') ></textarea>
                </div>
                <p class="mt-2 text-xs text-slate-400">Variables: {patient_nom}, {date}, {heure}, {doctor_nom}, {cabinet_tel}</p>
            </div>
            <div class="flex items-center gap-2">
                <input id="reminder-active" type="checkbox" name="is_active" class="rounded border-slate-600 bg-slate-800 text-indigo-500 focus:ring-indigo-500" <= (int)($template['is_active'] 1) === 1 'checked' : '' > />
                <label for="reminder-active" class="text-sm text-slate-300">Actif</label>
            </div>
            <div class="flex items-center gap-3">
                <button class="inline-flex items-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Enregistrer</button>
                <a class="text-sm font-semibold text-slate-300 hover:text-white" href="r=reminders-templates">Annuler</a>
            </div>
        </form>
    </div>
</div>
<php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
>