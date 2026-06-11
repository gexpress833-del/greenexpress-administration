<div x-data="pwaInstall()" x-init="init()" x-cloak>
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
                <h3 class="text-base font-bold text-gray-900 dark:text-white leading-tight" x-text="bannerTitle">Installer Green Express</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">
                    Ajoutez l'application sur votre écran d'accueil pour un accès rapide et une meilleure expérience hors ligne.
                </p>
                <div class="mt-3 flex items-center gap-3">
                    <button @click="installPWA()"
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition shadow-sm active:scale-95">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span x-text="installButtonText">Installer</span>
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

    {{-- Modal d'instructions iOS / Android manuel (triggered by events) --}}
    <div x-data="{ open: false, platform: '' }" x-show="open" x-cloak
         class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
         style="display: none;"
         @pwa-instructions.window="open = true; platform = $event.detail.platform">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-sm w-full p-6"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white" x-text="platform === 'ios' ? 'Ajouter sur iOS' : 'Installer sur Android'">Installer</h3>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div x-show="platform === 'ios'" class="space-y-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0 text-sm font-bold text-green-600">1</div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Touchez le bouton Partager</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Le bouton <strong>Partager</strong> en bas de Safari</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0 text-sm font-bold text-green-600">2</div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Faites défiler et touchez "Ajouter à l'écran d'accueil"</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Ou "Add to Home Screen"</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0 text-sm font-bold text-green-600">3</div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Touchez "Ajouter" en haut à droite</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">L'icône apparaît sur votre écran d'accueil</p>
                    </div>
                </div>
            </div>

            <div x-show="platform === 'android'" class="space-y-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0 text-sm font-bold text-green-600">1</div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Ouvrez le menu Chrome</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Touchez les <strong>3 points verticaux</strong> &#8942; en haut à droite</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0 text-sm font-bold text-green-600">2</div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Touchez "Ajouter à l'écran d'accueil"</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Ou "Installer l'application" selon votre version</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0 text-sm font-bold text-green-600">3</div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Confirmez "Ajouter"</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">L'application s'installe automatiquement</p>
                    </div>
                </div>
            </div>

            <button @click="open = false" class="mt-5 w-full py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition">J'ai compris</button>
        </div>
    </div>

</div>

{{-- Modal d'instructions iOS / Android manuel --}}
<div x-data="{ open: false, platform: '' }" x-show="open" x-cloak
     class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
     style="display: none;"
     @pwa-instructions.window="open = true; platform = $event.detail.platform">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-sm w-full p-6"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white" x-text="platform === 'ios' ? 'Ajouter sur iOS' : 'Installer sur Android'">Installer</h3>
            <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Instructions iOS --}}
        <div x-show="platform === 'ios'" class="space-y-4">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0 text-sm font-bold text-green-600">1</div>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Touchez le bouton Partager</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Le bouton <strong>Partager</strong> en bas de Safari</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0 text-sm font-bold text-green-600">2</div>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Faites défiler et touchez "Ajouter à l'écran d'accueil"</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Ou "Add to Home Screen"</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0 text-sm font-bold text-green-600">3</div>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Touchez "Ajouter" en haut à droite</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">L'icône apparaît sur votre écran d'accueil</p>
                </div>
            </div>
        </div>

        {{-- Instructions Android manuel --}}
        <div x-show="platform === 'android'" class="space-y-4">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0 text-sm font-bold text-green-600">1</div>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Ouvrez le menu Chrome</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Touchez les <strong>3 points verticaux</strong> &#8942; en haut à droite</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0 text-sm font-bold text-green-600">2</div>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Touchez "Ajouter à l'écran d'accueil"</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Ou "Installer l'application" selon votre version</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0 text-sm font-bold text-green-600">3</div>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Confirmez "Ajouter"</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">L'application s'installe automatiquement</p>
                </div>
            </div>
        </div>

        <button @click="open = false" class="mt-5 w-full py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition">J'ai compris</button>
    </div>
</div>

<script>
    function pwaInstall() {
        return {
            showBanner: false,
            showMandatoryOverlay: false,
            deferredPrompt: null,
            platform: '',
            bannerTitle: 'Installer Green Express',
            installButtonText: 'Installer',
            canPrompt: false,

            init() {
                const ua = navigator.userAgent;
                const isIOS = /iPad|iPhone|iPod/.test(ua) && !window.MSStream;
                const isAndroid = /Android/.test(ua);
                const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

                this.platform = isIOS ? 'ios' : (isAndroid ? 'android' : 'desktop');

                // If already standalone, nothing to do
                if (isStandalone) return;

                // Mandatory overlay: block until installed
                this.showMandatoryOverlay = true;

                // Banner values
                if (isIOS) {
                    this.bannerTitle = 'Ajouter à l\'écran d\'accueil';
                    this.installButtonText = 'Voir comment';
                }

                // Listen for beforeinstallprompt (Chrome/Edge)
                window.addEventListener('beforeinstallprompt', (e) => {
                    e.preventDefault();
                    this.deferredPrompt = e;
                    this.canPrompt = true;
                    this.installButtonText = 'Installer';
                    // also show non-mandatory banner if needed
                    this.showBanner = true;
                });

                // Detect successful install via event
                window.addEventListener('appinstalled', () => {
                    this.onInstalled();
                });

                // Detect display-mode changes (user re-opens as standalone)
                const mediaQuery = window.matchMedia('(display-mode: standalone)');
                if (mediaQuery.addEventListener) {
                    mediaQuery.addEventListener('change', (e) => { if (e.matches) this.onInstalled(); });
                } else if (mediaQuery.addListener) {
                    mediaQuery.addListener((e) => { if (e.matches) this.onInstalled(); });
                }

                // Periodic check for iOS where no events exist: if user installed, page will be opened standalone
                setInterval(() => {
                    const nowStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
                    if (nowStandalone) this.onInstalled();
                }, 2000);
            },

            onInstalled() {
                this.showMandatoryOverlay = false;
                this.showBanner = false;
                // clear any saved dismissals
                localStorage.removeItem('pwa-dismissed');
            },

            async promptInstall() {
                if (this.deferredPrompt) {
                    try {
                        this.deferredPrompt.prompt();
                        const choice = await this.deferredPrompt.userChoice;
                        if (choice && choice.outcome === 'accepted') {
                            // wait a moment for appinstalled or display-mode change
                            setTimeout(() => this.onInstalled(), 1000);
                        }
                    } catch (err) {
                        console.warn('PWA prompt failed', err);
                    }
                    this.deferredPrompt = null;
                } else {
                    // Fallback: show manual instructions overlay
                    this.openInstructions(this.platform);
                }
            },

            openInstructions(platform) {
                window.dispatchEvent(new CustomEvent('pwa-instructions', { detail: { platform } }));
            },

            // legacy banner dismissal (kept for non-mandatory banner)
            dismissBanner() {
                this.showBanner = false;
                localStorage.setItem('pwa-dismissed', Date.now().toString());
            }
        }
    }
</script>
