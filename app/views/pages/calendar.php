<?php
$title = 'Calendrier';
ob_start();
?>
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-semibold text-white">Calendrier</h1>
      <p class="text-sm text-slate-300">Vue planning des rendez-vous</p>
    </div>
    <a class="inline-flex items-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400" href="?r=appointments-create">Nouveau rendez-vous</a>
  </div>
  <?php require __DIR__ . '/../partials/calendar.php'; ?>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
