<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tableau de Bord') }}
            </h2>
            <div class="text-sm text-gray-600">
                Bienvenue, <span class="font-semibold">{{ auth()->user()->name }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(auth()->user()->hasRole('super_admin'))
                @include('dashboard.super-admin', ['stats' => $stats])
            @elseif(auth()->user()->hasRole('secretary'))
                @include('dashboard.secretary', ['stats' => $stats])
            @else
                @include('dashboard.agent', ['stats' => $stats])
            @endif

        </div>
    </div>
</x-app-layout>
