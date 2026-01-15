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
>
<?php ob_start(); ?>
<div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
    <div class="xl:col-span-8 space-y-6">
        <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-white"><?= e($patient['full_name']) ?></h1>
                    <p class="mt-1 text-sm text-slate-400">Email: <?= e($patient['email']) ?> · Téléphone: <?= e($patient['phone']) ?></p>
                    <p class="mt-1 text-sm text-slate-400">Date de naissance: <?= e($patient['dob']) ?><?= $age' · ' . e($age) ?? : '' ?></p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($tags as $tag): ?>
                        <?php
                        $tagStyle = $tag['color'] 'style="background-color:' . e($tag['color']) ?? . '"' : '';
                        $tagClass = $tag['color'] ? '' : 'bg-indigo-500/20';
                        >
                        <span <?= $tagStyle ?> class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold text-white <?= e($tagClass) ?>">
                            <?= e($tag['name']) ?>
                            <form method="post" action="r=tags-remove" class="ml-2">
                                <?= csrf_field() ?>
                                <input type="hidden" name="patient_id" value="<?= e($patient['id']) ?>" />
                                <input type="hidden" name="tag_id" value="<?= e($tag['id']) ?>" />
                                <button class="text-white/80 hover:text-white" aria-label="Retirer le tag">×</button>
                            </form>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <form method="post" action="r=tags-add" class="mt-4 flex flex-col gap-3 sm:flex-row">
                <?= csrf_field() ?>
                <input type="hidden" name="patient_id" value="<?= e($patient['id']) ?>" />
                <label for="patient-tag-name" class="sr-only">Nom du tag</label>
                <input id="patient-tag-name" type="text" name="tag_name" placeholder="Ajouter un tag" class="flex-1 rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500" />
                <label for="patient-tag-color" class="sr-only">Couleur du tag</label>
                <input id="patient-tag-color" type="color" name="tag_color" value="#6366f1" class="h-10 w-12 rounded-md border-0 bg-slate-800/70 ring-1 ring-inset ring-white/10" />
                <button class="inline-flex items-center rounded-md bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Ajouter</button>
            </form>
        </div>

        <div class="rounded-2xl bg-slate-900/60 p-4 ring-1 ring-white/10">
            <div class="flex flex-wrap gap-2">
                <?php foreach ($tabs as $key => $label): ?>
                    <a class="rounded-md px-3 py-2 text-sm font-semibold <?= $tab === $key ? 'bg-indigo-500 text-white' : 'bg-slate-800 text-slate-200 hover:bg-slate-700' ?>" href="r=patient&id=<?= e($patient['id']) ?>&tab=<?= e($key) ?>">
                        <?= e($label) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if ($tab === 'appointments'): ?>
            <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
                <h2 class="text-base font-semibold text-white">Historique des rendez-vous</h2>
                <div class="mt-4 space-y-3">
                    <?php if (!$appointments): ?>
                        <p class="text-sm text-slate-400">Aucun rendez-vous enregistré.</p>
                    <?php else: ?>
                        <?php foreach ($appointments as $appt): ?>
                            <div class="rounded-xl bg-slate-800/70 p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-white"><?= e(date('d/m/Y H:i', strtotime($appt['starts_at']))) ?></p>
                                        <p class="text-xs text-slate-400"><?= e($appt['reason']) ?></p>
                                    </div>
                                    <span class="rounded-md bg-slate-700 px-2 py-1 text-xs text-white"><?= e(appointment_status_label($appt['status'])) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php elseif ($tab === 'payments'): ?>
            <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
                <h2 class="text-base font-semibold text-white">Historique des paiements</h2>
                <div class="mt-4 space-y-3">
                    <?php if (!$payments): ?>
                        <p class="text-sm text-slate-400">Aucun paiement enregistré.</p>
                    <?php else: ?>
                        <?php foreach ($payments as $payment): ?>
                            <div class="rounded-xl bg-slate-800/70 p-4">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-semibold text-white"><?= e($payment['received_at']) ?></p>
                                        <p class="text-xs text-slate-400"><?= e($payment['reference']) ?></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-white"><?= e(format_tnd($payment['amount'])) ?></p>
                                        <?php if (!empty($payment['receipt_number'])): ?>
                                            <a class="text-xs font-semibold text-indigo-300 hover:text-indigo-200" href="r=payments-receipt&id=<?= e($payment['id']) ?>">Imprimer reçu</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php elseif ($tab === 'notes'): ?>
            <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
                <h2 class="text-base font-semibold text-white">Notes cliniques</h2>
                <form method="post" action="r=notes-create" class="mt-4 space-y-3">
                    <?= csrf_field() ?>
                    <input type="hidden" name="patient_id" value="<?= e($patient['id']) ?>" />
                    <label for="patient-note-text" class="sr-only">Note clinique</label>
                    <textarea id="patient-note-text" name="note_text" rows="4" class="w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500" placeholder="Ajouter une note clinique..."></textarea>
                    <button class="inline-flex items-center rounded-md bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Ajouter la note</button>
                </form>

                <div class="mt-6 space-y-4">
                    <?php if (!$notes): ?>
                        <p class="text-sm text-slate-400">Aucune note disponible.</p>
                    <?php else: ?>
                        <?php foreach ($notes as $note): ?>
                            <div class="rounded-xl bg-slate-800/70 p-4">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs text-slate-400"><?= e(date('d/m/Y H:i', strtotime($note['created_at']))) ?></p>
                                    <div class="flex items-center gap-3">
                                        <a class="text-xs font-semibold text-indigo-300 hover:text-indigo-200" href="r=patient&id=<?= e($patient['id']) ?>&tab=notes&edit_note=<?= e($note['id']) ?>">Modifier</a>
                                        <form method="post" action="r=notes-delete" onsubmit="return confirm('Supprimer cette note') ">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="note_id" value="<?= e($note['id']) ?>" />
                                            <input type="hidden" name="patient_id" value="<?= e($patient['id']) ?>" />
                                            <button class="text-xs font-semibold text-rose-300 hover:text-rose-200">Supprimer</button>
                                        </form>
                                    </div>
                                </div>
                                <?php if ($editNoteId === (int)$note['id']): ?>
                                    <form method="post" action="r=notes-edit" class="mt-3 space-y-3">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="note_id" value="<?= e($note['id']) ?>" />
                                        <input type="hidden" name="patient_id" value="<?= e($patient['id']) ?>" />
                                        <label for="note-text-<?= e($note['id']) ?>" class="sr-only">Modifier la note clinique</label>
                                        <textarea id="note-text-<?= e($note['id']) ?>" name="note_text" rows="4" class="w-full rounded-md border-0 bg-slate-900/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500"><?= e($note['note_text']) ?></textarea>
                                        <button class="inline-flex items-center rounded-md bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Enregistrer</button>
                                    </form>
                                <?php else: ?>
                                    <p class="mt-3 text-sm text-slate-200 whitespace-pre-line"><?= e($note['note_text']) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
                <h2 class="text-base font-semibold text-white">Documents</h2>
                <form method="post" enctype="multipart/form-data" action="r=documents-upload" class="mt-4 space-y-3">
                    <?= csrf_field() ?>
                    <input type="hidden" name="patient_id" value="<?= e($patient['id']) ?>" />
                    <label for="patient-document-file" class="sr-only">Document</label>
                    <input id="patient-document-file" type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" required class="block w-full text-sm text-slate-200 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-500 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-400" />
                    <label for="patient-document-note" class="sr-only">Note du document</label>
                    <input id="patient-document-note" type="text" name="note" placeholder="Note (optionnel) " class="w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500" />
                    <button class="inline-flex items-center rounded-md bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Uploader</button>
                </form>

                <div class="mt-6 space-y-3">
                    <?php if (!$documents): ?>
                        <p class="text-sm text-slate-400">Aucun document disponible.</p>
                    <?php else: ?>
                        <?php foreach ($documents as $doc): ?>
                            <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-slate-800/70 p-4">
                                <div>
                                    <p class="text-sm font-semibold text-white"><?= e($doc['file_name']) ?></p>
                                    <p class="text-xs text-slate-400"><?= e($doc['note']) ?></p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <a class="text-xs font-semibold text-indigo-300 hover:text-indigo-200" href="r=documents-download&id=<?= e($doc['id']) ?>">Télécharger</a>
                                    <form method="post" action="r=documents-delete" onsubmit="return confirm('Supprimer ce document') ">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="doc_id" value="<?= e($doc['id']) ?>" />
                                        <input type="hidden" name="patient_id" value="<?= e($patient['id']) ?>" />
                                        <button class="text-xs font-semibold text-rose-300 hover:text-rose-200">Supprimer</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
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
                    <dd class="font-semibold text-white"><?= e($lastVisit adate('d/m/Y', strtotime($lastVisit)) ?? : '—') ?></dd>
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
>