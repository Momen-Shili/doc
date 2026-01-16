<?php
$title = 'Mapping CSV';
ob_start();
?>
<div class="max-w-2xl">
    <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
        <h1 class="text-lg font-semibold text-white">Mapping des colonnes</h1>
        <p class="mt-1 text-sm text-slate-400">Associez les colonnes du CSV aux champs.</p>

        <form method="post" action="?r=import-map" class="mt-6 space-y-4">
            <?= csrf_field()    ?>
            <div class="divide-y divide-white/10 border-y border-white/10">
                <?php foreach ($fields as $field): ?>
                    <div class="py-4 sm:flex sm:items-center sm:gap-6">
                        <label for="map-<?= e($field)    ?>" class="text-sm font-medium text-slate-200 sm:w-48"><?= e($field)    ?></label>
                        <div class="mt-2 sm:mt-0 sm:flex-1">
                            <select id="map-<?= e($field)    ?>" name="map_<?= e($field)    ?>" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Choisir --</option>
                                <?php foreach ($headers as $h): ?>
                                    <option value="<?= e($h)    ?>" <?= $h === $field ? 'selected' : ''    ?>><?= e($h)    ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="inline-flex items-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Continuer</button>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
?>