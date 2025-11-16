<!-- Système de notifications global -->
<div x-data="notificationSystem()" 
     @notification.window="showNotification($event.detail)"
     @confirm-modal.window="showConfirmModal($event.detail)"
     class="fixed inset-0 z-50 pointer-events-none">
     
    <!-- Notifications Toast -->
    <div class="fixed top-4 right-4 space-y-4 pointer-events-auto">
        <template x-for="notification in notifications" :key="notification.id">
            <div x-show="notification.show"
                 x-transition:enter="transform ease-out duration-300 transition"
                 x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                 x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
                
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <!-- Icône Success -->
                            <svg x-show="notification.type === 'success'" class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            
                            <!-- Icône Error -->
                            <svg x-show="notification.type === 'error'" class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            
                            <!-- Icône Warning -->
                            <svg x-show="notification.type === 'warning'" class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            
                            <!-- Icône Info -->
                            <svg x-show="notification.type === 'info'" class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            <p class="text-sm font-medium text-gray-900" x-text="notification.title"></p>
                            <p class="mt-1 text-sm text-gray-500" x-text="notification.message"></p>
                        </div>
                        
                        <div class="ml-4 flex-shrink-0 flex">
                            <button @click="removeNotification(notification.id)" 
                                class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Barre de progression -->
                <div x-show="notification.duration > 0" class="h-1 bg-gray-200">
                    <div class="h-full transition-all duration-100 ease-linear"
                         :class="{
                             'bg-green-500': notification.type === 'success',
                             'bg-red-500': notification.type === 'error', 
                             'bg-yellow-500': notification.type === 'warning',
                             'bg-blue-500': notification.type === 'info'
                         }"
                         :style="`width: ${notification.progress}%`"></div>
                </div>
            </div>
        </template>
    </div>

    <!-- Modal de confirmation -->
    <div x-show="confirmModal.show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity pointer-events-auto"
         style="display: none;">
         
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.away="closeConfirmModal()">
                
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10"
                         :class="{
                             'bg-red-100': confirmModal.type === 'danger',
                             'bg-yellow-100': confirmModal.type === 'warning',
                             'bg-blue-100': confirmModal.type === 'info'
                         }">
                        <!-- Icône Danger -->
                        <svg x-show="confirmModal.type === 'danger'" class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        
                        <!-- Icône Warning -->
                        <svg x-show="confirmModal.type === 'warning'" class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        
                        <!-- Icône Info -->
                        <svg x-show="confirmModal.type === 'info'" class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="confirmModal.title"></h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" x-text="confirmModal.message"></p>
                        </div>
                        
                        <!-- Champ input si requis -->
                        <div x-show="confirmModal.requireInput" class="mt-4">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input x-model="confirmModal.inputValue" 
                                    :type="confirmModal.inputType"
                                    :placeholder="confirmModal.inputPlaceholder"
                                    class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    @keydown.enter="confirmAction()">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button @click="confirmAction()" 
                        type="button" 
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"
                        :class="{
                            'bg-red-600 hover:bg-red-700 focus:ring-red-500': confirmModal.type === 'danger',
                            'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500': confirmModal.type === 'warning',
                            'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500': confirmModal.type === 'info'
                        }"
                        x-text="confirmModal.confirmText">
                    </button>
                    
                    <button @click="closeConfirmModal()" 
                        type="button" 
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm"
                        x-text="confirmModal.cancelText">
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function notificationSystem() {
    return {
        notifications: [],
        confirmModal: {
            show: false,
            type: 'info',
            title: '',
            message: '',
            confirmText: 'Confirmer',
            cancelText: 'Annuler',
            onConfirm: null,
            requireInput: false,
            inputPlaceholder: '',
            inputValue: '',
            inputType: 'text'
        },
        
        showNotification(data) {
            const id = Date.now() + Math.random();
            const notification = {
                id: id,
                type: data.type || 'info',
                title: data.title || '',
                message: data.message || '',
                duration: data.duration || 5000,
                show: true,
                progress: 100
            };
            
            this.notifications.push(notification);
            
            if (notification.duration > 0) {
                this.startProgress(notification);
            }
        },
        
        startProgress(notification) {
            const interval = 50;
            const step = (interval / notification.duration) * 100;
            
            const timer = setInterval(() => {
                notification.progress -= step;
                if (notification.progress <= 0) {
                    clearInterval(timer);
                    this.removeNotification(notification.id);
                }
            }, interval);
        },
        
        removeNotification(id) {
            const index = this.notifications.findIndex(n => n.id === id);
            if (index > -1) {
                this.notifications[index].show = false;
                setTimeout(() => {
                    this.notifications.splice(index, 1);
                }, 300);
            }
        },
        
        showConfirmModal(data) {
            this.confirmModal = {
                show: true,
                type: data.type || 'info',
                title: data.title || 'Confirmation',
                message: data.message || 'Êtes-vous sûr ?',
                confirmText: data.confirmText || 'Confirmer',
                cancelText: data.cancelText || 'Annuler',
                onConfirm: data.onConfirm || null,
                requireInput: data.requireInput || false,
                inputPlaceholder: data.inputPlaceholder || '',
                inputValue: '',
                inputType: data.inputType || 'text'
            };
        },
        
        confirmAction() {
            if (this.confirmModal.onConfirm) {
                if (this.confirmModal.requireInput) {
                    this.confirmModal.onConfirm(this.confirmModal.inputValue);
                } else {
                    this.confirmModal.onConfirm();
                }
            }
            this.closeConfirmModal();
        },
        
        closeConfirmModal() {
            this.confirmModal.show = false;
        }
    }
}

// Fonctions globales pour faciliter l'utilisation
window.showNotification = function(type, title, message, duration = 5000) {
    window.dispatchEvent(new CustomEvent('notification', {
        detail: { type, title, message, duration }
    }));
};

window.showSuccess = function(title, message = '') {
    window.showNotification('success', title, message);
};

window.showError = function(title, message = '') {
    window.showNotification('error', title, message);
};

window.showWarning = function(title, message = '') {
    window.showNotification('warning', title, message);
};

window.showInfo = function(title, message = '') {
    window.showNotification('info', title, message);
};

window.showConfirm = function(title, message, onConfirm, type = 'info', confirmText = 'Confirmer', cancelText = 'Annuler') {
    window.dispatchEvent(new CustomEvent('confirm-modal', {
        detail: { type, title, message, confirmText, cancelText, onConfirm }
    }));
};

window.showPrompt = function(title, message, onConfirm, inputPlaceholder = '', inputType = 'text', type = 'info', confirmText = 'Confirmer', cancelText = 'Annuler') {
    window.dispatchEvent(new CustomEvent('confirm-modal', {
        detail: { 
            type, 
            title, 
            message, 
            confirmText, 
            cancelText, 
            onConfirm, 
            requireInput: true, 
            inputPlaceholder, 
            inputType 
        }
    }));
};
</script>
