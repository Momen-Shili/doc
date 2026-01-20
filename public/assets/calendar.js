/* Mini Google Calendar style - vanilla JS */
(function () {
  var root = document.querySelector('.gc-calendar');
  if (!root) return;

  var state = {
    view: root.dataset.view || 'month',
    date: root.dataset.date || new Date().toISOString().slice(0, 10),
    selectedDate: root.dataset.date || new Date().toISOString().slice(0, 10),
    events: [],
    services: null,
    currentEvent: null
  };

  var csrf = root.dataset.csrf || '';
  var grid = document.getElementById('gc-grid');
  var miniGrid = document.getElementById('gc-mini-grid');
  var monthTitle = document.getElementById('gc-month-title');
  var miniTitle = document.getElementById('gc-mini-title');
  var agendaList = document.getElementById('gc-agenda-list');
  var agendaDate = document.getElementById('gc-agenda-date');
  var toast = document.getElementById('gc-toast');
  var popover = document.getElementById('gc-popover');
  var modal = document.getElementById('gc-modal');
  var form = document.getElementById('gc-form');

  var monthNames = ['Janvier','Fevrier','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Decembre'];
  var weekDays = ['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'];

  function pad(num) { return String(num).padStart(2, '0'); }
  function formatDate(date) { return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate()); }
  function parseDate(value) { return new Date(value + 'T00:00:00'); }
  function startOfWeek(date) {
    var d = new Date(date);
    var day = (d.getDay() + 6) % 7;
    d.setDate(d.getDate() - day);
    return d;
  }
  function monthTitleText(date) { return monthNames[date.getMonth()] + ' ' + date.getFullYear(); }

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
    root.querySelectorAll('[data-view-switch]').forEach(function (btn) {
      var active = btn.dataset.viewSwitch === state.view;
      btn.classList.toggle('is-active', active);
    });
  }

  function updateUrl() {
    var params = new URLSearchParams(window.location.search || '');
    params.set('view', state.view);
    params.set('date', state.date);
    var next = '?' + params.toString();
    window.history.replaceState({}, '', next);
  }

  function renderMiniPicker(baseDate) {
    var first = new Date(baseDate.getFullYear(), baseDate.getMonth(), 1);
    var start = startOfWeek(first);
    miniTitle.textContent = monthTitleText(baseDate);
    miniGrid.innerHTML = '';
    for (var i = 0; i < 42; i++) {
      var day = new Date(start);
      day.setDate(start.getDate() + i);
      var dateKey = formatDate(day);
      var cell = document.createElement('button');
      cell.type = 'button';
      cell.className = 'gc-mini-cell';
      if (day.getMonth() !== baseDate.getMonth()) cell.classList.add('is-outside');
      if (dateKey === state.selectedDate) cell.classList.add('is-selected');
      if (dateKey === formatDate(new Date())) cell.classList.add('is-today');
      cell.dataset.date = dateKey;
      cell.textContent = day.getDate();
      miniGrid.appendChild(cell);
    }
  }

  function renderMonthGrid(baseDate) {
    var first = new Date(baseDate.getFullYear(), baseDate.getMonth(), 1);
    var start = startOfWeek(first);
    monthTitle.textContent = monthTitleText(baseDate);
    grid.innerHTML = '';
    var eventsByDate = {};
    state.events.forEach(function (evt) {
      eventsByDate[evt.date] = eventsByDate[evt.date] || [];
      eventsByDate[evt.date].push(evt);
    });
    for (var i = 0; i < 42; i++) {
      var day = new Date(start);
      day.setDate(start.getDate() + i);
      var dateKey = formatDate(day);
      var cell = document.createElement('div');
      cell.className = 'gc-cell';
      if (day.getMonth() !== baseDate.getMonth()) cell.classList.add('is-outside');
      if (dateKey === state.selectedDate) cell.classList.add('is-selected');
      if (dateKey === formatDate(new Date())) cell.classList.add('is-today');
      cell.dataset.date = dateKey;
      var label = document.createElement('div');
      label.className = 'gc-cell-date';
      label.textContent = day.getDate();
      cell.appendChild(label);
      var list = document.createElement('div');
      list.className = 'gc-cell-events';
      var dayEvents = eventsByDate[dateKey] || [];
      dayEvents.slice(0, 3).forEach(function (evt) {
        var bar = document.createElement('button');
        bar.type = 'button';
        bar.className = 'gc-event ' + (evt.color_token || 'status-scheduled');
        bar.dataset.eventId = evt.id;
        bar.innerHTML = '<span class="gc-event-time">' + evt.starts_at.slice(11, 16) + '</span>' +
          '<span class="gc-event-name">' + (evt.patient_name || '') + '</span>';
        list.appendChild(bar);
      });
      if (dayEvents.length > 3) {
        var more = document.createElement('button');
        more.type = 'button';
        more.className = 'gc-more';
        more.dataset.date = dateKey;
        more.textContent = '+' + (dayEvents.length - 3) + ' plus';
        list.appendChild(more);
      }
      cell.appendChild(list);
      grid.appendChild(cell);
    }
  }

  function renderWeekGrid(baseDate) {
    var weekStart = startOfWeek(baseDate);
    monthTitle.textContent = 'Semaine du ' + formatDate(weekStart);
    grid.innerHTML = '';
    grid.className = 'gc-grid gc-grid-week-list';
    for (var i = 0; i < 7; i++) {
      var day = new Date(weekStart);
      day.setDate(weekStart.getDate() + i);
      var dateKey = formatDate(day);
      var group = document.createElement('div');
      group.className = 'gc-week-group';
      group.dataset.date = dateKey;
      group.innerHTML = '<div class="gc-week-title">' + weekDays[i] + ' ' + pad(day.getDate()) + '/' + pad(day.getMonth() + 1) + '</div>';
      var list = document.createElement('div');
      list.className = 'gc-week-list';
      var dayEvents = state.events.filter(function (evt) { return evt.date === dateKey; });
      if (!dayEvents.length) {
        var empty = document.createElement('div');
        empty.className = 'gc-week-empty';
        empty.textContent = 'Aucun rendez-vous';
        list.appendChild(empty);
      } else {
        dayEvents.forEach(function (evt) {
          var row = document.createElement('button');
          row.type = 'button';
          row.className = 'gc-week-row';
          row.dataset.eventId = evt.id;
          row.innerHTML =
            '<div class="gc-week-time">' + evt.starts_at.slice(11, 16) + ' - ' + evt.ends_at.slice(11, 16) + '</div>' +
            '<div class="gc-week-name">' + (evt.patient_name || '') + '</div>' +
            '<span class="gc-chip ' + (evt.color_token || 'status-scheduled') + '">' + (evt.status_label_fr || evt.status) + '</span>';
          list.appendChild(row);
        });
      }
      group.appendChild(list);
      grid.appendChild(group);
    }
  }

  function renderDayView(baseDate) {
    monthTitle.textContent = 'Jour ' + formatDate(baseDate);
    grid.innerHTML = '';
    grid.className = 'gc-grid gc-grid-day';
    var dayEvents = state.events.filter(function (evt) { return evt.date === formatDate(baseDate); });
    if (!dayEvents.length) {
      grid.innerHTML = '<div class="gc-day-empty">Aucun rendez-vous</div>';
      return;
    }
    dayEvents.forEach(function (evt) {
      var row = document.createElement('button');
      row.type = 'button';
      row.className = 'gc-day-item';
      row.dataset.eventId = evt.id;
      row.innerHTML = '<div class="gc-day-time">' + evt.starts_at.slice(11, 16) + ' - ' + evt.ends_at.slice(11, 16) + '</div>' +
        '<div class="gc-day-name">' + (evt.patient_name || '') + '</div>' +
        '<span class="gc-chip ' + (evt.color_token || 'status-scheduled') + '">' + (evt.status_label_fr || evt.status) + '</span>';
      grid.appendChild(row);
    });
  }

  function renderAgenda() {
    var selected = state.selectedDate;
    agendaDate.textContent = selected;
    var dayEvents = state.events.filter(function (evt) { return evt.date === selected; });
    agendaList.innerHTML = '';
    if (!dayEvents.length) {
      agendaList.innerHTML = '<div class="gc-agenda-empty">Aucun rendez-vous</div>';
      return;
    }
    dayEvents.forEach(function (evt) {
      var row = document.createElement('button');
      row.type = 'button';
      row.className = 'gc-agenda-item';
      row.dataset.eventId = evt.id;
      row.innerHTML = '<div>' +
        '<div class="gc-agenda-name">' + (evt.patient_name || '') + '</div>' +
        '<div class="gc-agenda-time">' + evt.starts_at.slice(11, 16) + ' - ' + evt.ends_at.slice(11, 16) + '</div>' +
      '</div>' +
      '<span class="gc-chip ' + (evt.color_token || 'status-scheduled') + '">' + (evt.status_label_fr || evt.status) + '</span>';
      agendaList.appendChild(row);
    });
  }

  function renderAll() {
    var baseDate = parseDate(state.date);
    updateViewButtons();
    renderMiniPicker(baseDate);
    if (state.view === 'month') {
      grid.className = 'gc-grid';
      renderMonthGrid(baseDate);
    } else if (state.view === 'week') {
      renderWeekGrid(baseDate);
    } else {
      renderDayView(baseDate);
    }
    renderAgenda();
  }

  function fetchEvents() {
    showToast('');
    updateUrl();
    fetch('?r=calendar-events-json&view=' + encodeURIComponent(state.view) + '&date=' + encodeURIComponent(state.date) + '&_=' + Date.now(), {
      headers: { 'Accept': 'application/json' },
      credentials: 'include',
      cache: 'no-store'
    })
      .then(function (res) { return res.ok ? res.text() : Promise.reject(); })
      .then(function (text) {
        var cleaned = (text || '').replace(/^\uFEFF/, '').trim();
        var data = cleaned ? JSON.parse(cleaned) : {};
        state.events = data.events || [];
        if (data.selected_date) {
          state.selectedDate = data.selected_date;
        }
        renderAll();
      })
      .catch(function () {
        showToast('Impossible de charger le calendrier. Reessayez.');
      });
  }

  function openPopover(evt, anchor) {
    state.currentEvent = evt;
    document.getElementById('gc-popover-title').textContent = evt.patient_name || 'Rendez-vous';
    document.getElementById('gc-popover-time').textContent = evt.starts_at.slice(11, 16) + ' - ' + evt.ends_at.slice(11, 16);
    document.getElementById('gc-popover-status').textContent = evt.status_label_fr || evt.status;
    document.getElementById('gc-popover-status').className = 'gc-chip ' + (evt.color_token || 'status-scheduled');
    document.getElementById('gc-popover-patient').textContent = (evt.patient_name || '') + (evt.patient_phone ? ' · ' + evt.patient_phone : '');
    document.getElementById('gc-popover-services').textContent = evt.services_text || '-';
    document.getElementById('gc-popover-reason').textContent = evt.reason || '-';
    document.getElementById('gc-popover-notes').textContent = evt.notes || '-';
    var rect = anchor.getBoundingClientRect();
    popover.style.top = (window.scrollY + rect.bottom + 8) + 'px';
    popover.style.left = (window.scrollX + Math.min(rect.left, window.innerWidth - 320)) + 'px';
    popover.classList.remove('hidden');
  }

  function closePopover() {
    popover.classList.add('hidden');
    state.currentEvent = null;
  }

  function openModal(mode, evt) {
    clearErrors();
    modal.classList.remove('hidden');
    modal.dataset.mode = mode;
    state.currentEvent = evt || null;
    form.reset();
    document.getElementById('gc-modal-title').textContent = mode === 'edit' ? 'Modifier un rendez-vous' : 'Creer un rendez-vous';
    var baseDate = evt ? evt.starts_at.slice(0, 10) : state.selectedDate;
    document.getElementById('gc-date').value = baseDate;
    document.getElementById('gc-start-time').value = evt ? evt.starts_at.slice(11, 16) : '09:00';
    document.getElementById('gc-end-time').value = evt ? evt.ends_at.slice(11, 16) : '';
    document.getElementById('gc-status').value = evt ? evt.status : 'PLANIFIE';
    document.getElementById('gc-reason').value = evt ? (evt.reason || '') : '';
    document.getElementById('gc-notes').value = evt ? (evt.notes || '') : '';
    document.getElementById('gc-patient-search').value = evt ? (evt.patient_name || '') : '';
    document.getElementById('gc-patient-id').value = evt ? (evt.patient_id || '') : '';
    loadServices(evt ? evt.services || [] : []);
  }

  function closeModal() {
    modal.classList.add('hidden');
  }

  function loadServices(selected) {
    if (state.services) {
      renderServices(selected);
      return;
    }
    fetch('?r=services-json', { headers: { 'Accept': 'application/json' }, credentials: 'include' })
      .then(function (res) { return res.ok ? res.text() : Promise.reject(); })
      .then(function (text) {
        var data = JSON.parse((text || '').replace(/^\uFEFF/, '').trim() || '[]');
        state.services = data;
        renderServices(selected);
      })
      .catch(function () {
        state.services = [];
        renderServices(selected);
      });
  }

  function renderServices(selected) {
    var box = document.getElementById('gc-services');
    var selectedMap = {};
    selected.forEach(function (srv) { selectedMap[srv.id] = srv.qty; });
    box.innerHTML = '';
    if (!state.services || !state.services.length) {
      box.innerHTML = '<div class="gc-help">Aucun service disponible</div>';
      return;
    }
    state.services.forEach(function (srv) {
      var row = document.createElement('div');
      row.className = 'gc-service-row';
      row.innerHTML =
        '<div>' +
          '<div class="gc-service-name">' + srv.name + '</div>' +
          '<div class="gc-service-meta">Prix: ' + formatTnd(srv.default_price) + ' · Duree: ' + srv.default_duration_minutes + ' min</div>' +
        '</div>' +
        '<input type="number" min="0" value="' + (selectedMap[srv.id] || 0) + '" data-id="' + srv.id + '" data-price="' + srv.default_price + '" data-duration="' + srv.default_duration_minutes + '" class="gc-input gc-qty">';
      box.appendChild(row);
    });
    updateServicesSummary();
  }

  function updateServicesSummary() {
    var total = 0;
    var duration = 0;
    document.querySelectorAll('.gc-qty').forEach(function (input) {
      var qty = parseInt(input.value || '0', 10);
      var price = parseFloat(input.dataset.price || '0');
      var dur = parseInt(input.dataset.duration || '0', 10);
      if (qty > 0) {
        total += qty * price;
        duration += qty * dur;
      }
    });
    var summary = document.getElementById('gc-services-summary');
    if (summary) {
      summary.textContent = 'Total suggere: ' + formatTnd(total) + ' · Duree: ' + duration + ' min';
    }
  }

  function formatTnd(amount) {
    var val = parseFloat(amount || 0);
    return val.toLocaleString('fr-FR', { minimumFractionDigits: 3, maximumFractionDigits: 3 }) + ' TND';
  }

  function clearErrors() {
    document.querySelectorAll('.gc-error').forEach(function (el) { el.textContent = ''; });
    var block = document.getElementById('gc-form-error');
    if (block) block.textContent = '';
  }

  function showErrors(errors) {
    clearErrors();
    var block = document.getElementById('gc-form-error');
    Object.keys(errors || {}).forEach(function (key) {
      var el = document.querySelector('[data-error=\"' + key + '\"]');
      if (el) el.textContent = errors[key];
      if (block && !el) block.textContent = errors[key];
    });
  }

  function submitForm(event) {
    event.preventDefault();
    clearErrors();
    var dateVal = document.getElementById('gc-date').value;
    var startTime = document.getElementById('gc-start-time').value;
    var endTime = document.getElementById('gc-end-time').value;
    document.getElementById('gc-starts-at').value = dateVal && startTime ? (dateVal + ' ' + startTime + ':00') : '';
    document.getElementById('gc-ends-at').value = endTime ? (dateVal + ' ' + endTime + ':00') : '';
    var formData = new FormData(form);
    document.querySelectorAll('.gc-qty').forEach(function (input) {
      formData.append('service_id[]', input.dataset.id);
      formData.append('service_qty[]', input.value || '0');
    });
    var mode = modal.dataset.mode || 'create';
    var url = mode === 'edit' && state.currentEvent ? '?r=calendar-event-update&id=' + encodeURIComponent(state.currentEvent.id) : '?r=calendar-event-create';
    fetch(url, { method: 'POST', body: formData, credentials: 'include' })
      .then(function (res) { return res.ok ? res.text() : res.text().then(function (t) { throw t; }); })
      .then(function (text) {
        closeModal();
        closePopover();
        fetchEvents();
        showToast(mode === 'edit' ? 'Rendez-vous modifie.' : 'Rendez-vous cree.');
      })
      .catch(function (text) {
        try {
          var data = JSON.parse((text || '').replace(/^\uFEFF/, '').trim());
          showErrors(data.errors || { form: 'Erreur validation.' });
        } catch (err) {
          showErrors({ form: 'Erreur lors de la sauvegarde.' });
        }
      });
  }

  function handlePatientSearch() {
    var q = document.getElementById('gc-patient-search').value.trim();
    var list = document.getElementById('gc-patient-results');
    if (!q) {
      list.classList.add('hidden');
      list.innerHTML = '';
      return;
    }
    fetch('?r=patients-search-json&q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' }, credentials: 'include' })
      .then(function (res) { return res.ok ? res.text() : Promise.reject(); })
      .then(function (text) {
        var data = JSON.parse((text || '').replace(/^\uFEFF/, '').trim() || '[]');
        list.innerHTML = '';
        data.forEach(function (p) {
          var item = document.createElement('button');
          item.type = 'button';
          item.className = 'gc-dropdown-item';
          item.dataset.id = p.id;
          item.textContent = p.full_name + (p.phone ? ' · ' + p.phone : '');
          list.appendChild(item);
        });
        list.classList.remove('hidden');
      });
  }

  root.addEventListener('click', function (event) {
    var nav = event.target.closest('[data-nav]');
    if (nav) {
      var action = nav.dataset.nav;
      var base = parseDate(state.date);
      if (action === 'today') {
        state.date = formatDate(new Date());
        state.selectedDate = state.date;
      } else {
        if (state.view === 'month') base.setMonth(base.getMonth() + (action === 'next' ? 1 : -1));
        if (state.view === 'week') base.setDate(base.getDate() + (action === 'next' ? 7 : -7));
        if (state.view === 'day') base.setDate(base.getDate() + (action === 'next' ? 1 : -1));
        state.date = formatDate(base);
        state.selectedDate = state.date;
      }
      fetchEvents();
      return;
    }
    var viewSwitch = event.target.closest('[data-view-switch]');
    if (viewSwitch) {
      state.view = viewSwitch.dataset.viewSwitch;
      fetchEvents();
      return;
    }
    var cell = event.target.closest('.gc-cell, .gc-mini-cell, .gc-week-col');
    if (cell && cell.dataset.date) {
      state.selectedDate = cell.dataset.date;
      state.date = cell.dataset.date;
      renderAll();
      if (cell.classList.contains('gc-cell') || cell.classList.contains('gc-week-col')) {
        openModal('create');
      }
      return;
    }
    var more = event.target.closest('.gc-more');
    if (more && more.dataset.date) {
      state.selectedDate = more.dataset.date;
      renderAgenda();
      return;
    }
    var eventBar = event.target.closest('.gc-event, .gc-agenda-item, .gc-day-item');
    if (eventBar && eventBar.dataset.eventId) {
      var evt = state.events.find(function (e) { return String(e.id) === String(eventBar.dataset.eventId); });
      if (evt) {
        openPopover(evt, eventBar);
      } else {
        fetch('?r=calendar-event-json&id=' + encodeURIComponent(eventBar.dataset.eventId))
          .then(function (res) { return res.ok ? res.text() : Promise.reject(); })
          .then(function (text) {
            var data = JSON.parse((text || '').replace(/^\uFEFF/, '').trim());
            openPopover(data, eventBar);
          });
      }
      return;
    }
    var createBtn = event.target.closest('[data-action=\"create\"]');
    if (createBtn) {
      openModal('create');
      return;
    }
    var miniNav = event.target.closest('[data-mini-nav]');
    if (miniNav) {
      var base = parseDate(state.date);
      base.setMonth(base.getMonth() + (miniNav.dataset.miniNav === 'next' ? 1 : -1));
      state.date = formatDate(base);
      state.selectedDate = state.date;
      fetchEvents();
      return;
    }
  });

  popover.addEventListener('click', function (event) {
    if (event.target.closest('[data-popover=\"close\"]')) {
      closePopover();
      return;
    }
    var action = event.target.closest('[data-action]');
    if (!action || !state.currentEvent) return;
    if (action.dataset.action === 'edit') {
      openModal('edit', state.currentEvent);
      return;
    }
    if (action.dataset.action === 'cancel') {
      if (!confirm('Annuler ce rendez-vous ?')) return;
      postStatus('ANNULE');
      return;
    }
    if (action.dataset.action === 'noshow') {
      if (!confirm('Marquer absent ?')) return;
      postStatus('NO_SHOW');
      return;
    }
    if (action.dataset.action === 'delete') {
      if (!confirm('Supprimer ce rendez-vous ?')) return;
      postDelete();
      return;
    }
  });

  function postStatus(status) {
    var formData = new FormData();
    formData.append('_csrf', csrf);
    formData.append('status', status);
    fetch('?r=calendar-event-status&id=' + encodeURIComponent(state.currentEvent.id), { method: 'POST', body: formData, credentials: 'include' })
      .then(function () { closePopover(); fetchEvents(); });
  }

  function postDelete() {
    var formData = new FormData();
    formData.append('_csrf', csrf);
    fetch('?r=calendar-event-delete&id=' + encodeURIComponent(state.currentEvent.id), { method: 'POST', body: formData, credentials: 'include' })
      .then(function () { closePopover(); fetchEvents(); });
  }

  document.addEventListener('click', function (event) {
    if (!popover.classList.contains('hidden') && !event.target.closest('#gc-popover') && !event.target.closest('.gc-event')) {
      closePopover();
    }
  });

  modal.addEventListener('click', function (event) {
    if (event.target.closest('[data-modal=\"close\"]')) closeModal();
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      if (!modal.classList.contains('hidden')) closeModal();
      if (!popover.classList.contains('hidden')) closePopover();
    }
  });

  form.addEventListener('submit', submitForm);
  document.getElementById('gc-patient-search').addEventListener('input', handlePatientSearch);
  document.getElementById('gc-patient-results').addEventListener('click', function (event) {
    var item = event.target.closest('.gc-dropdown-item');
    if (!item) return;
    document.getElementById('gc-patient-search').value = item.textContent;
    document.getElementById('gc-patient-id').value = item.dataset.id;
    document.getElementById('gc-patient-results').classList.add('hidden');
  });
  document.getElementById('gc-services').addEventListener('input', function (event) {
    if (event.target.classList.contains('gc-qty')) {
      updateServicesSummary();
    }
  });

  fetchEvents();
  setInterval(fetchEvents, 60000);
})();
