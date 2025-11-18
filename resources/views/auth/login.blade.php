<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion - Tontine App</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-float { animation: float 3s ease-in-out infinite; }
        .animate-fadeInUp { animation: fadeInUp 0.6s ease-out; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Left Side - Presentation -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-600 via-blue-500 to-yellow-500 p-12 text-white relative overflow-hidden">
            <!-- Animated Background -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-20 left-20 w-72 h-72 bg-white rounded-full mix-blend-overlay filter blur-xl animate-float"></div>
                <div class="absolute bottom-20 right-20 w-96 h-96 bg-white rounded-full mix-blend-overlay filter blur-xl animate-float" style="animation-delay: 1s;"></div>
            </div>

            <div class="relative z-10 flex flex-col justify-center max-w-xl">
                <div class="mb-8 animate-fadeInUp">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="bg-white rounded-xl p-3">
                            <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h1 class="text-4xl font-bold">Tontine App</h1>
                    </div>
                    <h2 class="text-3xl font-bold mb-4">Gérez vos tontines en toute simplicité</h2>
                    <p class="text-xl text-blue-100">La solution digitale pour moderniser la gestion de vos tontines</p>
                </div>

                <div class="space-y-6 animate-fadeInUp" style="animation-delay: 0.2s;">
                    <div class="flex items-start space-x-4">
                        <div class="bg-white bg-opacity-20 rounded-lg p-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-1">Gestion des clients</h3>
                            <p class="text-blue-100">Enregistrez et suivez facilement tous vos clients</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="bg-white bg-opacity-20 rounded-lg p-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-1">Suivi des paiements</h3>
                            <p class="text-blue-100">Collectez et validez les paiements en temps réel</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="bg-white bg-opacity-20 rounded-lg p-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-1">Rapports détaillés</h3>
                            <p class="text-blue-100">Exportez vos données en PDF et Excel</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="bg-white bg-opacity-20 rounded-lg p-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-1">Sécurité maximale</h3>
                            <p class="text-blue-100">Vos données sont protégées et traçables</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-4 sm:p-8 relative">
            <!-- Mobile Background Gradient -->
            <div class="lg:hidden absolute inset-0 bg-gradient-to-br from-blue-600/5 via-blue-500/5 to-yellow-500/5"></div>
            
            <div class="max-w-md w-full relative z-10">
                <!-- Mobile Header with Animation -->
                <div class="text-center mb-6 sm:mb-8 animate-fadeInUp">
                    <div class="lg:hidden flex justify-center mb-4">
                        <div class="bg-gradient-to-r from-blue-600 to-yellow-500 rounded-xl p-3 shadow-lg animate-float">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <h1 class="lg:hidden text-2xl sm:text-3xl font-bold bg-gradient-to-r from-blue-600 to-yellow-500 bg-clip-text text-transparent mb-2">
                        Tontine App
                    </h1>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Bon retour !</h2>
                    <p class="mt-2 text-sm sm:text-base text-gray-600">Connectez-vous pour accéder à votre espace</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-lg">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="bg-white shadow-xl rounded-2xl p-6 sm:p-8 space-y-5 sm:space-y-6 animate-fadeInUp" style="animation-delay: 0.2s;">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Adresse Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" 
                            placeholder="admin@tontine.local">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Mot de passe</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" 
                            placeholder="••••••••">
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600">Se souvenir de moi</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-800">
                                Mot de passe oublié ?
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-yellow-500 text-white py-3 px-4 rounded-lg font-medium hover:from-blue-700 hover:to-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200 transform hover:scale-[1.02]">
                        Se connecter
                    </button>
                </form>

                <!-- Mobile Animated Decoration -->
                <div class="lg:hidden mt-8 flex justify-center animate-fadeInUp" style="animation-delay: 0.4s;">
                    <div class="relative">
                        <!-- Cercles animés -->
                        <div class="w-32 h-32 bg-gradient-to-br from-blue-400 to-yellow-500 rounded-full opacity-20 animate-float"></div>
                        <div class="absolute top-4 left-4 w-24 h-24 bg-gradient-to-br from-yellow-400 to-blue-500 rounded-full opacity-30 animate-float" style="animation-delay: 0.5s;"></div>
                        <div class="absolute top-8 left-8 w-16 h-16 bg-gradient-to-br from-blue-500 to-yellow-400 rounded-full opacity-40 animate-float" style="animation-delay: 1s;"></div>
                    </div>
                </div>

                <!-- Footer Text -->
                <div class="lg:hidden mt-6 text-center animate-fadeInUp" style="animation-delay: 0.6s;">
                    <p class="text-sm text-gray-500">
                        Gérez vos tontines en toute sécurité
                    </p>
                    <p class="text-xs text-gray-400 mt-2">
                        © 2025 Tontine App. Tous droits réservés.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
