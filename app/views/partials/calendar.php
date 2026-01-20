<?php
$calendarView = $view ?? 'month';
$calendarDate = $date ?? date('Y-m-d');
$calendarView = in_array($calendarView, ['day', 'week', 'month'], true) ? $calendarView : 'month';
?>
<div class="gc-calendar" data-view="<?= e($calendarView) ?>" data-date="<?= e($calendarDate) ?>" data-csrf="<?= e(csrf_token()) ?>">
  <div class="gc-toolbar">
    <div>
      <h2 class="gc-title">Agenda</h2>
      <p class="gc-subtitle">Calendrier des rendez-vous</p>
    </div>
    <div class="gc-toolbar-actions">
      <button type="button" class="gc-btn" data-nav="prev">&laquo; Prec</button>
      <button type="button" class="gc-btn" data-nav="today">Aujourd'hui</button>
      <button type="button" class="gc-btn" data-nav="next">Suiv &raquo;</button>
    </div>
    <div class="gc-toolbar-view">
      <button type="button" class="gc-pill" data-view-switch="month">Mois</button>
      <button type="button" class="gc-pill" data-view-switch="week">Semaine</button>
      <button type="button" class="gc-pill" data-view-switch="day">Jour</button>
    </div>
  </div>

  <div class="gc-body">
    <aside class="gc-side">
      <button type="button" class="gc-create" data-action="create">Creer</button>
      <div class="gc-mini-header">
        <button type="button" class="gc-mini-nav" data-mini-nav="prev">&laquo;</button>
        <div id="gc-mini-title">--</div>
        <button type="button" class="gc-mini-nav" data-mini-nav="next">&raquo;</button>
      </div>
      <div class="gc-mini-grid" id="gc-mini-grid"></div>
    </aside>

    <section class="gc-main">
      <div class="gc-month-title" id="gc-month-title">--</div>
      <div class="gc-weekdays">
        <div>Lun</div><div>Mar</div><div>Mer</div><div>Jeu</div><div>Ven</div><div>Sam</div><div>Dim</div>
      </div>
      <div class="gc-grid" id="gc-grid"></div>
    </section>

    <aside class="gc-agenda">
      <div class="gc-agenda-header">
        <div>
          <div class="gc-agenda-label">Rendez-vous</div>
          <div class="gc-agenda-date" id="gc-agenda-date">--</div>
        </div>
        <button type="button" class="gc-link" data-action="create">Nouveau</button>
      </div>
      <div class="gc-agenda-list" id="gc-agenda-list"></div>
    </aside>
  </div>

  <div id="gc-toast" class="gc-toast hidden" role="alert"></div>
</div>

<div id="gc-popover" class="gc-popover hidden" role="dialog" aria-modal="false">
  <div class="gc-popover-content">
    <div class="gc-popover-header">
      <div>
        <div class="gc-popover-title" id="gc-popover-title">--</div>
        <div class="gc-popover-subtitle" id="gc-popover-time">--</div>
      </div>
      <button type="button" class="gc-icon-btn" data-popover="close">X</button>
    </div>
    <div class="gc-popover-body">
      <div class="gc-row">
        <span class="gc-label">Statut</span>
        <span class="gc-chip" id="gc-popover-status">--</span>
      </div>
      <div class="gc-row">
        <span class="gc-label">Patient</span>
        <span id="gc-popover-patient">--</span>
      </div>
      <div class="gc-row">
        <span class="gc-label">Services</span>
        <span id="gc-popover-services">--</span>
      </div>
      <div class="gc-row">
        <span class="gc-label">Motif</span>
        <span id="gc-popover-reason">--</span>
      </div>
      <div class="gc-row">
        <span class="gc-label">Notes</span>
        <span id="gc-popover-notes">--</span>
      </div>
    </div>
    <div class="gc-popover-actions">
      <button type="button" class="gc-btn" data-action="edit">Modifier</button>
      <button type="button" class="gc-btn" data-action="cancel">Annuler RDV</button>
      <button type="button" class="gc-btn" data-action="noshow">Marquer absent</button>
      <button type="button" class="gc-btn gc-btn-danger" data-action="delete">Supprimer</button>
    </div>
  </div>
</div>

<div id="gc-modal" class="gc-modal hidden" role="dialog" aria-modal="true">
  <div class="gc-modal-overlay" data-modal="close"></div>
  <div class="gc-modal-panel" role="document" aria-labelledby="gc-modal-title">
    <div class="gc-modal-header">
      <h3 class="gc-modal-title" id="gc-modal-title">Rendez-vous</h3>
      <button type="button" class="gc-icon-btn" data-modal="close">X</button>
    </div>
    <form id="gc-form" class="gc-modal-body">
      <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="patient_id" id="gc-patient-id">
      <input type="hidden" name="starts_at" id="gc-starts-at">
      <input type="hidden" name="ends_at" id="gc-ends-at">

      <div class="gc-field">
        <label for="gc-patient-search" class="gc-label">Patient</label>
        <input id="gc-patient-search" type="text" class="gc-input" placeholder="Rechercher un patient..." autocomplete="off">
        <div id="gc-patient-results" class="gc-dropdown hidden"></div>
        <div class="gc-error" data-error="patient_id"></div>
      </div>

      <div class="gc-field-row">
        <div class="gc-field">
          <label for="gc-date" class="gc-label">Date</label>
          <input id="gc-date" type="date" class="gc-input">
        </div>
        <div class="gc-field">
          <label for="gc-start-time" class="gc-label">Heure debut</label>
          <input id="gc-start-time" type="time" class="gc-input">
        </div>
        <div class="gc-field">
          <label for="gc-end-time" class="gc-label">Heure fin</label>
          <input id="gc-end-time" type="time" class="gc-input">
        </div>
      </div>

      <div class="gc-field">
        <label class="gc-label">Services</label>
        <div id="gc-services" class="gc-services"></div>
        <div class="gc-help" id="gc-services-summary">Total suggere: 0,000 TND · Duree: 0 min</div>
      </div>

      <div class="gc-field">
        <label for="gc-status" class="gc-label">Statut</label>
        <select id="gc-status" class="gc-input" name="status">
          <option value="PLANIFIE">Planifie</option>
          <option value="CONFIRME">Confirme</option>
          <option value="TERMINE">Termine</option>
          <option value="ANNULE">Annule</option>
          <option value="NO_SHOW">Absent</option>
        </select>
        <div class="gc-error" data-error="status"></div>
      </div>

      <div class="gc-field">
        <label for="gc-reason" class="gc-label">Motif</label>
        <input id="gc-reason" type="text" class="gc-input" name="reason">
      </div>

      <div class="gc-field">
        <label for="gc-notes" class="gc-label">Notes</label>
        <textarea id="gc-notes" class="gc-input" name="notes" rows="3"></textarea>
      </div>

      <div class="gc-error gc-error-block" id="gc-form-error"></div>
      <div class="gc-modal-actions">
        <button type="submit" class="gc-btn gc-btn-primary">Enregistrer</button>
        <button type="button" class="gc-btn" data-modal="close">Annuler</button>
      </div>
    </form>
  </div>
</div>

<script src="<?= e(asset_url('assets/calendar.js')) ?>"></script>
