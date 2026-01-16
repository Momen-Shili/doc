<?php
$title = 'Tableau de bord';
ob_start();
$statusColors = [
  'PLANIFIE' => 'bg-indigo-500/15 text-indigo-200 ring-indigo-500/30',
  'CONFIRME' => 'bg-emerald-500/15 text-emerald-200 ring-emerald-500/30',
  'TERMINE' => 'bg-slate-500/20 text-slate-200 ring-slate-500/30',
  'ANNULE' => 'bg-rose-500/15 text-rose-200 ring-rose-500/30',
  'NO_SHOW' => 'bg-amber-500/15 text-amber-200 ring-amber-500/30',
];
$views = ['day' => 'Jour', 'week' => 'Semaine', 'month' => 'Mois'];
?>
<div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
  <div class="xl:col-span-8 space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-lg font-semibold text-white">Bienvenue, Dr. <?= e($doctor['full_name'] ?? '')    ?></h1>
        <p class="text-sm text-slate-300"><?= e(date('d/m/Y'))    ?> Â· Vue dâ€™ensemble de votre cabinet</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <a class="inline-flex items-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400" href="?r=patients-create">Ajouter un patient</a>
        <a class="inline-flex items-center rounded-md bg-slate-800 px-3 py-2 text-sm font-semibold text-slate-100 ring-1 ring-white/10 hover:bg-slate-700" href="?r=appointments-create">Nouveau rendez-vous</a>
      </div>
    </div>

    <div class="rounded-2xl bg-gradient-to-r from-indigo-600/80 to-indigo-500/70 p-6 text-white shadow-lg">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 class="text-xl font-semibold">Bonjour <?= e($doctor['full_name'] ?? '')    ?></h2>
          <p class="mt-1 text-sm text-indigo-100">Planifiez votre journée et suivez vos revenus en temps réel.</p>
        </div>
        <div class="flex items-center gap-4 text-sm text-indigo-100">
          <div>
            <div class="text-xs uppercase tracking-wide">Revenus aujourdâ€™hui</div>
            <div class="text-lg font-semibold"><?= e(format_tnd($todayRevenue))    ?></div>
          </div>
          <div class="h-10 w-px bg-indigo-200/40"></div>
          <div>
            <div class="text-xs uppercase tracking-wide">Rendez-vous</div>
            <div class="text-lg font-semibold"><?= e($appointmentsToday)    ?></div>
          </div>
        </div>
      </div>
    </div>

    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
      <div class="rounded-2xl bg-slate-900/60 p-4 ring-1 ring-white/10">
        <dt class="text-xs uppercase tracking-wide text-slate-400">Cash aujourdâ€™hui</dt>
        <dd class="mt-2 text-lg font-semibold text-white"><?= e(format_tnd($todayRevenue))    ?></dd>
      </div>
      <div class="rounded-2xl bg-slate-900/60 p-4 ring-1 ring-white/10">
        <dt class="text-xs uppercase tracking-wide text-slate-400">Cash du mois</dt>
        <dd class="mt-2 text-lg font-semibold text-white"><?= e(format_tnd($monthRevenue))    ?></dd>
      </div>
      <div class="rounded-2xl bg-slate-900/60 p-4 ring-1 ring-white/10">
        <dt class="text-xs uppercase tracking-wide text-slate-400">Annulations/Absents (7j)</dt>
        <dd class="mt-2 text-lg font-semibold text-white"><?= e($cancellationsWeek)    ?></dd>
      </div>
      <div class="rounded-2xl bg-slate-900/60 p-4 ring-1 ring-white/10">
        <dt class="text-xs uppercase tracking-wide text-slate-400">Nouveaux patients (mois)</dt>
        <dd class="mt-2 text-lg font-semibold text-white"><?= e($newPatientsMonth)    ?></dd>
      </div>
      <div class="rounded-2xl bg-slate-900/60 p-4 ring-1 ring-white/10">
        <dt class="text-xs uppercase tracking-wide text-slate-400">Ticket moyen</dt>
        <dd class="mt-2 text-lg font-semibold text-white"><?= e(format_tnd($avgTicket))    ?></dd>
      </div>
    </dl>

    <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 class="text-base font-semibold text-white">Agenda</h2>
          <p class="text-sm text-slate-400">Planning des rendez-vous</p>
        </div>
        <div class="flex items-center gap-2">
          <?php foreach ($views as $key => $label): ?>
            <a class="rounded-md px-3 py-2 text-sm font-semibold <?= $view === $key ? 'bg-indigo-500 text-white' : 'bg-slate-800 text-slate-200 hover:bg-slate-700'    ?>" href="?r=dashboard&view=<?= e($key)    ?>&date=<?= e($date)    ?>">
              <?= e($label)    ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="mt-6">
        <div class="flex items-center justify-between text-sm text-slate-400">
          <div><?= e($rangeStart)    ?> â†’ <?= e($rangeEnd)    ?></div>
          <div class="flex gap-2">
            <a class="text-slate-300 hover:text-white" aria-label="Période précédente" href="?r=dashboard&view=<?= e($view)    ?>&date=<?= e(date('Y-m-d', strtotime($date . ' -1 day')))    ?>">&larr;</a>
            <a class="text-slate-300 hover:text-white" aria-label="Période suivante" href="?r=dashboard&view=<?= e($view)    ?>&date=<?= e(date('Y-m-d', strtotime($date . ' +1 day')))    ?>">&rarr;</a>
          </div>
        </div>

        <?php if (empty($appointmentsByDate)): ?>
          <div class="mt-6 rounded-xl border border-dashed border-slate-700 p-6 text-center text-sm text-slate-400">Aucun rendez-vous sur cette période.</div>
        <?php else: ?>
          <div class="mt-6 space-y-4">
            <?php foreach ($appointmentsByDate as $day => $items): ?>
              <div>
                <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-400"><?= e($day)    ?></h3>
                <div class="mt-3 space-y-3">
                  <?php foreach ($items as $appt): ?>
                    <?php $badge = $statusColors[$appt['status']] ?? 'bg-slate-500/20 text-slate-200 ring-slate-500/30'; ?>
                    <?php
                    $services = $servicesByAppointment[$appt['id']] ?? [];
                    $serviceTotal = 0;
                    foreach ($services as $srv) {
                      $serviceTotal += (float)$srv['default_price'] * (int)$srv['qty'];
                    }
                    ?>
                    <button type="button" class="w-full rounded-xl bg-slate-800/70 p-4 text-left ring-1 ring-white/5 hover:ring-indigo-400/40" data-drawer="open" aria-controls="appointment-drawer" data-appointment-id="<?= e($appt['id'])    ?>" data-patient="<?= e($appt['patient_name'])    ?>" data-time="<?= e(date('H:i', strtotime($appt['starts_at'])))    ?> - <?= e(date('H:i', strtotime($appt['ends_at'])))    ?>" data-status="<?= e(appointment_status_label($appt['status']))    ?>" data-reason="<?= e($appt['reason'])    ?>" data-notes="<?= e($appt['notes'])    ?>" data-services="<?= e(implode(', ', array_map(function ($s) { return $s['name']; }, $services)))    ?>" data-total="<?= e(format_tnd($serviceTotal))    ?>">
                      <div class="flex items-center justify-between">
                        <div>
                          <div class="text-sm font-semibold text-white"><?= e($appt['patient_name'])    ?></div>
                          <div class="text-xs text-slate-400"><?= e(date('H:i', strtotime($appt['starts_at'])))    ?> â€“ <?= e(date('H:i', strtotime($appt['ends_at'])))    ?></div>
                          <?php if ($services): ?>
                            <div class="mt-2 flex flex-wrap gap-2">
                              <?php foreach ($services as $srv): ?>
                                <span class="rounded-full bg-slate-700 px-2 py-1 text-xs text-slate-200"><?= e($srv['name'])    ?> x<?= e($srv['qty'])    ?></span>
                              <?php endforeach; ?>
                            </div>
                          <?php endif; ?>
                        </div>
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset <?= e($badge)    ?>">
                          <?= e(appointment_status_label($appt['status']))    ?>
                        </span>
                      </div>
                    </button>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-5">
      <a class="rounded-2xl bg-slate-900/60 p-4 text-sm font-semibold text-white ring-1 ring-white/10 hover:ring-indigo-400/40" href="?r=patients-create">Ajouter un patient</a>
      <a class="rounded-2xl bg-slate-900/60 p-4 text-sm font-semibold text-white ring-1 ring-white/10 hover:ring-indigo-400/40" href="?r=appointments-create">Nouveau rendez-vous</a>
      <a class="rounded-2xl bg-slate-900/60 p-4 text-sm font-semibold text-white ring-1 ring-white/10 hover:ring-indigo-400/40" href="?r=payments-create">Encaisser un paiement</a>
      <a class="rounded-2xl bg-slate-900/60 p-4 text-sm font-semibold text-white ring-1 ring-white/10 hover:ring-indigo-400/40" href="?r=import">Importer CSV</a>
      <a class="rounded-2xl bg-slate-900/60 p-4 text-sm font-semibold text-white ring-1 ring-white/10 hover:ring-indigo-400/40" href="?r=patients">Ajouter une note clinique</a>
    </div>
  </div>

  <div class="xl:col-span-4 space-y-6">
    <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-xs uppercase tracking-wide text-slate-400">Mon profil</p>
          <p class="mt-2 text-base font-semibold text-white"><?= e($doctor['full_name'] ?? '')    ?></p>
          <p class="text-sm text-slate-400"><?= e($doctor['specialty'] ?? 'Médecin')    ?></p>
        </div>
        <div class="h-12 w-12 rounded-full bg-indigo-500/30 flex items-center justify-center text-indigo-200">
          <span class="text-sm font-semibold">Dr</span>
        </div>
      </div>
      <div class="mt-6">
        <p class="text-xs uppercase tracking-wide text-slate-400">Revenus 7 jours</p>
        <div class="mt-3 flex items-end gap-1 h-20">
          <?php
          $totalsByDate = [];
          foreach ($miniRevenue as $row) {
            $totalsByDate[$row['received_at']] = (float)$row['total'];
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
    </div>

    <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
      <h2 class="text-base font-semibold text-white">Derniers patients</h2>
      <p class="mt-1 text-sm text-slate-400">Nouveaux patients enregistrés.</p>
      <div class="mt-4 space-y-3">
        <?php if (!$recentPatients): ?>
          <p class="text-sm text-slate-400">Aucun patient récent.</p>
        <?php else: ?>
          <?php foreach ($recentPatients as $patient): ?>
            <div class="flex items-center justify-between rounded-lg bg-slate-800/70 p-3">
              <div>
                <div class="text-sm font-semibold text-white"><?= e($patient['full_name'])    ?></div>
                <div class="text-xs text-slate-400">ID #<?= e($patient['id'])    ?></div>
              </div>
              <a class="text-xs font-semibold text-indigo-300 hover:text-indigo-200" href="?r=patients-edit&id=<?= e($patient['id']) ?>">Voir</a>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
      <h2 class="text-base font-semibold text-white">Actions rapides</h2>
      <div class="mt-4 grid grid-cols-1 gap-3">
        <a class="rounded-lg bg-indigo-500/20 px-4 py-3 text-sm font-semibold text-indigo-100 hover:bg-indigo-500/30" href="?r=patients-create">Ajouter un patient</a>
        <a class="rounded-lg bg-slate-800/70 px-4 py-3 text-sm font-semibold text-slate-100 hover:bg-slate-700" href="?r=appointments-create">Nouveau rendez-vous</a>
        <a class="rounded-lg bg-slate-800/70 px-4 py-3 text-sm font-semibold text-slate-100 hover:bg-slate-700" href="?r=payments-create">Encaisser un paiement</a>
        <a class="rounded-lg bg-slate-800/70 px-4 py-3 text-sm font-semibold text-slate-100 hover:bg-slate-700" href="?r=import">Importer CSV</a>
        <a class="rounded-lg bg-slate-800/70 px-4 py-3 text-sm font-semibold text-slate-100 hover:bg-slate-700" href="?r=patients">Ajouter une note clinique</a>
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
            <div class="flex items-center justify-between rounded-lg bg-slate-800/70 p-3">
              <div>
                <div class="text-sm font-semibold text-white"><?= e($reminder['patient_name'])    ?></div>
                <div class="text-xs text-slate-400"><?= e(date('d/m/Y ? H : i', strtotime($reminder['starts_at'])))    ?></div>
              </div>
              <a class="text-xs font-semibold text-indigo-300 hover:text-indigo-200" href="?r=reminders-send&appointment_id=<?= e($reminder['id'])    ?>">Préparer</a>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div id="appointment-drawer" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="drawer-patient" aria-describedby="drawer-time" aria-hidden="true">
  <div class="absolute inset-0 bg-black/60" data-drawer="close"></div>
  <div id="appointment-drawer-panel" class="absolute right-0 top-0 h-full w-full max-w-md bg-slate-950 p-6 shadow-xl ring-1 ring-white/10" tabindex="-1">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-base font-semibold text-white" id="drawer-patient">Rendez-vous</h2>
        <p class="text-sm text-slate-400" id="drawer-time"></p>
      </div>
      <button class="text-slate-400 hover:text-white" data-drawer="close" aria-label="Fermer le panneau">âœ•</button>
    </div>
    <div class="mt-6 space-y-4">
      <div>
        <p class="text-xs uppercase tracking-wide text-slate-500">Statut</p>
        <p class="mt-1 text-sm font-semibold text-white" id="drawer-status"></p>
      </div>
      <div>
        <p class="text-xs uppercase tracking-wide text-slate-500">Actes / Services</p>
        <p class="mt-1 text-sm text-slate-200" id="drawer-services"></p>
      </div>
      <div>
        <p class="text-xs uppercase tracking-wide text-slate-500">Total suggéré</p>
        <p class="mt-1 text-sm text-slate-200" id="drawer-total"></p>
      </div>
      <div>
        <p class="text-xs uppercase tracking-wide text-slate-500">Motif</p>
        <p class="mt-1 text-sm text-slate-200" id="drawer-reason"></p>
      </div>
      <div>
        <p class="text-xs uppercase tracking-wide text-slate-500">Notes</p>
        <p class="mt-1 text-sm text-slate-200" id="drawer-notes"></p>
      </div>
      <a id="drawer-reminder-link" class="inline-flex items-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400" href="#">Envoyer un rappel</a>
      <a class="inline-flex items-center rounded-md bg-slate-800 px-3 py-2 text-sm font-semibold text-slate-100 ring-1 ring-white/10 hover:bg-slate-700" href="?r=appointments">Voir tous les rendez-vous</a>
    </div>
  </div>
</div>

<script>
var drawer = document.getElementById('appointment-drawer');
var drawerPanel = document.getElementById('appointment-drawer-panel');
var lastFocused = null;

function openDrawer(trigger) {
  document.getElementById('drawer-patient').textContent = trigger.dataset.patient || 'Rendez-vous';
  document.getElementById('drawer-time').textContent = trigger.dataset.time || '';
  document.getElementById('drawer-status').textContent = trigger.dataset.status || '';
  document.getElementById('drawer-services').textContent = trigger.dataset.services || 'â€”';
  document.getElementById('drawer-total').textContent = trigger.dataset.total || '-';
  document.getElementById('drawer-reason').textContent = trigger.dataset.reason || '-';
  document.getElementById('drawer-notes').textContent = trigger.dataset.notes || '-';
  var reminderLink = document.getElementById('drawer-reminder-link');
  if (reminderLink && trigger.dataset.appointmentId) {
    reminderLink.href = 'r=reminders-send&appointment_id=' + encodeURIComponent(trigger.dataset.appointmentId);
  }
  drawer.classList.remove('hidden');
  drawer.setAttribute('aria-hidden', 'false');
  lastFocused = trigger;
  var closeBtn = drawer.querySelector('[data-drawer="close"]');
  if (closeBtn) {
    closeBtn.focus();
  } else if (drawerPanel) {
    drawerPanel.focus();
  }
}

function closeDrawer() {
  drawer.classList.add('hidden');
  drawer.setAttribute('aria-hidden', 'true');
  if (lastFocused) {
    lastFocused.focus();
  }
}

document.querySelectorAll('[data-drawer="open"]').forEach(function (btn) {
  btn.addEventListener('click', function () {
    openDrawer(this);
  });
});

document.querySelectorAll('[data-drawer="close"]').forEach(function (btn) {
  btn.addEventListener('click', closeDrawer);
});

document.addEventListener('keydown', function (event) {
  if (drawer.classList.contains('hidden')) return;
  if (event.key === 'Escape') {
    event.preventDefault();
    closeDrawer();
    return;
  }
  if (event.key !== 'Tab') return;
  var focusable = drawer.querySelectorAll('a[href], button, input, select, textarea, [tabindex]:not([tabindex="-1"])');
  if (!focusable.length) return;
  var first = focusable[0];
  var last = focusable[focusable.length - 1];
  if (event.shiftKey && document.activeElement === first) {
    event.preventDefault();
    last.focus();
  } else if (!event.shiftKey && document.activeElement === last) {
    event.preventDefault();
    first.focus();
  }
});
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
