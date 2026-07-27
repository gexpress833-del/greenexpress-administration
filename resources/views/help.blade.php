@extends('layouts.app')

@section('title', 'Aide & Support — Green Express')

@section('content')
<style>
[x-cloak] { display: none !important; }
.help-scroll::-webkit-scrollbar { width: 5px; }
.help-scroll::-webkit-scrollbar-track { background: transparent; }
.help-scroll::-webkit-scrollbar-thumb { background: rgba(148,163,184,.35); border-radius: 9999px; }
.bubble-bot { border-radius: 16px 16px 16px 4px; }
.bubble-user { border-radius: 16px 16px 4px 16px; }
@keyframes blink { 0%,100%{opacity:.3;transform:scale(.85)} 50%{opacity:1;transform:scale(1.1)} }
.dot-blink { animation: blink 1.4s infinite ease-in-out both; }
.dot-blink:nth-child(2) { animation-delay: .2s; }
.dot-blink:nth-child(3) { animation-delay: .4s; }
</style>

<div class="max-w-2xl mx-auto px-4 py-6" x-data="helpChat()" x-init="init()">

    {{-- En-tête avec logo réel --}}
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-white dark:bg-slate-800 shadow-lg mb-3 p-1.5">
            <img src="{{ asset('logo-192.png') }}" alt="Green Express" class="w-full h-full rounded-xl object-cover">
        </div>
        <h1 class="text-xl font-bold text-slate-800 dark:text-white">Direction Green Express</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Posez vos questions sur les commandes, abonnements, livraisons, paiements et plus encore.
        </p>
    </div>

    {{-- Carte de chat --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-800 overflow-hidden">

        {{-- Bandeau statut --}}
        <div class="flex items-center gap-3 px-4 py-3 bg-gradient-to-r from-emerald-600 to-emerald-500">
            <img src="{{ asset('logo-192.png') }}" alt="GE" class="w-10 h-10 rounded-full bg-white p-0.5 shadow">
            <div class="flex-1">
                <p class="text-white font-semibold text-sm">Direction Green Express</p>
                <p class="text-emerald-50 text-xs flex items-center gap-1.5">
                    <span class="w-2 h-2 bg-green-300 rounded-full"></span> En ligne
                </p>
            </div>
            <button type="button" @click="window.open('https://chat.whatsapp.com/K411WvfkA9HH9k2IKqbImb', '_blank')" class="text-white/80 hover:text-white transition" title="WhatsApp">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            </button>
        </div>

        {{-- Zone messages --}}
        <div x-ref="messages" class="px-4 py-5 space-y-4 max-h-[55vh] overflow-y-auto help-scroll bg-slate-50 dark:bg-slate-950/40">

            {{-- Message de bienvenue --}}
            <div class="flex gap-3">
                <img src="{{ asset('logo-192.png') }}" alt="GE" class="flex-shrink-0 w-9 h-9 rounded-full bg-white p-0.5 shadow-sm">
                <div class="max-w-[80%]">
                    <div class="bubble-bot bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 leading-relaxed">
                        Bonjour 👋 Je suis la Direction Green Express. Comment puis-je vous aider aujourd'hui ?
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1 ml-1">Direction Green Express • maintenant</p>
                </div>
            </div>

            {{-- Messages dynamiques --}}
            <template x-for="(msg, i) in messages" :key="i">
                <div class="flex gap-3" :class="msg.role === 'user' ? 'flex-row-reverse' : ''">
                    <img x-show="msg.role === 'bot'" src="{{ asset('logo-192.png') }}" alt="GE" class="flex-shrink-0 w-9 h-9 rounded-full bg-white p-0.5 shadow-sm">
                    <div x-show="msg.role === 'user'" class="flex-shrink-0 w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold shadow-sm">Vous</div>
                    <div class="max-w-[80%]" :class="msg.role === 'user' ? 'flex flex-col items-end' : ''">
                        <div class="px-4 py-3 text-sm leading-relaxed whitespace-pre-wrap shadow-sm"
                             :class="msg.role === 'user'
                                ? 'bubble-user bg-emerald-600 text-white'
                                : 'bubble-bot bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-100 dark:border-slate-700'"
                             x-text="msg.content"></div>
                        <p x-show="msg.role === 'bot'" class="text-[10px] text-slate-400 mt-1 ml-1">Direction Green Express</p>
                    </div>
                </div>
            </template>

            {{-- Indicateur de saisie --}}
            <div x-show="loading" x-cloak class="flex gap-3">
                <img src="{{ asset('logo-192.png') }}" alt="GE" class="flex-shrink-0 w-9 h-9 rounded-full bg-white p-0.5 shadow-sm">
                <div class="bubble-bot bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700 px-5 py-4 flex items-center gap-1.5">
                    <span class="w-2 h-2 bg-slate-400 rounded-full dot-blink"></span>
                    <span class="w-2 h-2 bg-slate-400 rounded-full dot-blink"></span>
                    <span class="w-2 h-2 bg-slate-400 rounded-full dot-blink"></span>
                </div>
            </div>

            {{-- Erreur --}}
            <div x-show="error" x-cloak class="flex justify-center">
                <div class="inline-flex items-center gap-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg px-4 py-2.5">
                    <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <p class="text-xs text-red-700 dark:text-red-300" x-text="error"></p>
                </div>
            </div>
        </div>

        {{-- Suggestions rapides --}}
        <div x-show="messages.length === 0 && !loading" x-cloak class="px-4 py-3 border-t border-slate-100 dark:border-slate-800 flex flex-wrap gap-2">
            <button @click="quickAsk('Comment créer une commande ?')" class="text-xs bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 px-3 py-1.5 rounded-full transition">Comment créer une commande ?</button>
            <button @click="quickAsk('Comment fonctionne le code de validation ?')" class="text-xs bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 px-3 py-1.5 rounded-full transition">Code de validation</button>
            <button @click="quickAsk('Quels sont les types d\'abonnement disponibles ?')" class="text-xs bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 px-3 py-1.5 rounded-full transition">Abonnements</button>
            <button @click="quickAsk('Comment utiliser mes points de fidélité ?')" class="text-xs bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 px-3 py-1.5 rounded-full transition">Points de fidélité</button>
        </div>

        {{-- Zone de saisie --}}
        <div class="border-t border-slate-200 dark:border-slate-800 p-3">
            <div class="flex gap-2">
                <input
                    type="text"
                    x-model="input"
                    @keydown.enter.prevent="send()"
                    placeholder="Écrivez votre question..."
                    class="flex-1 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm text-slate-900 dark:text-slate-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                    maxlength="1000"
                >
                <button
                    type="button"
                    @click="send()"
                    :disabled="!input.trim()"
                    class="flex-shrink-0 inline-flex items-center justify-center w-11 h-11 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-300 dark:disabled:bg-slate-700 text-white transition shadow-md disabled:shadow-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Support WhatsApp --}}
    <div class="mt-5 text-center">
        <p class="text-xs text-slate-400 dark:text-slate-500 mb-2">Besoin d'une assistance supplémentaire ?</p>
        <button
            @click="window.open('https://chat.whatsapp.com/K411WvfkA9HH9k2IKqbImb', '_blank')"
            class="inline-flex items-center gap-2 text-sm text-green-600 dark:text-green-400 font-semibold hover:underline"
        >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Contacter le support WhatsApp
        </button>
    </div>
</div>

<script>
function helpChat() {
    return {
        messages: [],
        input: '',
        loading: false,
        error: '',

        init() {
            window.helpChatResetLoading = () => {
                this.loading = false;
            };
        },

        quickAsk(question) {
            this.input = question;
            this.send();
        },

        async send() {
            const question = this.input.trim();
            if (!question) return;

            this.error = '';
            this.messages.push({ role: 'user', content: question });
            this.input = '';
            this.loading = true;
            this.$nextTick(() => this.scrollToBottom());

            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 25000);
            const safetyId = setTimeout(() => { this.loading = false; }, 28000);

            try {
                const res = await fetch('{{ route("help.ask") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ question }),
                    signal: controller.signal,
                });

                clearTimeout(timeoutId);

                const text = await res.text();

                let data;
                try {
                    data = JSON.parse(text);
                } catch (jsonErr) {
                    throw new Error('La réponse du service est invalide. Veuillez réessayer.');
                }

                if (!res.ok) {
                    throw new Error(data.error || 'Une erreur est survenue. Veuillez réessayer.');
                }

                if (!data.response) {
                    throw new Error('Réponse vide de la Direction Green Express. Veuillez réessayer.');
                }

                this.messages.push({ role: 'bot', content: data.response });
            } catch (e) {
                if (e.name === 'AbortError') {
                    this.error = 'La Direction Green Express met trop de temps à répondre. Veuillez réessayer.';
                } else {
                    this.error = e.message || 'Impossible de contacter la Direction Green Express. Veuillez réessayer.';
                }
            } finally {
                clearTimeout(timeoutId);
                clearTimeout(safetyId);
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
