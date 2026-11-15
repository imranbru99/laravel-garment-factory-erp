<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToFactory;
use App\Models\Concerns\LimitsBuyerPortal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\Attributes\Slug;

class OrderMatrixLine extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
}
