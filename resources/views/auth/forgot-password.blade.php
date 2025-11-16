<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mot de passe oublié - Tontine App</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
    <div class="min-h-screen flex items-center justify-center p-4 sm:p-8 relative">
        <!-- Mobile Background Gradient -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 via-blue-500/5 to-yellow-500/5"></div>
        
        <div class="max-w-md w-full relative z-10">
            <!-- Header with Animation -->
            <div class="text-center mb-6 sm:mb-8 animate-fadeInUp">
                <a href="{{ route('login') }}" class="inline-flex justify-center mb-4">
                    <div class="bg-gradient-to-r from-blue-600 to-yellow-500 rounded-xl p-3 shadow-lg animate-float">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                </a>
                <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-blue-600 to-yellow-500 bg-clip-text text-transparent mb-2">
                    Mot de passe oublié ?
                </h1>
                <p class="text-sm sm:text-base text-gray-600">
                    Pas de problème ! Entrez votre email et nous vous enverrons un lien de réinitialisation.
                </p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg animate-fadeInUp">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-green-800 font-medium">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="bg-white shadow-xl rounded-2xl p-6 sm:p-8 space-y-6 animate-fadeInUp" style="animation-delay: 0.2s;">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Adresse Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus 
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" 
                            placeholder="votre@email.com">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-yellow-500 text-white py-3 px-4 rounded-lg font-medium hover:from-blue-700 hover:to-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200 transform hover:scale-[1.02]">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Envoyer le lien de réinitialisation
                    </div>
                </button>

                <!-- Back to Login -->
                <div class="text-center pt-4 border-t border-gray-200">
                    <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium inline-flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Retour à la connexion
                    </a>
                </div>
            </form>

            <!-- Animated Decoration -->
            <div class="mt-8 flex justify-center animate-fadeInUp" style="animation-delay: 0.4s;">
                <div class="relative">
                    <div class="w-24 h-24 bg-gradient-to-br from-blue-400 to-yellow-500 rounded-full opacity-20 animate-float"></div>
                    <div class="absolute top-3 left-3 w-18 h-18 bg-gradient-to-br from-yellow-400 to-blue-500 rounded-full opacity-30 animate-float" style="animation-delay: 0.5s;"></div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-6 text-center animate-fadeInUp" style="animation-delay: 0.6s;">
                <p class="text-xs text-gray-400">
                    © 2025 Tontine App. Tous droits réservés.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
