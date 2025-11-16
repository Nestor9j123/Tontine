<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nouveau Mouvement de Stock</h2>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('stock.store') }}">
                @csrf
                
                <div class="space-y-6">
                    {{-- Produit --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Produit *</label>
                        <select name="product_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" id="product-select">
                            <option value="">Sélectionner un produit</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-stock="{{ $product->stock_quantity }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->code }} - {{ $product->name }} (Stock actuel: {{ $product->stock_quantity }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        <div id="current-stock" class="mt-2 p-3 bg-blue-50 rounded-lg hidden">
                            <p class="text-sm text-blue-800">
                                <span class="font-semibold">Stock actuel:</span> <span id="stock-value" class="text-lg font-bold">0</span>
                            </p>
                        </div>
                    </div>

                    {{-- Type de mouvement --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type de mouvement *</label>
                        <select name="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" id="movement-type">
                            <option value="">Sélectionner un type</option>
                            <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>➕ Entrée (Réception marchandise)</option>
                            <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>➖ Sortie (Vente, perte, etc.)</option>
                            <option value="adjustment" {{ old('type') == 'adjustment' ? 'selected' : '' }}>⚙️ Ajustement (Inventaire)</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Quantité --}}
                    <div id="quantity-field">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span id="quantity-label">Quantité *</span>
                        </label>
                        <input type="number" name="quantity" value="{{ old('quantity') }}" required min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" id="quantity-input">
                        <p class="mt-1 text-xs text-gray-500" id="quantity-help">
                            Entrez la quantité du mouvement
                        </p>
                        @error('quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Référence --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Référence</label>
                        <input type="text" name="reference" value="{{ old('reference') }}" placeholder="Ex: BON-2025-001, FACTURE-123..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Bon de livraison, facture, etc.</p>
                        @error('reference')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Raison --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Raison / Motif *</label>
                        <textarea name="reason" required rows="3" placeholder="Décrivez la raison du mouvement..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('reason') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Ex: Réception fournisseur, Inventaire annuel, Produit endommagé, etc.</p>
                        @error('reason')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="mt-6 flex justify-end space-x-4">
                    <a href="{{ route('stock.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-yellow-500 text-white rounded-lg hover:from-blue-700 hover:to-yellow-600 transition">
                        Enregistrer le Mouvement
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Afficher le stock actuel quand un produit est sélectionné
        document.getElementById('product-select').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const stock = selectedOption.getAttribute('data-stock');
            const stockDiv = document.getElementById('current-stock');
            const stockValue = document.getElementById('stock-value');
            
            if (stock !== null && this.value) {
                stockValue.textContent = stock;
                stockDiv.classList.remove('hidden');
            } else {
                stockDiv.classList.add('hidden');
            }
        });

        // Changer le label selon le type de mouvement
        document.getElementById('movement-type').addEventListener('change', function() {
            const quantityLabel = document.getElementById('quantity-label');
            const quantityHelp = document.getElementById('quantity-help');
            const quantityInput = document.getElementById('quantity-input');
            
            if (this.value === 'in') {
                quantityLabel.textContent = 'Quantité à ajouter *';
                quantityHelp.textContent = 'Quantité reçue (sera ajoutée au stock)';
                quantityInput.placeholder = 'Ex: 50';
            } else if (this.value === 'out') {
                quantityLabel.textContent = 'Quantité à retirer *';
                quantityHelp.textContent = 'Quantité sortie (sera retirée du stock)';
                quantityInput.placeholder = 'Ex: 10';
            } else if (this.value === 'adjustment') {
                quantityLabel.textContent = 'Nouveau stock total *';
                quantityHelp.textContent = 'Le stock sera ajusté à cette valeur exacte';
                quantityInput.placeholder = 'Ex: 100';
            }
        });
    </script>
    @endpush
</x-app-layout>
