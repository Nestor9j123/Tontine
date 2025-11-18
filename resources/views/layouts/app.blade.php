<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#2563eb">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <link rel="manifest" href="/manifest.json">
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
        <!-- Notifications centrées -->
        <div id="toast-container" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 space-y-2">
            <!-- Messages de session -->
            @if(session('success'))
                <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg animate-bounce">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ session('success') }}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-green-200 hover:text-white">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif
            
            @if(session('error'))
                <div class="bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg animate-bounce">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ session('error') }}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-red-200 hover:text-white">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif
        </div>
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
        
        // Fonctions de notification JavaScript globales
        window.showSuccess = function(title, message = '') {
            const toast = createToast('success', title, message);
            showToast(toast);
        };
        
        window.showError = function(title, message = '') {
            const toast = createToast('error', title, message);
            showToast(toast);
        };
        
        window.showInfo = function(title, message = '') {
            const toast = createToast('info', title, message);
            showToast(toast);
        };
        
        window.showWarning = function(title, message = '') {
            const toast = createToast('warning', title, message);
            showToast(toast);
        };
        
        function createToast(type, title, message) {
            const colors = {
                success: { bg: 'bg-green-500', icon: 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z' },
                error: { bg: 'bg-red-500', icon: 'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z' },
                info: { bg: 'bg-blue-500', icon: 'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z' },
                warning: { bg: 'bg-yellow-500', icon: 'M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z' }
            };
            
            const color = colors[type] || colors.info;
            const fullMessage = message ? `${title}: ${message}` : title;
            
            return `
                <div class="${color.bg} text-white px-6 py-3 rounded-lg shadow-lg toast-item" style="animation: slideInDown 0.5s ease-out">
                    <div class="flex items-center max-w-md">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="${color.icon}" clip-rule="evenodd"></path>
                        </svg>
                        <span class="flex-1 text-sm">${fullMessage}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white/70 hover:text-white flex-shrink-0">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
        }
        
        function showToast(toastHtml) {
            const container = document.getElementById('toast-container');
            if (container) {
                container.insertAdjacentHTML('beforeend', toastHtml);
                
                // Auto-remove après 5 secondes
                const toastElements = container.querySelectorAll('.toast-item');
                const lastToast = toastElements[toastElements.length - 1];
                if (lastToast) {
                    setTimeout(() => {
                        if (lastToast.parentNode) {
                            lastToast.style.animation = 'slideOutUp 0.5s ease-in';
                            setTimeout(() => lastToast.remove(), 500);
                        }
                    }, 5000);
                }
            }
        }
        
        // Auto-hide session messages
        document.addEventListener('DOMContentLoaded', function() {
            const sessionMessages = document.querySelectorAll('#toast-container > div:not(.toast-item)');
            sessionMessages.forEach(message => {
                setTimeout(() => {
                    if (message.parentNode) {
                        message.style.animation = 'slideOutUp 0.5s ease-in';
                        setTimeout(() => message.remove(), 500);
                    }
                }, 5000);
            });
        });

    </script>
    
    <style>
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translate(-50%, -100%);
            }
            to {
                opacity: 1;
                transform: translate(-50%, 0);
            }
        }
        
        @keyframes slideOutUp {
            from {
                opacity: 1;
                transform: translate(-50%, 0);
            }
            to {
                opacity: 0;
                transform: translate(-50%, -100%);
            }
        }
        
        #toast-container .toast-item {
            transform: translateX(-50%);
        }
    </style>

    <!-- PWA Service Worker -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/service-worker.js');
            });
        }

        // PWA Install Prompt
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            
            // Créer un bouton d'installation si pas déjà présent
            if (!document.querySelector('#pwa-install-btn')) {
                const installBtn = document.createElement('button');
                installBtn.id = 'pwa-install-btn';
                installBtn.innerHTML = '📱 Installer l\'app';
                installBtn.className = 'fixed bottom-4 right-4 bg-blue-600 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-blue-700 transition-colors z-50';
                installBtn.onclick = async () => {
                    if (deferredPrompt) {
                        deferredPrompt.prompt();
                        const { outcome } = await deferredPrompt.userChoice;
                        deferredPrompt = null;
                        installBtn.remove();
                    }
                };
                document.body.appendChild(installBtn);
                
                // Masquer le bouton après 10 secondes
                setTimeout(() => {
                    if (installBtn && installBtn.parentNode) {
                        installBtn.remove();
                    }
                }, 10000);
            }
        });

        // Cacher le bouton si l'app est déjà installée
        window.addEventListener('appinstalled', (evt) => {
            const installBtn = document.querySelector('#pwa-install-btn');
            if (installBtn) {
                installBtn.remove();
            }
        });

        // Indicateur de statut en ligne/hors ligne
        function createOnlineIndicator() {
            const indicator = document.createElement('div');
            indicator.id = 'online-indicator';
            indicator.className = 'fixed top-4 left-4 px-3 py-1 rounded-full text-xs font-medium z-50 transition-all duration-300';
            
            function updateStatus() {
                if (navigator.onLine) {
                    indicator.className = 'fixed top-4 left-4 px-3 py-1 rounded-full text-xs font-medium z-50 transition-all duration-300 bg-green-500 text-white';
                    indicator.innerHTML = '● En ligne';
                } else {
                    indicator.className = 'fixed top-4 left-4 px-3 py-1 rounded-full text-xs font-medium z-50 transition-all duration-300 bg-red-500 text-white animate-pulse';
                    indicator.innerHTML = '● Hors ligne';
                }
            }
            
            updateStatus();
            document.body.appendChild(indicator);
            
            // Masquer après 3 secondes si en ligne
            if (navigator.onLine) {
                setTimeout(() => {
                    if (indicator && indicator.parentNode && navigator.onLine) {
                        indicator.style.opacity = '0';
                        setTimeout(() => {
                            if (indicator && indicator.parentNode) {
                                indicator.remove();
                            }
                        }, 300);
                    }
                }, 3000);
            }
            
            window.addEventListener('online', updateStatus);
            window.addEventListener('offline', updateStatus);
        }

        // Créer l'indicateur au chargement
        document.addEventListener('DOMContentLoaded', createOnlineIndicator);

        // Gérer la synchronisation en background (quand on revient en ligne)
        window.addEventListener('online', () => {
            // Ici on pourrait ajouter la logique de synchronisation des données en attente
        });
    </script>
</body>
</html>
