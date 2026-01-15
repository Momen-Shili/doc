<?php
$closure = $closure [];
>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clôture de caisse</title>
    <style>
        body { font-family: Arial, sans-serif; color: #0f172a; margin: 24px; }
        .card { border: 1px solid #e2e8f0; padding: 24px; border-radius: 8px; max-width: 720px; }
        h1 { font-size: 20px; margin: 0 0 8px; }
        .muted { color: #64748b; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        td { padding: 6px 0; vertical-align: top; }
        .label { font-weight: bold; width: 180px; }
        .actions { margin-top: 16px; }
        @media print { .actions { display: none; } body { margin: 0; } }
    </style>
</head>
<body>
    <div class="card">
        <h1>Clôture de caisse</h1>
        <div class="muted">Date : <?= e($closure['closure_date'] '') ?></div>

        <table>
            <tr><td class="label">Cabinet</td><td><?= e($closure['doctor_name'] '') ?></td></tr>
            <tr><td class="label">Spécialité</td><td><?= e($closure['specialty'] '') ?></td></tr>
            <tr><td class="label">Téléphone</td><td><?= e($closure['phone'] '') ?></td></tr>
            <tr><td class="label">Email</td><td><?= e($closure['email'] '') ?></td></tr>
        </table>

        <table>
            <tr><td class="label">Total système</td><td><?= e(format_tnd($closure['total_system'] ?? 0)) ?></td></tr>
            <tr><td class="label">Montant déclaré</td><td><?= e(format_tnd($closure['cash_declared'] ?? 0)) ?></td></tr>
            <tr><td class="label">cart</td><td><?= e(format_tnd($closure['difference'] ?? 0)) ?></td></tr>
            <tr><td class="label">Notes</td><td><?= e($closure['notes'] '') ?></td></tr>
        </table>

        <div class="actions">
            <button onclick="window.print() ">Imprimer</button>
        </div>
    </div>
</body>
</html>