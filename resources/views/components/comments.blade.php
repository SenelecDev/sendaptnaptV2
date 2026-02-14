@props(['type', 'id'])

<div x-data="commentsComponent('{{ $type }}', {{ $id }})" class="card-senelec">
    <div class="p-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="font-semibold text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-senelec-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            Fil de discussion
            <span x-show="comments.length > 0" class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full" x-text="comments.length"></span>
        </h3>
        <button @click="refreshComments()" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
        </button>
    </div>
    
    <!-- Formulaire nouveau commentaire -->
    <div class="p-4 border-b border-gray-100 bg-gray-50">
        <form @submit.prevent="submitComment()">
            <div class="flex gap-3">
                <div class="flex-shrink-0">
                    @if(auth()->user()->photo_url)
                        <img src="{{ auth()->user()->photo_url }}" alt="" class="w-10 h-10 rounded-full object-cover">
                    @else
                        <div class="w-10 h-10 rounded-full bg-senelec-purple/10 flex items-center justify-center text-senelec-purple font-bold text-sm">
                            {{ auth()->user()->initials }}
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <textarea x-model="newComment" 
                              placeholder="Ajouter un commentaire..." 
                              rows="2"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-senelec-purple focus:border-senelec-purple resize-none"></textarea>
                    <div class="flex items-center justify-between mt-2">
                        @if(auth()->user()->hasRole(['admin', 'desa', 'verificateur', 'valideur']))
                        <label class="flex items-center gap-2 text-sm text-gray-500">
                            <input type="checkbox" x-model="isInternal" class="rounded border-gray-300 text-senelec-purple focus:ring-senelec-purple">
                            Note interne (non visible par le demandeur)
                        </label>
                        @else
                        <div></div>
                        @endif
                        <button type="submit" 
                                :disabled="!newComment.trim() || submitting"
                                class="px-4 py-1.5 bg-senelec-purple text-white text-sm rounded-lg hover:bg-senelec-purple/90 disabled:opacity-50 disabled:cursor-not-allowed transition">
                            <span x-show="!submitting">Envoyer</span>
                            <span x-show="submitting">Envoi...</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Liste des commentaires -->
    <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
        <template x-if="loading && comments.length === 0">
            <div class="p-8 text-center text-gray-500">
                <svg class="w-8 h-8 mx-auto text-gray-300 animate-spin mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Chargement...
            </div>
        </template>
        
        <template x-if="!loading && comments.length === 0">
            <div class="p-8 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <p>Aucun commentaire pour le moment</p>
                <p class="text-sm">Soyez le premier à commenter !</p>
            </div>
        </template>
        
        <template x-for="comment in comments" :key="comment.id">
            <div class="p-4">
                <div class="flex gap-3">
                    <div class="flex-shrink-0">
                        <template x-if="comment.user.photo_url">
                            <img :src="comment.user.photo_url" alt="" class="w-10 h-10 rounded-full object-cover">
                        </template>
                        <template x-if="!comment.user.photo_url">
                            <div class="w-10 h-10 rounded-full bg-senelec-purple/10 flex items-center justify-center text-senelec-purple font-bold text-sm"
                                 x-text="comment.user.name?.split(' ').map(n => n[0]).join('').substring(0,2).toUpperCase()">
                            </div>
                        </template>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-medium text-gray-900" x-text="comment.user.name"></span>
                            <span class="text-xs text-gray-400" x-text="formatDate(comment.created_at)"></span>
                            <template x-if="comment.is_internal">
                                <span class="text-xs bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded">Interne</span>
                            </template>
                        </div>
                        <p class="text-gray-700 mt-1 text-sm whitespace-pre-wrap" x-text="comment.content"></p>
                        
                        <div class="flex items-center gap-4 mt-2 text-sm">
                            <button @click="replyTo = comment.id; replyContent = ''" class="text-gray-500 hover:text-senelec-purple">
                                Répondre
                            </button>
                            <template x-if="comment.user_id === {{ auth()->id() }}">
                                <button @click="deleteComment(comment.id)" class="text-red-500 hover:text-red-700">
                                    Supprimer
                                </button>
                            </template>
                        </div>
                        
                        <!-- Formulaire de réponse -->
                        <template x-if="replyTo === comment.id">
                            <div class="mt-3 pl-4 border-l-2 border-senelec-purple/30">
                                <textarea x-model="replyContent" 
                                          placeholder="Votre réponse..." 
                                          rows="2"
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-senelec-purple focus:border-senelec-purple resize-none"></textarea>
                                <div class="flex gap-2 mt-2">
                                    <button @click="submitReply(comment.id)" 
                                            :disabled="!replyContent.trim()"
                                            class="px-3 py-1 bg-senelec-purple text-white text-xs rounded-lg hover:bg-senelec-purple/90 disabled:opacity-50">
                                        Envoyer
                                    </button>
                                    <button @click="replyTo = null" class="px-3 py-1 text-gray-500 text-xs hover:text-gray-700">
                                        Annuler
                                    </button>
                                </div>
                            </div>
                        </template>
                        
                        <!-- Réponses -->
                        <template x-if="comment.replies && comment.replies.length > 0">
                            <div class="mt-3 space-y-3 pl-4 border-l-2 border-gray-200">
                                <template x-for="reply in comment.replies" :key="reply.id">
                                    <div class="flex gap-2">
                                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-xs flex-shrink-0"
                                             x-text="reply.user.name?.split(' ').map(n => n[0]).join('').substring(0,2).toUpperCase()">
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-medium text-gray-900 text-sm" x-text="reply.user.name"></span>
                                                <span class="text-xs text-gray-400" x-text="formatDate(reply.created_at)"></span>
                                            </div>
                                            <p class="text-gray-700 text-sm mt-0.5" x-text="reply.content"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

@push('scripts')
<script>
function commentsComponent(type, id) {
    return {
        comments: [],
        loading: true,
        submitting: false,
        newComment: '',
        isInternal: false,
        replyTo: null,
        replyContent: '',
        
        init() {
            this.refreshComments();
        },
        
        async refreshComments() {
            this.loading = true;
            try {
                const response = await fetch(`/comments?commentable_type=${type}&commentable_id=${id}`);
                this.comments = await response.json();
            } catch (error) {
                console.error('Erreur chargement commentaires:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async submitComment() {
            if (!this.newComment.trim()) return;
            
            this.submitting = true;
            try {
                const response = await fetch('/comments', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        commentable_type: type,
                        commentable_id: id,
                        content: this.newComment,
                        is_internal: this.isInternal,
                    }),
                });
                
                if (response.ok) {
                    this.newComment = '';
                    this.isInternal = false;
                    await this.refreshComments();
                }
            } catch (error) {
                console.error('Erreur envoi commentaire:', error);
            } finally {
                this.submitting = false;
            }
        },
        
        async submitReply(parentId) {
            if (!this.replyContent.trim()) return;
            
            try {
                const response = await fetch('/comments', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        commentable_type: type,
                        commentable_id: id,
                        content: this.replyContent,
                        parent_id: parentId,
                    }),
                });
                
                if (response.ok) {
                    this.replyTo = null;
                    this.replyContent = '';
                    await this.refreshComments();
                }
            } catch (error) {
                console.error('Erreur envoi réponse:', error);
            }
        },
        
        async deleteComment(commentId) {
            if (!confirm('Supprimer ce commentaire ?')) return;
            
            try {
                const response = await fetch(`/comments/${commentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                });
                
                if (response.ok) {
                    await this.refreshComments();
                }
            } catch (error) {
                console.error('Erreur suppression:', error);
            }
        },
        
        formatDate(dateStr) {
            const date = new Date(dateStr);
            const now = new Date();
            const diff = now - date;
            
            if (diff < 60000) return 'À l\'instant';
            if (diff < 3600000) return Math.floor(diff / 60000) + ' min';
            if (diff < 86400000) return Math.floor(diff / 3600000) + ' h';
            if (diff < 604800000) return Math.floor(diff / 86400000) + ' j';
            
            return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
        }
    };
}
</script>
@endpush
