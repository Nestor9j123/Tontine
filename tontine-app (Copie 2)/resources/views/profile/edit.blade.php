<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                👤 Mon Profil
            </h2>
            <div class="flex items-center space-x-3 text-sm text-gray-600">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold text-sm mr-2">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <span class="font-medium">{{ auth()->user()->name }}</span>
                </div>
                @if(auth()->user()->roles->first())
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                        {{ ucfirst(auth()->user()->roles->first()->name) }}
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-8">
        <!-- En-tête du profil -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl shadow-xl overflow-hidden">
            <div class="px-8 py-12 text-white">
                <div class="flex items-center space-x-6">
                    <div class="w-20 h-20 rounded-full bg-white bg-opacity-20 backdrop-blur-sm flex items-center justify-center text-white font-bold text-2xl border-4 border-white border-opacity-30">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">{{ auth()->user()->name }}</h1>
                        <p class="text-blue-100 text-lg">{{ auth()->user()->email }}</p>
                        @if(auth()->user()->roles->first())
                            <div class="mt-2">
                                <span class="px-3 py-1 bg-white bg-opacity-20 backdrop-blur-sm rounded-full text-sm font-medium">
                                    {{ ucfirst(auth()->user()->roles->first()->name) }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Grille des sections -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Informations du profil -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Informations Personnelles
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Modifiez vos informations de base</p>
                </div>
                <div class="p-6">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Sécurité -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-orange-50 to-red-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Sécurité du Compte
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Changez votre mot de passe</p>
                </div>
                <div class="p-6">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>

        <!-- Statistiques personnelles (si agent) -->
        @if(auth()->user()->hasRole('agent'))
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Mes Performances
                </h3>
                <p class="text-sm text-gray-600 mt-1">Vos statistiques de collecte</p>
            </div>
            <div class="p-6">
                @php
                    $totalCollected = \App\Models\Payment::where('collected_by', auth()->id())->where('status', 'validated')->sum('amount');
                    $totalPayments = \App\Models\Payment::where('collected_by', auth()->id())->count();
                    $thisMonthCollected = \App\Models\Payment::where('collected_by', auth()->id())->where('status', 'validated')->whereMonth('payment_date', now()->month)->sum('amount');
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center p-4 bg-green-50 rounded-xl">
                        <div class="text-2xl font-bold text-green-600">{{ number_format($totalCollected, 0, ',', ' ') }}</div>
                        <div class="text-sm text-green-700 font-medium">FCFA Collectés</div>
                        <div class="text-xs text-green-600">Total validé</div>
                    </div>
                    <div class="text-center p-4 bg-blue-50 rounded-xl">
                        <div class="text-2xl font-bold text-blue-600">{{ $totalPayments }}</div>
                        <div class="text-sm text-blue-700 font-medium">Paiements</div>
                        <div class="text-xs text-blue-600">Total enregistrés</div>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-xl">
                        <div class="text-2xl font-bold text-purple-600">{{ number_format($thisMonthCollected, 0, ',', ' ') }}</div>
                        <div class="text-sm text-purple-700 font-medium">FCFA ce mois</div>
                        <div class="text-xs text-purple-600">{{ now()->format('F Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Zone dangereuse -->
        <div class="bg-white rounded-2xl shadow-lg border border-red-200 overflow-hidden">
            <div class="bg-gradient-to-r from-red-50 to-pink-50 px-6 py-4 border-b border-red-200">
                <h3 class="text-lg font-semibold text-red-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    Zone Dangereuse
                </h3>
                <p class="text-sm text-red-600 mt-1">Actions irréversibles sur votre compte</p>
            </div>
            <div class="p-6">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
