// Helper functions pour l'upload de photos avec fallback si les toasts n'existent pas

window.safeShowError = function(title, message) {
    if (typeof window.showError === 'function') {
        window.showError(title, message);
    } else {
        console.error(`${title}: ${message}`);
        alert(`Erreur: ${title}\n${message}`);
    }
};

window.safeShowSuccess = function(message) {
    if (typeof window.showSuccess === 'function') {
        window.showSuccess(message);
    } else {
        console.log(`Succès: ${message}`);
    }
};

// Validation helper
window.validateImageFile = function(file) {
    // Vérifier le type de fichier
    if (!file.type.startsWith('image/')) {
        window.safeShowError('Fichier invalide', `"${file.name}" n'est pas une image valide`);
        return false;
    }
    
    // Vérifier la taille (2MB max)
    if (file.size > 2 * 1024 * 1024) {
        window.safeShowError('Fichier trop volumineux', `"${file.name}" dépasse 2MB`);
        return false;
    }
    
    // Vérifier les formats supportés
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type.toLowerCase())) {
        window.safeShowError('Format non supporté', `"${file.name}" - Formats acceptés: JPG, PNG, GIF, WebP`);
        return false;
    }
    
    return true;
};

// Format de taille de fichier
window.formatFileSize = function(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
};
