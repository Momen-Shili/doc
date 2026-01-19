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

    <div id="calendar-card" class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10" data-mode="<?= e($view) ?>" data-date="<?= e($date) ?>">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <h2 class="text-base font-semibold text-white">Agenda</h2>
          <p class="text-sm text-slate-400">Planning des rendez-vous</p>
        </div>
        <div class="flex items-center gap-2">
          <?php foreach ($views as $key => $label): ?>
            <button type="button" class="rounded-md px-3 py-2 text-sm font-semibold bg-slate-800 text-slate-200 hover:bg-slate-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500" data-calendar-view="<?= e($key) ?>">
              <?= e($label) ?>
            </button>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-slate-300">
        <div class="inline-flex items-center gap-2 rounded-lg bg-slate-800/70 px-3 py-2">
          <button id="calendar-prev" type="button" class="text-slate-200 hover:text-white" aria-label="Période précédente">&larr;</button>
          <input id="calendar-date" type="date" class="rounded-md border-0 bg-transparent text-sm text-slate-100 focus-visible:outline-none" value="<?= e($date) ?>" />
          <button id="calendar-next" type="button" class="text-slate-200 hover:text-white" aria-label="Période suivante">&rarr;</button>
        </div>
        <div class="text-xs text-slate-400" id="calendar-range"></div>
        <div class="flex flex-wrap items-center gap-3 text-xs text-slate-400">
          <span class="inline-flex items-center gap-2"><span class="status-dot status-scheduled"></span>Planifié</span>
          <span class="inline-flex items-center gap-2"><span class="status-dot status-confirmed"></span>Confirmé</span>
          <span class="inline-flex items-center gap-2"><span class="status-dot status-completed"></span>Terminé</span>
          <span class="inline-flex items-center gap-2"><span class="status-dot status-cancelled"></span>Annulé</span>
          <span class="inline-flex items-center gap-2"><span class="status-dot status-noshow"></span>Absent</span>
        </div>
      </div>

      <div id="calendar-loading" class="mt-5 space-y-3">
        <div class="h-16 rounded-xl bg-slate-800/70"></div>
        <div class="h-16 rounded-xl bg-slate-800/70"></div>
        <div class="h-16 rounded-xl bg-slate-800/70"></div>
      </div>
      <div id="calendar-container" class="mt-5 space-y-4" aria-live="polite"></div>
      <div id="calendar-empty" class="mt-6 hidden rounded-xl border border-dashed border-slate-700 p-6 text-center text-sm text-slate-400">
        Aucun rendez-vous sur cette période.
      </div>
      <div id="calendar-toast" class="mt-4 hidden rounded-lg border border-rose-500/30 bg-rose-500/10 px-3 py-2 text-sm text-rose-200" role="alert"></div>
    </div>

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

<div id="appointment-drawer" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="drawer-patient" aria-describedby="drawer-time" aria-hidden="true">
  <div class="absolute inset-0 bg-black/60" data-drawer="close"></div>
  <div id="appointment-drawer-panel" class="absolute right-0 top-0 h-full w-full max-w-md bg-slate-950 p-6 shadow-xl ring-1 ring-white/10" tabindex="-1">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-base font-semibold text-white" id="drawer-patient">Rendez-vous</h2>
        <p class="text-sm text-slate-400" id="drawer-time"></p>
      </div>
      <button class="text-slate-400 hover:text-white" data-drawer="close" aria-label="Fermer le panneau">X</button>
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
  document.getElementById('drawer-services').textContent = trigger.dataset.services || '-';
  document.getElementById('drawer-total').textContent = trigger.dataset.total || '-';
  document.getElementById('drawer-reason').textContent = trigger.dataset.reason || '-';
  document.getElementById('drawer-notes').textContent = trigger.dataset.notes || '-';
  var reminderLink = document.getElementById('drawer-reminder-link');
  if (reminderLink && trigger.dataset.appointmentId) {
    reminderLink.href = '?r=reminders-send&appointment_id=' + encodeURIComponent(trigger.dataset.appointmentId);
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

document.addEventListener('click', function (event) {
  var trigger = event.target.closest('[data-appointment-card]');
  if (trigger) {
    openDrawer(trigger);
  }
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

var calendarCard = document.getElementById('calendar-card');
var calendarContainer = document.getElementById('calendar-container');
var calendarLoading = document.getElementById('calendar-loading');
var calendarEmpty = document.getElementById('calendar-empty');
var calendarRange = document.getElementById('calendar-range');
var calendarToast = document.getElementById('calendar-toast');
var dateInput = document.getElementById('calendar-date');
var prevBtn = document.getElementById('calendar-prev');
var nextBtn = document.getElementById('calendar-next');
var viewButtons = document.querySelectorAll('[data-calendar-view]');

var calendarState = {
  mode: calendarCard ? calendarCard.dataset.mode : 'day',
  date: calendarCard ? calendarCard.dataset.date : new Date().toISOString().slice(0, 10),
  firstLoad: true
};

function formatDate(date) {
  return date.toISOString().slice(0, 10);
}

function parseDate(value) {
  return new Date(value + 'T00:00:00');
}

function escapeHtml(value) {
  return String(value || '').replace(/[&<>"']/g, function (char) {
    return {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#39;'
    }[char];
  });
}

function setCalendarLoading(isLoading) {
  if (!calendarLoading || !calendarContainer) return;
  calendarLoading.classList.toggle('hidden', !isLoading);
  calendarContainer.classList.toggle('hidden', isLoading);
  if (calendarEmpty) {
    calendarEmpty.classList.add('hidden');
  }
}

function setCalendarToast(message) {
  if (!calendarToast) return;
  if (!message) {
    calendarToast.classList.add('hidden');
    calendarToast.textContent = '';
    return;
  }
  calendarToast.textContent = message;
  calendarToast.classList.remove('hidden');
}

function updateViewButtons() {
  viewButtons.forEach(function (btn) {
    var isActive = btn.dataset.calendarView === calendarState.mode;
    btn.classList.toggle('bg-indigo-500', isActive);
    btn.classList.toggle('text-white', isActive);
    if (isActive) {
      btn.classList.remove('bg-slate-800', 'text-slate-200');
    } else {
      btn.classList.add('bg-slate-800', 'text-slate-200');
    }
  });
}

function updateUrl() {
  var params = new URLSearchParams(window.location.search || '');
  params.set('r', 'dashboard');
  params.set('view', calendarState.mode);
  params.set('date', calendarState.date);
  var next = '?' + params.toString();
  window.history.replaceState({}, '', next);
}

function renderCalendar(data) {
  if (!calendarContainer) return;
  calendarContainer.innerHTML = '';
  var events = data.events || [];
  if (calendarRange) {
    calendarRange.textContent = data.range.start + ' - ' + data.range.end;
  }
  if (!events.length) {
    calendarEmpty.classList.remove('hidden');
    calendarContainer.classList.add('hidden');
    return;
  }
  calendarEmpty.classList.add('hidden');
  calendarContainer.classList.remove('hidden');

  var byDay = {};
  events.forEach(function (evt) {
    var dayKey = evt.starts_at.slice(0, 10);
    byDay[dayKey] = byDay[dayKey] || [];
    byDay[dayKey].push(evt);
  });

  var dayKeys = Object.keys(byDay).sort();
  var now = new Date((data.now || '').replace(' ', 'T'));
  var nowDay = formatDate(now);
  var markerElement = null;

  dayKeys.forEach(function (dayKey) {
    var section = document.createElement('div');
    var title = document.createElement('h3');
    title.className = 'text-xs font-semibold uppercase tracking-wide text-slate-400';
    title.textContent = dayKey;
    section.appendChild(title);

    var list = document.createElement('div');
    list.className = 'mt-3 space-y-3';

    var insertedMarker = false;
    byDay[dayKey].forEach(function (evt) {
      var startsAt = new Date(evt.starts_at.replace(' ', 'T'));
      if (!insertedMarker && (calendarState.mode === 'day' || calendarState.mode === 'week') && dayKey === nowDay && startsAt > now) {
        var marker = document.createElement('div');
        marker.className = 'rounded-lg border border-indigo-500/30 bg-indigo-500/10 px-3 py-2 text-xs font-semibold text-indigo-200';
        marker.textContent = 'Maintenant · ' + now.toTimeString().slice(0, 5);
        marker.id = 'calendar-now-marker';
        list.appendChild(marker);
        insertedMarker = true;
        markerElement = marker;
      }

      var button = document.createElement('button');
      button.type = 'button';
      button.className = 'w-full rounded-xl bg-slate-800/70 p-4 text-left ring-1 ring-white/5 hover:ring-indigo-400/40 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500';
      button.setAttribute('data-appointment-card', 'true');
      button.setAttribute('data-appointment-id', evt.id);
      button.setAttribute('data-patient', evt.patient_name || '');
      button.setAttribute('data-time', evt.starts_at.slice(11, 16) + ' - ' + evt.ends_at.slice(11, 16));
      button.setAttribute('data-status', evt.status_label || evt.status);
      button.setAttribute('data-reason', evt.reason || '');
      button.setAttribute('data-notes', evt.notes || '');
      button.setAttribute('data-services', (evt.service_labels || []).join(', ') || '-');
      button.setAttribute('data-total', evt.service_total_formatted ? evt.service_total_formatted : '0,000 TND');
      button.setAttribute('aria-label', 'Rendez-vous: ' + (evt.patient_name || '') + ' le ' + dayKey + ' à ' + evt.starts_at.slice(11, 16) + ', statut ' + (evt.status_label || evt.status));

      var badgeClass = evt.color_token || 'status-scheduled';
      var patientName = escapeHtml(evt.patient_name || '');
      var statusLabel = escapeHtml(evt.status_label || evt.status || '');
      var timeLabel = escapeHtml(evt.starts_at.slice(11, 16) + ' - ' + evt.ends_at.slice(11, 16));
      var serviceHtml = '';
      if (evt.service_labels && evt.service_labels.length) {
        serviceHtml = '<div class="mt-2 flex flex-wrap gap-2">' +
          evt.service_labels.map(function (label) {
            return '<span class="rounded-full bg-slate-700 px-2 py-1 text-xs text-slate-200">' + escapeHtml(label) + '</span>';
          }).join('') +
        '</div>';
      }

      button.innerHTML =
        '<div class="flex items-center justify-between">' +
          '<div>' +
            '<div class="text-sm font-semibold text-white">' + patientName + '</div>' +
            '<div class="text-xs text-slate-400">' + timeLabel + '</div>' +
            serviceHtml +
          '</div>' +
          '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ' + badgeClass + '">' + statusLabel + '</span>' +
        '</div>';

      list.appendChild(button);
    });

    if (!insertedMarker && (calendarState.mode === 'day' || calendarState.mode === 'week') && dayKey === nowDay) {
      var endMarker = document.createElement('div');
      endMarker.className = 'rounded-lg border border-indigo-500/30 bg-indigo-500/10 px-3 py-2 text-xs font-semibold text-indigo-200';
      endMarker.textContent = 'Maintenant · ' + now.toTimeString().slice(0, 5);
      endMarker.id = 'calendar-now-marker';
      list.appendChild(endMarker);
      markerElement = endMarker;
    }

    section.appendChild(list);
    calendarContainer.appendChild(section);
  });

  if (markerElement && calendarState.firstLoad) {
    markerElement.scrollIntoView({ block: 'center' });
  }
  calendarState.firstLoad = false;
}

function fetchCalendar() {
  if (!calendarCard) return;
  setCalendarToast('');
  setCalendarLoading(true);
  updateViewButtons();
  updateUrl();
  fetch('?r=calendar-events-json&mode=' + encodeURIComponent(calendarState.mode) + '&date=' + encodeURIComponent(calendarState.date), {
    headers: { 'Accept': 'application/json' }
  })
    .then(function (res) { return res.json(); })
    .then(function (data) {
      setCalendarLoading(false);
      renderCalendar(data || {});
    })
    .catch(function () {
      setCalendarLoading(false);
      setCalendarToast('Impossible de charger le calendrier. Réessayez.');
    });
}

function shiftCalendar(delta) {
  var current = parseDate(calendarState.date);
  if (calendarState.mode === 'week') {
    current.setDate(current.getDate() + delta * 7);
  } else if (calendarState.mode === 'month') {
    current.setMonth(current.getMonth() + delta);
  } else {
    current.setDate(current.getDate() + delta);
  }
  calendarState.date = formatDate(current);
  if (dateInput) {
    dateInput.value = calendarState.date;
  }
  fetchCalendar();
}

viewButtons.forEach(function (btn) {
  btn.addEventListener('click', function () {
    calendarState.mode = btn.dataset.calendarView;
    fetchCalendar();
  });
});

if (dateInput) {
  dateInput.addEventListener('change', function () {
    calendarState.date = dateInput.value;
    fetchCalendar();
  });
}

if (prevBtn) {
  prevBtn.addEventListener('click', function () { shiftCalendar(-1); });
}
if (nextBtn) {
  nextBtn.addEventListener('click', function () { shiftCalendar(1); });
}

var needsRefresh = localStorage.getItem('calendarRefresh') === '1';
if (needsRefresh) {
  localStorage.removeItem('calendarRefresh');
}
fetchCalendar();
setInterval(fetchCalendar, 30000);
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
