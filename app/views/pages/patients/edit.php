<php
$title = 'Modifier un patient';
ob_start();
$hasErrors = !empty($errors);
>
<div class="max-w-2xl">
    <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
        <h1 class="text-lg font-semibold text-white">Modifier un patient</h1>
        <p class="mt-1 text-sm text-slate-400">Mettez à jour les informations.</p>

        <php if ($hasErrors): >
            <div id="patient-edit-error" class="mt-4 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200" role="alert">
                <= e(implode(' ', $errors)) >
            </div>
        <php endif; >

        <form method="post" action="r=patients-edit&id=<= e($patient['id']) >" class="mt-6 space-y-5">
            <= csrf_field() >
            <div>
                <label for="patient-edit-full-name" class="block text-sm font-medium text-slate-200">Nom complet <span class="text-rose-400">*</span></label>
                <div class="mt-2">
                    <input id="patient-edit-full-name" type="text" name="full_name" value="<= e($patient['full_name']) >" required <= $hasErrors 'aria-describedby="patient-edit-error"' : '' > class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="patient-edit-phone" class="block text-sm font-medium text-slate-200">Téléphone</label>
                    <div class="mt-2">
                        <input id="patient-edit-phone" type="text" name="phone" value="<= e($patient['phone']) >" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                    </div>
                </div>
                <div>
                    <label for="patient-edit-email" class="block text-sm font-medium text-slate-200">Email</label>
                    <div class="mt-2">
                        <input id="patient-edit-email" type="email" name="email" value="<= e($patient['email']) >" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                    </div>
                </div>
            </div>
            <div>
                <label for="patient-edit-dob" class="block text-sm font-medium text-slate-200">Date de naissance</label>
                <div class="mt-2">
                    <input id="patient-edit-dob" type="date" name="dob" value="<= e($patient['dob']) >" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <div>
                <label for="patient-edit-notes" class="block text-sm font-medium text-slate-200">Notes</label>
                <div class="mt-2">
                    <textarea id="patient-edit-notes" name="notes" rows="3" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500"><= e($patient['notes']) ></textarea>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button class="inline-flex items-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Mettre à jour</button>
                <a class="text-sm font-semibold text-slate-300 hover:text-white" href="r=patients">Annuler</a>
            </div>
        </form>
    </div>
</div>
<php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
>