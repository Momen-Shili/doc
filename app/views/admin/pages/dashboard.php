<?php
$title = 'Tableau de bord admin';
ob_start();
?>
<div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <h1 class="text-lg font-semibold text-white">Tableau de bord</h1>
    <p class="mt-1 text-sm text-slate-400">Vue globale de la plateforme.</p>
</div>

<dl class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div class="rounded-2xl bg-slate-900/60 p-4 ring-1 ring-white/10">
        <dt class="text-xs uppercase tracking-wide text-slate-400">Médecins</dt>
        <dd class="mt-2 text-lg font-semibold text-white"><?= e($totalDoctors)    ?></dd>
    </div>
    <div class="rounded-2xl bg-slate-900/60 p-4 ring-1 ring-white/10">
        <dt class="text-xs uppercase tracking-wide text-slate-400">Actifs</dt>
        <dd class="mt-2 text-lg font-semibold text-white"><?= e($activeDoctors)    ?></dd>
    </div>
    <div class="rounded-2xl bg-slate-900/60 p-4 ring-1 ring-white/10">
        <dt class="text-xs uppercase tracking-wide text-slate-400">Suspendus</dt>
        <dd class="mt-2 text-lg font-semibold text-white"><?= e($suspendedDoctors)    ?></dd>
    </div>
</dl>

<div class="mt-6 rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <dt class="text-xs uppercase tracking-wide text-slate-400">Chiffre d'affaires global (espèces)</dt>
    <dd class="mt-2 text-lg font-semibold text-white"><?= e(format_tnd($globalRevenue))    ?></dd>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>