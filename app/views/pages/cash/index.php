<php
$title = 'Caisse';
ob_start();
>
<div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
    <div class="xl:col-span-8 space-y-6">
        <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-lg font-semibold text-white">Caisse</h1>
                    <p class="text-sm text-slate-400">Suivi des encaissements en espèces.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a class="inline-flex items-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400" href="r=payments-create">Encaisser un paiement</a>
                    <a class="inline-flex items-center rounded-md bg-slate-800 px-3 py-2 text-sm font-semibold text-slate-100 ring-1 ring-white/10 hover:bg-slate-700" href="r=cash-export&from=<= e($from) >&to=<= e($to) >">Exporter CSV</a>
                </div>
            </div>
        </div>

        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-2xl bg-slate-900/60 p-4 ring-1 ring-white/10">
                <dt class="text-xs uppercase tracking-wide text-slate-400">Total encaissé aujourd’hui</dt>
                <dd class="mt-2 text-lg font-semibold text-white"><= e(format_tnd($todayTotal)) ></dd>
            </div>
            <div class="rounded-2xl bg-slate-900/60 p-4 ring-1 ring-white/10">
                <dt class="text-xs uppercase tracking-wide text-slate-400">Nb paiements aujourd’hui</dt>
                <dd class="mt-2 text-lg font-semibold text-white"><= e($todayCount) ></dd>
            </div>
            <div class="rounded-2xl bg-slate-900/60 p-4 ring-1 ring-white/10">
                <dt class="text-xs uppercase tracking-wide text-slate-400">Total période</dt>
                <dd class="mt-2 text-lg font-semibold text-white"><= e(format_tnd($rangeTotal)) ></dd>
            </div>
        </dl>

        <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
            <h2 class="text-base font-semibold text-white">Filtrer la période</h2>
            <form method="get" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3 sm:items-end">
                <input type="hidden" name="r" value="cash" />
                <div>
                    <label for="cash-from" class="block text-sm font-medium text-slate-200">Du</label>
                    <div class="mt-2">
                        <input id="cash-from" type="date" name="from" value="<= e($from) >" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                    </div>
                </div>
                <div>
                    <label for="cash-to" class="block text-sm font-medium text-slate-200">Au</label>
                    <div class="mt-2">
                        <input id="cash-to" type="date" name="to" value="<= e($to) >" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                    </div>
                </div>
                <div>
                    <button class="inline-flex items-center justify-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Filtrer</button>
                </div>
            </form>
        </div>

        <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
            <h2 class="text-base font-semibold text-white">Paiements d’aujourd’hui</h2>
            <div class="mt-4 space-y-3">
                <php if (!$todayPayments): >
                    <p class="text-sm text-slate-400">Aucun paiement aujourd’hui.</p>
                <php else: >
                    <php foreach ($todayPayments as $payment): >
                        <div class="flex items-center justify-between rounded-xl bg-slate-800/70 p-4">
                            <div>
                                <p class="text-sm font-semibold text-white"><= e(date('H:i', strtotime($payment['created_at']))) > · <= e($payment['patient_name']) ></p>
                                <p class="text-xs text-slate-400">Reçu: <= e($payment['receipt_number']) ></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-white"><= e(format_tnd($payment['amount'])) ></p>
                                <a class="text-xs font-semibold text-indigo-300 hover:text-indigo-200" href="r=payments-receipt&id=<= e($payment['id']) >">Imprimer reçu</a>
                            </div>
                        </div>
                    <php endforeach; >
                <php endif; >
            </div>
        </div>
    </div>

    <div class="xl:col-span-4 space-y-6">
        <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
            <h2 class="text-base font-semibold text-white">Clôture de caisse</h2>
            <php if ($closure): >
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-400">Date</dt>
                        <dd class="font-semibold text-white"><= e($closure['closure_date']) ></dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-400">Total système</dt>
                        <dd class="font-semibold text-white"><= e(format_tnd($closure['total_system'])) ></dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-400">Montant déclaré</dt>
                        <dd class="font-semibold text-white"><= e(format_tnd($closure['cash_declared'])) ></dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-400">Écart</dt>
                        <dd class="font-semibold text-white"><= e(format_tnd($closure['difference'])) ></dd>
                    </div>
                </dl>
                <div class="mt-4">
                    <a class="inline-flex items-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400" href="r=cash-closure-print&id=<= e($closure['id']) >">Imprimer clôture</a>
                </div>
            <php else: >
                <form method="post" action="r=cash-closure" class="mt-4 space-y-4">
                    <= csrf_field() >
                    <div>
                        <label for="cash-closure-date" class="block text-sm font-medium text-slate-200">Date</label>
                        <div class="mt-2">
                            <input id="cash-closure-date" type="date" name="closure_date" value="<= e($today) >" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                        </div>
                    </div>
                    <div>
                        <label for="cash-closure-declared" class="block text-sm font-medium text-slate-200">Montant déclaré</label>
                        <div class="mt-2">
                            <input id="cash-closure-declared" type="number" step="0.001" name="cash_declared" required class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                        </div>
                    </div>
                    <div>
                        <label for="cash-closure-notes" class="block text-sm font-medium text-slate-200">Notes</label>
                        <div class="mt-2">
                            <textarea id="cash-closure-notes" name="notes" rows="3" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500"></textarea>
                        </div>
                    </div>
                    <button class="inline-flex items-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Clôturer</button>
                </form>
            <php endif; >
        </div>
    </div>
</div>
<php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
>