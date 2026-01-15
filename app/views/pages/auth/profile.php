<php
$title = 'Paramètres';
ob_start();
$hasErrors = !empty($errors);
>
<div class="max-w-2xl">
    <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
        <h1 class="text-lg font-semibold text-white">Profil médecin</h1>
        <p class="mt-1 text-sm text-slate-400">Gérez vos informations personnelles.</p>

        <php if ($hasErrors): >
            <div id="profile-error" class="mt-4 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200" role="alert">
                <= e(implode(' ', $errors)) >
            </div>
        <php endif; >

        <form method="post" action="r=profile" class="mt-6 space-y-5">
            <= csrf_field() >
            <div>
                <label for="profile-full-name" class="block text-sm font-medium text-slate-200">Nom complet <span class="text-rose-400">*</span></label>
                <div class="mt-2">
                    <input id="profile-full-name" type="text" name="full_name" value="<= e($doctor['full_name'] '') >" required <= $hasErrors 'aria-describedby="profile-error"' : '' > class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <div>
                <label for="profile-specialty" class="block text-sm font-medium text-slate-200">Spécialité</label>
                <div class="mt-2">
                    <input id="profile-specialty" type="text" name="specialty" value="<= e($doctor['specialty'] '') >" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <div>
                <label for="profile-phone" class="block text-sm font-medium text-slate-200">Téléphone</label>
                <div class="mt-2">
                    <input id="profile-phone" type="text" name="phone" value="<= e($doctor['phone'] '') >" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <div>
                <label for="profile-email" class="block text-sm font-medium text-slate-200">Email</label>
                <div class="mt-2">
                    <input id="profile-email" type="email" value="<= e($doctor['email'] '') >" disabled class="block w-full rounded-md border-0 bg-slate-800/40 px-3 py-2 text-sm text-slate-400 ring-1 ring-inset ring-white/5" />
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button class="inline-flex items-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Enregistrer</button>
                <a class="text-sm font-semibold text-slate-300 hover:text-white" href="r=dashboard">Retour</a>
            </div>
        </form>
    </div>
</div>
<php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
>