@extends('layouts.app')

@section('title', 'Aide & Support — Green Express')

@section('content')
<style>
@keyframes fade-up {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-up { animation: fade-up 0.5s ease-out forwards; }
</style>

<div class="max-w-2xl mx-auto px-4 py-6 lg:py-10" x-data="helpChat()" x-init="init()">
    {{-- En-tête --}}
    <div class="text-center mb-8 animate-fade-up">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-600 to-emerald-800 shadow-lg mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Service Client Green Express</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 max-w-md mx-auto">
            Posez vos questions sur les commandes, abonnements, livraisons, paiements et plus encore.
        </p>
    </div>

    {{-- Zone de chat --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden animate-fade-up" style="animation-delay: 0.1s; opacity: 0;">
        {{-- Messages --}}
        <div x-ref="messages" class="px-4 py-6 space-y-4 max-h-[50vh] overflow-y-auto" style="scroll-behavior: smooth;">
            {{-- Message de bienvenue --}}
            <div class="flex gap-3">
                <div class="flex-shrink-0 w-9 h-9 rounded-full bg-emerald-600 flex items-center justify-center text-white text-xs font-bold">GE</div>
                <div class="flex-1 bg-slate-100 dark:bg-slate-800 rounded-2xl rounded-tl-sm px-4 py-3">
                    <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">
                        Bonjour 👋 Je suis le Service Client Green Express. Comment puis-je vous aider aujourd'hui ?
                    </p>
                </div>
            </div>

            {{-- Messages dynamiques --}}
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="msg.role === 'user' ? 'flex gap-3 flex-row-reverse' : 'flex gap-3'">
                    <div class="flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold"
                         :class="msg.role === 'user' ? 'bg-blue-600' : 'bg-emerald-600'"
                         x-text="msg.role === 'user' ? 'Vous' : 'GE'"></div>
                    <div class="flex-1 rounded-2xl px-4 py-3 max-w-[80%]"
                         :class="msg.role === 'user'
                            ? 'bg-blue-600 text-white rounded-tr-sm'
                            : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-tl-sm'">
                        <p class="text-sm leading-relaxed whitespace-pre-wrap" x-text="msg.content"></p>
                        <p x-show="msg.role === 'bot'" class="text-[10px] mt-1.5 opacity-50">Service Client Green Express</p>
                    </div>
                </div>
            </template>

            {{-- Indicateur de saisie --}}
            <div x-show="loading" class="flex gap-3">
                <div class="flex-shrink-0 w-9 h-9 rounded-full bg-emerald-600 flex items-center justify-center text-white text-xs font-bold">GE</div>
                <div class="bg-slate-100 dark:bg-slate-800 rounded-2xl rounded-tl-sm px-4 py-3">
                    <div class="flex gap-1.5 items-center">
                        <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0s;"></span>
                        <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.15s;"></span>
                        <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.3s;"></span>
                    </div>
                </div>
            </div>

            {{-- Erreur --}}
            <div x-show="error" class="flex gap-3">
                <div class="flex-shrink-0 w-9 h-9 rounded-full bg-red-500 flex items-center justify-center text-white text-xs font-bold">!</div>
                <div class="flex-1 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl rounded-tl-sm px-4 py-3">
                    <p class="text-sm text-red-700 dark:text-red-300" x-text="error"></p>
                </div>
            </div>
        </div>

        {{-- Suggestions rapides --}}
        <div x-show="messages.length === 0 && !loading" class="px-4 pb-4 flex flex-wrap gap-2">
            <button @click="quickAsk('Comment créer une commande ?')"
                class="text-xs bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 px-3 py-1.5 rounded-full transition">
                Comment créer une commande ?
            </button>
            <button @click="quickAsk('Comment fonctionne le code de validation ?')"
                class="text-xs bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 px-3 py-1.5 rounded-full transition">
                Comment fonctionne le code de validation ?
            </button>
            <button @click="quickAsk('Quels sont les types d\'abonnement disponibles ?')"
                class="text-xs bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 px-3 py-1.5 rounded-full transition">
                Types d'abonnement ?
            </button>
            <button @click="quickAsk('Comment utiliser mes points de fidélité ?')"
                class="text-xs bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 px-3 py-1.5 rounded-full transition">
                Points de fidélité ?
            </button>
        </div>

        {{-- Zone de saisie --}}
        <div class="border-t border-slate-200 dark:border-slate-800 p-4">
            <form @submit.prevent="send()" class="flex gap-2">
                <input
                    type="text"
                    x-model="input"
                    :disabled="loading"
                    placeholder="Écrivez votre question..."
                    class="flex-1 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-slate-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                    maxlength="1000"
                >
                <button
                    type="submit"
                    :disabled="loading || !input.trim()"
                    class="flex-shrink-0 inline-flex items-center justify-center w-11 h-11 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white transition disabled:opacity-40 disabled:cursor-not-allowed">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    {{-- Support WhatsApp --}}
    <div class="mt-6 text-center animate-fade-up" style="animation-delay: 0.2s; opacity: 0;">
        <p class="text-xs text-slate-400 dark:text-slate-500 mb-2">Besoin d'une assistance supplémentaire ?</p>
        <div
            x-data
            @click="
                window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'info', message: 'Redirection vers le Service Client Green Express...' } }));
                setTimeout(() => window.open('https://chat.whatsapp.com/K411WvfkA9HH9k2IKqbImb', '_blank'), 800);
            "
            class="inline-flex items-center gap-2 text-sm text-green-600 dark:text-green-400 font-semibold cursor-pointer hover:underline"
        >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Contacter le support WhatsApp
        </div>
    </div>
</div>

<script>
function helpChat() {
    return {
        messages: [],
        input: '',
        loading: false,
        error: '',

        init() {},

        quickAsk(question) {
            this.input = question;
            this.send();
        },

        async send() {
            const question = this.input.trim();
            if (!question || this.loading) return;

            this.error = '';
            this.messages.push({ role: 'user', content: question });
            this.input = '';
            this.loading = true;
            this.$nextTick(() => this.scrollToBottom());

            try {
                const res = await fetch('{{ route("help.ask") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ question }),
                });

                const data = await res.json();

                if (!res.ok) {
                    throw new Error(data.error || 'Une erreur est survenue. Veuillez réessayer.');
                }

                this.messages.push({ role: 'bot', content: data.response });
            } catch (e) {
                this.error = e.message;
            } finally {
                this.loading = false;
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        scrollToBottom() {
            const el = this.$refs.messages;
            if (el) el.scrollTop = el.scrollHeight;
        },
    };
}
</script>
@endsection
