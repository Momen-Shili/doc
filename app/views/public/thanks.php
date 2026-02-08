<?php
$title = 'Merci';
ob_start();
?>
<main class="public-main">
    <section class="public-thanks">
        <div class="public-container">
            <h1>Merci</h1>
            <p><?= e($message ?? 'Votre demande a ete envoyee.') ?></p>
            <a class="public-btn primary" href="?r=public-doctors">Retour a l'annuaire</a>
        </div>
    </section>
</main>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
