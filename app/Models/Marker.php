<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToFactory;
use App\Models\Concerns\LimitsBuyerPortal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\Attributes\Slug;

class Marker extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['sizes' => 'array', 'width_inch' => 'decimal:2', 'efficiency' => 'decimal:2'];
    }

    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
    public function style(): BelongsTo { return $this->belongsTo(Style::class); }
    public function fabric(): BelongsTo { return $this->belongsTo(Fabric::class); }
    public function cutPlans(): HasMany { return $this->hasMany(CutPlan::class); }
}
