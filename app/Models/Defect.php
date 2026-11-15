<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToFactory;
use App\Models\Concerns\LimitsBuyerPortal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\Attributes\Slug;

class Defect extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::creating(function (Defect $defect) {
            if (blank($defect->root_cause) && filled($defect->category)) {
                $defect->root_cause = app(\App\Services\Issues\IssueEngine::class)->suggestCause($defect);
            }

            if (blank($defect->ai_category)) {
                $defect->ai_category = $defect->category;
            }
        });
    }

    public function inspection(): BelongsTo { return $this->belongsTo(Inspection::class); }
    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
}
