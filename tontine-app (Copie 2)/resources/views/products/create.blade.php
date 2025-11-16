<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nouveau Produit Tontine
            </h2>
            <a href="{{ route('products.index') }}" 
                class="text-gray-600 hover:text-gray-900 flex items-center text-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour aux produits
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-8">
        <!-- En-tête avec icône -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-xl overflow-hidden">
            <div class="px-8 py-12 text-white">
                <div class="flex items-center space-x-6">
                    <div class="w-16 h-16 rounded-full bg-white bg-opacity-20 backdrop-blur-sm flex items-center justify-center text-white border-4 border-white border-opacity-30">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">Créer un nouveau produit</h1>
                        <p class="text-blue-100 mt-1">Définissez les caractéristiques de votre produit tontine</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulaire principal -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-8 py-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Informations du produit</h3>
                <p class="text-sm text-gray-600 mt-1">Remplissez tous les champs requis pour créer votre produit</p>
            </div>

            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="p-8" x-data="productForm()" @submit="validateForm">
                @csrf
                
                <div class="space-y-8">
                    <!-- Informations de base -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="lg:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nom du produit <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                                <input type="text" id="name" name="name" required 
                                    class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="Ex: Épargne Éducation, Projet Maison..."
                                    value="{{ old('name') }}">
                            </div>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="lg:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>
                            <div class="relative">
                                <textarea id="description" name="description" rows="4" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="Décrivez les avantages et caractéristiques de ce produit...">{{ old('description') }}</textarea>
                            </div>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Prix et durée -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                                Prix total (FCFA) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-sm">FCFA</span>
                                </div>
                                <input type="number" id="price" name="price" required min="1" 
                                    class="pl-16 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="50000"
                                    value="{{ old('price') }}"
                                    x-model="price" @input="calculatePayment()">
                            </div>
                            @error('price')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="duration_value" class="block text-sm font-medium text-gray-700 mb-2">
                                Durée <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="duration_value" name="duration_value" required min="1" value="{{ old('duration_value', 1) }}" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="12"
                                x-model="duration" @input="calculatePayment()">
                            @error('duration_value')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="duration_unit" class="block text-sm font-medium text-gray-700 mb-2">
                                Unité <span class="text-red-500">*</span>
                            </label>
                            <select id="duration_unit" name="duration_unit" required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                x-model="durationUnit" @change="calculatePayment()">
                                <option value="days" {{ old('duration_unit') == 'days' ? 'selected' : '' }}>Jours</option>
                                <option value="weeks" {{ old('duration_unit') == 'weeks' ? 'selected' : '' }}>Semaines</option>
                                <option value="months" {{ old('duration_unit', 'months') == 'months' ? 'selected' : '' }}>Mois</option>
                                <option value="years" {{ old('duration_unit') == 'years' ? 'selected' : '' }}>Années</option>
                            </select>
                            @error('duration_unit')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Quantité disponible -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                Stock disponible
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                                    </svg>
                                </div>
                                <input type="number" id="stock_quantity" name="stock_quantity" min="0" 
                                    class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="Ex: 50, 100, 200... (laisser vide si illimité)"
                                    value="{{ old('stock_quantity') }}">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Nombre de places en stock (laisser vide pour un stock illimité)</p>
                            @error('stock_quantity')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Statut du produit
                            </label>
                            <div class="flex items-center space-x-4 mt-3">
                                <label class="flex items-center">
                                    <input type="radio" name="status" value="active" class="text-blue-600 focus:ring-blue-500" 
                                           {{ old('status', 'active') == 'active' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ✅ Actif
                                        </span>
                                    </span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="status" value="inactive" class="text-gray-600 focus:ring-gray-500"
                                           {{ old('status') == 'inactive' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            ⏸️ Inactif
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Les produits actifs sont visibles aux clients</p>
                            @error('status')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Type de paiement -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            Fréquence de paiement <span class="text-red-500">*</span>
                        </label>
                        <select id="type" name="type" required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            x-model="paymentType" @change="calculatePayment()">
                            <option value="daily" {{ old('type') == 'daily' ? 'selected' : '' }}>💰 Journalier - Paiement chaque jour</option>
                            <option value="weekly" {{ old('type') == 'weekly' ? 'selected' : '' }}>📅 Hebdomadaire - Paiement chaque semaine</option>
                            <option value="monthly" {{ old('type', 'monthly') == 'monthly' ? 'selected' : '' }}>🗓️ Mensuel - Paiement chaque mois</option>
                            <option value="yearly" {{ old('type') == 'yearly' ? 'selected' : '' }}>📆 Annuel - Paiement unique par an</option>
                        </select>
                        <p class="mt-2 text-xs text-gray-500">
                            Détermine à quelle fréquence le client effectue ses paiements
                        </p>
                        @error('type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Calcul automatique -->
                    <div x-show="price && duration && paymentType" class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-green-800 mb-2">💡 Calcul automatique</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div class="text-center">
                                <div class="text-lg font-bold text-green-600" x-text="formatPrice(paymentAmount)"></div>
                                <div class="text-green-700" x-text="paymentFrequencyText"></div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-blue-600" x-text="totalPayments"></div>
                                <div class="text-blue-700">Paiements total</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-purple-600" x-text="formatPrice(price)"></div>
                                <div class="text-purple-700">Montant total</div>
                            </div>
                        </div>
                    </div>

                    <!-- Photos -->
                    <div>
                        <label for="photos" class="block text-sm font-medium text-gray-700 mb-2">
                            Photos du produit
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <div class="mt-4">
                                <label for="photos" class="cursor-pointer">
                                    <span class="mt-2 block text-sm font-medium text-gray-900">
                                        Cliquez pour ajouter des photos
                                    </span>
                                    <input type="file" id="photos" name="photos[]" accept="image/*" multiple class="hidden">
                                </label>
                                <p class="mt-2 text-xs text-gray-500">
                                    JPG, PNG, GIF jusqu'à 2MB chacune. La première sera la photo principale.
                                </p>
                            </div>
                        </div>
                        @error('photos')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Statut -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" 
                                {{ old('is_active', true) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-sm font-medium text-gray-700">
                                ✅ Produit actif et disponible pour les nouvelles tontines
                            </span>
                        </label>
                        <p class="mt-2 text-xs text-gray-500 ml-6">
                            Les produits inactifs ne peuvent pas être utilisés pour créer de nouvelles tontines
                        </p>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="mt-8 flex justify-between items-center pt-6 border-t border-gray-200">
                    <a href="{{ route('products.index') }}" 
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Annuler
                    </a>
                    
                    <button type="submit" 
                        :disabled="isSubmitting"
                        :class="isSubmitting ? 'opacity-75 cursor-not-allowed' : 'hover:from-blue-700 hover:to-indigo-700'"
                        class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-lg focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 flex items-center">
                        
                        <!-- Icône de chargement -->
                        <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        
                        <!-- Icône normale -->
                        <svg x-show="!isSubmitting" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        
                        <span x-text="isSubmitting ? 'Création en cours...' : 'Créer le produit'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function productForm() {
            return {
                price: {{ old('price', 0) }},
                duration: {{ old('duration_value', 1) }},
                durationUnit: '{{ old('duration_unit', 'months') }}',
                paymentType: '{{ old('type', 'monthly') }}',
                paymentAmount: 0,
                totalPayments: 0,
                paymentFrequencyText: '',
                isSubmitting: false,

                calculatePayment() {
                    if (!this.price || !this.duration || !this.paymentType) return;

                    const durationInDays = this.getDurationInDays();
                    const paymentFrequencyInDays = this.getPaymentFrequencyInDays();
                    
                    this.totalPayments = Math.ceil(durationInDays / paymentFrequencyInDays);
                    this.paymentAmount = Math.ceil(this.price / this.totalPayments);
                    
                    const frequencies = {
                        'daily': 'par jour',
                        'weekly': 'par semaine', 
                        'monthly': 'par mois',
                        'yearly': 'par an'
                    };
                    this.paymentFrequencyText = frequencies[this.paymentType] || '';
                },

                validateForm(event) {
                    const form = event.target;
                    const formData = new FormData(form);
                    
                    // Validation du nom
                    const name = formData.get('name');
                    if (!name || name.trim().length < 3) {
                        event.preventDefault();
                        showError('Nom requis', 'Le nom du produit doit contenir au moins 3 caractères');
                        return false;
                    }
                    
                    // Validation du prix
                    const price = parseFloat(formData.get('price'));
                    if (!price || price < 1000) {
                        event.preventDefault();
                        showError('Prix invalide', 'Le prix doit être d\'au moins 1 000 FCFA');
                        return false;
                    }
                    
                    // Validation de la durée
                    const duration = parseInt(formData.get('duration_value'));
                    if (!duration || duration < 1) {
                        event.preventDefault();
                        showError('Durée invalide', 'La durée doit être d\'au moins 1');
                        return false;
                    }
                    
                    // Validation des photos (optionnel mais si présent)
                    const photos = formData.getAll('photos[]');
                    if (photos.length > 0) {
                        for (let photo of photos) {
                            if (photo.size > 0) {
                                if (photo.size > 2 * 1024 * 1024) { // 2MB
                                    event.preventDefault();
                                    showError('Photo trop grande', `La photo "${photo.name}" dépasse 2MB`);
                                    return false;
                                }
                                
                                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                                if (!allowedTypes.includes(photo.type)) {
                                    event.preventDefault();
                                    showError('Format invalide', `La photo "${photo.name}" n'est pas au bon format (JPG, PNG, GIF, WebP)`);
                                    return false;
                                }
                            }
                        }
                    }
                    
                    // Si tout est valide, on peut soumettre
                    this.isSubmitting = true;
                    showInfo('Création en cours...', 'Veuillez patienter pendant la création du produit');
                },

                getDurationInDays() {
                    const multipliers = {
                        'days': 1,
                        'weeks': 7,
                        'months': 30,
                        'years': 365
                    };
                    return this.duration * (multipliers[this.durationUnit] || 30);
                },

                getPaymentFrequencyInDays() {
                    const frequencies = {
                        'daily': 1,
                        'weekly': 7,
                        'monthly': 30,
                        'yearly': 365
                    };
                    return frequencies[this.paymentType] || 30;
                },

                formatPrice(amount) {
                    if (!amount) return '0 FCFA';
                    return new Intl.NumberFormat('fr-FR').format(amount) + ' FCFA';
                },

                init() {
                    this.calculatePayment();
                    
                    // Afficher les erreurs Laravel s'il y en a
                    @if($errors->any())
                        @foreach($errors->all() as $error)
                            showError('Erreur de validation', '{{ $error }}');
                        @endforeach
                    @endif
                    
                    // Afficher le message de succès s'il y en a un
                    @if(session('success'))
                        showSuccess('Succès', '{{ session('success') }}');
                    @endif
                    
                    @if(session('error'))
                        showError('Erreur', '{{ session('error') }}');
                    @endif
                }
            }
        }
    </script>
</x-app-layout>
