<php
$title = 'Créer un service';
ob_start();
$hasErrors = !empty($errors);
>
<div class="max-w-2xl">
    <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
        <h1 class="text-lg font-semibold text-white">Créer un service</h1>
        <p class="text-sm text-slate-400">Définissez un acte ou service.</p>

        <php if ($hasErrors): >
            <div id="service-create-error" class="mt-4 rounded-md bg-rose-500/10 p-3 text-sm text-rose-200 ring-1 ring-rose-500/30" role="alert">
                <= e(implode(' ', $errors)) >
            </div>
        <php endif; >

        <form method="post" action="r=services-create" class="mt-6 space-y-4">
            <= csrf_field() >
            <div>
                <label for="service-name" class="block text-sm font-medium text-slate-200">Nom <span class="text-rose-300">*</span></label>
                <input id="service-name" type="text" name="name" required <= $hasErrors 'aria-describedby="service-create-error"' : '' > class="mt-2 block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="service-default-price" class="block text-sm font-medium text-slate-200">Prix par défaut (TND)</label>
                    <input id="service-default-price" type="number" step="0.001" name="default_price" value="0" class="mt-2 block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                </div>
                <div>
                    <label for="service-default-duration" class="block text-sm font-medium text-slate-200">Durée par défaut (minutes)</label>
                    <input id="service-default-duration" type="number" name="default_duration_minutes" value="0" class="mt-2 block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                </div>
            </div>
            <div>
                <label for="service-description" class="block text-sm font-medium text-slate-200">Description</label>
                <textarea id="service-description" name="description" rows="3" class="mt-2 block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-200">
                <input type="checkbox" name="is_active" checked class="rounded border-slate-600 bg-slate-800 text-indigo-500 focus:ring-indigo-500" />
                Actif
            </label>
            <div class="flex items-center gap-3">
                <button class="inline-flex items-center rounded-md bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Créer</button>
                <a class="text-sm font-semibold text-slate-300 hover:text-white" href="r=services">Annuler</a>
            </div>
        </form>
    </div>
</div>
<php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
>