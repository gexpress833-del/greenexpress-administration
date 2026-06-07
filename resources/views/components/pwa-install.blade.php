<div x-data="pwaInstall()" x-show="showBanner" x-cloak
     class="fixed bottom-0 left-0 right-0 z-50 p-4 sm:p-6"
     style="display: none;">
    <div class="mx-auto max-w-lg transform transition-all duration-500"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-full opacity-0">
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-2xl border border-gray-100 dark:border-gray-700 p-5 flex items-start gap-4">
            <div class="shrink-0">
                <div class="h-12 w-12 rounded-xl bg-green-600 flex items-center justify-center shadow-lg">
                    <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-base font-bold text-gray-900 dark:text-white leading-tight">Installer Green Express</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">
                    Ajoutez l'application sur votre écran d'accueil pour un accès rapide et une meilleure expérience hors ligne.
                </p>
                <div class="mt-3 flex items-center gap-3">
                    <button @click="installPWA()"
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition shadow-sm active:scale-95">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Installer
                    </button>
                    <button @click="dismissBanner()"
                            class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 font-medium px-3 py-2 transition">
                        Plus tard
                    </button>
                </div>
            </div>
            <button @click="dismissBanner()"
                    class="shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition p-1 -mr-1 -mt-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
    function pwaInstall() {
        return {
            showBanner: false,
            deferredPrompt: null,
            init() {
                // iOS detection
                const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
                const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

                if (isStandalone) return;

                if (isIOS) {
                    // Show custom iOS install hint after delay
                    setTimeout(() => {
                        if (!localStorage.getItem('pwa-dismissed')) {
                            this.showBanner = true;
                        }
                    }, 3000);
                    return;
                }

                // Android/Desktop: listen for beforeinstallprompt
                window.addEventListener('beforeinstallprompt', (e) => {
                    e.preventDefault();
                    this.deferredPrompt = e;

                    if (!localStorage.getItem('pwa-dismissed')) {
                        setTimeout(() => { this.showBanner = true; }, 2000);
                    }
                });
            },
            async installPWA() {
                if (this.deferredPrompt) {
                    this.deferredPrompt.prompt();
                    const { outcome } = await this.deferredPrompt.userChoice;
                    if (outcome === 'accepted') {
                        this.showBanner = false;
                    }
                    this.deferredPrompt = null;
                } else {
                    // iOS: show instructions
                    alert('Appuyez sur le bouton Partager, puis "Sur l\'écran d\'accueil" pour installer Green Express.');
                    this.showBanner = false;
                }
            },
            dismissBanner() {
                this.showBanner = false;
                localStorage.setItem('pwa-dismissed', Date.now().toString());
            }
        }
    }
</script>
