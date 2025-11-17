<!-- Mobile sidebar overlay -->
<div x-show="sidebarOpen" 
     @click="sidebarOpen = false"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden"
     style="display: none;"></div>

<!-- Sidebar -->
<aside 
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
    class="w-64 bg-gradient-to-b from-gray-900 to-gray-800 text-white flex-shrink-0 flex flex-col fixed inset-y-0 left-0 z-30 transform transition-transform duration-300 ease-in-out h-screen"
    style="display: flex;">
    <!-- Logo -->
    <div class="flex items-center justify-start px-4 py-6 bg-gray-900 border-b border-gray-700">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 w-full">
            <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-yellow-500 rounded-xl flex items-center justify-center shadow-lg">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h1 class="text-xl font-bold text-white leading-tight">
                    {{ $themeVars['company_name'] ?? config('app.name', 'Tontine App') }}
                </h1>
                <p class="text-blue-200 text-sm mt-1">Gestion de Tontines</p>
            </div>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4 px-3 scrollbar-hide">
        <div class="space-y-1">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" 
               class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-blue-600 to-yellow-500 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>

            @can('view_clients')
            <!-- Clients -->
            <a href="{{ route('clients.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('clients.*') ? 'bg-gradient-to-r from-blue-600 to-yellow-500 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span class="font-medium">Clients</span>
            </a>
            @endcan

            @can('view_tontines')
            <!-- Tontines -->
            <a href="{{ route('tontines.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('tontines.*') ? 'bg-gradient-to-r from-blue-600 to-yellow-500 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="font-medium">Tontines</span>
            </a>
            @endcan

            @can('view_payments')
            <!-- Paiements -->
            <a href="{{ route('payments.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('payments.*') ? 'bg-gradient-to-r from-blue-600 to-yellow-500 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span class="font-medium">Paiements</span>
                @if(auth()->user()->hasRole('secretary') || auth()->user()->hasRole('super_admin'))
                    @php
                        $pendingCount = \App\Models\Payment::pending()->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">{{ $pendingCount }}</span>
                    @endif
                @endif
            </a>
            @endcan

            <!-- Messages -->
            <a href="{{ route('chat.index') }}" @click="closeMobileSidebar()" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('chat.*') ? 'bg-gradient-to-r from-blue-600 to-yellow-500 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <span class="font-medium">Messages</span>
                @php
                    $unreadMessagesCount = auth()->user()->conversations()
                        ->get()
                        ->sum(function($conversation) {
                            return $conversation->getUnreadCount(auth()->user());
                        });
                @endphp
                <span id="unread-messages-badge" class="ml-auto bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded-full" 
                      style="{{ $unreadMessagesCount > 0 ? '' : 'display: none;' }}">
                    <span id="unread-count">{{ $unreadMessagesCount }}</span>
                </span>
            </a>

            <!-- Notifications -->
            <a href="{{ route('notifications.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('notifications.*') ? 'bg-gradient-to-r from-blue-600 to-yellow-500 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <span class="font-medium">Notifications</span>
                @php
                    $unreadNotificationsCount = 0;
                    if (auth()->check()) {
                        if (auth()->user()->hasRole('agent')) {
                            // Pour les agents : leurs notifications + notifications générales
                            $unreadNotificationsCount = \App\Models\TontineNotification::unread()
                                ->where(function($query) {
                                    $query->where('agent_id', auth()->id())
                                          ->orWhereNull('agent_id'); // Notifications générales
                                })
                                ->count();
                        } else {
                            // Pour admin/secretary : toutes les notifications
                            $unreadNotificationsCount = \App\Models\TontineNotification::unread()->count();
                        }
                    }
                @endphp
                @if($unreadNotificationsCount > 0)
                    <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full animate-pulse">
                        {{ $unreadNotificationsCount }}
                    </span>
                @endif
            </a>

            @can('view_products')
            <!-- Produits -->
            <a href="{{ route('products.index') }}" @click="closeMobileSidebar()" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('products.*') ? 'bg-gradient-to-r from-blue-600 to-yellow-500 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <span class="font-medium">Produits</span>
                @if(auth()->user()->hasRole(['super_admin', 'secretary']))
                @php
                    $threshold = \App\Models\SystemSetting::get('low_stock_threshold', 10);
                    $lowStockCount = \App\Models\Product::where('is_active', true)
                        ->where(function($query) use ($threshold) {
                            $query->where('stock_quantity', '<=', $threshold);
                        })
                        ->count();
                @endphp
                @if($lowStockCount > 0)
                    <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full animate-pulse" 
                          title="{{ $lowStockCount }} produit(s) en stock faible/rupture">
                        ⚠️ {{ $lowStockCount }}
                    </span>
                @endif
                @endif
            </a>
            @endcan

            @if(auth()->user()->hasRole('super_admin'))
            <!-- Utilisateurs / Agents (Super Admin uniquement) -->
            <a href="{{ route('users.index') }}" @click="closeMobileSidebar()" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('users.*') ? 'bg-gradient-to-r from-blue-600 to-yellow-500 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span class="font-medium">Utilisateurs</span>
            </a>

            <!-- Paramètres Système (Super Admin uniquement) -->
            <a href="{{ route('system-settings.index') }}" @click="closeMobileSidebar()" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('system-settings.*') || request()->routeIs('admin.*') ? 'bg-gradient-to-r from-blue-600 to-yellow-500 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="font-medium">Paramètres Système</span>
            </a>
            @endif

            @can('view_reports')
            <!-- Rapports -->
            <a href="{{ route('reports.index') }}" @click="closeMobileSidebar()" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('reports.index') ? 'bg-gradient-to-r from-blue-600 to-yellow-500 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="font-medium">Rapports</span>
            </a>

            <!-- Rapports Avancés -->
            <a href="{{ route('reports.advanced') }}" @click="closeMobileSidebar()" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('reports.advanced*') ? 'bg-gradient-to-r from-blue-600 to-yellow-500 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="font-medium">Rapports Avancés</span>
            </a>
            @endcan
            
            @if(auth()->user()->hasRole('secretary') || auth()->user()->hasRole('super_admin'))
            <!-- Gestion de Stock -->
            <a href="{{ route('stock.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('stock.*') ? 'bg-gradient-to-r from-blue-600 to-yellow-500 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <span class="font-medium">Stock</span>
            </a>
            
            <!-- Classement Agents -->
            <a href="{{ route('agents.ranking') }}" 
               class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('agents.ranking') ? 'bg-gradient-to-r from-blue-600 to-yellow-500 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <span class="font-medium">Classement</span>
            </a>
            @endif

            @can('view expenses')
            <!-- Charges Mensuelles -->
            <a href="{{ route('expenses.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('expenses.*') ? 'bg-gradient-to-r from-blue-600 to-yellow-500 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span class="font-medium">Charges Mensuelles</span>
            </a>
            @endcan

            @if(auth()->user()->hasRole('agent') || auth()->user()->can('view agent-receipts'))
            <!-- Reçus d'Agents / Mes Reçus -->
            <a href="{{ route('agent-receipts.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('agent-receipts.*') ? 'bg-gradient-to-r from-blue-600 to-yellow-500 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="font-medium">
                    @if(auth()->user()->hasRole('agent'))
                        Mes Reçus
                    @else
                        Reçus d'Agents
                    @endif
                </span>
            </a>
            @endif

            @can('view monthly-report')
            <!-- Rapports Mensuels -->
            <a href="{{ route('monthly-reports.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('monthly-reports.*') ? 'bg-gradient-to-r from-blue-600 to-yellow-500 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="font-medium">Rapports Mensuels</span>
            </a>
            @endcan
        </div>

        <!-- Divider -->
        <div class="my-4 border-t border-gray-700"></div>

        <!-- User Info -->
        <div class="px-4 py-3 bg-gray-800 rounded-lg">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-yellow-500 flex items-center justify-center text-white font-bold text-lg">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400">
                        @if(auth()->user()->hasRole('super_admin'))
                            Super Admin
                        @elseif(auth()->user()->hasRole('secretary'))
                            Secrétaire
                        @else
                            Agent
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </nav>

    <!-- Logout -->
    <div class="p-3 border-t border-gray-700">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center w-full px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span class="font-medium">Déconnexion</span>
            </button>
        </form>
    </div>
</aside>
