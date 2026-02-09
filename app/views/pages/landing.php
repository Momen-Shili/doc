<?php $isLandingPage = true; ?>
<?php ob_start(); ?>
<!-- Hero Section -->
<div class="relative overflow-hidden bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900">
    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0icmdiYSgyNTUsMjU1LDI1NSwwLjAzKSIgc3Ryb2tlLXdpZHRoPSIxIi8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI2dyaWQpIi8+PC9zdmc+')] opacity-40"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-24 sm:pt-32 sm:pb-32">
        <div class="text-center">
            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold text-white tracking-tight">
                Gérez votre cabinet
                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-400">en toute simplicité</span>
            </h1>
            <p class="mt-6 max-w-2xl mx-auto text-xl sm:text-2xl text-slate-300">
                La solution complète pour la gestion de votre cabinet médical. Agenda, dossiers patients, facturation et bien plus encore.
            </p>
            <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="?r=register" class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-blue-600 to-cyan-600 text-white text-lg font-semibold rounded-lg hover:from-blue-700 hover:to-cyan-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    Créer un compte
                </a>
                <a href="?r=login" class="w-full sm:w-auto px-8 py-4 bg-slate-700 text-white text-lg font-semibold rounded-lg hover:bg-slate-600 transition-all duration-200 border border-slate-600">
                    Se connecter
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Social Proof Section -->
<div class="bg-slate-900 py-16 border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-400">500+</div>
                <div class="mt-2 text-slate-400">Cabinets utilisateurs</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-400">50k+</div>
                <div class="mt-2 text-slate-400">Patients gérés</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-400">99.9%</div>
                <div class="mt-2 text-slate-400">Disponibilité</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-400">24/7</div>
                <div class="mt-2 text-slate-400">Support client</div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="bg-slate-950 py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-5xl font-bold text-white mb-4">Fonctionnalités complètes</h2>
            <p class="text-xl text-slate-400 max-w-2xl mx-auto">Tout ce dont vous avez besoin pour gérer votre cabinet efficacement</p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="bg-slate-900 rounded-xl p-8 border border-slate-800 hover:border-blue-500 transition-all duration-300 hover:shadow-xl hover:shadow-blue-500/10">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-3">Agenda intelligent</h3>
                <p class="text-slate-400">Planifiez et gérez vos rendez-vous avec un calendrier intuitif et des rappels automatiques.</p>
            </div>

            <!-- Feature 2 -->
            <div class="bg-slate-900 rounded-xl p-8 border border-slate-800 hover:border-blue-500 transition-all duration-300 hover:shadow-xl hover:shadow-blue-500/10">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-3">Dossiers patients</h3>
                <p class="text-slate-400">Centralisez toutes les informations médicales de vos patients de manière sécurisée.</p>
            </div>

            <!-- Feature 3 -->
            <div class="bg-slate-900 rounded-xl p-8 border border-slate-800 hover:border-blue-500 transition-all duration-300 hover:shadow-xl hover:shadow-blue-500/10">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-3">Facturation automatique</h3>
                <p class="text-slate-400">Générez vos factures et suivez vos paiements en quelques clics.</p>
            </div>

            <!-- Feature 4 -->
            <div class="bg-slate-900 rounded-xl p-8 border border-slate-800 hover:border-blue-500 transition-all duration-300 hover:shadow-xl hover:shadow-blue-500/10">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-3">Statistiques détaillées</h3>
                <p class="text-slate-400">Analysez l'activité de votre cabinet avec des tableaux de bord personnalisés.</p>
            </div>

            <!-- Feature 5 -->
            <div class="bg-slate-900 rounded-xl p-8 border border-slate-800 hover:border-blue-500 transition-all duration-300 hover:shadow-xl hover:shadow-blue-500/10">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-3">Sécurité maximale</h3>
                <p class="text-slate-400">Vos données sont chiffrées et sauvegardées quotidiennement. Conformité RGPD garantie.</p>
            </div>

            <!-- Feature 6 -->
            <div class="bg-slate-900 rounded-xl p-8 border border-slate-800 hover:border-blue-500 transition-all duration-300 hover:shadow-xl hover:shadow-blue-500/10">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-3">Application mobile</h3>
                <p class="text-slate-400">Accédez à votre cabinet depuis n'importe où, sur tous vos appareils.</p>
            </div>
        </div>
    </div>
</div>

<!-- Screenshots Section -->
<div class="bg-slate-900 py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-5xl font-bold text-white mb-4">Interface intuitive</h2>
            <p class="text-xl text-slate-400 max-w-2xl mx-auto">Une expérience utilisateur pensée pour les professionnels de santé</p>
        </div>
        
        <div class="grid md:grid-cols-2 gap-8">
            <div class="bg-slate-800 rounded-xl p-4 border border-slate-700">
                <div class="bg-slate-950 rounded-lg aspect-video flex items-center justify-center border border-slate-700">
                    <div class="text-center">
                        <svg class="w-16 h-16 mx-auto text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-slate-500 font-medium">Tableau de bord</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-slate-800 rounded-xl p-4 border border-slate-700">
                <div class="bg-slate-950 rounded-lg aspect-video flex items-center justify-center border border-slate-700">
                    <div class="text-center">
                        <svg class="w-16 h-16 mx-auto text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-slate-500 font-medium">Gestion des patients</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pricing Section -->
<div class="bg-slate-950 py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-5xl font-bold text-white mb-4">Tarifs transparents</h2>
            <p class="text-xl text-slate-400 max-w-2xl mx-auto">Choisissez le plan qui correspond à vos besoins</p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            <!-- Starter Plan -->
            <div class="bg-slate-900 rounded-2xl p-8 border border-slate-800 hover:border-blue-500 transition-all duration-300">
                <div class="mb-8">
                    <h3 class="text-2xl font-bold text-white mb-2">Starter</h3>
                    <p class="text-slate-400">Pour les cabinets individuels</p>
                </div>
                <div class="mb-8">
                    <span class="text-5xl font-bold text-white">29€</span>
                    <span class="text-slate-400">/mois</span>
                </div>
                <ul class="space-y-4 mb-8">
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-slate-300">Jusqu'à 100 patients</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-slate-300">Agenda en ligne</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-slate-300">Facturation basique</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-slate-300">Support par email</span>
                    </li>
                </ul>
                <a href="?r=register" class="block w-full py-3 px-6 text-center bg-slate-800 text-white font-semibold rounded-lg hover:bg-slate-700 transition-colors duration-200">
                    Commencer
                </a>
            </div>

            <!-- Pro Plan -->
            <div class="bg-gradient-to-b from-blue-600 to-cyan-600 rounded-2xl p-8 transform scale-105 shadow-2xl relative">
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-gradient-to-r from-yellow-400 to-orange-400 text-slate-900 px-4 py-1 rounded-full text-sm font-bold">
                    POPULAIRE
                </div>
                <div class="mb-8">
                    <h3 class="text-2xl font-bold text-white mb-2">Pro</h3>
                    <p class="text-blue-100">Pour les cabinets en croissance</p>
                </div>
                <div class="mb-8">
                    <span class="text-5xl font-bold text-white">79€</span>
                    <span class="text-blue-100">/mois</span>
                </div>
                <ul class="space-y-4 mb-8">
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-white mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-white">Patients illimités</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-white mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-white">Toutes les fonctionnalités Starter</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-white mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-white">Statistiques avancées</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-white mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-white">Application mobile</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-white mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-white">Support prioritaire</span>
                    </li>
                </ul>
                <a href="?r=register" class="block w-full py-3 px-6 text-center bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors duration-200">
                    Commencer
                </a>
            </div>

            <!-- Clinic Plan -->
            <div class="bg-slate-900 rounded-2xl p-8 border border-slate-800 hover:border-blue-500 transition-all duration-300">
                <div class="mb-8">
                    <h3 class="text-2xl font-bold text-white mb-2">Clinic</h3>
                    <p class="text-slate-400">Pour les cliniques et groupes</p>
                </div>
                <div class="mb-8">
                    <span class="text-5xl font-bold text-white">199€</span>
                    <span class="text-slate-400">/mois</span>
                </div>
                <ul class="space-y-4 mb-8">
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-slate-300">Toutes les fonctionnalités Pro</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-slate-300">Utilisateurs illimités</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-slate-300">Multi-cabinets</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-slate-300">API personnalisée</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-slate-300">Support dédié 24/7</span>
                    </li>
                </ul>
                <a href="?r=register" class="block w-full py-3 px-6 text-center bg-slate-800 text-white font-semibold rounded-lg hover:bg-slate-700 transition-colors duration-200">
                    Commencer
                </a>
            </div>
        </div>
    </div>
</div>

<!-- FAQ Section -->
<div class="bg-slate-900 py-24">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-5xl font-bold text-white mb-4">Questions fréquentes</h2>
            <p class="text-xl text-slate-400">Tout ce que vous devez savoir sur Cabinet SaaS</p>
        </div>
        
        <div class="space-y-6">
            <div class="bg-slate-800 rounded-lg p-6 border border-slate-700">
                <h3 class="text-xl font-semibold text-white mb-3">Comment démarrer avec Cabinet SaaS ?</h3>
                <p class="text-slate-400">Il vous suffit de créer un compte, de choisir votre plan et de commencer à ajouter vos patients. Notre interface intuitive vous guide pas à pas dans la configuration de votre cabinet.</p>
            </div>
            
            <div class="bg-slate-800 rounded-lg p-6 border border-slate-700">
                <h3 class="text-xl font-semibold text-white mb-3">Mes données sont-elles sécurisées ?</h3>
                <p class="text-slate-400">Absolument. Nous utilisons un chiffrement de niveau bancaire (AES-256) et nos serveurs sont hébergés en France. Nous sommes conformes au RGPD et effectuons des sauvegardes quotidiennes automatiques.</p>
            </div>
            
            <div class="bg-slate-800 rounded-lg p-6 border border-slate-700">
                <h3 class="text-xl font-semibold text-white mb-3">Puis-je changer de plan à tout moment ?</h3>
                <p class="text-slate-400">Oui, vous pouvez passer à un plan supérieur ou inférieur à tout moment. Les changements prennent effet immédiatement et nous ajustons votre facturation au prorata.</p>
            </div>
            
            <div class="bg-slate-800 rounded-lg p-6 border border-slate-700">
                <h3 class="text-xl font-semibold text-white mb-3">Y a-t-il une période d'essai gratuite ?</h3>
                <p class="text-slate-400">Oui, nous offrons 14 jours d'essai gratuit sur tous nos plans. Aucune carte bancaire n'est requise pour démarrer votre essai.</p>
            </div>
            
            <div class="bg-slate-800 rounded-lg p-6 border border-slate-700">
                <h3 class="text-xl font-semibold text-white mb-3">Quel type de support proposez-vous ?</h3>
                <p class="text-slate-400">Nous offrons un support par email pour tous les plans, un support prioritaire pour le plan Pro, et un support dédié 24/7 pour le plan Clinic. Notre équipe répond généralement en moins de 2 heures.</p>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-slate-950 border-t border-slate-800 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8 mb-8">
            <div>
                <h3 class="text-white font-bold text-lg mb-4">Cabinet SaaS</h3>
                <p class="text-slate-400 text-sm">La solution complète pour la gestion de votre cabinet médical.</p>
            </div>
            
            <div>
                <h4 class="text-white font-semibold mb-4">Produit</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="text-slate-400 hover:text-blue-400 transition-colors">Fonctionnalités</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-blue-400 transition-colors">Tarifs</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-blue-400 transition-colors">Démo</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-blue-400 transition-colors">Mises à jour</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="text-white font-semibold mb-4">Ressources</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="text-slate-400 hover:text-blue-400 transition-colors">Documentation</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-blue-400 transition-colors">Blog</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-blue-400 transition-colors">Guides</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-blue-400 transition-colors">Support</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="text-white font-semibold mb-4">Entreprise</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="text-slate-400 hover:text-blue-400 transition-colors">À propos</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-blue-400 transition-colors">Contact</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-blue-400 transition-colors">Confidentialité</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-blue-400 transition-colors">CGU</a></li>
                </ul>
            </div>
        </div>
        
        <div class="border-t border-slate-800 pt-8 text-center">
            <p class="text-slate-400 text-sm">&copy; 2024 Cabinet SaaS. Tous droits réservés.</p>
        </div>
    </div>
</footer>

<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php'; ?>