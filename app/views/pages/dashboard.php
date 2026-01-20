<?php
$title = 'Tableau de bord';
ob_start();
$views = ['day' => 'Jour', 'week' => 'Semaine', 'month' => 'Mois'];
$notifConfig = $notifConfig ?? ['email' => false, 'sms' => false, 'whatsapp' => false];
$quickActions = [
    ['label' => 'Ajouter un patient', 'href' => '?r=patients-create'],
    ['label' => 'Nouveau rendez-vous', 'href' => '?r=appointments-create'],
    ['label' => 'Encaisser un paiement', 'href' => '?r=payments-create'],
    ['label' => 'Importer CSV', 'href' => '?r=import'],
    ['label' => 'Ajouter une note clinique', 'href' => '?r=patients'],
];
?>
<div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
  <div class="xl:col-span-8 space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-semibold text-white">Tableau de bord</h1>
        <p class="text-sm text-slate-300">Aujourd'hui, <?= e(date('d/m/Y')) ?></p>
      </div>
      <div class="relative">
        <details class="group">
          <summary class="inline-flex cursor-pointer items-center gap-2 rounded-md bg-slate-800 px-3 py-2 text-sm font-semibold text-slate-100 ring-1 ring-white/10 hover:bg-slate-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
            Actions rapides
            <span aria-hidden="true">▾</span>
          </summary>
          <div class="absolute right-0 z-20 mt-2 w-56 rounded-xl bg-slate-900 p-2 shadow-lg ring-1 ring-white/10">
            <?php foreach ($quickActions as $action): ?>
              <a class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-200 hover:bg-slate-800/70" href="<?= e($action['href']) ?>">
                <?= e($action['label']) ?>
              </a>
            <?php endforeach; ?>
          </div>
        </details>
      </div>
    </div>

    <div class="rounded-2xl bg-gradient-to-r from-indigo-600/80 to-indigo-500/70 p-6 text-white shadow-lg">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 class="text-xl font-semibold">Bienvenue, Dr. <?= e($doctor['full_name'] ?? '') ?></h2>
          <p class="mt-1 text-sm text-indigo-100">Suivez vos rendez-vous et votre encaissement en temps réel.</p>
        </div>
        <div class="flex items-center gap-6 text-sm text-indigo-100">
          <div>
            <div class="text-xs uppercase tracking-wide">Cash aujourd'hui</div>
            <div class="text-lg font-semibold"><?= e(format_tnd($todayRevenue)) ?></div>
          </div>
          <div class="h-10 w-px bg-indigo-200/40"></div>
          <div>
            <div class="text-xs uppercase tracking-wide">Rendez-vous aujourd'hui</div>
            <div class="text-lg font-semibold"><?= e($appointmentsToday) ?></div>
          </div>
        </div>
      </div>
    </div>

    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
      <div class="rounded-2xl bg-accent-1-soft p-4 ring-1 ring-white/10">
        <div class="flex items-center justify-between">
          <div>
            <dt class="text-xs uppercase tracking-wide text-slate-400">Cash aujourd'hui</dt>
            <dd class="mt-2 text-lg font-semibold text-white"><?= e(format_tnd($todayRevenue)) ?></dd>
            <p class="mt-1 text-xs text-slate-400">Encaissements du jour</p>
          </div>
          <div class="flex h-9 w-9 items-center justify-center rounded-full bg-accent-1-soft text-accent-1">TND</div>
        </div>
      </div>
      <div class="rounded-2xl bg-accent-2-soft p-4 ring-1 ring-white/10">
        <div class="flex items-center justify-between">
          <div>
            <dt class="text-xs uppercase tracking-wide text-slate-400">Cash du mois</dt>
            <dd class="mt-2 text-lg font-semibold text-white"><?= e(format_tnd($monthRevenue)) ?></dd>
            <p class="mt-1 text-xs text-slate-400">Jusqu'à aujourd'hui</p>
          </div>
          <div class="flex h-9 w-9 items-center justify-center rounded-full bg-accent-2-soft text-accent-2">M</div>
        </div>
      </div>
      <div class="rounded-2xl bg-accent-3-soft p-4 ring-1 ring-white/10">
        <div class="flex items-center justify-between">
          <div>
            <dt class="text-xs uppercase tracking-wide text-slate-400">Annulations / absents</dt>
            <dd class="mt-2 text-lg font-semibold text-white"><?= e($cancellationsWeek) ?></dd>
            <p class="mt-1 text-xs text-slate-400">7 derniers jours</p>
          </div>
          <div class="flex h-9 w-9 items-center justify-center rounded-full bg-accent-3-soft text-accent-3">!</div>
        </div>
      </div>
      <div class="rounded-2xl bg-accent-4-soft p-4 ring-1 ring-white/10">
        <div class="flex items-center justify-between">
          <div>
            <dt class="text-xs uppercase tracking-wide text-slate-400">Nouveaux patients</dt>
            <dd class="mt-2 text-lg font-semibold text-white"><?= e($newPatientsMonth) ?></dd>
            <p class="mt-1 text-xs text-slate-400">Ce mois</p>
          </div>
          <div class="flex h-9 w-9 items-center justify-center rounded-full bg-accent-4-soft text-accent-4">+</div>
        </div>
      </div>
      <div class="rounded-2xl bg-accent-5-soft p-4 ring-1 ring-white/10">
        <div class="flex items-center justify-between">
          <div>
            <dt class="text-xs uppercase tracking-wide text-slate-400">Ticket moyen</dt>
            <dd class="mt-2 text-lg font-semibold text-white"><?= e(format_tnd($avgTicket)) ?></dd>
            <p class="mt-1 text-xs text-slate-400">Moyenne mensuelle</p>
          </div>
          <div class="flex h-9 w-9 items-center justify-center rounded-full bg-accent-5-soft text-accent-5">AVG</div>
        </div>
      </div>
    </dl>
    <?php require __DIR__ . '/../partials/calendar.php'; ?>

    <details class="rounded-2xl bg-slate-900/60 p-5 ring-1 ring-white/10">
      <summary class="cursor-pointer text-sm font-semibold text-slate-200">Détails complémentaires</summary>
      <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div class="rounded-xl bg-slate-800/70 p-4">
          <p class="text-xs uppercase tracking-wide text-slate-400">Revenus 7 jours</p>
          <div class="mt-3 flex h-20 items-end gap-1">
            <?php
            $totalsByDate = [];
            foreach ($miniRevenue as $row) {
                $totalsByDate[$row['day']] = (float)$row['total'];
            }
            for ($i = 6; $i >= 0; $i--) {
                $d = date('Y-m-d', strtotime('-' . $i . ' days'));
                $value = $totalsByDate[$d] ?? 0;
                $height = $value > 0 ? max(12, min(80, (int)($value / 10))) : 8;
                echo '<div class="flex-1"><div class="mx-auto w-3 rounded-full bg-indigo-500/70" style="height:' . $height . 'px"></div></div>';
            }
            ?>
          </div>
        </div>
        <div class="rounded-xl bg-slate-800/70 p-4">
          <h3 class="text-sm font-semibold text-white">Derniers patients</h3>
          <p class="mt-1 text-xs text-slate-400">Nouveaux patients enregistrés.</p>
          <div class="mt-3 space-y-2">
            <?php if (!$recentPatients): ?>
              <p class="text-sm text-slate-400">Aucun patient récent.</p>
            <?php else: ?>
              <?php foreach ($recentPatients as $patient): ?>
                <div class="flex items-center justify-between rounded-lg bg-slate-900/60 p-2">
                  <div>
                    <div class="text-sm font-semibold text-white"><?= e($patient['full_name']) ?></div>
                    <div class="text-xs text-slate-400">ID #<?= e($patient['id']) ?></div>
                  </div>
                  <a class="text-xs font-semibold text-indigo-300 hover:text-indigo-200" href="?r=patients-edit&id=<?= e($patient['id']) ?>">Voir</a>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </details>
  </div>

  <div class="xl:col-span-4 space-y-6">
    <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-xs uppercase tracking-wide text-slate-400">Mon profil</p>
          <p class="mt-2 text-base font-semibold text-white"><?= e($doctor['full_name'] ?? '') ?></p>
          <p class="text-sm text-slate-400"><?= e($doctor['specialty'] ?? 'Médecin') ?></p>
        </div>
        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-500/30 text-indigo-200">
          <span class="text-sm font-semibold">Dr</span>
        </div>
      </div>
      <div class="mt-5 space-y-2 text-sm text-slate-300">
        <div class="flex items-center justify-between">
          <span>Rendez-vous aujourd'hui</span>
          <span class="font-semibold text-white"><?= e($appointmentsToday) ?></span>
        </div>
        <div class="flex items-center justify-between">
          <span>Cash aujourd'hui</span>
          <span class="font-semibold text-white"><?= e(format_tnd($todayRevenue)) ?></span>
        </div>
      </div>
    </div>

    <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-base font-semibold text-white">Rappels à envoyer</h2>
          <p class="mt-1 text-sm text-slate-400">Rendez-vous dans les prochaines 24h.</p>
        </div>
        <a class="text-sm font-semibold text-indigo-300 hover:text-indigo-200" href="?r=reminders-log">Journal</a>
      </div>
      <div class="mt-4 space-y-3">
        <?php if (empty($remindersDue)): ?>
          <p class="text-sm text-slate-400">Aucun rappel à préparer.</p>
        <?php else: ?>
          <?php foreach ($remindersDue as $reminder): ?>
            <?php
              $canEmail = $notifConfig['email'] && !empty($reminder['consent_email']) && !empty($reminder['email']);
              $canSms = $notifConfig['sms'] && !empty($reminder['consent_sms']) && !empty($reminder['phone']) && normalize_tn_phone($reminder['phone']);
              $waPhone = $reminder['whatsapp_phone'] ?: $reminder['phone'];
              $canWhatsapp = $notifConfig['whatsapp'] && !empty($reminder['consent_whatsapp']) && !empty($waPhone) && normalize_tn_phone($waPhone);
            ?>
            <div class="rounded-lg bg-slate-800/70 p-3">
              <div class="flex items-center justify-between">
                <div>
                  <div class="text-sm font-semibold text-white"><?= e($reminder['patient_name']) ?></div>
                  <div class="text-xs text-slate-400"><?= e(date('d/m/Y H:i', strtotime($reminder['starts_at']))) ?></div>
                </div>
                <a class="text-xs font-semibold text-indigo-300 hover:text-indigo-200" href="?r=reminders-send&appointment_id=<?= e($reminder['id']) ?>">Préparer</a>
              </div>
              <div class="mt-3 flex flex-wrap gap-2">
                <?php if ($canEmail): ?>
                  <a class="rounded-md bg-slate-700 px-2 py-1 text-xs font-semibold text-slate-100" href="?r=reminders-send&appointment_id=<?= e($reminder['id']) ?>&channel=EMAIL">Email</a>
                <?php endif; ?>
                <?php if ($canSms): ?>
                  <a class="rounded-md bg-slate-700 px-2 py-1 text-xs font-semibold text-slate-100" href="?r=reminders-send&appointment_id=<?= e($reminder['id']) ?>&channel=SMS">SMS</a>
                <?php endif; ?>
                <?php if ($canWhatsapp): ?>
                  <a class="rounded-md bg-slate-700 px-2 py-1 text-xs font-semibold text-slate-100" href="?r=reminders-send&appointment_id=<?= e($reminder['id']) ?>&channel=WHATSAPP">WhatsApp</a>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
