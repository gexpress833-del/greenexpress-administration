@extends('layouts.app')

@section('title', 'Aide & Support — Green Express')

@section('content')
<style>
@keyframes fade-up {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes pop {
    0% { opacity: 0; transform: scale(0.95) translateY(10px); }
    70% { transform: scale(1.02) translateY(-2px); }
    100% { opacity: 1; transform: scale(1) translateY(0); }
}
@keyframes slide-in-right {
    from { opacity: 0; transform: translateX(20px); }
    to { opacity: 1; transform: translateX(0); }
}
@keyframes slide-in-left {
    from { opacity: 0; transform: translateX(-20px); }
    to { opacity: 1; transform: translateX(0); }
}
@keyframes pulse-dot {
    0%, 100% { opacity: 0.4; transform: scale(0.9); }
    50% { opacity: 1; transform: scale(1.1); }
}
.animate-fade-up { animation: fade-up 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards; }
.animate-pop { animation: pop 0.35s cubic-bezier(0.22, 1, 0.36, 1) forwards; }
.animate-in-right { animation: slide-in-right 0.3s ease-out forwards; }
.animate-in-left { animation: slide-in-left 0.3s ease-out forwards; }
[x-cloak] { display: none !important; }

.help-scrollbar::-webkit-scrollbar { width: 6px; }
.help-scrollbar::-webkit-scrollbar-track { background: transparent; }
.help-scrollbar::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.4); border-radius: 9999px; }
.help-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(148, 163, 184, 0.6); }

.chat-bubble-bot {
    border-radius: 18px 18px 18px 4px;
}
.chat-bubble-user {
    border-radius: 18px 18px 4px 18px;
}

.typing-dot { animation: pulse-dot 1.4s infinite ease-in-out both; }
.typing-dot:nth-child(1) { animation-delay: -0.32s; }
.typing-dot:nth-child(2) { animation-delay: -0.16s; }

.glass-header {
    background: rgba(5, 150, 105, 0.95);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}

.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<div class="max-w-md mx-auto h-[85vh] lg:h-[800px] flex flex-col mt-4 lg:mt-8" x-data="helpChat()" x-init="init()">
    {{-- Carte de chat premium --}}
    <div class="flex-1 bg-white dark:bg-slate-900 rounded-3xl shadow-2xl shadow-emerald-900/10 dark:shadow-black/30 border border-slate-200/60 dark:border-slate-800 overflow-hidden flex flex-col animate-fade-up" style="animation-delay: 0.05s; opacity: 1;">
        {{-- Header glass avec logo réel --}}
        <div class="glass-header relative flex items-center gap-3 px-4 py-3.5 border-b border-emerald-500/20 shrink-0">
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-600 to-emerald-500 opacity-90 -z-10"></div>
            <div class="relative">
                <img src="{{ asset('logo-192.png') }}" alt="Green Express" class="w-11 h-11 rounded-full bg-white p-0.5 shadow-md object-cover">
                <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 border-2 border-emerald-600 rounded-full"></span>
            </div>
            <div class="flex-1 min-w-0">
                <h1 class="text-white font-semibold text-base leading-tight truncate">Service Client Green Express</h1>
                <p class="text-emerald-100 text-xs flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span>
                    En ligne — réponse instantanée
                </p>
            </div>
            <button type="button" class="text-white/80 hover:text-white transition" @click="window.open('https://chat.whatsapp.com/K411WvfkA9HH9k2IKqbImb', '_blank')" title="Contacter WhatsApp">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            </button>
        </div>

        {{-- Messages --}}
        <div x-ref="messages" class="flex-1 px-4 py-5 space-y-4 overflow-y-auto help-scrollbar bg-slate-50 dark:bg-slate-950/50" style="scroll-behavior: smooth;">
            {{-- Message de bienvenue --}}
            <div class="flex gap-2.5 animate-in-left">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white dark:bg-slate-800 shadow-sm p-0.5 flex items-center justify-center">
                    <img src="{{ asset('logo-192.png') }}" alt="GE" class="w-7 h-7 rounded-full object-cover">
                </div>
                <div class="flex-1 max-w-[80%]">
                    <div class="chat-bubble-bot bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-200 leading-relaxed">
                        Bonjour 👋<br>Je suis le Service Client Green Express. Comment puis-je vous aider aujourd'hui ?
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1 ml-1">Service Client • maintenant</p>
                </div>
            </div>

            {{-- Messages dynamiques --}}
            <template x-for="(msg, index) in messages" :key="index">
                <div class="flex gap-2.5" :class="msg.role === 'user' ? 'flex-row-reverse' : ''" :style="`animation-delay: ${index * 40}ms`">
                    {{-- Avatar --}}
                    <template x-if="msg.role === 'bot'">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white dark:bg-slate-800 shadow-sm p-0.5 flex items-center justify-center">
                            <img src="{{ asset('logo-192.png') }}" alt="GE" class="w-7 h-7 rounded-full object-cover">
                        </div>
                    </template>
                    <template x-if="msg.role === 'user'">
                        <div class="flex-shrink-0 w-8 h-9 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold shadow-sm" x-text="'Vous'"></div>
                    </template>

                    {{-- Bubble --}}
                    <div class="flex-1" :class="msg.role === 'user' ? 'max-w-[82%] flex flex-col items-end' : 'max-w-[80%]'">
                        <div class="text-sm leading-relaxed whitespace-pre-wrap px-4 py-2.5 shadow-sm"
                             :class="msg.role === 'user'
                                ? 'chat-bubble-user bg-emerald-600 text-white'
                                : 'chat-bubble-bot bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-100 dark:border-slate-700'"
                             x-text="msg.content"></div>
                        <p x-show="msg.role === 'bot'" class="text-[10px] text-slate-400 mt-1 ml-1">Service Client Green Express</p>
                    </div>
                </div>
            </template>

            {{-- Indicateur de saisie --}}
            <div x-show="loading" x-cloak class="flex gap-2.5 animate-in-left">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white dark:bg-slate-800 shadow-sm p-0.5 flex items-center justify-center">
                    <img src="{{ asset('logo-192.png') }}" alt="GE" class="w-7 h-7 rounded-full object-cover">
                </div>
                <div class="chat-bubble-bot bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700 px-4 py-3.5 flex items-center gap-1">
                    <span class="w-2 h-2 bg-slate-400 rounded-full typing-dot"></span>
                    <span class="w-2 h-2 bg-slate-400 rounded-full typing-dot"></span>
                    <span class="w-2 h-2 bg-slate-400 rounded-full typing-dot"></span>
                </div>
            </div>

            {{-- Erreur --}}
            <div x-show="error" x-cloak class="flex justify-center animate-pop">
                <div class="inline-flex items-center gap-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-full px-4 py-2">
                    <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <p class="text-xs text-red-700 dark:text-red-300" x-text="error"></p>
                </div>
            </div>
        </div>

        {{-- Suggestions rapides --}}
        <div x-show="messages.length === 0 && !loading" x-cloak class="shrink-0 px-4 py-3 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 overflow-x-auto no-scrollbar">
            <div class="flex gap-2.5 w-max">
                <button @click="quickAsk('Comment créer une commande ?')" class="text-xs bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 border border-emerald-100 dark:border-emerald-900/30 px-3.5 py-2 rounded-full transition whitespace-nowrap">Comment créer une commande ?</button>
                <button @click="quickAsk('Code de validation')" class="text-xs bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 px-3.5 py-2 rounded-full transition whitespace-nowrap">Code de validation</button>
                <button @click="quickAsk('Abonnements')" class="text-xs bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 px-3.5 py-2 rounded-full transition whitespace-nowrap">Abonnements</button>
                <button @click="quickAsk('Points de fidélité')" class="text-xs bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 px-3.5 py-2 rounded-full transition whitespace-nowrap">Points de fidélité</button>
            </div>
        </div>

        {{-- Zone de saisie --}}
        <div class="shrink-0 bg-white dark:bg-slate-900 p-3 border-t border-slate-200 dark:border-slate-800">
            <form @submit.prevent="send()" class="flex items-end gap-2 bg-slate-100 dark:bg-slate-800 rounded-2xl p-1.5">
                <input
                    type="text"
                    x-model="input"
                    :disabled="loading"
                    placeholder="Écrivez votre message..."
                    class="flex-1 bg-transparent text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 px-3 py-2.5 focus:outline-none rounded-xl"
                    maxlength="1000"
                >
                <button
                    type="submit"
                    :disabled="loading || !input.trim()"
                    class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-300 dark:disabled:bg-slate-700 text-white transition shadow-lg shadow-emerald-600/25 disabled:shadow-none">
                    <svg class="w-5 h-5 -rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </form>
            <p class="text-[10px] text-center text-slate-400 mt-2">Propulsé par le Service Client Green Express — WhatsApp disponible 24/7</p>
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
            if (!question || this.loading) return;

            this.error = '';
            this.messages.push({ role: 'user', content: question });
            this.input = '';
            this.loading = true;
            this.$nextTick(() => this.scrollToBottom());

            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 25000);

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

                let data = {};
                try {
                    data = await res.json();
                } catch (jsonErr) {
                    throw new Error('La réponse du service est invalide. Veuillez réessayer.');
                }

                if (!res.ok) {
                    throw new Error(data.error || 'Une erreur est survenue. Veuillez réessayer.');
                }

                if (!data.response) {
                    throw new Error('Réponse vide du Service Client. Veuillez réessayer.');
                }

                this.messages.push({ role: 'bot', content: data.response });
            } catch (e) {
                if (e.name === 'AbortError') {
                    this.error = 'Le Service Client met trop de temps à répondre. Veuillez réessayer.';
                } else {
                    this.error = e.message || 'Impossible de contacter le Service Client. Veuillez réessayer.';
                }
            } finally {
                clearTimeout(timeoutId);
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
