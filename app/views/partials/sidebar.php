<?php
$nav = [
    [
        'route' => 'dashboard',
        'label' => 'Tableau de bord',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />'
    ],
    [
        'route' => 'patients',
        'label' => 'Patients',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z" />'
    ],
    [
        'route' => 'appointments',
        'label' => 'Rendez-vous',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />'
    ],
    [
        'route' => 'appointments-pending',
        'label' => 'Demandes',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3h9m-9 3h5.25M3.75 6h16.5A1.5 1.5 0 0121.75 7.5v9a1.5 1.5 0 01-1.5 1.5H6l-3.75 3v-3H3.75A1.5 1.5 0 012.25 16.5v-9A1.5 1.5 0 013.75 6z" />'
    ],
    [
        'route' => 'services',
        'label' => 'Services',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 0015 0 7.5 7.5 0 00-15 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6" />'
    ],

    [
        'route' => 'payments',
        'label' => 'Paiements (Espèces)',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />'
    ],
    [
        'route' => 'cash',
        'label' => 'Caisse',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m4-2a8 8 0 11-16 0 8 8 0 0116 0z" />'
    ],
    [
        'route' => 'reports',
        'label' => 'Rapports',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" /><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />'
    ],
    [
        'route' => 'reminders-templates',
        'label' => 'Rappels',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 13.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5a7.5 7.5 0 10-15 0v2.25l-1.5 1.5h18l-1.5-1.5V13.5z" /><path stroke-linecap="round" stroke-linejoin="round" d="M9 19.5a3 3 0 006 0" />'
    ],
    [
        'route' => 'import',
        'label' => 'Import CSV',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5V18a2.25 2.25 0 002.25 2.25h13.5A2.25 2.25 0 0021 18v-1.5M12 3v13.5m0 0l-3.75-3.75M12 16.5l3.75-3.75" />'
    ],
    [
        'route' => 'notifications-settings',
        'label' => 'Notifications',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />'
    ],
    [
        'route' => 'profile',
        'label' => 'Paramètres',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />'
    ],
];
$current = $_GET['r'] ?? 'dashboard';
?>
<div class="print-hidden">
    <div class="relative z-50 lg:hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-950/80"></div>
        <div class="fixed inset-0 flex">
            <div class="relative mr-16 flex w-full max-w-xs flex-1">
                <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                    <button type="button" class="-m-2.5 p-2.5">
                        <span class="sr-only">Fermer le menu</span>
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-slate-950 px-6 pb-4 ring-1 ring-white/10">
                    <div class="flex h-16 shrink-0 items-center text-lg font-semibold text-white">
                        <?= e(APP_NAME) ?>
                    </div>
                    <nav class="flex flex-1 flex-col">
                        <ul role="list" class="flex flex-1 flex-col gap-y-7">
                            <li>
                                <ul role="list" class="-mx-2 space-y-1">
                                    <?php foreach ($nav as $item): ?>
                                        <?php $isActive = $current === $item['route']; ?>
                                        <li>
                                            <a href="?r=<?= e($item['route']) ?>" class="<?= $isActive ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' ?> group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6">
                                                <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                                    <?= $item['icon'] ?>
                                                </svg>
                                                <?= e($item['label']) ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
        <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-slate-950 px-6 pb-4">
            <div class="flex h-16 shrink-0 items-center text-lg font-semibold text-white">
                <?= e(APP_NAME) ?>
            </div>
            <nav class="flex flex-1 flex-col">
                <ul role="list" class="flex flex-1 flex-col gap-y-7">
                    <li>
                        <ul role="list" class="-mx-2 space-y-1">
                            <?php foreach ($nav as $item): ?>
                                <?php $isActive = $current === $item['route']; ?>
                                <li>
                                    <a href="?r=<?= e($item['route']) ?>" class="<?= $isActive ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' ?> group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6">
                                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                            <?= $item['icon'] ?>
                                        </svg>
                                        <?= e($item['label']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li class="mt-auto">
                        <a href="?r=logout" class="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-slate-400 hover:bg-slate-800 hover:text-white">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9l3 3m0 0l-3 3m3-3H9" />
                            </svg>
                            Déconnexion
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>
