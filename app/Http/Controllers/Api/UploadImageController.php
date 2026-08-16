<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CloudinaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\ImageFile;

class UploadImageController extends Controller
{
    public function __construct(
        private CloudinaryService $cloudinary
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'image' => ['required', new ImageFile, 'max:2048'],
            'folder' => ['sometimes', 'string', 'in:menus,promotions,uploads,profiles,subscription-plans'],
        ]);

        $folder = $validated['folder'] ?? 'uploads';

        $url = $this->cloudinary->upload($request->file('image'), $folder);

        if (! $url) {
            return response()->json([
                'message' => 'Échec de l\'upload de l\'image',
            ], 500);
        }

        return response()->json([
            'message' => 'Image uploadée avec succès',
            'data' => [
                'url' => $url,
                'folder' => $folder,
            ],
        ]);
    }
}
