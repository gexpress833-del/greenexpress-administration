<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request, CloudinaryService $cloudinary): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        $avatarData = $request->input('avatar');

        if ($avatarData) {
            if ($user->avatar) {
                $cloudinary->delete($user->avatar);
            }

            $file = $this->base64ToFile($avatarData);

            if ($file) {
                $avatarUrl = $cloudinary->upload($file, 'avatars');

                if ($avatarUrl !== null) {
                    $user->avatar = $avatarUrl;
                } else {
                    return Redirect::route('profile.edit')->with('error', 'avatar-upload-failed');
                }
            }
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    private function base64ToFile(string $base64): ?UploadedFile
    {
        if (! str_starts_with($base64, 'data:image')) {
            return null;
        }

        [$type, $data] = explode(';', $base64);
        [, $data] = explode(',', $data);

        $decoded = base64_decode($data);
        if ($decoded === false) {
            return null;
        }

        // Limite de taille : 5 Mo
        if (strlen($decoded) > 5 * 1024 * 1024) {
            return null;
        }

        // Vérifier que c'est bien une image valide
        $imageInfo = getimagesizefromstring($decoded);
        if ($imageInfo === false) {
            return null;
        }

        $mime = $imageInfo['mime'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (! in_array($mime, $allowedTypes, true)) {
            return null;
        }

        $extension = match ($mime) {
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => 'jpg',
        };

        $tmpPath = tempnam(sys_get_temp_dir(), 'avatar_').'.'.$extension;
        file_put_contents($tmpPath, $decoded);

        return new UploadedFile($tmpPath, 'avatar.'.$extension, $mime, null, true);
    }
}
