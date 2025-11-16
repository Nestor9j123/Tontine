<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#2563eb">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <link rel="manifest" href="/manifest.webmanifest">
        <link rel="apple-touch-icon" href="/icons/icon-192.png">
        

        @php
            $themeVars = [
                'company_name' => \App\Models\SystemSetting::get('company_name', config('app.name', 'Tontine App')),
                'primary_color' => \App\Models\SystemSetting::get('primary_color', '#3B82F6'),
                'secondary_color' => \App\Models\SystemSetting::get('secondary_color', '#10B981'),
                'theme_mode' => \App\Models\SystemSetting::get('theme_mode', 'light'),
                'low_stock_threshold' => \App\Models\SystemSetting::get('low_stock_threshold', 10),
            ];
        @endphp

        <title>{{ $themeVars['company_name'] ?? config('app.name', 'Tontine App') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- Chart.js UMD -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>

        <style>
            body { font-family: 'Inter', sans-serif; }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50" data-theme="{{ $themeVars['theme_mode'] ?? 'light' }}">
        <div x-data="{ 
            sidebarOpen: false,
            mobileMenuOpen: false,
            userMenuOpen: false,
            dropdownOpen: false,
            closeMobileSidebar() {
                if (window.innerWidth < 768) {
                    this.sidebarOpen = false;
                }
                return true; // Permet la navigation normale
            }
        }" class="min-h-screen">
            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Main Content -->
            <div class="md:ml-64 flex flex-col min-h-screen">
                <!-- Top Navigation -->
                @include('layouts.topbar')

                <!-- Page Content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 w-full">
                    <!-- Page Heading -->
                    @if (isset($header))
                        <div class="bg-white border-b border-gray-200 px-4 sm:px-6 py-3 sm:py-4">
                            {{ $header }}
                        </div>
                    @endif

                    <div class="p-4 sm:p-6">
                        @if(isset($slot))
                            {{ $slot }}
                        @else
                            @yield('content')
                        @endif
                    </div>
                </main>
            </div>
        </div>

        <!-- Système de notifications global -->
        <x-notification-system />
        
        <!-- Variables de thème dynamiques -->
        <x-theme-variables :theme-vars="$themeVars" />

        <!-- Notification de stock faible -->
        <x-low-stock-notification />

    <script>
        // Fonction globale pour mettre à jour le badge des messages non lus
        window.updateMessagesBadge = function(count) {
            const badge = document.getElementById('unread-messages-badge');
            const countSpan = document.getElementById('unread-count');
            
            if (badge && countSpan) {
                if (count > 0) {
                    badge.style.display = '';
                    countSpan.textContent = count;
                } else {
                    badge.style.display = 'none';
                }
            }
        };

        // Sauvegarde et restauration de la position de scroll
        window.saveScrollPosition = function() {
            sessionStorage.setItem('scrollPosition', window.pageYOffset || document.documentElement.scrollTop);
        };

        window.restoreScrollPosition = function() {
            const scrollPosition = sessionStorage.getItem('scrollPosition');
            if (scrollPosition) {
                window.scrollTo(0, parseInt(scrollPosition));
                sessionStorage.removeItem('scrollPosition');
            }
        };

        // Option pour désactiver l'AJAX (pour debug)
        window.disableAjaxNavigation = false;
        
        // Navigation AJAX simplifiée et rapide
        window.navigateWithAjax = function(url, element) {
            // Si AJAX désactivé, navigation normale
            if (window.disableAjaxNavigation) {
                window.location.href = url;
                return;
            }
            
            // Éviter les clics multiples
            if (window.isNavigating) {
                return;
            }
            window.isNavigating = true;
            
            // Mettre à jour immédiatement l'état visuel
            updateSidebarActiveStates(url);
            
            // Effectuer la requête AJAX avec timeout court
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 3000); // 3 secondes max
            
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                },
                signal: controller.signal
            })
            .then(response => {
                clearTimeout(timeoutId);
                if (!response.ok) {
                    throw new Error(`Erreur HTTP: ${response.status}`);
                }
                return response.text();
            })
            .then(html => {
                // Parser et remplacer le contenu
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.querySelector('main');
                const currentMain = document.querySelector('main');
                
                if (newContent && currentMain) {
                    currentMain.innerHTML = newContent.innerHTML;
                    
                    // Mettre à jour l'URL
                    history.pushState({}, '', url);
                    
                    // Réinitialiser Alpine.js si nécessaire
                    if (typeof Alpine !== 'undefined') {
                        Alpine.initTree(currentMain);
                    }
                } else {
                    throw new Error('Contenu principal non trouvé');
                }
            })
            .catch(error => {
                clearTimeout(timeoutId);
                console.error('Erreur de navigation AJAX:', error);
                // Fallback: navigation normale immédiate
                window.location.href = url;
            })
            .finally(() => {
                // Libérer le verrou de navigation rapidement
                window.isNavigating = false;
            });
        };

        // Mettre à jour les états actifs du sidebar (optimisé)
        window.updateSidebarActiveStates = function(currentUrl) {
            const currentPath = new URL(currentUrl, window.location.origin).pathname;
            
            // Traiter uniquement les liens avec data-sidebar-link
            const sidebarLinks = document.querySelectorAll('[data-sidebar-link]');
            
            sidebarLinks.forEach(link => {
                const href = link.getAttribute('href');
                const linkPath = new URL(href, window.location.origin).pathname;
                const isActive = currentPath === linkPath || (linkPath !== '/' && currentPath.startsWith(linkPath));
                
                if (isActive) {
                    // Activer ce lien
                    link.classList.remove('text-gray-300', 'hover:bg-gray-700', 'hover:text-white');
                    link.classList.add('bg-gradient-to-r', 'from-blue-600', 'to-yellow-500', 'text-white');
                } else {
                    // Désactiver ce lien
                    link.classList.remove('bg-gradient-to-r', 'from-blue-600', 'to-yellow-500', 'text-white');
                    link.classList.add('text-gray-300', 'hover:bg-gray-700', 'hover:text-white');
                }
            });
        };

        // Gestion du bouton retour du navigateur
        window.addEventListener('popstate', function(event) {
            // Recharger la page lors du retour arrière pour éviter les problèmes de state
            window.location.reload();
        });

        // Solution simple : remonter en haut seulement si on vient d'une autre page
        window.addEventListener('pageshow', function(event) {
            // Si c'est un nouveau chargement (pas depuis le cache)
            if (!event.persisted) {
                // Petit délai pour laisser la page se charger
                setTimeout(function() {
                    // Remonter en haut seulement si on n'est pas déjà en haut
                    if (window.pageYOffset > 100) {
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    }
                }, 200);
            }
        });

        // Fermer le sidebar mobile automatiquement lors des clics sur les liens
        document.addEventListener('click', function(e) {
            // Vérifier si c'est un lien dans le sidebar
            if (e.target.closest('nav a[href]')) {
                // Fermer le sidebar mobile si ouvert
                if (window.innerWidth < 768) {
                    const sidebarElement = document.querySelector('[x-data]');
                    if (sidebarElement && sidebarElement.__x) {
                        sidebarElement.__x.$data.sidebarOpen = false;
                    }
                }
            }
        });

        // Gestion globale des erreurs AJAX (session expirée, token CSRF invalide)
        document.addEventListener('DOMContentLoaded', function() {
            
            // Ajouter des transitions CSS pour le contenu principal
            const mainElement = document.querySelector('main');
            if (mainElement) {
                mainElement.style.transition = 'opacity 0.2s ease-in-out';
            }
            // Intercepter toutes les requêtes fetch pour gérer les erreurs d'authentification
            const originalFetch = window.fetch;
            window.fetch = function(...args) {
                return originalFetch.apply(this, args)
                    .then(response => {
                        // Si erreur 419 (Page Expired) ou 401 (Unauthorized)
                        if (response.status === 419 || response.status === 401) {
                            alert('Votre session a expiré. Vous allez être redirigé vers la page de connexion.');
                            window.location.href = '{{ route("login") }}';
                            return Promise.reject(new Error('Session expirée'));
                        }
                        return response;
                    })
                    .catch(error => {
                        console.error('Erreur de requête:', error);
                        throw error;
                    });
            };

            // Gestion des erreurs pour les formulaires AJAX avec jQuery (si utilisé)
            if (typeof $ !== 'undefined') {
                $(document).ajaxError(function(event, xhr, settings) {
                    if (xhr.status === 419 || xhr.status === 401) {
                        alert('Votre session a expiré. Vous allez être redirigé vers la page de connexion.');
                        window.location.href = '{{ route("login") }}';
                    }
                });
            }
        });

    </script>
</body>
</html>
