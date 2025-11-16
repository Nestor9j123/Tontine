@props(['paginator'])

<div class="bg-white border-t border-gray-200">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-4 sm:px-6 py-4">
        {{-- Informations sur les résultats --}}
        <div class="text-sm text-gray-700 order-2 sm:order-1">
            @if ($paginator->total() > 0)
                Affichage de 
                <span class="font-medium">{{ $paginator->firstItem() }}</span>
                à
                <span class="font-medium">{{ $paginator->lastItem() }}</span>
                sur
                <span class="font-medium">{{ $paginator->total() }}</span>
                résultats
            @else
                <span class="font-medium">Aucun résultat</span>
            @endif
        </div>

        {{-- Navigation de pagination avec numéros --}}
        @if ($paginator->hasPages())
            <nav class="flex items-center gap-1 order-1 sm:order-2" aria-label="Pagination">
                {{-- Bouton Précédent --}}
                @if ($paginator->onFirstPage())
                    <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-lg cursor-not-allowed">
                        ← Préc
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        ← Préc
                    </a>
                @endif

                {{-- Numéros de pages --}}
                @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-2 text-sm font-bold text-white bg-blue-600 border border-blue-600 rounded-lg">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                {{-- Bouton Suivant --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Suiv →
                    </a>
                @else
                    <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-lg cursor-not-allowed">
                        Suiv →
                    </span>
                @endif
            </nav>
        @endif

        {{-- Sélecteur de nombre par page --}}
        <div class="flex items-center gap-2 order-3">
            <label for="perPage" class="text-sm text-gray-700 whitespace-nowrap">Par page:</label>
            <select id="perPage" onchange="changePerPage(this.value)" 
                class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
            </select>
        </div>
    </div>
</div>

<script>
function changePerPage(perPage) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', perPage);
    url.searchParams.delete('page'); // Reset à la page 1
    window.location.href = url.toString();
}
</script>
