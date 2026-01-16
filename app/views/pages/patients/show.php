<?php
$title = 'Fiche patient';
$dob = $patient['dob'] ?? null;
$age = '';
if ($dob) {
    $birth = new DateTime($dob);
    $age = $birth->diff(new DateTime('today'))->y . ' ans';
}
$editNoteId = (int)($_GET['edit_note'] ?? 0);
$tabs = [
    'appointments' => 'Rendez-vous',
    'payments' => 'Paiements',
    'notes' => 'Notes cliniques',
    'documents' => 'Documents',
];
?>
<?php ob_start(); ?>
<div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
    <div class="xl:col-span-8 space-y-6">
        <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-white"><?= e($patient['full_name']) ?></h1>
                    <p class="mt-1 text-sm text-slate-400">Email: <?= e($patient['email']) ?> · Téléphone: <?= e($patient['phone']) ?></p>
                    <p class="mt-1 text-sm text-slate-400">Date de naissance: <?= e($patient['dob']) ?><?= $age ? ' · ' . e($age) : '' ?></p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($tags as $tag): ?>
                        <?php
                        $tagStyle = !empty($tag['color']) ? 'style="background-color:' . e($tag['color']) . '"' : '';
                        $tagClass = !empty($tag['color']) ? '' : 'bg-indigo-500/20';
                        ?>
                        <span <?= $tagStyle ?> class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold text-white <?= e($tagClass) ?>">
                            <?= e($tag['name']) ?>
                            <form method="post" action="?r=tags-remove" class="ml-2">
                                <?= csrf_field() ?>
                                <input type="hidden" name="patient_id" value="<?= e($patient['id']) ?>" />
                                <input type="hidden" name="tag_id" value="<?= e($tag['id']) ?>" />
                                <button class="text-white/80 hover:text-white" aria-label="Retirer le tag">×</button>
                            </form>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>

            <form method="post" action="?r=tags-add" class="mt-4 flex flex-col gap-3 sm:flex-row">
                <?= csrf_field() ?>
                <input type="hidden" name="patient_id" value="<?= e($patient['id']) ?>" />
                <input type="text" name="tag_name" placeholder="Nom du tag" class="w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500" />
                <input type="text" name="tag_color" placeholder="#4F46E5" class="w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500" />
                <button class="rounded-md bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Ajouter un tag</button>
            </form>
        </div>

        <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
            <div class="flex flex-wrap gap-2">
                <?php foreach ($tabs as $key => $label): ?>
                    <a class="rounded-md px-3 py-2 text-sm font-semibold <?= $tab === $key ? 'bg-indigo-500 text-white' : 'bg-slate-800 text-slate-200 hover:bg-slate-700' ?>" href="?r=patient&id=<?= e($patient['id']) ?>&tab=<?= e($key) ?>">
                        <?= e($label) ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if ($tab === 'appointments'): ?>
                <div class="mt-4">
                    <?php if (!$appointments): ?>
                        <p class="text-sm text-slate-400">Aucun rendez-vous pour le moment.</p>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($appointments as $appt): ?>
                                <div class="rounded-lg bg-slate-800/70 p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-semibold text-white"><?= e(date('d/m/Y H:i', strtotime($appt['starts_at']))) ?> – <?= e(date('H:i', strtotime($appt['ends_at']))) ?></p>
                                            <p class="text-xs text-slate-400">Statut: <?= e(appointment_status_label($appt['status'])) ?></p>
                                        </div>
                                        <a class="text-xs font-semibold text-indigo-300 hover:text-indigo-200" href="?r=appointments-edit&id=<?= e($appt['id']) ?>">Voir</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php elseif ($tab === 'payments'): ?>
                <div class="mt-4">
                    <?php if (!$payments): ?>
                        <p class="text-sm text-slate-400">Aucun paiement pour le moment.</p>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($payments as $payment): ?>
                                <div class="rounded-lg bg-slate-800/70 p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-semibold text-white"><?= e(date('d/m/Y', strtotime($payment['received_at']))) ?> · <?= e(format_tnd($payment['amount'])) ?></p>
                                            <p class="text-xs text-slate-400">Référence: <?= e($payment['reference']) ?></p>
                                        </div>
                                        <?php if (!empty($payment['receipt_number'])): ?>
                                            <a class="text-xs font-semibold text-indigo-300 hover:text-indigo-200" href="?r=payments-receipt&id=<?= e($payment['id']) ?>">Reçu</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php elseif ($tab === 'notes'): ?>
                <div class="mt-4">
                    <form method="post" action="?r=notes-create" class="space-y-3">
                        <?= csrf_field() ?>
                        <input type="hidden" name="patient_id" value="<?= e($patient['id']) ?>" />
                        <textarea name="note_text" rows="4" class="w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" placeholder="Ajouter une note clinique..."></textarea>
                        <button class="rounded-md bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Ajouter la note</button>
                    </form>

                    <div class="mt-4 space-y-4">
                        <?php if (!$notes): ?>
                            <p class="text-sm text-slate-400">Aucune note clinique pour le moment.</p>
                        <?php else: ?>
                            <?php foreach ($notes as $note): ?>
                                <div class="rounded-lg bg-slate-800/70 p-4">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <p class="text-xs text-slate-400"><?= e(date('d/m/Y H:i', strtotime($note['created_at']))) ?></p>
                                            <p class="mt-2 text-sm text-slate-100"><?= nl2br(e($note['note_text'])) ?></p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a class="text-xs font-semibold text-indigo-300 hover:text-indigo-200" href="?r=patient&id=<?= e($patient['id']) ?>&tab=notes&edit_note=<?= e($note['id']) ?>">Modifier</a>
                                            <form method="post" action="?r=notes-delete" onsubmit="return confirm('Supprimer cette note ?');">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="note_id" value="<?= e($note['id']) ?>" />
                                                <input type="hidden" name="patient_id" value="<?= e($patient['id']) ?>" />
                                                <button class="text-xs font-semibold text-rose-300 hover:text-rose-200">Supprimer</button>
                                            </form>
                                        </div>
                                    </div>

                                    <?php if ($editNoteId === (int)$note['id']): ?>
                                        <form method="post" action="?r=notes-edit" class="mt-3 space-y-3">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="note_id" value="<?= e($note['id']) ?>" />
                                            <input type="hidden" name="patient_id" value="<?= e($patient['id']) ?>" />
                                            <textarea name="note_text" rows="3" class="w-full rounded-md border-0 bg-slate-900/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500"><?= e($note['note_text']) ?></textarea>
                                            <button class="rounded-md bg-indigo-500 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-400">Enregistrer</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="mt-4">
                    <form method="post" enctype="multipart/form-data" action="?r=documents-upload" class="space-y-3">
                        <?= csrf_field() ?>
                        <input type="hidden" name="patient_id" value="<?= e($patient['id']) ?>" />
                        <input id="patient-document-file" type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" required class="block w-full text-sm text-slate-200 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-500 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-400" />
                        <input id="patient-document-note" type="text" name="note" placeholder="Note (optionnel)" class="w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500" />
                        <button class="rounded-md bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Ajouter le document</button>
                    </form>

                    <div class="mt-4 space-y-3">
                        <?php if (!$documents): ?>
                            <p class="text-sm text-slate-400">Aucun document pour le moment.</p>
                        <?php else: ?>
                            <?php foreach ($documents as $doc): ?>
                                <div class="rounded-lg bg-slate-800/70 p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-semibold text-white"><?= e($doc['file_name']) ?></p>
                                            <p class="text-xs text-slate-400"><?= e($doc['mime_type']) ?> · <?= e(number_format(($doc['size_bytes'] ?? 0) / 1024, 1, ',', ' ')) ?> Ko</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a class="text-xs font-semibold text-indigo-300 hover:text-indigo-200" href="?r=documents-download&id=<?= e($doc['id']) ?>">Télécharger</a>
                                            <form method="post" action="?r=documents-delete" onsubmit="return confirm('Supprimer ce document ?');">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="doc_id" value="<?= e($doc['id']) ?>" />
                                                <input type="hidden" name="patient_id" value="<?= e($patient['id']) ?>" />
                                                <button class="text-xs font-semibold text-rose-300 hover:text-rose-200">Supprimer</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="xl:col-span-4 space-y-6">
        <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
            <h2 class="text-base font-semibold text-white">Statistiques</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <dt class="text-slate-400">Absents</dt>
                    <dd class="font-semibold text-white"><?= e($noShowCount) ?></dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-slate-400">Dernière visite</dt>
                    <dd class="font-semibold text-white"><?= e($lastVisit ? date('d/m/Y', strtotime($lastVisit)) : '—') ?></dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-slate-400">Total encaissé</dt>
                    <dd class="font-semibold text-white"><?= e(format_tnd($totalPaid)) ?></dd>
                </div>
            </dl>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
?>