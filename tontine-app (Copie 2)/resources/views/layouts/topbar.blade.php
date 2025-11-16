<header class="bg-white border-b border-gray-200 h-14 sm:h-16 flex items-center justify-between px-3 sm:px-6">
    <div class="flex items-center">
        <!-- Mobile menu button -->
        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Breadcrumb -->
        <nav class="ml-2 sm:ml-4 flex items-center space-x-2 text-xs sm:text-sm">
            <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700">Accueil</a>
            @if(!request()->routeIs('dashboard'))
                <svg class="w-3 h-3 sm:w-4 sm:h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-gray-700 font-medium truncate max-w-[100px] sm:max-w-none">{{ ucfirst(request()->segment(1)) }}</span>
            @endif
        </nav>
    </div>

    <div class="flex items-center space-x-2 sm:space-x-4">
        <!-- Notifications -->
        <button class="relative text-gray-500 hover:text-gray-700 focus:outline-none hidden sm:block">
            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            @if(auth()->user()->hasRole('secretary') || auth()->user()->hasRole('super_admin'))
                @php
                    $pendingCount = \App\Models\Payment::pending()->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                @endif
            @endif
        </button>

        <!-- User Menu -->
        <div class="relative">
            <button @click="userMenuOpen = !userMenuOpen" class="flex items-center space-x-2 sm:space-x-3 focus:outline-none">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-gradient-to-r from-blue-500 to-yellow-500 flex items-center justify-center text-white font-bold text-sm">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="hidden md:block text-left">
                    <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                </div>
                <svg class="w-4 h-4 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <!-- Dropdown -->
            <div x-show="userMenuOpen" 
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 @click.away="userMenuOpen = false" 
                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50 border border-gray-200"
                 style="display: none;"
                 x-cloak>
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Mon Profil
                    </div>
                </a>
                <a href="{{ route('two-factor.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Sécurité 2FA
                        @if(auth()->user()->google2fa_enabled)
                            <span class="ml-auto w-2 h-2 bg-green-500 rounded-full"></span>
                        @else
                            <span class="ml-auto w-2 h-2 bg-red-500 rounded-full"></span>
                        @endif
                    </div>
                </a>
                <div class="border-t border-gray-100"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Déconnexion
                        </div>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
