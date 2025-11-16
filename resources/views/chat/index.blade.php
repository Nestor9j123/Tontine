<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center" x-data="{ notificationsEnabled: true, toggleNotifications() { this.notificationsEnabled = !this.notificationsEnabled; } }">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                Messagerie
            </h2>
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                    <span class="text-sm text-gray-600">En ligne</span>
                </div>
                
                <!-- Bouton notifications sonores -->
                <button @click="toggleNotifications()" 
                    :class="notificationsEnabled ? 'text-blue-600' : 'text-gray-400'"
                    class="flex items-center space-x-1 text-sm hover:text-blue-700 transition-colors"
                    :title="notificationsEnabled ? 'Désactiver les sons' : 'Activer les sons'">
                    <!-- Icône son activé -->
                    <svg x-show="notificationsEnabled" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 14.142M8.586 8.586A2 2 0 1011.414 11.414L8.586 8.586zM12 6v6l4 2"/>
                    </svg>
                    <!-- Icône son désactivé -->
                    <svg x-show="!notificationsEnabled" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/>
                    </svg>
                    <span x-text="notificationsEnabled ? 'Sons' : 'Muet'"></span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="h-[85vh] flex bg-white rounded-2xl shadow-lg overflow-hidden" x-data="chatApp()">
        <!-- Sidebar - Liste des conversations -->
        <div class="w-full md:w-1/3 border-r border-gray-200 flex flex-col" 
             x-show="!isMobile || !selectedConversation">
            <!-- Header du sidebar -->
            <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Conversations</h3>
                    <button @click="showNewChatModal = true" 
                        class="p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Barre de recherche -->
                <div class="relative">
                    <input type="text" 
                        placeholder="Rechercher une conversation..." 
                        x-model="searchQuery"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Liste des conversations -->
            <div class="flex-1 overflow-y-auto">
                <template x-for="conversation in filteredConversations" :key="conversation.id">
                    <div @click="selectConversation(conversation)" 
                        :class="selectedConversation?.id === conversation.id ? 'bg-blue-50 border-r-4 border-blue-600' : 'hover:bg-gray-50'"
                        class="p-4 border-b border-gray-100 cursor-pointer transition-colors">
                        
                        <div class="flex items-center space-x-3">
                            <!-- Avatar -->
                            <div class="relative">
                                <img :src="getConversationAvatar(conversation)" 
                                    :alt="getConversationTitle(conversation)"
                                    class="w-12 h-12 rounded-full">
                                <div x-show="conversation.unread_count > 0" 
                                    class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center"
                                    x-text="conversation.unread_count"></div>
                            </div>
                            
                            <!-- Infos conversation -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-medium text-gray-900 truncate" x-text="getConversationTitle(conversation)"></h4>
                                    <div class="flex items-center space-x-2">
                                        <!-- Indicateur de lecture pour le dernier message -->
                                        <div x-show="conversation.latest_message && isOwnMessage(conversation.latest_message)" class="flex items-center">
                                            <!-- Message lu (œil vert) -->
                                            <svg x-show="isLastMessageRead(conversation)" class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" title="Lu">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            
                                            <!-- Message non lu (point orange) -->
                                            <div x-show="!isLastMessageRead(conversation)" class="w-2 h-2 bg-orange-400 rounded-full" title="Envoyé"></div>
                                        </div>
                                        
                                        <span class="text-xs text-gray-500" x-text="formatTime(conversation.last_message_at)"></span>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-500 truncate mt-1" x-text="conversation.latest_message?.content || 'Commencer la conversation'"></p>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- État vide -->
                <div x-show="conversations.length === 0" class="p-8 text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <p class="text-lg font-medium mb-2">Aucune conversation</p>
                    <p class="text-sm">Commencez une nouvelle conversation pour démarrer</p>
                </div>
            </div>
        </div>

        <!-- Zone de chat principale -->
        <div class="flex-1 flex flex-col" 
             x-show="!isMobile || selectedConversation">
            <!-- Header du chat -->
            <div x-show="selectedConversation" class="p-4 md:p-6 bg-white border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <!-- Bouton retour mobile -->
                    <button @click="selectedConversation = null" x-show="isMobile" class="p-2 text-gray-600 hover:text-gray-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    
                    <img :src="selectedConversation ? getConversationAvatar(selectedConversation) : ''" 
                        :alt="selectedConversation ? getConversationTitle(selectedConversation) : ''"
                        class="w-10 h-10 rounded-full">
                    <div>
                        <h3 class="font-medium text-gray-900" x-text="selectedConversation ? getConversationTitle(selectedConversation) : ''"></h3>
                        <p class="text-sm text-gray-500">En ligne</p>
                    </div>
                </div>
                
                <div x-show="!isMobile" class="flex items-center space-x-2">
                    <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </button>
                    <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Zone des messages -->
            <div x-show="selectedConversation" class="flex-1 overflow-y-auto p-4 md:p-6 flex flex-col" x-ref="messagesContainer">
                <!-- Spacer pour pousser les messages vers le bas -->
                <div class="flex-1"></div>
                
                <!-- Messages -->
                <div class="space-y-4">
                    <template x-for="message in messages" :key="message.id">
                    <div :class="message.is_own ? 'flex justify-end' : 'flex justify-start'">
                        <div :class="message.is_own ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-900'" 
                            class="max-w-[280px] md:max-w-xs lg:max-w-md px-3 md:px-4 py-2 rounded-2xl">
                            <div x-show="!message.is_own" class="flex items-center space-x-2 mb-1">
                                <img :src="message.user.avatar" :alt="message.user.name" class="w-6 h-6 rounded-full">
                                <span class="text-xs font-medium" x-text="message.user.name"></span>
                            </div>
                            <p class="text-sm" x-text="message.content"></p>
                            <div class="flex items-center justify-between mt-1">
                                <p :class="message.is_own ? 'text-blue-100' : 'text-gray-500'" 
                                    class="text-xs" x-text="message.created_at"></p>
                                
                                <!-- Indicateurs de lecture pour les messages envoyés -->
                                <div x-show="message.is_own" class="flex items-center ml-2">
                                    <!-- Message lu (œil vert) -->
                                    <div x-show="message.is_read" class="flex items-center space-x-1" title="Lu">
                                        <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </div>
                                    
                                    <!-- Message envoyé mais non lu (point orange) -->
                                    <div x-show="!message.is_read" class="flex items-center space-x-1" title="Envoyé">
                                        <div class="w-2 h-2 bg-orange-400 rounded-full"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </template>

                    <!-- Indicateur de frappe -->
                    <div x-show="isTyping" class="flex justify-start">
                        <div class="bg-gray-100 px-4 py-2 rounded-2xl">
                            <div class="flex space-x-1">
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Zone de saisie -->
            <div x-show="selectedConversation" class="p-3 md:p-6 bg-gray-50 border-t border-gray-200">
                <form @submit.prevent="sendMessage()" class="flex items-end space-x-2 md:space-x-4">
                    <div class="flex-1">
                        <textarea x-model="newMessage" 
                            @keydown.enter.prevent="sendMessage()"
                            @input="handleTyping()"
                            placeholder="Tapez votre message..."
                            rows="1"
                            class="w-full px-3 md:px-4 py-2 md:py-3 border border-gray-300 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none text-sm md:text-base"></textarea>
                    </div>
                    
                    <div class="flex items-center space-x-1 md:space-x-2">
                        <button type="button" x-show="!isMobile" class="p-3 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                        </button>
                        
                        <button type="submit" 
                            :disabled="!newMessage.trim()"
                            :class="newMessage.trim() ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-300 cursor-not-allowed'"
                            class="p-2 md:p-3 text-white rounded-full transition-colors">
                            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- État vide du chat -->
            <div x-show="!selectedConversation" class="flex-1 flex items-center justify-center bg-gray-50">
                <div class="text-center">
                    <svg class="w-24 h-24 mx-auto mb-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">Sélectionnez une conversation</h3>
                    <p class="text-gray-500 mb-6">Choisissez une conversation existante ou commencez-en une nouvelle</p>
                    <button @click="showNewChatModal = true" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Nouvelle conversation
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal nouvelle conversation -->
        <div x-show="showNewChatModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
             style="display: none;">
            
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-4 md:p-6"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.away="showNewChatModal = false">
                
                <h3 class="text-lg font-medium text-gray-900 mb-4">Nouvelle conversation</h3>
                
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    <template x-for="user in availableUsers" :key="user.id">
                        <div @click="startConversation(user)" 
                            class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <img :src="user.avatar" :alt="user.name" class="w-10 h-10 rounded-full">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900" x-text="user.name"></h4>
                                <p class="text-xs text-gray-500 capitalize" x-text="user.role"></p>
                            </div>
                            <div x-show="user.is_online" class="w-3 h-3 bg-green-400 rounded-full"></div>
                        </div>
                    </template>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button @click="showNewChatModal = false" 
                        class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function chatApp() {
            return {
                conversations: {!! json_encode($conversations ?? []) !!},
                availableUsers: {!! json_encode($availableUsers ?? []) !!},
                selectedConversation: null,
                messages: [],
                newMessage: '',
                searchQuery: '',
                showNewChatModal: false,
                isTyping: false,
                typingTimer: null,
                isMobile: window.innerWidth < 768,
                sendSound: null,
                receiveSound: null,
                notificationsEnabled: true,

                init() {
                    // Charger la préférence de notifications
                    try {
                        const savedNotificationPref = localStorage.getItem('chatNotificationsEnabled');
                        if (savedNotificationPref !== null) {
                            this.notificationsEnabled = savedNotificationPref === 'true';
                        }
                    } catch (error) {
                        console.log('LocalStorage non disponible:', error);
                    }
                    
                    // Initialiser les notifications sonores
                    try {
                        this.initNotificationSounds();
                    } catch (error) {
                        console.log('Erreur initialisation sons:', error);
                    }
                    
                    // Gérer le redimensionnement de la fenêtre
                    let resizeTimer;
                    window.addEventListener('resize', () => {
                        clearTimeout(resizeTimer);
                        resizeTimer = setTimeout(() => {
                            this.isMobile = window.innerWidth < 768;
                        }, 100);
                    });
                    
                    // Mettre à jour le badge de la sidebar au démarrage
                    this.updateSidebarBadge();
                    
                    // Fonction globale pour les erreurs
                    window.showError = (title, message) => {
                        console.error(title + ':', message);
                        // Vous pouvez utiliser un système de notification ici
                        alert(title + ': ' + message);
                    };
                    
                    console.log('Chat app initialized successfully');
                },

                get filteredConversations() {
                    let conversations = this.conversations;
                    
                    // Filtrer par recherche si nécessaire
                    if (this.searchQuery) {
                        conversations = conversations.filter(conv => 
                            this.getConversationTitle(conv).toLowerCase().includes(this.searchQuery.toLowerCase())
                        );
                    }
                    
                    // Trier par heure du dernier message (plus récent en premier)
                    return conversations.sort((a, b) => {
                        const dateA = new Date(a.last_message_at || a.created_at);
                        const dateB = new Date(b.last_message_at || b.created_at);
                        return dateB - dateA; // Plus récent en premier
                    });
                },

                async selectConversation(conversation) {
                    this.selectedConversation = conversation;
                    await this.loadMessages(conversation);
                    
                    // Mettre à jour le compteur de messages non lus
                    conversation.unread_count = 0;
                    
                    // Mettre à jour le badge de la sidebar
                    this.updateSidebarBadge();
                    
                    // Marquer les messages comme lus côté serveur
                    await this.markConversationAsRead(conversation);
                },

                async loadMessages(conversation) {
                    try {
                        const response = await fetch(`/chat/conversations/${conversation.uuid}/messages`);
                        const data = await response.json();
                        this.messages = data.messages;
                        
                        // Scroll vers le bas
                        this.$nextTick(() => {
                            this.scrollToBottom();
                        });
                    } catch (error) {
                        console.error('Erreur lors du chargement des messages:', error);
                        showError('Erreur', 'Impossible de charger les messages');
                    }
                },

                async sendMessage() {
                    if (!this.newMessage.trim() || !this.selectedConversation) return;

                    const messageContent = this.newMessage.trim();
                    this.newMessage = '';

                    try {
                        const response = await fetch(`/chat/conversations/${this.selectedConversation.uuid}/messages`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                content: messageContent,
                                type: 'text'
                            })
                        });

                        const data = await response.json();
                        this.messages.push(data.message);
                        
                        // Mettre à jour le dernier message de la conversation
                        if (this.selectedConversation) {
                            this.selectedConversation.latest_message = {
                                content: data.message.content,
                                user_id: data.message.user.id,
                                is_read: false // Nouveau message, pas encore lu
                            };
                            this.selectedConversation.last_message_at = data.conversation_updated_at;
                        }
                        
                        // Scroll vers le bas
                        this.$nextTick(() => {
                            this.scrollToBottom();
                        });
                    } catch (error) {
                        console.error('Erreur lors de l\'envoi du message:', error);
                        showError('Erreur', 'Impossible d\'envoyer le message');
                    }
                },

                async startConversation(user) {
                    try {
                        const response = await fetch('/chat/conversations/start', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                user_id: user.id
                            })
                        });

                        const data = await response.json();
                        
                        // Ajouter la conversation à la liste si elle n'existe pas
                        const existingConv = this.conversations.find(c => c.uuid === data.conversation.uuid);
                        if (!existingConv) {
                            this.conversations.unshift(data.conversation);
                        }
                        
                        // Sélectionner la conversation
                        await this.selectConversation(data.conversation);
                        this.showNewChatModal = false;
                    } catch (error) {
                        console.error('Erreur lors de la création de la conversation:', error);
                        showError('Erreur', 'Impossible de créer la conversation');
                    }
                },

                async markConversationAsRead(conversation) {
                    try {
                        const response = await fetch(`/chat/conversations/${conversation.uuid}/read`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        
                        const data = await response.json();
                        
                        // Mettre à jour le statut de lecture dans toutes les conversations
                        if (data.updated_conversations) {
                            data.updated_conversations.forEach(updatedConv => {
                                const conv = this.conversations.find(c => c.uuid === updatedConv.uuid);
                                if (conv && conv.latest_message) {
                                    conv.latest_message.is_read = updatedConv.latest_message_is_read;
                                }
                            });
                        }
                        
                    } catch (error) {
                        console.error('Erreur lors du marquage comme lu:', error);
                    }
                },


                handleTyping() {
                    // Logique pour indiquer que l'utilisateur tape
                    clearTimeout(this.typingTimer);
                    this.typingTimer = setTimeout(() => {
                        // Arrêter l'indicateur de frappe
                    }, 1000);
                },

                scrollToBottom() {
                    if (this.$refs.messagesContainer) {
                        this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
                    }
                },

                getConversationTitle(conversation) {
                    return conversation.title || 'Conversation';
                },

                getConversationAvatar(conversation) {
                    return conversation.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(conversation.title || 'Chat')}&background=random&size=40`;
                },

                isOwnMessage(message) {
                    // Vérifier si le message est le nôtre (à implémenter selon la structure de vos données)
                    return message && message.user_id === @json(auth()->id());
                },

                isLastMessageRead(conversation) {
                    // Pour l'instant, on peut utiliser une logique simple
                    // Dans une vraie implémentation, il faudrait vérifier le statut de lecture
                    return conversation.latest_message && conversation.latest_message.is_read;
                },

                formatTime(timestamp) {
                    if (!timestamp) return '';
                    const date = new Date(timestamp);
                    const now = new Date();
                    const diff = now - date;
                    
                    if (diff < 24 * 60 * 60 * 1000) {
                        return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
                    } else {
                        return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' });
                    }
                },

                // Méthodes pour mettre à jour le badge de la sidebar
                updateSidebarBadge() {
                    const totalUnread = this.conversations.reduce((total, conv) => {
                        return total + (conv.unread_count || 0);
                    }, 0);
                    
                    const badge = document.getElementById('unread-messages-badge');
                    const countSpan = document.getElementById('unread-count');
                    
                    if (badge && countSpan) {
                        if (totalUnread > 0) {
                            badge.style.display = '';
                            countSpan.textContent = totalUnread;
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                },

                // Méthodes pour les notifications sonores
                initNotificationSounds() {
                    try {
                        // Son d'envoi (plus aigu)
                        this.sendSound = this.createNotificationSound(800, 0.1, 0.1);
                        
                        // Son de réception (plus grave)
                        this.receiveSound = this.createNotificationSound(400, 0.15, 0.2);
                    } catch (error) {
                        console.log('Audio non supporté:', error);
                        this.notificationsEnabled = false;
                    }
                },

                createNotificationSound(frequency, duration, volume) {
                    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                    
                    return () => {
                        if (!this.notificationsEnabled) return;
                        
                        const oscillator = audioContext.createOscillator();
                        const gainNode = audioContext.createGain();
                        
                        oscillator.connect(gainNode);
                        gainNode.connect(audioContext.destination);
                        
                        oscillator.frequency.setValueAtTime(frequency, audioContext.currentTime);
                        oscillator.type = 'sine';
                        
                        gainNode.gain.setValueAtTime(0, audioContext.currentTime);
                        gainNode.gain.linearRampToValueAtTime(volume, audioContext.currentTime + 0.01);
                        gainNode.gain.exponentialRampToValueAtTime(0.001, audioContext.currentTime + duration);
                        
                        oscillator.start(audioContext.currentTime);
                        oscillator.stop(audioContext.currentTime + duration);
                    };
                },

                playNotificationSound(type) {
                    if (!this.notificationsEnabled) return;
                    
                    try {
                        if (type === 'send' && this.sendSound) {
                            this.sendSound();
                        } else if (type === 'receive' && this.receiveSound) {
                            this.receiveSound();
                        }
                    } catch (error) {
                        console.log('Erreur de lecture audio:', error);
                    }
                },

                toggleNotifications() {
                    this.notificationsEnabled = !this.notificationsEnabled;
                    
                    // Sauvegarder la préférence
                    try {
                        localStorage.setItem('chatNotificationsEnabled', this.notificationsEnabled);
                    } catch (error) {
                        console.log('LocalStorage non disponible:', error);
                    }
                    
                    // Jouer un son de test si on active
                    if (this.notificationsEnabled) {
                        setTimeout(() => {
                            this.playNotificationSound('send');
                        }, 200);
                    }
                }
            }
        }
    </script>
</x-app-layout>
