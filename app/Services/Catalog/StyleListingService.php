<?php

namespace App\Services\Catalog;

use App\Models\Style;
use ImranDevBd\AiCatalog\Facades\AiCatalog;
use Throwable;

class StyleListingService
{
    /**
     * @return array<string, mixed>
     */
    public function generate(Style $style, string $locale = 'en'): array
    {
        $keyword = trim(implode(' ', array_filter([
            $style->style_no,
            $style->name,
            $style->category,
            'trouser',
            $style->fit ? $style->fit.' fit' : null,
            $style->fabric_type,
            $style->wash ? $style->wash.' wash' : null,
            $style->composition,
        ])));

        if (config('factory.ai.enabled')) {
            try {
                $generation = AiCatalog::fromKeyword($keyword)->generate();
                $payload = method_exists($generation, 'toArray') ? $generation->toArray() : ['generation_id' => $generation->getKey()];
                $payload['source'] = 'ai-catalog';
                $payload['locale'] = $locale;
                $style->update(['ai_listing' => $payload]);

                return $payload;
            } catch (Throwable $exception) {
                $fallback = $this->localDraft($style, $keyword, $locale);
                $fallback['ai_error'] = $exception->getMessage();
                $style->update(['ai_listing' => $fallback]);

                return $fallback;
            }
        }

        $draft = $this->localDraft($style, $keyword, $locale);
        $style->update(['ai_listing' => $draft]);

        return $draft;
    }

    /**
     * @return array<string, mixed>
     */
    protected function localDraft(Style $style, string $keyword, string $locale): array
    {
        $bn = $locale === 'bn';

        return [
            'source' => 'factory-draft',
            'locale' => $locale,
            'keyword' => $keyword,
            'title' => $style->name,
            'title_bn' => $bn ? $style->name : null,
            'summary' => sprintf(
                '%s is a %s %s trouser in %s. SMV %s. Composition: %s.',
                $style->style_no,
                $style->fit ?: 'regular',
                $style->category,
                $style->fabric_type ?: 'unspecified fabric',
                $style->smv,
                $style->composition ?: 'see tech pack',
            ),
            'filters' => [
                'category' => $style->category,
                'fit' => $style->fit,
                'fabric' => $style->fabric_type,
                'wash' => $style->wash,
            ],
        ];
    }
}
