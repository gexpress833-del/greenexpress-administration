<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('patch')

        <div x-data="avatarCropper()" class="space-y-4">
            <div class="flex items-center gap-5">
                <div class="relative group shrink-0">
                    <template x-if="!croppedPreview">
                        @if ($user->avatar)
                            <img src="{{ $user->avatar }}" alt="Avatar" class="h-24 w-24 rounded-2xl object-cover ring-2 ring-green-500 shadow-lg" x-ref="avatarPreview">
                        @else
                            <div class="h-24 w-24 rounded-2xl bg-gradient-to-br from-green-100 to-emerald-100 dark:from-green-900/60 dark:to-emerald-900/60 flex items-center justify-center ring-2 ring-green-500 shadow-lg">
                                <span class="text-3xl font-bold text-green-600 dark:text-green-400">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                        @endif
                    </template>
                    <template x-if="croppedPreview">
                        <img :src="croppedPreview" alt="Avatar" class="h-24 w-24 rounded-2xl object-cover ring-2 ring-green-500 shadow-lg">
                    </template>
                    <label for="avatar-upload" class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition rounded-2xl cursor-pointer">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </label>
                    <input id="avatar-upload" type="file" accept="image/*" @change="openCropper($event)" class="hidden">
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-1">Photo de profil</label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">JPG, PNG, GIF, WebP — max 5 Mo</p>
                    <div class="flex items-center gap-3 flex-wrap">
                        <label for="avatar-upload-fallback" class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg text-sm font-medium hover:bg-green-100 dark:hover:bg-green-900/50 transition cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Changer la photo
                        </label>
                        <input id="avatar-upload-fallback" type="file" accept="image/*" @change="openCropper($event)" class="hidden">
                        @if ($user->avatar)
                            <button type="button" @click="removeAvatar()" class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg text-sm font-medium hover:bg-red-100 dark:hover:bg-red-900/50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Supprimer
                            </button>
                        @endif
                    </div>
                    <input type="hidden" name="avatar" id="cropped-avatar" x-ref="croppedInput">
                    <input type="hidden" name="remove_avatar" value="0" x-ref="removeInput">
                    <p x-show="cropError" x-text="cropError" class="mt-2 text-sm text-red-600"></p>
                    @error('avatar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Modal de cadrage --}}
            <div x-show="showModal" x-transition.opacity style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" @keydown.escape.window="closeModal()" @click="closeModal()">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 max-w-lg w-full mx-4 border border-gray-100 dark:border-gray-700" @click.stop>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recadrer la photo</h3>
                    <div class="max-h-80 overflow-hidden rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                        <img x-ref="cropperImage" class="max-w-full" style="display: block;">
                    </div>
                    <div class="flex items-center justify-between gap-3 mt-5">
                        <div class="flex gap-2">
                            <button type="button" @click="rotate(-90)" class="p-2 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition" title="Pivoter à gauche">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9"/></svg>
                            </button>
                            <button type="button" @click="rotate(90)" class="p-2 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition" title="Pivoter à droite">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 4v5h-.582m-15.356 2A8.001 8.001 0 0020.418 9m0 0H15"/></svg>
                            </button>
                            <button type="button" @click="resetCrop()" class="p-2 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition" title="Réinitialiser">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            </button>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" @click="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                Annuler
                            </button>
                            <button type="button" @click="saveCrop()" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-xl hover:bg-green-700 transition shadow-lg shadow-green-600/20">
                                Appliquer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-900 dark:text-white mb-1.5">Nom complet</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                       class="block w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition shadow-sm">
                @error('name')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-gray-900 dark:text-white mb-1.5">Adresse e-mail</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                       class="block w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition shadow-sm">
                @error('email')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label for="phone" class="block text-sm font-semibold text-gray-900 dark:text-white mb-1.5">Téléphone</label>
                <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}" autocomplete="tel"
                       class="block w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition shadow-sm"
                       placeholder="+243...">
                @error('phone')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="address" class="block text-sm font-semibold text-gray-900 dark:text-white mb-1.5">Adresse</label>
                <input id="address" name="address" type="text" value="{{ old('address', $user->address) }}" autocomplete="address"
                       class="block w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition shadow-sm"
                       placeholder="Votre adresse de résidence">
                @error('address')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl">
                <p class="text-sm text-amber-800 dark:text-amber-200">
                    Votre adresse e-mail n'est pas vérifiée.
                    <button form="send-verification" class="underline text-sm font-medium hover:text-amber-900 dark:hover:text-amber-100">Renvoyer l'e-mail de vérification</button>
                </p>
                @if (session('status') === 'verification-link-sent')
                    <p class="mt-1 text-sm text-green-600 dark:text-green-400">Un nouveau lien de vérification a été envoyé.</p>
                @endif
            </div>
        @endif

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-6 rounded-xl transition flex items-center gap-2 shadow-lg shadow-green-600/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Enregistrer les modifications
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-green-600 dark:text-green-400 font-medium">Enregistré !</p>
            @endif
        </div>
    </form>
</section>

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
    function avatarCropper() {
        return {
            showModal: false,
            cropper: null,
            croppedPreview: null,
            cropError: '',
            openCropper(event) {
                const file = event.target.files[0];
                if (!file) return;

                this.cropError = '';

                if (typeof Cropper === 'undefined') {
                    this.cropError = "La librairie de recadrage n'a pas pu être chargée. Vérifiez votre connexion.";
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    this.cropError = 'L\'image dépasse 5 Mo. Choisissez une image plus légère.';
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    this.$refs.cropperImage.src = e.target.result;
                    this.showModal = true;

                    setTimeout(() => {
                        if (this.cropper) {
                            this.cropper.destroy();
                            this.cropper = null;
                        }
                        this.cropper = new Cropper(this.$refs.cropperImage, {
                            aspectRatio: 1,
                            viewMode: 1,
                            autoCropArea: 1,
                            movable: true,
                            scalable: true,
                            zoomable: true,
                            rotatable: true,
                        });
                    }, 150);
                };
                reader.readAsDataURL(file);
                event.target.value = '';
            },
            closeModal() {
                this.showModal = false;
                if (this.cropper) {
                    this.cropper.destroy();
                    this.cropper = null;
                }
            },
            rotate(deg) {
                if (this.cropper) this.cropper.rotate(deg);
            },
            resetCrop() {
                if (this.cropper) this.cropper.reset();
            },
            saveCrop() {
                if (!this.cropper) return;

                const canvas = this.cropper.getCroppedCanvas({
                    width: 400,
                    height: 400,
                    imageSmoothingQuality: 'high',
                });

                if (!canvas) {
                    this.cropError = 'Impossible de générer l\'image recadrée. Réessayez.';
                    return;
                }

                canvas.toBlob((blob) => {
                    const reader = new FileReader();
                    reader.onloadend = () => {
                        this.$refs.croppedInput.value = reader.result;
                        this.croppedPreview = reader.result;
                        this.$refs.removeInput.value = '0';
                        this.closeModal();
                    };
                    reader.readAsDataURL(blob);
                }, 'image/jpeg', 0.9);
            },
            removeAvatar() {
                this.croppedPreview = null;
                this.$refs.croppedInput.value = '';
                this.$refs.removeInput.value = '1';
            }
        }
    }
</script>
@endpush
