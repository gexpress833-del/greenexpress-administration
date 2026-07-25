<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReceiptController extends Controller
{
    private function generateQrSvg(Order $order): string
    {
        $qrData = route('livreur.deliveries.validate-qr-form', [
            'order_id' => $order->id,
            'code' => $order->client_validation_code,
        ]);

        $options = new QROptions([
            'outputInterface' => QRMarkupSVG::class,
            'outputBase64' => false,
        ]);
        $qrCode = (new QRCode($options))->render($qrData);

        // Inject width/height so the SVG actually renders
        $qrCode = preg_replace('/<svg/', '<svg width="150" height="150"', $qrCode, 1);

        return $qrCode;
    }

    private function generateQrPngBase64(Order $order): string
    {
        $qrData = route('livreur.deliveries.validate-qr-form', [
            'order_id' => $order->id,
            'code' => $order->client_validation_code,
        ]);

        $options = new QROptions([
            'outputInterface' => QRGdImagePNG::class,
            'outputBase64' => true,
            'scale' => 6,
        ]);

        return (new QRCode($options))->render($qrData);
    }

    private function mealImageUrls(Order $order): array
    {
        return $order->items->mapWithKeys(function ($item): array {
            $image = $item->meal?->image;

            if (! $image) {
                return [$item->id => null];
            }

            return [$item->id => str_starts_with($image, 'http') ? $image : asset('storage/'.$image)];
        })->all();
    }

    private function mealImageData(Order $order): array
    {
        return $order->items->mapWithKeys(function ($item): array {
            $image = $item->meal?->image;

            if (! $image) {
                return [$item->id => null];
            }

            $contents = str_starts_with($image, 'http')
                ? Http::timeout(5)->get($image)->body()
                : @file_get_contents(public_path('storage/'.$image));

            if (! $contents) {
                return [$item->id => null];
            }

            $imageInfo = @getimagesizefromstring($contents);
            if (! $imageInfo || empty($imageInfo['mime'])) {
                return [$item->id => null];
            }

            return [$item->id => 'data:'.$imageInfo['mime'].';base64,'.base64_encode($contents)];
        })->all();
    }

    private function logoBase64(): ?string
    {
        $path = public_path('logo.png');
        if (! file_exists($path) || ! extension_loaded('gd')) {
            return null;
        }

        $src = @imagecreatefrompng($path);
        if (! $src) {
            return null;
        }

        $w = imagesx($src);
        $h = imagesy($src);
        $maxW = 150;
        $newW = $maxW;
        $newH = (int) round($h * ($maxW / $w));

        $dst = imagecreatetruecolor($newW, $newH);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefill($dst, 0, 0, $transparent);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);

        ob_start();
        imagepng($dst, null, 6);
        $data = ob_get_clean();

        imagedestroy($src);
        imagedestroy($dst);

        return $data ? 'data:image/png;base64,'.base64_encode($data) : null;
    }

    private function authorizeReceipt(User $user, Order $order): void
    {
        abort_unless($user->role === 'admin' || $order->agent_id === $user->id, 403);
    }

    public function show(Request $request, Order $order)
    {
        $this->authorizeReceipt($request->user(), $order);

        $order->load(['agent', 'items.meal', 'subscription.subscriptionType']);
        $qrCode = $this->generateQrSvg($order);
        $mealImageUrls = $this->mealImageUrls($order);
        $receiptPdfRoute = $request->user()->role === 'admin' ? 'admin.receipt.pdf' : 'agent.receipt.pdf';

        return view('agent.receipt.show', compact('order', 'qrCode', 'mealImageUrls', 'receiptPdfRoute'));
    }

    public function pdf(Request $request, Order $order)
    {
        $this->authorizeReceipt($request->user(), $order);

        $order->load(['agent', 'items.meal', 'subscription.subscriptionType']);
        $qrCode = $this->generateQrPngBase64($order);
        $logoData = $this->logoBase64();
        $mealImageData = $this->mealImageData($order);

        $pdf = Pdf::loadView('pdf.receipt', compact('order', 'qrCode', 'logoData', 'mealImageData'));

        return $pdf->download('recu-'.$order->code.'.pdf');
    }
}
