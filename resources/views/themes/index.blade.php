@extends('layouts.app')

@section('title', 'Thèmes')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Personnalisation des Thèmes</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($themes as $key => $theme)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow cursor-pointer"
                     onclick="selectTheme('{{ $key }}')">
                    
                    <div class="w-full h-20 rounded mb-4" style="background: {{ $theme['preview'] ?? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' }}"></div>
                    
                    <h3 class="font-semibold text-lg mb-2">{{ $theme['name'] }}</h3>
                    <p class="text-gray-600 text-sm mb-4">{{ $theme['description'] }}</p>
                    
                    <button class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                        Appliquer
                    </button>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
function selectTheme(theme) {
    fetch('/themes/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            theme: theme
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}
</script>
@endsection
