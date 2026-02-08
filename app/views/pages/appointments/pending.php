<?php
$title = 'Demandes';
ob_start();
?>
<div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-lg font-semibold text-white">Demandes en attente</h1>
            <p class="mt-1 text-sm text-slate-400">Validez ou refusez les demandes de rendez-vous.</p>
        </div>
        <a class="inline-flex items-center rounded-md bg-slate-800 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-700" href="?r=appointments">Voir tous les rendez-vous</a>
    </div>
</div>

<div class="mt-6 rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
    <div class="-mx-3 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="text-xs uppercase text-slate-400">
                <tr>
                    <th class="px-3 py-2 text-left" scope="col">Date</th>
                    <th class="px-3 py-2 text-left" scope="col">Patient</th>
                    <th class="px-3 py-2 text-left" scope="col">Contact</th>
                    <th class="px-3 py-2 text-left" scope="col">Motif</th>
                    <th class="px-3 py-2 text-right" scope="col">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php if (!$appointments): ?>
                    <tr>
                        <td colspan="5" class="px-3 py-6 text-center text-sm text-slate-400">Aucune demande en attente.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($appointments as $appointment): ?>
                        <?php
                        $patientName = $appointment['patient_name'] ?: ($appointment['public_patient_name'] ?? 'Patient');
                        $patientPhone = $appointment['patient_phone'] ?: ($appointment['public_patient_phone'] ?? '');
                        $patientEmail = $appointment['patient_email'] ?: ($appointment['public_patient_email'] ?? '');
                        ?>
                        <tr class="odd:bg-slate-800/40">
                            <td class="px-3 py-3 text-slate-100">
                                <?= e(date('d/m/Y H:i', strtotime($appointment['starts_at']))) ?>
                            </td>
                            <td class="px-3 py-3 text-slate-100">
                                <?= e($patientName) ?>
                                <div class="mt-1 text-xs text-slate-400">Statut: <?= e(appointment_status_label_fr($appointment['status'])) ?></div>
                            </td>
                            <td class="px-3 py-3 text-slate-300">
                                <div><?= e($patientPhone) ?></div>
                                <div class="text-xs text-slate-400"><?= e($patientEmail) ?></div>
                            </td>
                            <td class="px-3 py-3 text-slate-300"><?= e($appointment['reason'] ?? '') ?></td>
                            <td class="px-3 py-3 text-right text-sm font-semibold">
                                <form method="post" action="?r=appointments-pending-confirm" class="inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= e($appointment['id']) ?>">
                                    <button class="rounded-md bg-emerald-500 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-400">Confirmer</button>
                                </form>
                                <form method="post" action="?r=appointments-pending-refuse" class="inline" onsubmit="return confirm('Refuser cette demande ?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= e($appointment['id']) ?>">
                                    <button class="ml-2 rounded-md bg-rose-500 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-400">Refuser</button>
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
