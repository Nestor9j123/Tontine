<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#2563eb">
        <meta name="apple-mobile-web-app-capable" content="yes">
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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
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
                        {{ $slot }}
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
    </div>

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

    </script>
</body>
</html>
