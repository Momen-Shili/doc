<?php
$title = 'Connexion';
ob_start();
$hasError = !empty($error);
>
<div class="w-full max-w-md">
    <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
        <h1 class="text-lg font-semibold text-white">Connexion médecin</h1>
        <p class="mt-1 text-sm text-slate-400">Accédez à votre espace sécurisé.</p>

        <?php if ($hasError): ?>
            <div id="login-error" class="mt-4 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200" role="alert">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="r=login" class="mt-6 space-y-5">
            <?= csrf_field() ?>
            <div>
                <label for="login-email" class="block text-sm font-medium text-slate-200">Email <span class="text-rose-400">*</span></label>
                <div class="mt-2">
                    <input id="login-email" type="email" name="email" required <?= $hasError ? 'aria-describedby="login-error"' : '' ?> class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <div>
                <label for="login-password" class="block text-sm font-medium text-slate-200">Mot de passe <span class="text-rose-400">*</span></label>
                <div class="mt-2">
                    <input id="login-password" type="password" name="password" required <?= $hasError ? 'aria-describedby="login-error"' : '' ?> class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <button class="inline-flex w-full items-center justify-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Se connecter</button>
        </form>

        <p class="mt-6 text-sm text-slate-400">
            Pas de compte a<a class="font-semibold text-indigo-300 hover:text-indigo-200" href="r=register">Créer un compte</a>
        </p>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
>