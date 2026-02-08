<?php
$title = 'Parametres';
ob_start();
$hasErrors = !empty($errors);
?>
<div class="max-w-3xl">
    <div class="rounded-2xl bg-slate-900/60 p-6 ring-1 ring-white/10">
        <h1 class="text-lg font-semibold text-white">Profil medecin</h1>
        <p class="mt-1 text-sm text-slate-400">Gerez vos informations personnelles et publiques.</p>

        <?php if ($hasErrors): ?>
            <div id="profile-error" class="mt-4 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200" role="alert">
                <?= e(implode(' ', $errors)) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="?r=profile" enctype="multipart/form-data" class="mt-6 space-y-8">
            <?= csrf_field() ?>
            <div>
                <h2 class="text-sm font-semibold text-slate-200">Profil interne</h2>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="profile-full-name" class="block text-sm font-medium text-slate-200">Nom complet <span class="text-rose-400">*</span></label>
                        <div class="mt-2">
                            <input id="profile-full-name" type="text" name="full_name" value="<?= e($doctor['full_name'] ?? '') ?>" required <?= $hasErrors ? 'aria-describedby="profile-error"' : '' ?> class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                        </div>
                    </div>
                    <div>
                        <label for="profile-specialty" class="block text-sm font-medium text-slate-200">Specialite</label>
                        <div class="mt-2">
                            <input id="profile-specialty" type="text" name="specialty" value="<?= e($doctor['specialty'] ?? '') ?>" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                        </div>
                    </div>
                    <div>
                        <label for="profile-phone" class="block text-sm font-medium text-slate-200">Telephone</label>
                        <div class="mt-2">
                            <input id="profile-phone" type="text" name="phone" value="<?= e($doctor['phone'] ?? '') ?>" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                        </div>
                    </div>
                    <div>
                        <label for="profile-email" class="block text-sm font-medium text-slate-200">Email</label>
                        <div class="mt-2">
                            <input id="profile-email" type="email" value="<?= e($doctor['email'] ?? '') ?>" disabled class="block w-full rounded-md border-0 bg-slate-800/40 px-3 py-2 text-sm text-slate-400 ring-1 ring-inset ring-white/5" />
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-sm font-semibold text-slate-200">Profil public</h2>
                <p class="mt-1 text-sm text-slate-400">Ces informations sont visibles sur votre page publique.</p>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="profile-city" class="block text-sm font-medium text-slate-200">Ville</label>
                        <div class="mt-2">
                            <input id="profile-city" type="text" name="city" value="<?= e($doctor['city'] ?? '') ?>" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                        </div>
                    </div>
                    <div>
                        <label for="profile-public-phone" class="block text-sm font-medium text-slate-200">Telephone public</label>
                        <div class="mt-2">
                            <input id="profile-public-phone" type="text" name="public_phone" value="<?= e($doctor['public_phone'] ?? '') ?>" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label for="profile-address" class="block text-sm font-medium text-slate-200">Adresse publique</label>
                        <div class="mt-2">
                            <input id="profile-address" type="text" name="address_public" value="<?= e($doctor['address_public'] ?? '') ?>" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500" />
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label for="profile-bio" class="block text-sm font-medium text-slate-200">Bio publique</label>
                        <div class="mt-2">
                            <textarea id="profile-bio" name="bio_public" rows="4" class="block w-full rounded-md border-0 bg-slate-800/70 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-indigo-500"><?= e($doctor['bio_public'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label for="profile-slug" class="block text-sm font-medium text-slate-200">Slug public (auto)</label>
                        <div class="mt-2">
                            <input id="profile-slug" type="text" value="<?= e($doctor['public_slug'] ?? '') ?>" disabled class="block w-full rounded-md border-0 bg-slate-800/40 px-3 py-2 text-sm text-slate-400 ring-1 ring-inset ring-white/5" />
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label for="profile-photo" class="block text-sm font-medium text-slate-200">Photo publique (JPG/PNG)</label>
                        <div class="mt-2 flex items-center gap-4">
                            <?php if (!empty($doctor['public_photo'])): ?>
                                <img src="<?= e($doctor['public_photo']) ?>" alt="Photo" class="h-16 w-16 rounded-full object-cover" />
                            <?php else: ?>
                                <div class="h-16 w-16 rounded-full bg-slate-800/70"></div>
                            <?php endif; ?>
                            <input id="profile-photo" type="file" name="public_photo" accept="image/jpeg,image/png" class="block text-sm text-slate-300" />
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <button type="button" id="geo-locate" class="inline-flex items-center rounded-md bg-slate-800 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-700">Localiser l'adresse</button>
                    <span id="geo-status" class="text-sm text-slate-400"></span>
                </div>

                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="profile-lat" class="block text-sm font-medium text-slate-200">Latitude</label>
                        <div class="mt-2">
                            <input id="profile-lat" type="text" name="latitude" value="<?= e($doctor['latitude'] ?? '') ?>" readonly class="block w-full rounded-md border-0 bg-slate-800/40 px-3 py-2 text-sm text-slate-400 ring-1 ring-inset ring-white/5" />
                        </div>
                    </div>
                    <div>
                        <label for="profile-lng" class="block text-sm font-medium text-slate-200">Longitude</label>
                        <div class="mt-2">
                            <input id="profile-lng" type="text" name="longitude" value="<?= e($doctor['longitude'] ?? '') ?>" readonly class="block w-full rounded-md border-0 bg-slate-800/40 px-3 py-2 text-sm text-slate-400 ring-1 ring-inset ring-white/5" />
                        </div>
                    </div>
                </div>
            </div>

            <button class="inline-flex items-center rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Enregistrer</button>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('geo-locate');
    const status = document.getElementById('geo-status');
    const city = document.getElementById('profile-city');
    const address = document.getElementById('profile-address');
    const lat = document.getElementById('profile-lat');
    const lng = document.getElementById('profile-lng');
    const csrf = document.querySelector('input[name="_csrf"]');

    if (!btn) return;
    btn.addEventListener('click', function () {
        status.textContent = 'Localisation en cours...';
        const formData = new FormData();
        formData.append('address_public', address.value || '');
        formData.append('city', city.value || '');
        if (csrf) {
            formData.append('_csrf', csrf.value);
        }
        fetch('?r=doctor-geocode-address', {
            method: 'POST',
            body: formData
        })
            .then((r) => r.json())
            .then((data) => {
                if (data.error) {
                    status.textContent = data.error;
                    return;
                }
                lat.value = data.lat || '';
                lng.value = data.lng || '';
                if (data.city && !city.value) {
                    city.value = data.city;
                }
                if (data.address && !address.value) {
                    address.value = data.address;
                }
                status.textContent = 'Adresse localisee.';
            })
            .catch(() => {
                status.textContent = 'Erreur de localisation.';
            });
    });
});
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
?>
