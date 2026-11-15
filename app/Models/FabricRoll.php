<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToFactory;
use App\Models\Concerns\LimitsBuyerPortal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\Attributes\Slug;

class FabricRoll extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['width_inch' => 'decimal:2', 'length_m' => 'decimal:2', 'remaining_m' => 'decimal:2'];
    }

    public function fabric(): BelongsTo { return $this->belongsTo(Fabric::class); }
    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
}
