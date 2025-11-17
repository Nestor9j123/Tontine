@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header avec statistiques -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                Notifications
            </h2>
            <div class="flex items-center space-x-4">
                @if($stats['unread'] > 0)
                    <button onclick="markAllAsRead()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                        Tout marquer comme lu
                    </button>
                @endif
            </div>
        </div>
        
        <!-- Statistiques -->
        <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 p-3 rounded-lg">
                <div class="text-sm text-blue-600 font-medium">Total</div>
                <div class="text-xl font-bold text-blue-800">{{ $stats['total'] }}</div>
            </div>
            <div class="bg-red-50 p-3 rounded-lg">
                <div class="text-sm text-red-600 font-medium">Non lues</div>
                <div class="text-xl font-bold text-red-800">{{ $stats['unread'] }}</div>
            </div>
            <div class="bg-green-50 p-3 rounded-lg">
                <div class="text-sm text-green-600 font-medium">Livrées</div>
                <div class="text-xl font-bold text-green-800">{{ $stats['delivered'] }}</div>
            </div>
            @if(!auth()->user()->hasRole('agent'))
            <div class="bg-gray-50 p-3 rounded-lg">
                <div class="text-sm text-gray-600 font-medium">Supprimées</div>
                <div class="text-xl font-bold text-gray-800">{{ $stats['deleted'] ?? 0 }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Filtres et actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <form method="GET" class="flex flex-wrap gap-3">
                    <div class="relative">
                        <select name="type" class="appearance-none bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 pr-8 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">🏷️ Tous les types</option>
                            <option value="payment_completed" {{ request('type') === 'payment_completed' ? 'selected' : '' }}>✅ Paiements terminés</option>
                            <option value="low_stock" {{ request('type') === 'low_stock' ? 'selected' : '' }}>📦 Stock faible</option>
                            <option value="delivery_reminder" {{ request('type') === 'delivery_reminder' ? 'selected' : '' }}>🚚 Rappels de livraison</option>
                            <option value="monthly_report_auto" {{ request('type') === 'monthly_report_auto' ? 'selected' : '' }}>📊 Rapport automatique</option>
                            <option value="monthly_report_reminder" {{ request('type') === 'monthly_report_reminder' ? 'selected' : '' }}>🔔 Rappel rapport</option>
                            <option value="monthly_report_error" {{ request('type') === 'monthly_report_error' ? 'selected' : '' }}>❌ Erreur rapport</option>
                            <option value="general" {{ request('type') === 'general' ? 'selected' : '' }}>📊 Général</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <div class="relative">
                        <select name="status" class="appearance-none bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 pr-8 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">📋 Tous les statuts</option>
                            <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>🔴 Non lues</option>
                            <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>✅ Lues</option>
                            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>🚚 Livrées</option>
                            @if(!auth()->user()->hasRole('agent'))
                            <option value="deleted" {{ request('status') === 'deleted' ? 'selected' : '' }}>🗑️ Supprimées</option>
                            @endif
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    
                    @if(!auth()->user()->hasRole('agent') && $agents->count() > 0)
                    <div class="relative">
                        <select name="agent_id" class="appearance-none bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 pr-8 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">👤 Tous les agents</option>
                            @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" {{ request('agent_id') == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    @endif
                    
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filtrer
                    </button>
                </form>
                
                @if($stats['unread'] > 0)
                    <button onclick="markAllAsRead()" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white text-sm font-medium rounded-lg hover:from-green-700 hover:to-green-800 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Tout marquer comme lu
                    </button>
                @endif
            </div>
        </div>

        <!-- Liste des notifications -->
        <div class="space-y-4">
            @if($notifications->count() > 0)
                @foreach($notifications as $notification)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all duration-200 {{ !$notification->is_read ? 'ring-2 ring-blue-100' : '' }}">
                        <div class="p-6">
                            <div class="flex items-start space-x-4">
                                <!-- Icône de type -->
                                <div class="flex-shrink-0">
                                    @if($notification->type === 'payment_completed')
                                        <div class="w-12 h-12 bg-gradient-to-br from-green-100 to-green-200 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    @elseif($notification->type === 'low_stock')
                                        <div class="w-12 h-12 bg-gradient-to-br from-red-100 to-red-200 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                            </svg>
                                        </div>
                                    @elseif($notification->type === 'delivery_reminder')
                                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                        </div>
                                    @elseif($notification->type === 'monthly_report_auto')
                                        <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                    @elseif($notification->type === 'monthly_report_reminder')
                                        <div class="w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-200 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                            </svg>
                                        </div>
                                    @elseif($notification->type === 'monthly_report_error')
                                        <div class="w-12 h-12 bg-gradient-to-br from-red-100 to-red-200 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Contenu -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center space-x-3">
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $notification->title }}</h3>
                                            
                                            <!-- Badges de statut -->
                                            <div class="flex space-x-2">
                                                @if(!$notification->is_read)
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 animate-pulse">
                                                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-1.5"></span>
                                                        Nouveau
                                                    </span>
                                                @endif
                                                
                                                @if($notification->is_delivered)
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                        </svg>
                                                        Livré
                                                    </span>
                                                @endif
                                                
                                                @if($notification->deleted_at)
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                        Supprimé
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Actions -->
                                        <div class="flex items-center space-x-2">
                                            @if(!$notification->deleted_at)
                                                @if(!$notification->is_read)
                                                    <button onclick="markAsRead('{{ $notification->uuid }}')" 
                                                        class="inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-medium rounded-lg transition-colors duration-200"
                                                        title="Marquer comme lu">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Lu
                                                    </button>
                                                @endif
                                                
                                                @if($notification->type === 'payment_completed' && !$notification->is_delivered && $notification->canBeMarkedAsDeliveredBy(auth()->user()))
                                                    <button onclick="markAsDelivered('{{ $notification->uuid }}')" 
                                                        class="inline-flex items-center px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-700 text-sm font-medium rounded-lg transition-colors duration-200"
                                                        title="Marquer comme livré">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                        </svg>
                                                        Livré
                                                    </button>
                                                @endif
                                                
                                                <a href="{{ route('notifications.show', $notification) }}" 
                                                    class="inline-flex items-center px-3 py-1.5 bg-gray-50 hover:bg-gray-100 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200"
                                                    title="Voir les détails">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    Voir
                                                </a>
                                                
                                                @if($notification->canBeDeletedBy(auth()->user()))
                                                    <form method="POST" action="{{ route('notifications.destroy', $notification) }}" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')"
                                                            class="inline-flex items-center px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 text-sm font-medium rounded-lg transition-colors duration-200"
                                                            title="Supprimer">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                            Supprimer
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                <!-- Actions pour notifications supprimées -->
                                                @if($notification->canBeDeletedBy(auth()->user()))
                                                    <form method="POST" action="{{ route('notifications.restore', $notification->uuid) }}" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" 
                                                            class="inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-medium rounded-lg transition-colors duration-200"
                                                            title="Restaurer">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                            </svg>
                                                            Restaurer
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                @if(auth()->user()->hasRole('super_admin'))
                                                    <form method="POST" action="{{ route('notifications.force-delete', $notification->uuid) }}" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                            onclick="return confirm('ATTENTION : Cette action supprimera définitivement cette notification. Êtes-vous sûr ?')"
                                                            class="inline-flex items-center px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 text-sm font-medium rounded-lg transition-colors duration-200"
                                                            title="Supprimer définitivement">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                            Supprimer définitivement
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <p class="text-gray-600 mb-3 leading-relaxed">{{ $notification->message }}</p>
                                    
                                    <!-- Informations de traçabilité -->
                                    @if($notification->is_delivered && $notification->markedDeliveredBy)
                                        <div class="mb-3 p-3 bg-green-50 rounded-lg border border-green-200">
                                            <div class="flex items-center text-sm text-green-700">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                </svg>
                                                <span><strong>Marqué comme livré</strong> par {{ $notification->markedDeliveredBy->name }} le {{ $notification->marked_delivered_at->format('d/m/Y à H:i') }}</span>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($notification->deleted_at && $notification->deletedBy)
                                        <div class="mb-3 p-3 bg-red-50 rounded-lg border border-red-200">
                                            <div class="flex items-center text-sm text-red-700">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                <span><strong>Supprimé</strong> par {{ $notification->deletedBy->name }} le {{ $notification->deleted_at->format('d/m/Y à H:i') }}</span>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Métadonnées -->
                                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>{{ $notification->created_at->diffForHumans() }}</span>
                                        </div>
                                        
                                        @if($notification->agent)
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 112 2H6a2 2 0 012-2z"></path>
                                                </svg>
                                                <span>Agent: {{ $notification->agent->name }}</span>
                                            </div>
                                        @endif
                                        
                                        @if($notification->client)
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                <span>{{ $notification->client->full_name }}</span>
                                            </div>
                                        @endif
                                        
                                        @if($notification->tontine && $notification->tontine->product)
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                </svg>
                                                <span>{{ $notification->tontine->product->name }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                
                <!-- Pagination -->
                @if($notifications->hasPages())
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        {{ $notifications->links() }}
                    </div>
                @endif
            @else
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12">
                    <div class="text-center">
                        <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucune notification</h3>
                        <p class="text-gray-500 mb-6">Vous n'avez aucune notification correspondant aux critères sélectionnés.</p>
                        <a href="{{ route('notifications.index') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Voir toutes les notifications
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Fonction pour afficher une notification toast
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg text-white font-medium transform transition-all duration-300 translate-x-full ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    toast.innerHTML = `
        <div class="flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${type === 'success' 
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>'
                }
            </svg>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Animation d'entrée
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // Suppression automatique
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

function markAsRead(id) {
    const button = event.target.closest('button');
    const notification = button.closest('.bg-white');
    
    // Animation de chargement
    button.innerHTML = `
        <svg class="w-4 h-4 mr-1 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Chargement...
    `;
    button.disabled = true;
    
    fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Animation de succès
            notification.classList.remove('ring-2', 'ring-blue-100');
            notification.classList.add('opacity-75');
            
            // Supprimer le badge "Nouveau"
            const badge = notification.querySelector('.animate-pulse');
            if (badge) {
                badge.style.display = 'none';
            }
            
            // Supprimer le bouton
            button.style.display = 'none';
            
            showToast('Notification marquée comme lue', 'success');
            
            // Mettre à jour le compteur dans le sidebar
            updateNotificationCount();
        } else {
            showToast('Erreur lors de la mise à jour', 'error');
            button.disabled = false;
            button.innerHTML = `
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Lu
            `;
        }
    })
    .catch(error => {
        showToast('Erreur de connexion', 'error');
        button.disabled = false;
        button.innerHTML = `
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Lu
        `;
    });
}

function markAllAsRead() {
    const button = event.target;
    const originalContent = button.innerHTML;
    
    // Animation de chargement
    button.innerHTML = `
        <svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Chargement...
    `;
    button.disabled = true;
    
    fetch('/notifications/read-all', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Animation de succès pour toutes les notifications
            const notifications = document.querySelectorAll('.ring-2.ring-blue-100');
            notifications.forEach(notification => {
                notification.classList.remove('ring-2', 'ring-blue-100');
                notification.classList.add('opacity-75');
                
                const badge = notification.querySelector('.animate-pulse');
                if (badge) badge.style.display = 'none';
                
                const readButton = notification.querySelector('button[onclick*="markAsRead"]');
                if (readButton) readButton.style.display = 'none';
            });
            
            button.style.display = 'none';
            showToast('Toutes les notifications ont été marquées comme lues', 'success');
            
            // Mettre à jour le compteur
            updateNotificationCount();
        } else {
            showToast('Erreur lors de la mise à jour', 'error');
            button.disabled = false;
            button.innerHTML = originalContent;
        }
    })
    .catch(error => {
        showToast('Erreur de connexion', 'error');
        button.disabled = false;
        button.innerHTML = originalContent;
    });
}

function markAsDelivered(id) {
    const button = event.target.closest('button');
    const notification = button.closest('.bg-white');
    
    // Animation de chargement
    button.innerHTML = `
        <svg class="w-4 h-4 mr-1 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Chargement...
    `;
    button.disabled = true;
    
    fetch(`/notifications/${id}/delivered`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Ajouter le badge "Livré"
            const badgeContainer = notification.querySelector('.flex.space-x-2');
            const deliveredBadge = document.createElement('span');
            deliveredBadge.className = 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800';
            deliveredBadge.innerHTML = `
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                Livré
            `;
            badgeContainer.appendChild(deliveredBadge);
            
            // Ajouter l'information de traçabilité
            const messageElement = notification.querySelector('.text-gray-600.mb-3');
            const traceabilityInfo = document.createElement('div');
            traceabilityInfo.className = 'mb-3 p-3 bg-green-50 rounded-lg border border-green-200';
            traceabilityInfo.innerHTML = `
                <div class="flex items-center text-sm text-green-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span><strong>Marqué comme livré</strong> par ${data.marked_by} le ${data.marked_at}</span>
                </div>
            `;
            messageElement.parentNode.insertBefore(traceabilityInfo, messageElement.nextSibling);
            
            // Supprimer le bouton "Livré"
            button.style.display = 'none';
            
            showToast('Notification marquée comme livrée !', 'success');
        } else {
            showToast('Erreur lors de la mise à jour', 'error');
            button.disabled = false;
            button.innerHTML = `
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                Livré
            `;
        }
    })
    .catch(error => {
        showToast('Erreur de connexion', 'error');
        button.disabled = false;
        button.innerHTML = `
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            Livré
        `;
    });
}

function updateNotificationCount() {
    fetch('/api/notifications/count')
        .then(response => response.json())
        .then(data => {
            const badge = document.querySelector('a[href*="notifications"] .animate-pulse');
            if (badge) {
                if (data.count > 0) {
                    badge.textContent = data.count;
                } else {
                    badge.style.display = 'none';
                }
            }
        })
        .catch(error => console.log('Erreur lors de la mise à jour du compteur'));
}
</script>
@endsection
