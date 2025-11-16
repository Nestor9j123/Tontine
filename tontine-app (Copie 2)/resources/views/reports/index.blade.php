<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Rapports</h2>
            <div class="space-x-2">
                <a href="{{ route('reports.clients') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Clients</a>
                <a href="{{ route('reports.tontines') }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg">Tontines</a>
                <a href="{{ route('reports.payments') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg">Paiements</a>
                <a href="{{ route('reports.agents') }}" class="px-4 py-2 bg-orange-600 text-white rounded-lg">Agents</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Statistiques principales --}}
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Clients</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['clients'] }}</h3>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Agents</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['agents'] }}</h3>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-3">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Tontines actives</p>
                        <h3 class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['tontines_active'] }}</h3>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Tontines terminées</p>
                        <h3 class="text-2xl font-bold text-green-600 mt-1">{{ $stats['tontines_completed'] }}</h3>
                    </div>
                    <div class="bg-green-50 rounded-lg p-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Somme validée</p>
                        <h3 class="text-2xl font-bold text-yellow-600 mt-1">{{ number_format($stats['payments_sum'], 0, ',', ' ') }} FCFA</h3>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-3">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Paiements en attente</p>
                        <h3 class="text-2xl font-bold text-orange-600 mt-1">{{ $stats['payments_pending'] }}</h3>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-3">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistiques de stock --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 bg-gradient-to-r from-blue-50 to-yellow-50 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">État du stock</h3>
                    <a href="{{ route('stock.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        Voir détails
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-1">Produits en stock</p>
                        <p class="text-2xl font-bold text-blue-600">{{ \App\Models\Product::where('stock_quantity', '>', 0)->count() }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-1">Produits en rupture</p>
                        <p class="text-2xl font-bold text-red-600">{{ \App\Models\Product::where('stock_quantity', '<=', 0)->count() }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-1">Alertes stock bas</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ \App\Models\Product::whereRaw('stock_quantity <= min_stock_alert AND stock_quantity > 0')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Performance des agents --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 bg-gradient-to-r from-blue-50 to-yellow-50 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Performance des agents</h3>
                    <a href="{{ route('reports.agents') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        Voir détails
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach(\App\Models\User::role('agent')->withCount(['clients', 'tontines', 'payments'])->orderByDesc('clients_count')->take(5)->get() as $agent)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-medium">{{ $agent->name }}</span>
                                <a href="{{ route('reports.agents.details', $agent) }}" class="text-xs text-blue-600 hover:text-blue-800">Détails</a>
                            </div>
                            <div class="grid grid-cols-3 gap-2 text-center">
                                <div>
                                    <p class="text-xs text-gray-500">Clients</p>
                                    <p class="font-semibold text-blue-600">{{ $agent->clients_count }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Tontines</p>
                                    <p class="font-semibold text-yellow-600">{{ $agent->tontines_count }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Paiements</p>
                                    <p class="font-semibold text-green-600">{{ $agent->payments_count }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Exports --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 bg-gradient-to-r from-blue-50 to-yellow-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Exports</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('reports.export.clients') }}" class="flex items-center justify-center bg-blue-50 hover:bg-blue-100 rounded-lg p-4 transition">
                        <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        <span class="font-medium">Exporter les clients</span>
                    </a>
                    <a href="{{ route('reports.export.tontines') }}" class="flex items-center justify-center bg-yellow-50 hover:bg-yellow-100 rounded-lg p-4 transition">
                        <svg class="w-6 h-6 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        <span class="font-medium">Exporter les tontines</span>
                    </a>
                    <a href="{{ route('reports.export.payments') }}" class="flex items-center justify-center bg-green-50 hover:bg-green-100 rounded-lg p-4 transition">
                        <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        <span class="font-medium">Exporter les paiements</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
