<php
$title = 'Import CSV';
ob_start();
>
<div class="max-w-2xl">
    <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
        <h1 class="text-lg font-semibold text-white">Import CSV</h1>
        <p class="mt-1 text-sm text-slate-400">Importer patients, rendez-vous ou paiements.</p>

        <php if (!empty($errors)): >
            <div class="mt-4 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200" role="alert">
                <= e(implode(' ', $errors)) >
            </div>
        <php endif; >

        <form method="post" enctype="multipart/form-data" action="r=import" class="mt-6 space-y-5">
            <= csrf_field() >
            <div>
                <label for="import-csv-file" class="block text-sm font-medium text-slate-200">Fichier CSV <span class="text-rose-400">*</span></label>
                <div class="mt-2">
                    <input id="import-csv-file" type="file" name="csv_file" accept=".csv" required class="block w-full text-sm text-slate-200 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-500 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-400" />
                </div>
                <p class="mt-2 text-xs text-slate-400">Nom attendu: patients.csv, appointments.csv ou payments.csv</p>
            </div>
            <button class="inline-flex items-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Uploader</button>
        </form>
    </div>
</div>
<php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
>