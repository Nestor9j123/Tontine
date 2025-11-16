@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
            <button onclick="markAllAsRead()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                Tout marquer comme lu
            </button>
        </div>
        
        <div class="space-y-4">
            @if(auth()->user()->notifications->count() > 0)
                @foreach(auth()->user()->notifications as $notification)
                    <div class="border border-gray-200 rounded-lg p-4 {{ $notification->read_at ? 'bg-gray-50' : 'bg-blue-50' }}">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $notification->data['title'] ?? 'Notification' }}</h3>
                                <p class="text-gray-600 mt-1">{{ $notification->data['message'] ?? '' }}</p>
                                <p class="text-sm text-gray-500 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            @if(!$notification->read_at)
                                <button onclick="markAsRead('{{ $notification->id }}')" class="text-blue-600 hover:text-blue-800">
                                    Marquer comme lu
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-8">
                    <p class="text-gray-500">Aucune notification</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function markAsRead(id) {
    fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function markAllAsRead() {
    fetch('/notifications/read-all', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
@endsection
