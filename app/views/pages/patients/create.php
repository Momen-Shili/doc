<php
$title = 'Ajouter un patient';
ob_start();
$hasErrors = !empty($errors);
>
<div class="max-w-2xl">
    <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
        <h1 class="text-lg font-semibold text-white">Ajouter un patient</h1>
        <p class="mt-1 text-sm text-slate-400">Renseignez les informations du patient.</p>

        <php if ($hasErrors): >
            <div id="patient-create-error" class="mt-4 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200" role="alert">
                <= e(implode(' ', $errors)) >
            </div>
        <php endif; >

        <form method="post" action="r=patients-create" class="mt-6 space-y-5">
            <= csrf_field() >
            <div>
                <label for="patient-full-name" class="block text-sm font-medium text-slate-200">Nom complet <span class="text-rose-400">*</span></label>
                <div class="mt-2">
                    <input id="patient-full-name" type="text" name="full_name" value="<= e(old('full_name')) >" required <= $hasErrors 'aria-describedby="patient-create-error"' : '' > class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="patient-phone" class="block text-sm font-medium text-slate-200">Téléphone</label>
                    <div class="mt-2">
                        <input id="patient-phone" type="text" name="phone" value="<= e(old('phone')) >" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                    </div>
                </div>
                <div>
                    <label for="patient-email" class="block text-sm font-medium text-slate-200">Email</label>
                    <div class="mt-2">
                        <input id="patient-email" type="email" name="email" value="<= e(old('email')) >" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                    </div>
                </div>
            </div>
            <div>
                <label for="patient-dob" class="block text-sm font-medium text-slate-200">Date de naissance</label>
                <div class="mt-2">
                    <input id="patient-dob" type="date" name="dob" value="<= e(old('dob')) >" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <div>
                <label for="patient-notes" class="block text-sm font-medium text-slate-200">Notes</label>
                <div class="mt-2">
                    <textarea id="patient-notes" name="notes" rows="3" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500"><= e(old('notes')) ></textarea>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button class="inline-flex items-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Enregistrer</button>
                <a class="text-sm font-semibold text-slate-300 hover:text-white" href="r=patients">Annuler</a>
            </div>
        </form>
    </div>
</div>
<php
$content = ob_get_clean();
clear_old();
require __DIR__ . '/../../layout.php';
>