<php
$title = 'Inscription';
ob_start();
$hasErrors = !empty($errors);
>
<div class="w-full max-w-md">
    <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
        <h1 class="text-lg font-semibold text-white">Inscription médecin</h1>
        <p class="mt-1 text-sm text-slate-400">Créez votre compte en quelques minutes.</p>

        <php if ($hasErrors): >
            <div id="register-error" class="mt-4 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200" role="alert">
                <= e(implode(' ', $errors)) >
            </div>
        <php endif; >

        <form method="post" action="r=register" class="mt-6 space-y-5">
            <= csrf_field() >
            <div>
                <label for="register-full-name" class="block text-sm font-medium text-slate-200">Nom complet <span class="text-rose-400">*</span></label>
                <div class="mt-2">
                    <input id="register-full-name" type="text" name="full_name" required <= $hasErrors 'aria-describedby="register-error"' : '' > class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <div>
                <label for="register-specialty" class="block text-sm font-medium text-slate-200">Spécialité</label>
                <div class="mt-2">
                    <input id="register-specialty" type="text" name="specialty" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <div>
                <label for="register-phone" class="block text-sm font-medium text-slate-200">Téléphone</label>
                <div class="mt-2">
                    <input id="register-phone" type="text" name="phone" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <div>
                <label for="register-email" class="block text-sm font-medium text-slate-200">Email <span class="text-rose-400">*</span></label>
                <div class="mt-2">
                    <input id="register-email" type="email" name="email" required <= $hasErrors 'aria-describedby="register-error"' : '' > class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <div>
                <label for="register-password" class="block text-sm font-medium text-slate-200">Mot de passe <span class="text-rose-400">*</span></label>
                <div class="mt-2">
                    <input id="register-password" type="password" name="password" required <= $hasErrors 'aria-describedby="register-error"' : '' > class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <button class="inline-flex w-full items-center justify-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Créer le compte</button>
        </form>

        <p class="mt-6 text-sm text-slate-400">
            Déjà inscrit a<a class="font-semibold text-indigo-300 hover:text-indigo-200" href="r=login">Se connecter</a>
        </p>
    </div>
</div>
<php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
>