<php
$payment = $payment [];
>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reçu <= e($payment['receipt_number'] '') ></title>
    <style>
        body { font-family: Arial, sans-serif; color: #0f172a; margin: 24px; }
        .card { border: 1px solid #e2e8f0; padding: 24px; border-radius: 8px; max-width: 720px; }
        h1 { font-size: 20px; margin: 0 0 8px; }
        .muted { color: #64748b; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        td { padding: 6px 0; vertical-align: top; }
        .label { font-weight: bold; width: 180px; }
        .actions { margin-top: 16px; }
        @media print {
            .actions { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Reçu de paiement - Espèces</h1>
        <div class="muted">Reçu n° <= e($payment['receipt_number'] '') ></div>

        <table>
            <tr><td class="label">Cabinet</td><td><= e($payment['doctor_name'] '') ></td></tr>
            <tr><td class="label">Spécialité</td><td><= e($payment['specialty'] '') ></td></tr>
            <tr><td class="label">Téléphone</td><td><= e($payment['doctor_phone'] '') ></td></tr>
            <tr><td class="label">Email</td><td><= e($payment['doctor_email'] '') ></td></tr>
        </table>

        <table>
            <tr><td class="label">Patient</td><td><= e($payment['patient_name'] '') ></td></tr>
            <tr><td class="label">Téléphone patient</td><td><= e($payment['patient_phone'] '') ></td></tr>
        </table>

        <table>
            <tr><td class="label">Date de paiement</td><td><= e($payment['received_at'] '') ></td></tr>
            <tr><td class="label">Montant</td><td><= e(format_tnd($payment['amount'] 0)) ></td></tr>
            <tr><td class="label">Référence</td><td><= e($payment['reference'] '') ></td></tr>
            <tr><td class="label">Notes</td><td><= e($payment['notes'] '') ></td></tr>
        </table>

        <div class="actions">
            <button onclick="window.print() ">Imprimer</button>
        </div>
    </div>
</body>
</html>