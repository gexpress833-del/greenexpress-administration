<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\Output\QRMarkupSVG;
use Illuminate\Http\Request;

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

    private function logoBase64(): ?string
    {
        $path = public_path('logo.png');
        if (!file_exists($path) || !extension_loaded('gd')) {
            return null;
        }

        $src = @imagecreatefrompng($path);
        if (!$src) {
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

        return $data ? 'data:image/png;base64,' . base64_encode($data) : null;
    }

    private function canView(Order $order, Request $request): bool
    {
        $user = $request->user();
        return $order->agent_id === $user->id || $user->role === 'admin';
    }

    public function show(Request $request, Order $order)
    {
        abort_unless($this->canView($order, $request), 403);

        $order->load(['agent', 'items.meal']);
        $qrCode = $this->generateQrSvg($order);

        return view('agent.receipt.show', compact('order', 'qrCode'));
    }

    public function pdf(Request $request, Order $order)
    {
        abort_unless($this->canView($order, $request), 403);

        $order->load(['agent', 'items.meal']);
        $qrCode = $this->generateQrPngBase64($order);
        $logoData = $this->logoBase64();

        $pdf = Pdf::loadView('pdf.receipt', compact('order', 'qrCode', 'logoData'));
        return $pdf->download('recu-' . $order->code . '.pdf');
    }
}
