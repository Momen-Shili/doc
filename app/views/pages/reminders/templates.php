<?php
$title = 'Rappels';
ob_start();
?>
<div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-lg font-semibold text-white">Modèles de rappels</h1>
            <p class="mt-1 text-sm text-slate-400">Créez des modèles pour vos rendez-vous.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a class="inline-flex items-center rounded-md bg-slate-800 px-3 py-2 text-sm font-semibold text-slate-100 ring-1 ring-white/10 hover:bg-slate-700" href="?r=reminders-log">Journal</a>
            <a class="inline-flex items-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400" href="?r=reminders-templates-create">Nouveau modèle</a>
        </div>
    </div>
</div>

<div class="mt-6 rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <h2 class="text-base font-semibold text-white">Variables disponibles</h2>
    <div class="mt-3 flex flex-wrap gap-2 text-sm text-slate-300">
        <span class="rounded-full bg-slate-800 px-3 py-1">{patient_nom}</span>
        <span class="rounded-full bg-slate-800 px-3 py-1">{date}</span>
        <span class="rounded-full bg-slate-800 px-3 py-1">{heure}</span>
        <span class="rounded-full bg-slate-800 px-3 py-1">{doctor_nom}</span>
        <span class="rounded-full bg-slate-800 px-3 py-1">{cabinet_tel}</span>
    </div>
</div>

<div class="mt-6 rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <div class="-mx-3 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="text-xs uppercase text-slate-400">
                <tr>
                    <th class="px-3 py-2 text-left" scope="col">Nom</th>
                    <th class="px-3 py-2 text-left" scope="col">Canal</th>
                    <th class="px-3 py-2 text-left" scope="col">Aperçu</th>
                    <th class="px-3 py-2 text-left" scope="col">Actif</th>
                    <th class="px-3 py-2 text-right" scope="col">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php if (empty($templates)): ?>
                    <tr>
                        <td colspan="5" class="px-3 py-6 text-center text-sm text-slate-400">Aucun modèle pour le moment.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($templates as $template): ?>
                        <tr class="odd:bg-slate-800/40">
                            <td class="px-3 py-3 text-slate-100"><?= e($template['name']) ?></td>
                            <td class="px-3 py-3 text-slate-300"><?= e($template['channel']) ?></td>
                            <td class="px-3 py-3 text-slate-300">
                                <?php
                                $preview = $previews[$template['id']] ?? $template['body'];
                                if (function_exists('mb_strlen')) {
                                    $short = mb_strlen($preview) > 80 ? mb_substr($preview, 0, 80) . '...' : $preview;
                                } else {
                                    $short = strlen($preview) > 80 ? substr($preview, 0, 80) . '...' : $preview;
                                }
                                ?>
                                <?= e($short) ?>
                            </td>
                            <td class="px-3 py-3">
                                <span class="rounded-full px-2 py-1 text-xs font-semibold <?= (int)$template['is_active'] === 1 ? 'bg-emerald-500/20 text-emerald-200' : 'bg-slate-700 text-slate-300' ?>">
                                    <?= (int)$template['is_active'] === 1 ? 'Actif' : 'Inactif' ?>
                                </span>
                            </td>
                            <td class="px-3 py-3 text-right text-sm font-semibold">
                                <a class="text-indigo-300 hover:text-indigo-200" href="?r=reminders-templates-edit&id=<?= e($template['id']) ?>">Modifier</a>
                                <form method="post" action="?r=reminders-templates-toggle" class="inline" onsubmit="return confirm('Changer le statut de ce modèle ?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= e($template['id']) ?>" />
                                    <button class="ml-3 text-slate-300 hover:text-white"><?= (int)$template['is_active'] === 1 ? 'Désactiver' : 'Activer' ?></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
?>