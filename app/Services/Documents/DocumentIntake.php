<?php

namespace App\Services\Documents;

use App\Models\GoodsReceipt;
use ImranDevBd\AiOcr\Facades\AiOcr;
use Illuminate\Http\UploadedFile;
use Throwable;

class DocumentIntake
{
    /**
     * Manual-first extraction. AI Hub is used by the package only when a key is configured
     * and the caller does not force manualOnly().
     *
     * @return array<string, mixed>
     */
    public function extract(UploadedFile|string $file, string $type = 'invoice'): array
    {
        try {
            $request = AiOcr::file($file)->as($type)->languages(['ben', 'eng']);

            if (! config('factory.ai.enabled')) {
                $request->manualOnly();
            }

            $result = $request->extract();

            return [
                'ok' => true,
                'data' => $result->data(),
                'warnings' => method_exists($result, 'warnings') ? $result->warnings() : [],
            ];
        } catch (Throwable $exception) {
            return [
                'ok' => false,
                'data' => [],
                'warnings' => [$exception->getMessage()],
            ];
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function attachToReceipt(GoodsReceipt $receipt, array $payload): GoodsReceipt
    {
        $receipt->update([
            'ocr_payload' => $payload,
            'notes' => trim(($receipt->notes ? $receipt->notes."\n" : '').'OCR intake stored.'),
        ]);

        return $receipt->refresh();
    }
}
