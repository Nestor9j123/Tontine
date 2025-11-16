@if(isset($stats['ranking']) && $stats['ranking'])
<div class="bg-gradient-to-br from-blue-500 via-blue-400 to-yellow-500 rounded-xl shadow-2xl p-6 text-white mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-bold">🏆 Mon Classement</h3>
        <span class="text-4xl">{{ $stats['ranking']['badge']['icon'] }}</span>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        {{-- Rang --}}
        <div class="bg-white bg-opacity-20 rounded-lg p-4 backdrop-blur-sm">
            <p class="text-xs opacity-90 mb-1">Mon Rang</p>
            <p class="text-3xl font-bold">
                #{{ $stats['ranking']['rank'] }}
                @if($stats['ranking']['top_3'])
                    @if($stats['ranking']['rank'] == 1) 👑
                    @elseif($stats['ranking']['rank'] == 2) 🥈
                    @else 🥉
                    @endif
                @endif
            </p>
            <p class="text-xs opacity-75">sur {{ $stats['ranking']['total_agents'] }} agents</p>
        </div>

        {{-- Badge --}}
        <div class="bg-white bg-opacity-20 rounded-lg p-4 backdrop-blur-sm">
            <p class="text-xs opacity-90 mb-1">Badge</p>
            <p class="text-2xl font-bold">{{ $stats['ranking']['badge']['name'] }}</p>
            <p class="text-xs opacity-75">Niveau {{ $stats['ranking']['badge']['level'] }}/5</p>
        </div>

        {{-- Score --}}
        <div class="bg-white bg-opacity-20 rounded-lg p-4 backdrop-blur-sm">
            <p class="text-xs opacity-90 mb-1">Score</p>
            <p class="text-3xl font-bold">{{ $stats['ranking']['performance_score'] }}%</p>
            <div class="w-full bg-white bg-opacity-30 rounded-full h-2 mt-2">
                <div class="bg-white h-2 rounded-full transition-all duration-500" style="width: {{ $stats['ranking']['performance_score'] }}%"></div>
            </div>
        </div>

        {{-- Montant collecté --}}
        <div class="bg-white bg-opacity-20 rounded-lg p-4 backdrop-blur-sm">
            <p class="text-xs opacity-90 mb-1">Collecté</p>
            <p class="text-xl font-bold">{{ number_format($stats['ranking']['total_amount'], 0, ',', ' ') }}</p>
            <p class="text-xs opacity-75">FCFA</p>
        </div>
    </div>

    {{-- Objectifs pour le prochain niveau --}}
    @if(!isset($stats['ranking']['next_level']['max_level']))
    <div class="bg-white bg-opacity-10 rounded-lg p-4 backdrop-blur-sm">
        <div class="flex items-center justify-between mb-3">
            <h4 class="font-semibold">🎯 Objectifs pour {{ $stats['ranking']['next_level']['target_badge'] }}</h4>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @if($stats['ranking']['next_level']['clients'] > 0)
            <div class="text-center">
                <p class="text-2xl font-bold">+{{ $stats['ranking']['next_level']['clients'] }}</p>
                <p class="text-xs opacity-90">clients</p>
            </div>
            @endif
            
            @if($stats['ranking']['next_level']['tontines'] > 0)
            <div class="text-center">
                <p class="text-2xl font-bold">+{{ $stats['ranking']['next_level']['tontines'] }}</p>
                <p class="text-xs opacity-90">tontines</p>
            </div>
            @endif
            
            @if($stats['ranking']['next_level']['payments'] > 0)
            <div class="text-center">
                <p class="text-2xl font-bold">+{{ $stats['ranking']['next_level']['payments'] }}</p>
                <p class="text-xs opacity-90">paiements</p>
            </div>
            @endif
            
            @if($stats['ranking']['next_level']['amount'] > 0)
            <div class="text-center">
                <p class="text-xl font-bold">+{{ number_format($stats['ranking']['next_level']['amount'], 0, ',', ' ') }}</p>
                <p class="text-xs opacity-90">FCFA</p>
            </div>
            @endif
        </div>

        @if($stats['ranking']['next_level']['clients'] == 0 && $stats['ranking']['next_level']['tontines'] == 0 && $stats['ranking']['next_level']['payments'] == 0 && $stats['ranking']['next_level']['amount'] == 0)
        <div class="text-center py-4">
            <p class="text-2xl mb-2">🎉</p>
            <p class="font-semibold">Félicitations! Vous avez atteint tous les objectifs!</p>
            <p class="text-sm opacity-90">Continuez votre excellent travail!</p>
        </div>
        @endif
    </div>
    @else
    <div class="bg-white bg-opacity-10 rounded-lg p-6 backdrop-blur-sm text-center">
        <p class="text-3xl mb-2">🌟</p>
        <p class="text-xl font-bold mb-1">Niveau Maximum Atteint!</p>
        <p class="text-sm opacity-90">Vous êtes au sommet! Continuez à exceller!</p>
    </div>
    @endif

    {{-- Messages de motivation --}}
    <div class="mt-4 text-center">
        @if($stats['ranking']['rank'] == 1)
            <p class="text-sm font-semibold">👑 Vous êtes le meilleur agent! Continuez comme ça!</p>
        @elseif($stats['ranking']['top_3'])
            <p class="text-sm font-semibold">🔥 Vous êtes dans le TOP 3! Encore un effort pour être #1!</p>
        @elseif($stats['ranking']['top_10'])
            <p class="text-sm font-semibold">💪 Vous êtes dans le TOP 10! Continuez vos efforts!</p>
        @else
            <p class="text-sm font-semibold">🚀 Continuez à travailler dur pour grimper dans le classement!</p>
        @endif
    </div>
</div>
@endif
