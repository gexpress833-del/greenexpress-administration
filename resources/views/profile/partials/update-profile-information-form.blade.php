<section>
    <header class="mb-5">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Informations du profil</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Mettez à jour vos informations personnelles et votre adresse e-mail.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('patch')

        <div class="flex items-center gap-4" x-data="avatarCropper()">
            <div class="relative">
                @if ($user->avatar)
                    <img src="{{ $user->avatar }}" alt="Avatar" class="h-20 w-20 rounded-full object-cover border-2 border-green-500">
                @else
                    <div class="h-20 w-20 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center border-2 border-green-500">
                        <span class="text-2xl font-bold text-green-600 dark:text-green-400">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Photo de profil</label>
                <input type="file" accept="image/*" @change="openCropper($event)"
                       class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 dark:file:bg-green-900 dark:file:text-green-300">
                <input type="hidden" name="avatar" id="cropped-avatar" x-ref="croppedInput">
                @error('avatar')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Modal de cadrage --}}
            <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @keydown.escape.window="closeModal()">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 max-w-lg w-full mx-4" @click.away="closeModal()">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Recadrer la photo</h3>
                    <div class="max-h-80 overflow-hidden">
                        <img x-ref="cropperImage" class="max-w-full">
                    </div>
                    <div class="flex justify-end gap-3 mt-4">
                        <button type="button" @click="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">
                            Annuler
                        </button>
                        <button type="button" @click="saveCrop()" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                            Appliquer
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom complet</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                   class="block w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Adresse e-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                   class="block w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Téléphone</label>
                <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}" autocomplete="tel"
                       class="block w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                       placeholder="+243...">
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Adresse</label>
                <input id="address" name="address" type="text" value="{{ old('address', $user->address) }}" autocomplete="address"
                       class="block w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                       placeholder="Votre adresse de résidence">
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                <p class="text-sm text-amber-800 dark:text-amber-200">
                    Votre adresse e-mail n'est pas vérifiée.
                    <button form="send-verification" class="underline text-sm font-medium hover:text-amber-900 dark:hover:text-amber-100">Renvoyer l'e-mail de vérification</button>
                </p>
                @if (session('status') === 'verification-link-sent')
                    <p class="mt-1 text-sm text-green-600 dark:text-green-400">Un nouveau lien de vérification a été envoyé.</p>
                @endif
            </div>
        @endif

        <div class="flex items-center gap-4">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Enregistrer
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
            openCropper(event) {
                const file = event.target.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = (e) => {
                    this.$refs.cropperImage.src = e.target.result;
                    this.showModal = true;
                    this.$nextTick(() => {
                        this.cropper = new Cropper(this.$refs.cropperImage, {
                            aspectRatio: 1,
                            viewMode: 1,
                            autoCropArea: 1,
                        });
                    });
                };
                reader.readAsDataURL(file);
            },
            closeModal() {
                this.showModal = false;
                if (this.cropper) {
                    this.cropper.destroy();
                    this.cropper = null;
                }
            },
            saveCrop() {
                if (!this.cropper) return;

                const canvas = this.cropper.getCroppedCanvas({
                    width: 400,
                    height: 400,
                });

                canvas.toBlob((blob) => {
                    const reader = new FileReader();
                    reader.onloadend = () => {
                        this.$refs.croppedInput.value = reader.result;
                        this.closeModal();
                    };
                    reader.readAsDataURL(blob);
                }, 'image/jpeg', 0.9);
            }
        }
    }
</script>
@endpush
