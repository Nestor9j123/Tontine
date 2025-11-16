<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🎨 Paramètres Système & Thèmes
            </h2>
            <div class="flex space-x-2">
                <button type="button" onclick="resetTheme()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    🔄 Réinitialiser
                </button>
                <button type="submit" form="settings-form" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    💾 Enregistrer
                </button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <form id="settings-form" method="POST" action="{{ route('system-settings.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Navigation par onglets -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button type="button" onclick="switchTab('general')" class="tab-btn active px-6 py-3 border-b-2 border-blue-500 text-blue-600 font-medium text-sm">
                            📋 Informations
                        </button>
                        <button type="button" onclick="switchTab('themes')" class="tab-btn px-6 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm">
                            🎨 Thèmes
                        </button>
                        <button type="button" onclick="switchTab('colors')" class="tab-btn px-6 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm">
                            🎨 Couleurs
                        </button>
                        <button type="button" onclick="switchTab('advanced')" class="tab-btn px-6 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm">
                            ⚙️ Avancé
                        </button>
                    </nav>
                </div>

                <!-- Contenu des onglets -->
                <div class="p-6">
                    <!-- Onglet Informations Générales -->
                    <div id="general-tab" class="tab-content">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Informations de l'entreprise</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nom de l'entreprise</label>
                                <input type="text" name="company_name" value="{{ $settings['company_name'] ?? 'Tontine App' }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" name="company_email" value="{{ $settings['company_email'] ?? '' }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                                <input type="tel" name="company_phone" value="{{ $settings['company_phone'] ?? '' }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Seuil de stock faible</label>
                                <input type="number" name="low_stock_threshold" value="{{ $settings['low_stock_threshold'] ?? 10 }}" 
                                    min="1" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
                                <textarea name="company_address" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ $settings['company_address'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Onglet Thèmes -->
                    <div id="themes-tab" class="tab-content hidden">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Sélection de thème</h3>
                        
                        <!-- Thèmes prédéfinis -->
                        <div class="mb-8">
                            <h4 class="text-md font-medium text-gray-700 mb-4">Thèmes prédéfinis</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                                <div class="theme-preset-card border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-400 transition-colors" 
                                     onclick="applyThemePreset('default')">
                                    <div class="space-y-2">
                                        <div class="flex space-x-1">
                                            <div class="w-6 h-6 rounded" style="background-color: #3B82F6"></div>
                                            <div class="w-6 h-6 rounded" style="background-color: #10B981"></div>
                                            <div class="w-6 h-6 rounded" style="background-color: #F59E0B"></div>
                                            <div class="w-6 h-6 rounded" style="background-color: #EF4444"></div>
                                        </div>
                                        <h5 class="font-medium text-sm text-gray-900">Défaut</h5>
                                        <div class="text-xs text-gray-500">
                                            <div>Light: #3B82F6</div>
                                            <div>Dark: #2563EB</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="theme-preset-card border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-400 transition-colors" 
                                     onclick="applyThemePreset('ocean')">
                                    <div class="space-y-2">
                                        <div class="flex space-x-1">
                                            <div class="w-6 h-6 rounded" style="background-color: #0891B2"></div>
                                            <div class="w-6 h-6 rounded" style="background-color: #0E7490"></div>
                                            <div class="w-6 h-6 rounded" style="background-color: #06B6D4"></div>
                                            <div class="w-6 h-6 rounded" style="background-color: #083344"></div>
                                        </div>
                                        <h5 class="font-medium text-sm text-gray-900">Océan</h5>
                                        <div class="text-xs text-gray-500">
                                            <div>Light: #0891B2</div>
                                            <div>Dark: #0C4A6E</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="theme-preset-card border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-400 transition-colors" 
                                     onclick="applyThemePreset('forest')">
                                    <div class="space-y-2">
                                        <div class="flex space-x-1">
                                            <div class="w-6 h-6 rounded" style="background-color: #059669"></div>
                                            <div class="w-6 h-6 rounded" style="background-color: #047857"></div>
                                            <div class="w-6 h-6 rounded" style="background-color: #10B981"></div>
                                            <div class="w-6 h-6 rounded" style="background-color: #B91C1C"></div>
                                        </div>
                                        <h5 class="font-medium text-sm text-gray-900">Forêt</h5>
                                        <div class="text-xs text-gray-500">
                                            <div>Light: #059669</div>
                                            <div>Dark: #047857</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="theme-preset-card border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-400 transition-colors" 
                                     onclick="applyThemePreset('sunset')">
                                    <div class="space-y-2">
                                        <div class="flex space-x-1">
                                            <div class="w-6 h-6 rounded" style="background-color: #F97316"></div>
                                            <div class="w-6 h-6 rounded" style="background-color: #EA580C"></div>
                                            <div class="w-6 h-6 rounded" style="background-color: #FB923C"></div>
                                            <div class="w-6 h-6 rounded" style="background-color: #DC2626"></div>
                                        </div>
                                        <h5 class="font-medium text-sm text-gray-900">Coucher de soleil</h5>
                                        <div class="text-xs text-gray-500">
                                            <div>Light: #F97316</div>
                                            <div>Dark: #EA580C</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="theme-preset-card border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-400 transition-colors" 
                                     onclick="applyThemePreset('royal')">
                                    <div class="space-y-2">
                                        <div class="flex space-x-1">
                                            <div class="w-6 h-6 rounded" style="background-color: #7C3AED"></div>
                                            <div class="w-6 h-6 rounded" style="background-color: #6D28D9"></div>
                                            <div class="w-6 h-6 rounded" style="background-color: #8B5CF6"></div>
                                            <div class="w-6 h-6 rounded" style="background-color: #BE123C"></div>
                                        </div>
                                        <h5 class="font-medium text-sm text-gray-900">Royal</h5>
                                        <div class="text-xs text-gray-500">
                                            <div>Light: #7C3AED</div>
                                            <div>Dark: #6D28D9</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Paramètres du thème -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Mode du thème</label>
                                <select name="theme_mode" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="light" {{ ($settings['theme_mode'] ?? 'light') == 'light' ? 'selected' : '' }}>☀️ Clair</option>
                                    <option value="dark" {{ ($settings['theme_mode'] ?? 'light') == 'dark' ? 'selected' : '' }}>🌙 Sombre</option>
                                    <option value="auto" {{ ($settings['theme_mode'] ?? 'light') == 'auto' ? 'selected' : '' }}>🔄 Automatique</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Taille de police</label>
                                <select name="font_size" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="small" {{ ($settings['font_size'] ?? 'medium') == 'small' ? 'selected' : '' }}>Petit</option>
                                    <option value="medium" {{ ($settings['font_size'] ?? 'medium') == 'medium' ? 'selected' : '' }}>Moyen</option>
                                    <option value="large" {{ ($settings['font_size'] ?? 'medium') == 'large' ? 'selected' : '' }}>Grand</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Style de la barre latérale</label>
                                <select name="sidebar_style" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="expanded" {{ ($settings['sidebar_style'] ?? 'expanded') == 'expanded' ? 'selected' : '' }}>Développée</option>
                                    <option value="collapsed" {{ ($settings['sidebar_style'] ?? 'expanded') == 'collapsed' ? 'selected' : '' }}>Réduite</option>
                                    <option value="compact" {{ ($settings['sidebar_style'] ?? 'expanded') == 'compact' ? 'selected' : '' }}>Compacte</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Rayon de bordure (px)</label>
                                <input type="number" name="border_radius" value="{{ $settings['border_radius'] ?? 8 }}" 
                                    min="0" max="20" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Vitesse d'animation</label>
                                <select name="animation_speed" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="slow" {{ ($settings['animation_speed'] ?? 'normal') == 'slow' ? 'selected' : '' }}>Lente</option>
                                    <option value="normal" {{ ($settings['animation_speed'] ?? 'normal') == 'normal' ? 'selected' : '' }}>Normale</option>
                                    <option value="fast" {{ ($settings['animation_speed'] ?? 'normal') == 'fast' ? 'selected' : '' }}>Rapide</option>
                                </select>
                            </div>
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center">
                                    <input type="checkbox" name="enable_animations" value="1" {{ ($settings['enable_animations'] ?? '1') == '1' ? 'checked' : '' }} 
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Activer les animations</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="enable_shadows" value="1" {{ ($settings['enable_shadows'] ?? '1') == '1' ? 'checked' : '' }} 
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Activer les ombres</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="enable_gradients" value="1" {{ ($settings['enable_gradients'] ?? '1') == '1' ? 'checked' : '' }} 
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Activer les dégradés</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Onglet Couleurs -->
                    <div id="colors-tab" class="tab-content hidden">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Personnalisation des couleurs</h3>
                        
                        <!-- Mode Light -->
                        <div class="mb-8">
                            <h4 class="text-md font-medium text-gray-700 mb-4 flex items-center">
                                ☀️ Mode Clair
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Couleur primaire</label>
                                    <div class="flex items-center space-x-2">
                                        <input type="color" name="primary_color" value="{{ $settings['primary_color'] ?? '#3B82F6' }}" 
                                            class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                                        <input type="text" value="{{ $settings['primary_color'] ?? '#3B82F6' }}" readonly
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Couleur secondaire</label>
                                    <div class="flex items-center space-x-2">
                                        <input type="color" name="secondary_color" value="{{ $settings['secondary_color'] ?? '#10B981' }}" 
                                            class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                                        <input type="text" value="{{ $settings['secondary_color'] ?? '#10B981' }}" readonly
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Couleur d'accent</label>
                                    <div class="flex items-center space-x-2">
                                        <input type="color" name="accent_color" value="{{ $settings['accent_color'] ?? '#F59E0B' }}" 
                                            class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                                        <input type="text" value="{{ $settings['accent_color'] ?? '#F59E0B' }}" readonly
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Couleur de danger</label>
                                    <div class="flex items-center space-x-2">
                                        <input type="color" name="danger_color" value="{{ $settings['danger_color'] ?? '#EF4444' }}" 
                                            class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                                        <input type="text" value="{{ $settings['danger_color'] ?? '#EF4444' }}" readonly
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Arrière-plan</label>
                                    <div class="flex items-center space-x-2">
                                        <input type="color" name="background_color" value="{{ $settings['background_color'] ?? '#FFFFFF' }}" 
                                            class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                                        <input type="text" value="{{ $settings['background_color'] ?? '#FFFFFF' }}" readonly
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Surface</label>
                                    <div class="flex items-center space-x-2">
                                        <input type="color" name="surface_color" value="{{ $settings['surface_color'] ?? '#F9FAFB' }}" 
                                            class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                                        <input type="text" value="{{ $settings['surface_color'] ?? '#F9FAFB' }}" readonly
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Texte</label>
                                    <div class="flex items-center space-x-2">
                                        <input type="color" name="text_color" value="{{ $settings['text_color'] ?? '#111827' }}" 
                                            class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                                        <input type="text" value="{{ $settings['text_color'] ?? '#111827' }}" readonly
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mode Dark -->
                        <div class="mb-8">
                            <h4 class="text-md font-medium text-gray-700 mb-4 flex items-center">
                                🌙 Mode Sombre
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Couleur primaire</label>
                                    <div class="flex items-center space-x-2">
                                        <input type="color" name="dark_primary_color" value="{{ $settings['dark_primary_color'] ?? '#2563EB' }}" 
                                            class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                                        <input type="text" value="{{ $settings['dark_primary_color'] ?? '#2563EB' }}" readonly
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Couleur secondaire</label>
                                    <div class="flex items-center space-x-2">
                                        <input type="color" name="dark_secondary_color" value="{{ $settings['dark_secondary_color'] ?? '#059669' }}" 
                                            class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                                        <input type="text" value="{{ $settings['dark_secondary_color'] ?? '#059669' }}" readonly
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Couleur d'accent</label>
                                    <div class="flex items-center space-x-2">
                                        <input type="color" name="dark_accent_color" value="{{ $settings['dark_accent_color'] ?? '#D97706' }}" 
                                            class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                                        <input type="text" value="{{ $settings['dark_accent_color'] ?? '#D97706' }}" readonly
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Couleur de danger</label>
                                    <div class="flex items-center space-x-2">
                                        <input type="color" name="dark_danger_color" value="{{ $settings['dark_danger_color'] ?? '#DC2626' }}" 
                                            class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                                        <input type="text" value="{{ $settings['dark_danger_color'] ?? '#DC2626' }}" readonly
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Arrière-plan</label>
                                    <div class="flex items-center space-x-2">
                                        <input type="color" name="dark_background_color" value="{{ $settings['dark_background_color'] ?? '#000000' }}" 
                                            class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                                        <input type="text" value="{{ $settings['dark_background_color'] ?? '#000000' }}" readonly
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Surface</label>
                                    <div class="flex items-center space-x-2">
                                        <input type="color" name="dark_surface_color" value="{{ $settings['dark_surface_color'] ?? '#1F2937' }}" 
                                            class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                                        <input type="text" value="{{ $settings['dark_surface_color'] ?? '#1F2937' }}" readonly
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Texte</label>
                                    <div class="flex items-center space-x-2">
                                        <input type="color" name="dark_text_color" value="{{ $settings['dark_text_color'] ?? '#F9FAFB' }}" 
                                            class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                                        <input type="text" value="{{ $settings['dark_text_color'] ?? '#F9FAFB' }}" readonly
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Aperçu en temps réel -->
                        <div class="mt-8">
                            <h4 class="text-md font-medium text-gray-700 mb-4">Aperçu en temps réel</h4>
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div id="theme-preview" class="space-y-4">
                                    <div class="flex space-x-4">
                                        <button class="preview-btn-primary px-4 py-2 text-white rounded" style="background-color: {{ $settings['primary_color'] ?? '#3B82F6' }}">
                                            Primaire
                                        </button>
                                        <button class="preview-btn-secondary px-4 py-2 text-white rounded" style="background-color: {{ $settings['secondary_color'] ?? '#10B981' }}">
                                            Secondaire
                                        </button>
                                        <button class="preview-btn-accent px-4 py-2 text-white rounded" style="background-color: {{ $settings['accent_color'] ?? '#F59E0B' }}">
                                            Accent
                                        </button>
                                        <button class="preview-btn-danger px-4 py-2 text-white rounded" style="background-color: {{ $settings['danger_color'] ?? '#EF4444' }}">
                                            Danger
                                        </button>
                                    </div>
                                    <div class="preview-surface p-4 rounded-lg border" style="background-color: {{ $settings['surface_color'] ?? '#F9FAFB' }}; border-color: {{ $settings['primary_color'] ?? '#3B82F6' }}">
                                        <p class="preview-text" style="color: {{ $settings['text_color'] ?? '#111827' }}">
                                            Ceci est un aperçu du texte avec les couleurs personnalisées.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Onglet Avancé -->
                    <div id="advanced-tab" class="tab-content hidden">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Paramètres avancés</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <h4 class="text-md font-medium text-gray-700">Performance</h4>
                                <label class="flex items-center">
                                    <input type="checkbox" name="enable_animations" value="1" {{ ($settings['enable_animations'] ?? '1') == '1' ? 'checked' : '' }} 
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Activer les animations de transition</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="enable_shadows" value="1" {{ ($settings['enable_shadows'] ?? '1') == '1' ? 'checked' : '' }} 
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Activer les effets d'ombre</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="enable_gradients" value="1" {{ ($settings['enable_gradients'] ?? '1') == '1' ? 'checked' : '' }} 
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Activer les dégradés de fond</span>
                                </label>
                            </div>
                            <div class="space-y-4">
                                <h4 class="text-md font-medium text-gray-700">Accessibilité</h4>
                                <label class="flex items-center">
                                    <input type="checkbox" name="high_contrast" value="1" {{ ($settings['high_contrast'] ?? '0') == '1' ? 'checked' : '' }} 
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Mode contraste élevé</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="reduced_motion" value="1" {{ ($settings['reduced_motion'] ?? '0') == '1' ? 'checked' : '' }} 
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Réduire les animations</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="large_text" value="1" {{ ($settings['large_text'] ?? '0') == '1' ? 'checked' : '' }} 
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Texte plus grand</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Gestion des onglets
        function switchTab(tabName) {
            // Cacher tous les contenus
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Désactiver tous les boutons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('border-blue-500', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Afficher le contenu sélectionné
            document.getElementById(tabName + '-tab').classList.remove('hidden');
            
            // Activer le bouton sélectionné
            event.target.classList.remove('border-transparent', 'text-gray-500');
            event.target.classList.add('border-blue-500', 'text-blue-600');
        }

        // Appliquer un thème prédéfini
        function applyThemePreset(preset) {
            fetch('{{ route("system-settings.apply-preset") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ preset: preset })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mettre à jour les champs de couleur
                    Object.keys(data.colors).forEach(key => {
                        const input = document.querySelector(`input[name="${key}"]`);
                        if (input) {
                            input.value = data.colors[key];
                            // Mettre à jour le texte affiché
                            const textInput = input.parentElement.querySelector('input[type="text"]');
                            if (textInput) {
                                textInput.value = data.colors[key];
                            }
                        }
                    });
                    
                    // Mettre à jour l'aperçu
                    updatePreview();
                    
                    // Afficher un message de succès
                    showNotification('Thème appliqué avec succès !', 'success');
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('Erreur lors de l\'application du thème', 'error');
            });
        }

        // Réinitialiser le thème
        function resetTheme() {
            if (confirm('Êtes-vous sûr de vouloir réinitialiser tous les paramètres de thème ?')) {
                window.location.href = '{{ route("system-settings.reset-theme") }}';
            }
        }

        // Mettre à jour l'aperçu en temps réel
        function updatePreview() {
            const primaryColor = document.querySelector('input[name="primary_color"]').value;
            const secondaryColor = document.querySelector('input[name="secondary_color"]').value;
            const accentColor = document.querySelector('input[name="accent_color"]').value;
            const dangerColor = document.querySelector('input[name="danger_color"]').value;
            const surfaceColor = document.querySelector('input[name="surface_color"]').value;
            const textColor = document.querySelector('input[name="text_color"]').value;
            
            document.querySelector('.preview-btn-primary').style.backgroundColor = primaryColor;
            document.querySelector('.preview-btn-secondary').style.backgroundColor = secondaryColor;
            document.querySelector('.preview-btn-accent').style.backgroundColor = accentColor;
            document.querySelector('.preview-btn-danger').style.backgroundColor = dangerColor;
            document.querySelector('.preview-surface').style.backgroundColor = surfaceColor;
            document.querySelector('.preview-text').style.color = textColor;
        }

        // Synchroniser les inputs de couleur
        document.querySelectorAll('input[type="color"]').forEach(input => {
            input.addEventListener('input', function() {
                const textInput = this.parentElement.querySelector('input[type="text"]');
                if (textInput) {
                    textInput.value = this.value.toUpperCase();
                }
                updatePreview();
            });
        });

        // Afficher les notifications
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white font-medium z-50 ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            }`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Initialiser l'aperçu au chargement
        document.addEventListener('DOMContentLoaded', function() {
            updatePreview();
        });
    </script>
</x-app-layout>
