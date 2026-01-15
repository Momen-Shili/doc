<?php
$title = 'Paramètres';
ob_start();
$hasErrors = !empty($errors);
>
<div class="max-w-2xl">
    <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
        <h1 class="text-lg font-semibold text-white">Paramètres de la plateforme</h1>
        <p class="mt-1 text-sm text-slate-400">Configuration globale du SaaS.</p>

        <?php if ($hasErrors): ?>
            <div id="admin-settings-error" class="mt-4 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200" role="alert">
                <?= e(implode(' ', $errors)) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="r=settings" class="mt-6 space-y-5">
            <?= csrf_field() ?>
            <div>
                <label for="admin-app-name" class="block text-sm font-medium text-slate-200">Nom de la plateforme <span class="text-rose-400">*</span></label>
                <div class="mt-2">
                    <input id="admin-app-name" type="text" name="app_name" value="<?= e($settings['app_name'] '') ?>" required <?= $hasErrors ? 'aria-describedby="admin-settings-error"' : '' ?> class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <div>
                <label for="admin-support-email" class="block text-sm font-medium text-slate-200">Email support</label>
                <div class="mt-2">
                    <input id="admin-support-email" type="email" name="support_email" value="<?= e($settings['support_email'] '') ?>" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <button class="inline-flex items-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Enregistrer</button>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
>