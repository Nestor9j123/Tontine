<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vérification 2FA - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="mx-auto h-12 w-12 bg-gradient-to-r from-blue-600 to-yellow-500 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Vérification de sécurité
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Entrez le code de votre application d'authentification
                </p>
            </div>

            <form class="mt-8 space-y-6" method="POST" action="{{ route('two-factor.challenge.process') }}">
                @csrf
                
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="one_time_password" class="sr-only">Code de vérification</label>
                        <input id="one_time_password" 
                               name="one_time_password" 
                               type="text" 
                               maxlength="10"
                               autocomplete="one-time-code" 
                               required 
                               class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm text-center text-2xl font-mono tracking-widest"
                               placeholder="123456"
                               autofocus>
                        @error('one_time_password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <button type="submit" 
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-blue-500 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </span>
                        Vérifier
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Vous pouvez aussi utiliser un code de récupération
                    </p>
                    <div class="mt-4">
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-red-600 hover:text-red-500">
                                Se déconnecter
                            </button>
                        </form>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Auto-focus et auto-submit quand 6 chiffres sont entrés
        document.getElementById('one_time_password').addEventListener('input', function(e) {
            const value = e.target.value.replace(/\D/g, ''); // Supprimer les non-chiffres
            e.target.value = value;
            
            if (value.length === 6) {
                // Auto-submit après un court délai
                setTimeout(() => {
                    e.target.form.submit();
                }, 500);
            }
        });
    </script>
</body>
</html>
