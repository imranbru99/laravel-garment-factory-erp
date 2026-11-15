<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToFactory;
use App\Models\Concerns\LimitsBuyerPortal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\Attributes\Slug;

class WashLot extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'finished_at' => 'datetime'];
    }

    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
    public function washRecipe(): BelongsTo { return $this->belongsTo(WashRecipe::class); }
}
