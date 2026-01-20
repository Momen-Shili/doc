<?php
$calendarMode = $view ?? 'month';
$calendarDate = $date ?? date('Y-m-d');
$calendarMode = in_array($calendarMode, ['day', 'week', 'month'], true) ? $calendarMode : 'month';
?>
<div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10 ui-calendar" data-calendar-mode="<?= e($calendarMode) ?>" data-calendar-date="<?= e($calendarDate) ?>">
  <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
      <h2 class="text-base font-semibold text-white">Agenda</h2>
      <p class="text-sm text-slate-400">Calendrier des rendez-vous</p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
      <button type="button" class="rounded-md bg-slate-800 px-3 py-2 text-sm font-semibold text-slate-100 hover:bg-slate-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500" data-calendar-nav="prev">&laquo; Prec</button>
      <button type="button" class="rounded-md bg-slate-800 px-3 py-2 text-sm font-semibold text-slate-100 hover:bg-slate-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500" data-calendar-nav="today">Aujourd'hui</button>
      <button type="button" class="rounded-md bg-slate-800 px-3 py-2 text-sm font-semibold text-slate-100 hover:bg-slate-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500" data-calendar-nav="next">Suiv &raquo;</button>
    </div>
  </div>

  <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="text-lg font-semibold text-white" id="calendar-title">--</div>
    <div class="inline-flex rounded-lg bg-slate-800/70 p-1">
      <button type="button" class="rounded-md px-3 py-1.5 text-xs font-semibold text-slate-200 hover:bg-slate-700" data-calendar-view="month">Mois</button>
      <button type="button" class="rounded-md px-3 py-1.5 text-xs font-semibold text-slate-200 hover:bg-slate-700" data-calendar-view="week">Semaine</button>
      <button type="button" class="rounded-md px-3 py-1.5 text-xs font-semibold text-slate-200 hover:bg-slate-700" data-calendar-view="day">Jour</button>
    </div>
  </div>

  <div class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-12">
    <div class="lg:col-span-8">
      <div class="grid grid-cols-7 text-xs uppercase tracking-wide text-slate-400">
        <div class="px-2 py-1">Lun</div>
        <div class="px-2 py-1">Mar</div>
        <div class="px-2 py-1">Mer</div>
        <div class="px-2 py-1">Jeu</div>
        <div class="px-2 py-1">Ven</div>
        <div class="px-2 py-1">Sam</div>
        <div class="px-2 py-1">Dim</div>
      </div>
      <div class="mt-2 overflow-x-auto pb-2">
        <div id="calendar-main"></div>
      </div>
    </div>
    <div class="lg:col-span-4">
      <div class="rounded-xl bg-slate-800/70 p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs uppercase tracking-wide text-slate-400">Rendez-vous</p>
            <p class="mt-1 text-sm font-semibold text-white" id="calendar-selected-label">--</p>
          </div>
          <a id="calendar-add" class="text-xs font-semibold text-indigo-300 hover:text-indigo-200" href="?r=appointments-create">Nouveau</a>
        </div>
        <div class="mt-4 space-y-2" id="calendar-agenda"></div>
      </div>
    </div>
  </div>
  <div id="calendar-toast" class="mt-4 hidden rounded-lg border border-rose-500/30 bg-rose-500/10 px-3 py-2 text-sm text-rose-200" role="alert"></div>
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
        <p class="text-xs uppercase tracking-wide text-slate-500">Total suggere</p>
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
(function () {
  var calendarRoot = document.querySelector('.ui-calendar');
  if (!calendarRoot) return;
  var calendarMain = document.getElementById('calendar-main');
  var calendarTitle = document.getElementById('calendar-title');
  var agendaContainer = document.getElementById('calendar-agenda');
  var agendaLabel = document.getElementById('calendar-selected-label');
  var addLink = document.getElementById('calendar-add');
  var toast = document.getElementById('calendar-toast');
  var viewButtons = calendarRoot.querySelectorAll('[data-calendar-view]');
  var navButtons = calendarRoot.querySelectorAll('[data-calendar-nav]');
  var weekDays = ['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'];
  var monthNames = ['Janvier','Fevrier','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Decembre'];

  var state = {
    mode: calendarRoot.dataset.calendarMode || 'month',
    date: calendarRoot.dataset.calendarDate || new Date().toISOString().slice(0, 10),
    selectedDate: calendarRoot.dataset.calendarDate || new Date().toISOString().slice(0, 10),
    events: []
  };

  function pad(num) { return String(num).padStart(2, '0'); }
  function formatDate(date) { return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate()); }
  function parseDate(value) { return new Date(value + 'T00:00:00'); }
  function startOfWeek(date) {
    var d = new Date(date);
    var day = (d.getDay() + 6) % 7;
    d.setDate(d.getDate() - day);
    return d;
  }
  function monthTitle(date) {
    return monthNames[date.getMonth()] + ' ' + date.getFullYear();
  }
  function showToast(message) {
    if (!toast) return;
    if (!message) {
      toast.classList.add('hidden');
      toast.textContent = '';
      return;
    }
    toast.textContent = message;
    toast.classList.remove('hidden');
  }

  function updateViewButtons() {
    viewButtons.forEach(function (btn) {
      var active = btn.dataset.calendarView === state.mode;
      btn.classList.toggle('bg-indigo-500', active);
      btn.classList.toggle('text-white', active);
      if (!active) {
        btn.classList.add('bg-slate-800', 'text-slate-200');
      } else {
        btn.classList.remove('bg-slate-800', 'text-slate-200');
      }
    });
  }

  function updateUrl() {
    var params = new URLSearchParams(window.location.search || '');
    params.set('r', params.get('r') || 'dashboard');
    params.set('view', state.mode);
    params.set('date', state.date);
    var next = '?' + params.toString();
    window.history.replaceState({}, '', next);
  }

  function buildDayCell(dayDate, inMonth, isToday, isSelected, events) {
    var day = dayDate.getDate();
    var cell = document.createElement('button');
    cell.type = 'button';
    cell.className = 'ui-calendar-cell';
    if (!inMonth) cell.classList.add('is-outside');
    if (isToday) cell.classList.add('is-today');
    if (isSelected) cell.classList.add('is-selected');
    cell.dataset.date = formatDate(dayDate);
    cell.setAttribute('aria-label', 'Jour ' + cell.dataset.date);
    cell.innerHTML = '<div class="calendar-day-number">' + day + '</div>';

    var markers = document.createElement('div');
    markers.className = 'calendar-markers';
    events.slice(0, 3).forEach(function (evt) {
      var dot = document.createElement('span');
      dot.className = 'calendar-dot ' + (evt.color_token || 'status-scheduled');
      dot.dataset.eventId = evt.id;
      dot.setAttribute('tabindex', '0');
      dot.setAttribute('role', 'button');
      dot.setAttribute('aria-label', 'Rendez-vous: ' + (evt.patient_name || '') + ' le ' + evt.date + ' a ' + evt.starts_at.slice(11, 16) + ', statut ' + (evt.status_label_fr || evt.status));
      markers.appendChild(dot);
    });
    if (events.length > 3) {
      var more = document.createElement('span');
      more.className = 'calendar-more';
      more.textContent = '+' + (events.length - 3);
      markers.appendChild(more);
    }
    cell.appendChild(markers);
    return cell;
  }

  function renderMonth(events, nowDate) {
    var current = parseDate(state.date);
    calendarTitle.textContent = monthTitle(current);
    var first = new Date(current.getFullYear(), current.getMonth(), 1);
    var start = startOfWeek(first);
    var grid = document.createElement('div');
    grid.className = 'ui-calendar-grid';
    var dayMap = {};
    events.forEach(function (evt) {
      dayMap[evt.date] = dayMap[evt.date] || [];
      dayMap[evt.date].push(evt);
    });

    for (var i = 0; i < 42; i++) {
      var dayDate = new Date(start);
      dayDate.setDate(start.getDate() + i);
      var dateKey = formatDate(dayDate);
      var inMonth = dayDate.getMonth() === current.getMonth();
      var isToday = dateKey === formatDate(nowDate);
      var isSelected = dateKey === state.selectedDate;
      var cell = buildDayCell(dayDate, inMonth, isToday, isSelected, dayMap[dateKey] || []);
      grid.appendChild(cell);
    }
    calendarMain.innerHTML = '';
    calendarMain.appendChild(grid);
  }

  function renderWeek(events) {
    var selected = parseDate(state.date);
    var weekStart = startOfWeek(selected);
    calendarTitle.textContent = 'Semaine du ' + formatDate(weekStart);
    var wrapper = document.createElement('div');
    wrapper.className = 'ui-calendar-week';
    for (var i = 0; i < 7; i++) {
      var dayDate = new Date(weekStart);
      dayDate.setDate(weekStart.getDate() + i);
      var dateKey = formatDate(dayDate);
      var column = document.createElement('div');
      column.className = 'ui-calendar-week-day';
      column.dataset.date = dateKey;
      var header = document.createElement('div');
      header.className = 'calendar-week-title';
      header.textContent = weekDays[i] + ' ' + pad(dayDate.getDate()) + '/' + pad(dayDate.getMonth() + 1);
      column.appendChild(header);
      var list = document.createElement('div');
      list.className = 'calendar-week-list';
      var dayEvents = events.filter(function (evt) { return evt.date === dateKey; });
      if (!dayEvents.length) {
        var empty = document.createElement('div');
        empty.className = 'calendar-week-empty';
        empty.textContent = 'Aucun rendez-vous';
        list.appendChild(empty);
      } else {
        dayEvents.forEach(function (evt) {
          var item = document.createElement('button');
          item.type = 'button';
          item.className = 'calendar-week-item';
          item.dataset.eventId = evt.id;
          item.dataset.date = dateKey;
          item.innerHTML =
            '<div class="calendar-week-time">' + evt.starts_at.slice(11, 16) + ' - ' + evt.ends_at.slice(11, 16) + '</div>' +
            '<div class="calendar-week-name">' + (evt.patient_name || '') + '</div>' +
            '<span class="calendar-status-chip ' + (evt.color_token || 'status-scheduled') + '">' + (evt.status_label_fr || evt.status) + '</span>';
          list.appendChild(item);
        });
      }
      column.appendChild(list);
      wrapper.appendChild(column);
    }
    calendarMain.innerHTML = '';
    calendarMain.appendChild(wrapper);
  }

  function renderDay(events) {
    var selected = parseDate(state.date);
    calendarTitle.textContent = 'Jour ' + formatDate(selected);
    var dayEvents = events.filter(function (evt) { return evt.date === formatDate(selected); });
    var list = document.createElement('div');
    list.className = 'ui-calendar-day-list';
    var weekStart = startOfWeek(selected);
    var picker = document.createElement('div');
    picker.className = 'calendar-day-picker';
    for (var i = 0; i < 7; i++) {
      var dayDate = new Date(weekStart);
      dayDate.setDate(weekStart.getDate() + i);
      var dateKey = formatDate(dayDate);
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'calendar-day-pill';
      if (dateKey === formatDate(selected)) {
        btn.classList.add('is-active');
      }
      btn.dataset.date = dateKey;
      btn.textContent = weekDays[i] + ' ' + pad(dayDate.getDate()) + '/' + pad(dayDate.getMonth() + 1);
      picker.appendChild(btn);
    }
    list.appendChild(picker);
    if (!dayEvents.length) {
      var empty = document.createElement('div');
      empty.className = 'calendar-day-empty';
      empty.textContent = 'Aucun rendez-vous';
      list.appendChild(empty);
    } else {
      var byHour = {};
      dayEvents.forEach(function (evt) {
        var hour = evt.starts_at.slice(11, 13);
        byHour[hour] = byHour[hour] || [];
        byHour[hour].push(evt);
      });
      Object.keys(byHour).sort().forEach(function (hour) {
        var row = document.createElement('div');
        row.className = 'calendar-day-row';
        row.dataset.hour = hour;
        var time = document.createElement('div');
        time.className = 'calendar-day-hour';
        time.textContent = hour + ':00';
        var items = document.createElement('div');
        items.className = 'calendar-day-items';
        byHour[hour].forEach(function (evt) {
          var item = document.createElement('button');
          item.type = 'button';
          item.className = 'calendar-day-item';
          item.dataset.eventId = evt.id;
          item.innerHTML =
            '<div class="calendar-day-time">' + evt.starts_at.slice(11, 16) + ' - ' + evt.ends_at.slice(11, 16) + '</div>' +
            '<div class="calendar-day-name">' + (evt.patient_name || '') + '</div>' +
            '<span class="calendar-status-chip ' + (evt.color_token || 'status-scheduled') + '">' + (evt.status_label_fr || evt.status) + '</span>';
          items.appendChild(item);
        });
        row.appendChild(time);
        row.appendChild(items);
        list.appendChild(row);
      });
    }
    calendarMain.innerHTML = '';
    calendarMain.appendChild(list);
  }

  function updateAgendaLabel(dateStr) {
    if (!agendaLabel) return;
    var dateObj = parseDate(dateStr);
    agendaLabel.textContent = weekDays[(dateObj.getDay() + 6) % 7] + ' ' + pad(dateObj.getDate()) + '/' + pad(dateObj.getMonth() + 1) + '/' + dateObj.getFullYear();
  }

  function renderAgenda(events) {
    agendaContainer.innerHTML = '';
    if (!events.length) {
      agendaContainer.innerHTML = '<p class="text-sm text-slate-400">Aucun rendez-vous.</p>';
      return;
    }
    events.forEach(function (evt) {
      var row = document.createElement('button');
      row.type = 'button';
      row.className = 'calendar-agenda-item';
      row.dataset.eventId = evt.id;
      row.innerHTML =
        '<div>' +
          '<div class="text-sm font-semibold text-white">' + (evt.patient_name || '') + '</div>' +
          '<div class="text-xs text-slate-400">' + evt.starts_at.slice(11, 16) + ' - ' + evt.ends_at.slice(11, 16) + (evt.services ? ' · ' + evt.services : '') + '</div>' +
        '</div>' +
        '<span class="calendar-status-chip ' + (evt.color_token || 'status-scheduled') + '">' + (evt.status_label_fr || evt.status) + '</span>';
      agendaContainer.appendChild(row);
    });
  }

  function parseJsonSafe(text) {
    var cleaned = (text || '').replace(/^\uFEFF/, '').trim();
    if (!cleaned) return null;
    return JSON.parse(cleaned);
  }

  function fetchAgenda(dateStr) {
    updateAgendaLabel(dateStr);
    if (addLink) {
      addLink.href = '?r=appointments-create&date=' + encodeURIComponent(dateStr);
    }
    fetch('?r=calendar-day-agenda-json&date=' + encodeURIComponent(dateStr) + '&_=' + Date.now(), {
      headers: { 'Accept': 'application/json' },
      credentials: 'include',
      cache: 'no-store'
    })
      .then(function (res) { return res.ok ? res.text() : Promise.reject(); })
      .then(function (text) {
        var data = parseJsonSafe(text) || {};
        renderAgenda(data.events || []);
      })
      .catch(function () {
        renderAgenda([]);
      });
  }

  function fetchCalendar() {
    showToast('');
    updateViewButtons();
    updateUrl();
    fetch('?r=calendar-events-json&mode=' + encodeURIComponent(state.mode) + '&date=' + encodeURIComponent(state.date) + '&_=' + Date.now(), {
      headers: { 'Accept': 'application/json' },
      credentials: 'include',
      cache: 'no-store'
    })
      .then(function (res) { return res.ok ? res.text() : Promise.reject(); })
      .then(function (text) {
        var data = parseJsonSafe(text) || {};
        state.events = data.events || [];
        if (data.selected_date) {
          state.selectedDate = data.selected_date;
        }
        var nowDate = new Date();
        if (state.mode === 'month') {
          renderMonth(state.events, nowDate);
        } else if (state.mode === 'week') {
          renderWeek(state.events);
        } else {
          renderDay(state.events);
        }
        fetchAgenda(state.selectedDate);
      })
      .catch(function () {
        showToast('Impossible de charger le calendrier. Reessayez.');
      });
  }

  function shiftDate(direction) {
    var base = parseDate(state.date);
    if (state.mode === 'month') {
      base.setMonth(base.getMonth() + direction);
    } else if (state.mode === 'week') {
      base.setDate(base.getDate() + direction * 7);
    } else {
      base.setDate(base.getDate() + direction);
    }
    state.date = formatDate(base);
    state.selectedDate = state.date;
    fetchCalendar();
  }

  viewButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      state.mode = btn.dataset.calendarView;
      fetchCalendar();
    });
  });
  navButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var action = btn.dataset.calendarNav;
      if (action === 'today') {
        var now = new Date();
        state.date = formatDate(now);
        state.selectedDate = state.date;
        fetchCalendar();
        return;
      }
      shiftDate(action === 'next' ? 1 : -1);
    });
  });

  calendarMain.addEventListener('click', function (event) {
    var dot = event.target.closest('.calendar-dot');
    if (dot && dot.dataset.eventId) {
      openDrawerById(parseInt(dot.dataset.eventId, 10));
      return;
    }
    var dayCell = event.target.closest('.ui-calendar-cell');
    if (dayCell && dayCell.dataset.date) {
      var hasEvents = dayCell.querySelectorAll('.calendar-dot').length > 0;
      if (!hasEvents) {
        window.location.href = '?r=appointments-create&date=' + encodeURIComponent(dayCell.dataset.date);
        return;
      }
      state.selectedDate = dayCell.dataset.date;
      state.date = dayCell.dataset.date;
      fetchAgenda(state.selectedDate);
      fetchCalendar();
      return;
    }
    var weekDay = event.target.closest('.ui-calendar-week-day');
    if (weekDay && weekDay.dataset.date) {
      state.selectedDate = weekDay.dataset.date;
      state.date = weekDay.dataset.date;
      fetchAgenda(state.selectedDate);
      return;
    }
    var dayPick = event.target.closest('.calendar-day-pill');
    if (dayPick && dayPick.dataset.date) {
      state.selectedDate = dayPick.dataset.date;
      state.date = dayPick.dataset.date;
      fetchCalendar();
      return;
    }
    var dayRow = event.target.closest('.calendar-day-row');
    if (dayRow && dayRow.dataset.hour && !event.target.closest('.calendar-day-item')) {
      window.location.href = '?r=appointments-create&date=' + encodeURIComponent(state.date) + '&time=' + encodeURIComponent(dayRow.dataset.hour + ':00');
      return;
    }
    var weekItem = event.target.closest('.calendar-week-item, .calendar-day-item');
    if (weekItem && weekItem.dataset.eventId) {
      openDrawerById(parseInt(weekItem.dataset.eventId, 10));
    }
  });

  calendarMain.addEventListener('keydown', function (event) {
    var dot = event.target.closest('.calendar-dot');
    if (dot && (event.key === 'Enter' || event.key === ' ')) {
      event.preventDefault();
      openDrawerById(parseInt(dot.dataset.eventId, 10));
    }
  });

  agendaContainer.addEventListener('click', function (event) {
    var item = event.target.closest('.calendar-agenda-item');
    if (item && item.dataset.eventId) {
      openDrawerById(parseInt(item.dataset.eventId, 10));
    }
  });

  function openDrawerById(id) {
    var eventData = state.events.find(function (evt) { return evt.id === id; });
    if (eventData) {
      openDrawerWithData(eventData);
      return;
    }
    fetch('?r=calendar-event-json&id=' + encodeURIComponent(id), {
      headers: { 'Accept': 'application/json' },
      credentials: 'include'
    })
      .then(function (res) { return res.ok ? res.json() : Promise.reject(); })
      .then(function (data) { openDrawerWithData(data); })
      .catch(function () {});
  }

  var drawer = document.getElementById('appointment-drawer');
  var drawerPanel = document.getElementById('appointment-drawer-panel');
  var lastFocused = null;

  function openDrawerWithData(data) {
    document.getElementById('drawer-patient').textContent = data.patient_name || 'Rendez-vous';
    document.getElementById('drawer-time').textContent = (data.starts_at || '').slice(11, 16) + ' - ' + (data.ends_at || '').slice(11, 16);
    document.getElementById('drawer-status').textContent = data.status_label_fr || data.status || '';
    document.getElementById('drawer-services').textContent = data.services || '-';
    document.getElementById('drawer-total').textContent = data.service_total_formatted || '0,000 TND';
    document.getElementById('drawer-reason').textContent = data.reason || '-';
    document.getElementById('drawer-notes').textContent = data.notes || '-';
    var reminderLink = document.getElementById('drawer-reminder-link');
    if (reminderLink && data.id) {
      reminderLink.href = '?r=reminders-send&appointment_id=' + encodeURIComponent(data.id);
    }
    drawer.classList.remove('hidden');
    drawer.setAttribute('aria-hidden', 'false');
    lastFocused = document.activeElement;
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
    var focusable = drawer.querySelectorAll('a[href], button, input, select, textarea, [tabindex]:not([tabindex=\"-1\"])');
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

  if (localStorage.getItem('calendarRefresh') === '1') {
    localStorage.removeItem('calendarRefresh');
  }
  fetchCalendar();
  setInterval(fetchCalendar, 30000);
})();
</script>
