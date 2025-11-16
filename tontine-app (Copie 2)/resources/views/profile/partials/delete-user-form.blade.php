<div class="space-y-4" x-data="deleteAccountForm()">
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="h-5 w-5 text-red-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">
                    Suppression définitive du compte
                </h3>
                <p class="mt-2 text-sm text-red-700">
                    Une fois votre compte supprimé, toutes vos données seront définitivement perdues. 
                    Cette action est <strong>irréversible</strong>. Assurez-vous de sauvegarder toutes les informations importantes.
                </p>
                <ul class="mt-3 text-xs text-red-600 list-disc list-inside space-y-1">
                    <li>Tous vos paiements collectés</li>
                    <li>Votre historique d'activité</li>
                    <li>Vos informations personnelles</li>
                    <li>Votre accès à l'application</li>
                </ul>
            </div>
        </div>
    </div>

    <button @click="confirmDelete()"
        class="px-6 py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white font-medium rounded-lg hover:from-red-700 hover:to-pink-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 flex items-center">
        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
        </svg>
        Supprimer mon compte
    </button>

    <!-- Formulaire caché pour la suppression -->
    <form id="delete-account-form" method="post" action="{{ route('profile.destroy') }}" style="display: none;">
        @csrf
        @method('delete')
        <input type="password" id="delete-password" name="password" required>
    </form>
</div>

<script>
function deleteAccountForm() {
    return {
        confirmDelete() {
            showPrompt(
                'Supprimer définitivement votre compte',
                'Cette action est irréversible. Toutes vos données seront perdues. Entrez votre mot de passe pour confirmer.',
                (password) => {
                    if (password && password.trim()) {
                        document.getElementById('delete-password').value = password;
                        document.getElementById('delete-account-form').submit();
                        showInfo('Suppression en cours...', 'Veuillez patienter...');
                    } else {
                        showWarning('Mot de passe requis', 'Vous devez entrer votre mot de passe pour supprimer votre compte.');
                    }
                },
                'Entrez votre mot de passe',
                'password',
                'danger',
                'Supprimer définitivement',
                'Annuler'
            );
        },

        init() {
            // Afficher les erreurs de suppression s'il y en a
            @if($errors->userDeletion->any())
                @foreach($errors->userDeletion->all() as $error)
                    showError('Erreur de suppression', '{{ $error }}');
                @endforeach
            @endif
        }
    }
}
</script>
