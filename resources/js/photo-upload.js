// Composant Alpine.js réutilisable pour l'upload de photos

window.photoUploadMixin = {
    selectedFiles: [],
    
    handleFiles(event) {
        const files = Array.from(event.target.files);
        
        // Vider la liste précédente pour photos multiples
        this.selectedFiles = [];
        
        files.forEach((file, index) => {
            // Vérifications de validation
            if (!this.validateFile(file)) return;
            
            // Créer URL pour prévisualisation
            const fileUrl = URL.createObjectURL(file);
            
            this.selectedFiles.push({
                file: file,
                name: file.name,
                url: fileUrl,
                size: this.formatFileSize(file.size),
                index: index
            });
        });
        
        if (this.selectedFiles.length > 0) {
            window.showSuccess && showSuccess(`${this.selectedFiles.length} photo(s) sélectionnée(s)`);
        }
    },
    
    handleFile(event) {
        const file = event.target.files[0];
        
        if (!file) return;
        
        // Réinitialiser
        this.selectedFile = null;
        
        // Vérifications de validation
        if (!this.validateFile(file)) {
            this.clearInput();
            return;
        }
        
        // Créer URL pour prévisualisation
        const fileUrl = URL.createObjectURL(file);
        
        this.selectedFile = {
            file: file,
            name: file.name,
            url: fileUrl,
            size: this.formatFileSize(file.size)
        };
        
        window.showSuccess && showSuccess(`Photo sélectionnée : ${file.name}`);
    },
    
    validateFile(file) {
        // Vérifier le type de fichier
        if (!file.type.startsWith('image/')) {
            window.showError && showError('Fichier invalide', `"${file.name}" n'est pas une image valide`);
            return false;
        }
        
        // Vérifier la taille (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            window.showError && showError('Fichier trop volumineux', `"${file.name}" dépasse 2MB`);
            return false;
        }
        
        // Vérifier les formats supportés
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type.toLowerCase())) {
            window.showError && showError('Format non supporté', `"${file.name}" - Formats acceptés: JPG, PNG, GIF, WebP`);
            return false;
        }
        
        return true;
    },
    
    removeFile(index) {
        if (this.selectedFiles && this.selectedFiles[index]) {
            // Nettoyer l'URL de l'objet
            if (this.selectedFiles[index].url) {
                URL.revokeObjectURL(this.selectedFiles[index].url);
            }
            
            this.selectedFiles.splice(index, 1);
            
            // Mettre à jour l'input file
            this.updateFileInput();
        } else if (this.selectedFile) {
            // Pour upload simple
            if (this.selectedFile.url) {
                URL.revokeObjectURL(this.selectedFile.url);
            }
            
            this.selectedFile = null;
            this.clearInput();
        }
    },
    
    updateFileInput() {
        // Créer un nouveau DataTransfer pour mettre à jour l'input
        const dt = new DataTransfer();
        this.selectedFiles.forEach(fileObj => {
            dt.items.add(fileObj.file);
        });
        
        const input = document.getElementById('photos') || document.getElementById('photo');
        if (input) {
            input.files = dt.files;
        }
    },
    
    clearInput() {
        const input = document.getElementById('photos') || document.getElementById('photo');
        if (input) {
            input.value = '';
        }
    },
    
    formatFileSize(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }
};

// Fonctions helper pour Alpine.js
window.photoUpload = () => ({
    ...window.photoUploadMixin,
    selectedFiles: []
});

window.userPhotoUpload = () => ({
    ...window.photoUploadMixin,
    selectedFile: null
});
