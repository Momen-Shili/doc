<php
$title = 'Services';
ob_start();
>
<div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-lg font-semibold text-white">Services</h1>
            <p class="text-sm text-slate-400">Catalogue des actes et services.</p>
        </div>
        <a class="inline-flex items-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400" href="r=services-create">Créer un service</a>
    </div>
</div>

<div class="mt-6 rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <php if (!$services): >
        <p class="text-sm text-slate-400">Aucun service pour le moment.</p>
    <php else: >
        <div class="space-y-3">
            <php foreach ($services as $service): >
                <div class="flex flex-wrap items-center justify-between gap-4 rounded-xl bg-slate-800/70 p-4">
                    <div>
                        <p class="text-sm font-semibold text-white"><= e($service['name']) ></p>
                        <p class="text-xs text-slate-400">Prix: <= e(format_tnd($service['default_price'])) > · Durée: <= e($service['default_duration_minutes']) > min</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="rounded-md px-2 py-1 text-xs font-semibold <= (int)$service['is_active'] === 1 'bg-emerald-500/20 text-emerald-200' : 'bg-slate-700 text-slate-300' >">
                            <= (int)$service['is_active'] === 1 'Actif' : 'Inactif' >
                        </span>
                        <a class="text-xs font-semibold text-indigo-300 hover:text-indigo-200" href="r=services-edit&id=<= e($service['id']) >">Modifier</a>
                        <form method="post" action="r=services-toggle">
                            <= csrf_field() >
                            <input type="hidden" name="id" value="<= e($service['id']) >" />
                            <button class="text-xs font-semibold text-slate-300 hover:text-white">Basculer</button>
                        </form>
                    </div>
                </div>
            <php endforeach; >
        </div>
    <php endif; >
</div>
<php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
>