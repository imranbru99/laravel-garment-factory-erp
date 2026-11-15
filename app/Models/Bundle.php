<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToFactory;
use App\Models\Concerns\LimitsBuyerPortal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\Attributes\Slug;

class Bundle extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    public function cutPlan(): BelongsTo { return $this->belongsTo(CutPlan::class); }
    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
    public function components(): HasMany { return $this->hasMany(BundleComponent::class); }
    public function scans(): HasMany { return $this->hasMany(BundleScan::class); }
}
