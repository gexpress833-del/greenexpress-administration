<?php

namespace App\Http\Controllers;

use App\Services\AiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HelpController extends Controller
{
    public function index()
    {
        return view('help');
    }

    public function ask(Request $request, AiService $ai): JsonResponse
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:1000'],
        ]);

        try {
            $response = $ai->generateHelpResponse($data['question'], $request->user()?->name);

            return response()->json(['response' => $response]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
