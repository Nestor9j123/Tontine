<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Authentification à Deux Facteurs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($user->google2fa_enabled)
                        <!-- 2FA Activée -->
                        <div class="mb-6">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-green-800">2FA Activée</h3>
                                    <p class="text-sm text-green-600">Votre compte est protégé par l'authentification à deux facteurs</p>
                                    <p class="text-xs text-gray-500">Activée le {{ $user->google2fa_enabled_at->format('d/m/Y à H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Codes de récupération -->
                        @if($user->backup_codes && count($user->backup_codes) > 0)
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                            <h4 class="font-semibold text-yellow-800 mb-2">Codes de récupération</h4>
                            <p class="text-sm text-yellow-700 mb-3">Gardez ces codes en lieu sûr. Ils vous permettront d'accéder à votre compte si vous perdez votre appareil.</p>
                            <div class="grid grid-cols-2 gap-2 mb-3">
                                @foreach($user->backup_codes as $code)
                                    <code class="bg-white px-2 py-1 rounded text-sm font-mono">{{ $code }}</code>
                                @endforeach
                            </div>
                            <form method="POST" action="{{ route('two-factor.regenerate-backup-codes') }}" class="inline">
                                @csrf
                                <input type="password" name="password" placeholder="Mot de passe" required class="mr-2 px-3 py-1 border rounded text-sm">
                                <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded text-sm">
                                    Régénérer les codes
                                </button>
                            </form>
                        </div>
                        @endif

                        <!-- Désactiver la 2FA -->
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <h4 class="font-semibold text-red-800 mb-2">Désactiver la 2FA</h4>
                            <p class="text-sm text-red-700 mb-3">Attention : Désactiver la 2FA rendra votre compte moins sécurisé.</p>
                            <form method="POST" action="{{ route('two-factor.disable') }}" onsubmit="return confirm('Êtes-vous sûr de vouloir désactiver la 2FA ?')">
                                @csrf
                                <div class="flex items-center space-x-2">
                                    <input type="password" name="password" placeholder="Mot de passe" required class="px-3 py-2 border rounded">
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                                        Désactiver la 2FA
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <!-- 2FA Non activée -->
                        <div class="mb-6">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-red-800">2FA Non Activée</h3>
                                    <p class="text-sm text-red-600">Votre compte n'est pas protégé par l'authentification à deux facteurs</p>
                                </div>
                            </div>
                        </div>

                        <!-- Configuration de la 2FA -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Instructions -->
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-3">Comment activer la 2FA ?</h4>
                                <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700">
                                    <li>Installez une application d'authentification comme <strong>Google Authenticator</strong> ou <strong>Authy</strong></li>
                                    <li>Scannez le QR code ci-contre avec votre application</li>
                                    <li>Entrez le code à 6 chiffres généré par l'application</li>
                                    <li>Confirmez avec votre mot de passe</li>
                                </ol>

                                <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                                    <p class="text-sm text-blue-800">
                                        <strong>Secret manuel :</strong><br>
                                        <code class="bg-white px-2 py-1 rounded text-xs">{{ $secret }}</code>
                                    </p>
                                    <p class="text-xs text-blue-600 mt-1">
                                        Utilisez ce code si vous ne pouvez pas scanner le QR code
                                    </p>
                                </div>
                            </div>

                            <!-- QR Code et formulaire -->
                            <div>
                                @if($qrCodeUrl)
                                <div class="text-center mb-4">
                                    <div class="inline-block p-4 bg-white border-2 border-gray-200 rounded-lg">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}" 
                                             alt="QR Code 2FA" class="w-48 h-48">
                                    </div>
                                </div>
                                @endif

                                <!-- Formulaire d'activation -->
                                <form method="POST" action="{{ route('two-factor.enable') }}" class="space-y-4">
                                    @csrf
                                    <div>
                                        <label for="one_time_password" class="block text-sm font-medium text-gray-700">Code de vérification</label>
                                        <input type="text" 
                                               id="one_time_password" 
                                               name="one_time_password" 
                                               maxlength="6" 
                                               placeholder="123456"
                                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               required>
                                        @error('one_time_password')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                                        <input type="password" 
                                               id="password" 
                                               name="password" 
                                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               required>
                                        @error('password')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Activer la 2FA
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
