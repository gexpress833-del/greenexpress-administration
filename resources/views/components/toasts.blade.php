<div
    x-data="toasts()"
    x-init="initToasts()"
    @notify.window="addToast($event.detail)"
    class="fixed top-4 right-4 z-[10000] flex flex-col gap-3 pointer-events-none"
    style="top: calc(1rem + env(safe-area-inset-top));"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast.visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-4 scale-95"
            x-transition:enter-end="opacity-100 translate-x-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0 scale-100"
            x-transition:leave-end="opacity-0 translate-x-4 scale-95"
            class="pointer-events-auto w-80 max-w-[calc(100vw-2rem)] rounded-xl shadow-lg border overflow-hidden"
            :class="toast.bgClass"
        >
            <div class="p-4">
                <div class="flex items-start gap-3">
                    <div class="shrink-0 w-9 h-9 rounded-lg flex items-center justify-center text-white text-lg font-bold" :class="toast.iconBgClass">
                        <span x-text="toast.icon"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold" :class="toast.titleClass" x-text="toast.title"></p>
                        <p class="text-sm mt-0.5" :class="toast.textClass" x-text="toast.message"></p>
                    </div>
                    <button
                        @click="dismiss(toast.id)"
                        class="shrink-0 p-1 rounded-lg hover:bg-black/5 dark:hover:bg-white/10 transition"
                        :class="toast.textClass"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="mt-3 h-1 rounded-full overflow-hidden bg-black/10 dark:bg-white/10">
                    <div
                        class="h-full rounded-full transition-all duration-[4000ms] ease-linear"
                        :class="toast.progressClass"
                        x-show="toast.visible"
                        x-init="setTimeout(() => $el.style.width = '0%', 50)"
                        style="width: 100%"
                    ></div>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
    function toasts() {
        return {
            toasts: [],
            nextId: 1,

            initToasts() {
                @if(session('success'))
                    this.addToast({ type: 'success', title: 'Succès', message: @js(session('success')) });
                @endif
                @if(session('error'))
                    this.addToast({ type: 'error', title: 'Erreur', message: @js(session('error')) });
                @endif
                @if(session('warning'))
                    this.addToast({ type: 'warning', title: 'Attention', message: @js(session('warning')) });
                @endif
                @if(session('info'))
                    this.addToast({ type: 'info', title: 'Information', message: @js(session('info')) });
                @endif
                @if(session('reward'))
                    this.addToast({ type: 'reward', title: 'Récompense', message: @js(session('reward')) });
                @endif
                @if(isset($errors) && is_object($errors) && method_exists($errors, 'any') && $errors->any())
                    @foreach($errors->all() as $error)
                        this.addToast({ type: 'error', title: 'Validation', message: @js($error) });
                    @endforeach
                @endif
            },

            addToast(detail) {
                const config = this.getToastConfig(detail.type || 'info');
                const toast = {
                    id: this.nextId++,
                    type: detail.type || 'info',
                    title: detail.title || config.title,
                    message: detail.message || '',
                    icon: config.icon,
                    bgClass: config.bgClass,
                    iconBgClass: config.iconBgClass,
                    titleClass: config.titleClass,
                    textClass: config.textClass,
                    progressClass: config.progressClass,
                    visible: true,
                };

                this.toasts.push(toast);

                setTimeout(() => this.dismiss(toast.id), 4000);
            },

            dismiss(id) {
                const toast = this.toasts.find(t => t.id === id);
                if (toast) {
                    toast.visible = false;
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, 200);
                }
            },

            getToastConfig(type) {
                const configs = {
                    success: {
                        icon: '✅',
                        title: 'Succès',
                        bgClass: 'bg-white dark:bg-gray-800 border-emerald-200 dark:border-emerald-800',
                        iconBgClass: 'bg-emerald-500',
                        titleClass: 'text-emerald-900 dark:text-emerald-100',
                        textClass: 'text-gray-600 dark:text-gray-300',
                        progressClass: 'bg-emerald-500',
                    },
                    error: {
                        icon: '❌',
                        title: 'Erreur',
                        bgClass: 'bg-white dark:bg-gray-800 border-red-200 dark:border-red-800',
                        iconBgClass: 'bg-red-500',
                        titleClass: 'text-red-900 dark:text-red-100',
                        textClass: 'text-gray-600 dark:text-gray-300',
                        progressClass: 'bg-red-500',
                    },
                    warning: {
                        icon: '⚠️',
                        title: 'Attention',
                        bgClass: 'bg-white dark:bg-gray-800 border-amber-200 dark:border-amber-800',
                        iconBgClass: 'bg-amber-500',
                        titleClass: 'text-amber-900 dark:text-amber-100',
                        textClass: 'text-gray-600 dark:text-gray-300',
                        progressClass: 'bg-amber-500',
                    },
                    info: {
                        icon: 'ℹ️',
                        title: 'Information',
                        bgClass: 'bg-white dark:bg-gray-800 border-blue-200 dark:border-blue-800',
                        iconBgClass: 'bg-blue-500',
                        titleClass: 'text-blue-900 dark:text-blue-100',
                        textClass: 'text-gray-600 dark:text-gray-300',
                        progressClass: 'bg-blue-500',
                    },
                    reward: {
                        icon: '🏆',
                        title: 'Récompense',
                        bgClass: 'bg-white dark:bg-gray-800 border-yellow-200 dark:border-yellow-800',
                        iconBgClass: 'bg-gradient-to-br from-yellow-400 to-amber-500',
                        titleClass: 'text-yellow-900 dark:text-yellow-100',
                        textClass: 'text-gray-600 dark:text-gray-300',
                        progressClass: 'bg-gradient-to-r from-yellow-400 to-amber-500',
                    },
                };

                return configs[type] || configs.info;
            },
        };
    }
</script>
