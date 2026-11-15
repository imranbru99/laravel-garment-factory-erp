<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToFactory;
use App\Models\Concerns\LimitsBuyerPortal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\Attributes\Slug;

class BomItem extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['consumption' => 'decimal:4', 'unit_cost' => 'decimal:4'];
    }

    public function techPack(): BelongsTo { return $this->belongsTo(TechPack::class); }
    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
}
