<?php
$title = 'Notifications';
ob_start();
$hasErrors = !empty($errors);
$settings = $settings ?? [];
?>
<div class="max-w-4xl space-y-6">
    <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
        <h1 class="text-lg font-semibold text-white">Notifications</h1>
        <p class="mt-1 text-sm text-slate-400">Configurez les envois Email, SMS et WhatsApp.</p>

        <?php if ($hasErrors): ?>
            <div class="mt-4 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200" role="alert">
                <?= e(implode(' ', $errors)) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="?r=notifications-settings" class="mt-6 space-y-8">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save" />

            <div class="rounded-xl bg-slate-800/40 p-4 ring-1 ring-white/10">
                <h2 class="text-sm font-semibold text-slate-100">Email (SMTP)</h2>
                <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="smtp-host" class="block text-xs font-medium text-slate-200">Hôte SMTP</label>
                        <input id="smtp-host" name="smtp_host" type="text" value="<?= e($settings['smtp_host'] ?? '') ?>" class="mt-2 w-full rounded-md border-0 bg-slate-900/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10" />
                    </div>
                    <div>
                        <label for="smtp-port" class="block text-xs font-medium text-slate-200">Port</label>
                        <input id="smtp-port" name="smtp_port" type="number" value="<?= e($settings['smtp_port'] ?? '') ?>" class="mt-2 w-full rounded-md border-0 bg-slate-900/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10" />
                    </div>
                    <div>
                        <label for="smtp-secure" class="block text-xs font-medium text-slate-200">Sécurité</label>
                        <select id="smtp-secure" name="smtp_secure" class="mt-2 w-full rounded-md border-0 bg-slate-900/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10">
                            <?php foreach (['tls', 'ssl', 'none'] as $opt): ?>
                                <option value="<?= e($opt) ?>" <?= ($settings['smtp_secure'] ?? 'tls') === $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="smtp-user" class="block text-xs font-medium text-slate-200">Utilisateur</label>
                        <input id="smtp-user" name="smtp_username" type="text" value="<?= e($settings['smtp_username'] ?? '') ?>" class="mt-2 w-full rounded-md border-0 bg-slate-900/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10" />
                    </div>
                    <div>
                        <label for="smtp-pass" class="block text-xs font-medium text-slate-200">Mot de passe</label>
                        <input id="smtp-pass" name="smtp_password" type="password" value="" placeholder="<?= $hasSmtpPassword ? '******** (laisser vide pour conserver)' : '' ?>" class="mt-2 w-full rounded-md border-0 bg-slate-900/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10" />
                    </div>
                    <div>
                        <label for="smtp-from-email" class="block text-xs font-medium text-slate-200">Email expéditeur</label>
                        <input id="smtp-from-email" name="smtp_from_email" type="email" value="<?= e($settings['smtp_from_email'] ?? '') ?>" class="mt-2 w-full rounded-md border-0 bg-slate-900/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10" />
                    </div>
                    <div>
                        <label for="smtp-from-name" class="block text-xs font-medium text-slate-200">Nom expéditeur</label>
                        <input id="smtp-from-name" name="smtp_from_name" type="text" value="<?= e($settings['smtp_from_name'] ?? '') ?>" class="mt-2 w-full rounded-md border-0 bg-slate-900/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10" />
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-slate-800/40 p-4 ring-1 ring-white/10">
                <h2 class="text-sm font-semibold text-slate-100">Infobip (SMS + WhatsApp)</h2>
                <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="infobip-base" class="block text-xs font-medium text-slate-200">Base URL</label>
                        <input id="infobip-base" name="infobip_base_url" type="text" value="<?= e($settings['infobip_base_url'] ?? INFOBIP_BASE_URL) ?>" class="mt-2 w-full rounded-md border-0 bg-slate-900/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10" />
                    </div>
                    <div class="sm:col-span-2">
                        <label for="infobip-key" class="block text-xs font-medium text-slate-200">API Key</label>
                        <input id="infobip-key" name="infobip_api_key" type="password" value="" placeholder="<?= $hasInfobipKey ? '******** (laisser vide pour conserver)' : '' ?>" class="mt-2 w-full rounded-md border-0 bg-slate-900/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10" />
                    </div>
                    <div>
                        <label for="infobip-sms" class="block text-xs font-medium text-slate-200">Sender SMS</label>
                        <input id="infobip-sms" name="infobip_sms_sender" type="text" value="<?= e($settings['infobip_sms_sender'] ?? '') ?>" class="mt-2 w-full rounded-md border-0 bg-slate-900/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10" />
                    </div>
                    <div>
                        <label for="infobip-wa" class="block text-xs font-medium text-slate-200">Sender WhatsApp</label>
                        <input id="infobip-wa" name="infobip_whatsapp_sender" type="text" value="<?= e($settings['infobip_whatsapp_sender'] ?? '') ?>" class="mt-2 w-full rounded-md border-0 bg-slate-900/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10" />
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button class="rounded-md bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Enregistrer</button>
                <a href="?r=dashboard" class="text-sm font-semibold text-slate-300 hover:text-white">Retour</a>
            </div>
        </form>
    </div>

    <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
        <h2 class="text-base font-semibold text-white">Tests</h2>
        <p class="mt-1 text-sm text-slate-400">Les tests sont mis en file d'attente. Lancez ensuite le worker CLI.</p>

        <div class="mt-4 flex flex-col gap-3">
            <form method="post" action="?r=notifications-test-email" class="flex flex-wrap items-center gap-3">
                <?= csrf_field() ?>
                <button class="rounded-md bg-slate-800 px-3 py-2 text-sm font-semibold text-slate-100 ring-1 ring-white/10 hover:bg-slate-700">Envoyer email test</button>
            </form>
            <form method="post" action="?r=notifications-test-sms" class="flex flex-wrap items-center gap-3">
                <?= csrf_field() ?>
                <button class="rounded-md bg-slate-800 px-3 py-2 text-sm font-semibold text-slate-100 ring-1 ring-white/10 hover:bg-slate-700">Envoyer SMS test</button>
            </form>
            <form method="post" action="?r=notifications-test-whatsapp" class="flex flex-wrap items-center gap-3">
                <?= csrf_field() ?>
                <select name="template_id" class="rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10">
                    <option value="">Modèle WhatsApp pour test</option>
                    <?php foreach ($templates as $tpl): ?>
                        <option value="<?= e($tpl['id']) ?>"><?= e($tpl['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="rounded-md bg-slate-800 px-3 py-2 text-sm font-semibold text-slate-100 ring-1 ring-white/10 hover:bg-slate-700">Envoyer WhatsApp test</button>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
?>
